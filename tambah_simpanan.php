<?php
include 'auth.php';
include 'koneksi.php';

// Pastikan hanya Petugas atau Admin yang bisa menambah simpanan
if ($_SESSION['role'] == 'Anggota') {
    header("Location: dashboard_admin.php");
    exit();
}

include 'header.php';
?>

<div class="content">
    <div class="form-container" style="max-width: 700px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
        <h2 class="mb-4">Setor Simpanan Tunai</h2>
        <p class="text-muted">Gunakan form ini untuk mencatat setoran uang dari anggota koperasi.</p>
        <hr class="mb-4">

        <form action="proses_tambah_simpanan.php" method="POST">
            
            <div class="form-group mb-4">
                <label for="id_anggota" class="text-dark font-weight-bold mb-2">Pilih Anggota</label>
                <select name="id_anggota" id="id_anggota" class="form-control form-control-lg" required>
                    <option value="">-- Silakan Pilih Anggota --</option>
                    <?php
                    /**
                     * URUTAN: Berdasarkan id_anggota ASC
                     * TAMPILAN: Hanya menampilkan nama
                     */
                    $q_anggota = mysqli_query($koneksi, "SELECT * FROM tb_anggota_ramdan ORDER BY id_anggota ASC");
                    while ($d = mysqli_fetch_assoc($q_anggota)) {
                        echo "<option value='".$d['id_anggota']."'>".$d['nama']."</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="form-group">
                        <label for="jenis_simpanan" class="text-dark font-weight-bold mb-2">Jenis Simpanan</label>
                        <select name="jenis_simpanan" id="jenis_simpanan" class="form-control form-control-lg" required>
                            <option value="Pokok">Simpanan Pokok</option>
                            <option value="Wajib">Simpanan Wajib</option>
                            <option value="Sukarela" selected>Simpanan Sukarela</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="form-group">
                        <label for="jumlah" class="text-dark font-weight-bold mb-2">Jumlah Setoran (Rp)</label>
                        <input type="number" name="jumlah" id="jumlah" class="form-control form-control-lg" placeholder="Contoh: 100000" min="1000" required>
                    </div>
                </div>
            </div>

            <div class="form-group mb-4">
                <label for="tanggal" class="text-dark font-weight-bold mb-2">Tanggal Transaksi</label>
                <input type="date" name="tanggal" id="tanggal" class="form-control form-control-lg" value="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <div class="form-actions d-flex gap-3 mt-4">
                <button type="submit" name="submit" class="btn btn-success btn-lg px-5">Simpan Setoran</button>
                <a href="data_simpanan.php" class="btn btn-outline-secondary btn-lg px-4">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
    // FUNGSI MASKING: Format Ribuan (Titik)
    function formatInput(input, hiddenId) {
        let rawValue = input.value.replace(/\D/g, "");
        document.getElementById(hiddenId).value = rawValue;
        input.value = rawValue.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
</script>

<?php include 'footer.php'; ?>