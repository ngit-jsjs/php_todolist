<?php
require "config.php";
$conn->prepare("DELETE FROM tasks WHERE id=?")->execute([$_GET['id']]);
header("Location: index.php");
