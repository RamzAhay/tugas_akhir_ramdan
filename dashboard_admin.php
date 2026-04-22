<!DOCTYPE html>
<html>
<?php
include 'role_admin.php';
include 'koneksi.php';

$query_anggota = mysqli_query($koneksi, "SELECT COUNT(id_anggota) as total_anggota FROM tb_anggota_ramdan");
$data_anggota = mysqli_fetch_assoc($query_anggota);

$query_simpanan = mysqli_query($koneksi, "SELECT SUM(jumlah) as total_simpanan FROM tb_simpanan_ramdan");
$data_simpanan = mysqli_fetch_assoc($query_simpanan);

$query_pinjaman = mysqli_query($koneksi, "SELECT SUM(jumlah_pinjaman) as total_pinjaman FROM tb_pinjaman_ramdan");
$data_pinjaman = mysqli_fetch_assoc($query_pinjaman);

// Panggil Header
include 'header.php';
?>

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