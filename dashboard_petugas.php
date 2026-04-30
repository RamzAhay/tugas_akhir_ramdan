<?php
include 'auth.php';
include 'koneksi.php';

// Proteksi: Pastikan hanya Petugas yang masuk
if ($_SESSION['role'] != 'Petugas') {
    header("Location: dashboard_admin.php");
    exit();
}

include 'header.php';

// --- LOGIKA PERHITUNGAN OPERASIONAL HARI INI ---
$hari_ini = date('Y-m-d');

// 1. Total Simpanan Masuk Hari Ini
$q_setor = mysqli_query($koneksi, "SELECT SUM(jumlah) as total FROM tb_simpanan_ramdan WHERE tanggal = '$hari_ini' AND jumlah > 0");
$d_setor = mysqli_fetch_assoc($q_setor);
$setoran_hari_ini = $d_setor['total'] ?? 0;

// 2. Total Penarikan Hari Ini
$q_tarik = mysqli_query($koneksi, "SELECT SUM(jumlah) as total FROM tb_simpanan_ramdan WHERE tanggal = '$hari_ini' AND jumlah < 0");
$d_tarik = mysqli_fetch_assoc($q_tarik);
$tarikan_hari_ini = abs($d_tarik['total'] ?? 0);

// 3. Jumlah Pengajuan Pinjaman Menunggu (Status: Diajukan)
$q_pending = mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM tb_pinjaman_ramdan WHERE status_pinjaman = 'Diajukan'");
$d_pending = mysqli_fetch_assoc($q_pending);
$pinjaman_pending = $d_pending['jml'];

// 4. Total Anggota Terdaftar
$q_anggota = mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM tb_anggota_ramdan");
$d_anggota = mysqli_fetch_assoc($q_anggota);
$jml_anggota = $d_anggota['jml'];
?>

<style>
    .op-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px;
        height: 100%;
        transition: all 0.2s;
    }
    .op-card:hover {
        border-color: #334155;
        box-shadow: 0 8px 20px rgba(0,0,0,0.04);
    }
    .quick-link {
        display: flex;
        align-items: center;
        padding: 15px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        text-decoration: none !important;
        color: #334155;
        font-weight: 600;
        transition: 0.2s;
    }
    .quick-link:hover {
        background: #334155;
        color: #ffffff !important;
    }
    .quick-link i {
        font-size: 1.5rem;
        margin-right: 15px;
        opacity: 0.7;
    }
    .stat-label { font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
    .stat-value { font-size: 1.5rem; font-weight: 700; color: #1e293b; margin: 5px 0; }
    .icon-circle { width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
</style>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Dashboard Petugas</h3>
            <p class="text-muted mb-0">Selamat bekerja, <strong><?php echo $_SESSION['nama']; ?></strong>. Pantau aktivitas koperasi hari ini.</p>
        </div>
        <div class="text-end">
            <span class="badge bg-dark px-3 py-2"><?php echo date('d F Y'); ?></span>
        </div>
    </div>

    <!-- Ringkasan Harian -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="op-card shadow-sm">
                <div class="icon-circle bg-success-subtle text-success mb-3">
                    <i class="bi bi-box-arrow-in-down"></i>
                </div>
                <div class="stat-label">Setoran Hari Ini</div>
                <div class="stat-value">Rp <?php echo number_format($setoran_hari_ini, 0, ',', '.'); ?></div>
                <div class="small text-muted">Total uang masuk</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="op-card shadow-sm">
                <div class="icon-circle bg-danger-subtle text-danger mb-3">
                    <i class="bi bi-box-arrow-up"></i>
                </div>
                <div class="stat-label">Penarikan Hari Ini</div>
                <div class="stat-value">Rp <?php echo number_format($tarikan_hari_ini, 0, ',', '.'); ?></div>
                <div class="small text-muted">Total uang keluar</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="op-card shadow-sm">
                <div class="icon-circle bg-warning-subtle text-warning mb-3">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div class="stat-label">Pengajuan Pinjaman</div>
                <div class="stat-value"><?php echo $pinjaman_pending; ?> <small class="fw-normal" style="font-size: 0.9rem;">Data</small></div>
                <div class="small text-muted">Menunggu verifikasi</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="op-card shadow-sm">
                <div class="icon-circle bg-primary-subtle text-primary mb-3">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-label">Total Anggota</div>
                <div class="stat-value"><?php echo $jml_anggota; ?> <small class="fw-normal" style="font-size: 0.9rem;">Orang</small></div>
                <div class="small text-muted">Nasabah terdaftar</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Shortcut Transaksi -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                <h6 class="fw-bold mb-3 px-2">Akses Cepat Transaksi</h6>
                <div class="d-flex flex-column gap-2">
                    <a href="tambah_simpanan.php" class="quick-link">
                        <i class="bi bi-plus-circle text-success"></i> Input Setoran Baru
                    </a>
                    <a href="tarik_simpanan.php" class="quick-link">
                        <i class="bi bi-dash-circle text-danger"></i> Proses Tarik Tunai
                    </a>
                    <a href="tambah_angsuran.php" class="quick-link">
                        <i class="bi bi-receipt text-primary"></i> Input Bayar Angsuran
                    </a>
                    <a href="tambah_pinjaman.php" class="quick-link">
                        <i class="bi bi-cash-stack text-warning"></i> Ajukan Pinjaman
                    </a>
                </div>
            </div>
        </div>

        <!-- Tabel Aktivitas Terbaru -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0">Log Transaksi Terakhir (Hari Ini)</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 small fw-bold">ANGGOTA</th>
                                <th class="small fw-bold">JENIS</th>
                                <th class="small fw-bold text-end pe-4">NOMINAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $q_recent = mysqli_query($koneksi, "SELECT s.*, a.nama 
                                                                FROM tb_simpanan_ramdan s 
                                                                JOIN tb_anggota_ramdan a ON s.id_anggota = a.id_anggota 
                                                                WHERE s.tanggal = '$hari_ini' 
                                                                ORDER BY s.id_simpanan DESC LIMIT 5");
                            
                            if(mysqli_num_rows($q_recent) == 0) {
                                echo "<tr><td colspan='3' class='text-center py-4 text-muted small'>Belum ada transaksi hari ini.</td></tr>";
                            }

                            while($r = mysqli_fetch_assoc($q_recent)) {
                                $is_plus = $r['jumlah'] > 0;
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold"><?php echo strtoupper($r['nama']); ?></div>
                                    <div class="text-muted small">ID: <?php echo $r['id_anggota']; ?></div>
                                </td>
                                <td>
                                    <span class="badge <?php echo $is_plus ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'; ?> rounded-pill">
                                        <?php echo $is_plus ? 'Setoran' : 'Penarikan'; ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4 fw-bold <?php echo $is_plus ? 'text-success' : 'text-danger'; ?>">
                                    Rp <?php echo number_format(abs($r['jumlah']), 0, ',', '.'); ?>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white border-0 text-center py-3">
                    <a href="riwayat_simpanan.php" class="text-decoration-none small fw-bold text-primary">Lihat Semua Riwayat <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>