<?php
include 'auth.php';
include 'koneksi.php';
include 'header.php';
?>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Data Pembayaran Angsuran</h2>
            <p class="text-muted small mb-0">Kelola dan pantau pembayaran cicilan anggota yang disetujui.</p>
        </div>
        <?php if ($_SESSION['role'] != 'Anggota'): ?>
            <a href="tambah_angsuran.php" class="btn btn-primary shadow-sm">+ Input Pembayaran</a>
        <?php endif; ?>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th width="5%" class="text-center py-3">No</th>
                            <th class="py-3">Nama Anggota</th>
                            <th class="py-3">Tanggal ACC</th>
                            <th class="py-3">Total Pinjaman</th>
                            <th class="py-3">Sisa Hutang</th>
                            <th class="py-3">Status</th>
                            <th class="text-center py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        
                        /** * FIX BUG LOGIKA KEUANGAN: 
                         * Jangan pernah tampilkan pinjaman yang berstatus 'Diajukan' atau 'Ditolak'
                         * di halaman angsuran ini. Hanya tampilkan yang valid!
                         */
                        $query = mysqli_query($koneksi, "SELECT p.*, a.nama 
                                                         FROM tb_pinjaman_ramdan p 
                                                         JOIN tb_anggota_ramdan a ON p.id_anggota = a.id_anggota 
                                                         WHERE p.status_pinjaman NOT IN ('Diajukan', 'Ditolak')
                                                         ORDER BY p.sisa_pinjaman DESC, p.id_pinjaman DESC");
                        
                        if (mysqli_num_rows($query) == 0) {
                            echo "<tr><td colspan='7' class='text-center py-5 text-muted'>Belum ada data pinjaman yang disetujui untuk diangsur.</td></tr>";
                        }

                        while ($data = mysqli_fetch_assoc($query)) {
                            $status = $data['status_pinjaman'];
                            $badge_class = ($status == 'Lunas') ? 'bg-success' : 'bg-primary';
                        ?>
                        <tr>
                            <td class="text-center text-muted"><?php echo $no++; ?></td>
                            <td class="fw-bold"><?php echo htmlspecialchars($data['nama']); ?></td>
                            <td><?php echo date('d M Y', strtotime($data['tanggal_pinjaman'])); ?></td>
                            <td>Rp <?php echo number_format($data['total_pinjaman'], 0, ',', '.'); ?></td>
                            <td class="fw-bold text-danger">Rp <?php echo number_format($data['sisa_pinjaman'], 0, ',', '.'); ?></td>
                            <td><span class="badge <?php echo $badge_class; ?>"><?php echo $status; ?></span></td>
                            <td class="text-center">
                                <div class="btn-group gap-1">
                                    <a href="riwayat_angsuran.php?id=<?php echo $data['id_pinjaman']; ?>" class="btn btn-info btn-sm text-white">Lihat Riwayat</a>
                                    
                                    <?php if($status != 'Lunas' && $_SESSION['role'] != 'Anggota'): ?>
                                        <a href="tambah_angsuran.php?id=<?php echo $data['id_pinjaman']; ?>" class="btn btn-success btn-sm">Bayar</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>