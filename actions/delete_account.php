<?php
session_start();
require_once "../includes/config.php";

// Nếu chưa login → đá về login
if (!isset($_SESSION['user_id'])) {
    header("Location: dangnhap.php");
    exit;
}

$user_id = $_SESSION['user_id'];


    // 1. XÓA TASK (nếu không dùng ON DELETE CASCADE)
    $stmt = $conn->prepare("DELETE FROM tasks WHERE user_id = ?");
    $stmt->execute([$user_id]);

    // 2. XÓA USER
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id]);


// XÓA TOÀN BỘ SESSION COOKIES ĐÚNG CÁCH
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], 
        $params["domain"], 
        $params["secure"], 
        $params["httponly"]
    );
}

session_destroy();

// Redirect về login
header("Location: ../pages/login.php?msg=account_deleted");
exit;

?>
