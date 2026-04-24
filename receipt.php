<?php
session_start();
include("includes/config.php");
include("includes/header.php");
include("includes/sidebar.php");

$pass_no = $_GET['pass_no'] ?? '';

if (empty($pass_no)) {
    die("Invalid Pass No");
}


$sql = "SELECT v.*, e.emp_name AS meeting_person_name, e.contact_no AS meeting_person_contact, e.department, e.designation 
        FROM visitor_master v 
        LEFT JOIN employee_master e ON v.employee_id = e.id 
        WHERE v.pass_no = ?";

$stmt = $con->prepare($sql);
$stmt->execute([$pass_no]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    die("Visitor record not found");
}

// Barcode URL (Code 128 format)
$barcode_url = "https://barcode.tec-it.com/barcode.ashx?data=".$row['pass_no']."&code=Code128";
?>

<div class="content">
    <div class="container mt-4 content-animate">
        <div class="card shadow-lg border-0 receipt-card">
            
            <div class="card-header bg-primary text-white text-center py-3">
                <h4 class="mb-0 fw-bold"><i class="fas fa-id-card me-2"></i> VISITOR GATE PASS</h4>
            </div>

            <div class="card-body p-4" id="printArea">
                
                <!-- BARCODE SECTION -->
                <div class="text-center mb-4 border-bottom pb-4">
                    <img src="<?= $barcode_url ?>" alt="Pass Barcode" class="barcode-img">
                    <h5 class="mt-3 fw-bold text-primary">ID: <?= htmlspecialchars($row['pass_no']) ?></h5>
                </div>

                <div class="row g-4">
                    <!-- VISITOR DETAILS -->
                    <div class="col-md-6 border-end">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h6 class="text-muted fw-bold text-uppercase small mb-0">Visitor Details</h6>
                            <?php if(!empty($row['img_capture'])): 
                                $photo_src = (strpos($row['img_capture'], 'data:image') !== false) ? $row['img_capture'] : 'uploads/'.$row['img_capture'];
                            ?>
                                <img src="<?= $photo_src ?>" alt="Visitor Photo" class="rounded border shadow-sm" style="width: 100px; height: 100px; object-fit: cover;">
                            <?php endif; ?>
                        </div>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th class="text-muted" style="width: 120px;">Visitor Name:</th>
                                <td class="fw-bold"><?= htmlspecialchars($row['visitor_name']) ?></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Contact No:</th>
                                <td><?= htmlspecialchars($row['contact_no']) ?></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Visitor Type:</th>
                                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($row['visitor_type']) ?></span></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Company Name:</th>
                                <td><?= htmlspecialchars($row['company_name'] ?: '---') ?></td>
                            </tr>
                        </table>
                    </div>

                    <!-- MEETING DETAILS -->
                    <div class="col-md-6 ps-md-4">
                        <h6 class="text-muted fw-bold mb-3 text-uppercase small">Meeting Details</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th class="text-muted" style="width: 130px;">Whom to Meet:</th>
                                <td class="fw-bold text-primary"><?= htmlspecialchars($row['meeting_person_name'] ?: ($row['person_to_meet'] ?: 'Admin')) ?></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Contact No:</th>
                                <td class="fw-bold"><?= htmlspecialchars($row['meeting_person_contact'] ?: '---') ?></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Department:</th>
                                <td><?= htmlspecialchars($row['department'] ?: '---') ?></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Purpose:</th>
                                <td><?= htmlspecialchars($row['purpose']) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- FOOTER INFO -->
                <div class="mt-4 p-3 bg-light rounded-3 text-center">
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted d-block">Entry Time</small>
                            <span class="fw-bold"><?= date("d-M-Y h:i A", strtotime($row['in_time'])) ?></span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Status</small>
                            <span class="badge bg-success">Authorized</span>
                        </div>
                    </div>
                </div>

                <!-- SIGNATURE SECTION FOR PRINT -->
                <div class="mt-5 pt-5 d-none d-print-block">
                    <div class="row">
                        <div class="col-6 text-center">
                            <hr style="width: 150px; margin: auto;">
                            <p class="small text-muted mt-2">Visitor Signature</p>
                        </div>
                        <div class="col-6 text-center">
                            <hr style="width: 150px; margin: auto;">
                            <p class="small text-muted mt-2">Gate Security</p>
                        </div>
                    </div>
                </div>

            </div>

            <div class="card-footer text-center no-print py-3 bg-white border-top">
                <button onclick="printReceipt()" class="btn btn-success px-4 me-2">
                    <i class="fas fa-print me-2"></i> Print Gate Pass
                </button>
                <a href="list_visitor.php" class="btn btn-outline-secondary px-4">
                    <i class="fas fa-arrow-left me-2"></i> Back
                </a>
            </div>

        </div>
    </div>
</div>

<style>
.receipt-card {
    max-width: 800px;
    margin: auto;
    border-radius: 15px;
    overflow: hidden;
}

.barcode-img {
    max-width: 300px;
    width: 100%;
    height: auto;
}

@media print {
    body { background: #fff !important; }
    .sidebar, .no-print, header, footer { display: none !important; }
    .content { margin-left: 0 !important; padding: 0 !important; width: 100% !important; }
    .receipt-card { border: none !important; box-shadow: none !important; width: 100% !important; max-width: 100% !important; margin: 0 !important; }
    #printArea { visibility: visible !important; position: static !important; }
    .d-print-block { display: block !important; }
    @page { size: auto; margin: 10mm; }
}
</style>

<script>
function printReceipt() {
    window.print();
}
</script>

<?php include("includes/footer.php"); ?>