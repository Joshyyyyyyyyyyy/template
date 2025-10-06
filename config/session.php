<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Session timeout (30 minutes)
define('SESSION_TIMEOUT', 1800);

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_type']);
}

// Check session timeout
function checkSessionTimeout() {
    if (isset($_SESSION['last_activity'])) {
        $elapsed = time() - $_SESSION['last_activity'];
        if ($elapsed > SESSION_TIMEOUT) {
            session_unset();
            session_destroy();
            return false;
        }
    }
    $_SESSION['last_activity'] = time();
    return true;
}

// Require login
function requireLogin() {
    if (!isLoggedIn() || !checkSessionTimeout()) {
        header('Location: /login.php');
        exit();
    }
}

// Require specific user type
function requireUserType($allowedTypes) {
    requireLogin();
    if (!in_array($_SESSION['user_type'], $allowedTypes)) {
        header('Location: /unauthorized.php');
        exit();
    }
}

// Get current user data
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'user_id' => $_SESSION['user_id'],
        'email' => $_SESSION['email'],
        'user_type' => $_SESSION['user_type'],
        'profile_picture' => $_SESSION['profile_picture'] ?? null,
        'name' => $_SESSION['name'] ?? null
    ];
}

// Logout
function logout() {
    session_unset();
    session_destroy();
    header('Location: ../login.php');
    exit();
}
?>
