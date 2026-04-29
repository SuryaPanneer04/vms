<?php
// session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require('../includes/config.php');

$department = $_POST['department'] ?? $_GET['department'] ?? '';

try {
    if (empty($department)) {
        echo json_encode([
            'status' => false,
            'massage' => 'department required'
        ]);
        exit();
    }
    $query = $con->prepare("SELECT id,full_name,designation,department FROM users WHERE department = :department AND status = 'Active'");
    $query->bindParam(':department', $department);
    $query->execute();

    $row = $query->fetchAll(PDO::FETCH_ASSOC);

    // ✅ Check user exists
    if (!$row) {
        echo json_encode([
            'status' => false,
            'message' => 'departments not found'
        ]);
        exit();
    }
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
