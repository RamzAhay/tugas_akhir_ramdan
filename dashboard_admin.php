<?php
include 'auth.php';
include 'koneksi.php';

// Proteksi: Pastikan hanya Admin yang masuk
if ($_SESSION['role'] != 'Admin') {
    header("Location: dashboard_petugas.php");
    exit();
}

include 'header.php';

// --- LOGIKA PERHITUNGAN DATA ---
$q_simpanan = mysqli_query($koneksi, "SELECT SUM(jumlah) as total FROM tb_simpanan_ramdan");
$d_simpanan = mysqli_fetch_assoc($q_simpanan);
$total_simpanan = $d_simpanan['total'] ?? 0;

$q_diluar = mysqli_query($koneksi, "SELECT SUM(sisa_pinjaman) as total FROM tb_pinjaman_ramdan WHERE status_pinjaman = 'Disetujui'");
$d_diluar = mysqli_fetch_assoc($q_diluar);
$uang_diluar = $d_diluar['total'] ?? 0;

$q_lunas = mysqli_query($koneksi, "SELECT SUM(total_pinjaman) as total FROM tb_pinjaman_ramdan WHERE status_pinjaman = 'Lunas'");
$d_lunas = mysqli_fetch_assoc($q_lunas);
$total_lunas = $d_lunas['total'] ?? 0;

$q_anggota = mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM tb_anggota_ramdan");
$d_anggota = mysqli_fetch_assoc($q_anggota);
$jml_anggota = $d_anggota['jml'];

$dana_mengendap = $total_simpanan - $uang_diluar;
$rasio = ($total_simpanan > 0) ? ($uang_diluar / $total_simpanan) * 100 : 0;
?>

<style>
    /* Styling Minimalis Khusus Dashboard */
    .dash-card {
        background: #ffffff;
        border: 1px solid #e0e6ed;
        border-radius: 12px;
        transition: transform 0.2s, box-shadow 0.2s;
        text-decoration: none !important;
        display: block;
        height: 100%;
    }
    .dash-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important;
        border-color: #0d6efd;
    }
    .card-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #6c757d;
    }
    .card-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #212529;
        margin-top: 5px;
    }
    .icon-box {
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        font-size: 1.25rem;
    }
    /* Warna aksen yang tenang */
    .accent-blue { background-color: #e7f1ff; color: #0d6efd; }
    .accent-green { background-color: #e9f7ef; color: #198754; }
    .accent-dark { background-color: #f8f9fa; color: #212529; }
</style>

<div class="content">
    <!-- Header Minimalis -->
    <div class="mb-5">
        <h3 class="fw-bold text-dark mb-1">Ringkasan Eksekutif</h3>
        <p class="text-muted mb-0">Laporan posisi keuangan KSP RAMDAN per tanggal <?php echo date('d M Y'); ?></p>
    </div>

    <!-- Row Kartu Utama -->
    <div class="row g-4">
        <!-- Dana Simpanan -->
        <div class="col-md-4">
            <a href="data_simpanan.php" class="dash-card shadow-sm p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="icon-box accent-green">
                        <i class="bi bi-piggy-bank"></i>
                    </div>
                    <div class="card-label">Total Simpanan</div>
                </div>
                <div class="card-value">Rp <?php echo number_format($total_simpanan, 0, ',', '.'); ?></div>
                <div class="text-muted small mt-2">Saldo kas tersedia di sistem</div>
            </a>
        </div>

        <!-- Uang di Luar (Sekarang bisa diklik ke pelacakan pinjaman) -->
        <div class="col-md-4">
            <a href="data_pinjaman.php" class="dash-card shadow-sm p-4 border-primary">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="icon-box accent-blue">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <div class="card-label">Uang di Luar</div>
                </div>
                <div class="card-value">Rp <?php echo number_format($uang_diluar, 0, ',', '.'); ?></div>
                <div class="text-muted small mt-2">Klik untuk melacak pinjaman aktif <i class="bi bi-arrow-right-short"></i></div>
            </a>
        </div>

        <!-- Total Anggota -->
        <div class="col-md-4">
            <a href="data_anggota.php" class="dash-card shadow-sm p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="icon-box accent-dark">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="card-label">Total Anggota</div>
                </div>
                <div class="card-value"><?php echo $jml_anggota; ?> <span style="font-size: 1rem; font-weight: normal;">Orang</span></div>
                <div class="text-muted small mt-2">Jumlah nasabah terdaftar</div>
            </a>
        </div>
    </div>

    <!-- Detail & Analisis -->
    <div class="row mt-4 g-4">
        <div class="col-lg-8">
            <div class="dash-card shadow-sm h-100" style="cursor: default;">
                <div class="p-4 border-bottom bg-light rounded-top">
                    <h6 class="fw-bold mb-0">Rincian Perputaran Dana</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <tbody class="border-top-0">
                            <tr>
                                <td class="ps-4 py-3 text-muted">Dana Mengendap (Cash on Hand)</td>
                                <td class="text-end pe-4 fw-bold" style="color: #198754;">
                                    Rp <?php echo number_format($dana_mengendap, 0, ',', '.'); ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-4 py-3 text-muted">Total Pinjaman Berhasil (Lunas)</td>
                                <td class="text-end pe-4 fw-bold">
                                    Rp <?php echo number_format($total_lunas, 0, ',', '.'); ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-4 py-3 text-muted">Rasio Peminjaman Dana</td>
                                <td class="text-end pe-4">
                                    <span class="badge bg-secondary px-3 py-2">
                                        <?php echo number_format($rasio, 1); ?>% dari Total Kas
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="dash-card shadow-sm h-100 p-4" style="cursor: default;">
                <h6 class="card-label mb-3">Status Likuiditas</h6>
                
                <?php if ($rasio > 80): ?>
                    <div class="p-3 rounded-3" style="background-color: #fff5f5; border: 1px solid #feb2b2;">
                        <h6 class="fw-bold text-danger mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i>WASPADA</h6>
                        <p class="text-dark small mb-0">Perputaran dana di luar sangat tinggi. Disarankan untuk membatasi pengajuan pinjaman baru.</p>
                    </div>
                <?php else: ?>
                    <div class="p-3 rounded-3" style="background-color: #f0fff4; border: 1px solid #9ae6b4;">
                        <h6 class="fw-bold text-success mb-2"><i class="bi bi-check-circle-fill me-2"></i>KONDISI SEHAT</h6>
                        <p class="text-dark small mb-0">Koperasi memiliki cadangan kas yang aman untuk operasional dan penarikan simpanan.</p>
                    </div>
                <?php endif; ?>

                <div class="d-grid mt-4 gap-2">
                    <a href="riwayat_simpanan.php" class="btn btn-light border text-dark fw-bold btn-sm py-2 rounded-3">Buka Log Simpanan</a>
                    <a href="riwayat_pinjaman.php" class="btn btn-light border text-dark fw-bold btn-sm py-2 rounded-3">Buka Riwayat Pinjaman</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>