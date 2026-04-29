<?php
include 'auth.php';
include 'koneksi.php';
include 'header.php';
?>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Data Pinjaman Aktif</h2>
        <div class="btn-group">
            <a href="tambah_pinjaman.php" class="btn btn-primary">+ Tambah Pinjaman</a>
            <!-- Link tambahan ke riwayat agar petugas mudah pindah halaman -->
            <a href="riwayat_pinjaman.php" class="btn btn-outline-secondary">Lihat Semua Riwayat</a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Anggota</th>
                    <th>Tanggal Pinjam</th>
                    <th>Jumlah Pinjaman</th>
                    <th>Bunga (%)</th>
                    <th>Total Pinjaman</th>
                    <th>Sisa Pinjaman</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                /** * QUERY FILTER: 
                 * Kita hanya menampilkan pinjaman yang statusnya BUKAN 'Lunas'.
                 * Ini membuat halaman tetap bersih dan fokus pada pinjaman yang butuh tindakan.
                 */
                $query = mysqli_query($koneksi, "SELECT p.*, a.nama 
                                                 FROM tb_pinjaman_ramdan p 
                                                 JOIN tb_anggota_ramdan a ON p.id_anggota = a.id_anggota 
                                                 WHERE p.status_pinjaman != 'Lunas'
                                                 ORDER BY p.id_pinjaman DESC");
                
                if (mysqli_num_rows($query) == 0) {
                    echo "<tr><td colspan='9' class='text-center py-4 text-muted'>Tidak ada pinjaman aktif saat ini.</td></tr>";
                }

                while ($data = mysqli_fetch_assoc($query)) {
                    $status = $data['status_pinjaman'];
                    if ($status == 'Diajukan') {
                        $status_class = 'bg-warning text-dark';
                    } elseif ($status == 'Ditolak') {
                        $status_class = 'bg-danger';
                    } else {
                        $status_class = 'bg-primary'; // Untuk status 'Disetujui'
                    }
                ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $data['nama']; ?></td>
                    <td><?php echo date('d/m/Y', strtotime($data['tanggal_pinjaman'])); ?></td>
                    <td>Rp <?php echo number_format($data['jumlah_pinjaman'], 0, ',', '.'); ?></td>
                    <td><?php echo $data['bunga']; ?>%</td>
                    <td>Rp <?php echo number_format($data['total_pinjaman'], 0, ',', '.'); ?></td>
                    <td class="font-weight-bold text-danger">Rp <?php echo number_format($data['sisa_pinjaman'], 0, ',', '.'); ?></td>
                    <td><span class="badge <?php echo $status_class; ?>"><?php echo $status; ?></span></td>
                    <td>
                        <div class="btn-group gap-1">
                            <a href="riwayat_angsuran.php?id=<?php echo $data['id_pinjaman']; ?>" class="btn btn-info btn-sm text-white">Detail</a>
                            
                            <?php if($status == 'Disetujui'): ?>
                                <a href="tambah_angsuran.php?id=<?php echo $data['id_pinjaman']; ?>" class="btn btn-primary btn-sm">Angsur</a>
                            <?php endif; ?>

                            <a href="tarik_simpanan.php?id_anggota=<?php echo $data['id_anggota']; ?>" class="btn btn-success btn-sm">Tarik</a>
                        </div>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>