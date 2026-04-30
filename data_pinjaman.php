<?php
include 'auth.php';
include 'koneksi.php';
include 'header.php';

$role_user = $_SESSION['role'];

// Menangkap Filter
$filter_anggota = isset($_GET['id_anggota']) ? mysqli_real_escape_string($koneksi, $_GET['id_anggota']) : '';
$filter_tgl_awal = isset($_GET['tgl_awal']) ? mysqli_real_escape_string($koneksi, $_GET['tgl_awal']) : '';
$filter_tgl_akhir = isset($_GET['tgl_akhir']) ? mysqli_real_escape_string($koneksi, $_GET['tgl_akhir']) : '';
?>

<style>
    /* ============================================================
       COMPACT FILTER STYLE (RAMPING & MINIMALIS)
       ============================================================ */
    
    .filter-section {
        background: #f8fafc; 
        border: 1px solid #e2e8f0;
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
        min-width: 150px;
        margin-bottom: 0 !important;
    }

    .filter-item select.f-input-small, 
    .filter-item input[type=date].f-input-small {
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

    .filter-label-small {
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

    .btn-f-cari {
        height: 36px !important;
        padding: 0 20px !important;
        background: #334155 !important; 
        color: white !important;
        border: none !important;
        border-radius: 6px !important;
        font-weight: 600 !important;
        font-size: 12px !important;
        cursor: pointer;
        transition: 0.2s;
    }

    .btn-f-cari:hover {
        background: #1e293b !important;
    }

    /* TOMBOL CETAK: Sekarang disamakan efeknya dengan tombol FILTER */
    .btn-f-print {
        height: 36px !important;
        padding: 0 15px !important; 
        background: #334155 !important; /* Warna disamakan dengan btn-f-cari */
        color: white !important;        /* Warna teks disamakan */
        border: none !important;
        border-radius: 6px !important;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: 0.2s;
        font-weight: 600 !important;
        font-size: 12px !important;
    }

    .btn-f-print:hover {
        background: #1e293b !important; /* Efek hover disamakan */
        color: white !important;
        text-decoration: none;
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
        .filter-item { flex: 1 1 100%; }
    }
</style>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-0">Pelacakan Pinjaman Aktif</h4>
            <p class="text-muted small mb-0">Monitor perputaran dana anggota.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="tambah_pinjaman.php" class="btn btn-primary btn-sm px-3 shadow-sm">+ Pinjaman Baru</a>
        </div>
    </div>

    <!-- AREA FILTER COMPACT -->
    <div class="filter-section shadow-sm">
        <form method="GET" action="data_pinjaman.php">
            <div class="filter-flex-container">
                
                <div class="filter-item">
                    <span class="filter-label-small">Anggota</span>
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

                <div class="filter-item">
                    <span class="filter-label-small">Mulai Tanggal</span>
                    <input type="date" name="tgl_awal" class="f-input-small" value="<?php echo $filter_tgl_awal; ?>">
                </div>

                <div class="filter-item">
                    <span class="filter-label-small">Sampai Tanggal</span>
                    <input type="date" name="tgl_akhir" class="f-input-small" value="<?php echo $filter_tgl_akhir; ?>">
                </div>

                <div class="btn-action-group">
                    <button type="submit" class="btn-f-cari">FILTER</button>
                    
                    <!-- Tombol Cetak Laporan (Warna & Gaya disamakan dengan FILTER) -->
                    <a href="cetak_laporan_pinjaman.php?id_anggota=<?php echo $filter_anggota; ?>&tgl_awal=<?php echo $filter_tgl_awal; ?>&tgl_akhir=<?php echo $filter_tgl_akhir; ?>" 
                       class="btn-f-print" target="_blank" title="Cetak Laporan">
                        <i class="bi bi-printer me-2"></i> CETAK
                    </a>

                    <?php if($filter_anggota != '' || $filter_tgl_awal != '' || $filter_tgl_akhir != ''): ?>
                        <a href="data_pinjaman.php" class="btn-f-reset" title="Bersihkan Filter">
                            <i class="bi bi-x"></i>
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
                            <th class="text-center py-3 text-muted small fw-bold" width="50">NO</th>
                            <th class="py-3 small fw-bold">NAMA ANGGOTA</th>
                            <th class="py-3 small fw-bold text-center">TGL PINJAM</th>
                            <th class="py-3 small fw-bold">JUMLAH</th>
                            <th class="py-3 small fw-bold">TENOR</th>
                            <th class="py-3 small fw-bold">SISA HUTANG</th>
                            <th class="py-3 small fw-bold text-center">STATUS</th>
                            <th class="text-center py-3 small fw-bold">AKSI</th>
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
                        if ($filter_tgl_awal != '' && $filter_tgl_akhir != '') {
                            $sql .= " AND p.tanggal_pinjaman BETWEEN '$filter_tgl_awal' AND '$filter_tgl_akhir'";
                        }

                        $sql .= " ORDER BY p.id_pinjaman DESC";
                        $query = mysqli_query($koneksi, $sql);
                        
                        if (mysqli_num_rows($query) == 0) {
                            echo "<tr><td colspan='8' class='text-center py-5 text-muted'>Tidak ada data yang ditemukan.</td></tr>";
                        }

                        while ($data = mysqli_fetch_assoc($query)) {
                        ?>
                        <tr>
                            <td class="text-center text-muted small"><?php echo $no++; ?></td>
                            <td class="fw-bold"><?php echo htmlspecialchars($data['nama']); ?></td>
                            <td class="text-center"><?php echo date('d/m/Y', strtotime($data['tanggal_pinjaman'])); ?></td>
                            <td>Rp <?php echo number_format($data['jumlah_pinjaman'], 0, ',', '.'); ?></td>
                            <td class="small"><?php echo $data['lama_pinjaman']; ?> bln</td>
                            <td class="fw-bold text-danger">Rp <?php echo number_format($data['sisa_pinjaman'], 0, ',', '.'); ?></td>
                            <td class="text-center">
                                <span class="badge <?php echo ($data['status_pinjaman'] == 'Diajukan') ? 'bg-warning text-dark' : 'bg-primary'; ?> rounded-pill">
                                    <?php echo $data['status_pinjaman']; ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group gap-1">
                                    <a href="riwayat_angsuran.php?id=<?php echo $data['id_pinjaman']; ?>" class="btn btn-outline-info btn-sm py-1 px-2" style="font-size: 11px;">Detail</a>
                                    <?php if($data['status_pinjaman'] == 'Disetujui'): ?>
                                        <a href="tambah_angsuran.php?id=<?php echo $data['id_pinjaman']; ?>" class="btn btn-success btn-sm py-1 px-2" style="font-size: 11px;">Bayar</a>
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