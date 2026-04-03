<?php
include 'auth.php';
include 'koneksi.php';

$query = mysqli_query($koneksi, "
    SELECT ans.*, p.total_pinjaman, a.nama 
    FROM tb_angsuran_ramdan ans
    JOIN tb_pinjaman_ramdan p ON ans.id_pinjaman = p.id_pinjaman
    JOIN tb_anggota_ramdan a ON p.id_anggota = a.id_anggota
    ORDER BY ans.id_angsuran ASC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Angsuran</title>
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 10px; text-align: left; }
        th { background-color: #cfe2ff; } /* Warna biru muda */
        .btn { padding: 5px 10px; text-decoration: none; border: 1px solid black; background: #eee; color: black; }
    </style>
</head>
<body>

    <h2>Data Pembayaran Angsuran</h2>
    
    <?php if($_SESSION['role'] == 'Admin') { ?>
        <a href="dashboard_admin.php" class="btn">Kembali ke Dashboard</a>
    <?php } else { ?>
        <a href="dashboard_petugas.php" class="btn">Kembali ke Dashboard</a>
    <?php } ?>

    <br><br>
    <a href="tambah_angsuran.php" class="btn">+ Catat Pembayaran Angsuran</a>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Anggota</th>
                <th>Tanggal Bayar</th>
                <th>Jumlah Bayar</th>
                <th>Sisa Hutang</th>
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
                <td><?php echo date('d-m-Y', strtotime($data['tanggal_bayar'])); ?></td>
                <td>Rp <?php echo number_format($data['jumlah_bayar'], 0, ',', '.'); ?></td>
                <td>Rp <?php echo number_format($data['sisa_pinjaman'], 0, ',', '.'); ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

</body>
</html>