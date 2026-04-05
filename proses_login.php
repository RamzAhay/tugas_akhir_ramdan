<?php
session_start();
include 'koneksi.php';

$username = mysqli_real_escape_string($koneksi, $_POST['username']);
$password = mysqli_real_escape_string($koneksi, $_POST['password']);

// Query cek user
$query = "SELECT * FROM tb_user_ramdan WHERE username='$username' AND password='$password'";
$login = mysqli_query($koneksi, $query);

if (!$login) {
    die("Query Error: " . mysqli_error($koneksi));
}

$cek = mysqli_num_rows($login);

if($cek > 0){
    $data = mysqli_fetch_assoc($login);

    // Simpan data ke session
    $_SESSION['username'] = $data['username'];
    $_SESSION['nama']     = $data['nama'];
    
    // Perhatikan bagian id_role ini:
    // Berdasarkan data kamu: 1 = Admin, 2 = Petugas
    if($data['id_role'] == 1){
        $_SESSION['role'] = "Admin"; // Kita set teks agar sinkron dengan auth.php
        header("location:dashboard_admin.php");
        exit();
    } else if($data['id_role'] == 2){
        $_SESSION['role'] = "Petugas";
        header("location:dashboard_petugas.php");
        exit();
    } else {
        header("location:login.php?pesan=gagal");
        exit();
    }
} else {
    header("location:login.php?pesan=gagal");
    exit();
}
?>