<?php
session_start();
session_destroy();
header('Location: ../pages/dangnhap.php');
exit;