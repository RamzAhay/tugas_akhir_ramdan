<?php
include 'auth_ramdan.php';

if ($_SESSION['role'] != 'Admin') {
    header("Location: login_ramdan.php");
    exit;
}
?>
