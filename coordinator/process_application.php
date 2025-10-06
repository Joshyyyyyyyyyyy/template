<?php
/**
 * Process Scholarship Application Actions
 * Handles approve, reject, and status update operations
 */

session_start();
header('Content-Type: application/json');

// Check if user is logged in and is a scholarship coordinator
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'scholarship_coordinator') {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access. Please login as scholarship coordinator.'
    ]);
    exit();
}

require_once '../config/db_config.php';

// Validate required parameters
if (!isset($_POST['application_id']) || !isset($_POST['action'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required parameters.'
    ]);
    exit();
}

$application_id = intval($_POST['application_id']);
$action = $_POST['action'];
$conn = getDBConnection();

try {
    switch ($action) {
        case 'update_status':
            // Update application status to under_review
            if (!isset($_POST['status'])) {
                throw new Exception('Status parameter is required.');
            }
            
            $status = $_POST['status'];
            $allowed_statuses = ['pending', 'under_review', 'approved', 'rejected'];
            
            if (!in_array($status, $allowed_statuses)) {
                throw new Exception('Invalid status value.');
            }
            
            $sql = "UPDATE scholarship_applications 
                    SET application_status = ?,
                        review_date = NOW(),
                        reviewed_by = ?
                    WHERE application_id = ?";
            
            $reviewer_name = $_SESSION['name'] ?? 'Scholarship Coordinator';
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $status, $reviewer_name, $application_id);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to update application status.');
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Application status updated successfully.'
            ]);
            break;
            
        case 'approve':
            // Approve application with scholarship percentage
            if (!isset($_POST['scholarship_percentage'])) {
                throw new Exception('Scholarship percentage is required.');
            }
            
            $scholarship_percentage = floatval($_POST['scholarship_percentage']);
            $remarks = isset($_POST['remarks']) ? trim($_POST['remarks']) : '';
            
            if ($scholarship_percentage < 0 || $scholarship_percentage > 100) {
                throw new Exception('Scholarship percentage must be between 0 and 100.');
            }
            
            // Start transaction
            $conn->begin_transaction();
            
            // Get student's tuition fee information
            $sql_tuition = "SELECT tf.fee_id, tf.tuition_fee, tf.misc_fees, tf.enrollment_fees, 
                                   tf.less_payment, sa.student_id
                            FROM scholarship_applications sa
                            INNER JOIN tuition_fees tf ON sa.student_id = tf.student_id
                            WHERE sa.application_id = ? 
                            AND tf.academic_year = sa.academic_year 
                            AND tf.semester = sa.semester";
            
            $stmt_tuition = $conn->prepare($sql_tuition);
            $stmt_tuition->bind_param("i", $application_id);
            $stmt_tuition->execute();
            $result_tuition = $stmt_tuition->get_result();
            $tuition_data = $result_tuition->fetch_assoc();
            
            if (!$tuition_data) {
                throw new Exception('Tuition fee record not found for this application.');
            }
            
            // Scholarship covers miscellaneous fees only. Enrollment fees (medical + ID) must always be paid.
            // Fixed scholarship amounts based on percentage level:
            // 25% = 1,250, 50% = 2,250, 75% = 3,250, 100% = 5,175
            $scholarship_amounts = [
                25 => 1250,
                50 => 2250,
                75 => 3250,
                100 => 5175
            ];
            
            // Get the scholarship amount based on percentage, or calculate if not in fixed amounts
            if (isset($scholarship_amounts[$scholarship_percentage])) {
                $scholarship_amount = $scholarship_amounts[$scholarship_percentage];
            } else {
                // For other percentages, calculate proportionally
                $scholarship_amount = ($tuition_data['misc_fees'] * $scholarship_percentage) / 100;
            }
            
            $reviewer_name = $_SESSION['name'] ?? 'Scholarship Coordinator';
            
            $sql_update = "UPDATE scholarship_applications 
                          SET application_status = 'approved',
                              scholarship_percentage = ?,
                              scholarship_amount = ?,
                              review_date = NOW(),
                              reviewed_by = ?,
                              remarks = ?
                          WHERE application_id = ?";
            
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("ddssi", $scholarship_percentage, $scholarship_amount, 
                                     $reviewer_name, $remarks, $application_id);
            
            if (!$stmt_update->execute()) {
                throw new Exception('Failed to approve application: ' . $stmt_update->error);
            }
            
            // Total amount to pay = misc_fees + enrollment_fees
            // Balance = (misc_fees + enrollment_fees) - scholarship_amount - less_payment
            $new_less_payment = $tuition_data['less_payment'] + $scholarship_amount;
            $total_payable = $tuition_data['misc_fees'] + $tuition_data['enrollment_fees'];
            $new_balance = $total_payable - $new_less_payment;
            
            // Ensure balance doesn't go negative
            if ($new_balance < 0) {
                $new_balance = 0;
            }
            
            $sql_tuition_update = "UPDATE tuition_fees 
                                  SET less_payment = ?,
                                      balance = ?
                                  WHERE fee_id = ?";
            
            $stmt_tuition_update = $conn->prepare($sql_tuition_update);
            $stmt_tuition_update->bind_param("ddi", $new_less_payment, $new_balance, 
                                             $tuition_data['fee_id']);
            
            if (!$stmt_tuition_update->execute()) {
                throw new Exception('Failed to update tuition fees.');
            }
            
            // Commit transaction
            $conn->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Application approved successfully.',
                'scholarship_amount' => $scholarship_amount,
                'new_balance' => $new_balance
            ]);
            break;
            
        case 'reject':
            // Reject application with remarks
            if (!isset($_POST['remarks']) || empty(trim($_POST['remarks']))) {
                throw new Exception('Rejection reason is required.');
            }
            
            $remarks = trim($_POST['remarks']);
            
            $sql = "UPDATE scholarship_applications 
                    SET application_status = 'rejected',
                        review_date = NOW(),
                        reviewed_by = ?,
                        remarks = ?
                    WHERE application_id = ?";
            
            $reviewer_name = $_SESSION['name'] ?? 'Scholarship Coordinator';
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $reviewer_name, $remarks, $application_id);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to reject application.');
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Application rejected successfully.'
            ]);
            break;
            
        default:
            throw new Exception('Invalid action specified.');
    }
    
} catch (Exception $e) {
    // Rollback transaction if active
    if ($conn->connect_errno === 0) {
        $conn->rollback();
    }
    
    error_log("Application Processing Error: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>
