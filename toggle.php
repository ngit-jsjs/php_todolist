<?php
require "config.php";
$conn->prepare("UPDATE tasks SET progress=? WHERE id=?")->execute([$_POST['progress'], $_POST['id']]);
header("Location: index.php");
