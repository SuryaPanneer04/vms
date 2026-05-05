<?php
include("includes/config.php");
include("includes/header.php");
include("includes/sidebar.php");
date_default_timezone_set('Asia/Kolkata');

function generateVisitorID($con){
    do{
        $pass_no = rand(100000,999999);
        $stmt = $con->prepare("SELECT COUNT(*) FROM visitor_master WHERE pass_no=?");
        $stmt->execute([$pass_no]);
    }while($stmt->fetchColumn()>0);

    return $pass_no;
}

$pass_no = generateVisitorID($con);
$current_time = date("Y-m-d\TH:i");
?>

<div class="content">
    <div class="container-fluid content-animate">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="fw-bold mb-1 text-primary"><i class="fas fa-calendar-plus me-2"></i> Schedule a Visitor</h2>
                <p class="text-muted">Pre-register your visitor to speed up their entry at the gate.</p>
            </div>
        </div>

        <div class="card shadow border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Visitor Details</h5>
            </div>
            <div class="card-body p-4">
                <form id="scheduleForm">
                    <input type="hidden" name="pass_no" value="<?= 'VMS_'.$pass_no ?>">
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Visitor Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" name="visitor_name" placeholder="Full Name" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Contact Number <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="fas fa-phone"></i></span>
                                <input type="text" class="form-control" name="contact_no" placeholder="Phone Number" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" name="email" placeholder="email@example.com">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Company / Organization</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="fas fa-building"></i></span>
                                <input type="text" class="form-control" name="company_name" placeholder="Business Name">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Visitor Category <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="fas fa-users"></i></span>
                                <select class="form-select" name="visitor_type" required>
                                    <option value="Visitor">General Visitor</option>
                                    <option value="Client">Client / Partner</option>
                                    <option value="Vendor">Vendor / Service</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Gender <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="fas fa-venus-mars"></i></span>
                                <select class="form-select" name="gender" required>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Visitor Location</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="fas fa-map-marker-alt"></i></span>
                                <input type="text" class="form-control" name="location" placeholder="City or Office Location">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Scheduled Arrival Time <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="fas fa-clock"></i></span>
                                <input type="datetime-local" class="form-control" name="in_time" value="<?= $current_time ?>" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Purpose of Visit <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="fas fa-comment-alt"></i></span>
                                <textarea class="form-control" name="purpose" rows="1" placeholder="Reason for meeting..." required></textarea>
                            </div>
                        </div>

                        <div class="col-12 text-end mt-4">
                            <button type="reset" class="btn btn-light px-4 me-2">Cancel</button>
                            <button type="submit" class="btn btn-primary px-5 shadow">
                                <i class="fas fa-calendar-check me-2"></i> Schedule Visit
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById("scheduleForm").addEventListener("submit", function(e){
    e.preventDefault();
    let btn = this.querySelector("button[type='submit']");
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Scheduling...';

    let formData = new FormData(this);
    fetch("employee_save_schedule.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.status == "success"){
            alert("Visitor scheduled successfully! Pass No: " + data.pass_no);
            window.location.href = "employee_portal.php";
        } else {
            alert("Error: " + data.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-calendar-check me-2"></i> Schedule Visit';
        }
    })
    .catch(err => {
        alert("Server Error");
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-calendar-check me-2"></i> Schedule Visit';
    });
});
</script>

<?php include("includes/footer.php"); ?>
