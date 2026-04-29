<!DOCTYPE html>
<html>
<?php
include 'role_admin.php';
include 'koneksi.php';

// 1. Hitung Total Anggota
$query_anggota = mysqli_query($koneksi, "SELECT COUNT(id_anggota) as total_anggota FROM tb_anggota_ramdan");
$data_anggota = mysqli_fetch_assoc($query_anggota);

// 2. Hitung Total Pinjaman REAL-TIME (Hanya yang BELUM LUNAS)
// Kita filter agar 'Lunas' tidak ikut terhitung
$query_pinjaman = mysqli_query($koneksi, "SELECT SUM(total_pinjaman) as total_pinjaman FROM tb_pinjaman_ramdan WHERE status_pinjaman != 'Lunas'");
$data_pinjaman = mysqli_fetch_assoc($query_pinjaman);
$total_pinjaman_aktif = $data_pinjaman['total_pinjaman'] ? $data_pinjaman['total_pinjaman'] : 0;

// 3. Hitung Total Simpanan
$query_simpanan = mysqli_query($koneksi, "SELECT SUM(jumlah) as total_simpanan FROM tb_simpanan_ramdan");
$data_simpanan = mysqli_fetch_assoc($query_simpanan);
$total_simpanan = $data_simpanan['total_simpanan'] ? $data_simpanan['total_simpanan'] : 0;

include 'header.php';
?>

<div class="content">
    <h2>Dashboard Admin</h2>
    <p>Selamat datang, <strong><?php echo $_SESSION['nama']; ?></strong>! Berikut adalah ringkasan data koperasi hari ini.</p>

    <div class="dashboard-cards">
        <div class="card bg-primary">
            <h3>Total Anggota</h3>
            <p><?php echo $data_anggota['total_anggota']; ?> Orang</p>
        </div>
        <div class="card bg-success">
            <h3>Total Simpanan</h3>
            <p>Rp <?php echo number_format($total_simpanan, 0, ',', '.'); ?></p>
        </div>
        <div class="card bg-warning">
            <h3>Total Pinjaman Aktif</h3>
            <p>Rp <?php echo number_format($total_pinjaman_aktif, 0, ',', '.'); ?></p>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
</html>