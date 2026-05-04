<?php
include 'auth.php';
include 'koneksi.php';
include 'header.php';

$role_user = $_SESSION['role'];
?>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Data Simpanan Anggota</h2>
            <p class="text-muted small">Rekapitulasi saldo per kategori yang diurutkan berdasarkan nama anggota.</p>
        </div>
        <?php if ($role_user != 'Anggota'): ?>
            <div class="d-flex gap-2">
                <a href="tambah_simpanan.php" class="btn btn-primary shadow-sm">
                    <i class="bi bi-plus-circle me-1"></i> Setor Simpanan
                </a>
            </div>
        <?php endif; ?>
    </div>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-primary text-white">
                    <tr>
                        <!-- Kolom NO sekarang menggunakan counter agar selalu berurutan 1, 2, 3... -->
                        <th class="text-center py-3" width="60">NO</th>
                        <th class="py-3">NAMA ANGGOTA</th>
                        <th class="py-3 text-center">POKOK</th>
                        <th class="py-3 text-center">WAJIB</th>
                        <th class="py-3 text-center">SUKARELA</th>
                        <th class="py-3 text-end px-4">TOTAL SALDO</th>
                        <?php if ($role_user != 'Anggota'): ?>
                            <th class="text-center py-3" width="100">AKSI</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Variabel counter dimulai dari 1
                    $no = 1; 
                    
                    // Query untuk mengambil data rekap saldo
                    $sql = "SELECT a.id_anggota, a.nama,
                                SUM(CASE WHEN s.jenis_simpanan = 'Pokok' THEN s.jumlah ELSE 0 END) as total_pokok,
                                SUM(CASE WHEN s.jenis_simpanan = 'Wajib' THEN s.jumlah ELSE 0 END) as total_wajib,
                                SUM(CASE WHEN s.jenis_simpanan = 'Sukarela' THEN s.jumlah ELSE 0 END) as total_sukarela,
                                SUM(s.jumlah) as total_semua
                            FROM tb_anggota_ramdan a
                            LEFT JOIN tb_simpanan_ramdan s ON a.id_anggota = s.id_anggota
                            GROUP BY a.id_anggota, a.nama
                            ORDER BY a.nama ASC";
                    
                    $query = mysqli_query($koneksi, $sql);

                    if (mysqli_num_rows($query) == 0) {
                        echo "<tr><td colspan='7' class='text-center py-5 text-muted'>Belum ada data simpanan.</td></tr>";
                    }

                    while ($data = mysqli_fetch_assoc($query)): ?>
                    <tr>
                        <!-- Menampilkan nomor urut baris, bukan ID Database -->
                        <td class="text-center text-muted small fw-bold"><?php echo $no++; ?></td>
                        <td class="fw-bold text-dark text-uppercase">
                            <?php echo htmlspecialchars($data['nama']); ?>
                            <div class="text-muted" style="font-size: 10px; font-weight: normal;">ID: <?php echo $data['id_anggota']; ?></div>
                        </td>
                        <td class="text-center"><?php echo rupiah($data['total_pokok']); ?></td>
                        <td class="text-center"><?php echo rupiah($data['total_wajib']); ?></td>
                        <td class="text-center"><?php echo rupiah($data['total_sukarela']); ?></td>
                        <td class="text-end px-4 fw-bold text-success">
                            <?php echo rupiah($data['total_semua']); ?>
                        </td>
                        <?php if ($role_user != 'Anggota'): ?>
                        <td class="text-center">
                            <a href="tarik_simpanan.php?id_anggota=<?php echo $data['id_anggota']; ?>" class="btn btn-outline-danger btn-sm rounded-pill px-3" style="font-size: 12px;">
                                <i class="bi bi-cash-dash me-1"></i> Tarik
                            </a>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>