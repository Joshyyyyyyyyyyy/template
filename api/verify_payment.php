<?php
session_start();
require_once '../config/db_config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['pending_payment'])) {
    echo json_encode(['success' => false, 'message' => 'No pending payment found']);
    exit;
}

$pending = $_SESSION['pending_payment'];
$source_id = $pending['source_id'];

// Check payment status from PayMongo
$ch = curl_init('https://api.paymongo.com/v1/sources/' . $source_id);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Basic ' . base64_encode(PAYMONGO_SECRET_KEY . ':')
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code !== 200) {
    echo json_encode(['success' => false, 'message' => 'Failed to verify payment']);
    exit;
}

$source_data = json_decode($response, true);
$status = $source_data['data']['attributes']['status'];

$conn = getDBConnection();

if ($status === 'chargeable') {
    // Create payment intent
    $payment_data = [
        'data' => [
            'attributes' => [
                'amount' => intval($pending['amount'] * 100),
                'payment_method_allowed' => ['gcash', 'grab_pay'],
                'payment_method_options' => [
                    'card' => ['request_three_d_secure' => 'any']
                ],
                'currency' => 'PHP',
                'description' => 'Tuition Payment'
            ]
        ]
    ];

    $ch = curl_init('https://api.paymongo.com/v1/payment_intents');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payment_data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Basic ' . base64_encode(PAYMONGO_SECRET_KEY . ':')
    ]);

    $payment_response = curl_exec($ch);
    curl_close($ch);

    $payment_intent = json_decode($payment_response, true);
    $payment_intent_id = $payment_intent['data']['id'];

    // Attach source to payment intent
    $attach_data = [
        'data' => [
            'attributes' => [
                'payment_method' => $source_id
            ]
        ]
    ];

    $ch = curl_init('https://api.paymongo.com/v1/payment_intents/' . $payment_intent_id . '/attach');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($attach_data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Basic ' . base64_encode(PAYMONGO_SECRET_KEY . ':')
    ]);

    $attach_response = curl_exec($ch);
    curl_close($ch);

    // Update payment status
    $stmt = $conn->prepare("UPDATE payments SET payment_status = 'completed', paymongo_payment_id = ? WHERE payment_id = ?");
    $stmt->bind_param("si", $payment_intent_id, $pending['payment_id']);
    $stmt->execute();
    $stmt->close();

    // Update tuition balance
    $stmt = $conn->prepare("UPDATE tuition_fees SET paid_amount = paid_amount + ?, balance = balance - ? WHERE fee_id = ?");
    $stmt->bind_param("ddi", $pending['amount'], $pending['amount'], $pending['fee_id']);
    $stmt->execute();
    $stmt->close();

    unset($_SESSION['pending_payment']);
    
    echo json_encode(['success' => true, 'status' => 'completed']);
} else if ($status === 'failed' || $status === 'cancelled') {
    // Update payment status
    $stmt = $conn->prepare("UPDATE payments SET payment_status = ? WHERE payment_id = ?");
    $stmt->bind_param("si", $status, $pending['payment_id']);
    $stmt->execute();
    $stmt->close();

    unset($_SESSION['pending_payment']);
    
    echo json_encode(['success' => false, 'status' => $status]);
} else {
    echo json_encode(['success' => false, 'status' => 'pending']);
}

$conn->close();
?>
