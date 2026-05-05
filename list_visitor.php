<?php include("includes/config.php"); ?>
<?php
// Access Control: Only admin and timeoffice/gate can access this log
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'timeoffice' && $_SESSION['role'] !== 'gate') {
    header("Location: employee_portal.php");
    exit();
}

$where = [];
$params = [];

/* FILTERS */
if(!empty($_GET['from_date']) && !empty($_GET['to_date'])){
    $where[] = "DATE(v.in_time) BETWEEN ? AND ?";
    $params[] = $_GET['from_date'];
    $params[] = $_GET['to_date'];
}

if(!empty($_GET['pass_no'])){
    $where[] = "pass_no LIKE ?";
    $params[] = "%".$_GET['pass_no']."%";
}

if(!empty($_GET['visitor_name'])){
    $where[] = "v.visitor_name LIKE ?";
    $params[] = "%" . $_GET['visitor_name'] . "%";
}

if(!empty($_GET['contact_no'])){
    $where[] = "v.contact_no LIKE ?";
    $params[] = "%" . $_GET['contact_no'] . "%";
}

// Pagination Setup
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$pass_no = $_GET['pass_no'] ?? '';
$contact_no = $_GET['contact_no'] ?? '';
$from_date = $_GET['from_date'] ?? '';
$to_date = $_GET['to_date'] ?? '';

// Filter setup (Total count for pagination)
$count_query = "SELECT COUNT(*) FROM visitor_master WHERE 1=1";
$params = [];

if($pass_no) { $count_query .= " AND pass_no LIKE ?"; $params[] = "%$pass_no%"; }
if($contact_no) { $count_query .= " AND contact_no LIKE ?"; $params[] = "%$contact_no%"; }
if($from_date) { $count_query .= " AND (DATE(in_time) >= ? OR DATE(meeting_date_time) >= ?)"; $params[] = $from_date; $params[] = $from_date; }
if($to_date) { $count_query .= " AND (DATE(in_time) <= ? OR DATE(meeting_date_time) <= ?)"; $params[] = $to_date; $params[] = $to_date; }

$total_stmt = $con->prepare($count_query);
$total_stmt->execute($params);
$total_records = $total_stmt->fetchColumn();
$total_pages = ceil($total_records / $limit);

// Fetch visitors with LIMIT
$query = "SELECT v.*, 
          orig_e.full_name AS first_host_name, orig_e.department AS first_host_dept,
          curr_e.full_name AS curr_host_name, curr_e.department AS curr_host_dept,
          d.dept_color, 
          c.full_name AS checkin_staff, co.full_name AS checkout_staff,
          creator.full_name AS scheduled_by_name
          FROM visitor_master v 
          /* Join for the First Host (Original Meeting) */
          LEFT JOIN (
              SELECT visitor_id, emp_id, MIN(id) as first_id 
              FROM visitor_handoffs 
              GROUP BY visitor_id
          ) fh ON v.id = fh.visitor_id
          LEFT JOIN users orig_e ON fh.emp_id = orig_e.id
          
          /* Join for Current Host (Last Handoff) */
          LEFT JOIN users curr_e ON v.employee_id = curr_e.id
          
          LEFT JOIN departments d ON orig_e.department = d.dept_name
          LEFT JOIN users c ON v.checkin_by = c.id
          LEFT JOIN users co ON v.checkout_by = co.id
          LEFT JOIN users creator ON v.checkin_by = creator.id
          WHERE 1=1";

$final_params = [];
if($pass_no) { $query .= " AND v.pass_no LIKE ?"; $final_params[] = "%$pass_no%"; }
if($contact_no) { $query .= " AND v.contact_no LIKE ?"; $final_params[] = "%$contact_no%"; }
if($from_date) { $query .= " AND (DATE(v.in_time) >= ? OR DATE(v.meeting_date_time) >= ?)"; $final_params[] = $from_date; $final_params[] = $from_date; }
if($to_date) { $query .= " AND (DATE(v.in_time) <= ? OR DATE(v.meeting_date_time) <= ?)"; $final_params[] = $to_date; $final_params[] = $to_date; }

$query .= " ORDER BY v.id DESC LIMIT $limit OFFSET $offset";

$stmt = $con->prepare($query);
$stmt->execute($final_params);
$visitors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include("includes/header.php"); ?>
<?php include("includes/sidebar.php"); ?>

<div class="content">
    <div class="content-animate">
        <!-- ================= FILTER ================= -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-primary-subtle text-primary rounded-3 p-2 me-3">
                    <i class="fas fa-filter fs-5"></i>
                </div>
                <h5 class="fw-bold mb-0">Search & Filter</h5>
            </div>
            <form method="GET">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">PASS NUMBER</label>
                        <input type="text" name="pass_no" id="filter_pass_no" class="form-control border-0 bg-light py-2" placeholder="Ex: VMS_123" value="<?= htmlspecialchars($_GET['pass_no'] ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">CONTACT NO / NAME</label>
                        <input type="text" name="contact_no" class="form-control border-0 bg-light py-2" placeholder="Search visitor..." value="<?= htmlspecialchars($_GET['contact_no'] ?? '') ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">FROM DATE</label>
                        <input type="date" name="from_date" class="form-control border-0 bg-light py-2" value="<?= $_GET['from_date'] ?? '' ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">TO DATE</label>
                        <input type="date" name="to_date" class="form-control border-0 bg-light py-2" value="<?= $_GET['to_date'] ?? '' ?>">
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button class="btn btn-primary flex-grow-1 rounded-3 py-2 shadow-sm"><i class="fas fa-search me-1"></i> Apply Filters</button>
                        <a href="list_visitor.php" class="btn btn-light rounded-3 py-2 border"><i class="fas fa-sync-alt"></i></a>
                    </div>
                </div>
            </form>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center p-4">
                <div>
                    <h4 class="fw-bold mb-0 text-dark"><i class="fas fa-clipboard-list text-primary me-2"></i> Visitor Entry Log</h4>
                    <p class="text-muted small mb-0 mt-1">Real-time tracking of all visitor movements and approvals.</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="getPrint()"><i class="fas fa-print me-1"></i> Print</button>
                    <button class="btn btn-outline-success btn-sm rounded-pill px-3" onclick="exportExcel()"><i class="fas fa-file-excel me-1"></i> Export</button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3 text-muted small fw-bold">VISITOR</th>
                                <th class="py-3 text-muted small fw-bold">HOST</th>
                                <?php if($_SESSION['role'] === 'admin'): ?>
                                    <!-- <th class="py-3 text-muted small fw-bold">SCHEDULE INFO</th> -->
                                <?php endif; ?>
                                <th class="py-3 text-muted small fw-bold">TIMESTAMPS</th>
                                <th class="py-3 text-muted small fw-bold">STATUS</th>
                                <?php if($_SESSION['role'] === 'admin'): ?>
                                    <th class="py-3 text-muted small fw-bold">STAFF DETAILS</th>
                                <?php endif; ?>
                                <th class="text-center py-3 text-muted small fw-bold pe-4">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($visitors as $row): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary-subtle text-primary rounded-circle overflow-hidden me-3 d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px; min-width: 40px;">
                                            <?php if(!empty($row['img_capture']) && file_exists("uploads/" . $row['img_capture'])): ?>
                                                <img src="uploads/<?= htmlspecialchars($row['img_capture']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                            <?php else: ?>
                                                <i class="fas fa-user"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($row['visitor_name']) ?></div>
                                            <div class="small text-muted mb-1">
                                                <span class="badge bg-light text-dark border-0 rounded-pill me-1"><?= htmlspecialchars($row['pass_no'] ?: 'NULL') ?></span> 
                                                <?= htmlspecialchars($row['visitor_type']) ?>
                                            </div>
                                            <div class="small text-muted"><i class="fas fa-building me-1"></i> <?= htmlspecialchars($row['company_name'] ?: 'Independent') ?></div>
                                            <div class="small text-primary mt-1 fw-medium"><i class="fas fa-comment-dots me-1"></i> <?= htmlspecialchars($row['purpose']) ?></div>
                                            <div class="small text-muted mt-1"><i class="fas fa-phone-alt small me-1"></i> <?= htmlspecialchars($row['contact_no']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php 
                                        $host_name = $row['first_host_name'] ?: ($row['curr_host_name'] ?: 'Not Assigned');
                                        $host_dept = $row['first_host_dept'] ?: ($row['curr_host_dept'] ?: '-');
                                    ?>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($host_name) ?></div>
                                    <div class="small text-muted"><?= htmlspecialchars($host_dept) ?></div>
                                </td>
                                <?php if($_SESSION['role'] === 'admin'): ?>
                                <!-- <td>
                                    <?php if($row['approval_status'] == 3): ?>
                                        <div class="small">
                                            <span class="text-muted small d-block mb-1 text-uppercase fw-bold" style="font-size: 0.65rem;">Scheduled Info</span>
                                            <span class="fw-bold text-primary"><i class="fas fa-user-check me-1"></i> By: <?= htmlspecialchars($row['scheduled_by_name'] ?: 'Staff') ?></span>
                                        </div>
                                    <?php else: ?>
                                        <span class="badge bg-light text-muted fw-normal border rounded-pill px-3 py-2"><i class="fas fa-walking me-1"></i>Direct Visit</span>
                                    <?php endif; ?>
                                </td> -->
                                <?php endif; ?>
                                <td>
                                    <?php if(!empty($row['meeting_date_time'])): ?>
                                    <div class="small mb-1">
                                        <i class="fas fa-calendar-check text-info me-1" style="width: 14px;"></i> 
                                        <span class="text-muted" style="display:inline-block; width: 60px;">Sch:</span> <span class="fw-medium"><?= date("d M, h:i A", strtotime($row['meeting_date_time'])) ?></span>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if(!empty($row['in_time'])): ?>
                                    <div class="small mb-1">
                                        <i class="fas fa-sign-in-alt text-success me-1" style="width: 14px;"></i> 
                                        <span class="text-muted" style="display:inline-block; width: 60px;">In:</span> <span class="fw-medium"><?= date("d M, h:i A", strtotime($row['in_time'])) ?></span>
                                    </div>
                                    <?php endif; ?>

                                    <?php if(!empty($row['meeting_out_time'])): ?>
                                    <div class="small mb-1 text-primary">
                                        <i class="fas fa-stopwatch me-1" style="width: 14px;"></i> 
                                        <span class="text-muted" style="display:inline-block; width: 60px;">M-End:</span> <span class="fw-medium"><?= date("d M, h:i A", strtotime($row['meeting_out_time'])) ?></span>
                                    </div>
                                    <?php endif; ?>

                                    <?php if(!empty($row['out_time'])): ?>
                                    <div class="small text-danger">
                                        <i class="fas fa-sign-out-alt me-1" style="width: 14px;"></i> 
                                        <span class="text-muted" style="display:inline-block; width: 60px;">Out:</span> <span class="fw-medium"><?= date("d M, h:i A", strtotime($row['out_time'])) ?></span>
                                    </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                        $is_expired = false;
                                        if($row['approval_status'] == 3) {
                                            $scheduled_time = !empty($row['meeting_date_time']) ? strtotime($row['meeting_date_time']) : time();
                                            if((time() - $scheduled_time) > (12 * 3600)) $is_expired = true;
                                        }
                                    ?>
                                    <?php 
                                        if($is_expired): ?>
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3">Expired</span>
                                        <?php elseif(!empty($row['out_time'])): ?>
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3">Exited</span>
                                        <?php else: ?>
                                            <?php if($row['approval_status'] == 0): ?>
                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3">Pending</span>
                                            <?php elseif($row['approval_status'] == 1): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3">Approved</span>
                                            <?php elseif($row['approval_status'] == 2): ?>
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3">Rejected</span>
                                            <?php elseif($row['approval_status'] == 3): ?>
                                                <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-3">Scheduled</span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                </td>
                                <?php if($_SESSION['role'] === 'admin'): ?>
                                <td>
                                    <?php if(!empty($row['in_time'])): ?>
                                        <div class="small mb-1">
                                            <span class="text-muted">In By:</span> <?= htmlspecialchars($row['checkin_staff'] ?: '—') ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if(!empty($row['out_time'])): ?>
                                        <div class="small">
                                            <span class="text-muted">Out By:</span> <?= htmlspecialchars($row['checkout_staff'] ?: '—') ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if(empty($row['in_time'])): ?>
                                        <span class="text-muted small italic">Awaiting Gate Entry</span>
                                    <?php endif; ?>
                                </td>
                                <?php endif; ?>
                                <td class="pe-4">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <?php if($row['approval_status'] == 1): ?>
                                            <a href="receipt.php?pass_no=<?= urlencode($row['pass_no']) ?>" class="btn btn-light btn-sm text-primary shadow-sm" title="Print Pass"><i class="fas fa-print"></i></a>
                                            <button class="btn btn-light btn-sm text-info shadow-sm viewTimelineBtn" data-id="<?= $row['id'] ?>" data-name="<?= htmlspecialchars($row['visitor_name']) ?>" title="History"><i class="fas fa-clock-rotate-left"></i></button>
                                            <?php if(empty($row['out_time']) && $_SESSION['role'] === 'timeoffice'): ?>
                                                <button class="btn btn-danger btn-sm shadow checkoutBtn" data-pass="<?= $row['pass_no'] ?>" title="Checkout"><i class="fas fa-sign-out-alt"></i></button>
                                            <?php endif; ?>
                                        <?php elseif($row['approval_status'] == 3 && $_SESSION['role'] === 'timeoffice' && !$is_expired): ?>
                                            <a href="add_visitor_form.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">Check-IN</a>
                                        <?php else: ?>
                                            <span class="text-muted small">N/A</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($visitors)): ?>
                            <tr>
                                    <p class="text-muted">No visitor records found for the selected criteria.</p>
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
                    Showing <?= count($visitors) ?> of <?= $total_records ?> entries
                </div>
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page-1 ?>&pass_no=<?= $pass_no ?>&visitor_name=<?= $visitor_name ?>&from_date=<?= $from_date ?>&to_date=<?= $to_date ?>"><i class="fas fa-chevron-left"></i></a>
                        </li>
                        
                        <?php for($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>&pass_no=<?= $pass_no ?>&visitor_name=<?= $visitor_name ?>&from_date=<?= $from_date ?>&to_date=<?= $to_date ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page+1 ?>&pass_no=<?= $pass_no ?>&visitor_name=<?= $visitor_name ?>&from_date=<?= $from_date ?>&to_date=<?= $to_date ?>"><i class="fas fa-chevron-right"></i></a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- TIMELINE MODAL -->
<div class="modal fade" id="timelineModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title fw-bold"><i class="fas fa-clock-rotate-left me-2"></i> Meeting Timeline: <span id="timelineVisitorName"></span></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <div id="timelineContent">
            <!-- Dynamic content -->
        </div>
      </div>
    </div>
  </div>
</div>

<!-- CHECKOUT MODAL -->
<div class="modal fade" id="checkoutModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="checkoutForm" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Visitor Checkout</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="pass_no" id="modal_pass_no">
        <label>Check-out Time</label>
        <input type="datetime-local" class="form-control bg-light" name="out_time" id="out_time" readonly>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-danger">Confirm Checkout</button>
      </div>
    </form>
  </div>
</div>
<script>

function getPrint(){
    let printContent = document.querySelector(".table-responsive").innerHTML;
    let win = window.open("");

    win.document.write(`
        <html>
        <head>
            <title>Visitor List Print</title>
            <style>
                table { width:100%; border-collapse: collapse; }
                table, th, td { border:1px solid #000; padding:8px; text-align:center; }
            </style>
        </head>
        <body>
            <h3 style="text-align:center;">Visitor List</h3>
            ${printContent}
        </body>
        </html>
    `);

    win.document.close();
    win.print();
}

function exportExcel(){
    let table = document.querySelector("table").outerHTML;
    let file = new Blob([table], {type:"application/vnd.ms-excel"});
    
    let a = document.createElement("a");
    a.href = URL.createObjectURL(file);
    a.download = "visitor_list.xls";
    a.click();
}

function generatePassPopup() {

    let passNo = document.getElementById("pass_no").value;

    if(passNo == null || passNo.trim() === "") {
        return;
    }

    passNo = passNo.trim();

    window.location.href = "receipt.php?pass_no=" + encodeURIComponent(passNo);
}

document.querySelectorAll(".checkoutBtn").forEach(btn=>{
    btn.addEventListener("click",function(){
        document.getElementById("modal_pass_no").value = this.dataset.pass;

        let now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        document.getElementById("out_time").value = now.toISOString().slice(0,16);

        new bootstrap.Modal(document.getElementById("checkoutModal")).show();
    });
});

document.getElementById("checkoutForm").addEventListener("submit", function(e){
    e.preventDefault();
    let formData = new FormData(this);
    fetch("checkout_visitor.php", { method: "POST", body: formData })
    .then(res => res.json())
    .then(data => {
        if(data.status == "success") location.reload();
        else alert("Error: " + data.message);
    });
});

// Timeline Logic
document.querySelectorAll(".viewTimelineBtn").forEach(btn => {
    btn.addEventListener("click", function() {
        const id = this.dataset.id;
        document.getElementById('timelineVisitorName').innerText = this.dataset.name;
        const timelineModal = new bootstrap.Modal(document.getElementById('timelineModal'));
        
        fetch("get_meeting_timeline.php?visitor_id=" + id)
        .then(res => res.json())
        .then(data => {
            let html = '<div class="timeline-container">';
            if(data.length === 0) {
                html += '<p class="text-center text-muted">No detailed handoff history recorded yet.</p>';
            } else {
                data.forEach(item => {
                    let out = item.check_out_time ? new Date(item.check_out_time).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : '<span class="badge bg-success">Ongoing</span>';
                    
                    let durationTxt = "";
                    if (item.check_out_time) {
                        let diffMin = Math.round((new Date(item.check_out_time) - new Date(item.check_in_time)) / 60000);
                        if (diffMin >= 60) {
                            let h = Math.floor(diffMin / 60);
                            let m = diffMin % 60;
                            durationTxt = h + " hr" + (h > 1 ? "s" : "") + (m > 0 ? " " + m + " mins" : "");
                        } else {
                            durationTxt = diffMin + " mins";
                        }
                    }

                    html += `
                        <div class="mb-3 p-3 border rounded shadow-sm">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="fw-bold mb-0 text-primary">${item.emp_name}</h6>
                                    <small class="text-muted">${item.department} | ${item.designation}</small>
                                </div>
                                <div class="text-end">
                                    <div class="small fw-bold">${new Date(item.check_in_time).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})} - ${out}</div>
                                    ${durationTxt ? `<span class="badge bg-light text-dark border mt-1" style="font-size:0.6rem;">${durationTxt}</span>` : ''}
                                </div>
                            </div>
                            <div class="mt-2 small border-top pt-2">
                                ${item.assigner_name !== item.emp_name ? `
                                <span class="text-muted">Assigned By:</span> <strong>${item.assigner_name}</strong>` : ''}
                                ${item.notes ? `${item.assigner_name !== item.emp_name ? '<br>' : ''}<span class="text-muted">Note:</span> <em>${item.notes}</em>` : ''}
                            </div>
                        </div>
                    `;
                });
            }
            html += '</div>';
            document.getElementById('timelineContent').innerHTML = html;
            timelineModal.show();
        });
    });
});
</script>

<?php include("includes/footer.php"); ?>