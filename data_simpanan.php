<?php
include 'auth.php';
include 'koneksi.php';

// Kita pakai teknik JOIN untuk menggabungkan tabel simpanan dan anggota
// supaya kita bisa menampilkan 'nama' anggota, bukan cuma 'id_anggota'
$query = mysqli_query($koneksi, "
    SELECT s.*, a.nama 
    FROM tb_simpanan_ramdan s
    JOIN tb_anggota_ramdan a ON s.id_anggota = a.id_anggota
    ORDER BY s.id_simpanan DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Simpanan Koperasi</title>
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 10px; text-align: left; }
        th { background-color: #d1e7dd; }
        .btn { padding: 5px 10px; text-decoration: none; border: 1px solid black; background: #eee; color: black; }
    </style>
</head>
<body>

    <h2>Data Simpanan Anggota</h2>
    
    <?php if($_SESSION['role'] == 'Admin') { ?>
        <a href="dashboard_admin.php" class="btn">Kembali ke Dashboard</a>
    <?php } else { ?>
        <a href="dashboard_petugas.php" class="btn">Kembali ke Dashboard</a>
    <?php } ?>

    <br><br>
    <a href="tambah_simpanan.php" class="btn">+ Catat Simpanan Baru</a>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Anggota</th>
                <th>Jenis Simpanan</th>
                <th>Jumlah (Rp)</th>
                <th>Tanggal Transaksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while($data = mysqli_fetch_assoc($query)) { 
            ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo $data['nama']; ?></td>
                <td><?php echo $data['jenis_simpanan']; ?></td>
                <td><?php echo number_format($data['jumlah'], 0, ',', '.'); ?></td>
                <td><?php echo date('d-m-Y', strtotime($data['tanggal'])); ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

</body>
</html>