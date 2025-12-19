<?php
session_start();
require "../includes/config.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error']);
    exit;
}

$id = $_POST['id'] ?? null;
$progress = $_POST['progress'] ?? null;
$user_id = $_SESSION['user_id'];

if ($id === null || $progress === null) {
    echo json_encode(['status' => 'error']);
    exit;
}

$stmt = $conn->prepare(
    "UPDATE tasks SET progress = ? WHERE id = ? AND user_id = ?"
);
$ok = $stmt->execute([$progress, $id, $user_id]);

echo json_encode([
    'status' => $ok ? 'success' : 'error'
]);
