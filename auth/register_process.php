<?php
require_once '../config/db_config.php';
require_once '../config/session.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

// Get form data
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$program = trim($_POST['program'] ?? '');
$year_level = trim($_POST['year_level'] ?? '');
$college = trim($_POST['college'] ?? '');
$campus = trim($_POST['campus'] ?? '');
$student_status = $_POST['student_status'] ?? 'regular';

// Validate inputs
if (empty($name) || empty($email) || empty($password) || empty($confirm_password) || 
    empty($program) || empty($year_level) || empty($college) || empty($campus)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit();
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit();
}

// Validate password match
if ($password !== $confirm_password) {
    echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
    exit();
}

// Validate password strength (minimum 8 characters)
if (strlen($password) < 8) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters long']);
    exit();
}

$conn = getDBConnection();

// Check if email already exists
$checkStmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
$checkStmt->bind_param("s", $email);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Email already registered']);
    $checkStmt->close();
    $conn->close();
    exit();
}
$checkStmt->close();

// Handle profile picture upload
$profile_picture = null;
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    $file_type = $_FILES['profile_picture']['type'];
    $file_size = $_FILES['profile_picture']['size'];
    
    if (!in_array($file_type, $allowed_types)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, and GIF allowed.']);
        $conn->close();
        exit();
    }
    
    if ($file_size > $max_size) {
        echo json_encode(['success' => false, 'message' => 'File size exceeds 5MB limit']);
        $conn->close();
        exit();
    }
    
    // Create upload directory if it doesn't exist
    $upload_dir = '../uploads/profiles/students/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Generate unique filename
    $file_extension = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
    $unique_filename = 'student_' . time() . '_' . uniqid() . '.' . $file_extension;
    $upload_path = $upload_dir . $unique_filename;
    
    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path)) {
        $profile_picture = '/uploads/profiles/students/' . $unique_filename;
    }
}

// Start transaction
$conn->begin_transaction();

try {
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert into users table
    $userStmt = $conn->prepare("INSERT INTO users (email, password, user_type, profile_picture, is_active) VALUES (?, ?, 'student', ?, 1)");
    $userStmt->bind_param("sss", $email, $hashed_password, $profile_picture);
    $userStmt->execute();
    $user_id = $conn->insert_id;
    $userStmt->close();
    
    // Insert into students table
    $studentStmt = $conn->prepare("INSERT INTO students (user_id, name, email, program, year_level, college, campus, student_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $studentStmt->bind_param("isssssss", $user_id, $name, $email, $program, $year_level, $college, $campus, $student_status);
    $studentStmt->execute();
    $student_id = $conn->insert_id;
    $studentStmt->close();
    
    $tuitionStmt = $conn->prepare("INSERT INTO tuition_fees (student_id, semester, academic_year, tuition_fee, tuition_sponsored, misc_fees, enrollment_fees, less_payment, total_amount, paid_amount, balance) VALUES (?, '1st Semester', 'AY 2025-2026', 9000.00, 1, 5175.00, 850.00, 0.00, 6025.00, 0.00, 6025.00)");
    $tuitionStmt->bind_param("i", $student_id);
    $tuitionStmt->execute();
    $tuitionStmt->close();
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Registration successful! You can now login.',
        'redirect' => '/PMS/login.php'
    ]);
    
} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    
    // Delete uploaded file if exists
    if ($profile_picture && file_exists('../' . $profile_picture)) {
        unlink('../' . $profile_picture);
    }
    
    echo json_encode(['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()]);
}

$conn->close();
?>
