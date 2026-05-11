<?php
include 'auth_ramdan.php';
include 'koneksi_ramdan.php';

// Pastikan hanya Petugas atau Admin yang bisa menambah simpanan
if ($_SESSION['role'] == 'Anggota') {
    header("Location: dashboard_admin_ramdan.php");
    exit();
}

include 'header_ramdan.php';
?>

<div class="content">
    <div class="form-container" style="max-width: 700px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
        <h2 class="mb-4">Setor Simpanan Tunai</h2>
        <p class="text-muted">Gunakan form ini untuk mencatat setoran uang dari anggota koperasi.</p>
        <hr class="mb-4">

        <form action="proses_tambah_simpanan_ramdan.php" method="POST">
            
            <div class="form-group mb-4">
                <label for="id_anggota_ramdan" class="text-dark font-weight-bold mb-2">Pilih Anggota</label>
                <select name="id_anggota_ramdan" id="id_anggota_ramdan" class="form-control form-control-lg" required>
                    <option value="">-- Silakan Pilih Anggota --</option>
                    <?php
                    /**
                     * URUTAN: Berdasarkan id_anggota_ramdan ASC
                     * TAMPILAN: Hanya menampilkan nama_ramdan
                     */
                    $q_anggota = mysqli_query($koneksi, "SELECT * FROM tb_anggota_ramdan ORDER BY id_anggota_ramdan ASC");
                    while ($d = mysqli_fetch_assoc($q_anggota)) {
                        echo "<option value='".$d['id_anggota_ramdan']."'>".$d['nama_ramdan']."</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="form-group">
                        <label for="jenis_simpanan_ramdan" class="text-dark font-weight-bold mb-2">Jenis Simpanan</label>
                        <select name="jenis_simpanan_ramdan" id="jenis_simpanan_ramdan" class="form-control form-control-lg" required>
                            <option value="Pokok">Simpanan Pokok</option>
                            <option value="Wajib">Simpanan Wajib</option>
                            <option value="Sukarela" selected>Simpanan Sukarela</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="form-group">
                        <label for="jumlah_ramdan" class="text-dark font-weight-bold mb-2">Jumlah Setoran (Rp)</label>
                        <!-- PERUBAHAN: Input text visual & input hidden -->
                        <input type="text" id="jumlah_format" class="form-control form-control-lg" placeholder="Contoh: 100.000" required oninput="formatInput(this, 'jumlah_ramdan')">
                        <input type="hidden" name="jumlah_ramdan" id="jumlah_ramdan">
                    </div>
                </div>
            </div>

            <!-- Metode Setoran -->
            <div class="form-group mb-4">
                <label for="metode_pembayaran_ramdan" class="text-dark font-weight-bold mb-2">Metode Setoran</label>
                <select name="metode_pembayaran_ramdan" id="metode_pembayaran_ramdan" class="form-control form-control-lg" required>
                    <option value="Tunai">Tunai / Cash</option>
                    <option value="Transfer">Transfer Bank</option>
                </select>
                <small class="text-muted">Pilih bagaimana anggota menyetorkan uangnya.</small>
            </div>

            <div class="form-group mb-4">
                <label for="tanggal_ramdan" class="text-dark font-weight-bold mb-2">Tanggal Transaksi</label>
                <input type="date" name="tanggal_ramdan" id="tanggal_ramdan" class="form-control form-control-lg" value="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <div class="form-actions d-flex gap-3 mt-4">
                <button type="submit" name="submit" class="btn btn-success btn-lg px-5">Simpan Setoran</button>
                <a href="data_simpanan_ramdan.php" class="btn btn-outline-secondary btn-lg px-4">Batal</a>
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

<?php include 'footer_ramdan.php'; ?>