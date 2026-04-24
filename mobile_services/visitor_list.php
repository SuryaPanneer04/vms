<?php
session_start();
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
    $query = $con->prepare("SELECT * FROM visitor_master WHERE employee_id = :id ORDER BY id DESC");
    $query->bindParam(':id', $userId);
    $query->execute();

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
