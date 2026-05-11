<?php
include 'auth_ramdan.php';
include 'koneksi_ramdan.php';
include 'header_ramdan.php';

// Cek Role User untuk RBAC
$role_user = isset($_SESSION['role']) ? $_SESSION['role'] : 'Petugas';

// FIX: Menggunakan $koneksi (sesuai file koneksi_ramdan.php)
$query = mysqli_query($koneksi, "SELECT * FROM tb_anggota_ramdan ORDER BY id_anggota_ramdan DESC");
?>

<div class="content">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 text-white p-4 rounded-4 shadow-sm" style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-people-fill me-2"></i>Data Master Anggota</h4>
            <p class="mb-0 opacity-75">Kelola seluruh data anggota yang terdaftar di KSP Ramdan.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="cetak_anggota_ramdan.php" target="_blank" class="btn btn-light text-primary fw-bold border-0 shadow-sm">
                <i class="bi bi-printer-fill me-1"></i> Cetak Laporan
            </a>
            <a href="tambah_anggota_ramdan.php" class="btn btn-warning fw-bold border-0 shadow-sm">
                <i class="bi bi-person-plus-fill me-1"></i> Tambah Anggota
            </a>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="text-secondary small fw-bold text-uppercase">
                            <th class="py-3 px-4" width="80">ID</th>
                            <th class="py-3">Nama Anggota</th>
                            <th class="py-3">Alamat</th>
                            <th class="py-3">No. HP</th>
                            <th class="py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if(mysqli_num_rows($query) == 0) {
                            echo "<tr><td colspan='5' class='text-center py-5 text-muted'>Belum ada data anggota.</td></tr>";
                        }
                        while ($data = mysqli_fetch_array($query)) : 
                        ?>
                        <tr>
                            <td class="px-4 text-muted fw-medium">#<?php echo $data['id_anggota_ramdan']; ?></td>
                            <td>
                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($data['nama_ramdan']); ?></div>
                                <div class="small text-muted">Terdaftar: <?php echo date('d M Y', strtotime($data['tanggal_daftar_ramdan'])); ?></div>
                            </td>
                            <td class="text-muted small"><?php echo htmlspecialchars($data['alamat_ramdan']); ?></td>
                            <td><span class="badge bg-info-subtle text-info rounded-pill px-3"><?php echo htmlspecialchars($data['no_hp_ramdan']); ?></span></td>
                            <td class="text-center">
                                <div class="btn-group gap-1">
                                    <a href="edit_anggota_ramdan.php?id=<?php echo $data['id_anggota_ramdan']; ?>" 
                                       class="btn btn-outline-primary btn-sm rounded-3">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    
                                    <!-- LOGIKA RBAC: Tombol Hapus HANYA muncul jika login sebagai Admin -->
                                    <?php if ($role_user == 'Admin'): ?>
                                    <a href="hapus_anggota_ramdan.php?id=<?php echo $data['id_anggota_ramdan']; ?>" 
                                       class="btn btn-outline-danger btn-sm rounded-3"
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus anggota <?php echo addslashes($data['nama_ramdan']); ?>? Seluruh riwayat transaksi akan ikut terhapus.')">
                                        <i class="bi bi-trash3-fill"></i> Hapus
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer Info -->
        <div class="card-footer bg-white border-top-0 py-3 px-4 d-flex justify-content-between align-items-center">
            <span class="small text-muted">
                Menampilkan <strong class="text-dark"><?php echo mysqli_num_rows($query); ?></strong> total anggota terdaftar.
            </span>
            <div class="small text-muted">
                Status Role: <span class="badge bg-secondary-subtle text-secondary rounded-pill"><?php echo $role_user; ?></span>
            </div>
        </div>
    </div>

</div>

<?php include 'footer_ramdan.php'; ?>