<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

class EmailService {
    private $mailer;
    
    public function __construct() {
        $this->mailer = new PHPMailer(true);
        $this->configureMailer();
    }
    
    private function configureMailer() {
        try {
            // Server settings
            $this->mailer->isSMTP();
            $this->mailer->Host = 'smtp.gmail.com';
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = 'suruiz.joshuabcp@gmail.com';
            $this->mailer->Password = 'aovb dqcb sqve rbsa'; // App password
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port = 587;
            
            // Sender info
            $this->mailer->setFrom('suruiz.joshuabcp@gmail.com', 'Payment Management System');
            $this->mailer->isHTML(true);
            $this->mailer->CharSet = 'UTF-8';
        } catch (Exception $e) {
            error_log("Mailer configuration error: " . $e->getMessage());
        }
    }
    
    public function sendPaymentReceipt($payment_details) {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($payment_details['email'], $payment_details['name']);
            
            $this->mailer->Subject = 'Payment Receipt - Payment ID #' . str_pad($payment_details['payment_id'], 6, '0', STR_PAD_LEFT);
            
            // Generate receipt HTML
            $html_body = $this->generateReceiptHTML($payment_details);
            $this->mailer->Body = $html_body;
            
            // Plain text alternative
            $this->mailer->AltBody = $this->generateReceiptPlainText($payment_details);
            
            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Email sending failed: " . $this->mailer->ErrorInfo);
            return false;
        }
    }
    
    private function generateReceiptHTML($details) {
        $payment_id = str_pad($details['payment_id'], 6, '0', STR_PAD_LEFT);
        $amount = number_format($details['amount'], 2);
        $date = date('F d, Y h:i A', strtotime($details['created_at']));
        $student_name = htmlspecialchars($details['name']);
        $student_id = htmlspecialchars($details['student_id']);
        $program = htmlspecialchars($details['program']);
        $year_level = htmlspecialchars($details['year_level']);
        $semester = htmlspecialchars($details['semester']);
        $academic_year = htmlspecialchars($details['academic_year']);
        $payment_method = strtoupper(htmlspecialchars($details['payment_method']));
        $balance = number_format($details['balance'] - $details['amount'], 2);
        
        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif; background-color: #0f172a;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #0f172a; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #1e293b; border-radius: 12px; border: 1px solid #334155; overflow: hidden;">
                    <tr>
                        <td style="background-color: #1e293b; padding: 40px 40px 30px; text-align: center; border-bottom: 1px solid #334155;">
                            <div style="width: 80px; height: 80px; background-color: #3b82f6; border-radius: 50%; margin: 0 auto 20px; display: inline-flex; align-items: center; justify-content: center;">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20 6L9 17L4 12" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 700;">Payment Successful</h1>
                            <p style="margin: 12px 0 0; color: #94a3b8; font-size: 16px;">Thank you for your payment</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <td style="padding: 40px 40px 20px; text-align: center; background-color: #1e293b;">
                            <div style="font-size: 48px; font-weight: 700; color: #3b82f6; margin-bottom: 8px;">₱{$amount}</div>
                            <p style="margin: 0; color: #94a3b8; font-size: 14px;">Payment ID: #{$payment_id}</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <td style="padding: 0 40px 40px; background-color: #1e293b;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #0f172a; border-radius: 8px; padding: 24px; border: 1px solid #334155;">
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #334155;">
                                        <table width="100%">
                                            <tr>
                                                <td style="color: #94a3b8; font-size: 14px;">Student Name</td>
                                                <td align="right" style="color: #f1f5f9; font-size: 14px; font-weight: 600;">{$student_name}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #334155;">
                                        <table width="100%">
                                            <tr>
                                                <td style="color: #94a3b8; font-size: 14px;">Student ID</td>
                                                <td align="right" style="color: #f1f5f9; font-size: 14px; font-weight: 600;">{$student_id}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #334155;">
                                        <table width="100%">
                                            <tr>
                                                <td style="color: #94a3b8; font-size: 14px;">Program</td>
                                                <td align="right" style="color: #f1f5f9; font-size: 14px; font-weight: 600;">{$program}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #334155;">
                                        <table width="100%">
                                            <tr>
                                                <td style="color: #94a3b8; font-size: 14px;">Year Level</td>
                                                <td align="right" style="color: #f1f5f9; font-size: 14px; font-weight: 600;">{$year_level}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #334155;">
                                        <table width="100%">
                                            <tr>
                                                <td style="color: #94a3b8; font-size: 14px;">Semester</td>
                                                <td align="right" style="color: #f1f5f9; font-size: 14px; font-weight: 600;">{$semester} - {$academic_year}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #334155;">
                                        <table width="100%">
                                            <tr>
                                                <td style="color: #94a3b8; font-size: 14px;">Payment Method</td>
                                                <td align="right" style="color: #f1f5f9; font-size: 14px; font-weight: 600;">{$payment_method}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #334155;">
                                        <table width="100%">
                                            <tr>
                                                <td style="color: #94a3b8; font-size: 14px;">Date & Time</td>
                                                <td align="right" style="color: #f1f5f9; font-size: 14px; font-weight: 600;">{$date}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0;">
                                        <table width="100%">
                                            <tr>
                                                <td style="color: #94a3b8; font-size: 14px;">Remaining Balance</td>
                                                <td align="right" style="color: #f1f5f9; font-size: 14px; font-weight: 600;">₱{$balance}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <tr>
                        <td style="padding: 30px 40px; background-color: #0f172a; border-top: 1px solid #334155; text-align: center;">
                            <p style="margin: 0 0 8px; color: #94a3b8; font-size: 14px;">This is an automated receipt. Please keep it for your records.</p>
                            <p style="margin: 0; color: #64748b; font-size: 12px;">If you have any questions, please contact our support team.</p>
                        </td>
                    </tr>
                </table>
                
                <table width="600" cellpadding="0" cellspacing="0" style="margin-top: 20px;">
                    <tr>
                        <td style="text-align: center; padding: 20px;">
                            <p style="margin: 0; color: #64748b; font-size: 12px;">© 2025 Payment Management System. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }
    
    private function generateReceiptPlainText($details) {
        $payment_id = str_pad($details['payment_id'], 6, '0', STR_PAD_LEFT);
        $amount = number_format($details['amount'], 2);
        $date = date('F d, Y h:i A', strtotime($details['created_at']));
        $student_name = $details['name'];
        $balance = number_format($details['balance'] - $details['amount'], 2);
        
        return <<<TEXT
PAYMENT RECEIPT
===============

Payment Successful!

Amount: ₱{$amount}
Payment ID: #{$payment_id}

PAYMENT DETAILS
---------------

Student Name: {$student_name}
Student ID: {$details['student_id']}
Program: {$details['program']}
Year Level: {$details['year_level']}
Semester: {$details['semester']} - {$details['academic_year']}
Payment Method: {$details['payment_method']}
Date & Time: {$date}
Remaining Balance: ₱{$balance}

This is an automated receipt. Please keep it for your records.

© 2025 Payment Management System. All rights reserved.
TEXT;
    }
}
?>
