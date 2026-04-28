<?php 
include("includes/config.php");

// Only Admin can access dashboard
if ($_SESSION['role'] !== 'admin') {
    if ($_SESSION['role'] === 'timeoffice') {
        header("Location: add_visitor_form.php");
    } else {
        header("Location: employee_portal.php");
    }
    exit();
}

include("includes/header.php"); 
include("includes/sidebar.php"); 

// Quick Stats
$total = $con->query("SELECT COUNT(*) FROM visitor_master")->fetchColumn();
$inside = $con->query("SELECT COUNT(*) FROM visitor_master WHERE out_time IS NULL")->fetchColumn();
$today = $con->query("SELECT COUNT(*) FROM visitor_master WHERE DATE(in_time) = CURDATE()")->fetchColumn();
?>

<div class="content">
    <div class="container-fluid content-animate">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1">Dashboard</h2>
                <p class="text-muted small mb-0">Overview of VMS PRO Visitor Analytics</p>
            </div>
            <div class="text-end">
                <span class="badge bg-light text-dark border p-2">
                    <i class="fas fa-calendar-alt me-2 text-primary"></i> <?= date('d M, Y') ?>
                </span>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4" style="background: linear-gradient(135deg, #4361ee, #4895ef);">
                    <div class="card-body p-4 text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase fw-bold small mb-2" style="opacity: 0.8;">Currently Inside</h6>
                                <h2 class="fw-bold mb-0"><?= $inside ?></h2>
                            </div>
                            <div class="fs-1 opacity-50"><i class="fas fa-walking"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4" style="background: linear-gradient(135deg, #2ec4b6, #cbf3f0);">
                    <div class="card-body p-4 text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase fw-bold small mb-2" style="opacity: 0.8;">Total Today</h6>
                                <h2 class="fw-bold mb-0"><?= $today ?></h2>
                            </div>
                            <div class="fs-1 opacity-50"><i class="fas fa-user-check"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4" style="background: linear-gradient(135deg, #1d3557, #457b9d);">
                    <div class="card-body p-4 text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase fw-bold small mb-2" style="opacity: 0.8;">Total Records</h6>
                                <h2 class="fw-bold mb-0"><?= $total ?></h2>
                            </div>
                            <div class="fs-1 opacity-50"><i class="fas fa-database"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white py-3 border-0">
                        <h5 class="mb-0 fw-bold"><i class="fas fa-clock text-primary me-2"></i> Recent Visitor Activity</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Visitor Name</th>
                                        <th>Company / Purpose</th>
                                        <th>Host Employee / Dept</th>
                                        <th>Time IN</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $recent = $con->query("SELECT v.*, u.full_name as host_name, u.department, d.dept_color 
                                                          FROM visitor_master v 
                                                          LEFT JOIN users u ON v.employee_id = u.id 
                                                          LEFT JOIN departments d ON u.department = d.dept_name
                                                          ORDER BY v.id DESC LIMIT 10")->fetchAll();
                                    foreach($recent as $r):
                                        $status_class = $r['out_time'] ? 'bg-secondary' : 'bg-success';
                                        $status_text = $r['out_time'] ? 'Out' : 'Inside';
                                    ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold"><?= htmlspecialchars($r['visitor_name']) ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($r['pass_no']) ?></small>
                                        </td>
                                        <td>
                                            <div class="small fw-bold"><?= htmlspecialchars($r['company_name']) ?></div>
                                            <div class="small text-muted"><?= htmlspecialchars($r['purpose']) ?></div>
                                        </td>
                                        <td>
                                            <div class="small fw-bold"><?= htmlspecialchars($r['host_name'] ?: 'N/A') ?></div>
                                            <div class="badge fw-normal small" style="background: <?= !empty($r['dept_color']) ? $r['dept_color'] : '#f8f9fa' ?>; color: <?= !empty($r['dept_color']) ? '#fff' : '#212529' ?>;">
                                                <?= htmlspecialchars($r['department'] ?: 'N/A') ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small"><?= date("d M, Y", strtotime($r['in_time'])) ?></div>
                                            <div class="badge bg-light text-dark border"><?= date("h:i A", strtotime($r['in_time'])) ?></div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge <?= $status_class ?> rounded-pill px-3" style="font-size: 0.7rem;">
                                                <?= $status_text ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if(empty($recent)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">No recent activity found.</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-light border-0 py-3 text-center">
                        <a href="list_visitor.php" class="btn btn-primary btn-sm px-4 rounded-pill">
                            <i class="fas fa-list-ul me-2"></i> View Full Visitor Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("includes/footer.php"); ?>