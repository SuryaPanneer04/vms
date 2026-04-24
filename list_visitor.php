<?php include("includes/config.php"); ?>

<?php
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
if($from_date) { $count_query .= " AND DATE(in_time) >= ?"; $params[] = $from_date; }
if($to_date) { $count_query .= " AND DATE(in_time) <= ?"; $params[] = $to_date; }

$total_stmt = $con->prepare($count_query);
$total_stmt->execute($params);
$total_records = $total_stmt->fetchColumn();
$total_pages = ceil($total_records / $limit);

// Fetch visitors with LIMIT
$query = "SELECT v.*, e.emp_name, e.department 
          FROM visitor_master v 
          LEFT JOIN employee_master e ON v.employee_id = e.id 
          WHERE 1=1";

$final_params = [];
if($pass_no) { $query .= " AND v.pass_no LIKE ?"; $final_params[] = "%$pass_no%"; }
if($contact_no) { $query .= " AND v.contact_no LIKE ?"; $final_params[] = "%$contact_no%"; }
if($from_date) { $query .= " AND DATE(v.in_time) >= ?"; $final_params[] = $from_date; }
if($to_date) { $query .= " AND DATE(v.in_time) <= ?"; $final_params[] = $to_date; }

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
        <div class="card p-4 mb-4">
            <h5 class="fw-bold mb-3"><i class="fas fa-filter text-primary me-2"></i> Security Filters</h5>
            <form method="GET">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label fw-bold small">Pass No</label>
                        <input type="text" name="pass_no" id="filter_pass_no" class="form-control" placeholder="Search ID" value="<?= htmlspecialchars($_GET['pass_no'] ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">Contact No</label>
                        <input type="text" name="contact_no" class="form-control" placeholder="Ph no" value="<?= htmlspecialchars($_GET['contact_no'] ?? '') ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold small">From Date</label>
                        <input type="date" name="from_date" class="form-control" value="<?= $_GET['from_date'] ?? '' ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold small">To Date</label>
                        <input type="date" name="to_date" class="form-control" value="<?= $_GET['to_date'] ?? '' ?>">
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button class="btn btn-primary flex-grow-1" title="Filter Search"><i class="fas fa-search"></i> Search</button>
                        <a href="list_visitor.php" class="btn btn-light" title="Reset Filter"><i class="fas fa-sync-alt"></i></a>
                    </div>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="fas fa-history text-primary me-2"></i> Gate Entry Log</h4>
                <div class="d-flex gap-2">
                    <button class="btn btn-info btn-sm text-white px-3" onclick="getPrint()" title= "Reports print"><i class="fas fa-print"></i> Print</button>
                    <button class="btn btn-success btn-sm px-3" onclick="exportExcel()" title= "Export the Excel File"><i class="fas fa-file-excel"></i> Excel</button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th># Pass No</th>
                                <th>Visitor Details</th>
                                <th>Meeting With</th>
                                <th>Timestamps</th>
                                <th>Status</th>
                                <th>Approval Status</th>
                                <th>Meeting OverTime</th>
                                <th class="text-center">Gate Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($visitors as $row): ?>
                            <tr>
                                <td>
                                    <span class="fw-bold text-primary">#<?= htmlspecialchars($row['pass_no']) ?></span>
                                    <div class="small text-muted"><?= htmlspecialchars($row['visitor_type']) ?></div>
                                </td>
                                <td>
                                    <div class="fw-bold"><?= htmlspecialchars($row['visitor_name']) ?></div>
                                    <div class="small text-muted"><i class="fas fa-building me-1"></i> <?= htmlspecialchars($row['company_name'] ?: 'Independent') ?></div>
                                    <div class="small text-muted"><i class="fas fa-map-marker-alt me-1"></i> <?= htmlspecialchars($row['location'] ?: 'Not Specified') ?></div>
                                    <div class="small text-muted"><i class="fas fa-phone-alt me-1"></i> <?= htmlspecialchars($row['contact_no']) ?></div>
                                </td>
                                <td>
                                    <div class="fw-bold"><?= htmlspecialchars($row['emp_name']) ?></div>
                                    <div class="badge bg-light text-dark fw-normal"><?= htmlspecialchars($row['department']) ?></div>
                                </td>
                                <td>
                                    <div class="small">
                                        <i class="fas fa-sign-in-alt text-success me-1"></i> 
                                        <?= date("d M, h:i A", strtotime($row['in_time'])) ?>
                                    </div>
                                    <?php if(!empty($row['out_time'])): ?>
                                    <div class="small mt-1">
                                        <i class="fas fa-sign-out-alt text-danger me-1"></i> 
                                        <?= date("d M, h:i A", strtotime($row['out_time'])) ?>
                                    </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($row['approval_status'] == 3): ?>
                                        <span class="badge bg-info"><i class="fas fa-door-closed me-1"></i> OUTSIDE</span>
                                    <?php elseif(empty($row['out_time'])): ?>
                                        <span class="badge bg-success"><i class="fas fa-door-open me-1"></i> INSIDE</span>
                                    <?php else: ?>
                                        <span class="badge bg-dark"><i class="fas fa-door-closed me-1"></i> EXITED</span>
                                    <?php endif; ?>

                                </td>
                                <!-- Approval Status Column -->
                                    <td>
                                        <?php if($row['approval_status'] == 0): ?>
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-clock me-1"></i> Pending
                                            </span>
                                        <?php elseif($row['approval_status'] == 1): ?>
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i> Approved
                                            </span>

                                        <?php elseif($row['approval_status'] == 2): ?>
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times-circle me-1"></i> Rejected
                                            </span>
                                        <?php endif; ?>
                                        <?php if($row['approval_status'] == 3): ?>
                                            <a href="add_visitor_form.php?id=<?= $row['id'] ?>" class="text-decoration-none">
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-clock me-1"></i> Scheduled
                                                </span>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                
                                    <td>
                                        <?php if(!empty($row['meeting_out_time'])): ?>
                                            <div class="small text-danger">
                                                <i class="fas fa-sign-out-alt me-1"></i> 
                                                <?= date("d M, h:i A", strtotime($row['meeting_out_time'])) ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    
                                <td>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <?php if($row['approval_status'] == 1): ?>
                                            <!-- APPROVED ONLY — show actions -->
                                            <a href="receipt.php?pass_no=<?= urlencode($row['pass_no']) ?>" 
                                            class="btn btn-outline-primary btn-sm" 
                                            title="Print Pass">
                                                <i class="fas fa-print"></i>
                                            </a>

                                            <?php if(empty($row['out_time'])): ?>
                                                <button class="btn btn-danger btn-sm checkoutBtn" 
                                                    title="End Visitor Meeting"
                                                    data-pass="<?= $row['pass_no'] ?>">
                                                    <i class="fas fa-sign-out-alt me-1"></i> End Visit
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-secondary btn-sm" disabled>
                                                    <i class="fas fa-check"></i> Done
                                                </button>
                                            <?php endif; ?>

                                        <?php else: ?>
                                            <!-- PENDING (0) or REJECTED (2) — no actions -->
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($visitors)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="fas fa-folder-open fa-3x text-light mb-3"></i>
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
        <input type="datetime-local" class="form-control" name="out_time" id="out_time">

      </div>

      <div class="modal-footer">
        <button type="submit" class="btn btn-danger">Confirm Checkout</button>
      </div>

    </form>
  </div>
</div>
<!-- END CHECKOUT MODAL -->
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

    fetch("checkout_visitor.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())       
    .then(text => {
        console.log("Server raw response:", text);
        try {
            let data = JSON.parse(text);  
            if(data.status == "success"){
                location.reload();
            } else {
                alert("Error: " + data.message);
            }
        } catch(e) {
            
            alert("Server error:\n" + text.substring(0, 200));
        }
    })
    .catch(err => {
        alert("Network error: " + err);
    });
});
</script>

<?php include("includes/footer.php"); ?>