<?php
session_start();
require_once '../config/db_config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$student_id = $data['student_id'] ?? null;
$fee_id = $data['fee_id'] ?? null;
$amount = $data['amount'] ?? null;
$payment_method = $data['payment_method'] ?? null;

// Validate input
if (!$student_id || !$fee_id || !$amount || !$payment_method) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Validate amount
if ($amount <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid amount']);
    exit;
}

// Create PayMongo source
$source_type = ($payment_method === 'gcash') ? 'gcash' : 'grab_pay';

$source_data = [
    'data' => [
        'attributes' => [
            'amount' => intval($amount * 100), // Convert to centavos
            'redirect' => [
                'success' => 'http://localhost/PMS/Students/payment_success.php',
                'failed' => 'http://localhost/PMS/Students/payment_failed.php'
            ],
            'type' => $source_type,
            'currency' => 'PHP'
        ]
    ]
];

// Call PayMongo API to create source
$ch = curl_init('https://api.paymongo.com/v1/sources');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($source_data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Basic ' . base64_encode(PAYMONGO_SECRET_KEY . ':')
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code !== 200 && $http_code !== 201) {
    echo json_encode(['success' => false, 'message' => 'Payment gateway error', 'details' => $response]);
    exit;
}

$source_response = json_decode($response, true);
$source_id = $source_response['data']['id'];
$checkout_url = $source_response['data']['attributes']['redirect']['checkout_url'];

// Save payment record as pending
$conn = getDBConnection();
$stmt = $conn->prepare("INSERT INTO payments (student_id, fee_id, amount, payment_method, payment_status, paymongo_source_id) VALUES (?, ?, ?, ?, 'pending', ?)");
$stmt->bind_param("iidss", $student_id, $fee_id, $amount, $payment_method, $source_id);
$stmt->execute();
$payment_id = $stmt->insert_id;
$stmt->close();
$conn->close();

// Store payment info in session for verification
$_SESSION['pending_payment'] = [
    'payment_id' => $payment_id,
    'source_id' => $source_id,
    'amount' => $amount,
    'student_id' => $student_id,
    'fee_id' => $fee_id
];

echo json_encode([
    'success' => true,
    'checkout_url' => $checkout_url,
    'payment_id' => $payment_id
]);
?>
