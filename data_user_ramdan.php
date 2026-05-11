<?php
include 'auth_ramdan.php';
include 'koneksi_ramdan.php';

// Proteksi Admin dengan SweetAlert2
if ($_SESSION['role'] != 'Admin') {
    echo "<!DOCTYPE html><html><head><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script><style>body{font-family:Poppins;}</style></head><body>";
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Akses Ditolak',
            text: 'Halaman ini hanya untuk Administrator!',
            confirmButtonColor: '#0d6efd'
        }).then(() => { window.location.href = 'dashboard_petugas_ramdan.php'; });
    </script></body></html>";
    exit();
}

include 'header_ramdan.php';
?>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Manajemen Pengguna</h2>
            <p class="text-muted small mb-0">Daftar akun login sistem KSP RAMDAN.</p>
        </div>
        <a href="tambah_user_ramdan.php" class="btn btn-primary shadow-sm"><i class="bi bi-person-plus-fill me-2"></i>Tambah User</a>
    </div>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center py-3" width="60">NO</th>
                            <th class="py-3">NAMA LENGKAP</th>
                            <th class="py-3">USERNAME</th>
                            <th class="py-3 text-center">ROLE</th>
                            <th class="text-center py-3" width="150">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        // Query JOIN sesuai screenshot database kamu
                        $sql = "SELECT u.id_user_ramdan, u.nama_ramdan, u.username_ramdan, r.nama_role_ramdan 
                                FROM tb_user_ramdan u 
                                JOIN tb_role_ramdan r ON u.id_role_ramdan = r.id_role_ramdan 
                                WHERE r.nama_role_ramdan IN ('Admin', 'Petugas') 
                                ORDER BY r.nama_role_ramdan ASC, u.nama_ramdan ASC";
                        $query = mysqli_query($koneksi, $sql);

                        while ($row = mysqli_fetch_assoc($query)) {
                        ?>
                        <tr>
                            <td class="text-center text-muted small"><?php echo $no++; ?></td>
                            <td class="fw-bold"><?php echo htmlspecialchars($row['nama_ramdan']); ?></td>
                            <td><span class="badge bg-light text-dark border fw-normal px-3"><?php echo htmlspecialchars($row['username_ramdan']); ?></span></td>
                            <td class="text-center">
                                <?php if ($row['nama_role_ramdan'] == 'Admin'): ?>
                                    <span class="badge bg-primary rounded-pill px-3 py-2">Administrator</span>
                                <?php else: ?>
                                    <span class="badge bg-info text-dark rounded-pill px-3 py-2">Petugas</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($row['id_user_ramdan'] != $_SESSION['id_user_ramdan']): ?>
                                    <a href="hapus_user_ramdan.php?id=<?php echo $row['id_user_ramdan']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Cabut akses user ini?');"><i class="bi bi-trash"></i></a>
                                <?php else: ?>
                                    <small class="text-muted italic">Akun Aktif</small>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer_ramdan.php'; ?>