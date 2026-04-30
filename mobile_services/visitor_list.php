<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require('../includes/config.php');

$userId = $_POST['user_id'] ?? $_GET['user_id'] ?? '';


try {
    if (empty($userId)) {
        echo json_encode([
            'status' => false,
            'massage' => 'user Id required'
        ]);
        exit();
    }
    $query = $con->prepare("SELECT 
            hm.*,
            hm.id AS handoffsId,
            v.*,
            ash.full_name AS assigner_name,
            curr_emp.full_name AS current_host_name
        FROM visitor_master v

        LEFT JOIN visitor_handoffs hm 
            ON v.id = hm.visitor_id 
            AND hm.emp_id != :id AND hm.assigned_by = :id
             -- hm.emp_id = :id

        LEFT JOIN users ash 
            ON hm.assigned_by = ash.id 

        LEFT JOIN users curr_emp 
            ON hm.emp_id = curr_emp.id
            -- v.employee_id = curr_emp.id

        WHERE 
        -- v.approval_status = 1 AND
         (
            v.employee_id = :id 
            OR EXISTS (
                SELECT 1 
                FROM visitor_handoffs vh 
                WHERE vh.visitor_id = v.id 
                AND vh.emp_id = :id
            )
        );
    ");
    $query->bindParam(':id', $userId);
    $query->execute();

    // -- SELECT vm.*, 
    // --      vh.id AS handoffsId, vh.visitor_id,vh.emp_id,vh.assigned_by,vh.check_in_time,vh.check_out_time 
    // --      FROM visitor_master vm 
    // --      LEFT JOIN visitor_handoffs vh ON vh.visitor_id = vm.id AND vh.emp_id = vm.employee_id 
    // --      WHERE vm.employee_id = :id ORDER BY vm.id DESC;
    $row = $query->fetchAll(PDO::FETCH_ASSOC);

    // ✅ Check user exists
    if (!$row) {
        echo json_encode([
            'status' => false,
            'message' => 'User not found'
        ]);
        exit();
    }
    // ✅ Correct password check
    echo json_encode([
        'status' => true,
        'data' => $row
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => false,
        'massage' => 'Db Error'
    ]);
}
