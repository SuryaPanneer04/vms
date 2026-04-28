<?php
include("includes/config.php");

// Only Admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$message = "";

// Handle Update Theme
if (isset($_POST['update_theme'])) {
    $color = $_POST['sidebar_color'];
    try {
        $stmt = $con->prepare("UPDATE app_settings SET setting_value = ? WHERE setting_key = 'sidebar_color'");
        $stmt->execute([$color]);
        $message = "<div class='alert alert-success'>Theme updated successfully!</div>";
    } catch (PDOException $e) {
        $message = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
}

// Fetch current color
$stmt = $con->prepare("SELECT setting_value FROM app_settings WHERE setting_key = 'sidebar_color'");
$stmt->execute();
$current_color = $stmt->fetchColumn() ?: '#000000';

include("includes/header.php");
include("includes/sidebar.php");
?>

<div class="content">
    <div class="container-fluid content-animate">
        <div class="d-flex align-items-center mb-4">
            <div class="bg-primary-subtle p-3 rounded-4 me-3">
                <i class="fas fa-palette text-primary fs-3"></i>
            </div>
            <div>
                <h2 class="fw-bold text-dark mb-0">Theme Settings</h2>
                <p class="text-muted mb-0">Customize the appearance of your VMS Pro workspace.</p>
            </div>
        </div>

        <?php if ($message) echo $message; ?>

        <div class="row">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white py-3 border-0">
                        <h5 class="fw-bold mb-0">Sidebar Configuration</h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST">
                            <div class="mb-4">
                                <label class="form-label fw-bold small text-muted text-uppercase mb-3">Choose Sidebar Background Color</label>
                                <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-4">
                                    <input type="color" name="sidebar_color" class="form-control form-control-color border-0 rounded-circle" id="colorPicker" value="<?= $current_color ?>" style="width: 60px; height: 60px; cursor: pointer;">
                                    <div>
                                        <div class="fw-bold" id="colorHex"><?= strtoupper($current_color) ?></div>
                                        <small class="text-muted">Select a color to change the sidebar theme instantly.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="alert bg-primary-subtle text-primary border-0 rounded-4 d-flex align-items-center gap-3">
                                <i class="fas fa-info-circle fs-4"></i>
                                <div class="small">This change will apply globally to all users' sidebars. Choose a color that maintains high contrast for text readability.</div>
                            </div>

                            <button type="submit" name="update_theme" class="btn btn-primary w-100 py-3 rounded-4 fw-bold shadow-sm">
                                <i class="fas fa-save me-2"></i> Save Theme Changes
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                    <div class="card-header bg-white py-3 border-0">
                        <h5 class="fw-bold mb-0">Live Preview</h5>
                    </div>
                    <div class="card-body p-4 d-flex flex-column align-items-center justify-content-center bg-light">
                        <div id="sidebarPreview" class="rounded-4 shadow-lg p-4" style="width: 220px; height: 350px; background-color: <?= $current_color ?>; transition: all 0.3s ease;">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-primary rounded-2 p-1 me-2" style="width: 25px; height: 25px;"></div>
                                <div class="bg-white opacity-50 rounded" style="width: 100px; height: 10px;"></div>
                            </div>
                            <div class="mb-3 d-flex align-items-center gap-2">
                                <div class="bg-white opacity-25 rounded-circle" style="width: 15px; height: 15px;"></div>
                                <div class="bg-white opacity-25 rounded" style="width: 120px; height: 8px;"></div>
                            </div>
                            <div class="mb-3 d-flex align-items-center gap-2">
                                <div class="bg-white opacity-25 rounded-circle" style="width: 15px; height: 15px;"></div>
                                <div class="bg-white opacity-25 rounded" style="width: 120px; height: 8px;"></div>
                            </div>
                            <div class="mb-3 d-flex align-items-center gap-2" style="background: rgba(255,255,255,0.1); padding: 8px; border-radius: 8px;">
                                <div class="bg-white opacity-50 rounded-circle" style="width: 15px; height: 15px;"></div>
                                <div class="bg-white opacity-50 rounded" style="width: 120px; height: 8px;"></div>
                            </div>
                        </div>
                        <p class="mt-4 text-muted small text-center">Your sidebar will look like this.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('colorPicker').addEventListener('input', function(e) {
    const color = e.target.value;
    document.getElementById('colorHex').innerText = color.toUpperCase();
    document.getElementById('sidebarPreview').style.backgroundColor = color;
});
</script>

<?php include("includes/footer.php"); ?>
