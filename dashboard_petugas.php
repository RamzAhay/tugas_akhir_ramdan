<?php
include 'role_petugas.php';
include 'koneksi.php'; 

$query_anggota = mysqli_query($koneksi, "SELECT COUNT(id_anggota) as total_anggota FROM tb_anggota_ramdan");
$data_anggota = mysqli_fetch_assoc($query_anggota);

// Panggil Header
include 'header.php';
?>

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