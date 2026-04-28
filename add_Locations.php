<?php
include("includes/config.php");

// Only Admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$message = "";

// Handle Delete Location
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $con->prepare("DELETE FROM plants WHERE plant_id = ?");
    $stmt->execute([$id]);
    header("Location: add_Locations.php");
    exit();
}

// Handle Update Location
if (isset($_POST['update_location'])) {
    $id = $_POST['plant_id'];
    $location = $_POST['plant_location'];
    if (!empty($id) && !empty($location)) {
        $stmt = $con->prepare("UPDATE plants SET plant_location = ? WHERE plant_id = ?");
        $stmt->execute([$location, $id]);
        $message = "Location updated successfully!";
    }
}

// Fetch all locations
$plants = $con->query("SELECT * FROM plants ORDER BY plant_id DESC")->fetchAll();

include("includes/header.php");
include("includes/sidebar.php");
?>

<div class="content">
    <div class="container-fluid content-animate">
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <h2 class="fw-bold text-primary mb-1"><i class="fas fa-map-marker-alt me-2"></i>Locations</h2>
                <p class="text-muted mb-0">Update or remove existing facility locations.</p>
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
                        <thead class="bg-light text-uppercase small fw-bold">
                            <tr>
                                <th class="ps-4" style="width: 100px;">ID</th>
                                <th>Location Name</th>
                                <th>Created At</th>
                                <th class="text-center pe-4" style="width: 200px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($plants as $p): ?>
                            <tr>
                                <td class="ps-4 text-muted">#<?= $p['plant_id'] ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                            <i class="fas fa-map-marker-alt small"></i>
                                        </div>
                                        <span class="fw-bold text-dark"><?= htmlspecialchars($p['plant_location']) ?></span>
                                    </div>
                                </td>
                                <td><?= date("d M Y", strtotime($p['created_at'])) ?></td>
                                <td class="text-center pe-4">
                                    <button class="btn btn-outline-primary btn-sm border-0 rounded-3 px-3 editLocationBtn" 
                                        data-id="<?= $p['plant_id'] ?>" 
                                        data-location="<?= htmlspecialchars($p['plant_location']) ?>">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </button>
                                    <a href="?delete=<?= $p['plant_id'] ?>" class="btn btn-outline-danger btn-sm border-0 rounded-3 px-3" onclick="return confirm('Delete this location?')">
                                        <i class="fas fa-trash-alt me-1"></i> Delete
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <?php if (empty($plants)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="text-muted opacity-50">
                                        <i class="fas fa-map-marked-alt fa-3x mb-3"></i>
                                        <p>No locations found. Add your first one above!</p>
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

<!-- Edit Location Modal -->
<div class="modal fade" id="editLocationModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Edit Location</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="plant_id" id="edit_plant_id">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted text-uppercase">Location Name</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="fas fa-map-marker-alt text-danger"></i></span>
                        <input type="text" name="plant_location" id="edit_plant_location" class="form-control bg-light border-0" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="update_location" class="btn btn-primary px-4 shadow">Update Location</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editBtns = document.querySelectorAll('.editLocationBtn');
    const editModal = new bootstrap.Modal(document.getElementById('editLocationModal'));
    
    editBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('edit_plant_id').value = this.dataset.id;
            document.getElementById('edit_plant_location').value = this.dataset.location;
            editModal.show();
        });
    });
});
</script>

<?php include("includes/footer.php"); ?>
