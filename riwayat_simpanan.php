<?php
include 'auth.php';
include 'koneksi.php';
include 'header.php';

$role_user = $_SESSION['role'];

// 1. Menangkap Filter dari URL
$id_anggota_filter = isset($_GET['id_anggota']) ? mysqli_real_escape_string($koneksi, $_GET['id_anggota']) : '';
$jenis_filter = isset($_GET['jenis_simpanan']) ? mysqli_real_escape_string($koneksi, $_GET['jenis_simpanan']) : '';
$tgl_awal = isset($_GET['tgl_awal']) ? mysqli_real_escape_string($koneksi, $_GET['tgl_awal']) : '';
$tgl_akhir = isset($_GET['tgl_akhir']) ? mysqli_real_escape_string($koneksi, $_GET['tgl_akhir']) : '';

// Jika login sebagai Anggota, kunci filter hanya untuk dirinya sendiri
if ($role_user == 'Anggota') {
    $id_user = $_SESSION['id_user'];
    $q_u = mysqli_query($koneksi, "SELECT id_anggota FROM tb_user_ramdan WHERE id_user = '$id_user'");
    $d_u = mysqli_fetch_assoc($q_u);
    $id_anggota_filter = $d_u['id_anggota'];
}

// 2. Ambil Statistik (Global atau Spesifik Anggota berdasarkan filter)
$where_stat = "WHERE 1=1";
if ($id_anggota_filter != '') $where_stat .= " AND id_anggota = '$id_anggota_filter'";
if ($jenis_filter != '') $where_stat .= " AND jenis_simpanan = '$jenis_filter'";

// Total Masuk (Setoran)
$q_in = mysqli_query($koneksi, "SELECT SUM(jumlah) as total FROM tb_simpanan_ramdan $where_stat AND jumlah > 0");
$d_in = mysqli_fetch_assoc($q_in);
$total_masuk = $d_in['total'] ?? 0;

// Total Keluar (Penarikan)
$q_out = mysqli_query($koneksi, "SELECT SUM(jumlah) as total FROM tb_simpanan_ramdan $where_stat AND jumlah < 0");
$d_out = mysqli_fetch_assoc($q_out);
$total_keluar = abs($d_out['total'] ?? 0);

// Saldo Bersih
$saldo_akhir = $total_masuk - $total_keluar;

// Build URL Cetak Laporan (PDF Global)
$params_cetak = http_build_query([
    'id_anggota' => $id_anggota_filter,
    'jenis_simpanan' => $jenis_filter,
    'tgl_awal' => $tgl_awal,
    'tgl_akhir' => $tgl_akhir
]);
?>

<style>
    :root {
        --ksp-slate: #334155;
        --ksp-blue: #0d6efd;
        --ksp-slate-light: #f8fafc;
        --ksp-border: #e2e8f0;
    }

    /* KARTU STATISTIK PROFESIONAL */
    .stat-card {
        background: white;
        border: 1px solid var(--ksp-border);
        border-radius: 12px;
        padding: 20px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    .stat-label { font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
    .stat-value { font-size: 1.5rem; font-weight: 800; color: #1e293b; }

    /* FILTER SECTION */
    .f-section {
        background: var(--ksp-slate-light); 
        border: 1px solid var(--ksp-border);
        border-radius: 12px;
        padding: 20px; 
        margin-bottom: 25px;
    }
    .f-container { display: flex; flex-wrap: wrap; gap: 15px; align-items: flex-end; }
    .f-item { flex: 1; min-width: 150px; }
    .f-label { font-size: 11px; font-weight: 700; color: #475569; margin-bottom: 8px; display: block; text-transform: uppercase; }
    .f-control {
        display: block; width: 100%; height: 40px; padding: 5px 12px;
        border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px;
        background-color: #fff;
    }

    /* BUTTONS */
    .btn-action {
        height: 40px; padding: 0 20px; font-weight: 600; font-size: 13px;
        border-radius: 8px; border: none; cursor: pointer;
        display: flex; align-items: center; justify-content: center; text-decoration: none !important;
    }
    .btn-filter { background: var(--ksp-slate); color: white; }
    .btn-filter:hover { background: #1e293b; color: white; }
    .btn-print { background: #f1f5f9; color: var(--ksp-slate); border: 1px solid #e2e8f0; }
    .btn-print:hover { background: #e2e8f0; color: var(--ksp-slate); }
    
    .btn-reset-icon {
        height: 40px; width: 40px; background: white; color: #ef4444;
        border: 1px solid #fed7d7; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
    }

    /* TABLE HEAD BLUE STYLE */
    .table-ksp-head {
        background: var(--ksp-blue) !important;
        color: white !important;
    }
    .table-ksp-head th {
        font-weight: 600 !important;
        font-size: 12px !important;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: none !important;
    }
</style>

<div class="content">
    <div class="mb-4">
        <h3 class="fw-bold text-dark mb-1">Riwayat Mutasi Simpanan</h3>
        <p class="text-muted small">
            <?php echo ($id_anggota_filter == '') ? 'Menampilkan akumulasi seluruh transaksi kas koperasi.' : 'Menampilkan histori keuangan personal anggota yang dipilih.'; ?>
        </p>
    </div>

    <!-- Statistik Dinamis -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card border-start border-4 border-success">
                <div class="stat-label">Total Uang Masuk (+)</div>
                <div class="stat-value text-success"><?php echo rupiah($total_masuk); ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card border-start border-4 border-danger">
                <div class="stat-label">Total Uang Keluar (-)</div>
                <div class="stat-value text-danger"><?php echo rupiah($total_keluar); ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card border-start border-4 border-primary">
                <div class="stat-label">Saldo Bersih / Kas Tersedia</div>
                <div class="stat-value text-primary"><?php echo rupiah($saldo_akhir); ?></div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="f-section shadow-sm">
        <form method="GET" action="riwayat_simpanan.php">
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

                <div class="f-item">
                    <span class="f-label">Kategori</span>
                    <select name="jenis_simpanan" class="f-control">
                        <option value="">-- Semua Jenis --</option>
                        <option value="Pokok" <?php echo ($jenis_filter == 'Pokok') ? 'selected' : ''; ?>>Pokok</option>
                        <option value="Wajib" <?php echo ($jenis_filter == 'Wajib') ? 'selected' : ''; ?>>Wajib</option>
                        <option value="Sukarela" <?php echo ($jenis_filter == 'Sukarela') ? 'selected' : ''; ?>>Sukarela</option>
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

                <div class="d-flex gap-2">
                    <button type="submit" class="btn-action btn-filter">FILTER</button>
                    
                    <a href="cetak_simpanan.php?<?php echo $params_cetak; ?>" 
                       class="btn-action btn-print" target="_blank" title="Cetak Laporan Rekap">
                        <i class="bi bi-printer me-2"></i> Laporan
                    </a>

                    <?php if($id_anggota_filter != '' || $jenis_filter != '' || $tgl_awal != '' || $tgl_akhir != ''): ?>
                        <a href="riwayat_simpanan.php" class="btn-reset-icon" title="Reset Filter">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    <?php endif; ?>
                </div>

            </div>
        </form>
    </div>

    <!-- Mutasi Table -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-ksp-head">
                        <tr>
                            <th class="text-center py-3" width="60">NO</th>
                            <th class="py-3 text-center">TANGGAL</th>
                            <th class="py-3">NAMA ANGGOTA</th>
                            <th class="py-3">KETERANGAN TRANSAKSI</th>
                            <!-- Kolom baru: METODE -->
                            <th class="py-3 text-center">METODE</th>
                            <th class="py-3 text-end px-4">NOMINAL (RP)</th>
                            <th class="py-3 text-center">STRUK</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $sql = "SELECT s.*, a.nama FROM tb_simpanan_ramdan s 
                                JOIN tb_anggota_ramdan a ON s.id_anggota = a.id_anggota 
                                WHERE 1=1";

                        // Terapkan Filter
                        if ($id_anggota_filter != '') $sql .= " AND s.id_anggota = '$id_anggota_filter'";
                        if ($jenis_filter != '') $sql .= " AND s.jenis_simpanan = '$jenis_filter'";
                        
                        if ($tgl_awal != '' && $tgl_akhir != '') {
                            $sql .= " AND s.tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'";
                        } elseif ($tgl_awal != '') {
                            $sql .= " AND s.tanggal >= '$tgl_awal'";
                        } elseif ($tgl_akhir != '') {
                            $sql .= " AND s.tanggal <= '$tgl_akhir'";
                        }

                        // URUTAN DATA: Terbaru selalu di atas
                        $sql .= " ORDER BY s.tanggal DESC, s.id_simpanan DESC";
                        $query = mysqli_query($koneksi, $sql);

                        if (mysqli_num_rows($query) == 0) {
                            echo "<tr><td colspan='7' class='text-center py-5 text-muted'>Tidak ada histori transaksi ditemukan.</td></tr>";
                        }

                        while ($row = mysqli_fetch_assoc($query)) {
                            $is_plus = $row['jumlah'] > 0;
                        ?>
                        <tr>
                            <td class="text-center text-muted small"><?php echo $no++; ?></td>
                            <td class="text-center small"><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                            <td class="fw-bold small text-uppercase text-dark"><?php echo htmlspecialchars($row['nama']); ?></td>
                            <td>
                                <span class="badge <?php echo $is_plus ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'; ?> rounded-pill border px-3">
                                    <?php echo $is_plus ? 'SETORAN: '.$row['jenis_simpanan'] : 'PENARIKAN TUNAI'; ?>
                                </span>
                            </td>
                            
                            <!-- DATA METODE PEMBAYARAN -->
                            <td class="text-center">
                                <?php 
                                $metode = (isset($row['metode_pembayaran']) && $row['metode_pembayaran'] != '') ? $row['metode_pembayaran'] : 'Tunai'; 
                                
                                if ($metode == 'Transfer') {
                                    echo '<span class="badge bg-info text-dark px-3 py-2 rounded-pill"><i class="bi bi-bank me-1"></i> Transfer</span>';
                                } else {
                                    echo '<span class="badge bg-secondary px-3 py-2 rounded-pill"><i class="bi bi-cash-coin me-1"></i> Tunai</span>';
                                }
                                ?>
                            </td>

                            <td class="text-end px-4 fw-bold <?php echo $is_plus ? 'text-success' : 'text-danger'; ?>">
                                <?php echo ($is_plus ? '+' : '-') . ' ' . rupiah(abs($row['jumlah'])); ?>
                            </td>
                            <td class="text-center">
                                <!-- TOMBOL CETAK STRUK PER TRANSAKSI -->
                                <a href="cetak_struk_simpanan.php?id=<?php echo $row['id_simpanan']; ?>" target="_blank" class="btn btn-sm btn-light border shadow-sm" title="Cetak Struk Transaksi">
                                    <i class="bi bi-printer text-muted"></i>
                                </a>
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