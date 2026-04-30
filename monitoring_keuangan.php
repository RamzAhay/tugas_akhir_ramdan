<?php
include 'auth.php';
include 'koneksi.php';

// Proteksi: Hanya Admin yang bisa melihat ringkasan keuangan sensitif
if ($_SESSION['role'] != 'Admin') {
    header("Location: dashboard_admin.php");
    exit();
}

include 'header.php';

// 1. HITUNG TOTAL UANG DI LUAR (Outstanding Loans)
$q_hutang = mysqli_query($koneksi, "SELECT SUM(sisa_pinjaman) as total_diluar FROM tb_pinjaman_ramdan WHERE status_pinjaman = 'Disetujui'");
$d_hutang = mysqli_fetch_assoc($q_hutang);
$uang_diluar = $d_hutang['total_diluar'] ?? 0;

// 2. HITUNG TOTAL SIMPANAN ANGGOTA (Total Saldo Kas)
$q_simpanan = mysqli_query($koneksi, "SELECT SUM(jumlah) as total_simpanan FROM tb_simpanan_ramdan");
$d_simpanan = mysqli_fetch_assoc($q_simpanan);
$total_simpanan = $d_simpanan['total_simpanan'] ?? 0;

// 3. HITUNG TOTAL PINJAMAN YANG SUDAH LUNAS (History)
$q_lunas = mysqli_query($koneksi, "SELECT SUM(total_pinjaman) as total_lunas FROM tb_pinjaman_ramdan WHERE status_pinjaman = 'Lunas'");
$d_lunas = mysqli_fetch_assoc($q_lunas);
$total_lunas = $d_lunas['total_lunas'] ?? 0;

// 4. HITUNG JUMLAH ANGGOTA AKTIF
$q_anggota = mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM tb_anggota_ramdan");
$d_anggota = mysqli_fetch_assoc($q_anggota);
$jml_anggota = $d_anggota['jml'];
?>

<div class="content">
    <div class="mb-4">
        <h2 class="fw-bold">Monitoring Keuangan Global</h2>
        <p class="text-muted">Ringkasan perputaran dana KSP RAMDAN secara real-time.</p>
    </div>

    <div class="row g-4">
        <!-- Card: Total Simpanan -->
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-3" style="border-left: 6px solid #198754 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-success fw-bold text-uppercase small">Total Dana Simpanan</div>
                        <i class="bi bi-piggy-bank fs-3 text-success opacity-50"></i>
                    </div>
                    <h2 class="fw-bold mb-1">Rp <?php echo number_format($total_simpanan, 0, ',', '.'); ?></h2>
                    <p class="text-muted small mb-0">Total saldo bersih yang tersedia di kas.</p>
                </div>
            </div>
        </div>

        <!-- Card: Uang di Luar -->
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-3" style="border-left: 6px solid #0d6efd !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-primary fw-bold text-uppercase small">Uang Sedang di Luar</div>
                        <i class="bi bi-arrow-up-right-circle fs-3 text-primary opacity-50"></i>
                    </div>
                    <h2 class="fw-bold mb-1">Rp <?php echo number_format($uang_diluar, 0, ',', '.'); ?></h2>
                    <p class="text-muted small mb-0">Total tagihan pinjaman yang belum terbayar.</p>
                </div>
            </div>
        </div>

        <!-- Card: Anggota -->
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-3" style="border-left: 6px solid #6c757d !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-secondary fw-bold text-uppercase small">Total Anggota</div>
                        <i class="bi bi-people fs-3 text-secondary opacity-50"></i>
                    </div>
                    <h2 class="fw-bold mb-1"><?php echo $jml_anggota; ?> Orang</h2>
                    <p class="text-muted small mb-0">Jumlah anggota terdaftar saat ini.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Detail Section -->
    <div class="row mt-5">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="fw-bold mb-0">Analisis Perputaran Dana</h5>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle">
                            <tbody>
                                <tr>
                                    <td width="50%" class="text-muted">Total Pinjaman Terdistribusi (Lunas)</td>
                                    <td class="text-end fw-bold">Rp <?php echo number_format($total_lunas, 0, ',', '.'); ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Estimasi Dana Mengendap (Cash on Hand)</td>
                                    <td class="text-end fw-bold text-success">
                                        Rp <?php echo number_format($total_simpanan - $uang_diluar, 0, ',', '.'); ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="alert alert-info border-0 rounded-3 mt-3">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        Dana mengendap dihitung dari selisih simpanan dengan uang yang sedang dipinjamkan.
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 bg-dark text-white">
                <div class="card-body p-4">
                    <h6 class="text-uppercase opacity-75 small fw-bold mb-3">Saran Manajemen</h6>
                    <?php if ($uang_diluar > $total_simpanan * 0.8): ?>
                        <div class="p-3 rounded-3 bg-danger bg-opacity-25 border border-danger">
                            <small>⚠️ <strong>Peringatan Risiko:</strong> Lebih dari 80% dana koperasi sedang dipinjamkan. Batasi pengajuan pinjaman baru sementara.</small>
                        </div>
                    <?php else: ?>
                        <div class="p-3 rounded-3 bg-success bg-opacity-25 border border-success">
                            <small>✅ <strong>Kondisi Aman:</strong> Rasio perputaran dana masih dalam batas wajar. Koperasi memiliki likuiditas yang baik.</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>