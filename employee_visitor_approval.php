<?php
include("includes/config.php");
include("includes/header.php");
include("includes/sidebar.php");

// Only employees (and managers/admin if needed, but primarily for the 'employee_id' match)
$emp_id = $_SESSION['user_id'];

// Handle Approval/Rejection
$message = "";
if (isset($_POST['action']) && isset($_POST['visitor_id'])) {
    $visitor_id = $_POST['visitor_id'];
    $action = $_POST['action']; // 'approve' or 'reject'
    $status = ($action === 'approve') ? 1 : 2;

    try {
        $con->beginTransaction();
        
        $stmt = $con->prepare("UPDATE visitor_master SET approval_status = ? WHERE id = ? AND employee_id = ?");
        $stmt->execute([$status, $visitor_id, $emp_id]);
        
        if ($status == 1) {
            // Check if handoff already exists
            $check_h = $con->prepare("SELECT id FROM visitor_handoffs WHERE visitor_id = ?");
            $check_h->execute([$visitor_id]);
            if (!$check_h->fetch()) {
                // Fetch in_time to maintain consistency
                $v_stmt = $con->prepare("SELECT in_time FROM visitor_master WHERE id = ?");
                $v_stmt->execute([$visitor_id]);
                $v_time = $v_stmt->fetchColumn() ?: date("Y-m-d H:i:s");

                $h_ins = $con->prepare("INSERT INTO visitor_handoffs (visitor_id, emp_id, assigned_by, check_in_time, notes) VALUES (?, ?, ?, ?, ?)");
                $h_ins->execute([$visitor_id, $emp_id, $emp_id, $v_time, "Visitor Approved"]);
            }
            
            $message = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                            <i class='fas fa-check-circle me-2'></i> Visitor Approved Successfully!
                            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                        </div>";
        } else {
            $message = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                            <i class='fas fa-times-circle me-2'></i> Visitor Request Rejected.
                            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                        </div>";
        }
        
        $con->commit();
    } catch (PDOException $e) {
        if ($con->inTransaction()) $con->rollBack();
        $message = "<div class='alert alert-warning'>Error: " . $e->getMessage() . "</div>";
    }
}

// Fetch Pending Visitors for this Employee
$query = "SELECT v.*, u.full_name as checkin_staff 
          FROM visitor_master v 
          LEFT JOIN users u ON v.checkin_by = u.id
          WHERE v.employee_id = ? AND v.approval_status = 0 
          ORDER BY v.created_at DESC";
$stmt = $con->prepare($query);
$stmt->execute([$emp_id]);
$pending_visitors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content">
    <div class="container-fluid content-animate">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="fw-bold mb-1 text-primary"><i class="fas fa-user-check me-2"></i> Visitor Approvals</h2>
                <p class="text-muted">Review and manage pending visitor requests assigned to you.</p>
            </div>
        </div>

        <?= $message ?>

        <div class="card shadow border-0 overflow-hidden">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Pending Requests</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-uppercase small fw-bold">
                            <tr>
                                <th class="ps-4">Visitor Info</th>
                                <th>Company / Purpose</th>
                                <th>Contact</th>
                                <th>Entry Time</th>
                                <th class="text-center pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pending_visitors)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-clipboard-check fa-3x mb-3 opacity-25"></i>
                                        <p>No pending visitor requests at the moment.</p>
                                    </div>
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($pending_visitors as $v): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle me-3 bg-warning text-dark">
                                                <?= strtoupper(substr($v['visitor_name'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <div class="fw-bold"><?= htmlspecialchars($v['visitor_name']) ?></div>
                                                <div class="small text-muted">#<?= $v['pass_no'] ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small fw-bold"><?= htmlspecialchars($v['company_name'] ?: 'Independent') ?></div>
                                        <div class="small text-muted"><?= htmlspecialchars($v['purpose']) ?></div>
                                    </td>
                                    <td>
                                        <div class="small"><i class="fas fa-phone-alt me-1 text-muted"></i> <?= htmlspecialchars($v['contact_no']) ?></div>
                                        <div class="small"><i class="fas fa-envelope me-1 text-muted"></i> <?= htmlspecialchars($v['email'] ?: 'N/A') ?></div>
                                    </td>
                                    <td>
                                        <div class="small text-primary"><i class="fas fa-clock me-1"></i> <?= date("d M, h:i A", strtotime($v['in_time'])) ?></div>
                                        <div class="small mt-1 text-muted">
                                            <i class="fas fa-user-edit me-1"></i> Check-in by: <strong><?= htmlspecialchars($v['checkin_staff'] ?: 'N/A') ?></strong>
                                        </div>
                                    </td>
                                    <td class="text-center pe-4">
                                        <div class="d-flex justify-content-center gap-2">
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="visitor_id" value="<?= $v['id'] ?>">
                                                <button type="submit" name="action" value="approve" class="btn btn-success btn-sm px-3 shadow-sm">
                                                    <i class="fas fa-check me-1"></i> Approve
                                                </button>
                                            </form>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="visitor_id" value="<?= $v['id'] ?>">
                                                <button type="submit" name="action" value="reject" class="btn btn-outline-danger btn-sm px-3 shadow-sm">
                                                    <i class="fas fa-times me-1"></i> Reject
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.9rem;
}
.btn-sm {
    font-size: 0.8rem;
    font-weight: 600;
}
</style>

<?php include("includes/footer.php"); ?>
