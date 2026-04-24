<?php
include("includes/config.php");
include("smtp.php");

$pass_no = $_GET['pass_no'] ?? '';

$stmt = $con->prepare("
    SELECT v.*, e.email as employee_email, e.emp_name
    FROM visitor_master v
    LEFT JOIN employee_master e ON v.employee_id = e.id
    WHERE v.pass_no = ?
");
$stmt->execute([$pass_no]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if($data && !empty($data['employee_email'])){

    $subject = "Visitor Request Submitted";

    $body = "
        Dear {$data['emp_name']},<br><br>

        A new visitor request has been submitted and is awaiting your approval.<br><br>

        <b>Visitor Details:</b><br>
        <b>Visitor Name:</b> {$data['visitor_name']} <br>
        <b>Contact Number:</b> {$data['contact_no']} <br>
        <b>Pass Number:</b> {$data['pass_no']} <br>
        <b>Purpose of Visit:</b> {$data['purpose']} <br>
        <b>Company Name:</b> {$data['company_name']} <br>
        <b>Check-in Time:</b> {$data['in_time']} <br><br>

        Kindly review and approve the visitor request at your earliest convenience.<br><br>

        Regards,<br>
        <b>Visitor Management System</b>
    ";

    sendMail($data['employee_email'], $subject, $body);
}

echo "Mail Sent";
?>