<?php
session_start();
require_once __DIR__ . '/../config/db_config.php';

// Connect to DB
$conn = getDBConnection();

// Initialize filter variables
$semester_filter = isset($_GET['semester']) ? $_GET['semester'] : '';
$course_filter = isset($_GET['course']) ? $_GET['course'] : '';
$scholarship_filter = isset($_GET['scholarship']) ? $_GET['scholarship'] : '';
$date_filter = isset($_GET['date']) ? $_GET['date'] : '';

// Build the SQL query with filters for archived applications
$sql = "SELECT 
    sa.application_id,
    sa.application_date,
    sa.semester,
    sa.academic_year,
    sa.scholarship_amount,
    sa.scholarship_percentage,
    sa.review_date,
    sa.reviewed_by,
    s.name AS student_name,
    s.email AS student_email,
    s.program,
    s.year_level,
    sch.scholarship_name,
    sch.scholarship_code,
    sch.coverage_percentage
FROM scholarship_applications sa
INNER JOIN students s ON sa.student_id = s.student_id
INNER JOIN scholarships sch ON sa.scholarship_id = sch.scholarship_id
WHERE sa.application_status = 'archived'";

// Add filters to query
if (!empty($semester_filter)) {
    $sql .= " AND sa.semester = '" . $conn->real_escape_string($semester_filter) . "'";
}
if (!empty($course_filter)) {
    $sql .= " AND s.program LIKE '%" . $conn->real_escape_string($course_filter) . "%'";
}
if (!empty($scholarship_filter)) {
    $sql .= " AND sa.scholarship_id = " . intval($scholarship_filter);
}
if (!empty($date_filter)) {
    $sql .= " AND DATE(sa.application_date) = '" . $conn->real_escape_string($date_filter) . "'";
}

$sql .= " ORDER BY sa.review_date DESC";

// Execute main query
$result = $conn->query($sql);

// Get unique semesters for filter dropdown
$semesters_query = "SELECT DISTINCT semester FROM scholarship_applications WHERE application_status = 'archived' ORDER BY semester";
$semesters_result = $conn->query($semesters_query);

// Get unique programs for filter dropdown
$programs_query = "SELECT DISTINCT program FROM students ORDER BY program";
$programs_result = $conn->query($programs_query);

// Get scholarships for filter dropdown
$scholarships_query = "SELECT scholarship_id, scholarship_name FROM scholarships WHERE is_active = 1 ORDER BY scholarship_name";
$scholarships_result = $conn->query($scholarships_query);
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
        .filters-container {
            background: var(--card-bg);
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
        }
        
        .filter-group label {
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
        }
        
        .filter-group select,
        .filter-group input {
            padding: 0.625rem;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            background: var(--input-bg);
            color: var(--text-primary);
            font-size: 0.875rem;
        }
        
        .filter-actions {
            display: flex;
            gap: 0.75rem;
            justify-content: flex-end;
        }
        
        .btn-search, .btn-reset {
            padding: 0.625rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .btn-search {
            background: #3b82f6;
            color: white;
        }
        
        .btn-search:hover {
            background: #2563eb;
        }
        
        .btn-reset {
            background: var(--card-bg);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }
        
        .btn-reset:hover {
            background: var(--hover-bg);
        }
        
        .table-container {
            background: var(--card-bg);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table thead {
            background: var(--table-header-bg);
        }
        
        .data-table th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--text-primary);
            border-bottom: 1px solid var(--border-color);
        }
        
        .data-table td {
            padding: 1rem;
            font-size: 0.875rem;
            color: var(--text-secondary);
            border-bottom: 1px solid var(--border-color);
        }
        
        .data-table tbody tr:hover {
            background: var(--hover-bg);
        }
        
        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .badge-archived {
            background: #f3f4f6;
            color: #6b7280;
        }
        
        .no-data {
            text-align: center;
            padding: 3rem;
            color: var(--text-secondary);
        }
    </style>
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
                        <li><a href="application.php" class="nav-link" data-section="tuition-balance" title="Tuition Balance">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-user-icon lucide-file-user"><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M15 18a3 3 0 1 0-6 0"/>
                            <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7z"/><circle cx="12" cy="13" r="2"/></svg>
                            <span class="nav-text">Student Applications</span>
                        </a></li>
                        <li><a href="approved.php" class="nav-link" data-section="scholarship-application" title="Scholarship">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big-icon lucide-circle-check-big">
                            <path d="M21.801 10A10 10 0 1 1 17 3.335"/><path d="m9 11 3 3L22 4"/></svg>
                            <span class="nav-text">Approved</span>
                        </a></li>
                        <li><a href="archive.php" class="nav-link active" data-section="scholarship-application" title="Scholarship">
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

            <section id="archived-scholarships" class="content-section active">
                <div class="section-header">
                    <h1>Archived Scholarships</h1>
                </div>

                 Search Filters 
                <div class="filters-container">
                    <form method="GET" action="archive.php">
                        <div class="filters-grid">
                            <div class="filter-group">
                                <label for="semester">Semester</label>
                                <select name="semester" id="semester">
                                    <option value="">All Semesters</option>
                                    <?php while($sem = $semesters_result->fetch_assoc()): ?>
                                        <option value="<?php echo htmlspecialchars($sem['semester']); ?>" 
                                                <?php echo $semester_filter == $sem['semester'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($sem['semester']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="filter-group">
                                <label for="course">Course/Program</label>
                                <select name="course" id="course">
                                    <option value="">All Programs</option>
                                    <?php while($prog = $programs_result->fetch_assoc()): ?>
                                        <option value="<?php echo htmlspecialchars($prog['program']); ?>" 
                                                <?php echo $course_filter == $prog['program'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($prog['program']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="filter-group">
                                <label for="scholarship">Type of Scholarship</label>
                                <select name="scholarship" id="scholarship">
                                    <option value="">All Scholarships</option>
                                    <?php while($sch = $scholarships_result->fetch_assoc()): ?>
                                        <option value="<?php echo $sch['scholarship_id']; ?>" 
                                                <?php echo $scholarship_filter == $sch['scholarship_id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($sch['scholarship_name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="filter-group">
                                <label for="date">Application Date</label>
                                <input type="date" name="date" id="date" value="<?php echo htmlspecialchars($date_filter); ?>">
                            </div>
                        </div>

                        <div class="filter-actions">
                            <button type="button" class="btn-reset" onclick="window.location.href='archive.php'">Reset</button>
                            <button type="submit" class="btn-search">Search</button>
                        </div>
                    </form>
                </div>

                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Application ID</th>
                                <th>Student Name</th>
                                <th>Program</th>
                                <th>Year Level</th>
                                <th>Scholarship</th>
                                <th>Semester</th>
                                <th>Academic Year</th>
                                <th>Coverage</th>
                                <th>Amount</th>
                                <th>Date Applied</th>
                                <th>Reviewed By</th>
                                <th>Date Reviewed</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($result && $result->num_rows > 0): ?>
                                <?php while($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['application_id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['program']); ?></td>
                                        <td><?php echo htmlspecialchars($row['year_level']); ?></td>
                                        <td>
                                            <span class="badge badge-archived">
                                                <?php echo htmlspecialchars($row['scholarship_name']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['semester']); ?></td>
                                        <td><?php echo htmlspecialchars($row['academic_year']); ?></td>
                                        <td><?php echo number_format($row['scholarship_percentage'], 0); ?>%</td>
                                        <td>₱<?php echo number_format($row['scholarship_amount'], 2); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($row['application_date'])); ?></td>
                                        <td>
                                            <?php if(!empty($row['reviewed_by'])): ?>
                                                <?php echo htmlspecialchars($row['reviewed_by']); ?>
                                            <?php else: ?>
                                                <span style="color: #999;">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php 
                                            if(!empty($row['review_date'])) {
                                                echo date('M d, Y', strtotime($row['review_date']));
                                            } else {
                                                echo '<span style="color: #999;">N/A</span>';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="12" class="no-data">
                                        <p>No archived scholarship applications found.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <script src="../js/script.js"></script>
</body>
</html>
<?php
$conn->close();
?>
