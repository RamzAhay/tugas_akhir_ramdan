<?php
session_start();
include 'koneksi.php';

$username = $_POST['username'];
$password = $_POST['password'];

$query = mysqli_query($koneksi, "
    SELECT u.*, r.nama_role
    FROM tb_user_ramdan u
    JOIN tb_role_ramdan r ON u.id_role = r.id_role
    WHERE u.username='$username' AND u.password='$password'
");

$data = mysqli_fetch_assoc($query);

if ($data) {
    $_SESSION['id_user'] = $data['id_user'];
    $_SESSION['nama'] = $data['nama'];
    $_SESSION['role'] = $data['nama_role'];

    if ($data['nama_role'] == 'Admin') {
        header("Location: dashboard_admin.php");
    } else if ($data['nama_role'] == 'Petugas') {
        header("Location: dashboard_petugas.php");
    }
} else {
    echo "Login gagal!";
}
?>
