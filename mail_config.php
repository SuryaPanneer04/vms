<?php
include("includes/config.php");

// Only Admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$message = "";

// Handle Update
if (isset($_POST['update_config'])) {
    $host = $_POST['smtp_host'];
    $user = $_POST['smtp_user'];
    $pass = $_POST['smtp_pass'];
    $port = $_POST['smtp_port'];
    $secure = $_POST['smtp_secure'];
    $from_e = $_POST['from_email'];
    $from_n = $_POST['from_name'];
    $footer = $_POST['mail_footer'];

    try {
        $stmt = $con->prepare("UPDATE mail_settings SET smtp_host=?, smtp_user=?, smtp_pass=?, smtp_port=?, smtp_secure=?, from_email=?, from_name=?, mail_footer=? WHERE id=1");
        $stmt->execute([$host, $user, $pass, $port, $secure, $from_e, $from_n, $footer]);
        $message = "<div class='alert alert-success'>Mail configuration updated successfully!</div>";
    } catch (PDOException $e) {
        $message = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
}

// Fetch current
$config = $con->query("SELECT * FROM mail_settings LIMIT 1")->fetch();

include("includes/header.php");
include("includes/sidebar.php");
?>

<div class="content">
    <div class="container-fluid content-animate">
        <h2 class="fw-bold text-dark mb-4">Mail Configuration</h2>

        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">SMTP Settings</h5>
                        
                        <?php if ($message) echo $message; ?>

                        <form method="POST" class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">SMTP Host</label>
                                <input type="text" name="smtp_host" class="form-control" value="<?= htmlspecialchars($config['smtp_host']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">SMTP User (Email)</label>
                                <input type="email" name="smtp_user" class="form-control" value="<?= htmlspecialchars($config['smtp_user']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">SMTP Password / App Password</label>
                                <input type="password" name="smtp_pass" class="form-control" value="<?= htmlspecialchars($config['smtp_pass']) ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Port</label>
                                <input type="number" name="smtp_port" class="form-control" value="<?= htmlspecialchars($config['smtp_port']) ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Secure</label>
                                <select name="smtp_secure" class="form-select">
                                    <option value="tls" <?= $config['smtp_secure'] == 'tls' ? 'selected' : '' ?>>TLS</option>
                                    <option value="ssl" <?= $config['smtp_secure'] == 'ssl' ? 'selected' : '' ?>>SSL</option>
                                </select>
                            </div>
                            
                            <hr class="my-4">
                            <h5 class="fw-bold mb-3">Sender Details</h5>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold">From Email</label>
                                <input type="email" name="from_email" class="form-control" value="<?= htmlspecialchars($config['from_email']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">From Name</label>
                                <input type="text" name="from_name" class="form-control" value="<?= htmlspecialchars($config['from_name']) ?>" required>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label small fw-bold">Global Email Footer (HTML Supported)</label>
                                <textarea name="mail_footer" class="form-control" rows="4" placeholder="Regards, Team VMS"><?= htmlspecialchars($config['mail_footer'] ?? '') ?></textarea>
                            </div>

                            <div class="col-12 text-end mt-4">
                                <button type="submit" name="update_config" class="btn btn-primary px-5 py-2 rounded-3 shadow">
                                    <i class="fas fa-save me-2"></i> Save Configuration
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card border-0 bg-primary-subtle text-primary rounded-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold">Information</h6>
                        <p class="small mb-0">Use this page to configure the SMTP server for sending account credentials and notifications to your employees.</p>
                        <hr>
                        <p class="small"><b>Note:</b> If using Gmail, you must use an <b>App Password</b> instead of your regular account password.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("includes/footer.php"); ?>
