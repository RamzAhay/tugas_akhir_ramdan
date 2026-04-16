<?php
session_start();
include 'koneksi.php';

$username = $_POST['username'];
$password = $_POST['password'];

$stmt = $koneksi->prepare("SELECT * FROM tb_user_ramdan WHERE username = ? AND password = ?");
$stmt->bind_param("ss", $username, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    
    $_SESSION['username'] = $data['username'];
    $_SESSION['nama'] = $data['nama'];
    $_SESSION['status'] = "login";
    
    if ($data['id_role'] == 1) {
        $_SESSION['role'] = 'Admin';  // 1 = Admin
        header("location:dashboard_admin.php");
    } else if ($data['id_role'] == 2) {
        $_SESSION['role'] = 'Petugas'; // 2 = Petugas
        header("location:dashboard_petugas.php");
    }
} else {
    header("location:login.php?pesan=gagal");
}

$stmt->close();
?>