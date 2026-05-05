<?php
include("includes/config.php");
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(["status" => "error", "message" => "ID missing"]);
    exit;
}

$id = $_GET['id'];
$stmt = $con->prepare("
    SELECT v.*, e.full_name as host_name, e.department as host_dept, e.designation as host_desig
    FROM visitor_master v
    LEFT JOIN users e ON v.employee_id = e.id
    WHERE v.id = ?
");
$stmt->execute([$id]);
$visitor = $stmt->fetch(PDO::FETCH_ASSOC);

if ($visitor) {
    echo json_encode(["status" => "success", "data" => $visitor]);
} else {
    echo json_encode(["status" => "error", "message" => "Visitor not found"]);
}
?>
