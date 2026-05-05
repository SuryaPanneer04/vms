<?php
include("includes/config.php");

// Restrict access to timeoffice role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'timeoffice') {
    header("Location: login.php");
    exit();
}

include("includes/header.php");
include("includes/sidebar.php");

// Fetch Metrics
$total_visitors = $con->query("SELECT COUNT(*) FROM visitor_master")->fetchColumn();
$male_visitors = $con->query("SELECT COUNT(*) FROM visitor_master WHERE gender = 'Male'")->fetchColumn();
$female_visitors = $con->query("SELECT COUNT(*) FROM visitor_master WHERE gender = 'Female'")->fetchColumn();

// Fetch Recent Visitors
$stmt = $con->query("
    SELECT v.*, e.full_name as host_name 
    FROM visitor_master v 
    LEFT JOIN users e ON v.employee_id = e.id 
    ORDER BY v.id DESC 
    LIMIT 20
");
$recent_visitors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content">
    <div class="container-fluid">
        <!-- Dashboard Header -->
        <div class="row mb-4 content-animate">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between bg-white p-4 rounded-4 shadow-sm border">
                    <div>
                        <h3 class="fw-bold text-dark mb-1">Time Office Dashboard</h3>
                        <p class="text-muted mb-0">Overview of visitor traffic and demographics</p>
                    </div>
                    <div class="d-none d-md-block">
                        <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill">
                            <i class="fas fa-calendar-alt me-2"></i><?= date('d M, Y') ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="row g-4 mb-5 content-animate">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 card-hover-up" style="background: linear-gradient(135deg, #4361ee 0%, #3f37c9 100%);">
                    <div class="card-body p-4 text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 fw-bold text-uppercase mb-2">Total Visitors</h6>
                                <h2 class="fw-bold mb-0"><?= number_format($total_visitors) ?></h2>
                            </div>
                            <div class="bg-white bg-opacity-20 p-3 rounded-4">
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 card-hover-up" style="background: linear-gradient(135deg, #4cc9f0 0%, #4361ee 100%);">
                    <div class="card-body p-4 text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 fw-bold text-uppercase mb-2">Male Visitors</h6>
                                <h2 class="fw-bold mb-0"><?= number_format($male_visitors) ?></h2>
                            </div>
                            <div class="bg-white bg-opacity-20 p-3 rounded-4">
                                <i class="fas fa-mars fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 card-hover-up" style="background: linear-gradient(135deg, #f72585 0%, #b5179e 100%);">
                    <div class="card-body p-4 text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 fw-bold text-uppercase mb-2">Female Visitors</h6>
                                <h2 class="fw-bold mb-0"><?= number_format($female_visitors) ?></h2>
                            </div>
                            <div class="bg-white bg-opacity-20 p-3 rounded-4">
                                <i class="fas fa-venus fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Visitor List Table -->
        <div class="row content-animate">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0"><i class="fas fa-history me-2 text-primary"></i>Recent Visitors</h5>
                        <a href="list_visitor.php" class="btn btn-sm btn-outline-primary rounded-pill px-3"><i class="fas fa-list me-1 small"></i>View All</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Visitor</th>
                                        <th>Gender</th>
                                        <th>Host / Meeting With</th>
                                        <th>Check-in Time</th>
                                        <th>Status</th>
                                        <th class="text-center pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_visitors as $v): ?>
                                    <tr class="visitor-row" style="cursor: pointer;" onclick="showVisitorDetails(<?= $v['id'] ?>)">
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-3 bg-soft-primary text-primary rounded-circle overflow-hidden d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px; min-width: 40px;">
                                                    <?php if(!empty($v['img_capture']) && file_exists("uploads/" . $v['img_capture'])): ?>
                                                        <img src="uploads/<?= htmlspecialchars($v['img_capture']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                                    <?php else: ?>
                                                        <?= strtoupper(substr($v['visitor_name'], 0, 1)) ?>
                                                    <?php endif; ?>
                                                </div>
                                                <div>
                                                    <div class="fw-bold"><?= htmlspecialchars($v['visitor_name']) ?></div>
                                                    <small class="text-muted"><i class="fas fa-building me-1 small"></i><?= htmlspecialchars($v['company_name'] ?: 'No Company') ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if($v['gender'] == 'Male'): ?>
                                                <span class="badge bg-soft-info text-info"><i class="fas fa-mars me-1"></i> Male</span>
                                            <?php elseif($v['gender'] == 'Female'): ?>
                                                <span class="badge bg-soft-danger text-danger"><i class="fas fa-venus me-1"></i> Female</span>
                                            <?php else: ?>
                                                <span class="badge bg-soft-secondary text-secondary"><i class="fas fa-genderless me-1"></i> Other</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($v['host_name'] ?: 'Pending') ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($v['purpose']) ?></small>
                                        </td>
                                        <td>
                                            <div class="small fw-bold"><?= date('d M, h:i A', strtotime($v['in_time'])) ?></div>
                                        </td>
                                        <td>
                                            <?php if($v['approval_status'] == 1): ?>
                                                <span class="badge bg-soft-success text-success">Approved</span>
                                            <?php elseif($v['approval_status'] == 2): ?>
                                                <span class="badge bg-soft-danger text-danger">Rejected</span>
                                            <?php elseif($v['approval_status'] == 3): ?>
                                                <span class="badge bg-soft-warning text-warning">Scheduled</span>
                                            <?php else: ?>
                                                <span class="badge bg-soft-primary text-primary">Pending</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center pe-4">
                                            <button class="btn btn-sm btn-light btn-round" onclick="event.stopPropagation(); showVisitorDetails(<?= $v['id'] ?>)">
                                                <i class="fas fa-eye text-primary"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if(empty($recent_visitors)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">No visitors recorded yet</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Visitor Details Modal -->
<div class="modal fade" id="visitorDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold" id="modalVisitorName"><i class="fas fa-user-circle me-2 text-primary"></i>Visitor Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="modalBodyContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card-hover-up { transition: transform 0.3s ease, box-shadow 0.3s ease; }
    .card-hover-up:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
    .bg-soft-primary { background-color: rgba(67, 97, 238, 0.1); }
    .bg-soft-success { background-color: rgba(76, 201, 240, 0.1); }
    .bg-soft-danger { background-color: rgba(247, 37, 133, 0.1); }
    .bg-soft-warning { background-color: rgba(255, 159, 64, 0.1); }
    .bg-soft-info { background-color: rgba(0, 184, 212, 0.1); }
    .bg-soft-secondary { background-color: rgba(108, 117, 125, 0.1); }
    .visitor-row:hover { background-color: rgba(67, 97, 238, 0.02) !important; }
    .btn-round { border-radius: 50%; width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; }
</style>

<script>
function showVisitorDetails(id) {
    const modal = new bootstrap.Modal(document.getElementById('visitorDetailsModal'));
    const modalBody = document.getElementById('modalBodyContent');
    const modalTitle = document.getElementById('modalVisitorName');
    
    modalBody.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>';
    modal.show();

    fetch('get_visitor_details.php?id=' + id)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const v = data.data;
                modalTitle.innerText = v.visitor_name + "'s Full Profile";
                
                let devicesHtml = '';
                if(v.devices && v.devices.trim() !== '') {
                    const devices = v.devices.split(',');
                    devices.forEach(d => {
                        let count = 0;
                        let icon = 'fas fa-tag';
                        let deviceName = d.trim();
                        
                        if(deviceName === 'Laptop') { count = v.laptop_count; icon = 'fas fa-laptop'; }
                        else if(deviceName === 'Mobile') { count = v.mobile_count; icon = 'fas fa-mobile-alt'; }
                        else if(deviceName === 'Disc') { count = v.disc_count; icon = 'fas fa-compact-disc'; }
                        else if(deviceName === 'Charger') { count = v.charger_count; icon = 'fas fa-plug'; }
                        
                        devicesHtml += `<span class="badge bg-light text-dark border me-2 mb-2 p-2 shadow-sm"><i class="${icon} me-1 text-primary"></i> ${deviceName} (${count})</span>`;
                    });
                } else {
                    devicesHtml = '<span class="text-muted small italic">No assets declared for this visit</span>';
                }

                modalBody.innerHTML = `
                    <div class="row g-4">
                        <div class="col-md-4 text-center">
                            <div class="mb-3">
                                ${v.img_capture ? 
                                    `<img src="uploads/${v.img_capture}" class="img-fluid rounded-4 shadow-sm border" style="max-height: 200px; width: 100%; object-fit: cover;">` : 
                                    `<div class="bg-light rounded-4 d-flex align-items-center justify-content-center" style="height: 200px;"><i class="fas fa-user fa-4x text-muted opacity-25"></i></div>`
                                }
                            </div>
                            <div class="badge bg-primary px-3 py-2 rounded-pill mb-2">${v.pass_no}</div>
                            <div class="d-block">
                                <span class="badge bg-soft-info text-info px-3 py-2 rounded-pill"><i class="fas fa-venus-mars me-1"></i> ${v.gender}</span>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="text-muted small fw-bold text-uppercase">Contact</label>
                                    <div class="fw-bold"><i class="fas fa-phone-alt me-1 text-primary small"></i> ${v.contact_no}</div>
                                </div>
                                <div class="col-6">
                                    <label class="text-muted small fw-bold text-uppercase">Email</label>
                                    <div class="fw-bold text-truncate"><i class="fas fa-envelope me-1 text-primary small"></i> ${v.email || 'N/A'}</div>
                                </div>
                                <div class="col-6">
                                    <label class="text-muted small fw-bold text-uppercase">Company</label>
                                    <div class="fw-bold"><i class="fas fa-building me-1 text-primary small"></i> ${v.company_name || 'Individual'}</div>
                                </div>
                                <div class="col-6">
                                    <label class="text-muted small fw-bold text-uppercase">ID Type</label>
                                    <div class="fw-bold"><i class="fas fa-id-card me-1 text-primary small"></i> ${v.id_type}</div>
                                </div>
                                <div class="col-12 border-top pt-3">
                                    <h6 class="fw-bold mb-2"><i class="fas fa-info-circle me-1 text-primary"></i> Meeting Details</h6>
                                    <div class="bg-light p-3 rounded-3 border">
                                        <div class="row">
                                            <div class="col-6">
                                                <small class="text-muted d-block">Meeting With</small>
                                                <span class="fw-bold"><i class="fas fa-user-tie me-1 text-primary small"></i> ${v.host_name}</span>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted d-block">Department</small>
                                                <span class="badge bg-white text-dark border"><i class="fas fa-sitemap me-1 text-primary small"></i> ${v.host_dept}</span>
                                            </div>
                                            <div class="col-12 mt-2">
                                                <small class="text-muted d-block">Purpose</small>
                                                <p class="mb-0 small fw-medium text-dark"><i class="fas fa-comment-alt me-1 text-primary small"></i> ${v.purpose}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 pt-2">
                                    <h6 class="fw-bold mb-2"><i class="fas fa-briefcase me-1 text-primary"></i> Assets Brought</h6>
                                    <div class="d-flex flex-wrap">
                                        ${devicesHtml}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                modalBody.innerHTML = '<div class="alert alert-danger">Error: ' + data.message + '</div>';
            }
        })
        .catch(error => {
            modalBody.innerHTML = '<div class="alert alert-danger">An error occurred while fetching details.</div>';
        });
}
</script>

<?php include("includes/footer.php"); ?>
