<?php
include 'role_petugas.php';
include 'koneksi.php'; 

// Petugas mungkin hanya butuh melihat total anggota untuk sementara
$query_anggota = mysqli_query($koneksi, "SELECT COUNT(id_anggota) as total_anggota FROM tb_anggota_ramdan");
$data_anggota = mysqli_fetch_assoc($query_anggota);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Petugas - Koperasi</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; background-color: #f4f7f6; }
        
        .navbar { background-color: #198754; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar h2 { margin: 0; font-size: 22px; }
        .nav-links { display: flex; gap: 15px; }
        .nav-links a { color: white; text-decoration: none; padding: 8px 15px; border-radius: 5px; font-weight: 500; transition: 0.3s; }
        .nav-links a:hover { background-color: #146c43; }
        .btn-logout { background-color: #dc3545; }
        .btn-logout:hover { background-color: #bb2d3b !important; }

        .container { padding: 30px; }
        
        .card-container { display: flex; gap: 20px; flex-wrap: wrap; margin-top: 20px; }
        .card { background: white; border-radius: 10px; padding: 20px; width: 300px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); border-top: 4px solid #198754; }
        .card h3 { margin-top: 0; color: #6c757d; font-size: 16px; text-transform: uppercase; }
        .card p { font-size: 28px; font-weight: bold; color: #212529; margin-bottom: 0; }
    </style>
</head>
<body>

    <div class="navbar">
        <h2>🏢 Koperasi Ramdan (Petugas)</h2>
        <div class="nav-links">
            <a href="dashboard_petugas.php" style="background-color: #146c43;">Dashboard</a>
            <a href="data_anggota.php">Data Anggota</a>
            <a href="data_simpanan.php">Simpanan</a>
            <a href="data_pinjaman.php">Pinjaman</a>
            <a href="data_angsuran.php">Angsuran</a>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </div>

    <div class="container">
        <h2>Selamat bertugas, <?php echo $_SESSION['nama']; ?>! 👋</h2>
        <p>Akses operasional harian koperasi:</p>
        
        <div class="card-container">
            <div class="card">
                <h3>👥 Total Anggota Koperasi</h3>
                <p><?php echo $data_anggota['total_anggota'] ? $data_anggota['total_anggota'] : 0; ?> Orang</p>
            </div>
        </div>
    </div>

</body>
</html>