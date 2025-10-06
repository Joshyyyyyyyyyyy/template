<?php
session_start();

// Check if user is logged in and is a scholarship coordinator
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'scholarship_coordinator') {
    header('Location: ../login.php');
    exit();
}

require_once '../config/db_config.php';

$conn = getDBConnection();

$sql = "SELECT 
            sa.application_id,
            sa.application_status,
            sa.application_date,
            sa.academic_year,
            sa.semester,
            s.name as student_name,
            s.email as student_email,
            s.program,
            s.year_level,
            sch.scholarship_name,
            sch.scholarship_code
        FROM scholarship_applications sa
        INNER JOIN students s ON sa.student_id = s.student_id
        INNER JOIN scholarships sch ON sa.scholarship_id = sch.scholarship_id
        WHERE sa.application_status IN ('pending', 'under_review')
        ORDER BY sa.application_date DESC";

$result = $conn->query($sql);
$applications = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $applications[] = $row;
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
    <link rel="stylesheet" href="../css/scholarship.css">
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
                        <span class="user-role">Financial Coordinator</span>
                    </div>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section">
                    <span class="nav-section-title">Main</span>
                    <ul>
                        <li><a href="scholarship_coordinator.php" class="nav-link" data-section="dashboard" title="Dashboard">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="7" height="7"></rect>
                                <rect x="14" y="3" width="7" height="7"></rect>
                                <rect x="14" y="14" width="7" height="7"></rect>
                                <rect x="3" y="14" width="7" height="7"></rect>
                            </svg>
                            <span class="nav-text">Dashboard</span>
                        </a></li>
                        <li><a href="application.php" class="nav-link active" data-section="tuition-balance" title="Tuition Balance">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-user-icon lucide-file-user"><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M15 18a3 3 0 1 0-6 0"/>
                            <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7z"/><circle cx="12" cy="13" r="2"/></svg>
                            <span class="nav-text">Student Applications</span>
                        </a></li>
                        <li><a href="approved.php" class="nav-link" data-section="scholarship-application" title="Scholarship">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big-icon lucide-circle-check-big">
                            <path d="M21.801 10A10 10 0 1 1 17 3.335"/><path d="m9 11 3 3L22 4"/></svg>
                            <span class="nav-text">Approved</span>
                        </a></li>
                        <li><a href="archive.php" class="nav-link" data-section="scholarship-application" title="Scholarship">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-archive-icon lucide-archive"><rect width="20" height="5" x="2" y="3" rx="1"/>
                            <path d="M4 8v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8"/><path d="M10 12h4"/></svg>
                            <span class="nav-text">Archive</span>
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
        <div class="section-header-enhanced">
            <div>
                <h1 class="page-title">Student Applications</h1>
                <p class="page-subtitle">Review and process scholarship applications</p>
            </div>
            <div class="header-stats">
                <div class="stat-card">
                    <div class="stat-value"><?php echo count($applications); ?></div>
                    <div class="stat-label">Pending Review</div>
                </div>
            </div>
        </div>

        <div class="applications-table">
            <?php if (count($applications) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Application ID</th>
                        <th>Student Name</th>
                        <th>Program</th>
                        <th>Scholarship</th>
                        <th>Academic Year</th>
                        <th>Status</th>
                        <th>Date Applied</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($applications as $app): ?>
                    <tr>
                        <td><span class="app-id">#<?php echo $app['application_id']; ?></span></td>
                        <td>
                            <div class="student-info">
                                <div class="student-name"><?php echo htmlspecialchars($app['student_name']); ?></div>
                                <div class="student-email"><?php echo htmlspecialchars($app['student_email']); ?></div>
                            </div>
                        </td>
                        <td>
                            <div class="program-info">
                                <div class="program-name"><?php echo htmlspecialchars($app['program']); ?></div>
                                <div class="year-level"><?php echo htmlspecialchars($app['year_level']); ?></div>
                            </div>
                        </td>
                        <td>
                            <div class="scholarship-info">
                                <div class="scholarship-name"><?php echo htmlspecialchars($app['scholarship_name']); ?></div>
                                <div class="scholarship-code"><?php echo htmlspecialchars($app['scholarship_code']); ?></div>
                            </div>
                        </td>
                        <td>
                            <div class="academic-info">
                                <div class="academic-year"><?php echo htmlspecialchars($app['academic_year']); ?></div>
                                <div class="semester"><?php echo htmlspecialchars($app['semester']); ?></div>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge status-<?php echo $app['application_status']; ?>">
                                <?php echo str_replace('_', ' ', ucwords($app['application_status'])); ?>
                            </span>
                        </td>
                        <td><span class="date-text"><?php echo date('M d, Y', strtotime($app['application_date'])); ?></span></td>
                        <td>
                            <button class="btn-view" onclick="window.location.href='view_application.php?id=<?php echo $app['application_id']; ?>'">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                View Details
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7z"/>
                    </svg>
                </div>
                <h3 class="empty-title">No Applications Found</h3>
                <p class="empty-description">There are no pending or under review applications at the moment.</p>
            </div>
            <?php endif; ?>
        </div>
    </section>
        </main>
    </div>

    <script src="../js/script.js"></script>
</body>
</html>
