<?php 
include 'auth.php';
include 'header.php'; 
include 'koneksi.php';

// Tangkap ID Pinjaman dari URL
$id_pinjaman = $_GET['id'];

// Ambil info nama anggota untuk judul halaman
$query_info = mysqli_query($koneksi, "
    SELECT p.total_pinjaman, a.nama 
    FROM tb_pinjaman_ramdan p 
    JOIN tb_anggota_ramdan a ON p.id_anggota = a.id_anggota 
    WHERE p.id_pinjaman = '$id_pinjaman'
");
$info = mysqli_fetch_assoc($query_info);
?>

<div class="container mt-4">
    <h3>Riwayat Angsuran: <strong><?php echo $info['nama']; ?></strong></h3>
    <h5 class="mb-4 text-muted">Total Pinjaman: Rp <?php echo number_format($info['total_pinjaman'], 0, ',', '.'); ?></h5>
    
    <a href="data_angsuran.php" class="btn btn-secondary mb-3">⬅️ Kembali ke Data Angsuran</a>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Tanggal Pembayaran</th>
                <th>Jumlah Bayar</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            // Ambil semua transaksi angsuran berdasarkan id_pinjaman
            $query_riwayat = mysqli_query($koneksi, "
                SELECT * FROM tb_angsuran_ramdan 
                WHERE id_pinjaman = '$id_pinjaman' 
                ORDER BY tanggal_bayar ASC
            ");

            if(mysqli_num_rows($query_riwayat) > 0) {
                while($riwayat = mysqli_fetch_assoc($query_riwayat)){ 
            ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo date('d-m-Y', strtotime($riwayat['tanggal_bayar'])); ?></td>
                <td>Rp <?php echo number_format($riwayat['jumlah_bayar'], 0, ',', '.'); ?></td>
                <td>
                    <a href="cetak_struk_angsuran.php?id=<?php echo $riwayat['id_angsuran']; ?>" target="_blank" class="btn btn-success btn-sm">
                        📄 Cetak Struk
                    </a>
                </td>
            </tr>
            <?php 
                } 
            } else {
                echo "<tr><td colspan='4' class='text-center'>Anggota ini belum pernah melakukan pembayaran angsuran.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>