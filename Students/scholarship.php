<?php
session_start();
require_once '../config/db_config.php';

// Check if user is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: ../login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Connect to DB
$conn = getDBConnection();

// Fetch student + user info
$stmt = $conn->prepare("
    SELECT s.*, 
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
$conn->close();

// Now you can use $student['name'], $student['profile_picture'], $student['user_type'], etc.
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PMS</title>
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
                        <li><a href="scholarship.php" class="nav-link active" data-section="scholarship-application" title="Scholarship">
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

            <section id="dashboard" class="content-section active">
                <div class="section-header">
                    <h1>Scholarship</h1>
                </div>

                <div class="scholarship-container">
                    <div class="scholarship-intro">
                        <p>Apply for scholarships to help fund your education. Review the requirements below and submit your application with all necessary documents.</p>
                    </div>

                    <div class="scholarships-grid">
                        <div class="scholarship-card">
                            <div class="scholarship-header">
                                <h2>ACCAEX Scholarship</h2>
                                <span class="scholarship-badge">Academic</span>
                            </div>
                            <div class="scholarship-body">
                                <h3>Requirements:</h3>
                                <ul class="requirements-list">
                                    <li>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                        Academic Program Evaluation (Minimum GPA: 2.50)
                                    </li>
                                    <li>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                        Recommendation Letter from Dean
                                    </li>
                                    <li>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                        Recommendation Letter from Professor
                                    </li>
                                    <li>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                        Letter of Intent
                                    </li>
                                    <li>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                        Photocopy of Updated School ID
                                    </li>
                                </ul>
                                <button class="apply-btn" onclick="openModal('accaex')">Apply Now</button>
                            </div>
                        </div>

                        <div class="scholarship-card">
                            <div class="scholarship-header">
                                <h2>PWD Scholarship</h2>
                                <span class="scholarship-badge special">100% Coverage</span>
                            </div>
                            <div class="scholarship-body">
                                <div class="scholarship-highlight">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                    </svg>
                                    <span>Automatic 100% scholarship upon completion of all requirements</span>
                                </div>
                                <h3>Requirements:</h3>
                                <ul class="requirements-list">
                                    <li>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                        PWD Application Form
                                    </li>
                                    <li>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                        Certificate of Registration (COR)
                                    </li>
                                    <li>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                        Recommendation Letter <span class="optional-tag">(Optional)</span>
                                    </li>
                                    <li>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                        Certificate of Residency
                                    </li>
                                    <li>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                        Photocopy of ID
                                    </li>
                                    <li>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                        Letter of Intent
                                    </li>
                                </ul>
                                <button class="apply-btn" onclick="openModal('pwd')">Apply Now</button>
                            </div>
                        </div>

                        <div class="scholarship-card">
                            <div class="scholarship-header">
                                <h2>Sports Scholarship</h2>
                                <span class="scholarship-badge special">100% Coverage</span>
                            </div>
                            <div class="scholarship-body">
                                <div class="scholarship-highlight">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                    </svg>
                                    <span>Automatic 100% scholarship upon completion of all requirements</span>
                                </div>
                                <h3>Requirements:</h3>
                                <ul class="requirements-list">
                                    <li>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                        Coach Recommendation Letter
                                    </li>
                                    <li>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                        Certificate of Registration (COR)
                                    </li>
                                </ul>
                                <button class="apply-btn" onclick="openModal('sports')">Apply Now</button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <div id="scholarshipModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Apply for Scholarship</h2>
                <button class="modal-close" onclick="closeModal()">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <div class="warning-message" id="warningMessage">
                    <div class="warning-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                            <line x1="12" y1="9" x2="12" y2="13"></line>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                    </div>
                    <div class="warning-content">
                        <h3 class="warning-title">Active Application Found</h3>
                        <p class="warning-text" id="warningMessageText">You already have an active application for this scholarship</p>
                    </div>
                </div>
                <div class="success-message" id="successMessage">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    <span id="successMessageText">Application submitted successfully!</span>
                </div>
                <div class="error-message" id="errorMessage">
                </div>
                <form id="scholarshipForm">
                    <div id="uploadFields"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="button" class="btn-primary" id="submitBtn" onclick="submitApplication()">Submit Application</button>
            </div>
        </div>
    </div>

    <script src="../js/script.js"></script>
    <script>
        const scholarshipRequirements = {
            accaex: [
                { name: 'Academic Program Evaluation (Min GPA: 2.50)', id: 'academic_eval', optional: false },
                { name: 'Recommendation Letter from Dean', id: 'dean_letter', optional: false },
                { name: 'Recommendation Letter from Professor', id: 'prof_letter', optional: false },
                { name: 'Letter of Intent', id: 'intent_letter', optional: false },
                { name: 'Photocopy of Updated School ID', id: 'school_id', optional: false }
            ],
            pwd: [
                { name: 'PWD Application Form', id: 'pwd_form', optional: false },
                { name: 'Certificate of Registration (COR)', id: 'cor', optional: false },
                { name: 'Recommendation Letter', id: 'recommendation', optional: true },
                { name: 'Certificate of Residency', id: 'residency', optional: false },
                { name: 'Photocopy of ID', id: 'id_copy', optional: false },
                { name: 'Letter of Intent', id: 'intent_letter', optional: false }
            ],
            sports: [
                { name: 'Coach Recommendation Letter', id: 'coach_letter', optional: false },
                { name: 'Certificate of Registration (COR)', id: 'cor', optional: false }
            ]
        };

        const scholarshipTitles = {
            accaex: 'ACCAEX Scholarship',
            pwd: 'PWD Scholarship',
            sports: 'Sports Scholarship'
        };

        const scholarshipCodes = {
            accaex: 'ACCAEX',
            pwd: 'PWD',
            sports: 'SPORTS'
        };

        let currentScholarship = null;

        function openModal(scholarshipType) {
            currentScholarship = scholarshipType;
            const modal = document.getElementById('scholarshipModal');
            const modalTitle = document.getElementById('modalTitle');
            const uploadFields = document.getElementById('uploadFields');
            const successMessage = document.getElementById('successMessage');
            const errorMessage = document.getElementById('errorMessage');
            const warningMessage = document.getElementById('warningMessage');
            
            successMessage.classList.remove('show');
            errorMessage.classList.remove('show');
            warningMessage.classList.remove('show');
            
            modalTitle.textContent = `Apply for ${scholarshipTitles[scholarshipType]}`;
            
            const requirements = scholarshipRequirements[scholarshipType];
            uploadFields.innerHTML = requirements.map(req => `
                <div class="upload-section">
                    <label class="upload-label ${req.optional ? 'optional' : ''}">${req.name}</label>
                    <div class="file-upload-wrapper" id="wrapper-${req.id}">
                        <input 
                            type="file" 
                            class="file-upload-input" 
                            id="${req.id}"
                            name="${req.id}"
                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                            onchange="handleFileSelect(this, '${req.id}')"
                            ${req.optional ? '' : 'required'}
                        >
                        <div class="file-upload-content">
                            <div class="file-upload-icon">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="17 8 12 3 7 8"></polyline>
                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
                            </div>
                            <div class="file-upload-text">
                                <strong>Click to upload</strong> or drag and drop<br>
                                <small>PDF, DOC, DOCX, JPG, PNG (Max 10MB)</small>
                            </div>
                        </div>
                        <div class="file-name" id="filename-${req.id}">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            <span></span>
                        </div>
                    </div>
                </div>
            `).join('');
            
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            const modal = document.getElementById('scholarshipModal');
            modal.classList.remove('active');
            document.body.style.overflow = '';
            document.getElementById('scholarshipForm').reset();
            
            // Reset all file upload wrappers
            document.querySelectorAll('.file-upload-wrapper').forEach(wrapper => {
                wrapper.classList.remove('has-file');
            });
            document.querySelectorAll('.file-name').forEach(filename => {
                filename.classList.remove('show');
            });
            
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Submit Application';
        }

        function handleFileSelect(input, fieldId) {
            const wrapper = document.getElementById(`wrapper-${fieldId}`);
            const filenameDiv = document.getElementById(`filename-${fieldId}`);
            
            if (input.files && input.files[0]) {
                const file = input.files[0];
                
                if (file.size > 10 * 1024 * 1024) {
                    alert('File size must be less than 10MB');
                    input.value = '';
                    wrapper.classList.remove('has-file');
                    filenameDiv.classList.remove('show');
                    return;
                }
                
                wrapper.classList.add('has-file');
                filenameDiv.classList.add('show');
                filenameDiv.querySelector('span').textContent = file.name;
            } else {
                wrapper.classList.remove('has-file');
                filenameDiv.classList.remove('show');
            }
        }

        async function submitApplication() {
            console.log('[v0] Starting scholarship application submission');
            
            const form = document.getElementById('scholarshipForm');
            const requirements = scholarshipRequirements[currentScholarship];
            const submitBtn = document.getElementById('submitBtn');
            const successMessage = document.getElementById('successMessage');
            const errorMessage = document.getElementById('errorMessage');
            const warningMessage = document.getElementById('warningMessage');
            
            successMessage.classList.remove('show');
            errorMessage.classList.remove('show');
            warningMessage.classList.remove('show');
            
            let allRequiredUploaded = true;
            let missingFiles = [];
            
            requirements.forEach(req => {
                if (!req.optional) {
                    const input = document.getElementById(req.id);
                    if (!input.files || !input.files[0]) {
                        allRequiredUploaded = false;
                        missingFiles.push(req.name);
                    }
                }
            });
            
            if (!allRequiredUploaded) {
                document.getElementById('errorMessageText').textContent = 
                    'Please upload all required documents: ' + missingFiles.join(', ');
                errorMessage.classList.add('show');
                return;
            }
            
            // Disable submit button and show loading state
            submitBtn.disabled = true;
            submitBtn.textContent = 'Submitting...';
            
            try {
                const formData = new FormData();
                formData.append('scholarship_code', scholarshipCodes[currentScholarship]);
                
                console.log('[v0] Scholarship code:', scholarshipCodes[currentScholarship]);
                
                let fileCount = 0;
                requirements.forEach(req => {
                    const input = document.getElementById(req.id);
                    if (input.files && input.files[0]) {
                        formData.append(req.id, input.files[0]);
                        fileCount++;
                        console.log('[v0] Added file:', req.id, input.files[0].name);
                    }
                });
                
                console.log('[v0] Total files to upload:', fileCount);
                
                // Send to API
                const response = await fetch('../api/scholarship_application.php', {
                    method: 'POST',
                    body: formData
                });
                
                console.log('[v0] Response status:', response.status);
                
                const result = await response.json();
                console.log('[v0] Response data:', result);
                
                if (result.success) {
                    let successText = `Application submitted successfully! ${result.total_uploaded} file(s) uploaded.`;
                    if (result.warning) {
                        successText += ' Note: ' + result.warning;
                    }
                    document.getElementById('successMessageText').textContent = successText;
                    successMessage.classList.add('show');
                    
                    // Reset form after 3 seconds and close modal
                    setTimeout(() => {
                        closeModal();
                    }, 3000);
                } else {
                    if (result.message && result.message.includes('already have an active application')) {
                        document.getElementById('warningMessageText').textContent = result.message;
                        document.getElementById('warningMessage').classList.add('show');
                        document.getElementById('scholarshipForm').style.display = 'none';
                        document.getElementById('submitBtn').style.display = 'none';
                    } else {
                        document.getElementById('errorMessageText').textContent = result.message || 'An error occurred while submitting your application.';
                        errorMessage.classList.add('show');
                    }
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Submit Application';
                }
            } catch (error) {
                console.error('[v0] Error submitting application:', error);
                document.getElementById('errorMessageText').textContent = 'Network error. Please check your connection and try again.';
                errorMessage.classList.add('show');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Submit Application';
            }
        }

        // Close modal when clicking outside
        document.getElementById('scholarshipModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</body>
</html>
