<?php
include 'auth.php';
include 'koneksi.php';

// Pastikan hanya Petugas/Admin
if ($_SESSION['role'] == 'Anggota') {
    header("Location: dashboard_admin.php");
    exit();
}

$id_pinjaman = $_GET['id'];
$query = mysqli_query($koneksi, "SELECT p.*, a.nama FROM tb_pinjaman_ramdan p JOIN tb_anggota_ramdan a ON p.id_anggota = a.id_anggota WHERE p.id_pinjaman = '$id_pinjaman' AND p.status_pinjaman = 'Diajukan'");
$data = mysqli_fetch_assoc($query);

if(!$data) {
    echo "<script>alert('Data tidak ditemukan atau pinjaman sudah diproses!'); window.location='data_pinjaman.php';</script>";
    exit();
}

include 'header.php';
?>

<div class="content">
    <div class="form-container" style="max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
        <h2 class="mb-4">Edit Pengajuan Pinjaman</h2>
        <p class="text-muted">Edit data pinjaman milik <strong><?php echo $data['nama']; ?></strong>. Hanya bisa diedit sebelum di-ACC.</p>
        <hr class="mb-4">

        <form action="proses_edit_pinjaman.php" method="POST">
            <input type="hidden" name="id_pinjaman" value="<?php echo $data['id_pinjaman']; ?>">
            
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="form-group">
                        <label class="text-dark font-weight-bold mb-2">Jumlah Pinjaman (Rp)</label>
                        <input type="number" name="jumlah_pinjaman" class="form-control form-control-lg" value="<?php echo (int)$data['jumlah_pinjaman']; ?>" min="100000" required>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="form-group">
                        <label class="text-dark font-weight-bold mb-2">Bunga Pinjaman (%)</label>
                        <input type="number" name="bunga" class="form-control form-control-lg" value="<?php echo $data['bunga']; ?>" min="0" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="form-group">
                        <label class="text-dark font-weight-bold mb-2">Lama Pinjaman (Bulan)</label>
                        <select name="lama_pinjaman" class="form-control form-control-lg" required>
                            <option value="3" <?php echo ($data['lama_pinjaman'] == 3) ? 'selected' : ''; ?>>3 Bulan</option>
                            <option value="6" <?php echo ($data['lama_pinjaman'] == 6) ? 'selected' : ''; ?>>6 Bulan</option>
                            <option value="12" <?php echo ($data['lama_pinjaman'] == 12) ? 'selected' : ''; ?>>12 Bulan</option>
                            <option value="24" <?php echo ($data['lama_pinjaman'] == 24) ? 'selected' : ''; ?>>24 Bulan</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="form-group">
                        <label class="text-dark font-weight-bold mb-2">Tanggal Pengajuan</label>
                        <input type="date" name="tanggal_pinjaman" class="form-control form-control-lg" value="<?php echo $data['tanggal_pinjaman']; ?>" required>
                    </div>
                </div>
            </div>

            <div class="form-actions d-flex gap-3 mt-4">
                <button type="submit" name="submit" class="btn btn-primary btn-lg px-5">Simpan Perubahan</button>
                <a href="data_pinjaman.php" class="btn btn-outline-secondary btn-lg px-4">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>