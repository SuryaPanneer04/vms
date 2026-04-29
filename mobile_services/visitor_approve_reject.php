<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require('../includes/config.php');

$tableId = $_POST['table_id'] ?? $_GET['table_id'] ?? '';
$status = $_POST['status'] ?? $_GET['status'] ?? '';

try {
    if (empty($tableId)) {
        echo json_encode([
            'status' => false,
            'massage' => 'table Id required'
        ]);
        exit();
    }
    $query = $con->prepare("UPDATE visitor_master SET approval_status =:status WHERE id = :id");
    $query->bindParam(':status', $status);
    $query->bindParam(':id', $tableId);
    if ($query->execute()) {
        // ✅ Correct password check
        echo json_encode([
            'status' => true,
            'message' => 'Status updated successfully'
        ]);
    } else {
        echo json_encode([
            'status' => false,
            'massage' => 'Some went wrong'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => false,
        'massage' => 'Db Error'
    ]);
}
