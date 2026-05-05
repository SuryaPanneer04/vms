<?php
ob_start();
include("includes/config.php");
include 'smtp.php';
header('Content-Type: application/json');
date_default_timezone_set('Asia/Kolkata');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$visitor_name = trim($_POST['visitor_name'] ?? '');
$contact_no   = trim($_POST['contact_no'] ?? '');
$email        = trim($_POST['email'] ?? '');
$location     = trim($_POST['location'] ?? '');
$purpose      = trim($_POST['purpose'] ?? '');
$visitor_type = $_POST['visitor_type'] ?? 'Visitor';
$company_name = trim($_POST['company_name'] ?? '');
$in_time      = $_POST['in_time'] ?? date("Y-m-d H:i:s");
$pass_no      = trim($_POST['pass_no'] ?? '');
$gender       = $_POST['gender'] ?? 'Male';
$employee_id  = $_SESSION['user_id'];

if (empty($visitor_name) || empty($contact_no) || empty($purpose)) {
    echo json_encode(["status" => "error", "message" => "Required fields missing"]);
    exit;
}

// Fetch person_to_meet name from users table
$stmt_u = $con->prepare("SELECT full_name FROM users WHERE id = ?");
$stmt_u->execute([$employee_id]);
$person_to_meet = $stmt_u->fetchColumn() ?: '';

try {
    $stmt = $con->prepare("INSERT INTO visitor_master 
    (pass_no, visitor_name, contact_no, email, company_name, location, purpose, in_time, meeting_date_time, employee_id, person_to_meet, approval_status, visitor_type, checkin_by, gender) 
    VALUES (?, ?, ?, ?, ?, ?, ?, NULL, ?, ?, ?, 3, ?, ?, ?)");
    
    $stmt->execute([
        $pass_no,
        $visitor_name,
        $contact_no,
        $email,
        $company_name,
        $location,
        $purpose,
        $in_time,
        $employee_id,
        $person_to_meet,
        $visitor_type,
        $employee_id,
        $gender
    ]);

    // Send Mail to Visitor
    $mail_status = true;
    if(!empty($email)){
        $subject = "Visitor Pre-Registration - " . $pass_no;
        $body = "<h3>Hello " . htmlspecialchars($visitor_name) . ",</h3>
                 <p>Your visit to our office has been scheduled by <b>" . htmlspecialchars($person_to_meet) . "</b>.</p>
                 <p><b>Pass Number:</b> " . $pass_no . "</p>
                 <p><b>Scheduled Time:</b> " . date("d M Y, h:i A", strtotime($in_time)) . "</p>
                 <p>Please show this Pass Number at the security gate upon arrival.</p>";
        
        $mail_status = sendMail($email, $subject, $body);
    }

    if (ob_get_level()) ob_end_clean();
    echo json_encode([
        "status" => "success",
        "pass_no" => $pass_no,
        "mail_status" => $mail_status === true ? "sent" : "failed",
        "mail_error" => $mail_status === true ? "" : $mail_status
    ]);

} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Database Error: " . $e->getMessage()
    ]);
}
?>
