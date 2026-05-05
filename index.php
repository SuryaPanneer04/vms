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
                                    <tr class="visitor-row" style="cursor: pointer;" data-id="<?= $r['id'] ?>" title="Click to view details">
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
                                            <?php 
                                                $display_time = !empty($r['in_time']) ? $r['in_time'] : $r['meeting_date_time'];
                                                if($display_time):
                                            ?>
                                                <div class="small"><?= date("d M, Y", strtotime($display_time)) ?></div>
                                                <div class="badge bg-light text-dark border">
                                                    <?= !empty($r['in_time']) ? '' : '<span class="text-info me-1">Sch:</span>' ?>
                                                    <?= date("h:i A", strtotime($display_time)) ?>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted small">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php 
                                                $is_expired = false;
                                                if($r['approval_status'] == 3) {
                                                    $scheduled_time = !empty($r['meeting_date_time']) ? strtotime($r['meeting_date_time']) : time();
                                                    if((time() - $scheduled_time) > (12 * 3600)) $is_expired = true;
                                                }

                                                if($is_expired): ?>
                                                    <span class="badge bg-danger rounded-pill px-3" style="font-size: 0.7rem;">Expired</span>
                                                <?php elseif($r['approval_status'] == 3): ?>
                                                    <span class="badge bg-info rounded-pill px-3" style="font-size: 0.7rem;">Scheduled</span>
                                                <?php elseif($r['out_time']): ?>
                                                    <span class="badge bg-secondary rounded-pill px-3" style="font-size: 0.7rem;">Out</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success rounded-pill px-3" style="font-size: 0.7rem;">Inside</span>
                                                <?php endif; ?>
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

<!-- VISITOR DETAILS MODAL -->
<div class="modal fade" id="visitorDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-primary text-white border-0 py-3">
                <h5 class="modal-title fw-bold"><i class="fas fa-id-card me-2"></i> Visitor Information</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" id="modalContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted small">Loading details...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const detailModal = new bootstrap.Modal(document.getElementById('visitorDetailModal'));
    const modalContent = document.getElementById('modalContent');

    document.querySelectorAll(".visitor-row").forEach(row => {
        row.addEventListener("click", function() {
            const id = this.dataset.id;
            modalContent.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted small">Loading details...</p></div>';
            detailModal.show();

            fetch(`get_visitor_details.php?id=${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === "success") {
                    const v = data.data;
                    const img = v.img_capture ? `uploads/${v.img_capture}` : 'assets/img/default-avatar.png';
                    
                    modalContent.innerHTML = `
                        <div class="p-4">
                            <div class="row g-4">
                                <div class="col-md-4 text-center border-end">
                                    <div class="position-relative d-inline-block mb-3">
                                        <img src="${img}" class="rounded-4 shadow-sm border" style="width: 150px; height: 150px; object-fit: cover;" onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(v.visitor_name)}&background=4361ee&color=fff&size=150'">
                                    </div>
                                    <h4 class="fw-bold mb-1">${v.visitor_name}</h4>
                                    <div class="badge bg-primary-subtle text-primary rounded-pill px-3 mb-2">${v.pass_no}</div>
                                    <div class="small text-muted mb-3"><i class="fas fa-building me-1"></i> ${v.company_name || 'Independent'}</div>
                                    <div class="d-grid gap-2">
                                        <a href="receipt.php?pass_no=${v.pass_no}" class="btn btn-outline-primary btn-sm rounded-pill"><i class="fas fa-print me-1"></i> Print Pass</a>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <label class="text-muted small fw-bold text-uppercase mb-1 d-block">Contact Info</label>
                                            <p class="mb-0 small fw-bold"><i class="fas fa-phone me-1 text-primary"></i> ${v.contact_no}</p>
                                            <p class="mb-0 small text-muted"><i class="fas fa-envelope me-1"></i> ${v.email || 'N/A'}</p>
                                        </div>
                                        <div class="col-6">
                                            <label class="text-muted small fw-bold text-uppercase mb-1 d-block">Purpose</label>
                                            <p class="mb-0 small fw-bold text-primary">${v.purpose}</p>
                                            <p class="mb-0 small text-muted">${v.visitor_type}</p>
                                        </div>
                                        <div class="col-12 border-top pt-3">
                                            <label class="text-muted small fw-bold text-uppercase mb-2 d-block">Meeting Details</label>
                                            <div class="p-3 bg-light rounded-3 d-flex align-items-center">
                                                <div class="bg-white rounded-circle p-2 me-3 shadow-sm">
                                                    <i class="fas fa-user-tie text-primary fa-lg"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-bold">${v.host_name || 'N/A'}</h6>
                                                    <small class="text-muted">${v.host_dept || 'N/A'} | ${v.host_desig || 'N/A'}</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 border-top pt-3">
                                            <div class="row text-center">
                                                <div class="col-4">
                                                    <label class="text-muted small fw-bold text-uppercase d-block mb-1">Check-IN</label>
                                                    <div class="small fw-bold text-success">${v.in_time ? new Date(v.in_time).toLocaleString() : 'N/A'}</div>
                                                </div>
                                                <div class="col-4">
                                                    <label class="text-muted small fw-bold text-uppercase d-block mb-1">M-End</label>
                                                    <div class="small fw-bold text-primary">${v.meeting_out_time ? new Date(v.meeting_out_time).toLocaleString() : 'Ongoing'}</div>
                                                </div>
                                                <div class="col-4">
                                                    <label class="text-muted small fw-bold text-uppercase d-block mb-1">Check-OUT</label>
                                                    <div class="small fw-bold text-danger">${v.out_time ? new Date(v.out_time).toLocaleString() : 'Not Out'}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                } else {
                    modalContent.innerHTML = `<div class="alert alert-danger m-4">${data.message}</div>`;
                }
            })
            .catch(err => {
                modalContent.innerHTML = `<div class="alert alert-danger m-4">Failed to fetch data.</div>`;
            });
        });
    });
});
</script>

<?php include("includes/footer.php"); ?>