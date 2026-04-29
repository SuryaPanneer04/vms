<?php
header('Content-Type: application/json');

require('../includes/config.php');


date_default_timezone_set('Asia/Kolkata');

$visitor_id  = $_POST['visitor_id'] ?? $_GET['visitor_id'] ?? '';
$to_emp_id   = $_POST['to_emp_id'] ?? $_GET['to_emp_id'] ?? '';
$notes       = $_POST['notes'] ?? $_GET['notes'] ?? '';
$from_emp_id = $_POST['user_id'] ?? $_GET['user_id'] ?? '';
$now         = date("Y-m-d H:i:s");

if (empty($visitor_id) || empty($to_emp_id)) {
    echo json_encode([
        'status' => false,
        'message' => 'Invalid data'
    ]);
    exit();
}

try {
    // 1. Close current meeting session for the current employee
    $stmt = $con->prepare("SELECT id FROM visitor_handoffs WHERE visitor_id = ? AND check_out_time IS NULL ORDER BY id DESC LIMIT 1");
    $stmt->execute([$visitor_id]);
    $active_id = $stmt->fetchColumn();

    if ($active_id) {
        $upd = $con->prepare("UPDATE visitor_handoffs SET check_out_time = ? WHERE id = ?");
        $upd->execute([$now, $active_id]);
    } else {
        // Fallback: if no active segment found, create one and close it immediately to maintain history
        $v_stmt = $con->prepare("SELECT in_time FROM visitor_master WHERE id = ?");
        $v_stmt->execute([$visitor_id]);
        $in_time = $v_stmt->fetchColumn() ?: $now;

        $ins_old = $con->prepare("INSERT INTO visitor_handoffs (visitor_id, emp_id, check_in_time, check_out_time, assigned_by, notes) VALUES (?, ?, ?, ?, ?, ?)");
        $ins_old->execute([$visitor_id, $from_emp_id, $in_time, $now, $from_emp_id, "Handoff"]);
    }

    // 2. Insert new session for the TO employee
    $ins_new = $con->prepare("INSERT INTO visitor_handoffs (visitor_id, emp_id, check_in_time, assigned_by, notes) VALUES (?, ?, ?, ?, ?)");
    $ins_new->execute([$visitor_id, $to_emp_id, $now, $from_emp_id, $notes]);

    // // 3. Update main visitor record to show current host
    // $upd_v = $con->prepare("UPDATE visitor_master SET employee_id = ? WHERE id = ?");
    // $upd_v->execute([$to_emp_id, $visitor_id]);

    // $con->commit();
    echo json_encode(["status" => true, "message" => 'data update successfully']);
} catch (Exception $e) {
    // if ($con->inTransaction()) $con->rollBack();
    echo json_encode([
        "status" => "error",
        "vi" => $visitor_id,
        "ei" => $to_emp_id,
        "from" => $from_emp_id,
        "notes" => $notes,
        "message" => $e
    ]);
}
