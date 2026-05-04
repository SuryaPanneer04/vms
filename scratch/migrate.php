<?php
include("includes/config.php");
try {
    $con->exec("ALTER TABLE visitor_master ADD COLUMN checkin_by INT DEFAULT NULL");
    echo "Added checkin_by\n";
} catch(Exception $e) { echo "checkin_by might already exist\n"; }

try {
    $con->exec("ALTER TABLE visitor_master ADD COLUMN checkout_by INT DEFAULT NULL");
    echo "Added checkout_by\n";
} catch(Exception $e) { echo "checkout_by might already exist\n"; }
?>
