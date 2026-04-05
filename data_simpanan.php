<?php
include 'auth.php';
include 'koneksi.php';

$query = mysqli_query($koneksi, "
    SELECT s.*, a.nama 
    FROM tb_simpanan_ramdan s
    JOIN tb_anggota_ramdan a ON s.id_anggota = a.id_anggota
    ORDER BY s.id_simpanan DESC
");

// Panggil Header
include 'header.php';
?>

    <h2>Data Simpanan Anggota</h2>
    <a href="tambah_simpanan.php" class="btn" style="margin-bottom: 15px;">+ Catat Simpanan Baru</a>

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
                <td>Rp <?php echo number_format($data['jumlah'], 0, ',', '.'); ?></td>
                <td><?php echo date('d-m-Y', strtotime($data['tanggal'])); ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

</div>
</body>
</html>