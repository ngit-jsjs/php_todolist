<?php
require "config.php";

$id = $_GET["id"];
$stmt = $conn->prepare("SELECT * FROM tasks WHERE id=?");
$stmt->execute([$id]);
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_POST) {
    $stmt = $conn->prepare("UPDATE tasks SET title=?, content=?, start_time=?, end_time=? WHERE id=?");
    $stmt->execute([
        $_POST["title"], 
        $_POST["content"], 
        $_POST["start"], 
        $_POST["end"], 
        $id
    ]);
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Sửa công việc</title>

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
        border-radius: 22px;
        width: 440px;
        box-shadow: 0 10px 26px rgba(255, 115, 190, 0.25);
        animation: fadeUp 0.45s ease;
    }

    h1 {
        text-align: center;
        margin-bottom: 25px;
        color: #ff6fb8;
        font-weight: 600;
        font-size: 26px;
    }

    label {
        font-weight: 500;
        color: #555;
    }

    input, textarea {
        width: 100%;
        padding: 13px;
        border-radius: 14px;
        border: 1px solid #e7c7eb;
        background: #fff6fd;
        margin-top: 6px;
        margin-bottom: 20px;
        outline: none;
        font-size: 15px;
        transition: 0.25s;
        box-shadow: 0 3px 10px rgba(255, 118, 188, 0.1);
    }

    input:focus, textarea:focus {
        border-color: #ff87cd;
        box-shadow: 0 0 12px rgba(255, 118, 188, 0.25);
    }

    textarea {
        resize: none;
        height: 100px;
    }

    button {
        width: 100%;
        padding: 13px;
        border: none;
        background: #ff71c5;
        color: white;
        font-size: 17px;
        font-weight: 600;
        border-radius: 14px;
        cursor: pointer;
        transition: 0.25s;
        box-shadow: 0 4px 14px rgba(255, 118, 188, 0.25);
    }

    button:hover {
        background: #ff4ebb;
        transform: translateY(-2px);
    }

    .back {
        display: block;
        margin-top: 15px;
        text-align: center;
        text-decoration: none;
        color: #777;
        font-size: 14px;
        transition: 0.2s;
    }

    .back:hover {
        color: #ff4ebb;
    }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }
</style>

</head>

<body>

<div class="container">

    <h1>✏️ Sửa Công Việc</h1>

    <form method="POST">

        <label>Tên công việc:</label>
        <input name="title" value="<?= htmlspecialchars($task['title']) ?>" required>

        <label>Nội dung:</label>
        <textarea name="content"><?= htmlspecialchars($task['content']) ?></textarea>

        <label>Bắt đầu:</label>
        <input type="datetime-local" name="start" 
            value="<?= date('Y-m-d\TH:i', strtotime($task['start_time'])) ?>">

        <label>Hạn chót:</label>
        <input type="datetime-local" name="end"
            value="<?= date('Y-m-d\TH:i', strtotime($task['end_time'])) ?>" required>

        <button>Lưu thay đổi</button>
    </form>

    <a href="index.php" class="back">← Quay lại</a>

</div>

</body>
</html>
