<?php
include 'auth.php';
include 'koneksi.php';

// Pastikan hanya Petugas atau Admin yang bisa menambah angsuran
if ($_SESSION['role'] == 'Anggota') {
    header("Location: dashboard_admin.php");
    exit();
}

include 'header.php';
?>

<div class="content">
    <div class="form-container">
        <h2 class="mb-4">Input Pembayaran Angsuran</h2>
        <p class="text-muted">Gunakan formulir ini untuk mencatat pembayaran angsuran anggota secara real-time.</p>
        <hr>

        <form action="proses_tambah_angsuran.php" method="POST" id="formAngsuran">
            <!-- Bagian Pilih Pinjaman -->
            <div class="form-group mb-4">
                <label for="id_pinjaman" class="text-dark font-weight-bold">Pilih Pinjaman (Anggota - Total Pinjaman)</label>
                <select name="id_pinjaman" id="id_pinjaman" class="form-control" required>
                    <option value="">-- Pilih Pinjaman Aktif --</option>
                    <?php
                    // Query mengambil pinjaman yang belum lunas
                    $q_pinjam = mysqli_query($koneksi, "SELECT p.*, a.nama 
                                                       FROM tb_pinjaman_ramdan p 
                                                       JOIN tb_anggota_ramdan a ON p.id_anggota = a.id_anggota 
                                                       WHERE p.status_pinjaman = 'Disetujui' OR p.status_pinjaman = 'Belum Lunas'");
                    while ($d_pinjam = mysqli_fetch_assoc($q_pinjam)) {
                        echo "<option value='".$d_pinjam['id_pinjaman']."'>".$d_pinjam['nama']." - Rp ".number_format($d_pinjam['total_pinjaman'], 0, ',', '.')."</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Bagian Informasi Otomatis (AJAX) -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <!-- Penyelamatan Tampilan: Paksa warna teks agar tetap hitam/biru -->
                    <div class="card p-3 bg-light shadow-sm" style="border: 1px solid #ddd; min-height: 100px;">
                        <label class="font-weight-bold" style="color: #0d6efd !important; display: block; margin-bottom: 5px;">Sisa Pinjaman Saat Ini</label>
                        <h3 id="display_sisa" style="color: #212529 !important; font-weight: 800; margin: 0;">Rp 0</h3>
                        <input type="hidden" name="sisa_pinjaman_hidden" id="sisa_pinjaman_hidden">
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <label for="jumlah_bayar" class="text-dark font-weight-bold">Jumlah Bayar (Rp)</label>
                        <input type="number" name="jumlah_bayar" id="jumlah_bayar" class="form-control form-control-lg" placeholder="Masukkan nominal pembayaran..." required>
                        <small class="text-danger" id="msg_error" style="display:none; font-weight: 700; margin-top: 5px;">⚠️ Peringatan: Pembayaran melebihi sisa hutang!</small>
                    </div>
                </div>
            </div>

            <div class="form-group mb-4">
                <label for="tanggal_bayar" class="text-dark font-weight-bold">Tanggal Pembayaran</label>
                <input type="date" name="tanggal_bayar" id="tanggal_bayar" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <div class="form-actions d-flex gap-2">
                <button type="submit" name="submit" id="btnSubmit" class="btn btn-primary btn-lg px-4">Simpan Pembayaran</button>
                <a href="data_angsuran.php" class="btn btn-outline-secondary btn-lg px-4">Batal</a>
            </div>
        </form>
    </div>
</div>

<!-- Script AJAX dan Validasi Real-time -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#id_pinjaman').change(function() {
        var id_pinjaman = $(this).val();
        if (id_pinjaman != "") {
            $.ajax({
                url: 'get_sisa_pinjaman.php',
                type: 'POST',
                data: {id_pinjaman: id_pinjaman},
                success: function(response) {
                    // Perbaikan: Ambil hanya angka dari data mentah
                    // Kita pecah dulu kalau-kalau server ngirim data lebih dari satu baris
                    var lines = response.trim().split(/\s+/);
                    var rawValue = lines[lines.length - 1]; // Ambil elemen terakhir (biasanya nominalnya)
                    var cleanData = rawValue.replace(/[^0-9]/g, '');
                    
                    var sisa = parseInt(cleanData);
                    
                    if (isNaN(sisa)) sisa = 0;

                    // Update Nilai Hidden dan Tampilan
                    $('#sisa_pinjaman_hidden').val(sisa);
                    $('#display_sisa').text('Rp ' + sisa.toLocaleString('id-ID'));
                    
                    // Reset Field Input Bayar
                    $('#jumlah_bayar').val('');
                    $('#msg_error').hide();
                    $('#btnSubmit').prop('disabled', false);
                    $('#jumlah_bayar').removeClass('is-invalid');
                },
                error: function() {
                    $('#display_sisa').text('Gagal memuat data');
                }
            });
        } else {
            $('#display_sisa').text('Rp 0');
            $('#sisa_pinjaman_hidden').val(0);
        }
    });

    // Validasi Gembok Anti-Jebol
    $('#jumlah_bayar').on('input', function() {
        var bayar = parseInt($(this).val());
        var sisa = parseInt($('#sisa_pinjaman_hidden').val());

        if (isNaN(bayar)) bayar = 0;

        // Logika pencegahan bayar lebih dari sisa
        if (bayar > sisa && sisa > 0) {
            $('#msg_error').fadeIn();
            $('#btnSubmit').prop('disabled', true);
            $(this).addClass('is-invalid');
        } else {
            $('#msg_error').fadeOut();
            $('#btnSubmit').prop('disabled', false);
            $(this).removeClass('is-invalid');
        }
    });
});
</script>

<?php include 'footer.php'; ?>