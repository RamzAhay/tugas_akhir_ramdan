<?php
include 'auth.php';
include 'koneksi.php';

// Pastikan hanya Petugas atau Admin yang bisa menambah pinjaman
if ($_SESSION['role'] == 'Anggota') {
    header("Location: dashboard_admin.php");
    exit();
}

include 'header.php';
?>

<div class="content">
    <div class="form-container" style="max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
        <h2 class="mb-4">Ajukan Pinjaman Baru</h2>
        <p class="text-muted">Isi formulir di bawah ini dengan teliti untuk mengajukan pinjaman anggota.</p>
        <hr class="mb-4">

        <form action="proses_tambah_pinjaman.php" method="POST">
            
            <div class="form-group mb-4">
                <label for="id_anggota" class="text-dark font-weight-bold mb-2">Pilih Anggota</label>
                <select name="id_anggota" id="id_anggota" class="form-control form-control-lg" required>
                    <option value="">-- Silakan Pilih Anggota --</option>
                    <?php
                    // Ambil data anggota yang aktif
                    $q_anggota = mysqli_query($koneksi, "SELECT * FROM tb_anggota_ramdan ORDER BY nama ASC");
                    while ($d = mysqli_fetch_assoc($q_anggota)) {
                        echo "<option value='".$d['id_anggota']."'>".$d['nama']." (ID: ".$d['id_anggota'].")</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="form-group">
                        <label for="jumlah_pinjaman" class="text-dark font-weight-bold mb-2">Jumlah Pinjaman (Rp)</label>
                        <input type="number" name="jumlah_pinjaman" id="jumlah_pinjaman" class="form-control form-control-lg" placeholder="Contoh: 5000000" min="100000" required>
                        <small class="text-muted">Minimal pinjaman Rp 100.000</small>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="form-group">
                        <label for="bunga" class="text-dark font-weight-bold mb-2">Bunga Pinjaman (%)</label>
                        <input type="number" name="bunga" id="bunga" class="form-control form-control-lg" placeholder="Contoh: 10" value="10" min="0" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="form-group">
                        <label for="lama_pinjaman" class="text-dark font-weight-bold mb-2">Lama Pinjaman (Bulan)</label>
                        <select name="lama_pinjaman" id="lama_pinjaman" class="form-control form-control-lg" required>
                            <option value="">-- Pilih Lama Pinjaman --</option>
                            <option value="3">3 Bulan</option>
                            <option value="6">6 Bulan</option>
                            <option value="12">12 Bulan</option>
                            <option value="24">24 Bulan</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="form-group">
                        <label for="tanggal_pinjaman" class="text-dark font-weight-bold mb-2">Tanggal Pengajuan</label>
                        <input type="date" name="tanggal_pinjaman" id="tanggal_pinjaman" class="form-control form-control-lg" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                </div>
            </div>

            <div class="form-actions d-flex gap-3 mt-4">
                <button type="submit" name="submit" class="btn btn-primary btn-lg px-5">Ajukan Pinjaman</button>
                <a href="data_pinjaman.php" class="btn btn-outline-secondary btn-lg px-4">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>