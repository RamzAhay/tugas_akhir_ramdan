<?php 
session_start();
session_destroy();
header("location:login_ramdan.php?pesan=logout");
exit();
?>