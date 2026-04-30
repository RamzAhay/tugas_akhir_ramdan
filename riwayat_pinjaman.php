<?php
include 'auth.php';
include 'koneksi.php';
include 'header.php';

// Menangkap filter ID Anggota jika ada (berguna jika Admin ingin melihat riwayat 1 orang saja)
$id_anggota_filter = isset($_GET['id_anggota']) ? $_GET['id_anggota'] : '';
$role_user = $_SESSION['role'];
?>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Riwayat Pinjaman Anggota</h2>
            <p class="text-muted small mb-0">Melacak seluruh rekam jejak pengajuan pinjaman (Disetujui, Lunas, & Ditolak).</p>
        </div>
        <a href="cetak_pinjaman.php<?php echo $id_anggota_filter ? '?id_anggota='.$id_anggota_filter : ''; ?>" class="btn btn-secondary shadow-sm" target="_blank">
            <i class="bi bi-printer"></i> 🖨️ Cetak Laporan
        </a>
    </div>

    <!-- FITUR FILTER: Hanya muncul untuk Admin & Petugas -->
    <?php if ($role_user != 'Anggota'): ?>
    <div class="card mb-4 shadow-sm border-0 bg-light">
        <div class="card-body">
            <form method="GET" action="riwayat_pinjaman.php" class="row g-3 align-items-center">
                <div class="col-auto">
                    <label class="fw-bold text-dark">Filter Anggota:</label>
                </div>
                <div class="col-auto">
                    <select name="id_anggota" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Semua Anggota --</option>
                        <?php
                        $q_anggota = mysqli_query($koneksi, "SELECT * FROM tb_anggota_ramdan ORDER BY nama ASC");
                        while ($d = mysqli_fetch_assoc($q_anggota)) {
                            $selected = ($d['id_anggota'] == $id_anggota_filter) ? 'selected' : '';
                            echo "<option value='".$d['id_anggota']."' $selected>".$d['nama']."</option>";
                        }
                        ?>
                    </select>
                </div>
                <?php if($id_anggota_filter): ?>
                <div class="col-auto">
                    <a href="riwayat_pinjaman.php" class="btn btn-outline-danger btn-sm">Reset</a>
                </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- Tabel Data Riwayat -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th width="5%" class="text-center py-3">No</th>
                            <th class="py-3">Nama Anggota</th>
                            <th class="py-3">Tanggal Pengajuan</th>
                            <th class="py-3">Jumlah Pengajuan</th>
                            <th class="py-3">Total (+Bunga)</th>
                            <th class="py-3">Status Akhir</th>
                            <th class="text-center py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        
                        /** * FIX BUG & ENHANCEMENT:
                         * Di halaman ini, kita TAMPILKAN SEMUA riwayat KECUALI yang masih 'Diajukan'
                         * (Karena yang 'Diajukan' itu masih ngambang, tempatnya di Data Pinjaman Aktif).
                         * Jadi yang masuk ke sini adalah: 'Disetujui', 'Lunas', dan 'Ditolak'.
                         */
                        
                        $sql = "SELECT p.*, a.nama 
                                FROM tb_pinjaman_ramdan p 
                                JOIN tb_anggota_ramdan a ON p.id_anggota = a.id_anggota 
                                WHERE p.status_pinjaman != 'Diajukan'";

                        // Jika ada filter anggota
                        if ($id_anggota_filter != '') {
                            $sql .= " AND p.id_anggota = '$id_anggota_filter'";
                        }
                        
                        // Urutkan dari yang terbaru
                        $sql .= " ORDER BY p.id_pinjaman DESC";
                                      
                        $query = mysqli_query($koneksi, $sql);

                        if (mysqli_num_rows($query) == 0) {
                            echo "<tr><td colspan='7' class='text-center py-5 text-muted'>Belum ada riwayat pinjaman untuk ditampilkan.</td></tr>";
                        }

                        while ($data = mysqli_fetch_assoc($query)) {
                            $status = $data['status_pinjaman'];
                            
                            // Penentuan warna badge status
                            if ($status == 'Lunas') {
                                $badge_class = 'bg-success';
                            } elseif ($status == 'Ditolak') {
                                $badge_class = 'bg-danger';
                            } else {
                                $badge_class = 'bg-primary'; // Untuk yang sedang 'Disetujui' (berjalan)
                            }
                        ?>
                        <tr>
                            <td class="text-center text-muted"><?php echo $no++; ?></td>
                            <td class="fw-bold"><?php echo htmlspecialchars($data['nama']); ?></td>
                            <td><?php echo date('d M Y', strtotime($data['tanggal_pinjaman'])); ?></td>
                            <td>Rp <?php echo number_format($data['jumlah_pinjaman'], 0, ',', '.'); ?></td>
                            <td>Rp <?php echo number_format($data['total_pinjaman'], 0, ',', '.'); ?></td>
                            <td><span class="badge <?php echo $badge_class; ?> px-3 py-2"><?php echo $status; ?></span></td>
                            <td class="text-center">
                                <!-- Tombol detail hanya relevan untuk yang pernah disetujui (punya angsuran) -->
                                <?php if($status != 'Ditolak'): ?>
                                    <a href="riwayat_angsuran.php?id=<?php echo $data['id_pinjaman']; ?>" class="btn btn-outline-info btn-sm fw-bold">Lihat Angsuran</a>
                                <?php else: ?>
                                    <span class="text-muted small"><em>Tidak ada detail</em></span>
                                <?php endif; ?>
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