<?php
session_start();
require_once '../config/db_config.php';

$student_id = 1; // Hardcoded for demo
$conn = getDBConnection();

$stmt = $conn->prepare("SELECT s.*, t.fee_id, t.semester, t.academic_year, t.tuition_fee, t.misc_fees, t.enrollment_fees, t.balance, t.paid_amount FROM students s LEFT JOIN tuition_fees t ON s.student_id = t.student_id WHERE s.student_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

$stmt = $conn->prepare("SELECT * FROM payments WHERE student_id = ? AND payment_status = 'completed' ORDER BY created_at DESC");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$payments = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$actual_balance = $student['balance'];
$total_paid = $student['paid_amount'];
$is_regular = ($student['student_status'] === 'regular');

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Management System</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="theme-dark">
    <div id="sidebarOverlay" class="sidebar-overlay"></div>
    
    <div id="searchOverlay" class="search-overlay">
        <div class="search-container">
            <div class="search-header">
                <input type="text" id="globalSearch" placeholder="Search anything..." class="global-search-input">
                <button class="search-close" onclick="closeGlobalSearch()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <div class="search-results" id="searchResults">
                <div class="search-suggestion" data-section="tuition-balance">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="1" x2="12" y2="23"></line>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                    </svg>
                    <span>Check tuition balance</span>
                </div>
                <div class="search-suggestion" data-section="scholarship-application">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                        <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                    </svg>
                    <span>Apply for scholarship</span>
                </div>
                <div class="search-suggestion" data-section="payment-portal">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                        <line x1="1" y1="10" x2="23" y2="10"></line>
                    </svg>
                    <span>Payment Portal</span>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-container">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <span class="logo-text">Joshua University</span>
                </div>
                <div class="user-profile">
                    <img class="user-avatar" src="../img/joshua.jpeg" alt="Avatar">
                    <div class="user-info">
                        <span class="user-name"><?php echo htmlspecialchars($student['name']); ?></span>
                        <span class="user-role">Student</span>
                    </div>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section">
                    <span class="nav-section-title">Main</span>
                    <ul>
                        <li><a href="index.php" class="nav-link" data-section="dashboard" title="Dashboard">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="7" height="7"></rect>
                                <rect x="14" y="3" width="7" height="7"></rect>
                                <rect x="14" y="14" width="7" height="7"></rect>
                                <rect x="3" y="14" width="7" height="7"></rect>
                            </svg>
                            <span class="nav-text">Dashboard</span>
                        </a></li>
                        <li><a href="tutionbalance.php" class="nav-link active" data-section="tuition-balance" title="Tuition Balance">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="1" x2="12" y2="23"></line>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                            <span class="nav-text">Tuition Balance</span>
                        </a></li>
                        <li><a href="scholarship.php" class="nav-link" data-section="scholarship-application" title="Scholarship">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                                <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                            </svg>
                            <span class="nav-text">Scholarship</span>
                        </a></li>
                    </ul>
                </div>

                <div class="nav-section">
                    <span class="nav-section-title">Financial</span>
                    <ul>
                        <li><a href="paymentportal.php" class="nav-link" data-section="payment-portal" title="Payment Portal">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                                <line x1="1" y1="10" x2="23" y2="10"></line>
                            </svg>
                            <span class="nav-text">Payment Portal</span>
                        </a></li>
                        <li><a href="history.php" class="nav-link" data-section="history" title="History">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12,6 12,12 16,14"></polyline>
                            </svg>
                            <span class="nav-text">History</span>
                        </a></li>
                    </ul>
                </div>

               <div class="nav-section">
                    <span class="nav-section-title">Account Settings</span>
                    <ul>
                         <li><a href="account.php" class="nav-link" data-section="account-settings" title="Settings">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-user-icon lucide-circle-user">
                               <circle cx="12" cy="12" r="10"/><circle cx="12" cy="10" r="3"/>
                               <path d="M7 20.662V19a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v1.662"/></svg>
                            <span class="nav-text">Account</span>
                        </a></li>
                         <li><a href="security.php" class="nav-link" data-section="account-settings" title="Settings">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-fingerprint-icon lucide-fingerprint"><path d="M12 10a2 0 0 0-2 2c0 1.02-.1 2.51-.26 4"/><path d="M14 13.12c0 2.38 0 6.38-1 8.88"/><path d="M17.29 21.02c.12-.6.43-2.3.5-3.02"/><path d="M2 12a10 10 0 0 1 18-6"/><path d="M2 16h.01"/><path d="M21.8 16c.2-2 .131-5.354 0-6"/>
                            <path d="M5 19.5C5.5 18 6 15 6 12a6 6 0 0 1 .34-2"/><path d="M8.65 22c.21-.66.45-1.32.57-2"/><path d="M9 6.8a6 6 0 0 1 9 5.2v2"/></svg>
                            <span class="nav-text">Security</span>
                        </a></li>
                        <li><a href="studentinfo.php" class="nav-link" data-section="account-settings" title="Settings">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-user-icon lucide-file-user"><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M15 18a3 3 0 1 0-6 0"/>
                            <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7z"/><circle cx="12" cy="13" r="2"/></svg>
                            <span class="nav-text">Student Additional Information</span>
                        </a></li>
                    </ul>
                </div>
            </nav>

            <div class="sidebar-footer">
                <div class="theme-toggle">
                    <button class="theme-btn" onclick="toggleTheme()">
                        <svg class="theme-icon-light" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="5"></circle>
                            <line x1="12" y1="1" x2="12" y2="3"></line>
                            <line x1="12" y1="21" x2="12" y2="23"></line>
                            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                            <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                            <line x1="1" y1="12" x2="3" y2="12"></line>
                            <line x1="21" y1="12" x2="23" y2="12"></line>
                            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                            <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                        </svg>
                        <svg class="theme-icon-dark" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                        </svg>
                    </button>
                </div>
                <button class="logout-btn" onclick="logout()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16,17 21,12 16,7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    <span class="logout-text">Logout</span>
                </button>
            </div>
        </aside>

        <main class="main-content">
            <div class="top-bar">
                <div class="top-bar-left">
                    <button class="universal-menu-toggle" onclick="toggleSidebar()">
                        <svg class="toggle-icon-menu" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                        <svg class="toggle-icon-close" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                    <button class="search-trigger" onclick="openGlobalSearch()">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="M21 21l-4.35-4.35"></path>
                        </svg>
                        <span class="search-text">Search anything...</span>
                        <kbd>⌘K</kbd>
                    </button>
                </div>
            </div>
            
               <section id="dashboard" class="content-section active">
                <div class="section-header">
                    <h1>Tuition Balance</h1>
                    <button class="download-btn">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7,10 12,15 17,10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        Download Statement
                    </button>
                </div>

                <div class="tuition-content">
                    <div class="tuition-cards">
                        <div class="info-card">
                            <h3>Student Information</h3>
                            <div class="info-grid">
                                <div class="info-row">
                                    <span class="info-label">Program/Stand</span>
                                    <span class="info-value"><?php echo htmlspecialchars($student['program']); ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Year Level</span>
                                    <span class="info-value"><?php echo htmlspecialchars($student['year_level']); ?></span>
                                </div>
         
                                <div class="info-row">
                                    <span class="info-label">Student Status</span>
                                    <span class="info-value"><?php echo ucfirst(htmlspecialchars($student['student_status'])); ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">College</span>
                                    <span class="info-value"><?php echo htmlspecialchars($student['college']); ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Campus</span>
                                    <span class="info-value"><?php echo htmlspecialchars($student['campus']); ?></span>
                                </div>
                            </div>

                            <div class="balance-section">
                                <h4>Outstanding Balance</h4>
                                <div class="balance-amount">₱<?php echo number_format($actual_balance, 2); ?></div>
                                <div class="balance-status">Payment Required</div>
                            </div>
                        </div>

                        <div class="fee-card">
                            <div class="fee-header">
                                <h3>Fee Breakdown</h3>
                                <span class="semester-badge"><?php echo htmlspecialchars($student['semester'] . ' ' . $student['academic_year']); ?></span>
                            </div>

                            <div class="fee-section">
                                <div class="fee-item main-fee">
                                    <span class="fee-name">Tuition Fee</span>
                                    <span class="fee-units"><?php echo $is_regular ? 'SPONSORED' : '6 Subjects x ₱1,500'; ?></span>
                                    <span class="fee-calculation"><?php echo $is_regular ? 'Fully Covered' : '18 Units x 500'; ?></span>
                                    <span class="fee-amount"><?php echo $is_regular ? 'PAID' : '₱ ' . number_format($student['tuition_fee'], 2); ?></span>
                                </div>

                                <div class="fee-category">
                                    <h4>Miscellaneous Fee:</h4>
                                    <div class="fee-item">
                                        <span class="fee-name">Registration</span>
                                        <span class="fee-amount">₱ 400</span>
                                    </div>
                                    <div class="fee-item">
                                        <span class="fee-name">Library</span>
                                        <span class="fee-amount">₱ 850</span>
                                    </div>
                                    <div class="fee-item">
                                        <span class="fee-name">Athletics & Sports Dev. fee</span>
                                        <span class="fee-amount">₱ 500</span>
                                    </div>
                                    <div class="fee-item">
                                        <span class="fee-name">Medical & Dental</span>
                                        <span class="fee-amount">₱ 400</span>
                                    </div>
                                    <div class="fee-item">
                                        <span class="fee-name">Culture Fee</span>
                                        <span class="fee-amount">₱ 400</span>
                                    </div>
                                    <div class="fee-item">
                                        <span class="fee-name">Guidance & Counseling</span>
                                        <span class="fee-amount">₱ 400</span>
                                    </div>
                                    <div class="fee-item">
                                        <span class="fee-name">Energy Fee</span>
                                        <span class="fee-amount">₱ 1000</span>
                                    </div>
                                    <div class="fee-item">
                                        <span class="fee-name">Laboratory Fee</span>
                                        <span class="fee-amount">₱ 600</span>
                                    </div>
                                    <div class="fee-item">
                                        <span class="fee-name">Community & Student Dev. Fee</span>
                                        <span class="fee-amount">₱ 600</span>
                                    </div>
                                    <div class="fee-item">
                                        <span class="fee-name">Insurance</span>
                                        <span class="fee-amount">₱ 25</span>
                                    </div>
                                    <div class="fee-total">
                                        <span class="fee-amount">₱ <?php echo number_format($student['misc_fees'], 2); ?></span>
                                    </div>
                                </div>

                                <div class="fee-category">
                                    <h4>Enrollment Fee</h4>
                                    <div class="fee-item">
                                        <span class="fee-name">Supplementary fee</span>
                                        <span class="fee-amount">₱ 350</span>
                                    </div>
                                    <div class="fee-item-with-desc">
                                        <span class="fee-description">SCHOOL ID WITH LACE</span>
                                    </div>
                                    
                                    <div class="fee-item">
                                        <span class="fee-name">Other Fee</span>
                                        <span class="fee-amount">₱500</span>
                                    </div>
                                    <div class="fee-item-with-desc">
                                        <span class="fee-description">MEDICAL LABORATORY FEE (PANMED)</span>
                                    </div>
                                </div>

                                <div class="fee-category">
                                    <h4>Payments:</h4>
                                    <?php if (!empty($payments)): ?>
                                        <?php foreach ($payments as $payment): ?>
                                            <div class="fee-item">
                                                <span class="fee-name">
                                                    <?php echo strtoupper(htmlspecialchars($payment['payment_method'])); ?> Payment
                                                    <span class="fee-description" style="display: block; font-size: 0.85em; color: var(--text-secondary); margin-top: 4px;">
                                                        <?php echo date('M d, Y g:i A', strtotime($payment['created_at'])); ?>
                                                    </span>
                                                </span>
                                                <span class="fee-amount">₱<?php echo number_format($payment['amount'], 2); ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                        <div class="fee-total" style="margin-top: 12px; padding-top: 12px; border-top: 1px solid var(--border-color);">
                                            <span class="fee-name" style="font-weight: 600;">Total Paid</span>
                                            <span class="fee-amount" style="font-weight: 600;">₱<?php echo number_format($total_paid, 2); ?></span>
                                        </div>
                                    <?php else: ?>
                                        <div class="fee-item">
                                            <span class="fee-name">No payments yet</span>
                                            <span class="fee-amount">₱0.00</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="payment-section">
                        <button class="pay-now-btn" onclick="window.location.href='paymentportal.php'">Pay Now</button>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script src="../js/script.js"></script>
</body>
</html>
