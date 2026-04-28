<?php
include("includes/config.php");
include("includes/header.php");
include("includes/sidebar.php");

// Only Admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Filters
$search = $_GET['search'] ?? '';
$dept_filter = $_GET['department'] ?? '';
$desig_filter = $_GET['designation'] ?? '';

// Pagination Setup
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Base Query Conditions
$where = " WHERE (u.full_name LIKE ? OR u.email LIKE ?)";
$params = ["%$search%", "%$search%"];

if (!empty($dept_filter)) {
    $where .= " AND u.department = ?";
    $params[] = $dept_filter;
}

if (!empty($desig_filter)) {
    $where .= " AND u.designation = ?";
    $params[] = $desig_filter;
}

// Count total records for pagination
$count_query = "SELECT COUNT(*) FROM users u" . $where;
$count_stmt = $con->prepare($count_query);
$count_stmt->execute($params);
$total_records = $count_stmt->fetchColumn();
$total_pages = ceil($total_records / $limit);

// Fetch records
$query = "SELECT u.*, m.full_name as manager_name, d.dept_color 
          FROM users u 
          LEFT JOIN users m ON u.reporting_manager = m.id 
          LEFT JOIN departments d ON u.department = d.dept_name
          $where
          ORDER BY u.id DESC 
          LIMIT $limit OFFSET $offset";

$stmt = $con->prepare($query);
$stmt->execute($params);
$staff = $stmt->fetchAll();

// Fetch Data for Filters
$departments = $con->query("SELECT dept_name FROM departments WHERE status = 1 ORDER BY dept_name ASC")->fetchAll();
$designations = $con->query("SELECT DISTINCT designation FROM users WHERE designation IS NOT NULL AND designation != '' ORDER BY designation ASC")->fetchAll();
?>

<div class="content">
    <div class="container-fluid content-animate">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="fw-bold text-dark mb-0">Staff List</h2>
                <p class="text-muted small">View and manage all system users with advanced filtering</p>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-3">
                <form method="GET" class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-0 pe-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-0 bg-transparent" placeholder="Search by name or email..." value="<?= htmlspecialchars($search) ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="department" class="form-select border-0 bg-light rounded-3">
                            <option value="">All Departments</option>
                            <?php foreach ($departments as $d): ?>
                                <option value="<?= htmlspecialchars($d['dept_name']) ?>" <?= $dept_filter == $d['dept_name'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($d['dept_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="designation" class="form-select border-0 bg-light rounded-3">
                            <option value="">All Designations</option>
                            <?php foreach ($designations as $ds): ?>
                                <option value="<?= htmlspecialchars($ds['designation']) ?>" <?= $desig_filter == $ds['designation'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($ds['designation']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100 rounded-3 shadow-sm">Filter</button>
                        <a href="staff_list.php" class="btn btn-light rounded-3"><i class="fas fa-undo"></i></a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Staff Table Detailed View -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3">Employee Name</th>
                                <th class="py-3 text-center">Designation</th>
                                <th class="py-3">Contact Details</th>
                                <th class="py-3">Role & Dept</th>
                                <th class="py-3">Reporting To</th>
                                <th class="py-3 text-center">Status</th>
                                <th class="text-center pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($staff) > 0): ?>
                                <?php foreach ($staff as $s): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-soft-primary text-primary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-weight: bold; font-size: 1rem;">
                                                <?= strtoupper(substr($s['full_name'] ?: 'U', 0, 1)) ?>
                                            </div>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($s['full_name']) ?></div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-soft-secondary text-dark border-0 px-3 py-2"><?= htmlspecialchars($s['designation'] ?: 'N/A') ?></span>
                                    </td>
                                    <td>
                                        <div class="small fw-bold text-dark"><i class="fas fa-envelope me-2 text-muted"></i><?= htmlspecialchars($s['email']) ?></div>
                                        <div class="small text-muted mt-1"><i class="fas fa-phone me-2 text-muted"></i><?= htmlspecialchars($s['contact_no'] ?: '---') ?></div>
                                    </td>
                                    <td>
                                        <div class="badge fw-normal mb-1" style="background: <?= !empty($s['dept_color']) ? $s['dept_color'] : '#f8f9fa' ?>; color: <?= !empty($s['dept_color']) ? '#fff' : '#212529' ?>;">
                                            <?= htmlspecialchars($s['department'] ?: 'General') ?>
                                        </div>
                                        <div><span class="badge bg-light text-dark border-0 small text-uppercase" style="font-size: 0.65rem;"><?= htmlspecialchars($s['role']) ?></span></div>
                                    </td>
                                    <td>
                                        <?php if ($s['manager_name']): ?>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-user-tie text-primary me-2 small"></i>
                                                <span class="fw-bold small"><?= htmlspecialchars($s['manager_name']) ?></span>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted italic small">No Manager</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($s['status'] == 'Active'): ?>
                                            <span class="badge bg-success-soft text-success px-3 rounded-pill"><i class="fas fa-check-circle me-1 small"></i> Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-soft text-danger px-3 rounded-pill"><i class="fas fa-times-circle me-1 small"></i> Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center pe-4">
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm rounded-circle" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3">
                                                <li><a class="dropdown-item py-2" href="manage_users.php?search=<?= urlencode($s['email']) ?>"><i class="fas fa-edit me-2 text-primary"></i> Edit Profile</a></li>
                                                <li><a class="dropdown-item py-2" href="reporting_chart.php"><i class="fas fa-sitemap me-2 text-info"></i> View in Hierarchy</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item py-2 text-danger" href="#"><i class="fas fa-trash-alt me-2"></i> Deactivate</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fas fa-users-slash fa-3x mb-3 d-block opacity-25"></i>
                                        No staff members found matching your search.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination Layout -->
            <?php if ($total_pages > 1): ?>
            <div class="card-footer bg-white border-top py-3 px-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Showing page <b><?= $page ?></b> of <b><?= $total_pages ?></b>
                    </div>
                    <nav aria-label="Staff List Pagination">
                        <ul class="pagination pagination-md mb-0">
                            <!-- Previous Link -->
                            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                <a class="page-link border-0 rounded-circle me-1" href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>&department=<?= urlencode($dept_filter) ?>&designation=<?= urlencode($desig_filter) ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>

                            <?php 
                                $start = max(1, $page - 1);
                                $end = min($total_pages, $page + 1);
                                for($i = $start; $i <= $end; $i++): 
                            ?>
                                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                    <a class="page-link border-0 rounded-circle mx-1" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&department=<?= urlencode($dept_filter) ?>&designation=<?= urlencode($desig_filter) ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <!-- Next Link -->
                            <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                                <a class="page-link border-0 rounded-circle ms-1" href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>&department=<?= urlencode($dept_filter) ?>&designation=<?= urlencode($desig_filter) ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .bg-soft-primary { background: rgba(67, 97, 238, 0.1); }
    .bg-soft-secondary { background: rgba(108, 117, 125, 0.1); }
    .bg-soft-info { background: rgba(76, 201, 240, 0.1); }
    .bg-success-soft { background: rgba(16, 196, 105, 0.1); color: #10c469; }
    .bg-danger-soft { background: rgba(255, 95, 126, 0.1); color: #ff5f7e; }
    .table thead th { font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; color: #64748b; }
    .dropdown-item { font-size: 0.85rem; font-weight: 500; }
    .dropdown-item:hover { background: #f8f9fa; }
    .pagination .page-link { width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; font-weight: 600; color: #64748b; }
    .pagination .active .page-link { background: #4361ee; color: white; }
</style>

<?php include("includes/footer.php"); ?>
