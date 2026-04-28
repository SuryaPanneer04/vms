<?php
include("includes/config.php");

// Only Admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$message = "";

// Handle Status Toggle (Active/Inactive)
if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = $_GET['id'];
    $new_status = $_GET['status'];
    $stmt = $con->prepare("UPDATE designation_master SET status = ? WHERE id = ?");
    $stmt->execute([$new_status, $id]);
    header("Location: designations.php?msg=Status updated successfully");
    exit();
}

// Handle Update Designation
if (isset($_POST['update_designation'])) {
    $id = $_POST['id'];
    $designation_name = $_POST['designation_name'];
    if (!empty($id) && !empty($designation_name)) {
        $stmt = $con->prepare("UPDATE designation_master SET designation_name = ? WHERE id = ?");
        $stmt->execute([$designation_name, $id]);
        $message = "Designation updated successfully!";
    }
}

if (isset($_GET['msg'])) {
    $message = $_GET['msg'];
}

// Fetch all designations
$designations = $con->query("SELECT * FROM designation_master ORDER BY id ASC")->fetchAll();

include("includes/header.php");
include("includes/sidebar.php");
?>

<div class="content">
    <div class="container-fluid content-animate">
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <h2 class="fw-bold text-primary mb-1"><i class="fas fa-user-tag me-2"></i> Designations</h2>
                <p class="text-muted mb-0">Manage default employee designations and their availability.</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <!-- No Add option as per requirement -->
                <div class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-3 py-2 rounded-3">
                    <i class="fas fa-info-circle me-1"></i> Management Only
                </div>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> <?= htmlspecialchars($message) ?>
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
                                <th>Designation Name</th>
                                <th>Created At</th>
                                <th>Status</th>
                                <th class="text-center pe-4" style="width: 250px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($designations as $d): ?>
                            <tr>
                                <td class="ps-4 text-muted">#<?= $d['id'] ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($d['designation_name']) ?></div>
                                </td>
                                <td><?= date("d M Y", strtotime($d['created_at'])) ?></td>
                                <td>
                                    <?php if ($d['status'] == 1): ?>
                                        <span class="badge rounded-pill bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3">
                                            Active
                                        </span>
                                    <?php else: ?>
                                        <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3">
                                            Inactive
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center pe-4">
                                    <button class="btn btn-outline-primary btn-sm border-0 rounded-3 px-3 editBtn" 
                                        data-id="<?= $d['id'] ?>" 
                                        data-name="<?= htmlspecialchars($d['designation_name']) ?>">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </button>
                                    
                                    <?php if ($d['status'] == 1): ?>
                                        <a href="?id=<?= $d['id'] ?>&status=0" class="btn btn-outline-warning btn-sm border-0 rounded-3 px-3" onclick="return confirm('Mark this designation as Inactive?')">
                                            <i class="fas fa-toggle-off me-1"></i> Deactivate
                                        </a>
                                    <?php else: ?>
                                        <a href="?id=<?= $d['id'] ?>&status=1" class="btn btn-outline-success btn-sm border-0 rounded-3 px-3" onclick="return confirm('Mark this designation as Active?')">
                                            <i class="fas fa-toggle-on me-1"></i> Activate
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <?php if (empty($designations)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-muted opacity-50">
                                        <i class="fas fa-user-tag fa-3x mb-3"></i>
                                        <p>No designations found.</p>
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

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Edit Designation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="id" id="edit_id">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted text-uppercase">Designation Name</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="fas fa-user-tag text-primary"></i></span>
                        <input type="text" name="designation_name" id="edit_name" class="form-control bg-light border-0" placeholder="e.g. Senior Manager" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="update_designation" class="btn btn-primary px-4 shadow">Update Designation</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editBtns = document.querySelectorAll('.editBtn');
    const editModal = new bootstrap.Modal(document.getElementById('editModal'));
    
    editBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('edit_id').value = this.dataset.id;
            document.getElementById('edit_name').value = this.dataset.name;
            editModal.show();
        });
    });
});
</script>

<?php include("includes/footer.php"); ?>
