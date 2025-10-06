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

    <div class="dashboard-container">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <span class="logo-text">University</span>
                </div>
                <div class="user-profile">
                    <img class="user-avatar" src="../img/red.jpg" alt="Avatar">
                    <div class="user-info">
                        <span class="user-name">Red Gin Bilog</span>
                        <span class="user-role">Financial Controller</span>
                    </div>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section">
                    <span class="nav-section-title">Main</span>
                     <ul>
                        <li><a href="dashboard.php" class="nav-link" data-section="dashboard" title="Dashboard">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="7" height="7"></rect>
                                <rect x="14" y="3" width="7" height="7"></rect>
                                <rect x="14" y="14" width="7" height="7"></rect>
                                <rect x="3" y="14" width="7" height="7"></rect>
                            </svg>
                            <span class="nav-text">Dashboard</span>
                        </a></li>
                        <li><a href="managetuitionbalance.php" class="nav-link" data-section="tuition-balance" title="Tuition Balance">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-credit-card-icon lucide-credit-card">
                            <rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                            <span class="nav-text">Tuition Balance Management</span>
                        </a></li>
                        <li><a href="Incomereport.php" class="nav-link" data-section="scholarship-application" title="Scholarship">
                           <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-banknote-arrow-up-icon lucide-banknote-arrow-up"><path d="M12 18H4a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5"/><path d="M18 12h.01"/><path d="M19 22v-6"/>
                           <path d="m22 19-3-3-3 3"/><path d="M6 12h.01"/><circle cx="12" cy="12" r="2"/></svg>
                            <span class="nav-text">Scholarship</span>
                        </a></li>
                    </ul>
                </div>

                <div class="nav-section">
                    <span class="nav-section-title">Account Settings</span>
                    <ul>
                         <li><a href="account.php" class="nav-link active" data-section="account-settings" title="Settings">
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
                </div>
            </div>

            <section id="dashboard" class="content-section active">
                <div class="section-header">
                    <h1>Dashboard</h1>
                </div>
            </section>
        </main>
    </div>

    <script src="../js/script.js"></script>
</body>
</html>
