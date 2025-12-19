<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');

$host = "sql200.infinityfree.com";
$db   = "if0_40678515_todolist_db";
$user = "if0_40678515";
$pass = "L8s4NxJPN9QVsZt";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Lỗi DB: " . $e->getMessage());
}

