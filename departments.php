<?php
include("includes/config.php");

// Only Admin can access - Check this BEFORE any HTML output
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$message = "";

// Handle Delete Department - MUST be before header.php for redirect to work
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $con->prepare("DELETE FROM departments WHERE dept_id = ?");
    $stmt->execute([$id]);
    header("Location: departments.php");
    exit();
}

// Handle Add Department
if (isset($_POST['add_dept'])) {
    $dept_name = $_POST['dept_name'];
    $dept_color = $_POST['dept_color'] ?? '#4361ee';
    if (!empty($dept_name)) {
        $stmt = $con->prepare("INSERT INTO departments (dept_name, dept_color) VALUES (?, ?)");
        $stmt->execute([$dept_name, $dept_color]);
        $message = "Department added successfully!";
    }
}

// Handle Update Department
if (isset($_POST['update_dept'])) {
    $dept_id = $_POST['dept_id'];
    $dept_name = $_POST['dept_name'];
    $dept_color = $_POST['dept_color'] ?? '#4361ee';
    if (!empty($dept_id) && !empty($dept_name)) {
        $stmt = $con->prepare("UPDATE departments SET dept_name = ?, dept_color = ? WHERE dept_id = ?");
        $stmt->execute([$dept_name, $dept_color, $dept_id]);
        $message = "Department updated successfully!";
    }
}

// Fetch all departments
$depts = $con->query("SELECT * FROM departments ORDER BY dept_id DESC")->fetchAll();

include("includes/header.php");
include("includes/sidebar.php");
?>

<div class="content">
    <div class="container-fluid content-animate">
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <h2 class="fw-bold text-primary mb-1"><i class="fas fa-building me-2"></i> Departments</h2>
                <p class="text-muted mb-0">Manage organization departments and structures.</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <button class="btn btn-primary px-4 py-2 shadow-sm rounded-3" data-bs-toggle="modal" data-bs-target="#addDeptModal">
                    <i class="fas fa-plus-circle me-2"></i> New Department
                </button>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> <?= $message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4" style="width: 80px;">ID</th>
                                <th>Department Name</th>
                                <th>Created At</th>
                                <th>Status</th>
                                <th class="text-center pe-4" style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($depts as $d): ?>
                            <tr>
                                <td class="ps-4 text-muted">#<?= $d['dept_id'] ?></td>
                                <td style="background: <?= $d['dept_color'] ?>;">
                                    <div class="d-flex align-items-center ps-2">
                                        <div class="fw-bold text-white text-uppercase small" style="letter-spacing: 0.5px;">
                                            <?= htmlspecialchars($d['dept_name']) ?>
                                        </div>
                                    </div>
                                </td>
                                <td><?= date("d M Y, h:i A", strtotime($d['created_at'])) ?></td>
                                <td>
                                    <span class="badge rounded-pill bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3">
                                        Active
                                    </span>
                                </td>
                                <td class="text-center pe-4">
                                    <button class="btn btn-outline-primary btn-sm border-0 rounded-3 px-3 editDeptBtn" 
                                        data-id="<?= $d['dept_id'] ?>" 
                                        data-name="<?= htmlspecialchars($d['dept_name']) ?>"
                                        data-color="<?= $d['dept_color'] ?>">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </button>
                                    <a href="?delete=<?= $d['dept_id'] ?>" class="btn btn-outline-danger btn-sm border-0 rounded-3 px-3" onclick="return confirm('Delete this department?')">
                                        <i class="fas fa-trash-alt me-1"></i> Delete
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <?php if (empty($depts)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-muted opacity-50">
                                        <i class="fas fa-building fa-3x mb-3"></i>
                                        <p>No departments found. Add your first one above!</p>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Dept Modal -->
<div class="modal fade" id="addDeptModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Add New Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted text-uppercase">Department Name</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="fas fa-building text-primary"></i></span>
                        <input type="text" name="dept_name" class="form-control bg-light border-0" placeholder="e.g. Information Technology" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted text-uppercase">Color Code</label>
                    <div class="d-flex align-items-center gap-3">
                        <input type="color" name="dept_color" class="form-control form-control-color border-0 bg-transparent" value="#4361ee" title="Choose department color">
                        <span class="text-muted small">Select a unique color for this department</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="add_dept" class="btn btn-primary px-4 shadow">Create Department</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Dept Modal -->
<div class="modal fade" id="editDeptModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Edit Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="dept_id" id="edit_dept_id">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted text-uppercase">Department Name</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="fas fa-building text-primary"></i></span>
                        <input type="text" name="dept_name" id="edit_dept_name" class="form-control bg-light border-0" placeholder="e.g. Information Technology" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted text-uppercase">Color Code</label>
                    <div class="d-flex align-items-center gap-3">
                        <input type="color" name="dept_color" id="edit_dept_color" class="form-control form-control-color border-0 bg-transparent" title="Choose department color">
                        <span class="text-muted small">Update the color for this department</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="update_dept" class="btn btn-primary px-4 shadow">Update Department</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editBtns = document.querySelectorAll('.editDeptBtn');
    const editModal = new bootstrap.Modal(document.getElementById('editDeptModal'));
    
    editBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('edit_dept_id').value = this.dataset.id;
            document.getElementById('edit_dept_name').value = this.dataset.name;
            document.getElementById('edit_dept_color').value = this.dataset.color || '#4361ee';
            editModal.show();
        });
    });
});
</script>

<style>
.dept-color-dot {
    width: 12px; height: 12px; border-radius: 50%;
}
.form-control-color {
    width: 50px; height: 40px; padding: 0; cursor: pointer;
}
</style>

<?php include("includes/footer.php"); ?>
