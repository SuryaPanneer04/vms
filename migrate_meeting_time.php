<?php
include("includes/config.php");
try {
    // Check if scheduled_time exists
    $check_sch = $con->query("SHOW COLUMNS FROM visitor_master LIKE 'scheduled_time'");
    if ($check_sch->rowCount() > 0) {
        $con->exec("ALTER TABLE visitor_master CHANGE scheduled_time meeting_date_time DATETIME DEFAULT NULL");
        echo "Renamed scheduled_time to meeting_date_time.<br>";
    } else {
        // Check if meeting_date_time exists
        $check_meet = $con->query("SHOW COLUMNS FROM visitor_master LIKE 'meeting_date_time'");
        if ($check_meet->rowCount() == 0) {
            $con->exec("ALTER TABLE visitor_master ADD COLUMN meeting_date_time DATETIME DEFAULT NULL AFTER email");
            echo "Added meeting_date_time column.<br>";
        } else {
            echo "meeting_date_time column already exists.<br>";
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
