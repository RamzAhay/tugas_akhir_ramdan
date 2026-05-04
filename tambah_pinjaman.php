<?php
include 'auth.php';
include 'koneksi.php';

// Proteksi: Hanya Admin/Petugas yang bisa akses form ini
if ($_SESSION['role'] == 'Anggota') {
    header("Location: dashboard_admin.php");
    exit();
}

include 'header.php';
?>

<div class="content">
    <div class="form-container" style="max-width: 800px; margin: 0 auto; background: #fff; padding: 40px; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.05);">
        <div class="d-flex align-items-center mb-4">
            <div class="bg-primary text-white rounded-4 d-flex align-items-center justify-content-center me-3" style="width: 55px; height: 55px;">
                <i class="bi bi-bank2 fs-3"></i>
            </div>
            <div>
                <h3 class="fw-bold mb-0">Input Pengajuan Pinjaman</h3>
                <p class="text-muted mb-0 small text-uppercase fw-bold">Modul Pembiayaan Anggota</p>
            </div>
        </div>
        <hr class="mb-4 opacity-50">

        <form action="proses_tambah_pinjaman.php" method="POST">
            <!-- Pilih Anggota -->
            <div class="mb-4">
                <label class="form-label fw-bold text-dark mb-2">Nama Anggota Koperasi</label>
                <select name="id_anggota" class="form-select form-select-lg border-2" required>
                    <option value="">-- Cari Nama Anggota --</option>
                    <?php
                    // Mengambil id_anggota dan nama dari tb_anggota_ramdan
                    $q_anggota = mysqli_query($koneksi, "SELECT id_anggota, nama FROM tb_anggota_ramdan ORDER BY nama ASC");
                    while ($d = mysqli_fetch_assoc($q_anggota)) {
                        echo "<option value='".$d['id_anggota']."'>".strtoupper($d['nama'])."</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="row g-4">
                <!-- Nominal Pinjaman -->
                <div class="col-md-7 mb-3">
                    <label class="form-label fw-bold text-dark mb-2">Jumlah Pinjaman (Rp)</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text border-2 bg-light fw-bold">Rp</span>
                        <input type="text" id="mask_rupiah" class="form-control border-2 fw-bold text-primary" placeholder="0" required>
                    </div>
                    <!-- Input hidden untuk mengirim angka murni ke database -->
                    <input type="hidden" name="jumlah_pinjaman" id="real_rupiah">
                    <div class="form-text small text-info mt-2">
                        <i class="bi bi-info-circle-fill me-1"></i> Syarat: Anggota harus memiliki saldo simpanan minimal 20%.
                    </div>
                </div>
                
                <!-- Bunga -->
                <div class="col-md-5 mb-3">
                    <label class="form-label fw-bold text-dark mb-2">Bunga Pinjaman (%)</label>
                    <div class="input-group input-group-lg">
                        <input type="number" name="bunga" class="form-control border-2" value="10" min="1" required>
                        <span class="input-group-text border-2 bg-light">%</span>
                    </div>
                </div>
            </div>

            <div class="row g-4 mt-1">
                <!-- Tenor -->
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-dark mb-2">Jangka Waktu (Tenor)</label>
                    <select name="lama_pinjaman" class="form-select form-select-lg border-2" required>
                        <option value="3">3 Bulan</option>
                        <option value="6">6 Bulan</option>
                        <option value="12" selected>12 Bulan (1 Tahun)</option>
                        <option value="24">24 Bulan (2 Tahun)</option>
                    </select>
                </div>
                
                <!-- Tanggal -->
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-dark mb-2">Tanggal Pengajuan</label>
                    <input type="date" name="tanggal_pinjaman" class="form-control form-control-lg border-2" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
            </div>

            <!-- Tombol -->
            <div class="d-grid gap-2 mt-4 pt-2">
                <button type="submit" name="submit" class="btn btn-primary btn-lg fw-bold shadow-sm py-3">
                    Kirim Pengajuan Ke Admin
                </button>
                <a href="data_pinjaman.php" class="btn btn-link text-muted text-decoration-none text-center">Batal dan Kembali</a>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Fungsi Masking: Menambah titik otomatis saat mengetik angka
    $('#mask_rupiah').on('input', function() {
        let val = $(this).val().replace(/\D/g, ""); // Hapus karakter non-angka
        $('#real_rupiah').val(val); // Masukkan angka murni ke hidden input untuk PHP
        $(this).val(val.replace(/\B(?=(\d{3})+(?!\d))/g, ".")); // Beri format titik ribuan
    });
});
</script>

<?php include 'footer.php'; ?>