<?php
include 'auth.php';

if ($_SESSION['role'] != 'Admin') {
    header("Location: login.php");
    exit;
}
?>
