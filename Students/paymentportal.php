<?php
session_start();
require_once '../config/db_config.php';

// Get student data (in production, use session-based authentication)
$student_id = 1; // Hardcoded for demo
$conn = getDBConnection();

$stmt = $conn->prepare("SELECT s.*, t.fee_id, t.balance FROM students s LEFT JOIN tuition_fees t ON s.student_id = t.student_id WHERE s.student_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();
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
    <style>
       
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
                        <li><a href="paymentportal.php" class="nav-link active" data-section="payment-portal" title="Payment Portal">
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
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-fingerprint-icon lucide-fingerprint"><path d="M12 10a2 2 0 0 0-2 2c0 1.02-.1 2.51-.26 4"/><path d="M14 13.12c0 2.38 0 6.38-1 8.88"/><path d="M17.29 21.02c.12-.6.43-2.3.5-3.02"/><path d="M2 12a10 10 0 0 1 18-6"/><path d="M2 16h.01"/><path d="M21.8 16c.2-2 .131-5.354 0-6"/>
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

            <!-- Redesigned payment portal with two-column layout showing student info -->
            <section id="dashboard" class="content-section active">
                <div class="section-header">
                    <h1>Payment Portal</h1>
                </div>

                <div class="payment-portal-grid">
                    <!-- Student Information Card -->
                    <div class="student-info-card">
                        <h2>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            Student Information
                        </h2>

                        <div class="info-section">
                            <div class="info-item">
                                <div class="student-name-display"><?php echo htmlspecialchars($student['name']); ?></div>
                                <div class="student-email-display"><?php echo htmlspecialchars($student['email']); ?></div>
                            </div>
                        </div>

                        <div class="info-section">
                            <div class="info-section-title">Academic Details</div>
                            <div class="info-item">
                                <span class="info-label">Program</span>
                                <span class="info-value"><?php echo htmlspecialchars($student['program']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Year Level</span>
                                <span class="info-value"><?php echo htmlspecialchars($student['year_level']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">College</span>
                                <span class="info-value"><?php echo htmlspecialchars($student['college']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Campus</span>
                                <span class="info-value"><?php echo htmlspecialchars($student['campus']); ?></span>
                            </div>
                        </div>

                        <div class="balance-highlight">
                            <div class="label">Outstanding Balance</div>
                            <div class="amount">₱<?php echo number_format($student['balance'], 2); ?></div>
                            <div class="status">Payment Required</div>
                        </div>
                    </div>

                    <!-- Payment Form -->
                    <div class="payment-form-container">
                        <h2>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                                <line x1="1" y1="10" x2="23" y2="10"></line>
                            </svg>
                            Make a Payment
                        </h2>

                        <div id="alertContainer"></div>

                        <form id="paymentForm">
                            <input type="hidden" id="studentId" value="<?php echo $student['student_id']; ?>">
                            <input type="hidden" id="feeId" value="<?php echo $student['fee_id']; ?>">
                            
                            <div class="form-group">
                                <label for="amount">Payment Amount</label>
                                <input 
                                    type="number" 
                                    id="amount" 
                                    name="amount" 
                                    placeholder="Enter amount to pay" 
                                    min="1" 
                                    max="<?php echo $student['balance']; ?>"
                                    step="0.01"
                                    required
                                >
                                <small>Maximum: ₱<?php echo number_format($student['balance'], 2); ?></small>
                            </div>

                            <div class="form-group">
                                <label>Select Payment Method</label>
                                <div class="payment-methods">
                                    <div class="payment-method" data-method="gcash">
                                        <svg width="60" height="40" viewBox="0 0 60 40" fill="none">
                                            <rect width="60" height="40" rx="6" fill="#007DFF"/>
                                            <text x="30" y="25" text-anchor="middle" fill="white" font-size="14" font-weight="bold">GCash</text>
                                        </svg>
                                        <div class="payment-method-name">GCash</div>
                                    </div>
                                    <div class="payment-method" data-method="maya">
                                        <svg width="60" height="40" viewBox="0 0 60 40" fill="none">
                                            <rect width="60" height="40" rx="6" fill="#00D632"/>
                                            <text x="30" y="25" text-anchor="middle" fill="white" font-size="14" font-weight="bold">Maya</text>
                                        </svg>
                                        <div class="payment-method-name">Maya</div>
                                    </div>
                                </div>
                            </div>

                            <div class="payment-summary">
                                <div class="payment-summary-item">
                                    <span class="payment-summary-label">Current Balance:</span>
                                    <span class="payment-summary-value">₱<?php echo number_format($student['balance'], 2); ?></span>
                                </div>
                                <div class="payment-summary-item">
                                    <span class="payment-summary-label">Payment Amount:</span>
                                    <span class="payment-summary-value" id="paymentAmountDisplay">₱0.00</span>
                                </div>
                                <div class="payment-summary-item">
                                    <span class="payment-summary-label">Remaining Balance:</span>
                                    <span class="payment-summary-value" id="remainingBalanceDisplay">₱<?php echo number_format($student['balance'], 2); ?></span>
                                </div>
                            </div>

                            <button type="submit" class="submit-btn" id="submitBtn">
                                Proceed to Payment
                            </button>
                        </form>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script src="../js/script.js"></script>
    <script>
        let selectedMethod = null;
        const currentBalance = <?php echo $student['balance']; ?>;

        const amountInput = document.getElementById('amount');
        const paymentAmountDisplay = document.getElementById('paymentAmountDisplay');
        const remainingBalanceDisplay = document.getElementById('remainingBalanceDisplay');

        amountInput.addEventListener('input', function() {
            const amount = parseFloat(this.value) || 0;
            const remaining = Math.max(0, currentBalance - amount);
            
            paymentAmountDisplay.textContent = '₱' + amount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            remainingBalanceDisplay.textContent = '₱' + remaining.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        });

        // Payment method selection
        document.querySelectorAll('.payment-method').forEach(method => {
            method.addEventListener('click', function() {
                document.querySelectorAll('.payment-method').forEach(m => m.classList.remove('selected'));
                this.classList.add('selected');
                selectedMethod = this.dataset.method;
            });
        });

        // Form submission
        document.getElementById('paymentForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const amount = parseFloat(document.getElementById('amount').value);
            const studentId = document.getElementById('studentId').value;
            const feeId = document.getElementById('feeId').value;
            const submitBtn = document.getElementById('submitBtn');

            // Validation
            if (!selectedMethod) {
                showAlert('Please select a payment method', 'error');
                return;
            }

            if (amount <= 0) {
                showAlert('Please enter a valid amount', 'error');
                return;
            }

            if (amount > currentBalance) {
                showAlert('Payment amount cannot exceed outstanding balance', 'error');
                return;
            }

            // Disable button
            submitBtn.disabled = true;
            submitBtn.textContent = 'Processing...';

            try {
                const response = await fetch('../api/payment_process.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        student_id: studentId,
                        fee_id: feeId,
                        amount: amount,
                        payment_method: selectedMethod
                    })
                });

                const data = await response.json();

                if (data.success) {
                    showAlert('Payment initiated successfully! Redirecting...', 'success');
                    // Redirect to PayMongo checkout or success page
                    setTimeout(() => {
                        if (data.checkout_url) {
                            window.location.href = data.checkout_url;
                        } else {
                            window.location.href = 'history.php';
                        }
                    }, 1500);
                } else {
                    showAlert(data.message || 'Payment failed. Please try again.', 'error');
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Proceed to Payment';
                }
            } catch (error) {
                console.error('[v0] Payment error:', error);
                showAlert('An error occurred. Please try again.', 'error');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Proceed to Payment';
            }
        });

        function showAlert(message, type) {
            const alertContainer = document.getElementById('alertContainer');
            const alertClass = type === 'error' ? 'alert-error' : 'alert-success';
            alertContainer.innerHTML = `<div class="alert ${alertClass}">${message}</div>`;
            
            setTimeout(() => {
                alertContainer.innerHTML = '';
            }, 5000);
        }
    </script>
</body>
</html>
