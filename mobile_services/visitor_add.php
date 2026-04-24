<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require('../includes/config.php');

$employeeId = $_POST['employee_id'] ?? $_GET['employee_id'] ?? '';
$name = $_POST['visitor_name'] ?? $_GET['visitor_name'] ?? '';
$contactNo = $_POST['contact_no'] ?? $_GET['contact_no'] ?? '';
$companyName = $_POST['company_name'] ?? $_GET['company_name'] ?? '';
$email = $_POST['email'] ?? $_GET['email'] ?? '';
$visitorType = $_POST['visitor_type'] ?? $_GET['visitor_type'] ?? '';


try {
    if (
        empty($employeeId) ||
        empty($name) ||
        empty($contactNo) ||
        empty($companyName) ||
        empty($email) ||
        empty($visitorType)
    ) {
        echo json_encode([
            'status' => false,
            'message' => 'All fields are required'
        ]);
        exit();
    }
    //	approval_status = 3 TASK SCUDULE BY EMPLOYEE

    $query = $con->prepare("INSERT INTO visitor_master 
        (employee_id, visitor_name, contact_no, company_name, email, visitor_type, 	approval_status)
        VALUES 
        (:employeeId, :name, :contact_no, :company_name, :email, :visitor_type, 3)
    ");

    $query->bindParam(':employeeId', $employeeId);
    $query->bindParam(':name', $name);
    $query->bindParam(':contact_no', $contactNo);
    $query->bindParam(':company_name', $companyName);
    $query->bindParam(':email', $email);
    $query->bindParam(':visitor_type', $visitorType);

    if ($query->execute()) {
        echo json_encode([
            'status' => true,
            'message' => 'Visitor added successfully'
        ]);
    } else {
        echo json_encode([
            'status' => false,
            'message' => 'Failed to insert data'
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'status' => false,
        'message' => $e->getMessage()
    ]);
}
