<?php
include("includes/config.php");

$contact = $_GET['contact_no'] ?? '';

$stmt = $con->prepare("
    SELECT v.*, e.department, e.designation
    FROM visitor_master v
    LEFT JOIN users e ON v.employee_id = e.id
    WHERE v.contact_no = ?
    ORDER BY v.id DESC
    LIMIT 1
");

$stmt->execute([$contact]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if($row){
    echo json_encode([
        "status"      => "found",
        "visitor_name"=> $row['visitor_name'],
        "visitor_type"=> $row['visitor_type'],
        "company_name"=> $row['company_name'],
        "email"       => $row['email'],
        "contact_no"  => $row['contact_no'],
        "employee_id" => $row['employee_id'],
        "department"  => $row['department'],
        "designation" => $row['designation'],
        "id_type"     => $row['id_type'],
        "id_upload" => $row['id_upload']
    ]);
}else{
    echo json_encode([
        "status" => "not_found"
    ]);
}
?>