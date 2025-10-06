<?php
session_start();
require_once __DIR__ . '/../config/db_config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Check if application ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = 'Invalid application ID';
    header('Location: approved.php');
    exit();
}

$application_id = intval($_GET['id']);

// Connect to database
$conn = getDBConnection();

// Update application status to archived
$sql = "UPDATE scholarship_applications 
        SET application_status = 'archived' 
        WHERE application_id = ? AND application_status = 'approved'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $application_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        $_SESSION['success'] = 'Application successfully archived';
    } else {
        $_SESSION['error'] = 'Application not found or already archived';
    }
} else {
    $_SESSION['error'] = 'Failed to archive application: ' . $conn->error;
}

$stmt->close();
$conn->close();

// Redirect back to approved page
header('Location: approved.php');
exit();
?>
