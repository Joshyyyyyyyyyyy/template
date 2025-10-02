<?php
session_start();
require_once '../config/db_config.php';
require_once '../services/EmailServices.php';

// Check if we have pending payment data
if (!isset($_SESSION['pending_payment'])) {
    header('Location: index.php?error=no_payment_data');
    exit;
}

$pending_payment = $_SESSION['pending_payment'];
$source_id = $pending_payment['source_id'];
$payment_id = $pending_payment['payment_id'];

// Verify payment status with PayMongo
$ch = curl_init('https://api.paymongo.com/v1/sources/' . $source_id);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Basic ' . base64_encode(PAYMONGO_SECRET_KEY . ':')
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code !== 200) {
    header('Location: payment_failed.php?error=verification_failed');
    exit;
}

$source_data = json_decode($response, true);
$payment_status = $source_data['data']['attributes']['status'];

// Check if payment was successful
if ($payment_status !== 'chargeable' && $payment_status !== 'paid') {
    header('Location: payment_failed.php?status=' . $payment_status);
    exit;
}

// Update payment status in database
$conn = getDBConnection();

$stmt = $conn->prepare("UPDATE payments SET payment_status = 'completed' WHERE payment_id = ?");
$stmt->bind_param("i", $payment_id);
$stmt->execute();
$stmt->close();

$stmt = $conn->prepare("
    SELECT 
        p.payment_id,
        p.amount,
        p.payment_method,
        p.created_at,
        s.student_id,
        s.name,
        s.email,
        s.program,
        s.year_level,
        s.college,
        s.campus,
        t.semester,
        t.academic_year,
        t.total_amount,
        t.balance
    FROM payments p
    JOIN students s ON p.student_id = s.student_id
    JOIN tuition_fees t ON p.fee_id = t.fee_id
    WHERE p.payment_id = ?
");
$stmt->bind_param("i", $payment_id);
$stmt->execute();
$result = $stmt->get_result();
$payment_details = $result->fetch_assoc();
$stmt->close();

$new_paid_amount = $payment_details['total_amount'] - $payment_details['balance'] + $payment_details['amount'];
$new_balance = $payment_details['balance'] - $payment_details['amount'];

$stmt = $conn->prepare("UPDATE tuition_fees SET paid_amount = ?, balance = ? WHERE fee_id = (SELECT fee_id FROM payments WHERE payment_id = ?)");
$stmt->bind_param("ddi", $new_paid_amount, $new_balance, $payment_id);
$stmt->execute();
$stmt->close();

$conn->close();

// Send email receipt
$emailService = new EmailService();
$receipt_sent = $emailService->sendPaymentReceipt($payment_details);

// Clear pending payment from session
unset($_SESSION['pending_payment']);

// Store success message in session
$_SESSION['payment_success'] = [
    'payment_id' => $payment_id,
    'amount' => $payment_details['amount'],
    'receipt_sent' => $receipt_sent
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #0f172a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            max-width: 500px;
            width: 100%;
            padding: 40px;
            text-align: center;
        }
        
        .success-icon {
            width: 80px;
            height: 80px;
            background: #3b82f6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            animation: scaleIn 0.5s ease-out;
        }
        
        .success-icon svg {
            width: 48px;
            height: 48px;
            stroke: white;
            stroke-width: 3;
            stroke-linecap: round;
            stroke-linejoin: round;
            fill: none;
        }
        
        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }
        
        h1 {
            color: #f1f5f9;
            font-size: 28px;
            margin-bottom: 12px;
        }
        
        .subtitle {
            color: #94a3b8;
            font-size: 16px;
            margin-bottom: 32px;
        }
        
        .details {
            background: #0f172a;
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            text-align: left;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #334155;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            color: #94a3b8;
            font-size: 14px;
        }
        
        .detail-value {
            color: #f1f5f9;
            font-weight: 600;
            font-size: 14px;
        }
        
        .amount {
            font-size: 32px;
            color: #3b82f6;
            font-weight: 700;
            margin: 16px 0;
        }
        
        .email-notice {
            background: #1e3a5f;
            border-left: 4px solid #3b82f6;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            text-align: left;
        }
        
        .email-notice p {
            color: #93c5fd;
            font-size: 14px;
            line-height: 1.5;
        }
        
        .email-notice strong {
            color: #dbeafe;
        }
        
        .email-notice.error {
            background: #3f1f1f;
            border-left-color: #ef4444;
        }
        
        .email-notice.error p {
            color: #fca5a5;
        }
        
        .email-notice.error strong {
            color: #fecaca;
        }
        
        .btn {
            display: inline-block;
            background: #3b82f6;
            color: white;
            padding: 14px 32px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        
        .btn:hover {
            background: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">
            <svg viewBox="0 0 24 24">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
        </div>
        
        <h1>Payment Successful!</h1>
        <p class="subtitle">Your payment has been processed successfully</p>
        
        <div class="amount">₱<?php echo number_format($payment_details['amount'], 2); ?></div>
        
        <div class="details">
            <div class="detail-row">
                <span class="detail-label">Payment ID</span>
                <span class="detail-value">#<?php echo str_pad($payment_id, 6, '0', STR_PAD_LEFT); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Student ID</span>
                <span class="detail-value"><?php echo htmlspecialchars($payment_details['student_id']); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Student Name</span>
                <span class="detail-value"><?php echo htmlspecialchars($payment_details['name']); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Program</span>
                <span class="detail-value"><?php echo htmlspecialchars($payment_details['program']); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Semester</span>
                <span class="detail-value"><?php echo htmlspecialchars($payment_details['semester'] . ' - ' . $payment_details['academic_year']); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Payment Method</span>
                <span class="detail-value"><?php echo strtoupper(htmlspecialchars($payment_details['payment_method'])); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Date</span>
                <span class="detail-value"><?php echo date('M d, Y h:i A', strtotime($payment_details['created_at'])); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Remaining Balance</span>
                <span class="detail-value">₱<?php echo number_format($new_balance, 2); ?></span>
            </div>
        </div>
        
        <?php if ($receipt_sent): ?>
        <div class="email-notice">
            <p>📧 A receipt has been sent to <strong><?php echo htmlspecialchars($payment_details['email']); ?></strong></p>
        </div>
        <?php else: ?>
        <div class="email-notice error">
            <p>⚠️ Receipt email could not be sent. Please contact support with Payment ID <strong>#<?php echo str_pad($payment_id, 6, '0', STR_PAD_LEFT); ?></strong></p>
        </div>
        <?php endif; ?>
        
        <a href="tutionbalance.php" class="btn">Check Your Balance</a>
    </div>
</body>
</html>
