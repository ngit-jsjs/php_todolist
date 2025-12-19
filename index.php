<?php
session_start();
// CHƯA LOGIN
if (!isset($_SESSION['user_id'])) {
    header("Location: pages/login.php");
    exit;
}

// ĐÃ LOGIN
header("Location: pages/home.php"); // hoặc pages/home.php
exit;
