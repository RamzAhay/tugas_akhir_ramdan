<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION['username_ramdan'])){
    header("location:login_ramdan.php?pesan=belum_login");
    exit();
}
?>