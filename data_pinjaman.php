<?php
include 'auth.php';
include 'koneksi.php';
include 'header.php';

$role_user = $_SESSION['role'];

// Menangkap filter dari URL
$filter_anggota = isset($_GET['id_anggota']) ? mysqli_real_escape_string($koneksi, $_GET['id_anggota']) : '';
$filter_status = isset($_GET['status']) ? mysqli_real_escape_string($koneksi, $_GET['status']) : '';
$filter_tgl_awal = isset($_GET['tgl_awal']) ? mysqli_real_escape_string($koneksi, $_GET['tgl_awal']) : '';
$filter_tgl_akhir = isset($_GET['tgl_akhir']) ? mysqli_real_escape_string($koneksi, $_GET['tgl_akhir']) : '';
?>

<style>
    /* THEME COLOR: Profesional Slate */
    :root {
        --ksp-theme: #334155;
        --ksp-hover: #1e293b;
        --ksp-border: #e2e8f0;
        --ksp-text-muted: #64748b;
    }

    /* COMPACT FILTER STYLE */
    .filter-section {
        background: #f8fafc; 
        border: 1px solid var(--ksp-border);
        border-radius: 10px;
        padding: 15px 20px; 
        margin-bottom: 20px;
    }

    .filter-flex-container {
        display: flex !important;
        flex-wrap: wrap;
        gap: 12px;
        align-items: flex-end !important;
    }

    .filter-item {
        flex: 1;
        min-width: 140px;
        margin-bottom: 0 !important;
    }

    .f-input-small {
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

    /* UNIFIED BUTTON STYLE */
    .btn-ksp {
        height: 36px !important;
        padding: 0 18px !important;
        background: var(--ksp-theme) !important; 
        color: white !important;
        border: none !important;
        border-radius: 6px !important;
        font-weight: 600 !important;
        font-size: 12px !important;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        text-decoration: none !important;
        cursor: pointer;
    }

    .btn-ksp:hover {
        background: var(--ksp-hover) !important;
        color: white !important;
    }

    .btn-ksp-outline {
        height: 32px;
        padding: 0 12px;
        background: transparent;
        border: 1px solid var(--ksp-theme);
        color: var(--ksp-theme);
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: 0.2s;
    }

    .btn-ksp-outline:hover {
        background: var(--ksp-theme);
        color: white;
    }

    /* Badge Minimalis */
    .badge-status {
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 5px 12px;
        border-radius: 50px;
        font-weight: 700;
    }

    .badge-pending { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .badge-active { background: #f1f5f9; color: #334155; border: 1px solid #e2e8f0; }

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
</style>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-0">Pelacakan Pinjaman Aktif</h4>
            <p class="text-muted small mb-0">Monitor piutang anggota yang sedang berjalan.</p>
        </div>
        <?php if ($role_user != 'Anggota'): ?>
            <a href="tambah_pinjaman.php" class="btn btn-dark btn-sm px-3 shadow-sm" style="background: var(--ksp-theme); border: none;">+ Pinjaman Baru</a>
        <?php endif; ?>
    </div>

    <!-- AREA FILTER -->
    <div class="filter-section shadow-sm">
        <form method="GET" action="data_pinjaman.php">
            <div class="filter-flex-container">
                
                <?php if($role_user != 'Anggota'): ?>
                <div class="filter-item">
                    <span class="small fw-bold text-muted text-uppercase mb-1 d-block">Anggota</span>
                    <select name="id_anggota" class="f-input-small">
                        <option value="">-- Semua --</option>
                        <?php
                        $q_opt = mysqli_query($koneksi, "SELECT * FROM tb_anggota_ramdan ORDER BY nama ASC");
                        while($d_opt = mysqli_fetch_assoc($q_opt)) {
                            $sel = ($filter_anggota == $d_opt['id_anggota']) ? 'selected' : '';
                            echo "<option value='".$d_opt['id_anggota']."' $sel>".$d_opt['nama']."</option>";
                        }
                        ?>
                    </select>
                </div>
                <?php endif; ?>

                <div class="filter-item">
                    <span class="small fw-bold text-muted text-uppercase mb-1 d-block">Status</span>
                    <select name="status" class="f-input-small">
                        <option value="">-- Semua Status --</option>
                        <option value="Diajukan" <?php echo ($filter_status == 'Diajukan') ? 'selected' : ''; ?>>Diajukan</option>
                        <option value="Disetujui" <?php echo ($filter_status == 'Disetujui') ? 'selected' : ''; ?>>Disetujui</option>
                    </select>
                </div>

                <div class="filter-item">
                    <span class="small fw-bold text-muted text-uppercase mb-1 d-block">Mulai</span>
                    <input type="date" name="tgl_awal" class="f-input-small" value="<?php echo $filter_tgl_awal; ?>">
                </div>

                <div class="filter-item">
                    <span class="small fw-bold text-muted text-uppercase mb-1 d-block">Sampai</span>
                    <input type="date" name="tgl_akhir" class="f-input-small" value="<?php echo $filter_tgl_akhir; ?>">
                </div>

                <div class="d-flex gap-1">
                    <button type="submit" class="btn-ksp">CARI</button>
                    
                    <?php 
                    $params_cetak = http_build_query([
                        'id_anggota' => $filter_anggota,
                        'status' => $filter_status,
                        'tgl_awal' => $filter_tgl_awal,
                        'tgl_akhir' => $filter_tgl_akhir
                    ]);
                    ?>
                    <a href="cetak_laporan_pinjaman.php?<?php echo $params_cetak; ?>" 
                       class="btn-ksp" target="_blank">
                        <i class="bi bi-printer me-2"></i> CETAK
                    </a>

                    <?php if($filter_anggota != '' || $filter_status != '' || $filter_tgl_awal != '' || $filter_tgl_akhir != ''): ?>
                        <a href="data_pinjaman.php" class="btn-f-reset" title="Bersihkan Filter">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    <?php endif; ?>
                </div>

            </div>
        </form>
    </div>

    <!-- TABEL DATA -->
    <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center py-3 small fw-bold text-muted" width="50">NO</th>
                            <th class="py-3 small fw-bold text-muted">NAMA ANGGOTA</th>
                            <th class="py-3 small fw-bold text-muted text-center">TGL PINJAM</th>
                            <th class="py-3 small fw-bold text-muted">JUMLAH</th>
                            <th class="py-3 small fw-bold text-muted">SISA HUTANG</th>
                            <th class="py-3 small fw-bold text-center text-muted">STATUS</th>
                            <th class="text-center py-3 small fw-bold text-muted">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $sql = "SELECT p.*, a.nama 
                                FROM tb_pinjaman_ramdan p 
                                JOIN tb_anggota_ramdan a ON p.id_anggota = a.id_anggota 
                                WHERE p.status_pinjaman NOT IN ('Lunas', 'Ditolak')";

                        if ($filter_anggota != '') $sql .= " AND p.id_anggota = '$filter_anggota'";
                        if ($filter_status != '') $sql .= " AND p.status_pinjaman = '$filter_status'";
                        
                        if ($filter_tgl_awal != '' && $filter_tgl_akhir != '') {
                            $sql .= " AND p.tanggal_pinjaman BETWEEN '$filter_tgl_awal' AND '$filter_tgl_akhir'";
                        } elseif ($filter_tgl_awal != '') {
                            $sql .= " AND p.tanggal_pinjaman >= '$filter_tgl_awal'";
                        } elseif ($filter_tgl_akhir != '') {
                            $sql .= " AND p.tanggal_pinjaman <= '$filter_tgl_akhir'";
                        }

                        $sql .= " ORDER BY p.id_pinjaman DESC";
                        $query = mysqli_query($koneksi, $sql);

                        if (mysqli_num_rows($query) == 0) {
                            echo "<tr><td colspan='7' class='text-center py-5 text-muted small'>Data tidak ditemukan sesuai filter.</td></tr>";
                        }

                        while ($data = mysqli_fetch_assoc($query)) {
                            $status = $data['status_pinjaman'];
                            $badge_class = ($status == 'Diajukan') ? 'badge-pending' : 'badge-active';
                        ?>
                        <tr>
                            <td class="text-center text-muted small"><?php echo $no++; ?></td>
                            <td class="fw-bold text-dark"><?php echo htmlspecialchars($data['nama']); ?></td>
                            <td class="text-center small"><?php echo date('d/m/Y', strtotime($data['tanggal_pinjaman'])); ?></td>
                            <td class="small"><?php echo rupiah($data['jumlah_pinjaman']); ?></td>
                            <td class="fw-bold text-dark"><?php echo rupiah($data['sisa_pinjaman']); ?></td>
                            <td class="text-center">
                                <span class="badge-status <?php echo $badge_class; ?>">
                                    <?php echo $status; ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="riwayat_angsuran.php?id=<?php echo $data['id_pinjaman']; ?>" class="btn-ksp-outline" title="Lihat Riwayat">Detail</a>
                                    
                                    <?php if ($data['status_pinjaman'] == 'Diajukan'): ?>
                                        
                                        <!-- CRITICAL BUG FIX: Tombol ACC & Tolak hanya untuk Admin -->
                                        <?php if ($role_user == 'Admin'): ?>
                                            <a href="acc_pinjaman.php?id=<?php echo $data['id_pinjaman']; ?>" class="btn-ksp-outline" style="border-color: #10b981; color: #10b981;" onclick="return confirm('Setujui pinjaman ini?')">ACC</a>
                                            <a href="tolak_pinjaman.php?id=<?php echo $data['id_pinjaman']; ?>" class="btn-ksp-outline" style="border-color: #ef4444; color: #ef4444;" onclick="return confirm('Tolak pinjaman ini?')">Tolak</a>
                                        <?php endif; ?>

                                        <!-- Petugas & Admin masih boleh edit sebelum di-ACC -->
                                        <?php if ($role_user == 'Admin' || $role_user == 'Petugas'): ?>
                                            <a href="edit_pinjaman.php?id=<?php echo $data['id_pinjaman']; ?>" class="btn-ksp-outline">Edit</a>
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