<?php
session_start();
include 'koneksi_ramdan.php';

$username_ramdan = $_POST['username_ramdan'];
$password_ramdan = $_POST['password_ramdan'];

// 1. Cari user hanya berdasarkan username_ramdan saja
$stmt = $koneksi->prepare("SELECT * FROM tb_user_ramdan WHERE username_ramdan = ?");
$stmt->bind_param("s", $username_ramdan);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    
    // 2. Verifikasi password_ramdan yang diinput dengan hash yang ada di database
    if (password_verify($password_ramdan, $data['password_ramdan'])) {
        $_SESSION['username_ramdan'] = $data['username_ramdan'];
        $_SESSION['nama_ramdan'] = $data['nama_ramdan'];
        $_SESSION['status'] = "login";
        $_SESSION['id_user_ramdan'] = $data['id_user_ramdan'];
        
        if ($data['id_role_ramdan'] == 1) {
            $_SESSION['role'] = 'Admin';  // 1 = Admin
            header("location:dashboard_admin_ramdan.php");
        } else if ($data['id_role_ramdan'] == 2) {
            $_SESSION['role'] = 'Petugas'; // 2 = Petugas
            header("location:dashboard_petugas_ramdan.php");
        }
    } else {
        // Password tidak cocok
        header("location:login_ramdan.php?pesan=gagal");
    }
} else {
    // Username tidak ditemukan
    header("location:login_ramdan.php?pesan=gagal");
}

$stmt->close();
?>