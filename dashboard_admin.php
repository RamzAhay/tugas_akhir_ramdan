<?php
include 'role_admin.php';
include 'koneksi.php'; // Tambahkan koneksi database

// 1. Ambil Total Anggota
$query_anggota = mysqli_query($koneksi, "SELECT COUNT(id_anggota) as total_anggota FROM tb_anggota_ramdan");
$data_anggota = mysqli_fetch_assoc($query_anggota);

// 2. Ambil Total Simpanan
$query_simpanan = mysqli_query($koneksi, "SELECT SUM(jumlah) as total_simpanan FROM tb_simpanan_ramdan");
$data_simpanan = mysqli_fetch_assoc($query_simpanan);

// 3. Ambil Total Pinjaman
$query_pinjaman = mysqli_query($koneksi, "SELECT SUM(jumlah_pinjaman) as total_pinjaman FROM tb_pinjaman_ramdan");
$data_pinjaman = mysqli_fetch_assoc($query_pinjaman);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin - Koperasi</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; background-color: #f4f7f6; }
        
        /* Desain Navbar */
        .navbar { background-color: #0d6efd; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar h2 { margin: 0; font-size: 22px; }
        .nav-links { display: flex; gap: 15px; }
        .nav-links a { color: white; text-decoration: none; padding: 8px 15px; border-radius: 5px; font-weight: 500; transition: 0.3s; }
        .nav-links a:hover { background-color: #0b5ed7; }
        .btn-logout { background-color: #dc3545; }
        .btn-logout:hover { background-color: #bb2d3b !important; }

        /* Desain Konten */
        .container { padding: 30px; }
        
        /* Desain Kartu Statistik */
        .card-container { display: flex; gap: 20px; flex-wrap: wrap; margin-top: 20px; }
        .card { background: white; border-radius: 10px; padding: 20px; width: 300px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); border-top: 4px solid #0d6efd; }
        .card h3 { margin-top: 0; color: #6c757d; font-size: 16px; text-transform: uppercase; }
        .card p { font-size: 28px; font-weight: bold; color: #212529; margin-bottom: 0; }
    </style>
</head>
<body>

    <div class="navbar">
        <h2>🏢 Koperasi Ramdan</h2>
        <div class="nav-links">
            <a href="dashboard_admin.php" style="background-color: #0b5ed7;">Dashboard</a>
            <a href="data_anggota.php">Data Anggota</a>
            <a href="data_simpanan.php">Simpanan</a>
            <a href="data_pinjaman.php">Pinjaman</a>
            <a href="data_angsuran.php">Angsuran</a>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </div>

    <div class="container">
        <h2>Selamat datang, <?php echo $_SESSION['nama']; ?>! 👋</h2>
        <p>Ringkasan sistem koperasi hari ini:</p>
        
        <div class="card-container">
            <div class="card">
                <h3>👥 Total Anggota Terdaftar</h3>
                <p><?php echo $data_anggota['total_anggota'] ? $data_anggota['total_anggota'] : 0; ?> Orang</p>
            </div>

            <div class="card" style="border-top-color: #198754;">
                <h3>💰 Total Dana Simpanan</h3>
                <p>Rp <?php echo number_format($data_simpanan['total_simpanan'], 0, ',', '.'); ?></p>
            </div>

            <div class="card" style="border-top-color: #dc3545;">
                <h3>💸 Total Dana Dipinjamkan</h3>
                <p>Rp <?php echo number_format($data_pinjaman['total_pinjaman'], 0, ',', '.'); ?></p>
            </div>
        </div>
    </div>

</body>
</html>