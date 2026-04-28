<?php
include("includes/config.php");
header('Content-Type: application/json');

$visitor_id = $_GET['visitor_id'] ?? '';

if (empty($visitor_id)) {
    echo json_encode([]);
    exit;
}

try {
    $stmt = $con->prepare("
        SELECT h.*, u.full_name as emp_name, u.department, u.designation, 
               a.full_name as assigner_name
        FROM visitor_handoffs h
        JOIN users u ON h.emp_id = u.id
        JOIN users a ON h.assigned_by = a.id
        WHERE h.visitor_id = ?
        ORDER BY h.id ASC
    ");
    $stmt->execute([$visitor_id]);
    $timeline = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($timeline)) {
        // Synthesize first meeting record from visitor_master
        $v_stmt = $con->prepare("
            SELECT v.in_time as check_in_time, v.meeting_out_time as check_out_time,
                   u.full_name as emp_name, u.department, u.designation,
                   u.full_name as assigner_name
            FROM visitor_master v
            JOIN users u ON v.employee_id = u.id
            WHERE v.id = ?
        ");
        $v_stmt->execute([$visitor_id]);
        $initial = $v_stmt->fetch(PDO::FETCH_ASSOC);
        if ($initial) {
            $initial['notes'] = "Initial Meeting";
            $timeline = [$initial];
        }
    }

    echo json_encode($timeline);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
