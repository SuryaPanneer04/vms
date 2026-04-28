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

$visitor_data = null;
if(isset($_GET['id'])){
    $stmt = $con->prepare("SELECT v.*, e.department, e.designation 
                           FROM visitor_master v 
                           LEFT JOIN users e ON v.employee_id = e.id 
                           WHERE v.id = ?");
    $stmt->execute([$_GET['id']]);
    $visitor_data = $stmt->fetch(PDO::FETCH_ASSOC);
    $pass_no = generateVisitorID($con);
} else {
    $pass_no = generateVisitorID($con);
}

$emp = $con->prepare("SELECT u.*, d.dept_color 
               FROM users u 
               LEFT JOIN departments d ON u.department = d.dept_name 
               WHERE u.status = 'Active' ORDER BY u.full_name ASC");
$emp->execute();
$employees = $emp->fetchAll(PDO::FETCH_ASSOC);

$dept_q = $con->query("SELECT dept_name, dept_color FROM departments WHERE status = 1 ORDER BY dept_name ASC");
$departments_list = $dept_q->fetchAll(PDO::FETCH_ASSOC);

$checkin_time = !empty($visitor_data['in_time']) ? date("Y-m-d\TH:i", strtotime($visitor_data['in_time'])) : date("Y-m-d\TH:i");
$is_scheduled = ($visitor_data && $visitor_data['approval_status'] == 3);
$devices_arr = !empty($visitor_data['devices']) ? explode(",", $visitor_data['devices']) : [];
?>
<!-- Include Webcam.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>

<div class="content">
    <div class="row content-animate">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-id-card text-primary me-2"></i> Visitor Entry Gate</h4>
                    <span class="badge bg-soft-primary text-primary"><?= $is_scheduled ? 'Scheduled Verification' : 'New Registration' ?></span>
                </div>

                <div class="card-body p-4">
                    <form id="visitorForm" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?= $visitor_data['id'] ?? '' ?>">
                        <input type="hidden" name="pass_no" value="<?= 'VMS_'.$pass_no ?>">
                        <input type="hidden" name="img_capture" id="img_capture" value="<?= $visitor_data['img_capture'] ?? '' ?>">

                        <!-- SECTION: PHOTO CAPTURE -->
                        <div class="mb-5 p-4 rounded-4" style="background: rgba(67, 97, 238, 0.05); border: 1px solid rgba(67, 97, 238, 0.1);">
                            <h5 class="fw-bold mb-4" style="color: var(--primary-color)">
                                <i class="fas fa-camera me-2"></i> Visitor Photo Capture
                            </h5>
                            <div class="row g-4 align-items-center">
                                <div class="col-md-6 text-center">
                                    <div id="my_camera" class="mx-auto rounded-3 shadow-sm border bg-dark d-flex align-items-center justify-content-center text-white small" style="width: 320px; height: 240px; overflow: hidden;">
                                        <div class="text-center">
                                            <i class="fas fa-circle-notch fa-spin mb-2"></i><br>
                                            Initializing Camera...
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <button type="button" class="btn btn-primary" onclick="take_snapshot()">
                                            <i class="fas fa-camera me-2"></i> Capture Photo
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6 text-center">
                                    <div id="results" class="mx-auto rounded-3 shadow-sm border bg-light d-flex align-items-center justify-content-center text-muted small" style="width: 320px; height: 240px; overflow: hidden;">
                                        <?php if(!empty($visitor_data['img_capture'])): ?>
                                            <img src="uploads/<?= htmlspecialchars($visitor_data['img_capture']) ?>" class="img-fluid rounded-3"/>
                                        <?php else: ?>
                                            Captured image will appear here
                                        <?php endif; ?>
                                    </div>
                                    <div class="mt-3">
                                        <button type="button" class="btn btn-outline-secondary" onclick="retake_snapshot()">
                                            <i class="fas fa-sync-alt me-2"></i> Retake
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION: VISITOR INFO -->
                        <div class="mb-5">
                            <h5 class="fw-bold mb-4" style="color: var(--primary-color)">
                                <i class="fas fa-user-circle me-2"></i> Visitor Information
                            </h5>
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Gate Pass Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="fas fa-ticket-alt"></i></span>
                                        <input type="text" class="form-control bg-light border-0 fw-bold text-primary" value="<?= 'VMS_'.$pass_no ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Contact Number <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="fas fa-phone"></i></span>
                                        <input type="text" class="form-control shadow-sm" name="contact_no" id="contact_no" placeholder="Primary Phone" value="<?= htmlspecialchars($visitor_data['contact_no'] ?? '') ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Visitor Category <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="fas fa-users"></i></span>
                                        <select class="form-select shadow-sm" name="visitor_type" id="visitor_type" required>
                                            <option value="">Select Category</option>
                                            <option value="Client" <?= ($visitor_data['visitor_type'] ?? '') == 'Client' ? 'selected' : '' ?>>Client / Partner</option>
                                            <option value="Vendor" <?= ($visitor_data['visitor_type'] ?? '') == 'Vendor' ? 'selected' : '' ?>>Vendor / Service</option>
                                            <option value="Visitor" <?= ($visitor_data['visitor_type'] ?? '') == 'Visitor' ? 'selected' : '' ?>>General Visitor</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Visitor Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control shadow-sm" name="visitor_name" id="visitor_name" placeholder="Full Name" value="<?= htmlspecialchars($visitor_data['visitor_name'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Organization / Company</label>
                                    <input type="text" class="form-control shadow-sm" name="company_name" id="company_name" placeholder="Business Name" value="<?= htmlspecialchars($visitor_data['company_name'] ?? '') ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Email Address</label>
                                    <input type="email" class="form-control shadow-sm" name="email" id="email" placeholder="email@example.com" value="<?= htmlspecialchars($visitor_data['email'] ?? '') ?>">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Purpose of Visit <span class="text-danger">*</span></label>
                                    <textarea class="form-control shadow-sm" name="purpose" rows="2" placeholder="Describe the reason for meeting..." required><?= htmlspecialchars($visitor_data['purpose'] ?? '') ?></textarea>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Location</label>
                                    <input type="text" class="form-control shadow-sm" name="location" id="location" placeholder="e.g. Chennai, Bangalore, or Branch Name" value="<?= htmlspecialchars($visitor_data['location'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <!-- SECTION: TRANSPORT & ID -->
                        <div class="mb-5">
                            <h5 class="fw-bold mb-4" style="color: var(--primary-color)">
                                <i class="fas fa-shuttle-van me-2"></i> Logistics & Verification
                            </h5>
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Vehicle Type</label>
                                    <select class="form-select shadow-sm" name="vehicle_type">
                                        <option value="">None / Pedestrian</option>
                                        <option value="Bike" <?= ($visitor_data['vehicle_type'] ?? '') == 'Bike' ? 'selected' : '' ?>>2 Wheeler (Bike)</option>
                                        <option value="Car" <?= ($visitor_data['vehicle_type'] ?? '') == 'Car' ? 'selected' : '' ?>>4 Wheeler (Car)</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Vehicle Number</label>
                                    <input type="text" class="form-control shadow-sm" name="vehicle_number" placeholder="TN 00 XX 0000" value="<?= htmlspecialchars($visitor_data['vehicle_number'] ?? '') ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Primary ID Proof <span class="text-danger">*</span></label>
                                    <select class="form-select shadow-sm" name="id_type" id="id_type" required>
                                        <option value="">Select ID Type</option>
                                        <option value="Aadhar" <?= ($visitor_data['id_type'] ?? '') == 'Aadhar' ? 'selected' : '' ?>>Aadhar Card</option>
                                        <option value="PAN" <?= ($visitor_data['id_type'] ?? '') == 'PAN' ? 'selected' : '' ?>>PAN Card</option>
                                        <option value="Driving License" <?= ($visitor_data['id_type'] ?? '') == 'Driving License' ? 'selected' : '' ?>>Driving License</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">ID Document Upload</label>
                                    <div class="p-3 border rounded-3 bg-light">
                                        <input type="file" class="form-control shadow-sm" name="id_upload" id="id_upload">
                                        <div class="mt-2 text-muted small fw-bold">
                                            Status: <span id="old_file_name" class="text-success"><?= $visitor_data['id_upload'] ?? 'New Visitor' ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION: DEVICEDS -->
                        <div class="mb-5 p-4 rounded-4" style="background: rgba(67, 97, 238, 0.03);">
                            <h5 class="fw-bold mb-4" style="color: var(--primary-color)">
                                <i class="fas fa-laptop-medical me-2"></i> Declared Personal Assets
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="card p-3 border shadow-none bg-white">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="laptopCheck" name="devices[]" value="Laptop" <?= in_array('Laptop', $devices_arr) ? 'checked' : '' ?>>
                                            <label class="form-check-label fw-bold">Laptop</label>
                                        </div>
                                        <input type="number" class="form-control shadow-sm mt-3 <?= in_array('Laptop', $devices_arr) ? '' : 'd-none' ?> laptop-count" name="laptop_count" placeholder="Quantity" value="<?= $visitor_data['laptop_count'] ?? '' ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card p-3 border shadow-none bg-white">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="mobileCheck" name="devices[]" value="Mobile" <?= in_array('Mobile', $devices_arr) ? 'checked' : '' ?>>
                                            <label class="form-check-label fw-bold">Mobile Device</label>
                                        </div>
                                        <input type="number" class="form-control shadow-sm mt-3 <?= in_array('Mobile', $devices_arr) ? '' : 'd-none' ?> mobile-count" name="mobile_count" placeholder="Quantity" value="<?= $visitor_data['mobile_count'] ?? '' ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card p-3 border shadow-none bg-white">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="discCheck" name="devices[]" value="Disc" <?= in_array('Disc', $devices_arr) ? 'checked' : '' ?>>
                                            <label class="form-check-label fw-bold">Storage / Disc</label>
                                        </div>
                                        <input type="number" class="form-control shadow-sm mt-3 <?= in_array('Disc', $devices_arr) ? '' : 'd-none' ?> disc-count" name="disc_count" placeholder="Quantity" value="<?= $visitor_data['disc_count'] ?? '' ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card p-3 border shadow-none bg-white">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="chargerCheck" name="devices[]" value="Charger" <?= in_array('Charger', $devices_arr) ? 'checked' : '' ?>>
                                            <label class="form-check-label fw-bold">Charging Gear</label>
                                        </div>
                                        <input type="number" class="form-control shadow-sm mt-3 <?= in_array('Charger', $devices_arr) ? '' : 'd-none' ?> charger-count" name="charger_count" placeholder="Quantity" value="<?= $visitor_data['charger_count'] ?? '' ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION: MEETING -->
                        <div class="mb-4">
                            <h5 class="fw-bold mb-4" style="color: var(--primary-color)">
                                <i class="fas fa-user-tie me-2"></i> Employee Mapping (Meeting Details)
                            </h5>
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Filter by Department</label>
                                    <select id="filter_dept" class="form-select shadow-sm">
                                        <option value="">All Departments</option>
                                        <?php foreach($departments_list as $d): ?>
                                            <option value="<?= $d['dept_name'] ?>" 
                                                    data-color="<?= $d['dept_color'] ?>"
                                                    style="background-color: <?= $d['dept_color'] ?>; color: #fff;"
                                                    <?= ($visitor_data['department'] ?? '') == $d['dept_name'] ? 'selected' : '' ?>>
                                                <?= $d['dept_name'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Host Employee <span class="text-danger">*</span></label>
                                    <select name="employee_id" id="employee_id" class="form-select shadow-sm select2" required>
                                        <option value="">Choose Employee</option>
                                        <?php foreach($employees as $e): ?>
                                            <option value="<?= $e['id'] ?>" 
                                                    data-dept="<?= $e['department'] ?>" 
                                                    data-des="<?= $e['designation'] ?>" 
                                                    data-color="<?= $e['dept_color'] ?>"
                                                    style="border-left: 10px solid <?= $e['dept_color'] ?>;"
                                                    class="emp-opt" 
                                                    <?= ($visitor_data['employee_id'] ?? '') == $e['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($e['full_name']) ?> (<?= htmlspecialchars($e['department']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div id="dept_badge_preview" class="mt-2"></div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Host Designation</label>
                                    <input type="text" class="form-control bg-light" id="designation" value="<?= htmlspecialchars($visitor_data['designation'] ?? '') ?>" readonly placeholder="Auto-filled">
                                </div>
                               
                                

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Gate Entry Timestamp</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="fas fa-clock"></i></span>
                                        <input type="datetime-local" class="form-control shadow-sm" name="in_time" value="<?= $checkin_time ?>">
                                    </div>
                                </div>
                                <div class="col-12 text-end mt-4">
                                    <button type="reset" class="btn btn-light px-4 me-2">Clear Form</button>
                                    <button type="submit" class="btn btn-primary px-5 shadow">
                                        <?php if($is_scheduled): ?>
                                            Submit <i class="fas fa-check-double ms-2"></i>
                                        <?php else: ?>
                                            Verify & Authorize Gate Entry <i class="fas fa-check-circle ms-2"></i>
                                        <?php endif; ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function(){

if (typeof Webcam !== 'undefined') {
    Webcam.set({
        width: 320,
        height: 240,
        image_format: 'jpeg',
        jpeg_quality: 90,
        constraints: {
            width: { exact: 320 },
            height: { exact: 240 },
            facingMode: 'user'
        }
    });
    
    Webcam.on('error', function(err) {
        console.error("Webcam Error: ", err);
        document.getElementById('my_camera').innerHTML = '<div class="p-3 text-danger small">Camera Error: ' + err + '</div>';
    });

    Webcam.attach('#my_camera');
} else {
    console.error("Webcam.js not loaded from CDN");
}

window.take_snapshot = function() {
    if (typeof Webcam !== 'undefined') {
        try {
            Webcam.snap(function(data_uri) {
                document.getElementById('results').innerHTML = '<img src="'+data_uri+'" class="img-fluid rounded-3"/>';
                document.getElementById('img_capture').value = data_uri;
                
                // Stop the camera
                Webcam.reset();
                document.getElementById('my_camera').innerHTML = '<div class="text-center text-muted small"><i class="fas fa-video-slash mb-2"></i><br>Camera turned off</div>';
            });
        } catch (e) {
            alert("Please wait for the camera to load completely.");
        }
    }
}

window.retake_snapshot = function() {
    document.getElementById('results').innerHTML = 'Captured image will appear here';
    document.getElementById('img_capture').value = '';
    
    // Restart the camera
    document.getElementById('my_camera').innerHTML = '<div class="text-center text-white small"><i class="fas fa-circle-notch fa-spin mb-2"></i><br>Initializing Camera...</div>';
    if (typeof Webcam !== 'undefined') {
        Webcam.attach('#my_camera');
    }
}

function toggleCount(checkId, className){
    document.getElementById(checkId).addEventListener("change", function(){
        document.querySelector("." + className).classList.toggle("d-none", !this.checked);
    });
}

toggleCount("laptopCheck","laptop-count");
toggleCount("discCheck","disc-count");
toggleCount("mobileCheck","mobile-count");
toggleCount("chargerCheck","charger-count");

document.getElementById("filter_dept").addEventListener("change", function(){
    let dept = this.value;
    let empSelect = document.getElementById("employee_id");
    let options = empSelect.querySelectorAll(".emp-opt");
    
    empSelect.value = ""; // Reset selection
    document.getElementById("designation").value = "";

    options.forEach(opt => {
        if(dept === "" || opt.getAttribute("data-dept") === dept){
            opt.style.display = "block";
        } else {
            opt.style.display = "none";
        }
    });
});

document.getElementById("employee_id").addEventListener("change", function(){
    let selected = this.options[this.selectedIndex];
    document.getElementById("designation").value = selected.getAttribute("data-des") || '';
    
    // Show Color Badge
    let deptName = selected.getAttribute("data-dept");
    let deptColor = selected.getAttribute("data-color");
    let badgeContainer = document.getElementById("dept_badge_preview");
    
    if(deptName && deptColor) {
        badgeContainer.innerHTML = `<span class="badge w-100 py-2 fw-bold text-uppercase" style="background: ${deptColor}; color: #fff; letter-spacing: 0.5px;">${deptName}</span>`;
    } else {
        badgeContainer.innerHTML = "";
    }
});

document.getElementById("contact_no").addEventListener("keyup", function(){
    let phone = this.value;

    if(phone.length >= 10){
        fetch("get_visitor.php?contact_no=" + phone)
        .then(res => res.json())
        .then(data => {
            if(data.status == "found"){
                  document.getElementById("visitor_name").value = data.visitor_name || '';
                  document.querySelector("[name='visitor_type']").value = data.visitor_type || '';
                  document.getElementById("company_name").value = data.company_name || '';
                  document.getElementById("email").value = data.email || '';
                  
                  // Reset department filter to show all so the selected employee is visible
                  document.getElementById("filter_dept").value = "";
                  document.querySelectorAll(".emp-opt").forEach(opt => opt.style.display = "block");
                  
                  document.getElementById("employee_id").value = data.employee_id || '';
                  document.getElementById("id_type").value = data.id_type || '';
                  document.getElementById("designation").value = data.designation || '';

                  document.getElementById("old_file_name").innerText = data.id_upload || 'No file uploaded';
              }
        });
    }
});

document.getElementById("visitorForm").addEventListener("submit", function(e){
    e.preventDefault();

    let submitBtn = this.querySelector("button[type='submit']");
    let originalBtnText = submitBtn.innerHTML;
    
    if(submitBtn.disabled){
        return;
    }

    submitBtn.disabled = true;
    submitBtn.innerHTML = "Processing...";

    let formData = new FormData(this);

    fetch("save_visitor.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.status == "success"){
            // Only send mail if it's a NEW registration (no ID in form)
            let isUpdate = document.querySelector('input[name="id"]').value;
            
            if(!isUpdate){
                alert("Visitor Added Successfully & wait for employee approval");
                window.location.href = "list_visitor.php";
            } else {
                alert("Approved");
                window.location.href = "list_visitor.php";
            }
        } else {
            alert("Warning: " + data.message);
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }
    })
    .catch(err => {
        alert("An error occurred while communicating with the server: " + err);
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    });
});

});
</script>

<?php include("includes/footer.php"); ?>