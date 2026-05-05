<?php
// ============================================================
//  config/db.php
//  Database connection file for QCUS-PORTAL
//  Include this at the top of any PHP file that needs the DB:
//    require_once '../config/db.php';
// ============================================================
 
define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // Default XAMPP MySQL username
define('DB_PASS', '');            // Default XAMPP MySQL password (empty)
define('DB_NAME', 'qcuportal_database');
 
// Create connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
 
// Check connection
if (!$conn) {
    die(json_encode([
        'error' => 'Database connection failed: ' . mysqli_connect_error()
    ]));
}
 
// Set character encoding to UTF-8
mysqli_set_charset($conn, 'utf8mb4');
?>