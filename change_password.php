<?php
include("includes/config.php");
include("includes/header.php");
include("includes/sidebar.php");

$emp_id = $_SESSION['user_id'];
$message = "";

if (isset($_POST['change_password'])) {
    $current_pass = $_POST['current_password'];
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    if ($new_pass !== $confirm_pass) {
        $message = "<div class='alert alert-danger shadow-sm border-0'><i class='fas fa-exclamation-circle me-2'></i> New passwords do not match!</div>";
    } else {
        // Verify current password
        $stmt = $con->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$emp_id]);
        $user = $stmt->fetch();

        if (password_verify($current_pass, $user['password'])) {
            $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
            $update_stmt = $con->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update_stmt->execute([$hashed_pass, $emp_id]);
            $message = "<div class='alert alert-success shadow-sm border-0'><i class='fas fa-check-circle me-2'></i> Password updated successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger shadow-sm border-0'><i class='fas fa-times-circle me-2'></i> Current password is incorrect!</div>";
        }
    }
}
?>

<div class="content">
    <div class="content-animate d-flex justify-content-center align-items-center" style="min-height: calc(100vh - 150px);">
        <div style="width: 100%; max-width: 450px;">
            <?php if($message) echo $message; ?>
            
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <!-- Header with Gradient -->
                <div class="p-4 bg-primary text-white text-center position-relative overflow-hidden">
                    <!-- Decorative Circle Background -->
                    <div class="position-absolute rounded-circle bg-white bg-opacity-10" style="width: 150px; height: 150px; top: -50px; right: -50px;"></div>
                    
                    <div class="bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 shadow-sm" style="width: 70px; height: 70px; backdrop-filter: blur(5px);">
                        <i class="fas fa-key fs-3"></i>
                    </div>
                    <h4 class="fw-bold mb-1">Update Security</h4>
                    <p class="small mb-0 opacity-75">Secure your account with a new password</p>
                </div>
                
                <div class="card-body p-4 p-md-5 bg-white">
                    <form method="POST">
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase text-muted" style="letter-spacing: 0.5px;">Current Password</label>
                            <div class="input-group border rounded-3 overflow-hidden bg-light border-0">
                                <span class="input-group-text bg-transparent border-0 pe-1"><i class="fas fa-lock text-muted opacity-50"></i></span>
                                <input type="password" name="current_password" class="form-control bg-transparent border-0 py-2 ps-2" placeholder="Enter current password" required>
                            </div>
                        </div>

                        <hr class="my-4 opacity-10">

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted" style="letter-spacing: 0.5px;">New Password</label>
                            <div class="input-group border rounded-3 overflow-hidden bg-light border-0">
                                <span class="input-group-text bg-transparent border-0 pe-1"><i class="fas fa-shield-alt text-muted opacity-50"></i></span>
                                <input type="password" name="new_password" class="form-control bg-transparent border-0 py-2 ps-2" placeholder="Min. 6 characters" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase text-muted" style="letter-spacing: 0.5px;">Confirm Password</label>
                            <div class="input-group border rounded-3 overflow-hidden bg-light border-0">
                                <span class="input-group-text bg-transparent border-0 pe-1"><i class="fas fa-check-circle text-muted opacity-50"></i></span>
                                <input type="password" name="confirm_password" class="form-control bg-transparent border-0 py-2 ps-2" placeholder="Repeat new password" required>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-5">
                            <button type="submit" name="change_password" class="btn btn-primary btn-lg py-3 rounded-3 shadow-sm fw-bold border-0" style="background: linear-gradient(135deg, #4361ee, #3f37c9);">
                                Update Password <i class="fas fa-save ms-2"></i>
                            </button>
                            <a href="employee_portal.php" class="btn btn-link text-muted text-decoration-none mt-2 small fw-bold">
                                <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <p class="text-muted small">
                    <i class="fas fa-shield-halved me-1 text-primary"></i> 
                    Your security is our priority.
                </p>
            </div>
        </div>
    </div>
</div>

<style>
.content-animate {
    animation: fadeInUp 0.5s cubic-bezier(0.165, 0.84, 0.44, 1);
}
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}
.input-group:focus-within {
    box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
    background: #fff !important;
    border: 1px solid rgba(67, 97, 238, 0.3) !important;
}
.form-control:focus {
    box-shadow: none;
}
</style>

<?php include("includes/footer.php"); ?>
