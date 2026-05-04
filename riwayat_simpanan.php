<?php
include 'auth.php';
include 'koneksi.php';
include 'header.php';

$role_user = $_SESSION['role'];

// 1. Menangkap Filter dari URL
$id_anggota_filter = isset($_GET['id_anggota']) ? mysqli_real_escape_string($koneksi, $_GET['id_anggota']) : '';
$jenis_filter = isset($_GET['jenis_simpanan']) ? mysqli_real_escape_string($koneksi, $_GET['jenis_simpanan']) : '';
$metode_filter = isset($_GET['metode']) ? mysqli_real_escape_string($koneksi, $_GET['metode']) : '';
$tgl_awal = isset($_GET['tgl_awal']) ? mysqli_real_escape_string($koneksi, $_GET['tgl_awal']) : '';
$tgl_akhir = isset($_GET['tgl_akhir']) ? mysqli_real_escape_string($koneksi, $_GET['tgl_akhir']) : '';

// Jika login sebagai Anggota, kunci filter hanya untuk dirinya sendiri
if ($role_user == 'Anggota') {
    $id_user = $_SESSION['id_user'];
    $q_u = mysqli_query($koneksi, "SELECT id_anggota FROM tb_user_ramdan WHERE id_user = '$id_user'");
    $d_u = mysqli_fetch_assoc($q_u);
    $id_anggota_filter = $d_u['id_anggota'] ?? '';
}

// 2. Bangun Query SQL Dinamis
$sql = "SELECT s.*, a.nama FROM tb_simpanan_ramdan s 
        JOIN tb_anggota_ramdan a ON s.id_anggota = a.id_anggota 
        WHERE 1=1";

if ($id_anggota_filter != '') $sql .= " AND s.id_anggota = '$id_anggota_filter'";
if ($jenis_filter != '') $sql .= " AND s.jenis_simpanan = '$jenis_filter'";
if ($metode_filter != '') $sql .= " AND s.metode_pembayaran = '$metode_filter'";

// Filter Range Tanggal
if ($tgl_awal != '' && $tgl_akhir != '') {
    $sql .= " AND s.tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'";
} elseif ($tgl_awal != '') {
    $sql .= " AND s.tanggal >= '$tgl_awal'";
} elseif ($tgl_akhir != '') {
    $sql .= " AND s.tanggal <= '$tgl_akhir'";
}

$sql .= " ORDER BY s.tanggal DESC, s.id_simpanan DESC";
$query = mysqli_query($koneksi, $sql);

// 3. Hitung Statistik Berdasarkan Filter yang Aktif
$total_masuk = 0;
$total_keluar = 0;
$q_stat = mysqli_query($koneksi, $sql);
while($st = mysqli_fetch_assoc($q_stat)){
    if($st['jumlah'] > 0) $total_masuk += $st['jumlah'];
    else $total_keluar += abs($st['jumlah']);
}
$saldo_akhir = $total_masuk - $total_keluar;

// Params untuk link cetak agar hasil PDF sama dengan di layar
$params_cetak = http_build_query([
    'id_anggota' => $id_anggota_filter,
    'jenis_simpanan' => $jenis_filter,
    'metode' => $metode_filter,
    'tgl_awal' => $tgl_awal,
    'tgl_akhir' => $tgl_akhir
]);
?>

<style>
    :root {
        --ksp-slate: #334155;
        --ksp-blue: #0d6efd;
        --ksp-border: #e2e8f0;
    }

    /* KARTU STATISTIK */
    .stat-card {
        background: white;
        border: 1px solid var(--ksp-border);
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    .stat-label { font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; }
    .stat-value { font-size: 1.4rem; font-weight: 800; }

    /* FILTER BAR */
    .f-section {
        background: #f8fafc;
        border: 1px solid var(--ksp-border);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 25px;
    }
    .f-container { display: flex; flex-wrap: wrap; gap: 15px; align-items: flex-end; }
    .f-item { flex: 1; min-width: 140px; }
    .f-label { font-size: 11px; font-weight: 700; color: #475569; margin-bottom: 8px; display: block; text-transform: uppercase; }
    .f-control {
        display: block; width: 100%; height: 42px; padding: 5px 12px;
        border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px;
        background-color: #fff;
    }

    .btn-f { height: 42px; padding: 0 20px; font-weight: 600; border-radius: 10px; border: none; cursor: pointer; transition: 0.2s; }
    .btn-f-primary { background: var(--ksp-slate); color: white; }
    .btn-f-primary:hover { background: #1e293b; }
    .btn-f-light { background: white; border: 1px solid #cbd5e1; color: #64748b; }
    .btn-f-light:hover { background: #f1f5f9; }

    /* TABLE HEAD */
    .table-ksp-head { background: var(--ksp-blue) !important; color: white !important; }
    .table-ksp-head th { font-size: 12px; text-transform: uppercase; border: none !important; padding: 15px !important; }
</style>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Riwayat Mutasi Simpanan</h3>
            <p class="text-muted small">
                <?php 
                if($tgl_awal != '' && $tgl_akhir != '') echo "Periode: <b>".date('d/m/Y', strtotime($tgl_awal))."</b> s/d <b>".date('d/m/Y', strtotime($tgl_akhir))."</b>"; 
                else echo "Kelola dan pantau histori arus kas masuk/keluar anggota."; 
                ?>
            </p>
        </div>
        <a href="cetak_simpanan.php?<?php echo $params_cetak; ?>" target="_blank" class="btn btn-dark fw-bold rounded-pill px-4 shadow-sm">
            <i class="bi bi-printer me-2"></i> LAPORAN PDF
        </a>
    </div>

    <!-- Statistik Dinamis -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card border-start border-4 border-success">
                <div class="stat-label">Total Masuk (+)</div>
                <div class="stat-value text-success"><?php echo rupiah($total_masuk); ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card border-start border-4 border-danger">
                <div class="stat-label">Total Keluar (-)</div>
                <div class="stat-value text-danger"><?php echo rupiah($total_keluar); ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card border-start border-4 border-primary">
                <div class="stat-label">Saldo Bersih Filter</div>
                <div class="stat-value text-primary"><?php echo rupiah($saldo_akhir); ?></div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="f-section shadow-sm">
        <form method="GET">
            <div class="f-container">
                <?php if ($role_user != 'Anggota'): ?>
                <div class="f-item" style="min-width: 200px;">
                    <span class="f-label">Nama Anggota</span>
                    <select name="id_anggota" class="f-control">
                        <option value="">-- Semua Anggota --</option>
                        <?php
                        $q_opt = mysqli_query($koneksi, "SELECT * FROM tb_anggota_ramdan ORDER BY nama ASC");
                        while($d_opt = mysqli_fetch_assoc($q_opt)) {
                            $sel = ($id_anggota_filter == $d_opt['id_anggota']) ? 'selected' : '';
                            echo "<option value='".$d_opt['id_anggota']."' $sel>".strtoupper($d_opt['nama'])."</option>";
                        }
                        ?>
                    </select>
                </div>
                <?php endif; ?>

                <div class="f-item">
                    <span class="f-label">Dari Tanggal</span>
                    <input type="date" name="tgl_awal" class="f-control" value="<?php echo $tgl_awal; ?>">
                </div>

                <div class="f-item">
                    <span class="f-label">Sampai Tanggal</span>
                    <input type="date" name="tgl_akhir" class="f-control" value="<?php echo $tgl_akhir; ?>">
                </div>

                <div class="f-item">
                    <span class="f-label">Kategori</span>
                    <select name="jenis_simpanan" class="f-control">
                        <option value="">-- Semua --</option>
                        <option value="Pokok" <?php echo ($jenis_filter == 'Pokok') ? 'selected' : ''; ?>>Pokok</option>
                        <option value="Wajib" <?php echo ($jenis_filter == 'Wajib') ? 'selected' : ''; ?>>Wajib</option>
                        <option value="Sukarela" <?php echo ($jenis_filter == 'Sukarela') ? 'selected' : ''; ?>>Sukarela</option>
                    </select>
                </div>

                <div class="f-item">
                    <span class="f-label">Metode</span>
                    <select name="metode" class="f-control">
                        <option value="">-- Semua --</option>
                        <option value="Tunai" <?php echo ($metode_filter == 'Tunai') ? 'selected' : ''; ?>>Tunai</option>
                        <option value="Transfer" <?php echo ($metode_filter == 'Transfer') ? 'selected' : ''; ?>>Transfer</option>
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn-f btn-f-primary px-4">
                        <i class="bi bi-search me-2"></i>CARI
                    </button>
                    <a href="riwayat_simpanan.php" class="btn-f btn-f-light d-flex align-items-center" title="Reset Filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Tabel Data Mutasi -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-ksp-head">
                    <tr>
                        <th class="text-center" width="60">NO</th>
                        <th class="text-center">TANGGAL</th>
                        <th>NAMA ANGGOTA</th>
                        <th>KETERANGAN</th>
                        <th class="text-center">METODE</th>
                        <th class="text-end px-4">NOMINAL</th>
                        <th class="text-center">STRUK</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    if (mysqli_num_rows($query) == 0) {
                        echo "<tr><td colspan='7' class='text-center py-5 text-muted small'>Data mutasi tidak ditemukan untuk kriteria ini.</td></tr>";
                    }
                    while ($row = mysqli_fetch_assoc($query)): 
                        $is_plus = $row['jumlah'] > 0;
                    ?>
                    <tr>
                        <td class="text-center text-muted small"><?php echo $no++; ?></td>
                        <td class="text-center small"><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                        <td class="fw-bold text-dark"><?php echo strtoupper($row['nama']); ?></td>
                        <td>
                            <span class="badge <?php echo $is_plus ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'; ?> rounded-pill border px-3">
                                <?php echo $is_plus ? 'SETORAN '.$row['jenis_simpanan'] : 'PENARIKAN TUNAI'; ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <?php if($row['metode_pembayaran'] == 'Transfer'): ?>
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-1 rounded-pill small">Transfer</span>
                            <?php else: ?>
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-1 rounded-pill small">Tunai</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end px-4 fw-bold <?php echo $is_plus ? 'text-success' : 'text-danger'; ?>">
                            <?php echo ($is_plus ? '+' : '-') . ' ' . rupiah(abs($row['jumlah'])); ?>
                        </td>
                        <td class="text-center">
                            <a href="cetak_struk_simpanan.php?id=<?php echo $row['id_simpanan']; ?>" target="_blank" class="btn btn-sm btn-light border" title="Cetak Struk">
                                <i class="bi bi-printer text-muted"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>