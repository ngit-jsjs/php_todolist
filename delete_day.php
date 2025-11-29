<?php
require "config.php";

$day = $_GET["day"];
$daySQL = DateTime::createFromFormat("d/m/Y", $day)->format("Y-m-d");

$conn->query("DELETE FROM tasks WHERE DATE(start_time) = '$daySQL'");
header("Location: index.php");
