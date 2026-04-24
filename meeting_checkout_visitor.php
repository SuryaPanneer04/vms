<?php
include("includes/config.php");
date_default_timezone_set('Asia/Kolkata');
header('Content-Type: application/json');

$pass_no  = $_POST['pass_no']  ?? '';
$out_time = !empty($_POST['meeting_out_time']) 
            ? date("Y-m-d H:i:s", strtotime($_POST['meeting_out_time']))
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
        SET meeting_out_time = ?
        WHERE pass_no = ?
    ");

    $stmt->execute([$out_time, $pass_no]);

    echo json_encode([
        "status"  => "success",
        "message" => "meeting checkout updated successfully"
    ]);

} catch(PDOException $e) {
    echo json_encode([
        "status"  => "error",
        "message" => $e->getMessage()
    ]);
}
?>