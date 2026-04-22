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
?>

<?php include 'header.php'; ?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Data Angsuran Pinjaman</h3>
        <div>
            <a href="tambah_angsuran.php" class="btn btn-primary">➕ Tambah Angsuran</a>
            <a href="cetak_angsuran.php" target="_blank" class="btn btn-danger">🖨️ Cetak PDF</a>
        </div>
    </div>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Nama Anggota</th>
                <th>Total Pinjaman</th>
                <th>Total Dibayar</th>
                <th>Sisa Hutang</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            include 'koneksi.php';
            $no = 1;
            
            // Query cerdas: Gabung 3 tabel & jumlahkan pembayaran otomatis
            $query = mysqli_query($koneksi, "
                SELECT p.id_pinjaman, ang.nama, p.total_pinjaman, 
                       IFNULL(SUM(ans.jumlah_bayar), 0) as total_dibayar 
                FROM tb_pinjaman_ramdan p 
                JOIN tb_anggota_ramdan ang ON p.id_anggota = ang.id_anggota 
                LEFT JOIN tb_angsuran_ramdan ans ON p.id_pinjaman = ans.id_pinjaman 
                GROUP BY p.id_pinjaman
            ");

            while($data = mysqli_fetch_assoc($query)){ 
                $sisa = $data['total_pinjaman'] - $data['total_dibayar'];
            ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo $data['nama']; ?></td>
                <td>Rp <?php echo number_format($data['total_pinjaman'], 0, ',', '.'); ?></td>
                <td>Rp <?php echo number_format($data['total_dibayar'], 0, ',', '.'); ?></td>
                <td>
                    <?php 
                    if($sisa <= 0) {
                        echo "<span class='badge bg-success'>Lunas</span>";
                    } else {
                        echo "Rp " . number_format($sisa, 0, ',', '.'); 
                    }
                    ?>
                </td>
                <td>
                    <?php 
                    if($sisa <= 0) {
                        echo "<span class='badge bg-success'>✅ Lunas</span>";
                    } else {
                        echo "<span class='badge bg-warning text-dark'>⏳ Belum Lunas</span>";
                    }
                    ?>
                </td>
                <td>
                    <a href="riwayat_angsuran.php?id=<?php echo $data['id_pinjaman']; ?>" class="btn btn-info btn-sm text-white">
                        👁️ Riwayat
                    </a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>
</body>
</html>