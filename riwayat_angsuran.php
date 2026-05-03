<?php
include 'auth.php';
include 'koneksi.php';

// Pastikan ada ID yang dikirim
if (!isset($_GET['id'])) {
    header("Location: data_pinjaman.php");
    exit();
}

$id_pinjaman = mysqli_real_escape_string($koneksi, $_GET['id']);

// Ambil info detail pinjaman & nama anggota
$q_info = mysqli_query($koneksi, "SELECT p.*, a.nama FROM tb_pinjaman_ramdan p 
                                  JOIN tb_anggota_ramdan a ON p.id_anggota = a.id_anggota 
                                  WHERE p.id_pinjaman = '$id_pinjaman'");
$d_info = mysqli_fetch_assoc($q_info);

if (!$d_info) {
    die("Data Pinjaman tidak ditemukan.");
}

include 'header.php';
?>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Riwayat Pembayaran Angsuran</h3>
            <p class="text-muted mb-0">Rincian pembayaran untuk pinjaman <span class="fw-bold text-primary"><?php echo strtoupper($d_info['nama']); ?></span></p>
        </div>
        <div class="d-flex gap-2">
            <!-- PERUBAHAN: Mengubah href dari cetak_angsuran.php menjadi cetak_laporan_angsuran.php -->
            <a href="cetak_laporan_angsuran.php?id=<?php echo $id_pinjaman; ?>" target="_blank" class="btn btn-dark shadow-sm">
                <i class="bi bi-printer me-1"></i> Cetak Laporan
            </a>
            <!-- Tombol Bayar (Disembunyikan jika lunas atau user = Anggota) -->
            <?php if($d_info['status_pinjaman'] != 'Lunas' && $_SESSION['role'] != 'Anggota'): ?>
                <a href="tambah_angsuran.php?id=<?php echo $id_pinjaman; ?>" class="btn btn-success shadow-sm">
                    <i class="bi bi-cash me-1"></i> Bayar Angsuran
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Ringkasan Info Pinjaman -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-label">Total Pinjaman</div>
                <div class="summary-value text-dark"><?php echo rupiah($d_info['total_pinjaman']); ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-label">Sudah Dibayar</div>
                <?php 
                    $sudah_bayar = $d_info['total_pinjaman'] - $d_info['sisa_pinjaman'];
                ?>
                <div class="summary-value text-success"><?php echo rupiah($sudah_bayar); ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-label">Sisa Hutang</div>
                <div class="summary-value text-danger"><?php echo rupiah($d_info['sisa_pinjaman']); ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-label">Status Pinjaman</div>
                <div class="mt-2">
                    <?php 
                    if ($d_info['status_pinjaman'] == 'Lunas') {
                        echo '<span class="badge bg-success px-3 py-2 fs-6"><i class="bi bi-check-circle me-1"></i> LUNAS</span>';
                    } elseif ($d_info['status_pinjaman'] == 'Diajukan') {
                        echo '<span class="badge bg-warning px-3 py-2 fs-6 text-dark"><i class="bi bi-hourglass-split me-1"></i> DIAJUKAN</span>';
                    } else {
                        echo '<span class="badge bg-primary px-3 py-2 fs-6"><i class="bi bi-activity me-1"></i> AKTIF</span>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Bar (Visualisasi Lunas) -->
    <?php 
        $persen = ($d_info['total_pinjaman'] > 0) ? ($sudah_bayar / $d_info['total_pinjaman']) * 100 : 0;
        $warna_bar = ($persen == 100) ? 'bg-success' : 'bg-primary';
    ?>
    <div class="card mb-4 border-0 shadow-sm rounded-4 p-4">
        <div class="d-flex justify-content-between mb-2">
            <span class="small fw-bold text-muted text-uppercase">Progress Pelunasan</span>
            <span class="small fw-bold text-dark"><?php echo round($persen); ?>%</span>
        </div>
        <div class="progress" style="height: 10px;">
            <div class="progress-bar <?php echo $warna_bar; ?>" role="progressbar" style="width: <?php echo $persen; ?>%" aria-valuenow="<?php echo $persen; ?>" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </div>

    <!-- Tabel Riwayat Pembayaran -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-ksp-head">
                <tr>
                    <th class="py-3 px-4 text-center" width="50">NO</th>
                    <th class="py-3">TANGGAL BAYAR</th>
                    <th class="py-3">PEMBAYARAN</th>
                    <th class="py-3 text-center">METODE</th>
                    <th class="py-3 text-end">NOMINAL BAYAR</th>
                    <th class="py-3 text-center pe-4" width="120">AKSI</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Ambil data angsuran khusus untuk pinjaman ini (Urutkan dari yang paling awal dibayar)
                $q_angsuran = mysqli_query($koneksi, "SELECT * FROM tb_angsuran_ramdan WHERE id_pinjaman = '$id_pinjaman' ORDER BY tanggal_bayar ASC, id_angsuran ASC");
                
                if(mysqli_num_rows($q_angsuran) == 0){
                    echo "<tr><td colspan='6' class='text-center py-5 text-muted'>Belum ada riwayat pembayaran untuk pinjaman ini.</td></tr>";
                }

                $no = 1;
                while ($r = mysqli_fetch_assoc($q_angsuran)): 
                ?>
                <tr>
                    <td class="text-center px-4 text-muted small"><?php echo $no; ?></td>
                    <td class="fw-bold"><?php echo tgl_indo($r['tanggal_bayar']); ?></td>
                    <td>
                        <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2">
                            Angsuran Ke-<?php echo $no; ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <?php 
                        // Menampilkan metode pembayaran dengan gaya badge
                        $metode = isset($r['metode_pembayaran']) ? $r['metode_pembayaran'] : 'Tunai'; 
                        if ($metode == 'Transfer') {
                            echo '<span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1"><i class="bi bi-bank me-1"></i> Transfer</span>';
                        } else {
                            echo '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1"><i class="bi bi-cash-coin me-1"></i> Tunai</span>';
                        }
                        ?>
                    </td>
                    <td class="text-end fw-bold text-success">
                        + <?php echo rupiah($r['jumlah_bayar']); ?>
                    </td>
                    <td class="text-center pe-4">
                        <a href="cetak_struk_angsuran.php?id=<?php echo $r['id_angsuran']; ?>" target="_blank" class="btn btn-outline-secondary btn-sm" title="Cetak Struk">
                            <i class="bi bi-printer"></i> Struk
                        </a>
                    </td>
                </tr>
                <?php 
                    $no++; // Nomor akan bertambah terus sesuai urutan pembayaran
                endwhile; 
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>