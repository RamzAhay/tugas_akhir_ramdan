<?php
include 'auth_ramdan.php';
include 'koneksi_ramdan.php';

// Proteksi halaman hanya untuk Admin
if ($_SESSION['role'] != 'Admin') {
    echo "<script>alert('Akses Ditolak!'); window.location='dashboard_petugas_ramdan.php';</script>";
    exit();
}

include 'header_ramdan.php';
?>

<div class="content">
    <div class="form-container" style="max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
        <div class="d-flex align-items-center mb-4">
            <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                <i class="bi bi-person-badge fs-4"></i>
            </div>
            <div>
                <h3 class="fw-bold mb-0">Tambah Akses Pengguna</h3>
                <p class="text-muted mb-0">Daftarkan akun Admin atau Petugas Kasir baru.</p>
            </div>
        </div>
        <hr class="mb-4">

        <form action="proses_tambah_user_ramdan.php" method="POST">
            
            <div class="mb-4">
                <label class="fw-bold text-dark mb-2">Nama Lengkap Pegawai</label>
                <input type="text" name="nama_ramdan" class="form-control form-control-lg" placeholder="Masukan Nama Lengkap" required>
            </div>

            <div class="mb-4">
                <label class="fw-bold text-dark mb-2">Username (Untuk Login)</label>
                <input type="text" name="username_ramdan" class="form-control form-control-lg" placeholder="Buat username tanpa spasi" required>
                <small class="text-muted">Username ini akan digunakan saat masuk ke sistem.</small>
            </div>

            <div class="mb-4">
                <label class="fw-bold text-dark mb-2">Password Login</label>
                <div class="input-group">
                    <input type="password" name="password_ramdan" id="passwordInput" class="form-control form-control-lg" placeholder="Buat kata sandi minimal 6 karakter" required>
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <div class="mb-5">
                <label class="fw-bold text-dark mb-2">Peran / Hak Akses</label>
                <!-- Nama input diubah menjadi id_role_ramdan agar sinkron dengan proses -->
                <select name="id_role_ramdan" class="form-select form-select-lg" required>
                    <option value="">-- Pilih Level Akses --</option>
                    <!-- Value diubah menjadi angka sesuai tb_role_ramdan -->
                    <option value="2">Petugas (Kasir Transaksi)</option>
                    <option value="1">Administrator (Pengawas Sistem)</option>
                </select>
            </div>

            <div class="d-flex gap-3">
                <button type="submit" name="submit" class="btn btn-primary btn-lg flex-grow-1 fw-bold">Simpan Pengguna</button>
                <a href="data_user_ramdan.php" class="btn btn-light border btn-lg px-4 text-muted fw-bold">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
    // Fitur Intip Password
    document.getElementById('togglePassword').addEventListener('click', function (e) {
        const passwordInput = document.getElementById('passwordInput');
        const icon = this.querySelector('i');
        
        if (passwordInput.type === 'password_ramdan') {
            passwordInput.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            passwordInput.type = 'password_ramdan';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    });
</script>

<?php include 'footer_ramdan.php'; ?>