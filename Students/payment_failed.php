<?php
session_start();

$error_message = 'Payment was not completed. Please try again.';
$error_details = '';

if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'no_payment_data':
            $error_message = 'No payment information found.';
            break;
        case 'verification_failed':
            $error_message = 'Unable to verify payment status.';
            break;
    }
}

if (isset($_GET['status'])) {
    $error_details = 'Payment status: ' . htmlspecialchars($_GET['status']);
}

// Clear pending payment from session
if (isset($_SESSION['pending_payment'])) {
    unset($_SESSION['pending_payment']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 100%;
            padding: 40px;
            text-align: center;
        }
        
        .error-icon {
            width: 80px;
            height: 80px;
            background: #ef4444;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            animation: shake 0.5s ease-out;
        }
        
        .error-icon svg {
            width: 48px;
            height: 48px;
            stroke: white;
            stroke-width: 3;
            stroke-linecap: round;
            stroke-linejoin: round;
            fill: none;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }
        
        h1 {
            color: #1f2937;
            font-size: 28px;
            margin-bottom: 12px;
        }
        
        .subtitle {
            color: #6b7280;
            font-size: 16px;
            margin-bottom: 24px;
        }
        
        .error-details {
            background: #fee2e2;
            border-left: 4px solid #ef4444;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            text-align: left;
        }
        
        .error-details p {
            color: #991b1b;
            font-size: 14px;
            line-height: 1.5;
        }
        
        .btn-group {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            display: inline-block;
            padding: 14px 32px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5568d3;
        }
        
        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
        }
        
        .btn-secondary:hover {
            background: #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-icon">
            <svg viewBox="0 0 24 24">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </div>
        
        <h1>Payment Failed</h1>
        <p class="subtitle"><?php echo htmlspecialchars($error_message); ?></p>
        
        <?php if ($error_details): ?>
        <div class="error-details">
            <p><?php echo htmlspecialchars($error_details); ?></p>
        </div>
        <?php endif; ?>
        
        <div class="btn-group">
            <a href="payments.php" class="btn btn-primary">Try Again</a>
            <a href="index.php" class="btn btn-secondary">Return to Dashboard</a>
        </div>
    </div>
</body>
</html>
