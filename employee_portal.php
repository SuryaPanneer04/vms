<?php
include("includes/config.php");
include("includes/header.php");
include("includes/sidebar.php");

// In a real system, you'd get the logged-in employee's ID from session.
// For now, let's assume we are viewing for Employee ID 1 (or allow selection for demo)
$emp_id = $_GET['emp_id'] ?? 42; 

// Fetch current employee info
$emp_stmt = $con->prepare("SELECT * FROM employee_master WHERE id = ?");
$emp_stmt->execute([$emp_id]);
$current_emp = $emp_stmt->fetch(PDO::FETCH_ASSOC);

// Fetch visitors assigned to THIS employee
$query = "SELECT * FROM visitor_master WHERE employee_id = ? AND approval_status = 1 ORDER BY id DESC";
$stmt = $con->prepare($query);
$stmt->execute([$emp_id]);
$my_visitors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pagination Setup
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Count total visitors for THIS employee
$count_stmt = $con->prepare("SELECT COUNT(*) FROM visitor_master WHERE employee_id = ? AND approval_status = 1");
$count_stmt->execute([$emp_id]);
$total_records = $count_stmt->fetchColumn();
$total_pages = ceil($total_records / $limit);

// Fetch visitors assigned to THIS employee with LIMIT
$query = "SELECT * FROM visitor_master WHERE employee_id = ? AND approval_status = 1 ORDER BY id DESC LIMIT $limit OFFSET $offset";
$stmt = $con->prepare($query);
$stmt->execute([$emp_id]);
$my_visitors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Summary stats for this employee
$active_count = 0;
foreach($my_visitors as $v) if(empty($v['out_time'])) $active_count++;
$total_count = $total_records; // use total for stats card
?>

<div class="content">
    <div class="container-fluid content-animate">
        <div class="row mb-4 align-items-end">
            <div class="col-lg-8 mb-3 mb-lg-0">
                <h2 class="fw-bold mb-1 text-primary"><i class="fas fa-user-shield me-2"></i> Employee Portal</h2>
                <p class="text-muted mb-0">Welcome back, <strong><?= htmlspecialchars($current_emp['emp_name'] ?? 'Employee') ?></strong>. Here are your assigned visitors.</p>
            </div>
            <div class="col-lg-4">
                <div class="row g-2 justify-content-lg-end">
                    <div class="col-6 col-md-auto">
                        <div class="card border-0 shadow-sm px-3 py-2 bg-success bg-opacity-10 text-center text-md-start">
                            <small class="text-success fw-bold text-uppercase d-block mb-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">Active Now</small>
                            <h4 class="mb-0 text-success fw-bold"><?= $active_count ?></h4>
                        </div>
                    </div>
                    <div class="col-6 col-md-auto">
                        <div class="card border-0 shadow-sm px-3 py-2 bg-primary bg-opacity-10 text-center text-md-start">
                            <small class="text-primary fw-bold text-uppercase d-block mb-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">Total Visits</small>
                            <h4 class="mb-0 text-primary fw-bold"><?= $total_count ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow border-0 overflow-hidden">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                <h5 class="mb-0 fw-bold">Visitor Log & Meeting Control</h5>
                <span class="small text-muted fst-italic"><i class="fas fa-info-circle me-1"></i> Data refreshes in real-time</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-uppercase small fw-bold">
                            <tr>
                                <th class="ps-4">Visitor & Pass</th>
                                <th>Contact / ID</th>
                                <th>Assets & Logistics</th>
                                <th>Timing</th>
                                <th>Status</th> 
                                <th class="text-center pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($my_visitors as $v): 
                                $has_id = !empty($v['id_upload']);
                            ?>
                            <tr class="<?= empty($v['out_time']) ? 'bg-light bg-opacity-25' : '' ?>">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-3 bg-primary text-white">
                                            <?= strtoupper(substr($v['visitor_name'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold"><?= htmlspecialchars($v['visitor_name']) ?></div>
                                            <div class="small text-muted"><i class="fas fa-envelope me-1"></i> <?= htmlspecialchars($v['email'] ?: 'No Email') ?></div>
                                            <div class="small bg-primary bg-opacity-10 text-primary px-2 rounded-pill d-inline-block mt-1">#<?= $v['pass_no'] ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="small fw-bold text-dark"><i class="fas fa-user-tag me-1 text-primary"></i> Meeting with You</div>
                                    <div class="small mt-1"><i class="fas fa-phone-alt me-1 text-muted"></i> <?= htmlspecialchars($v['contact_no']) ?></div>
                                </td>
                                <td>
                                    <div class="d-flex gap-1 flex-wrap">
                                        <?php if($v['laptop_count'] > 0): ?>
                                            <span class="badge bg-light text-dark border small"><i class="fas fa-laptop me-1"></i> <?= $v['laptop_count'] ?></span>
                                        <?php endif; ?>
                                        <?php if($v['mobile_count'] > 0): ?>
                                            <span class="badge bg-light text-dark border small"><i class="fas fa-mobile-alt me-1"></i> <?= $v['mobile_count'] ?></span>
                                        <?php endif; ?>
                                        <?php if(!empty($v['vehicle_number'])): ?>
                                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 small"><i class="fas fa-car me-1"></i> <?= $v['vehicle_number'] ?></span>
                                        <?php endif; ?>
                                        <?php if(empty($v['laptop_count']) && empty($v['mobile_count']) && empty($v['vehicle_number'])): ?>
                                            <span class="text-muted small italic">No Assets</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="small text-success"><i class="fas fa-sign-in-alt me-1"></i> <?= date("h:i A", strtotime($v['in_time'])) ?></div>
                                        <?php if(!empty($v['meeting_out_time'])): ?>
                                            <div class="small text-danger mt-1">
                                                <i class="fas fa-sign-out-alt me-1"></i> 
                                                <?= date("h:i A", strtotime($v['meeting_out_time'])) ?>
                                            </div>
                                        <?php endif; ?>
                                </td>
                                <td>
                                    <?php if(empty($v['meeting_out_time'])): ?>
                                        <span class="pulsing-dot me-1"></span>
                                        <span class="text-success small fw-bold">In Meeting</span>
                                    <?php else: ?>
                                        <span class="text-muted small text-decoration-line-through">Completed</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center pe-4">
                                    <div class="dropdown">
                                        <button class="btn btn-light btn-sm rounded-circle" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v text-muted"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                            <?php if(empty($v['out_time'])): ?>
                                                <li><a class="dropdown-item text-danger fw-bold endMeetingBtn" href="#" 
                                                    data-pass="<?= $v['pass_no'] ?>" data-name="<?= htmlspecialchars($v['visitor_name']) ?>">
                                                    <i class="fas fa-stopwatch me-2"></i> End Meeting
                                                </a></li>
                                            <?php endif; ?>
                                            <li><a class="dropdown-item" href="receipt.php?pass_no=<?= $v['pass_no'] ?>">
                                                <i class="fas fa-print me-2"></i> View Full Pass
                                            </a></li>
                                            <?php if($has_id): ?>
                                                <li><a class="dropdown-item" href="uploads/<?= $v['id_upload'] ?>" target="_blank">
                                                    <i class="fas fa-image me-2"></i> View ID Copy
                                                </a></li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <?php if(empty($my_visitors)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-user-clock fa-3x mb-3 opacity-25"></i>
                                        <p>No visitors assigned to you yet.</p>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- PAGINATION CONTROLS -->
            <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center py-3">
                <div class="text-muted small">
                    Showing <?= count($my_visitors) ?> of <?= $total_records ?> meetings
                </div>
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page-1 ?>&emp_id=<?= $emp_id ?>"><i class="fas fa-chevron-left"></i></a>
                        </li>
                        
                        <?php 
                        // Show up to 5 page numbers for space efficiency
                        $start_loop = max(1, $page - 2);
                        $end_loop = min($total_pages, $page + 2);
                        
                        for($i = $start_loop; $i <= $end_loop; $i++): 
                        ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>&emp_id=<?= $emp_id ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page+1 ?>&emp_id=<?= $emp_id ?>"><i class="fas fa-chevron-right"></i></a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.9rem;
}

.pulsing-dot {
    width: 8px;
    height: 8px;
    background: #28a745;
    border-radius: 50%;
    display: inline-block;
    animation: pulse 1.5s infinite;
}

@keyframes pulse {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(40, 167, 69, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(40, 167, 69, 0); }
}

.dropdown-item { padding: 8px 20px; font-size: 0.9rem; }
.bg-opacity-10 { --bs-bg-opacity: 0.1; }
</style>

<!-- END MEETING MODAL -->
<div class="modal fade" id="endMeetingModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="endMeetingForm" class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold">Confirm Meeting End</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <i class="fas fa-door-closed fa-3x text-danger mb-3"></i>
                <h5 class="mb-2">End meeting with <span id="modalVisitorName" class="text-primary fw-bold"></span>?</h5>
                <p class="text-muted small">This will record the current time as the official Check-Out time.</p>
                <input type="hidden" name="pass_no" id="modalPassNo">
                <input type="hidden" name="meeting_out_time" id="modalOutTime">
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger px-4 shadow">Confirm & End Visit</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const endBtns = document.querySelectorAll(".endMeetingBtn");
    const modal = new bootstrap.Modal(document.getElementById('endMeetingModal'));
    const form = document.getElementById('endMeetingForm');

    endBtns.forEach(btn => {
        btn.addEventListener("click", function(e) {
            e.preventDefault();
            document.getElementById('modalPassNo').value = this.dataset.pass;
            document.getElementById('modalVisitorName').innerText = this.dataset.name;
            
            // Set current time for checkout
            let now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            document.getElementById("modalOutTime").value = now.toISOString().slice(0, 16);
            
            modal.show();
        });
    });

    form.addEventListener("submit", function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch("meeting_checkout_visitor.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status == "success") {
                location.reload();
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(err => {
            alert("Error connecting to server. Please try again.");
        });
    });
});
</script>

<?php include("includes/footer.php"); ?>
