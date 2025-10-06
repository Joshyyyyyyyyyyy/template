<?php
session_start();
require_once '../config/db_config.php';

header('Content-Type: application/json');

// Enable error logging for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in JSON response
ini_set('log_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['student_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$student_id = $_SESSION['student_id'];

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get scholarship code from POST data
$scholarship_code = $_POST['scholarship_code'] ?? '';

if (empty($scholarship_code)) {
    echo json_encode(['success' => false, 'message' => 'Scholarship type is required']);
    exit;
}

// Validate files were uploaded
if (empty($_FILES) || count($_FILES) === 0) {
    echo json_encode(['success' => false, 'message' => 'No files uploaded. Please select at least one document.']);
    exit;
}

// Check for file upload errors
$hasValidFiles = false;
foreach ($_FILES as $file) {
    if ($file['error'] === UPLOAD_ERR_OK && $file['size'] > 0) {
        $hasValidFiles = true;
        break;
    }
}

if (!$hasValidFiles) {
    echo json_encode(['success' => false, 'message' => 'No valid files were uploaded. Please check your files and try again.']);
    exit;
}

$conn = getDBConnection();
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed. Please try again later.']);
    exit;
}

try {
    // Start transaction
    $conn->begin_transaction();
    
    // Get scholarship ID
    $stmt = $conn->prepare("SELECT scholarship_id, scholarship_name FROM scholarships WHERE scholarship_code = ? AND is_active = 1");
    if (!$stmt) {
        throw new Exception('Database error: ' . $conn->error);
    }
    
    $stmt->bind_param("s", $scholarship_code);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Invalid or inactive scholarship type');
    }
    
    $scholarship = $result->fetch_assoc();
    $scholarship_id = $scholarship['scholarship_id'];
    $scholarship_name = $scholarship['scholarship_name'];
    $stmt->close();
    
    // Check if student already has a pending or approved application for this scholarship
    $stmt = $conn->prepare("SELECT application_id, application_status FROM scholarship_applications 
                            WHERE student_id = ? AND scholarship_id = ? 
                            AND application_status IN ('pending', 'under_review', 'approved')");
    if (!$stmt) {
        throw new Exception('Database error: ' . $conn->error);
    }
    
    $stmt->bind_param("ii", $student_id, $scholarship_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $existing = $result->fetch_assoc();
        throw new Exception('You already have an active application for this scholarship (Status: ' . $existing['application_status'] . ')');
    }
    $stmt->close();
    
    // Get current academic year and semester
    $academic_year = 'AY 2025-2026';
    $semester = '1st Semester';
    
    // Create scholarship application
    $stmt = $conn->prepare("INSERT INTO scholarship_applications 
                            (student_id, scholarship_id, application_status, academic_year, semester, application_date) 
                            VALUES (?, ?, 'pending', ?, ?, NOW())");
    if (!$stmt) {
        throw new Exception('Database error: ' . $conn->error);
    }
    
    $stmt->bind_param("iiss", $student_id, $scholarship_id, $academic_year, $semester);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to create application: ' . $stmt->error);
    }
    
    $application_id = $conn->insert_id;
    $stmt->close();
    
    if (!$application_id || $application_id <= 0) {
        throw new Exception('Failed to generate application ID');
    }
    
    // Create upload directory if it doesn't exist
    $upload_base = dirname(__DIR__) . "/uploads/scholarships/";
    $upload_dir = $upload_base . $student_id . "/" . $application_id . "/";
    
    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            throw new Exception('Failed to create upload directory');
        }
    }
    
    // Process uploaded files
    $uploaded_files = [];
    $failed_files = [];
    
    foreach ($_FILES as $requirement_name => $file) {
        // Skip if file has an error (except for optional files)
        if ($file['error'] !== UPLOAD_ERR_OK) {
            if ($file['error'] !== UPLOAD_ERR_NO_FILE) {
                $failed_files[] = $requirement_name . ' (Error code: ' . $file['error'] . ')';
            }
            continue;
        }
        
        // Validate file size (max 10MB)
        if ($file['size'] > 10 * 1024 * 1024) {
            $failed_files[] = $requirement_name . ' (File too large)';
            continue;
        }
        
        // Validate file type
        $allowed_types = ['application/pdf', 'application/msword', 
                         'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                         'image/jpeg', 'image/jpg', 'image/png'];
        
        if (!in_array($file['type'], $allowed_types)) {
            $failed_files[] = $requirement_name . ' (Invalid file type)';
            continue;
        }
        
        // Generate safe filename
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $safe_requirement_name = preg_replace("/[^a-zA-Z0-9]/", "_", $requirement_name);
        $safe_filename = $safe_requirement_name . "_" . time() . "_" . uniqid() . "." . $file_extension;
        $file_path = $upload_dir . $safe_filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            // Insert requirement record
            $stmt = $conn->prepare("INSERT INTO scholarship_requirements 
                                   (application_id, requirement_name, file_name, file_path, file_size, file_type, uploaded_at) 
                                   VALUES (?, ?, ?, ?, ?, ?, NOW())");
            
            if (!$stmt) {
                throw new Exception('Database error: ' . $conn->error);
            }
            
            $stmt->bind_param("isssis", 
                $application_id, 
                $requirement_name, 
                $file['name'], 
                $file_path, 
                $file['size'], 
                $file['type']
            );
            
            if ($stmt->execute()) {
                $uploaded_files[] = [
                    'name' => $requirement_name,
                    'filename' => $file['name']
                ];
            } else {
                // If database insert fails, delete the uploaded file
                @unlink($file_path);
                $failed_files[] = $requirement_name . ' (Database error)';
            }
            
            $stmt->close();
        } else {
            $failed_files[] = $requirement_name . ' (Upload failed)';
        }
    }
    
    // Check if at least one file was uploaded successfully
    if (empty($uploaded_files)) {
        throw new Exception('No files were uploaded successfully. Please try again.');
    }
    
    // Commit transaction
    $conn->commit();
    
    $response = [
        'success' => true, 
        'message' => 'Scholarship application submitted successfully!',
        'application_id' => $application_id,
        'scholarship_name' => $scholarship_name,
        'uploaded_files' => $uploaded_files,
        'total_uploaded' => count($uploaded_files)
    ];
    
    if (!empty($failed_files)) {
        $response['warning'] = 'Some files failed to upload: ' . implode(', ', $failed_files);
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    // Rollback transaction on error
    if ($conn) {
        $conn->rollback();
    }
    
    // Clean up uploaded files if application failed
    if (isset($upload_dir) && file_exists($upload_dir)) {
        $files = glob($upload_dir . '*');
        foreach ($files as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }
        @rmdir($upload_dir);
    }
    
    error_log('Scholarship application error: ' . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}

if (isset($conn)) {
    closeDBConnection($conn);
}
?>
