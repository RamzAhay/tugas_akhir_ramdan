<?php
include 'auth.php';
include 'koneksi.php';
include 'header.php';

/**
 * FIX BUG KEAMANAN & LOGIKA:
 * 1. Jika yang login adalah 'Anggota', paksa filter hanya untuk dirinya sendiri.
 * 2. Admin & Petugas tetap bisa melihat semua data atau melakukan filter.
 */
$role_user = $_SESSION['role'];
$nama_user = $_SESSION['nama'];

// Asumsi: ID Anggota disimpan di session saat login (umumnya id_user atau id_anggota)
// Jika role adalah Anggota, kita cari ID-nya di tabel anggota berdasarkan nama/id session
if ($role_user == 'Anggota') {
    // Kita kunci filter hanya untuk anggota yang sedang login
    // Pastikan session menyimpan id_anggota, jika tidak kita cari berdasarkan nama
    $id_user_session = $_SESSION['id_user']; 
    $q_cari_id = mysqli_query($koneksi, "SELECT id_anggota FROM tb_user_ramdan WHERE id_user = '$id_user_session'");
    $d_user = mysqli_fetch_assoc($q_cari_id);
    
    // Jika user ini adalah anggota, kita ambil ID Anggotanya (jika ada relasi)
    // Untuk mempermudah, kita asumsikan admin/petugas mengelola filter
    $id_anggota_filter = isset($d_user['id_anggota']) ? $d_user['id_anggota'] : ''; 
} else {
    // Admin & Petugas bebas menggunakan filter dari URL
    $id_anggota_filter = isset($_GET['id_anggota']) ? mysqli_real_escape_string($koneksi, $_GET['id_anggota']) : '';
}
?>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Log Transaksi Simpanan</h2>
            <p class="text-muted small mb-0">Memantau riwayat setoran dan penarikan kas koperasi.</p>
        </div>
        <!-- Tombol cetak menyesuaikan filter -->
        <a href="cetak_simpanan.php<?php echo $id_anggota_filter ? '?id_anggota='.$id_anggota_filter : ''; ?>" class="btn btn-secondary shadow-sm" target="_blank">
            <i class="bi bi-printer"></i> 🖨️ Cetak Laporan
        </a>
    </div>

    <!-- FITUR FILTER: Hanya muncul untuk Admin & Petugas -->
    <?php if ($role_user != 'Anggota'): ?>
    <div class="card mb-4 shadow-sm border-0 bg-light">
        <div class="card-body">
            <form method="GET" action="riwayat_simpanan.php" class="row g-3 align-items-center">
                <div class="col-auto">
                    <label class="fw-bold text-dark">Pilih Anggota:</label>
                </div>
                <div class="col-auto">
                    <select name="id_anggota" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Tampilkan Semua Transaksi --</option>
                        <?php
                        $q_anggota = mysqli_query($koneksi, "SELECT * FROM tb_anggota_ramdan ORDER BY nama ASC");
                        while ($d = mysqli_fetch_assoc($q_anggota)) {
                            $selected = ($d['id_anggota'] == $id_anggota_filter) ? 'selected' : '';
                            echo "<option value='".$d['id_anggota']."' $selected>".$d['nama']."</option>";
                        }
                        ?>
                    </select>
                </div>
                <?php if($id_anggota_filter): ?>
                <div class="col-auto">
                    <a href="riwayat_simpanan.php" class="btn btn-outline-danger btn-sm">Reset</a>
                </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- Tabel Data Riwayat -->
    <div class="card shadow-sm mb-5 border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th width="5%" class="text-center py-3">No</th>
                            <th class="py-3">Tanggal</th>
                            <th class="py-3">Nama Anggota</th>
                            <th class="py-3">Jenis Transaksi</th>
                            <th class="text-end py-3 px-4">Nominal (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        
                        // Membangun Query SQL secara dinamis
                        $sql = "SELECT s.*, a.nama 
                                FROM tb_simpanan_ramdan s 
                                JOIN tb_anggota_ramdan a ON s.id_anggota = a.id_anggota";
                        
                        if ($id_anggota_filter != '') {
                            $sql .= " WHERE s.id_anggota = '$id_anggota_filter'";
                        }
                        
                        $sql .= " ORDER BY s.id_simpanan DESC";
                                      
                        $query = mysqli_query($koneksi, $sql);

                        if (!$query) {
                            echo "<tr><td colspan='5' class='text-center text-danger py-4'>Terjadi kesalahan database: " . mysqli_error($koneksi) . "</td></tr>";
                        } else if (mysqli_num_rows($query) == 0) {
                            echo "<tr><td colspan='5' class='text-center text-muted py-5'>
                                    <img src='https://cdn-icons-png.flaticon.com/512/7486/7486744.png' width='80' class='mb-3 opacity-25'><br>
                                    Belum ada catatan transaksi untuk filter ini.
                                  </td></tr>";
                        } else {
                            while ($data = mysqli_fetch_assoc($query)) {
                                $nominal = $data['jumlah'];
                                
                                if ($nominal > 0) {
                                    $jenis = "Setoran (" . $data['jenis_simpanan'] . ")";
                                    $warna = "text-success fw-bold";
                                    $tanda = "+ ";
                                } else {
                                    $jenis = "Penarikan Tunai";
                                    $warna = "text-danger fw-bold";
                                    $tanda = "- ";
                                    $nominal = abs($nominal);
                                }
                        ?>
                        <tr>
                            <td class="text-center text-muted"><?php echo $no++; ?></td>
                            <td><?php echo date('d M Y', strtotime($data['tanggal'])); ?></td>
                            <td class="fw-bold"><?php echo htmlspecialchars($data['nama']); ?></td>
                            <td>
                                <span class="badge <?php echo ($data['jumlah'] > 0) ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'; ?> px-3">
                                    <?php echo $jenis; ?>
                                </span>
                            </td>
                            <td class="text-end <?php echo $warna; ?> px-4">
                                <?php echo $tanda . number_format($nominal, 0, ',', '.'); ?>
                            </td>
                        </tr>
                        <?php 
                            } 
                        } 
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>