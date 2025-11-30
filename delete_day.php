<?php
session_start();
require "config.php";

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: dangnhap.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$day = $_GET["day"];
$daySQL = DateTime::createFromFormat("d/m/Y", $day)->format("Y-m-d");

// Xóa task trong ngày chỉ của user hiện tại
$stmt = $conn->prepare("DELETE FROM tasks WHERE DATE(start_time) = ? AND user_id = ?");
$stmt->execute([$daySQL, $user_id]);
header("Location: index.php");
