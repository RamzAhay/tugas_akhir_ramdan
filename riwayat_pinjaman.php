<?php
include 'auth.php';
include 'koneksi.php';
include 'header.php';

$role_user = isset($_SESSION['role']) ? $_SESSION['role'] : '';

// Menangkap Filter
$id_anggota_filter = isset($_GET['id_anggota']) ? mysqli_real_escape_string($koneksi, $_GET['id_anggota']) : '';
$tgl_awal = isset($_GET['tgl_awal']) ? mysqli_real_escape_string($koneksi, $_GET['tgl_awal']) : '';
$tgl_akhir = isset($_GET['tgl_akhir']) ? mysqli_real_escape_string($koneksi, $_GET['tgl_akhir']) : '';
$filter_tipe = isset($_GET['filter_tipe']) ? mysqli_real_escape_string($koneksi, $_GET['filter_tipe']) : 'pinjam';

// Bangun parameter untuk link Cetak agar sinkron dengan filter
$params_cetak = http_build_query([
    'id_anggota' => $id_anggota_filter,
    'tgl_awal' => $tgl_awal,
    'tgl_akhir' => $tgl_akhir,
    'filter_tipe' => $filter_tipe
]);
?>

<style>
    /* COMPACT FILTER STYLE - Konsisten dengan halaman lain */
    .f-section {
        background: #f8fafc; 
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 15px 20px; 
        margin-bottom: 20px;
    }

    .f-container {
        display: flex !important;
        flex-wrap: wrap;
        gap: 12px;
        align-items: flex-end !important;
    }

    .f-item {
        flex: 1;
        min-width: 150px;
        margin-bottom: 0 !important;
    }

    .f-control {
        display: block !important;
        width: 100% !important;
        height: 36px !important; 
        margin: 0 !important;    
        padding: 5px 10px !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 6px !important;
        font-size: 13px !important;
        background-color: #ffffff !important;
    }

    .f-label {
        font-size: 10px !important;
        font-weight: 700 !important;
        color: #475569 !important;
        text-transform: uppercase;
        margin-bottom: 5px !important;
        display: block !important;
        letter-spacing: 0.3px;
    }

    .btn-action-group {
        display: flex;
        gap: 5px;
        margin-bottom: 0 !important;
    }

    .btn-f-cari, .btn-f-print {
        height: 36px !important;
        padding: 0 20px !important;
        background: #334155 !important; 
        color: white !important;
        border: none !important;
        border-radius: 6px !important;
        font-weight: 600 !important;
        font-size: 12px !important;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none !important;
    }

    .btn-f-print:hover, .btn-f-cari:hover {
        background: #1e293b !important;
        color: white !important;
    }

    .btn-f-reset {
        height: 36px !important;
        width: 36px !important;
        background: #ffffff !important;
        color: #ef4444 !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 6px !important;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }

    @media (max-width: 768px) {
        .f-item { flex: 1 1 100%; }
    }
</style>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-0">Riwayat Pinjaman Selesai</h4>
            <p class="text-muted small mb-0">Daftar seluruh pengajuan pinjaman yang sudah Lunas, Disetujui, atau Ditolak.</p>
        </div>
    </div>

    <!-- AREA FILTER UNIFIED -->
    <div class="f-section shadow-sm">
        <form method="GET" action="riwayat_pinjaman.php">
            <div class="f-container">
                
                <?php if ($role_user != 'Anggota'): ?>
                <div class="f-item">
                    <span class="f-label">Anggota</span>
                    <select name="id_anggota" class="f-control">
                        <option value="">-- Semua Anggota --</option>
                        <?php
                        $q_opt = mysqli_query($koneksi, "SELECT * FROM tb_anggota_ramdan ORDER BY nama ASC");
                        while($d_opt = mysqli_fetch_assoc($q_opt)) {
                            $sel = ($id_anggota_filter == $d_opt['id_anggota']) ? 'selected' : '';
                            echo "<option value='".$d_opt['id_anggota']."' $sel>".$d_opt['nama']."</option>";
                        }
                        ?>
                    </select>
                </div>
                <?php endif; ?>

                <div class="f-item" style="min-width: 180px;">
                    <span class="f-label">Filter Tanggal Berdasarkan</span>
                    <select name="filter_tipe" class="f-control">
                        <option value="pinjam" <?php echo ($filter_tipe == 'pinjam') ? 'selected' : ''; ?>>Tanggal Pinjam</option>
                        <option value="lunas" <?php echo ($filter_tipe == 'lunas') ? 'selected' : ''; ?>>Tanggal Lunas</option>
                    </select>
                </div>

                <div class="f-item">
                    <span class="f-label">Dari Tanggal</span>
                    <input type="date" name="tgl_awal" class="f-control" value="<?php echo $tgl_awal; ?>">
                </div>

                <div class="f-item">
                    <span class="f-label">Sampai Tanggal</span>
                    <input type="date" name="tgl_akhir" class="f-control" value="<?php echo $tgl_akhir; ?>">
                </div>

                <div class="btn-action-group">
                    <button type="submit" class="btn-f-cari">FILTER</button>
                    
                    <a href="cetak_riwayat_pinjaman.php?<?php echo $params_cetak; ?>" 
                       class="btn-f-print" target="_blank" title="Cetak Laporan">
                        <i class="bi bi-printer me-2"></i> CETAK
                    </a>

                    <?php if($id_anggota_filter != '' || $tgl_awal != '' || $tgl_akhir != ''): ?>
                        <a href="riwayat_pinjaman.php" class="btn-f-reset" title="Bersihkan Filter">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    <?php endif; ?>
                </div>

            </div>
        </form>
    </div>

    <!-- TABEL RIWAYAT -->
    <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center py-3 text-muted small fw-bold" width="50">NO</th>
                            <th class="py-3 small fw-bold">ANGGOTA</th>
                            <th class="py-3 small fw-bold text-center">TGL PINJAM</th>
                            <th class="py-3 small fw-bold text-center">TGL LUNAS</th>
                            <th class="py-3 small fw-bold">JML PINJAMAN</th>
                            <th class="py-3 small fw-bold">TOTAL BAYAR</th>
                            <th class="py-3 small fw-bold text-center">STATUS</th>
                            <th class="text-center py-3 small fw-bold">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        // Base Query
                        $sql = "SELECT p.*, a.nama, 
                                (SELECT MAX(tanggal_bayar) FROM tb_angsuran_ramdan WHERE id_pinjaman = p.id_pinjaman) as tanggal_lunas
                                FROM tb_pinjaman_ramdan p 
                                JOIN tb_anggota_ramdan a ON p.id_anggota = a.id_anggota 
                                WHERE p.status_pinjaman != 'Diajukan'";

                        // Filter Anggota
                        if ($id_anggota_filter != '') $sql .= " AND p.id_anggota = '$id_anggota_filter'";
                        
                        // Logika Filter Tanggal Berdasarkan Tipe (Pinjam / Lunas)
                        $kolom_tgl = ($filter_tipe == 'lunas') ? "(SELECT MAX(tanggal_bayar) FROM tb_angsuran_ramdan WHERE id_pinjaman = p.id_pinjaman)" : "p.tanggal_pinjaman";

                        if ($tgl_awal != '' && $tgl_akhir != '') {
                            $sql .= " AND $kolom_tgl BETWEEN '$tgl_awal' AND '$tgl_akhir'";
                        } elseif ($tgl_awal != '') {
                            $sql .= " AND $kolom_tgl >= '$tgl_awal'";
                        } elseif ($tgl_akhir != '') {
                            $sql .= " AND $kolom_tgl <= '$tgl_akhir'";
                        }

                        /**
                         * UPDATE ORDER BY: Tanggal Pinjam (Terbaru ke Lama)
                         * Disamakan dengan file cetak_riwayat_pinjaman.php
                         */
                        $sql .= " ORDER BY p.tanggal_pinjaman DESC";
                        $query = mysqli_query($koneksi, $sql);

                        if (mysqli_num_rows($query) == 0) {
                            echo "<tr><td colspan='8' class='text-center py-5 text-muted small'>Data tidak ditemukan untuk kriteria ini.</td></tr>";
                        }

                        while ($data = mysqli_fetch_assoc($query)) {
                            $status = $data['status_pinjaman'];
                            $badge = 'bg-primary';
                            if ($status == 'Lunas') $badge = 'bg-success';
                            if ($status == 'Ditolak') $badge = 'bg-danger';

                            // Logika tampilan tanggal lunas
                            $tgl_lunas_tampil = ($status == 'Lunas' && $data['tanggal_lunas']) ? date('d/m/Y', strtotime($data['tanggal_lunas'])) : '-';
                        ?>
                        <tr>
                            <td class="text-center text-muted small"><?php echo $no++; ?></td>
                            <td class="fw-bold"><?php echo htmlspecialchars($data['nama']); ?></td>
                            <td class="text-center small"><?php echo date('d/m/Y', strtotime($data['tanggal_pinjaman'])); ?></td>
                            <td class="text-center small fw-bold text-success"><?php echo $tgl_lunas_tampil; ?></td>
                            <td><?php echo rupiah($data['jumlah_pinjaman']); ?></td>
                            <td class="fw-bold"><?php echo rupiah($data['total_pinjaman']); ?></td>
                            <td class="text-center">
                                <span class="badge <?php echo $badge; ?> rounded-pill px-3"><?php echo $status; ?></span>
                            </td>
                            <td class="text-center">
                                <?php if($status != 'Ditolak'): ?>
                                    <a href="riwayat_angsuran.php?id=<?php echo $data['id_pinjaman']; ?>" class="btn btn-outline-info btn-sm py-1 px-2" style="font-size: 11px;">Detail</a>
                                <?php else: ?>
                                    <span class="text-muted small">-</span>
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