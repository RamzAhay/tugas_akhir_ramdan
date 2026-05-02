<?php
include 'auth.php';
include 'koneksi.php';
include 'header.php';

$role_user = $_SESSION['role'];
?>

<style>
    /* Desain Tabel yang Selaras dengan Pinjaman */
    .table-ksp-head {
        background: #0d6efd !important;
        color: white !important;
    }
    .table-ksp-head th {
        font-weight: 600 !important;
        font-size: 12px !important;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: none !important;
    }
    .text-total {
        color: #198754;
        font-weight: 800;
    }
    .btn-tarik-sm {
        font-size: 11px;
        padding: 4px 12px;
        border-radius: 6px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.2s;
    }
    .btn-tarik-sm:hover {
        background-color: #dc3545;
        color: white;
    }
</style>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Data Simpanan Anggota</h2>
            <p class="text-muted small mb-0">Rekapitulasi total saldo simpanan per kategori untuk setiap anggota.</p>
        </div>
        <?php if ($role_user != 'Anggota'): ?>
            <div class="d-flex gap-2">
                <a href="tambah_simpanan.php" class="btn btn-primary shadow-sm">+ Setor Simpanan</a>
            </div>
        <?php endif; ?>
    </div>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-4">
            <h6 class="fw-bold text-primary mb-4">
                <i class="bi bi-journal-text me-2"></i>Rekap Saldo Aktif
            </h6>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-ksp-head">
                        <tr>
                            <th class="text-center py-3" width="50">NO</th>
                            <th class="py-3 text-center">ID</th>
                            <th class="py-3">NAMA ANGGOTA</th>
                            <th class="py-3 text-center">POKOK</th>
                            <th class="py-3 text-center">WAJIB</th>
                            <th class="py-3 text-center">SUKARELA</th>
                            <th class="py-3 text-end">TOTAL SALDO</th>
                            <th class="py-3 text-center">UPDATE</th>
                            <?php if ($role_user != 'Anggota'): ?>
                                <th class="text-center py-3">AKSI</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        
                        /** * QUERY DIPERBARUI:
                         * Menghitung total per jenis dan mengambil tanggal transaksi terbaru (MAX)
                         */
                        $sql = "SELECT 
                                    a.id_anggota,
                                    a.nama,
                                    SUM(CASE WHEN s.jenis_simpanan = 'Pokok' THEN s.jumlah ELSE 0 END) as total_pokok,
                                    SUM(CASE WHEN s.jenis_simpanan = 'Wajib' THEN s.jumlah ELSE 0 END) as total_wajib,
                                    SUM(CASE WHEN s.jenis_simpanan = 'Sukarela' THEN s.jumlah ELSE 0 END) as total_sukarela,
                                    SUM(s.jumlah) as total_semua,
                                    MAX(s.tanggal) as update_terakhir
                                FROM tb_anggota_ramdan a
                                LEFT JOIN tb_simpanan_ramdan s ON a.id_anggota = s.id_anggota
                                GROUP BY a.id_anggota, a.nama
                                ORDER BY a.nama ASC";
                                
                        $query = mysqli_query($koneksi, $sql);

                        if (mysqli_num_rows($query) == 0) {
                            echo "<tr><td colspan='9' class='text-center py-5 text-muted'>Belum ada data simpanan tercatat.</td></tr>";
                        }

                        while ($data = mysqli_fetch_assoc($query)) {
                            $tgl_update = ($data['update_terakhir']) ? date('d/m/y', strtotime($data['update_terakhir'])) : '-';
                        ?>
                        <tr>
                            <td class="text-center text-muted small"><?php echo $no++; ?></td>
                            <td class="text-center small text-muted"><?php echo $data['id_anggota']; ?></td>
                            <td class="fw-bold text-dark text-uppercase"><?php echo htmlspecialchars($data['nama']); ?></td>
                            <td class="text-center"><?php echo rupiah($data['total_pokok']); ?></td>
                            <td class="text-center"><?php echo rupiah($data['total_wajib']); ?></td>
                            <td class="text-center"><?php echo rupiah($data['total_sukarela']); ?></td>
                            <td class="text-end fw-bold text-total">
                                <?php echo rupiah($data['total_semua']); ?>
                            </td>
                            <td class="text-center small text-muted italic"><?php echo $tgl_update; ?></td>
                            
                            <?php if ($role_user != 'Anggota'): ?>
                            <td class="text-center">
                                <!-- Shortcut Tarik Tunai: Mengirim ID melalui URL agar otomatis terpilih di form -->
                                <a href="tarik_simpanan.php?id_anggota=<?php echo $data['id_anggota']; ?>" 
                                   class="btn btn-outline-danger btn-tarik-sm" 
                                   title="Tarik Tunai">
                                   <i class="bi bi-cash-stack"></i> Tarik
                                </a>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>