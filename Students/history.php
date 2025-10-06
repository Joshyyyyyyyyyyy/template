<?php
require_once '../config/session.php';
require_once '../config/db_config.php';

// ✅ Check if user is logged in and is a student
if (!isLoggedIn() || $_SESSION['user_type'] !== 'student') {
    header('Location: ../login.php');
    exit();
}

// ✅ Get student_id from session
$student_id = $_SESSION['student_id'];

// ✅ Validate that student_id exists
if (!$student_id) {
    die('Error: Student ID not found in session. Please log in again.');
}

$conn = getDBConnection();

// ✅ FETCH STUDENT + PROFILE INFO
$stmt = $conn->prepare("
    SELECT 
        s.*, 
        u.user_type, 
        u.profile_picture, 
        u.email
    FROM students s
    LEFT JOIN users u ON s.user_id = u.user_id
    WHERE s.student_id = ?
");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

// ✅ FETCH PAYMENT DETAILS
$stmt = $conn->prepare("
    SELECT 
        p.payment_id,
        p.amount,
        p.payment_method,
        p.payment_status,
        p.paymongo_payment_id,
        p.created_at,
        t.semester,
        t.academic_year
    FROM payments p
    LEFT JOIN tuition_fees t ON p.fee_id = t.fee_id
    WHERE p.student_id = ?
    ORDER BY p.created_at DESC
");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$payments = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ✅ Calculate payment statistics
$total_paid = 0;
$completed_payments = 0;
$pending_payments = 0;

foreach ($payments as $payment) {
    if ($payment['payment_status'] === 'completed') {
        $total_paid += (float) $payment['amount'];
        $completed_payments++;
    } elseif ($payment['payment_status'] === 'pending') {
        $pending_payments++;
    }
}

// ✅ FETCH SCHOLARSHIP APPLICATION DETAILS
$stmt = $conn->prepare("
    SELECT 
        sa.application_id,
        sa.scholarship_amount,
        sa.scholarship_percentage,
        sa.application_status,
        sa.application_date,
        sa.review_date,
        sa.reviewed_by,
        sa.remarks,
        sa.academic_year,
        sa.semester,
        s.scholarship_name,
        s.scholarship_code
    FROM scholarship_applications sa
    LEFT JOIN scholarships s ON sa.scholarship_id = s.scholarship_id
    WHERE sa.student_id = ?
    ORDER BY sa.application_date DESC
");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$scholarship_applications = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ✅ Calculate scholarship statistics
$total_scholarship_amount = 0;
$approved_scholarships = 0;
$pending_scholarships = 0;
$rejected_scholarships = 0;

foreach ($scholarship_applications as $application) {
    if ($application['application_status'] === 'approved') {
        $total_scholarship_amount += (float) $application['scholarship_amount'];
        $approved_scholarships++;
    } elseif ($application['application_status'] === 'pending' || $application['application_status'] === 'under_review') {
        $pending_scholarships++;
    } elseif ($application['application_status'] === 'rejected') {
        $rejected_scholarships++;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PMS</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Added custom styles for history page with tabs and transaction table */
        .history-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 24px;
            border-bottom: 2px solid var(--border-color, #e5e7eb);
            padding-bottom: 0;
        }

        .tab-button {
            padding: 12px 24px;
            background: none;
            border: none;
            border-bottom: 3px solid transparent;
            color: var(--text-secondary, #6b7280);
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            bottom: -2px;
        }

        .tab-button:hover {
            color: var(--text-primary, #111827);
            background: var(--hover-bg, rgba(0, 0, 0, 0.02));
        }

        .tab-button.active {
            color: var(--primary-color, #007DFF);
            border-bottom-color: var(--primary-color, #007DFF);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: var(--card-bg, #ffffff);
            border: 1px solid var(--border-color, #e5e7eb);
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .stat-card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-icon.blue {
            background: rgba(0, 125, 255, 0.1);
            color: #007DFF;
        }

        .stat-icon.green {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }

        .stat-icon.orange {
            background: rgba(251, 146, 60, 0.1);
            color: #fb923c;
        }

        /* Added red stat icon for rejected scholarships */
        .stat-icon.red {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .stat-label {
            font-size: 14px;
            color: var(--text-secondary, #6b7280);
            font-weight: 500;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: var(--text-primary, #111827);
            line-height: 1;
        }

        .transactions-table-container {
            background: var(--card-bg, #ffffff);
            border: 1px solid var(--border-color, #e5e7eb);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .table-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-color, #e5e7eb);
        }

        .table-header h3 {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-primary, #111827);
            margin: 0;
        }

        .transactions-table {
            width: 100%;
            border-collapse: collapse;
        }

        .transactions-table thead {
            background: var(--table-header-bg, #f9fafb);
        }

        .transactions-table th {
            padding: 12px 24px;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary, #6b7280);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .transactions-table td {
            padding: 16px 24px;
            border-top: 1px solid var(--border-color, #e5e7eb);
            font-size: 14px;
            color: var(--text-primary, #111827);
        }

        .transactions-table tbody tr:hover {
            background: var(--hover-bg, rgba(0, 0, 0, 0.02));
        }

        .payment-method-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
        }

        .payment-method-badge.gcash {
            background: rgba(0, 125, 255, 0.1);
            color: #007DFF;
        }

        .payment-method-badge.maya {
            background: rgba(0, 214, 50, 0.1);
            color: #00d632;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
        }

        .status-badge.completed {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }

        .status-badge.pending {
            background: rgba(251, 146, 60, 0.1);
            color: #fb923c;
        }

        /* Added status badge styles for scholarship statuses */
        .status-badge.approved {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }

        .status-badge.under_review {
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
        }

        .status-badge.rejected {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .status-badge.failed {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        /* Added scholarship badge styles */
        .scholarship-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            background: rgba(139, 92, 246, 0.1);
            color: #8b5cf6;
        }

        .empty-state {
            text-align: center;
            padding: 80px 24px;
        }

        .empty-state-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 24px;
            background: var(--empty-state-bg, #f9fafb);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary, #6b7280);
        }

        .empty-state h3 {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-primary, #111827);
            margin: 0 0 8px 0;
        }

        .empty-state p {
            font-size: 14px;
            color: var(--text-secondary, #6b7280);
            margin: 0;
        }

        .transaction-id {
            font-family: 'Courier New', monospace;
            font-size: 13px;
            color: var(--text-secondary, #6b7280);
        }

        .amount-cell {
            font-weight: 600;
            font-size: 15px;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .transactions-table {
                font-size: 13px;
            }

            .transactions-table th,
            .transactions-table td {
                padding: 12px 16px;
            }

            .tab-button {
                padding: 10px 16px;
                font-size: 14px;
            }
        }

        /* Dark theme support */
        .theme-dark .stat-card,
        .theme-dark .transactions-table-container {
            background: var(--card-bg-dark, #1f2937);
            border-color: var(--border-color-dark, #374151);
        }

        .theme-dark .transactions-table thead {
            background: var(--table-header-bg-dark, #111827);
        }

        .theme-dark .transactions-table td {
            border-color: var(--border-color-dark, #374151);
        }

        .theme-dark .transactions-table tbody tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .theme-dark .stat-value,
        .theme-dark .table-header h3,
        .theme-dark .transactions-table td {
            color: var(--text-primary-dark, #f9fafb);
        }

        .theme-dark .stat-label,
        .theme-dark .transactions-table th,
        .theme-dark .transaction-id {
            color: var(--text-secondary-dark, #9ca3af);
        }

        .theme-dark .empty-state-icon {
            background: var(--empty-state-bg-dark, #111827);
        }

        .theme-dark .tab-button:hover {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-primary-dark, #f9fafb);
        }
    </style>
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
                    <span class="logo-text">University</span>
                </div>
                <div class="user-profile"> 
                    <img class="user-avatar" 
                    src="<?php 
                        if (!empty($student['profile_picture'])) {
                            echo '../' . htmlspecialchars($student['profile_picture']); 
                        } else {
                            echo '../img/default-avatar.png'; 
                        }
                    ?>" 
                    alt="Avatar">

                    <div class="user-info">
                        <span class="user-name"><?php echo htmlspecialchars($student['name']); ?></span>
                        <span class="user-role"><?php echo ucfirst($student['user_type']); ?></span>
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
                        <li><a href="tutionbalance.php" class="nav-link" data-section="tuition-balance" title="Tuition Balance">
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
                        <li><a href="history.php" class="nav-link active" data-section="history" title="History">
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
                            <path d="M15 2H6a 2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7z"/><circle cx="12" cy="13" r="2"/></svg>
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
                    <h1>History</h1>
                </div>

                <div class="history-tabs">
                    <button class="tab-button active" onclick="switchTab('transactions')">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 6px;">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                            <line x1="1" y1="10" x2="23" y2="10"></line>
                        </svg>
                        Transaction History
                    </button>
                    <button class="tab-button" onclick="switchTab('scholarship')">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 6px;">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                            <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                        </svg>
                        Scholarship History
                    </button>
                </div>

                <div id="transactions-tab" class="tab-content active">
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-icon blue">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="12" y1="1" x2="12" y2="23"></line>
                                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                    </svg>
                                </div>
                                <span class="stat-label">Total Paid</span>
                            </div>
                            <div class="stat-value">₱<?php echo number_format($total_paid, 2); ?></div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-icon green">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="20,6 9,17 4,12"></polyline>
                                    </svg>
                                </div>
                                <span class="stat-label">Completed Payments</span>
                            </div>
                            <div class="stat-value"><?php echo $completed_payments; ?></div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-icon orange">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12,6 12,12 16,14"></polyline>
                                    </svg>
                                </div>
                                <span class="stat-label">Pending Payments</span>
                            </div>
                            <div class="stat-value"><?php echo $pending_payments; ?></div>
                        </div>
                    </div>

                    <div class="transactions-table-container">
                        <div class="table-header">
                            <h3>All Transactions</h3>
                        </div>
                        
                        <?php if (count($payments) > 0): ?>
                        <table class="transactions-table">
                            <thead>
                                <tr>
                                    <th>Transaction ID</th>
                                    <th>Date & Time</th>
                                    <th>Amount</th>
                                    <th>Payment Method</th>
                                    <th>Academic Period</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($payments as $payment): ?>
                                <tr>
                                    <td>
                                        <span class="transaction-id">
                                            <?php echo htmlspecialchars(substr($payment['paymongo_payment_id'] ?? 'TXN-' . $payment['payment_id'], 0, 20)); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y g:i A', strtotime($payment['created_at'])); ?></td>
                                    <td class="amount-cell">₱<?php echo number_format($payment['amount'], 2); ?></td>
                                    <td>
                                        <span class="payment-method-badge <?php echo strtolower($payment['payment_method']); ?>">
                                            <?php echo strtoupper(htmlspecialchars($payment['payment_method'])); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($payment['semester'] . ' ' . $payment['academic_year']); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo strtolower($payment['payment_status']); ?>">
                                            <?php echo ucfirst(htmlspecialchars($payment['payment_status'])); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                                    <line x1="1" y1="10" x2="23" y2="10"></line>
                                </svg>
                            </div>
                            <h3>No Transactions Yet</h3>
                            <p>Your payment history will appear here once you make your first payment.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Replaced empty scholarship tab with full scholarship history implementation -->
                <div id="scholarship-tab" class="tab-content">
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-icon blue">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="12" y1="1" x2="12" y2="23"></line>
                                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                    </svg>
                                </div>
                                <span class="stat-label">Total Scholarship Amount</span>
                            </div>
                            <div class="stat-value">₱<?php echo number_format($total_scholarship_amount, 2); ?></div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-icon green">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="20,6 9,17 4,12"></polyline>
                                    </svg>
                                </div>
                                <span class="stat-label">Approved Applications</span>
                            </div>
                            <div class="stat-value"><?php echo $approved_scholarships; ?></div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-icon orange">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12,6 12,12 16,14"></polyline>
                                    </svg>
                                </div>
                                <span class="stat-label">Pending Applications</span>
                            </div>
                            <div class="stat-value"><?php echo $pending_scholarships; ?></div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div class="stat-icon red">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                    </svg>
                                </div>
                                <span class="stat-label">Rejected Applications</span>
                            </div>
                            <div class="stat-value"><?php echo $rejected_scholarships; ?></div>
                        </div>
                    </div>

                    <div class="transactions-table-container">
                        <div class="table-header">
                            <h3>All Scholarship Applications</h3>
                        </div>
                        
                        <?php if (count($scholarship_applications) > 0): ?>
                        <table class="transactions-table">
                            <thead>
                                <tr>
                                    <th>Application ID</th>
                                    <th>Application Date</th>
                                    <th>Scholarship Type</th>
                                    <th>Academic Period</th>
                                    <th>Coverage</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Review Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($scholarship_applications as $application): ?>
                                <tr>
                                    <td>
                                        <span class="transaction-id">
                                            APP-<?php echo str_pad($application['application_id'], 6, '0', STR_PAD_LEFT); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y g:i A', strtotime($application['application_date'])); ?></td>
                                    <td>
                                        <span class="scholarship-badge">
                                            <?php echo htmlspecialchars($application['scholarship_name']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($application['semester'] . ' ' . $application['academic_year']); ?></td>
                                    <td><?php echo number_format($application['scholarship_percentage'], 0); ?>%</td>
                                    <td class="amount-cell">₱<?php echo number_format($application['scholarship_amount'], 2); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo strtolower($application['application_status']); ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', htmlspecialchars($application['application_status']))); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                        if ($application['review_date']) {
                                            echo date('M d, Y', strtotime($application['review_date']));
                                        } else {
                                            echo '<span style="color: var(--text-secondary, #6b7280);">Not reviewed</span>';
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                                    <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                                </svg>
                            </div>
                            <h3>No Scholarship History</h3>
                            <p>Your scholarship applications and awards will appear here.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script src="../js/script.js"></script>
    <script>
        function switchTab(tabName) {
            // Remove active class from all tabs and buttons
            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            
            // Add active class to selected tab
            if (tabName === 'transactions') {
                document.querySelector('.tab-button:first-child').classList.add('active');
                document.getElementById('transactions-tab').classList.add('active');
            } else if (tabName === 'scholarship') {
                document.querySelector('.tab-button:last-child').classList.add('active');
                document.getElementById('scholarship-tab').classList.add('active');
            }
        }
    </script>
</body>
</html>
