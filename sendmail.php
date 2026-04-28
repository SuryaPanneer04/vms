<?php
include("includes/config.php");
include("smtp.php");

$pass_no = $_GET['pass_no'] ?? '';

$stmt = $con->prepare("
    SELECT v.*, e.email as employee_email, e.full_name as emp_name
    FROM visitor_master v
    LEFT JOIN users e ON v.employee_id = e.id
    WHERE v.pass_no = ?
");
$stmt->execute([$pass_no]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if($data && !empty($data['employee_email'])){
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

echo "Mail Sent";
?>