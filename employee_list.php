<?php
session_start();
include("includes/header.php");
include("includes/sidebar.php");
include("includes/config.php");

// Fetch filters
$search_name = $_GET['search_name'] ?? '';
$search_contact = $_GET['search_contact'] ?? '';
$search_email = $_GET['search_email'] ?? '';

// Build query
$query = " FROM employee_master WHERE 1=1";
$params = [];

if (!empty($search_name)) {
    $query .= " AND emp_name LIKE ?";
    $params[] = "%$search_name%";
}
if (!empty($search_contact)) {
    $query .= " AND contact_no LIKE ?";
    $params[] = "%$search_contact%";
}
if (!empty($search_email)) {
    $query .= " AND email LIKE ?";
    $params[] = "%$search_email%";
}

// Pagination Setup
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Count total records
$count_stmt = $con->prepare("SELECT COUNT(*)" . $query);
$count_stmt->execute($params);
$total_records = $count_stmt->fetchColumn();
$total_pages = ceil($total_records / $limit);

// Fetch records with LIMIT
$stmt = $con->prepare("SELECT *" . $query . " ORDER BY emp_name ASC LIMIT $limit OFFSET $offset");
$stmt->execute($params);
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content">
    <div class="container-fluid content-animate">
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold mb-0 text-primary"><i class="fas fa-users me-2"></i> Employee Management</h4>
                <button type="button" class="btn btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="fas fa-file-excel me-2"></i> Import Employees
                </button>
            </div>
        </div>

        <!-- FILTERS -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Employee Name</label>
                        <input type="text" name="search_name" class="form-control" placeholder="Search by name..." value="<?= htmlspecialchars($search_name) ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Contact Number</label>
                        <input type="text" name="search_contact" class="form-control" placeholder="Search by phone..." value="<?= htmlspecialchars($search_contact) ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Email Address</label>
                        <input type="text" name="search_email" class="form-control" placeholder="Search by email..." value="<?= htmlspecialchars($search_email) ?>">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100 me-2"><i class="fas fa-filter me-2"></i> Filter</button>
                        <a href="employee_list.php" class="btn btn-light"><i class="fas fa-sync-alt"></i></a>
                    </div>
                </form>
            </div>
        </div>

        <!-- EMPLOYEE TABLE -->
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3 border-0">#</th>
                                <th class="py-3 border-0">Employee Name</th>
                                <th class="py-3 border-0">Designation</th>
                                <th class="py-3 border-0">Department</th>
                                <th class="py-3 border-0">Contact</th>
                                <th class="py-3 border-0">Email</th>
                                <th class="py-3 border-0">Joined Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($employees) > 0): ?>
                                <?php foreach ($employees as $index => $emp): ?>
                                    <tr>
                                        <td class="px-4"><?= $index + 1 ?></td>
                                        <td><span class="fw-bold"><?= htmlspecialchars($emp['emp_name']) ?></span></td>
                                        <td><span class="badge bg-soft-info text-info"><?= htmlspecialchars($emp['designation']) ?></span></td>
                                        <td><?= htmlspecialchars($emp['department']) ?></td>
                                        <td><i class="fas fa-phone-alt me-2 text-muted small"></i><?= htmlspecialchars($emp['contact_no'] ?: '---') ?></td>
                                        <td><i class="fas fa-envelope me-2 text-muted small"></i><?= htmlspecialchars($emp['email'] ?: '---') ?></td>
                                        <td class="text-muted small"><?= date("d M Y", strtotime($emp['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fas fa-user-slash fa-3x mb-3 d-block"></i>
                                        No employees found matching your criteria.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- PAGINATION CONTROLS -->
            <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center py-3">
                <div class="text-muted small">
                    Showing <?= count($employees) ?> of <?= $total_records ?> employees
                </div>
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page-1 ?>&search_name=<?= urlencode($search_name) ?>&search_contact=<?= urlencode($search_contact) ?>&search_email=<?= urlencode($search_email) ?>"><i class="fas fa-chevron-left"></i></a>
                        </li>
                        
                        <?php 
                        $start_loop = max(1, $page - 2);
                        $end_loop = min($total_pages, $page + 2);
                        for($i = $start_loop; $i <= $end_loop; $i++): 
                        ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>&search_name=<?= urlencode($search_name) ?>&search_contact=<?= urlencode($search_contact) ?>&search_email=<?= urlencode($search_email) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page+1 ?>&search_name=<?= urlencode($search_name) ?>&search_contact=<?= urlencode($search_contact) ?>&search_email=<?= urlencode($search_email) ?>"><i class="fas fa-chevron-right"></i></a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- IMPORT MODAL -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form id="importForm" enctype="multipart/form-data">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="fas fa-file-excel me-2"></i> Import Employee Data</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-warning small border-0 shadow-none">
                        <i class="fas fa-exclamation-triangle me-2"></i> <strong>Important:</strong> Uploading a new file will <b>DELETE all existing records</b> in the employee list and replace them with the new data.
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Select Excel File (.xlsx, .xls)</label>
                        <input type="file" name="excel_file" id="excel_file" class="form-control" accept=".xlsx, .xls" required>
                    </div>

                    <div class="card bg-light border-0">
                        <div class="card-body">
                            <h6 class="fw-bold small mb-2 text-uppercase">Expected Columns:</h6>
                            <p class="small text-muted mb-0">
                                1. Name | 2. Designation | 3. Department | 4. Contact | 5. Email
                            </p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success px-4" id="importBtn">
                        <i class="fas fa-upload me-2"></i> Start Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('importForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    let btn = document.getElementById('importBtn');
    let originalText = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Importing...';
    
    let formData = new FormData(this);
    
    fetch('import_employees.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.status == 'success') {
            alert('Employees imported successfully!');
            window.location.reload();
        } else {
            alert('Error: ' + data.message);
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    })
    .catch(err => {
        alert('An unexpected error occurred.');
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
});
</script>

<?php include("includes/footer.php"); ?>
