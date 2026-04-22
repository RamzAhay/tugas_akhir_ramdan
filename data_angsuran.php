<!DOCTYPE html>
<html>
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

// Panggil Header
include 'header.php';
?>

    <h2>Data Pembayaran Angsuran</h2>
    <a href="tambah_angsuran.php" class="btn" style="margin-bottom: 15px;">+ Catat Pembayaran Angsuran</a>
    <a href="cetak_angsuran.php" target="_blank" class="btn btn-warning" style="margin-left: 10px;">🖨️ Cetak Laporan</a>

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

</div>
</body>
</html>