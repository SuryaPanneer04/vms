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
    $con->beginTransaction();
    $stmt = $con->prepare("UPDATE visitor_master SET meeting_out_time = ? WHERE pass_no = ?");
    $stmt->execute([$out_time, $pass_no]);

    // Check if an active handoff segment exists
    $check = $con->prepare("
        SELECT h.id FROM visitor_handoffs h
        JOIN visitor_master v ON h.visitor_id = v.id
        WHERE v.pass_no = ? AND h.check_out_time IS NULL
    ");
    $check->execute([$pass_no]);
    $handoff_id = $check->fetchColumn();

    if ($handoff_id) {
        // Close existing segment
        $h_upd = $con->prepare("UPDATE visitor_handoffs SET check_out_time = ? WHERE id = ?");
        $h_upd->execute([$out_time, $handoff_id]);
    } else {
        // Fallback: create a segment if none exists (though save_visitor.php should handle this now)
        $v_stmt = $con->prepare("SELECT id, employee_id, in_time FROM visitor_master WHERE pass_no = ?");
        $v_stmt->execute([$pass_no]);
        $v_data = $v_stmt->fetch();
        
        if ($v_data) {
            $h_ins = $con->prepare("INSERT INTO visitor_handoffs (visitor_id, emp_id, check_in_time, check_out_time, assigned_by, notes) VALUES (?, ?, ?, ?, ?, ?)");
            $h_ins->execute([$v_data['id'], $v_data['employee_id'], $v_data['in_time'], $out_time, $v_data['employee_id'], "Completed"]);
        }
    }
    
    $con->commit();

    echo json_encode(["status" => "success", "message" => "meeting checkout updated successfully"]);

} catch(PDOException $e) {
    echo json_encode([
        "status"  => "error",
        "message" => $e->getMessage()
    ]);
}
?>