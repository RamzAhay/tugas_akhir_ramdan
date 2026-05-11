<?php
include 'auth_ramdan.php';
include 'koneksi_ramdan.php';

// Pastikan hanya Petugas/Admin
if ($_SESSION['role'] == 'Anggota') {
    header("Location: dashboard_admin_ramdan.php");
    exit();
}

$id_pinjaman_ramdan = $_GET['id'];
$query = mysqli_query($koneksi, "SELECT p.*, a.nama_ramdan FROM tb_pinjaman_ramdan p JOIN tb_anggota_ramdan a ON p.id_anggota_ramdan = a.id_anggota_ramdan WHERE p.id_pinjaman_ramdan = '$id_pinjaman_ramdan' AND p.status_pinjaman_ramdan = 'Diajukan'");
$data = mysqli_fetch_assoc($query);

if(!$data) {
    echo "<script>
        Swal.fire({
            title: 'Data Tidak Ditemukan!',
            text: 'Data tidak ditemukan atau pinjaman sudah diproses!',
            icon: 'error',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location='data_pinjaman_ramdan.php';
        });
    </script>";
    exit();
}

include 'header_ramdan.php';
?>

<div class="content">
    <div class="form-container" style="max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
        <h2 class="mb-4">Edit Pengajuan Pinjaman</h2>
        <p class="text-muted">Edit data pinjaman milik <strong><?php echo $data['nama_ramdan']; ?></strong>. Hanya bisa diedit sebelum di-ACC.</p>
        <hr class="mb-4">

        <form action="proses_edit_pinjaman_ramdan.php" method="POST">
            <input type="hidden" name="id_pinjaman_ramdan" value="<?php echo $data['id_pinjaman_ramdan']; ?>">
            
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="form-group">
                        <label class="text-dark font-weight-bold mb-2">Jumlah Pinjaman (Rp)</label>
                        <input type="number" name="jumlah_pinjaman_ramdan" class="form-control form-control-lg" value="<?php echo (int)$data['jumlah_pinjaman_ramdan']; ?>" min="100000" required>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="form-group">
                        <label class="text-dark font-weight-bold mb-2">Bunga Pinjaman (%)</label>
                        <input type="number" name="bunga_ramdan" class="form-control form-control-lg" value="<?php echo $data['bunga_ramdan']; ?>" min="0" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="form-group">
                        <label class="text-dark font-weight-bold mb-2">Lama Pinjaman (Bulan)</label>
                        <select name="lama_pinjaman_ramdan" class="form-control form-control-lg" required>
                            <option value="3" <?php echo ($data['lama_pinjaman_ramdan'] == 3) ? 'selected' : ''; ?>>3 Bulan</option>
                            <option value="6" <?php echo ($data['lama_pinjaman_ramdan'] == 6) ? 'selected' : ''; ?>>6 Bulan</option>
                            <option value="12" <?php echo ($data['lama_pinjaman_ramdan'] == 12) ? 'selected' : ''; ?>>12 Bulan</option>
                            <option value="24" <?php echo ($data['lama_pinjaman_ramdan'] == 24) ? 'selected' : ''; ?>>24 Bulan</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="form-group">
                        <label class="text-dark font-weight-bold mb-2">Tanggal Pengajuan</label>
                        <input type="date" name="tanggal_pinjaman_ramdan" class="form-control form-control-lg" value="<?php echo $data['tanggal_pinjaman_ramdan']; ?>" required>
                    </div>
                </div>
            </div>

            <div class="form-actions d-flex gap-3 mt-4">
                <button type="submit" name="submit" class="btn btn-primary btn-lg px-5">Simpan Perubahan</button>
                <a href="data_pinjaman_ramdan.php" class="btn btn-outline-secondary btn-lg px-4">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php include 'footer_ramdan.php'; ?>