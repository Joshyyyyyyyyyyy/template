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
                        <span class="user-name">Joshua Garcia</span>
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
                               <path d="M7 20.662V19a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v1.662"/>
                            </svg>
                            <span class="nav-text">Account</span>
                        </a></li>
                         <li><a href="security.php" class="nav-link" data-section="account-settings" title="Settings">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-fingerprint-icon lucide-fingerprint">
                                <path d="M12 10a2 2 0 0 0-2 2c0 1.02-.1 2.51-.26 4"/>
                                <path d="M14 13.12c0 2.38 0 6.38-1 8.88"/>
                                <path d="M17.29 21.02c.12-.6.43-2.3.5-3.02"/>
                                <path d="M2 12a10 10 0 0 1 18-6"/>
                                <path d="M2 16h.01"/>
                                <path d="M21.8 16c.2-2 .131-5.354 0-6"/>
                                <path d="M5 19.5C5.5 18 6 15 6 12a6 6 0 0 1 .34-2"/>
                                <path d="M8.65 22c.21-.66.45-1.32.57-2"/>
                                <path d="M9 6.8a6 6 0 0 1 9 5.2v2"/>
                            </svg>
                            <span class="nav-text">Security</span>
                        </a></li>
                        <li><a href="studentinfo.php" class="nav-link active" data-section="account-settings" title="Settings">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-user-icon lucide-file-user">
                                <path d="M14 2v4a2 2 0 0 0 2 2h4"/>
                                <path d="M15 18a3 3 0 1 0-6 0"/>
                                <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7z"/>
                                <circle cx="12" cy="13" r="2"/>
                            </svg>
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
                <div class="student-info-container">
                    <div class="student-info-header">
                        <h1 class="student-info-title">Student Additional Information</h1>
                        <p class="student-info-subtitle">Complete your profile with enrollment, personal, and educational details</p>
                    </div>

                    <form class="student-info-form" method="POST" action="update_student_info.php">
                        <div class="info-cards-grid">
                            <div class="enrollment-info-card">
                                <div class="enrollment-card-header">
                                    <svg class="enrollment-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                                        <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                                    </svg>
                                    <div>
                                        <h2 class="enrollment-card-title">Enrollment Information</h2>
                                        <p class="enrollment-card-subtitle">Verify Enrollment Information.</p>
                                    </div>
                                </div>
                                
                                <div class="enrollment-details-grid">
                                    <div class="enrollment-detail-item">
                                        <span class="enrollment-detail-label">Category</span>
                                        <div class="enrollment-detail-value">College</div>
                                    </div>
                                    <div class="enrollment-detail-item">
                                        <span class="enrollment-detail-label">Branch</span>
                                        <div class="enrollment-detail-value">Main Branch</div>
                                    </div>
                                    <div class="enrollment-detail-item">
                                        <span class="enrollment-detail-label">Program / Strand</span>
                                        <div class="enrollment-detail-value">Bachelor of Science in Information Technology</div>
                                    </div>
                                    <div class="enrollment-detail-item">
                                        <span class="enrollment-detail-label">Year Level</span>
                                        <div class="enrollment-detail-value">3rd Year</div>
                                    </div>
                                </div>
                            </div>

                            <div class="personal-info-card">
                                <div class="personal-card-header">
                                    <svg class="personal-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                    <div>
                                        <h2 class="personal-card-title">Personal Information</h2>
                                        <p class="personal-card-subtitle">All fields with <span>*</span> are required.</p>
                                    </div>
                                </div>

                                <div class="personal-form-grid">
                                    <div class="personal-form-group">
                                        <label class="personal-form-label">School ID</label>
                                        <input type="text" class="personal-form-input"  value="230116868" readonly>
                                    </div>
                                    <div class="personal-form-group">
                                        <label class="personal-form-label">Last Name *</label>
                                        <input type="text" class="personal-form-input" value="Suruiz" name="last_name"  readonly>
                                    </div>
                                    <div class="personal-form-group">
                                        <label class="personal-form-label">First Name *</label>
                                        <input type="text" class="personal-form-input" value="Joshua Andrie" name="first_name"  readonly>
                                    </div>
                                    <div class="personal-form-group">
                                        <label class="personal-form-label">Middle Name</label>
                                        <input type="text" class="personal-form-input" value="Rivero" name="middle_name"  readonly>
                                    </div>
                                    <div class="personal-form-group">
                                        <label class="personal-form-label">Suffix Name</label>
                                        <input type="text" class="personal-form-input" placeholder="Jr., Sr., III, etc." name="suffix_name"  readonly>
                                    </div>
                                    <div class="personal-form-group">
                                        <label class="personal-form-label">Facebook Name</label>
                                        <input type="text" class="personal-form-input" value="Joshua Rivero Suruiz" name="facebook_name"  readonly>
                                    </div>
                                    <div class="personal-form-group">
                                        <label class="personal-form-label">Sex *</label>
                                        <select class="personal-form-select" name="sex"  readonly>
                                            <option value="Male" selected>Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                    </div>
                                    <div class="personal-form-group">
                                        <label class="personal-form-label">Civil Status *</label>
                                        <select class="personal-form-select" name="civil_status"  readonly>
                                            <option value="Single" selected>Single</option>
                                            <option value="Married">Married</option>
                                            <option value="Divorced">Divorced</option>
                                            <option value="Widowed">Widowed</option>
                                        </select>
                                    </div>
                                    <div class="personal-form-group">
                                        <label class="personal-form-label">Religion</label>
                                        <input type="text" class="personal-form-input" value="Catholic" name="religion"  readonly>
                                    </div>
                                    <div class="personal-form-group">
                                        <label class="personal-form-label">Birthday *</label>
                                        <input type="date" class="personal-form-input" value="2004-06-04" name="birthday"  readonly>
                                    </div>
                                    <div class="personal-form-group">
                                        <label class="personal-form-label">Personal Email *</label>
                                        <input type="email" class="personal-form-input" value="riverojosh19@gmail.com" name="personal_email"  readonly>
                                    </div>
                                    <div class="personal-form-group">
                                        <label class="personal-form-label">Contact Number *</label>
                                        <input type="tel" class="personal-form-input" value="63969114283" name="contact_number"  readonly>
                                    </div>
                                    <div class="personal-form-group">
                                        <label class="personal-form-label">Address # *</label>
                                        <input type="text" class="personal-form-input" value="Black 2 Lot 5 April Extension" name="address"  readonly>
                                    </div>
                                    <div class="personal-form-group">
                                        <label class="personal-form-label">Barangay *</label>
                                        <input type="text" class="personal-form-input" value="Bahay Toro" name="barangay"  readonly>
                                    </div>
                                    <div class="personal-form-group">
                                        <label class="personal-form-label">Municipality / City *</label>
                                        <input type="text" class="personal-form-input" value="Quezon City" name="municipality"  readonly>
                                    </div>
                                    <div class="personal-form-group">
                                        <label class="personal-form-label">Province *</label>
                                        <input type="text" class="personal-form-input" value="NCR - National Capital Region" name="province"  readonly>
                                    </div>
                                    <div class="personal-form-group">
                                        <label class="personal-form-label">Disability</label>
                                        <select class="personal-form-select" name="disability"  readonly>
                                            <option value="">Select Disability</option>
                                            <option value="None">None</option>
                                            <option value="Visual">Visual Impairment</option>
                                            <option value="Hearing">Hearing Impairment</option>
                                            <option value="Physical">Physical Disability</option>
                                            <option value="Learning">Learning Disability</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                    <div class="personal-form-group">
                                        <label class="personal-form-label">Indigenous</label>
                                        <select class="personal-form-select" name="indigenous"  readonly>
                                            <option value="">Select Indigenous Group</option>
                                            <option value="None">None</option>
                                            <option value="Aeta">Aeta</option>
                                            <option value="Igorot">Igorot</option>
                                            <option value="Mangyan">Mangyan</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="parent-info-section">
                                    <h3 class="parent-section-title">Parent/Guardian Information</h3>
                                    <div class="parent-info-grid">
                                        <div class="parent-info-block">
                                            <h4 class="parent-block-title">Primary Contact</h4>
                                            <div class="parent-form-grid">
                                                <div class="personal-form-group">
                                                    <label class="personal-form-label">Last Name</label>
                                                    <input type="text" class="personal-form-input" value="Suruiz" name="parent_last_name"  readonly>
                                                </div>
                                                <div class="personal-form-group">
                                                    <label class="personal-form-label">First Name</label>
                                                    <input type="text" class="personal-form-input" value="Myra" name="parent_first_name"  readonly>
                                                </div>
                                                <div class="personal-form-group">
                                                    <label class="personal-form-label">Middle Name</label>
                                                    <input type="text" class="personal-form-input" value="Rivero" name="parent_middle_name"  readonly>
                                                </div>
                                                <div class="personal-form-group">
                                                    <label class="personal-form-label">Contact Number</label>
                                                    <input type="tel" class="personal-form-input" value="09516516348" name="parent_contact"  readonly>
                                                </div>
                                                <div class="personal-form-group">
                                                    <label class="personal-form-label">Occupation</label>
                                                    <input type="text" class="personal-form-input" value="House Wife" name="parent_occupation" readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="parent-info-block">
                                            <h4 class="parent-block-title">Father's Information</h4>
                                            <div class="parent-form-grid">
                                                <div class="personal-form-group">
                                                    <label class="personal-form-label">Last Name</label>
                                                    <input type="text" class="personal-form-input" value="Suruiz" name="father_last_name">
                                                </div>
                                                <div class="personal-form-group">
                                                    <label class="personal-form-label">First Name</label>
                                                    <input type="text" class="personal-form-input" value="Jonathan" name="father_first_name">
                                                </div>
                                                <div class="personal-form-group">
                                                    <label class="personal-form-label">Middle Name</label>
                                                    <input type="text" class="personal-form-input" value="Tugay" name="father_middle_name">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="parent-info-block">
                                            <h4 class="parent-block-title">Mother's Information (Maiden Name)</h4>
                                            <div class="parent-form-grid">
                                                <div class="personal-form-group">
                                                    <label class="personal-form-label">Last Name</label>
                                                    <input type="text" class="personal-form-input" value="Suruiz" name="mother_last_name">
                                                </div>
                                                <div class="personal-form-group">
                                                    <label class="personal-form-label">First Name</label>
                                                    <input type="text" class="personal-form-input" value="Myra" name="mother_first_name">
                                                </div>
                                                <div class="personal-form-group">
                                                    <label class="personal-form-label">Middle Name</label>
                                                    <input type="text" class="personal-form-input" value="Rivero" name="mother_middle_name">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="educational-info-card">
                                <div class="educational-card-header">
                                    <svg class="educational-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                                    </svg>
                                    <div>
                                        <h2 class="educational-card-title">Educational Background</h2>
                                        <p class="educational-required-note">All fields with * are required.</p>
                                    </div>
                                </div>

                                <div class="educational-sections">
                                    <div class="educational-section">
                                        <h3 class="educational-section-title">Primary Education</h3>
                                        <div class="educational-form-grid">
                                            <div class="educational-form-group">
                                                <label class="educational-form-label">School Name *</label>
                                                <input type="text" class="educational-form-input" value="Sto Cristo Elementary School" name="primary_school" required>
                                            </div>
                                            <div class="educational-form-group">
                                                <label class="educational-form-label">Year Graduated *</label>
                                                <input type="number" class="educational-form-input" value="2016" name="primary_year" min="1990" max="2030" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="educational-section">
                                        <h3 class="educational-section-title">Secondary Education</h3>
                                        <div class="educational-form-grid">
                                            <div class="educational-form-group">
                                                <label class="educational-form-label">School Name *</label>
                                                <input type="text" class="educational-form-input" value="San Francisco High School" name="secondary_school" required>
                                            </div>
                                            <div class="educational-form-group">
                                                <label class="educational-form-label">Year Graduated *</label>
                                                <input type="number" class="educational-form-input" value="2020" name="secondary_year" min="1990" max="2030" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="educational-section">
                                        <h3 class="educational-section-title">Last School Attended</h3>
                                        <div class="educational-form-grid">
                                            <div class="educational-form-group">
                                                <label class="educational-form-label">School Name *</label>
                                                <input type="text" class="educational-form-input" value="San Francisco High School" name="last_school" required>
                                            </div>
                                            <div class="educational-form-group">
                                                <label class="educational-form-label">Last Year Attended *</label>
                                                <input type="number" class="educational-form-input" value="2023" name="last_year" min="1990" max="2030" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </section>
        </main>
    </div>

    <script src="../js/script.js"></script>
</body>
</html>
