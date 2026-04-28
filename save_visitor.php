<?php
ob_start();
include("includes/config.php");
include 'smtp.php';
date_default_timezone_set('Asia/Kolkata');
header('Content-Type: application/json');

$id_file_name = null;

$id            = trim($_POST['id'] ?? '');
$pass_no       = trim($_POST['pass_no'] ?? '');
$purpose       = trim($_POST['purpose'] ?? '');
$visitor_name  = trim($_POST['visitor_name'] ?? '');
$visitor_type  = trim($_POST['visitor_type'] ?? '');
$company_name  = trim($_POST['company_name'] ?? '');
$contact_no    = trim($_POST['contact_no'] ?? '');
$email         = trim($_POST['email'] ?? '');
$employee_id   = trim($_POST['employee_id'] ?? '');
$vehicle_type   = trim($_POST['vehicle_type'] ?? '');
$vehicle_number = trim($_POST['vehicle_number'] ?? '');
$id_type   = trim($_POST['id_type'] ?? '');
$location  = trim($_POST['location'] ?? '');
$img_capture = trim($_POST['img_capture'] ?? '');

// Robust integer conversion to avoid '1366 Incorrect integer value' error
$laptop_count  = (int)($_POST['laptop_count'] ?? 0);
$disc_count    = (int)($_POST['disc_count'] ?? 0);
$mobile_count  = (int)($_POST['mobile_count'] ?? 0);
$charger_count = (int)($_POST['charger_count'] ?? 0);

$in_time = trim($_POST['in_time'] ?? date("Y-m-d H:i:s"));

$devices = isset($_POST['devices']) ? implode(",", $_POST['devices']) : '';

if (empty($employee_id) || empty($purpose) || empty($visitor_name) || empty($contact_no) || empty($visitor_type) || empty($id_type)) {
    echo json_encode([
        "status" => "error",
        "message" => "Fill all required fields"
    ]);
    exit;
}

if (empty($pass_no)) {
    echo json_encode([
        "status" => "error",
        "message" => "Pass number missing"
    ]); 
    exit;
}

$upload_dir = "uploads/";
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

if (isset($_FILES['id_upload']) && $_FILES['id_upload']['error'] === UPLOAD_ERR_OK) {
    $ext = pathinfo($_FILES['id_upload']['name'], PATHINFO_EXTENSION);
    $file_name = time() . "_" . uniqid() . "." . $ext;

    if (move_uploaded_file($_FILES['id_upload']['tmp_name'], $upload_dir . $file_name)) {
        $id_file_name = $file_name;
    }
}

// Handle Webcam Capture (Base64 string to Image File)
if (!empty($img_capture) && strpos($img_capture, 'data:image') !== false) {
    $img_parts = explode(";base64,", $img_capture);
    $image_type_aux = explode("image/", $img_parts[0]);
    $image_type = $image_type_aux[1];
    $image_base64 = base64_decode($img_parts[1]);
    
    // Custom Filename: DATE_TIME_PASSNO.jpg (as requested)
    $img_file_name = "img_" .date('Ymd_His') . "_" . $pass_no . "." . $image_type;
    $img_file_path = $upload_dir . $img_file_name;
    
    if (file_put_contents($img_file_path, $image_base64)) {
        $img_capture = $img_file_name; // Store only the filename in the DB
    }
}

// Fetch employee name for 'person_to_meet' to satisfy DB constraint
$person_to_meet = "";
if(!empty($employee_id)){
    $e_stmt = $con->prepare("SELECT full_name FROM users WHERE id = ?");
    $e_stmt->execute([$employee_id]);
    $person_to_meet = $e_stmt->fetchColumn() ?: "";
}

try {
    if (!empty($id)) {

        // UPDATE
        $stmt = $con->prepare("UPDATE visitor_master SET
            pass_no       = ?,
            purpose       = ?,
            visitor_name  = ?,
            visitor_type  = ?,
            company_name  = ?,
            contact_no    = ?,
            email         = ?,
            employee_id   = ?,
            vehicle_type  = ?,
            vehicle_number= ?,
            id_type       = ?,
            devices       = ?,
            laptop_count  = ?,
            disc_count    = ?,
            mobile_count  = ?,
            charger_count = ?,
            in_time       = ?,
            person_to_meet = ?,
            img_capture   = ?,
            location      = ?,
            approval_status = 1,
            id_upload     = COALESCE(?, id_upload)
            WHERE id = ?");

        $stmt->execute([
            $pass_no,
            $purpose,
            $visitor_name,
            $visitor_type,
            $company_name,
            $contact_no,
            $email,
            $employee_id,
            $vehicle_type,
            $vehicle_number,
            $id_type,
            $devices,
            $laptop_count,
            $disc_count,
            $mobile_count,
            $charger_count,
            $in_time,
            $person_to_meet,
            $img_capture,
            $location,
            $id_file_name,
            $id
        ]);

        // NEW: Create initial handoff record upon approval/check-in
        // Only if it doesn't already exist to avoid duplicates
        $check_h = $con->prepare("SELECT id FROM visitor_handoffs WHERE visitor_id = ?");
        $check_h->execute([$id]);
        if (!$check_h->fetch()) {
            $h_ins = $con->prepare("INSERT INTO visitor_handoffs (visitor_id, emp_id, assigned_by, check_in_time, notes) VALUES (?, ?, ?, ?, ?)");
            $h_ins->execute([$id, $employee_id, $employee_id, $in_time, "Initial Meeting"]);
        }

    } else {

        // INSERT
        $stmt = $con->prepare("INSERT INTO visitor_master
        (
            pass_no,
            purpose,
            visitor_name,
            visitor_type,
            company_name,
            contact_no,
            email,
            employee_id,
            vehicle_type,
            vehicle_number,
            id_type,
            devices,
            laptop_count,
            disc_count,
            mobile_count,
            charger_count,
            in_time,
            id_upload,
            person_to_meet,
            img_capture,
            location,
            approval_status
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)");

        $stmt->execute([
            $pass_no,
            $purpose,
            $visitor_name,
            $visitor_type,
            $company_name,
            $contact_no,
            $email,
            $employee_id,
            $vehicle_type,
            $vehicle_number,
            $id_type,
            $devices,
            $laptop_count,
            $disc_count,
            $mobile_count,
            $charger_count,
            $in_time,
            $id_file_name,
            $person_to_meet,
            $img_capture,
            $location
        ]);
    }

    // Prepare response data
    $response = [
        "status" => "success",
        "pass_no" => $pass_no
    ];

    // FAST RESPONSE: Send JSON to browser and close connection to continue in background
    if (function_exists('fast_response')) {
        fast_response($response);
    } else {
        // Fallback if helper not defined
        echo json_encode($response);
        
        // If we want to continue, we need to handle it properly
        if (session_id()) session_write_close();
        header("Content-Encoding: none");
        header("Content-Length: " . ob_get_length());
        header("Connection: close");
        ob_end_flush();
        ob_flush();
        flush();
    }

    // --- BACKGROUND TASKS (Mail Sending) ---
    // Only send mail if it's a NEW registration (no ID in form)
    if (empty($id)) {
        // Fetch host details for email
        $stmt = $con->prepare("
            SELECT v.*, e.email as employee_email, e.full_name as emp_name
            FROM visitor_master v
            LEFT JOIN users e ON v.employee_id = e.id
            WHERE v.pass_no = ?
        ");
        $stmt->execute([$pass_no]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data && !empty($data['employee_email'])) {
            $placeholders = [
                'emp_name' => $data['emp_name'],
                'visitor_name' => $data['visitor_name'],
                'contact_no' => $data['contact_no'],
                'pass_no' => $data['pass_no'],
                'purpose' => $data['purpose'],
                'company_name' => $data['company_name'],
                'in_time' => $data['in_time']
            ];

            sendTemplateMail($data['employee_email'], 'visitor_approval', $placeholders);
        }
    }

} catch(PDOException $e) {
    if (ob_get_level()) ob_end_clean();
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>