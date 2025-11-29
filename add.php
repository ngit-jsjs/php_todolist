<?php
require "config.php";

if ($_POST) {
    $title = $_POST["title"];
    $content = $_POST["content"];
    $start = $_POST["start"] ?: date("Y-m-d H:i:s");
    $end = $_POST["end"];

    if (!$title || !$end) {
        die("Thiếu dữ liệu!");
    }

    $stmt = $conn->prepare("INSERT INTO tasks (title, content, start_time, end_time) VALUES (?, ?, ?, ?)");
    $stmt->execute([$title, $content, $start, $end]);

    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Thêm công việc</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap">

<style>
    body {
        margin: 0;
        padding: 0;
        font-family: Poppins, sans-serif;
        background: #fdf6ff;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    .container {
        background: white;
        padding: 35px 40px;
        border-radius: 20px;
        box-shadow: 0 8px 25px rgba(255, 118, 188, 0.25);
        width: 420px;
        animation: fadeUp 0.5s ease;
    }

    h1 {
        text-align: center;
        margin-bottom: 25px;
        color: #ff6fb8;
        font-weight: 600;
    }

    label {
        font-weight: 500;
        color: #444;
    }

    input, textarea {
        width: 100%;
        padding: 12px;
        border-radius: 12px;
        border: 1px solid #e3cce9;
        margin-top: 6px;
        margin-bottom: 18px;
        outline: none;
        background: #fff6fd;
        transition: 0.25s;
    }

    input:focus, textarea:focus {
        border-color: #ff87cd;
        box-shadow: 0 0 10px rgba(255, 118, 188, 0.2);
    }

    textarea {
        height: 90px;
        resize: none;
    }

    button {
        width: 100%;
        padding: 12px;
        border: none;
        background: #ff71c5;
        color: white;
        font-size: 16px;
        font-weight: 600;
        border-radius: 12px;
        cursor: pointer;
        margin-top: 10px;
        transition: 0.25s;
        box-shadow: 0 4px 12px rgba(255, 118, 188, 0.25);
    }

    button:hover {
        background: #ff4fb4;
        transform: translateY(-2px);
    }

    .back {
        display: block;
        text-align: center;
        margin-top: 15px;
        text-decoration: none;
        color: #777;
        font-size: 14px;
        transition: 0.2s;
    }

    .back:hover {
        color: #ff4fb4;
    }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

</head>

<body>

<div class="container">
    <h1>➕ Thêm Công Việc</h1>

    <form method="POST">

        <label>Tên công việc:</label>
        <input name="title" placeholder="Nhập tên công việc..." required>

        <label>Nội dung:</label>
        <textarea name="content" placeholder="Nội dung chi tiết..."></textarea>

        <label>Bắt đầu:</label>
        <input type="datetime-local" name="start">

        <label>Hạn chót: *</label>
        <input type="datetime-local" name="end" required>

        <button>Thêm công việc</button>
    </form>

    <a href="index.php" class="back">← Quay lại danh sách</a>
</div>

</body>
</html>
