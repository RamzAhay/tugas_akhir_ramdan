<?php
session_start();
include 'koneksi.php';

$username = $_POST['username'];
$password = $_POST['password'];

// Cek username dan password ke database dengan aman
$stmt = $koneksi->prepare("SELECT * FROM tb_user_ramdan WHERE username = ? AND password = ?");
$stmt->bind_param("ss", $username, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    
    // Simpan data dasar ke session
    $_SESSION['username'] = $data['username'];
    $_SESSION['status'] = "login";
    
    // Pengecekan simpel berdasarkan id_role dari databasemu
    if ($data['id_role'] == 1) {
        $_SESSION['role'] = 'Admin';  // 1 = Admin
        header("location:dashboard_admin.php");
    } else if ($data['id_role'] == 2) {
        $_SESSION['role'] = 'Petugas'; // 2 = Petugas
        header("location:dashboard_petugas.php");
    }
} else {
    // Jika username/password salah, kembalikan ke form login
    header("location:login.php?pesan=gagal");
}

$stmt->close();
?>