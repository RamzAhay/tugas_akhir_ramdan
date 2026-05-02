<?php
include 'auth.php';
include 'koneksi.php';
include 'header.php';

// Menangkap ID Pinjaman
if (!isset($_GET['id'])) {
    header("Location: data_angsuran.php");
    exit();
}

$id_p = mysqli_real_escape_string($koneksi, $_GET['id']);

// 1. Ambil Informasi Utama Pinjaman & Anggota
$q_pinjam = mysqli_query($koneksi, "SELECT p.*, a.nama FROM tb_pinjaman_ramdan p 
                                    JOIN tb_anggota_ramdan a ON p.id_anggota = a.id_anggota 
                                    WHERE p.id_pinjaman = '$id_p'");
$d = mysqli_fetch_assoc($q_pinjam);

if (!$d) {
    echo "<script>
        Swal.fire({
            title: 'Data Tidak Ditemukan!',
            text: 'Data pinjaman tidak ditemukan di sistem.',
            icon: 'error',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location='data_angsuran.php';
        });
    </script>";
    exit();
}

// 2. Hitung Statistik untuk Kartu Ringkasan
$total_hutang  = $d['total_pinjaman'];
$sisa_hutang   = $d['sisa_pinjaman'];
$total_dibayar = $total_hutang - $sisa_hutang;

// Hitung Persentase Kelunasan
$persen_lunas = ($total_hutang > 0) ? ($total_dibayar / $total_hutang) * 100 : 0;
?>

<style>
    :root {
        --ksp-blue: #0d6efd;
        --ksp-slate: #334155;
        --ksp-border: #e2e8f0;
    }

    /* KARTU RINGKASAN */
    .summary-card {
        background: #ffffff;
        border: 1px solid var(--ksp-border);
        border-radius: 12px;
        padding: 20px;
        height: 100%;
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    }
    .summary-label { font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
    .summary-value { font-size: 1.3rem; font-weight: 700; color: #1e293b; margin-top: 5px; }

    /* PROGRESS BAR */
    .progress { height: 10px; border-radius: 10px; background-color: #f1f5f9; }
    .progress-bar { border-radius: 10px; }

    /* TABLE HEADER BLUE */
    .table-ksp-head {
        background: var(--ksp-blue) !important;
        color: white !important;
    }
    .table-ksp-head th {
        font-weight: 600 !important;
        font-size: 12px !important;
        text-transform: uppercase;
        border: none !important;
        padding: 15px !important;
    }
</style>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Riwayat Pembayaran</h3>
            <p class="text-muted mb-0">Detail angsuran untuk pinjaman atas nama <strong><?php echo strtoupper($d['nama']); ?></strong></p>
        </div>
        <a href="data_angsuran.php" class="btn btn-outline-secondary btn-sm px-3 rounded-pill">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <!-- Row Kartu Ringkasan -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="summary-card border-start border-4 border-dark">
                <div class="summary-label">Total Pinjaman</div>
                <div class="summary-value text-dark"><?php echo rupiah($total_hutang); ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card border-start border-4 border-success">
                <div class="summary-label">Sudah Dibayar</div>
                <div class="summary-value text-success"><?php echo rupiah($total_dibayar); ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card border-start border-4 border-danger">
                <div class="summary-label">Sisa Hutang</div>
                <div class="summary-value text-danger"><?php echo rupiah($sisa_hutang); ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-label">Progress Pelunasan</div>
                <div class="summary-value"><?php echo number_format($persen_lunas, 1); ?>%</div>
                <div class="progress mt-2">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo $persen_lunas; ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Mutasi Angsuran -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-primary">Daftar Transaksi Angsuran</h6>
            <div class="d-flex gap-2">
                <!-- PERBAIKAN: Tombol Cetak Laporan Utuh -->
                <a href="cetak_laporan_angsuran.php?id=<?php echo $id_p; ?>" target="_blank" class="btn btn-primary btn-sm px-3 shadow-sm">
                    <i class="bi bi-printer me-1"></i> Cetak Laporan
                </a>
                
                <?php if($sisa_hutang > 0 && $_SESSION['role'] != 'Anggota'): ?>
                    <a href="tambah_angsuran.php?id=<?php echo $id_p; ?>" class="btn btn-success btn-sm px-3 shadow-sm">+ Bayar Baru</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-ksp-head">
                    <tr>
                        <th class="text-center" width="60">NO</th>
                        <th class="text-center">TANGGAL BAYAR</th>
                        <th>KETERANGAN</th>
                        <th class="text-end">NOMINAL BAYAR</th>
                        <th class="text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $q_list = mysqli_query($koneksi, "SELECT * FROM tb_angsuran_ramdan WHERE id_pinjaman = '$id_p' ORDER BY id_angsuran DESC");
                    
                    if (mysqli_num_rows($q_list) == 0) {
                        echo "<tr><td colspan='5' class='text-center py-5 text-muted small'>Belum ada riwayat pembayaran untuk pinjaman ini.</td></tr>";
                    }

                    while ($r = mysqli_fetch_assoc($q_list)) {
                    ?>
                    <tr>
                        <td class="text-center text-muted small"><?php echo $no++; ?></td>
                        <td class="text-center small"><?php echo date('d/m/Y', strtotime($r['tanggal_bayar'])); ?></td>
                        <td><span class="badge bg-light text-dark border">Pembayaran Angsuran</span></td>
                        <td class="text-end fw-bold text-primary"><?php echo rupiah($r['jumlah_bayar']); ?></td>
                        <td class="text-center">
                            <!-- PERBAIKAN: Tombol Cetak Struk lebih jelas dengan Text & Warna -->
                            <a href="cetak_struk_angsuran.php?id=<?php echo $r['id_angsuran']; ?>" target="_blank" class="btn btn-sm btn-secondary text-white shadow-sm" title="Cetak Struk">
                                <i class="bi bi-printer me-1"></i> Cetak Struk
                            </a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php if($persen_lunas >= 100): ?>
            <div class="card-footer bg-success-subtle text-success text-center py-3 fw-bold border-top-0">
                <i class="bi bi-check-circle-fill me-2"></i> PINJAMAN INI TELAH LUNAS
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>