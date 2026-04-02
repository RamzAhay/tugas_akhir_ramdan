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
    <title>Dashboard Admin</title>
    <style>
        /* Sedikit styling sederhana supaya rapi */
        body { font-family: Arial, sans-serif; margin: 20px; }
        .card { border: 1px solid #ccc; padding: 15px; margin-bottom: 10px; width: 300px; border-radius: 5px; }
        .card h3 { margin-top: 0; }
    </style>
</head>
<body>

    <h2>Dashboard Administrator</h2>
    <p>Selamat datang, <strong><?php echo $_SESSION['nama']; ?></strong></p>
    <hr>

    <div style="display: flex; gap: 20px;">
        <div class="card">
            <h3>Total Anggota</h3>
            <p><?php echo $data_anggota['total_anggota'] ? $data_anggota['total_anggota'] : 0; ?> Orang</p>
        </div>

        <div class="card">
            <h3>Total Simpanan</h3>
            <p>Rp <?php echo number_format($data_simpanan['total_simpanan'], 0, ',', '.'); ?></p>
        </div>

        <div class="card">
            <h3>Total Pinjaman</h3>
            <p>Rp <?php echo number_format($data_pinjaman['total_pinjaman'], 0, ',', '.'); ?></p>
        </div>
    </div>

    <br>
    <a href="logout.php"><button>Logout</button></a>

</body>
</html>