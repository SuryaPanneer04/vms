<?php 
include("includes/config.php");
include("includes/header.php"); 
include("includes/sidebar.php"); 



// Quick Stats
$total = $con->query("SELECT COUNT(*) FROM visitor_master")->fetchColumn();
$inside = $con->query("SELECT COUNT(*) FROM visitor_master WHERE out_time IS NULL")->fetchColumn();
$today = $con->query("SELECT COUNT(*) FROM visitor_master WHERE DATE(in_time) = CURDATE()")->fetchColumn();
?>

<div class="content">
    <div class="row content-animate">
        <div class="col-12 mb-4">
            <h2 class="fw-bold">Welcome back,</h2>
            <p class="text-muted"> Control Panel & Visitor Analytics</p>
        </div>

        <div class="col-md-4">
            <div class="card bg-primary text-white border-0 shadow">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-uppercase mb-2" style="opacity: 0.8">Currently Inside</h6>
                            <h2 class="display-4 fw-bold mb-0"><?= $inside ?></h2>
                        </div>
                        <div class="fs-1"><i class="fas fa-door-open"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-success text-white border-0 shadow">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-uppercase mb-2" style="opacity: 0.8">Total Today</h6>
                            <h2 class="display-4 fw-bold mb-0"><?= $today ?></h2>
                        </div>
                        <div class="fs-1"><i class="fas fa-users"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-dark text-white border-0 shadow">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-uppercase mb-2" style="opacity: 0.8">Lifetime Entries</h6>
                            <h2 class="display-4 fw-bold mb-0"><?= $total ?></h2>
                        </div>
                        <div class="fs-1"><i class="fas fa-database"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8 mt-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold">Recent Activity</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Visitor</th>
                                    <th>Emp/Gate</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $recent = $con->query("SELECT v.*, e.emp_name FROM visitor_master v LEFT JOIN employee_master e ON v.employee_id = e.id ORDER BY v.id DESC LIMIT 5")->fetchAll();
                                foreach($recent as $r):
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($r['visitor_name']) ?></td>
                                    <td><?= htmlspecialchars($r['emp_name']) ?></td>
                                    <td><?= date("h:i A", strtotime($r['in_time'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white text-center">
                    <a href="list_visitor.php" class="btn btn-sm btn-link">View All Logs</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mt-4">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold">Quick Actions</h5>
                </div>
                <div class="card-body d-grid gap-2">
                    <a href="add_visitor_form.php" class="btn btn-primary py-3">
                        <i class="fas fa-plus-circle me-2"></i> New Gate Entry
                    </a>
                    <a href="list_visitor.php" class="btn btn-outline-dark py-3">
                        <i class="fas fa-sign-out-alt me-2"></i> Checkout Records
                    </a>
                    <button onclick="window.print()" class="btn btn-outline-info py-3">
                        <i class="fas fa-print me-2"></i> Print Shift Report
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("includes/footer.php"); ?>