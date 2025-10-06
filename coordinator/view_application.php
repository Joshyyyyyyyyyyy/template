<?php
session_start();

// Check if user is logged in and is a scholarship coordinator
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'scholarship_coordinator') {
    header('Location: ../login.php');
    exit();
}

require_once '../config/db_config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: application.php');
    exit();
}

$application_id = intval($_GET['id']);
$conn = getDBConnection();

// Fetch application details with student and scholarship information
$sql = "SELECT 
            sa.application_id,
            sa.application_status,
            sa.application_date,
            sa.review_date,
            sa.reviewed_by,
            sa.remarks,
            sa.academic_year,
            sa.semester,
            sa.scholarship_percentage,
            sa.scholarship_amount,
            s.student_id,
            s.name as student_name,
            s.email as student_email,
            s.program,
            s.year_level,
            s.gpa,
            s.college,
            s.campus,
            sch.scholarship_name,
            sch.scholarship_code,
            sch.coverage_percentage
        FROM scholarship_applications sa
        INNER JOIN students s ON sa.student_id = s.student_id
        INNER JOIN scholarships sch ON sa.scholarship_id = sch.scholarship_id
        WHERE sa.application_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $application_id);
$stmt->execute();
$result = $stmt->get_result();
$application = $result->fetch_assoc();

if (!$application) {
    header('Location: application.php');
    exit();
}

// Fetch requirements for this application
$sql_req = "SELECT 
                requirement_id,
                requirement_name,
                file_name,
                file_path,
                file_size,
                file_type,
                uploaded_at
            FROM scholarship_requirements
            WHERE application_id = ?
            ORDER BY uploaded_at DESC";

$stmt_req = $conn->prepare($sql_req);
$stmt_req->bind_param("i", $application_id);
$stmt_req->execute();
$result_req = $stmt_req->get_result();
$requirements = [];
while ($row = $result_req->fetch_assoc()) {
    $requirements[] = $row;
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
                <div class="section-header-detail">
                    <button class="btn-back-header" onclick="window.location.href='application.php'">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 12H5M12 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    <div class="header-title-group">
                        <h1 class="page-title">Application Details</h1>
                        <span class="status-badge-large status-<?php echo $application['application_status']; ?>">
                            <?php echo str_replace('_', ' ', ucwords($application['application_status'])); ?>
                        </span>
                    </div>
                </div>

           <div class="application-details-card">
                <div class="card-header">
                    <div class="app-id-large">#<?php echo $application['application_id']; ?></div>
                    <div class="app-date">Applied on <?php echo date('F d, Y', strtotime($application['application_date'])); ?></div>
                </div>

                <div class="detail-section">
                    <h3 class="section-title">Student Information</h3>
                    <div class="detail-grid-enhanced">
                        <div class="detail-item-enhanced">
                            <div class="detail-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>
                            <div>
                                <div class="detail-label">Full Name</div>
                                <div class="detail-value"><?php echo htmlspecialchars($application['student_name']); ?></div>
                            </div>
                        </div>
                        <div class="detail-item-enhanced">
                            <div class="detail-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                            </div>
                            <div>
                                <div class="detail-label">Email Address</div>
                                <div class="detail-value"><?php echo htmlspecialchars($application['student_email']); ?></div>
                            </div>
                        </div>
                        <div class="detail-item-enhanced">
                            <div class="detail-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                                    <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="detail-label">Program</div>
                                <div class="detail-value"><?php echo htmlspecialchars($application['program']); ?></div>
                            </div>
                        </div>
                        <div class="detail-item-enhanced">
                            <div class="detail-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                            </div>
                            <div>
                                <div class="detail-label">Year Level</div>
                                <div class="detail-value"><?php echo htmlspecialchars($application['year_level']); ?></div>
                            </div>
                        </div>
                        <div class="detail-item-enhanced">
                            <div class="detail-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                </svg>
                            </div>
                            <div>
                                <div class="detail-label">GPA</div>
                                <div class="detail-value"><?php echo $application['gpa'] ? number_format($application['gpa'], 2) : 'N/A'; ?></div>
                            </div>
                        </div>
                        <div class="detail-item-enhanced">
                            <div class="detail-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="detail-label">College</div>
                                <div class="detail-value"><?php echo htmlspecialchars($application['college']); ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="detail-section">
                    <h3 class="section-title">Scholarship Information</h3>
                    <div class="detail-grid-enhanced">
                        <div class="detail-item-enhanced">
                            <div class="detail-icon scholarship-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                                    <path d="M2 17l10 5 10-5M2 12l10 5 10-5"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="detail-label">Scholarship Type</div>
                                <div class="detail-value"><?php echo htmlspecialchars($application['scholarship_name']); ?></div>
                            </div>
                        </div>
                        <div class="detail-item-enhanced">
                            <div class="detail-icon scholarship-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <path d="M12 6v6l4 2"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="detail-label">Coverage</div>
                                <div class="detail-value"><?php echo number_format($application['coverage_percentage'], 0); ?>%</div>
                            </div>
                        </div>
                        <div class="detail-item-enhanced">
                            <div class="detail-icon scholarship-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                            </div>
                            <div>
                                <div class="detail-label">Academic Year</div>
                                <div class="detail-value"><?php echo htmlspecialchars($application['academic_year']); ?></div>
                            </div>
                        </div>
                        <div class="detail-item-enhanced">
                            <div class="detail-icon scholarship-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                            </div>
                            <div>
                                <div class="detail-label">Semester</div>
                                <div class="detail-value"><?php echo htmlspecialchars($application['semester']); ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($application['remarks']): ?>
                <div class="detail-section">
                    <h3 class="section-title">Remarks</h3>
                    <div class="remarks-box">
                        <?php echo nl2br(htmlspecialchars($application['remarks'])); ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="requirements-card">
                <h2 class="card-title">Submitted Requirements</h2>
                <?php if (count($requirements) > 0): ?>
                    <div class="requirements-grid">
                        <?php foreach ($requirements as $req): 
                            $file_path = str_replace('C:\\XAMMP\\htdocs\\PMS/', '../', $req['file_path']);
                            $file_path = str_replace('\\', '/', $file_path);
                        ?>
                        <div class="requirement-card">
                            <div class="requirement-header">
                                <div class="file-icon-large">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                                        <polyline points="13 2 13 9 20 9"></polyline>
                                    </svg>
                                </div>
                                <div class="requirement-details">
                                    <div class="requirement-name">
                                        <?php echo ucwords(str_replace('_', ' ', $req['requirement_name'])); ?>
                                    </div>
                                    <div class="file-meta">
                                        <span class="file-name"><?php echo htmlspecialchars($req['file_name']); ?></span>
                                        <span class="file-size"><?php echo number_format($req['file_size'] / 1024, 2); ?> KB</span>
                                    </div>
                                </div>
                            </div>
                            <a href="<?php echo htmlspecialchars($file_path); ?>" target="_blank" class="btn-view-file">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-requirements">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                            <polyline points="13 2 13 9 20 9"></polyline>
                        </svg>
                        <p>No requirements uploaded yet.</p>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($application['application_status'] === 'pending' || $application['application_status'] === 'under_review'): ?>
            <div class="action-buttons-enhanced">
                <?php if ($application['application_status'] === 'pending'): ?>
                <button class="btn-action btn-review" onclick="updateStatus('under_review')">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </button>
                <?php endif; ?>
                <button class="btn-action btn-approve" onclick="openApproveModal()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </button>
                <button class="btn-action btn-reject" onclick="openRejectModal()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <?php endif; ?>
            </section>
        </main>
    </div>

    <div id="approveModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h2 class="modal-title">Approve Application</h2>
                <button class="modal-close" onclick="closeApproveModal()">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <form id="approveForm">
                <div class="modal-body">
                    <div class="form-group-enhanced">
                        <label class="form-label-enhanced">Scholarship Percentage (%)</label>
                        <input type="number" class="form-input-enhanced" id="scholarshipPercentage" 
                               min="0" max="100" step="0.01" 
                               value="<?php echo $application['coverage_percentage']; ?>" required>
                        <span class="form-hint">Enter the percentage of scholarship coverage to grant</span>
                    </div>
                    <div class="form-group-enhanced">
                        <label class="form-label-enhanced">Remarks (Optional)</label>
                        <textarea class="form-input-enhanced" id="approveRemarks" rows="4"
                                  placeholder="Add any notes or comments about this approval..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modal btn-cancel" onclick="closeApproveModal()">Cancel</button>
                    <button type="submit" class="btn-modal btn-confirm-approve">Confirm Approval</button>
                </div>
            </form>
        </div>
    </div>

    <div id="rejectModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h2 class="modal-title">Reject Application</h2>
                <button class="modal-close" onclick="closeRejectModal()">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <form id="rejectForm">
                <div class="modal-body">
                    <div class="form-group-enhanced">
                        <label class="form-label-enhanced">Reason for Rejection *</label>
                        <textarea class="form-input-enhanced" id="rejectRemarks" rows="4"
                                  placeholder="Please provide a clear reason for rejecting this application..." required></textarea>
                        <span class="form-hint">This reason will be communicated to the student</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modal btn-cancel" onclick="closeRejectModal()">Cancel</button>
                    <button type="submit" class="btn-modal btn-confirm-reject">Confirm Rejection</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openApproveModal() {
            document.getElementById('approveModal').classList.add('active');
        }

        function closeApproveModal() {
            document.getElementById('approveModal').classList.remove('active');
        }

        function openRejectModal() {
            document.getElementById('rejectModal').classList.add('active');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.remove('active');
        }

        function updateStatus(status) {
            if (confirm('Are you sure you want to mark this application as under review?')) {
                const formData = new FormData();
                formData.append('application_id', <?php echo $application_id; ?>);
                formData.append('action', 'update_status');
                formData.append('status', status);

                // <CHANGE> Added better error handling and logging
                console.log('[v0] Updating status to:', status);

                fetch('process_application.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    console.log('[v0] Response status:', response.status);
                    return response.text();
                })
                .then(text => {
                    console.log('[v0] Raw response:', text);
                    try {
                        const data = JSON.parse(text);
                        if (data.success) {
                            alert('Application status updated successfully!');
                            location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    } catch (e) {
                        console.error('[v0] JSON parse error:', e);
                        alert('Server error: ' + text);
                    }
                })
                .catch(error => {
                    console.error('[v0] Fetch error:', error);
                    alert('An error occurred. Please try again.');
                });
            }
        }

        // <CHANGE> Improved approve form handler with better error logging
        document.getElementById('approveForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const percentage = document.getElementById('scholarshipPercentage').value;
            const remarks = document.getElementById('approveRemarks').value;
            
            console.log('[v0] Approving application:', {
                application_id: <?php echo $application_id; ?>,
                percentage: percentage,
                remarks: remarks
            });

            const formData = new FormData();
            formData.append('application_id', <?php echo $application_id; ?>);
            formData.append('action', 'approve');
            formData.append('scholarship_percentage', percentage);
            formData.append('remarks', remarks);

            fetch('process_application.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('[v0] Response status:', response.status);
                console.log('[v0] Response headers:', response.headers);
                return response.text();
            })
            .then(text => {
                console.log('[v0] Raw response:', text);
                try {
                    const data = JSON.parse(text);
                    console.log('[v0] Parsed data:', data);
                    if (data.success) {
                        alert('Application approved successfully!');
                        window.location.href = 'application.php';
                    } else {
                        alert('Error: ' + data.message);
                    }
                } catch (e) {
                    console.error('[v0] JSON parse error:', e);
                    alert('Server returned invalid response: ' + text.substring(0, 200));
                }
            })
            .catch(error => {
                console.error('[v0] Fetch error:', error);
                alert('Network error occurred. Please check console for details.');
            });
        });

        // <CHANGE> Improved reject form handler with better error logging
        document.getElementById('rejectForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const remarks = document.getElementById('rejectRemarks').value;
            
            console.log('[v0] Rejecting application:', {
                application_id: <?php echo $application_id; ?>,
                remarks: remarks
            });

            const formData = new FormData();
            formData.append('application_id', <?php echo $application_id; ?>);
            formData.append('action', 'reject');
            formData.append('remarks', remarks);

            fetch('process_application.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('[v0] Response status:', response.status);
                return response.text();
            })
            .then(text => {
                console.log('[v0] Raw response:', text);
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        alert('Application rejected.');
                        window.location.href = 'application.php';
                    } else {
                        alert('Error: ' + data.message);
                    }
                } catch (e) {
                    console.error('[v0] JSON parse error:', e);
                    alert('Server returned invalid response: ' + text.substring(0, 200));
                }
            })
            .catch(error => {
                console.error('[v0] Fetch error:', error);
                alert('Network error occurred. Please check console for details.');
            });
        });
    </script>
    <script src="../js/script.js"></script>
</body>
</html>
