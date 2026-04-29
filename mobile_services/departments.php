<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require('../includes/config.php');


try {
    $query = $con->prepare("SELECT * FROM departments WHERE status = 1");
    $query->execute();

    $row = $query->fetchAll(PDO::FETCH_ASSOC);

    // ✅ Check user exists
    if (!$row) {
        echo json_encode([
            'status' => false,
            'message' => 'department table error'
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
