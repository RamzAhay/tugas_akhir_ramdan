<?php
include 'auth.php';
include 'koneksi.php';

if ($_SESSION['role'] != 'Admin') {
    die("Akses Ditolak!");
}

if (isset($_GET['id'])) {
    $id_user = mysqli_real_escape_string($koneksi, $_GET['id']);
    
    // Keamanan lapis dua: Admin tidak boleh hapus diri sendiri lewat URL injeksi
    if ($id_user == $_SESSION['id_user']) {
        echo "<script>alert('Anda tidak bisa menghapus akun Anda sendiri yang sedang aktif!'); window.location='data_user.php';</script>";
        exit();
    }

    $query = mysqli_query($koneksi, "DELETE FROM tb_user_ramdan WHERE id_user = '$id_user'");

    if ($query) {
        echo "<script>alert('Akses pengguna berhasil dicabut!'); window.location='data_user.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus pengguna.'); window.location='data_user.php';</script>";
    }
} else {
    header("Location: data_user.php");
}
?>