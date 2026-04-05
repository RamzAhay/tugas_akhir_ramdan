<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION['username'])){
    header("location:login.php?pesan=belum_login");
    exit();
}
?>