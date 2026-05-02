<?php
include 'auth.php';
include 'koneksi.php';

// Proteksi: Hanya Admin/Petugas
if ($_SESSION['role'] == 'Anggota') {
    header("Location: dashboard_admin.php");
    exit();
}

/**
 * TANGKAP ID DARI URL:
 * Digunakan saat petugas klik tombol "Tarik" di data_simpanan.php
 */
$id_anggota_target = isset($_GET['id_anggota']) ? trim($_GET['id_anggota']) : '';

include 'header.php';
?>

<div class="content">
    <div class="form-container" style="max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.08);">
        <div class="d-flex align-items-center mb-4">
            <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                <i class="bi bi-cash-stack fs-4"></i>
            </div>
            <div>
                <h3 class="fw-bold mb-0">Tarik Simpanan</h3>
                <p class="text-muted mb-0">Proses pengambilan saldo simpanan anggota.</p>
            </div>
        </div>
        <hr class="mb-4">

        <form action="proses_tarik_simpanan.php" method="POST" id="formTarik">
            <div class="form-group mb-4">
                <label class="label-minimal fw-bold mb-2">Pilih Nama Anggota</label>
                <select name="id_anggota" id="id_anggota" class="form-select form-select-lg border-2" required>
                    <option value="">-- Cari Nama Anggota --</option>
                    <?php
                    $q_anggota = mysqli_query($koneksi, "SELECT * FROM tb_anggota_ramdan ORDER BY nama ASC");
                    while ($d = mysqli_fetch_assoc($q_anggota)) {
                        $selected = ((string)$d['id_anggota'] === (string)$id_anggota_target) ? 'selected' : '';
                        echo "<option value='".$d['id_anggota']."' $selected>".strtoupper($d['nama'])."</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="p-4 rounded-4 border-0 shadow-sm h-100" style="background-color: #f0fdf4; border-left: 5px solid #22c55e !important;">
                        <span class="text-success small fw-bold text-uppercase d-block mb-2">Saldo Sukarela Saat Ini</span>
                        <h2 id="display_saldo" class="fw-bold text-dark mb-0">Rp 0</h2>
                        <input type="hidden" id="saldo_hidden">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="fw-bold text-dark mb-2">Nominal Penarikan (Rp)</label>
                        <!-- PERUBAHAN: Input text visual (untuk format titik) & input hidden (untuk dikirim ke database) -->
                        <input type="text" id="jumlah_tarik_format" class="form-control form-control-lg border-2" placeholder="Contoh: 500.000" required disabled>
                        <input type="hidden" name="jumlah_tarik" id="jumlah_tarik">
                        
                        <div id="msg_error" class="text-danger small mt-2 fw-bold" style="display:none;">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Saldo tidak mencukupi!
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group mb-4">
                <label class="fw-bold text-dark mb-2">Tanggal Penarikan</label>
                <input type="date" name="tanggal" class="form-control form-control-lg border-2" value="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <div class="d-grid gap-2 mt-5">
                <button type="submit" name="submit" id="btnSubmit" class="btn btn-danger btn-lg fw-bold py-3 shadow" disabled>
                    Konfirmasi Penarikan Tunai
                </button>
                <a href="data_simpanan.php" class="btn btn-link text-muted text-decoration-none">Batal dan Kembali</a>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    function fetchSaldo(id) {
        if (id !== "") {
            $.ajax({
                url: 'get_total_simpanan.php',
                type: 'POST',
                data: { id_anggota: id },
                success: function(data) {
                    var saldo = parseInt(data) || 0;
                    $('#saldo_hidden').val(saldo);
                    $('#display_saldo').text('Rp ' + saldo.toLocaleString('id-ID'));
                    
                    if (saldo > 0) {
                        // Buka kunci input format jika saldo tersedia
                        $('#jumlah_tarik_format').prop('disabled', false).focus();
                        $('#display_saldo').removeClass('text-danger').addClass('text-dark');
                    } else {
                        $('#jumlah_tarik_format').prop('disabled', true);
                        $('#display_saldo').addClass('text-danger');
                    }
                }
            });
        } else {
            $('#display_saldo').text('Rp 0');
            $('#jumlah_tarik_format').prop('disabled', true);
            $('#btnSubmit').prop('disabled', true);
        }
    }

    // AUTO-FILL: Jalankan jika ID sudah ada saat load (dari klik data simpanan)
    var currentId = $('#id_anggota').val();
    if (currentId) fetchSaldo(currentId);

    // Trigger saat ganti anggota
    $('#id_anggota').on('change', function() {
        fetchSaldo($(this).val());
        $('#jumlah_tarik_format').val(''); // Reset input format
        $('#jumlah_tarik').val('');        // Reset input hidden
        $('#msg_error').hide();
        $('#btnSubmit').prop('disabled', true);
    });

    // MASKING & VALIDASI PENARIKAN (Digabung agar lebih efisien)
    $('#jumlah_tarik_format').on('input', function() {
        // 1. Dapatkan angka murni & update input hidden
        let rawValue = $(this).val().replace(/\D/g, "");
        $('#jumlah_tarik').val(rawValue); 
        
        // 2. Beri format titik di input visual
        $(this).val(rawValue.replace(/\B(?=(\d{3})+(?!\d))/g, "."));

        // 3. Validasi Saldo
        var tarik = parseInt(rawValue) || 0;
        var saldo = parseInt($('#saldo_hidden').val()) || 0;

        if (tarik > saldo) {
            $('#msg_error').show();
            $('#btnSubmit').prop('disabled', true);
            $(this).addClass('is-invalid');
        } else if (tarik <= 0) {
            $('#btnSubmit').prop('disabled', true);
            $('#msg_error').hide();
            $(this).removeClass('is-invalid');
        } else {
            $('#msg_error').hide();
            $('#btnSubmit').prop('disabled', false);
            $(this).removeClass('is-invalid');
        }
    });
});
</script>

<?php include 'footer.php'; ?>