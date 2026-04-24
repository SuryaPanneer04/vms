<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require('../includes/config.php');

$email = $_POST['email'] ?? $_GET['email'] ?? '';
$password = $_POST['password'] ?? $_GET['password'] ?? '';

try {
    if (empty($email) || empty($password)) {
        echo json_encode([
            'status' => false,
            'massage' => 'email & password required'
        ]);
        exit();
    }
    $query = $con->prepare("SELECT * FROM employee_master WHERE email = :email");
    $query->bindParam(':email', $email);
    $query->execute();

    $row = $query->fetch(PDO::FETCH_ASSOC);

    // ✅ Check user exists
    if (!$row) {
        echo json_encode([
            'status' => false,
            'message' => 'User not found'
        ]);
        exit();
    }
    // ✅ Correct password check
    if (password_verify($password, $row['password'])) {
        echo json_encode([
            'status' => true,
            'message' => 'Login Success',
            'data' => $row
        ]);
    } else {
        echo json_encode([
            'status' => false,
            'message' => 'Invalid password'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => false,
        'massage' => 'Db Error'
    ]);
}
