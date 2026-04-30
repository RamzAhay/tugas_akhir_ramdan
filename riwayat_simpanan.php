<?php
include 'auth.php';
include 'koneksi.php';
include 'header.php';

// Pastikan session ada, jika tidak set null untuk menghindari error undefined key
$role_user = isset($_SESSION['role']) ? $_SESSION['role'] : '';
$id_user_logged = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : null;

// Logika Filter Anggota
if ($role_user == 'Anggota') {
    // Jika Anggota, ambil ID Anggota-nya dari tabel user berdasarkan id_user di session
    if ($id_user_logged) {
        $q_user = mysqli_query($koneksi, "SELECT id_anggota FROM tb_user_ramdan WHERE id_user = '$id_user_logged'");
        $d_user = mysqli_fetch_assoc($q_user);
        $filter_anggota = isset($d_user['id_anggota']) ? $d_user['id_anggota'] : '';
    } else {
        $filter_anggota = '';
    }
} else {
    // Admin/Petugas mengambil dari GET id_anggota atau id (sebagai fallback dari URL ?id=1)
    if (isset($_GET['id_anggota'])) {
        $filter_anggota = mysqli_real_escape_string($koneksi, $_GET['id_anggota']);
    } elseif (isset($_GET['id'])) {
        $filter_anggota = mysqli_real_escape_string($koneksi, $_GET['id']);
    } else {
        $filter_anggota = '';
    }
}

// Ambil Nama Anggota untuk ditampilkan di Header
$nama_anggota_tampil = "";
if (!empty($filter_anggota)) {
    $q_m = mysqli_query($koneksi, "SELECT nama FROM tb_anggota_ramdan WHERE id_anggota = '$filter_anggota'");
    $d_m = mysqli_fetch_assoc($q_m);
    $nama_anggota_tampil = isset($d_m['nama']) ? $d_m['nama'] : "Tidak Ditemukan";
}

$filter_tgl_awal = isset($_GET['tgl_awal']) ? mysqli_real_escape_string($koneksi, $_GET['tgl_awal']) : '';
$filter_tgl_akhir = isset($_GET['tgl_akhir']) ? mysqli_real_escape_string($koneksi, $_GET['tgl_akhir']) : '';
?>

<style>
    .f-section {
        background: #f8fafc; 
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px 18px; 
        margin-bottom: 20px;
    }
    .f-container {
        display: flex !important;
        flex-wrap: wrap;
        gap: 10px;
        align-items: flex-end !important;
    }
    .f-group {
        flex: 1;
        min-width: 140px;
        margin-bottom: 0 !important;
    }
    .f-label {
        font-size: 10px !important;
        font-weight: 700 !important;
        color: #475569 !important;
        text-transform: uppercase;
        margin-bottom: 4px !important;
        display: block !important;
    }
    .f-control {
        display: block !important;
        width: 100% !important;
        height: 34px !important; 
        margin: 0 !important;    
        padding: 4px 10px !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 6px !important;
        font-size: 13px !important;
        background-color: #ffffff !important;
    }
    .btn-f-cari, .btn-f-print {
        height: 34px !important;
        padding: 0 15px !important;
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
        height: 34px !important;
        width: 34px !important;
        background: #ffffff !important;
        color: #ef4444 !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 6px !important;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }
    .info-member-box {
        background: white;
        border: 1px solid #e2e8f0;
        border-left: 5px solid #334155;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }
</style>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-0">Riwayat Mutasi Simpanan</h4>
            <p class="text-muted small mb-0">Laporan transaksi simpanan dan penarikan anggota.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="data_simpanan.php" class="btn btn-outline-secondary btn-sm px-3">Kembali</a>
        </div>
    </div>

    <!-- TAMPILAN INFORMASI ANGGOTA -->
    <?php if (!empty($filter_anggota)): ?>
    <div class="info-member-box shadow-sm">
        <div class="row">
            <div class="col-md-3">
                <span class="text-muted small fw-bold">ID ANGGOTA</span>
                <div class="fw-bold text-dark"><?php echo $filter_anggota; ?></div>
            </div>
            <div class="col-md-9">
                <span class="text-muted small fw-bold">NAMA ANGGOTA</span>
                <div class="fw-bold text-dark fs-5"><?php echo strtoupper($nama_anggota_tampil); ?></div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- FILTER SECTION -->
    <div class="f-section shadow-sm">
        <form method="GET" action="riwayat_simpanan.php">
            <input type="hidden" name="id_anggota" value="<?php echo $filter_anggota; ?>">
            
            <div class="f-container">
                <div class="f-group">
                    <span class="f-label">Dari Tanggal</span>
                    <input type="date" name="tgl_awal" class="f-control" value="<?php echo $filter_tgl_awal; ?>">
                </div>

                <div class="f-group">
                    <span class="f-label">Sampai Tanggal</span>
                    <input type="date" name="tgl_akhir" class="f-control" value="<?php echo $filter_tgl_akhir; ?>">
                </div>

                <div class="d-flex gap-1">
                    <button type="submit" class="btn-f-cari">FILTER</button>
                    
                    <a href="cetak_simpanan.php?id_anggota=<?php echo $filter_anggota; ?>&tgl_awal=<?php echo $filter_tgl_awal; ?>&tgl_akhir=<?php echo $filter_tgl_akhir; ?>" 
                       class="btn-f-print" target="_blank">
                        <i class="bi bi-printer me-2"></i> CETAK
                    </a>

                    <?php if($filter_tgl_awal != ''): ?>
                        <a href="riwayat_simpanan.php?id_anggota=<?php echo $filter_anggota; ?>" class="btn-f-reset" title="Reset Tanggal">
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
                    <thead class="bg-light text-dark">
                        <tr>
                            <th class="text-center py-3 small fw-bold" width="50">NO</th>
                            <th class="py-3 small fw-bold">TANGGAL</th>
                            <th class="py-3 small fw-bold">KETERANGAN</th>
                            <th class="py-3 small fw-bold text-end px-4">JUMLAH (RP)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($filter_anggota)) {
                            $no = 1;
                            $sql = "SELECT * FROM tb_simpanan_ramdan WHERE id_anggota = '$filter_anggota'";

                            if ($filter_tgl_awal != '' && $filter_tgl_akhir != '') {
                                $sql .= " AND tanggal BETWEEN '$filter_tgl_awal' AND '$filter_tgl_akhir'";
                            }

                            $sql .= " ORDER BY id_simpanan ASC";
                            $query = mysqli_query($koneksi, $sql);

                            if(mysqli_num_rows($query) == 0) {
                                echo "<tr><td colspan='4' class='text-center py-5 text-muted'>Tidak ada data transaksi simpanan untuk periode ini.</td></tr>";
                            }

                            while ($data = mysqli_fetch_assoc($query)) {
                                $is_setoran = ($data['jumlah'] > 0);
                            ?>
                            <tr>
                                <td class="text-center text-muted small"><?php echo $no++; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($data['tanggal'])); ?></td>
                                <td>
                                    <?php if($is_setoran): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                                            Setoran: <?php echo $data['jenis_simpanan']; ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                            Penarikan Tunai
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end px-4 fw-bold <?php echo $is_setoran ? 'text-success' : 'text-danger'; ?>">
                                    <?php echo ($is_setoran ? '+' : '-') . ' ' . number_format(abs($data['jumlah']), 0, ',', '.'); ?>
                                </td>
                            </tr>
                            <?php 
                            } 
                        } else {
                            echo "<tr><td colspan='4' class='text-center py-5 text-muted'>Silakan pilih anggota terlebih dahulu atau gunakan tautan 'Lihat Riwayat' dari menu Simpanan.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>