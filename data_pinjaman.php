<?php
include 'auth.php';
include 'koneksi.php';
include 'header.php';

$role_user = $_SESSION['role'];
?>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Data Pinjaman Aktif</h2>
            <p class="text-muted small mb-0">Mengelola pengajuan pinjaman yang sedang berjalan atau menunggu persetujuan.</p>
        </div>
        <div class="btn-group shadow-sm">
            <a href="tambah_pinjaman.php" class="btn btn-primary">+ Tambah Pinjaman</a>
            <a href="riwayat_pinjaman.php" class="btn btn-outline-secondary">Lihat Riwayat</a>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th width="5%" class="text-center py-3">No</th>
                            <th>Nama Anggota</th>
                            <th>Tanggal Pinjam</th>
                            <th>Jml Pinjaman</th>
                            <th>Bunga & Lama</th>
                            <th>Sisa Pinjaman</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        
                        // FIX: Query ini sekarang membuang status 'Lunas' dan 'Ditolak'
                        $query = mysqli_query($koneksi, "SELECT p.*, a.nama 
                                                         FROM tb_pinjaman_ramdan p 
                                                         JOIN tb_anggota_ramdan a ON p.id_anggota = a.id_anggota 
                                                         WHERE p.status_pinjaman NOT IN ('Lunas', 'Ditolak')
                                                         ORDER BY p.id_pinjaman DESC");
                        
                        if (mysqli_num_rows($query) == 0) {
                            echo "<tr><td colspan='8' class='text-center py-5 text-muted'>Tidak ada pinjaman aktif saat ini.</td></tr>";
                        }

                        while ($data = mysqli_fetch_assoc($query)) {
                            $status = $data['status_pinjaman'];
                            $status_class = ($status == 'Diajukan') ? 'bg-warning text-dark' : 'bg-primary';
                        ?>
                        <tr>
                            <td class="text-center text-muted"><?php echo $no++; ?></td>
                            <td class="fw-bold"><?php echo htmlspecialchars($data['nama']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($data['tanggal_pinjaman'])); ?></td>
                            <td>Rp <?php echo number_format($data['jumlah_pinjaman'], 0, ',', '.'); ?></td>
                            <td><?php echo $data['bunga']; ?>% (<?php echo $data['lama_pinjaman']; ?> Bln)</td>
                            <td class="fw-bold text-danger">Rp <?php echo number_format($data['sisa_pinjaman'], 0, ',', '.'); ?></td>
                            <td><span class="badge <?php echo $status_class; ?> px-2 py-1"><?php echo $status; ?></span></td>
                            <td class="text-center">
                                <div class="btn-group gap-1">
                                    <a href="riwayat_angsuran.php?id=<?php echo $data['id_pinjaman']; ?>" class="btn btn-info btn-sm text-white">Detail</a>
                                    
                                    <?php if($status == 'Disetujui'): ?>
                                        <a href="tambah_angsuran.php?id=<?php echo $data['id_pinjaman']; ?>" class="btn btn-success btn-sm">Angsur</a>
                                    <?php endif; ?>

                                    <?php if($status == 'Diajukan'): ?>
                                        <a href="edit_pinjaman.php?id=<?php echo $data['id_pinjaman']; ?>" class="btn btn-secondary btn-sm">Edit</a>
                                        <?php if($role_user == 'Admin'): ?>
                                            <a href="acc_pinjaman.php?id=<?php echo $data['id_pinjaman']; ?>" class="btn btn-primary btn-sm fw-bold">ACC</a>
                                            <a href="tolak_pinjaman.php?id=<?php echo $data['id_pinjaman']; ?>" class="btn btn-danger btn-sm fw-bold" onclick="return confirm('Tolak pengajuan ini?');">Tolak</a>
                                        <?php endif; ?>
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