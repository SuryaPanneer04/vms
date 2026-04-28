<?php
include("includes/config.php");
date_default_timezone_set('Asia/Kolkata');
header('Content-Type: application/json');

$pass_no  = $_POST['pass_no']  ?? '';
$out_time = !empty($_POST['out_time']) 
            ? date("Y-m-d H:i:s", strtotime($_POST['out_time']))
            : date("Y-m-d H:i:s");

if(empty($pass_no)){
    echo json_encode([
        "status"  => "error",
        "message" => "Pass number missing"
    ]);
    exit;
}

try {
    $stmt = $con->prepare("
        UPDATE visitor_master
        SET out_time = ?,
            meeting_out_time = COALESCE(meeting_out_time, ?)
        WHERE pass_no = ?
    ");
    $stmt->execute([$out_time, $out_time, $pass_no]);

    // Also close any active handoff segment
    $h_upd = $con->prepare("
        UPDATE visitor_handoffs h
        JOIN visitor_master v ON h.visitor_id = v.id
        SET h.check_out_time = ?
        WHERE v.pass_no = ? AND h.check_out_time IS NULL
    ");
    $h_upd->execute([$out_time, $pass_no]);

    echo json_encode([
        "status"  => "success",
        "message" => "Gate checkout updated successfully"
    ]);

} catch(PDOException $e) {
    echo json_encode([
        "status"  => "error",
        "message" => $e->getMessage()
    ]);
}
?>