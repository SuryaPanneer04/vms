<?php
include("includes/config.php");

// Only logged in users can access
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if user has subordinates
$stmt = $con->prepare("SELECT COUNT(*) FROM users WHERE reporting_manager = ?");
$stmt->execute([$user_id]);
if ($stmt->fetchColumn() == 0) {
    // If not a manager, redirect or show message
    header("Location: index.php");
    exit();
}

// Fetch Team Members
$stmt = $con->prepare("SELECT u.id, u.full_name, u.designation, u.department, u.email, d.dept_color 
                       FROM users u 
                       LEFT JOIN departments d ON u.department = d.dept_name 
                       WHERE u.reporting_manager = ?");
$stmt->execute([$user_id]);
$team_members = $stmt->fetchAll();
$team_ids = array_column($team_members, 'id');

// Fetch Team Visitors
$visitors = [];
if (!empty($team_ids)) {
    $placeholders = str_repeat('?,', count($team_ids) - 1) . '?';
    $v_query = "SELECT v.*, u.full_name as host_name, u.department as host_dept, d.dept_color 
                FROM visitor_master v 
                JOIN users u ON v.employee_id = u.id 
                LEFT JOIN departments d ON u.department = d.dept_name
                WHERE v.employee_id IN ($placeholders) 
                ORDER BY v.created_at DESC";
    $v_stmt = $con->prepare($v_query);
    $v_stmt->execute($team_ids);
    $visitors = $v_stmt->fetchAll();
}

include("includes/header.php");
include("includes/sidebar.php");
?>

<div class="content">
    <div class="container-fluid content-animate">
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h2 class="fw-bold text-dark mb-0">Team Visitor Management</h2>
                <div class="d-flex gap-2">
                    <span class="badge bg-primary px-3 py-2 rounded-pill shadow-sm">
                        <i class="fas fa-users me-2"></i> Team Size: <?= count($team_members) ?>
                    </span>
                    <span class="badge bg-success px-3 py-2 rounded-pill shadow-sm">
                        <i class="fas fa-id-card me-2"></i> Total Visitors: <?= count($visitors) ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Team Members List (Accordion style) -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold mb-0 text-primary">My Direct Reports</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Employee Name</th>
                                <th>Designation</th>
                                <th>Department</th>
                                <th>Contact Email</th>
                                <th class="text-center pe-4">Visitors (Total)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($team_members as $m): 
                                $count = array_reduce($visitors, function($carry, $v) use ($m) {
                                    return $carry + ($v['employee_id'] == $m['id'] ? 1 : 0);
                                }, 0);
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                            <i class="fas fa-user small"></i>
                                        </div>
                                        <span class="fw-bold"><?= htmlspecialchars($m['full_name']) ?></span>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($m['designation']) ?></td>
                                <td><span class="badge fw-normal" style="background: <?= !empty($m['dept_color']) ? $m['dept_color'] : '#f8f9fa' ?>; color: <?= !empty($m['dept_color']) ? '#fff' : '#212529' ?>;"><?= htmlspecialchars($m['department']) ?></span></td>
                                <td><?= htmlspecialchars($m['email']) ?></td>
                                <td class="text-center pe-4">
                                    <span class="badge <?= $count > 0 ? 'bg-primary' : 'bg-secondary' ?> rounded-pill px-3"><?= $count ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Team Visitors Detailed Log -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-dark py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-white">Team Visitor Logs</h5>
                <button class="btn btn-sm btn-outline-light" onclick="window.print()"><i class="fas fa-print me-2"></i> Print Log</button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Pass No</th>
                                <th>Visitor Name</th>
                                <th>Meeting Host</th>
                                <th>Purpose</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th class="text-center pe-4">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($visitors as $v): ?>
                            <tr>
                                <td class="ps-4"><code class="fw-bold"><?= htmlspecialchars($v['pass_no']) ?></code></td>
                                <td>
                                    <div class="fw-bold"><?= htmlspecialchars($v['visitor_name']) ?></div>
                                    <small class="text-muted"><?= htmlspecialchars($v['company_name']) ?></small>
                                </td>
                                <td>
                                    <div class="fw-bold text-primary"><?= htmlspecialchars($v['host_name']) ?></div>
                                    <span class="badge fw-normal small" style="background: <?= !empty($v['dept_color']) ? $v['dept_color'] : '#f8f9fa' ?>; color: <?= !empty($v['dept_color']) ? '#fff' : '#212529' ?>;"><?= htmlspecialchars($v['host_dept']) ?></span>
                                </td>
                                <td><?= htmlspecialchars($v['purpose']) ?></td>
                                <td><?= date("d M, h:i A", strtotime($v['in_time'])) ?></td>
                                <td><?= $v['out_time'] ? date("d M, h:i A", strtotime($v['out_time'])) : '<span class="text-danger small">In-Premise</span>' ?></td>
                                <td class="text-center pe-4">
                                    <?php 
                                        $s = $v['approval_status'];
                                        $badge = 'bg-secondary';
                                        $text = 'Pending';
                                        if($s == 1) { $badge = 'bg-success'; $text = 'Completed'; }
                                        elseif($s == 2) { $badge = 'bg-danger'; $text = 'Rejected'; }
                                        elseif($s == 3) { $badge = 'bg-info text-white'; $text = 'Scheduled'; }
                                    ?>
                                    <span class="badge <?= $badge ?> px-3 py-1"><?= $text ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>

                            <?php if (empty($visitors)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-user-clock fa-3x mb-3"></i><br>
                                    No visitors recorded for your team members yet.
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

<?php include("includes/footer.php"); ?>
