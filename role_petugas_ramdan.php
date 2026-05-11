<?php
include 'auth_ramdan.php';

if ($_SESSION['role'] != 'Petugas') {
    header("Location: login_ramdan.php");
    exit;
}
?>
