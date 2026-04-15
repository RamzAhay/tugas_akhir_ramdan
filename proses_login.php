<?php
session_start();
include 'koneksi.php';

// Menangkap data yang dikirim dari form login
$username = $_POST['username'];
$password = $_POST['password'];

// 1. Menggunakan Prepared Statement untuk MENCEGAH SQL Injection
// Kerangka query disiapkan tanpa memasukkan variabel secara langsung
$stmt = $koneksi->prepare("SELECT * FROM tb_user_ramdan WHERE username = ? AND password = ?");

// 2. Menghubungkan variabel dengan query (s = string, s = string)
$stmt->bind_param("ss", $username, $password);

// 3. Menjalankan query
$stmt->execute();

// 4. Mengambil hasil dari eksekusi query
$result = $stmt->get_result();

// Menghitung jumlah data yang ditemukan
$cek = $result->num_rows;

if ($cek > 0) {
    // Jika data ditemukan, ambil datanya
    $data = $result->fetch_assoc();
    
    // Simpan data ke dalam session
    $_SESSION['username'] = $data['username'];
    $_SESSION['role'] = $data['role']; // Asumsi nama kolomnya adalah 'role', bisa juga 'level'
    $_SESSION['status'] = "login";
    
    // Cek hak akses/role (silakan sesuaikan nama kolomnya jika di databasemu menggunakan 'level')
    if ($data['role'] == "admin") {
        header("location:dashboard_admin.php");
    } else if ($data['role'] == "petugas") {
        header("location:dashboard_petugas.php");
    } else {
        // Jika tidak ada role yang cocok, lempar ke admin sebagai default
        header("location:dashboard_admin.php");
    }
} else {
    // Jika username/password salah, kembalikan ke halaman login dengan pesan error
    header("location:login.php?pesan=gagal");
}

// Tutup koneksi agar server tidak berat
$stmt->close();
?>