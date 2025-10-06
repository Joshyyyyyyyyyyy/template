<?php
require_once '../config/db_config.php';

// This file allows you to reset passwords for non-student users
// Access it directly and submit the form

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    
    if (empty($email) || empty($new_password)) {
        $message = 'Email and password are required';
        $messageType = 'error';
    } else {
        $conn = getDBConnection();
        
        // Check if user exists
        $checkStmt = $conn->prepare("SELECT user_id, user_type FROM users WHERE email = ?");
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        
        if ($result->num_rows === 0) {
            $message = 'User not found with this email';
            $messageType = 'error';
        } else {
            $user = $result->fetch_assoc();
            
            // Hash the new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            // Update password
            $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
            $updateStmt->bind_param("ss", $hashed_password, $email);
            
            if ($updateStmt->execute()) {
                $message = "Password updated successfully for {$email} (User Type: {$user['user_type']})";
                $messageType = 'success';
            } else {
                $message = 'Failed to update password';
                $messageType = 'error';
            }
            
            $updateStmt->close();
        }
        
        $checkStmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Admin Tool</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
        }
        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }
        button {
            background: #3b82f6;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        button:hover {
            background: #2563eb;
        }
        .message {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔧 Password Reset Tool</h2>
        
        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required placeholder="user@example.com">
            </div>
            
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="text" id="new_password" name="new_password" required placeholder="Enter new password (min 8 characters)" minlength="8">
            </div>
            
            <button type="submit">Reset Password</button>
        </form>
        
        <div class="info">
            <strong>📋 Instructions:</strong>
            <ol style="margin: 10px 0 0 20px; padding: 0;">
                <li>Enter the email of the user whose password you want to reset</li>
                <li>Enter a new password (minimum 8 characters)</li>
                <li>Click "Reset Password"</li>
                <li>Use the new password to log in</li>
            </ol>
        </div>
        
        <div class="info" style="margin-top: 15px;">
            <strong>🔍 Current Non-Student Users:</strong>
            <ul style="margin: 10px 0 0 20px; padding: 0;">
                <li>andrie@gmail.com (scholarship_coordinator)</li>
                <li>suruiz@gmail.com (admin)</li>
            </ul>
        </div>
    </div>
</body>
</html>
