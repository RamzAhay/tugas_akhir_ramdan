<?php
include 'auth.php';

if ($_SESSION['role'] != 'Petugas') {
    header("Location: login.php");
    exit;
}
?>
