<?php
require 'vendor/autoload.php';
include 'includes/config.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

header('Content-Type: application/json');

// Only Admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {
    $file = $_FILES['excel_file']['tmp_name'];
    $extension = pathinfo($_FILES['excel_file']['name'], PATHINFO_EXTENSION);

    if (!in_array($extension, ['xlsx', 'xls', 'csv'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid file format. Please upload an Excel or CSV file.']);
        exit();
    }

    try {
        $spreadsheet = IOFactory::load($file);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        // Remove header row
        array_shift($rows);

        if (empty($rows)) {
            echo json_encode(['status' => 'error', 'message' => 'The uploaded file is empty.']);
            exit();
        }

        // Fix: Ensure the 'id' column is AUTO_INCREMENT to avoid 'Duplicate entry 0' errors
        // and TRUNCATE the table to reset the counter for a fresh import.
        // NOTE: These DDL statements cause an implicit commit in MySQL, so they must run before beginTransaction()
        try {
            $con->exec("ALTER TABLE employee_master MODIFY id INT NOT NULL AUTO_INCREMENT");
            $con->exec("TRUNCATE TABLE employee_master");
        } catch (Exception $e) {
            // If TRUNCATE fails (e.g. due to foreign keys), fallback to DELETE
            $con->exec("DELETE FROM employee_master");
            $con->exec("ALTER TABLE employee_master AUTO_INCREMENT = 1");
        }

        $con->beginTransaction();

        $stmt = $con->prepare("INSERT INTO employee_master (emp_name, designation, department, contact_no, email) VALUES (?, ?, ?, ?, ?)");

        foreach ($rows as $row) {
            if (empty($row) || !isset($row[0])) continue;

            // Handle case where entire row is in the first column (comma separated)
            if (count(array_filter($row)) == 1 && strpos($row[0], ',') !== false) {
                $row = str_getcsv($row[0]);
            }

            $row = array_map('trim', $row);

            // If the first column is a numeric ID, skip it to get to the Name
            if (isset($row[0]) && is_numeric($row[0]) && count($row) > 5) {
                array_shift($row);
            }

            if (empty($row[0])) continue; // Skip if name is empty

            $emp_name = $row[0] ?? '';
            $designation = $row[1] ?? '';
            $department = $row[2] ?? '';
            $val3 = $row[3] ?? '';
            $val4 = $row[4] ?? '';

            // Smart detection for Contact and Email order
            if (strpos($val3, '@') !== false) {
                $email = $val3;
                $contact_no = $val4;
            } else {
                $contact_no = $val3;
                $email = $val4;
            }

            $stmt->execute([$emp_name, $designation, $department, $contact_no, $email]);
        }

        $con->commit();
        echo json_encode(['status' => 'success', 'message' => 'Employees imported successfully.']);

    } catch (Exception $e) {
        if ($con->inTransaction()) {
            $con->rollBack();
        }
        echo json_encode(['status' => 'error', 'message' => 'Error processing file: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No file uploaded.']);
}
?>
