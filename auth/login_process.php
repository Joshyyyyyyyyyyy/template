<?php
require_once '../config/db_config.php';
require_once '../config/session.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$user_type = $_POST['user_type'] ?? '';

// Validate inputs
if (empty($email) || empty($password) || empty($user_type)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit();
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit();
}

$conn = getDBConnection();

$stmt = $conn->prepare("SELECT u.user_id, u.email, u.password, u.user_type, u.name as user_name, u.position, u.profile_picture, u.is_active, s.name as student_name, s.student_id 
                        FROM users u 
                        LEFT JOIN students s ON u.user_id = s.user_id 
                        WHERE u.email = ? AND u.user_type = ?");
$stmt->bind_param("ss", $email, $user_type);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        'success' => false, 
        'message' => 'Invalid credentials. Please check your email and user type.'
    ]);
    $stmt->close();
    $conn->close();
    exit();
}

$user = $result->fetch_assoc();

// Check if account is active
if (!$user['is_active']) {
    echo json_encode(['success' => false, 'message' => 'Account is deactivated. Please contact administrator.']);
    $stmt->close();
    $conn->close();
    exit();
}

$passwordMatches = false;
if (password_get_info($user['password'])['algo'] === null) {
    // Password is not hashed, compare directly (NOT RECOMMENDED FOR PRODUCTION)
    $passwordMatches = ($password === $user['password']);
    
    if ($passwordMatches) {
        // Hash the password for future use
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $updatePassStmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        $updatePassStmt->bind_param("si", $hashedPassword, $user['user_id']);
        $updatePassStmt->execute();
        $updatePassStmt->close();
    }
} else {
    // Password is hashed, verify normally
    $passwordMatches = password_verify($password, $user['password']);
}

if (!$passwordMatches) {
    echo json_encode([
        'success' => false, 
        'message' => 'Invalid credentials. Please check your password.'
    ]);
    $stmt->close();
    $conn->close();
    exit();
}

// Update last login
$updateStmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE user_id = ?");
$updateStmt->bind_param("i", $user['user_id']);
$updateStmt->execute();
$updateStmt->close();

$_SESSION['user_id'] = $user['user_id'];
$_SESSION['email'] = $user['email'];
$_SESSION['user_type'] = $user['user_type'];
$_SESSION['profile_picture'] = $user['profile_picture'];
// Use user_name for non-students, student_name for students
$_SESSION['name'] = $user['user_type'] === 'student' ? $user['student_name'] : $user['user_name'];
$_SESSION['position'] = $user['position'] ?? null;
$_SESSION['student_id'] = $user['student_id'] ?? null;
$_SESSION['last_activity'] = time();

// Determine redirect URL based on user type
$redirectUrls = [
    'student' => '/pms/students/index.php',
    'scholarship_coordinator' => '/pms/coordinator/application.php',
    'financial_controller' => '/pms/financial/dashboard.php',
    'admin' => '/pms/admin/dashboard.php'
];

$redirectUrl = $redirectUrls[$user['user_type']] ?? '/dashboard.php';

echo json_encode([
    'success' => true, 
    'message' => 'Login successful',
    'redirect' => $redirectUrl,
    'user' => [
        'name' => $_SESSION['name'],
        'email' => $user['email'],
        'user_type' => $user['user_type'],
        'position' => $user['position']
    ]
]);

$stmt->close();
$conn->close();
?>
