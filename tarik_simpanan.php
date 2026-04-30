<?php
include 'auth.php';
include 'koneksi.php';

if ($_SESSION['role'] == 'Anggota') {
    header("Location: dashboard_admin.php");
    exit();
}

/**
 * LOGIKA TANGKAP ID: 
 * Kita pastikan ID dibersihkan dari spasi agar pembandingan 'selected' tidak gagal.
 */
$id_anggota_target = isset($_GET['id_anggota']) ? trim($_GET['id_anggota']) : '';

include 'header.php';
?>

<div class="content">
    <div class="form-container" style="max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
        <h2 class="mb-4">Tarik Simpanan Anggota</h2>
        <p class="text-muted">Proses pengambilan saldo simpanan sukarela anggota.</p>
        <hr class="mb-4">

        <form action="proses_tarik_simpanan.php" method="POST" id="formTarik">
            <div class="form-group mb-4">
                <label class="text-dark font-weight-bold mb-2">Pilih Nama Anggota</label>
                <select name="id_anggota" id="id_anggota" class="form-control form-control-lg" required>
                    <option value="">-- Cari/Pilih Anggota --</option>
                    <?php
                    // Kita urutkan berdasarkan nama agar lebih rapi
                    $q_anggota = mysqli_query($koneksi, "SELECT * FROM tb_anggota_ramdan ORDER BY nama ASC");
                    while ($d = mysqli_fetch_assoc($q_anggota)) {
                        /**
                         * FIX: Kita konversi ke string agar pembandingan '1' == 1 selalu benar.
                         */
                        $selected = ((string)$d['id_anggota'] === (string)$id_anggota_target) ? 'selected' : '';
                        echo "<option value='".$d['id_anggota']."' $selected>".strtoupper($d['nama'])."</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card p-3 bg-light border-0 shadow-sm" style="border-left: 5px solid #198754 !important;">
                        <label class="text-success small fw-bold mb-1">SALDO SAAT INI</label>
                        <h3 id="display_saldo" style="font-weight: 800; color: #212529;">Rp 0</h3>
                        <input type="hidden" id="saldo_hidden">
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="form-group">
                        <label class="text-dark font-weight-bold mb-2">Jumlah Penarikan (Rp)</label>
                        <input type="number" name="jumlah_tarik" id="jumlah_tarik" class="form-control form-control-lg" placeholder="Masukkan nominal..." required disabled>
                        <div id="msg_error" class="text-danger small mt-2 fw-bold" style="display:none;">
                            <i class="bi bi-exclamation-circle me-1"></i> Saldo tidak cukup!
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group mb-4">
                <label class="text-dark font-weight-bold mb-2">Tanggal Penarikan</label>
                <input type="date" name="tanggal" class="form-control form-control-lg" value="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <div class="form-actions d-flex gap-2 mt-4">
                <button type="submit" name="submit" id="btnSubmit" class="btn btn-success btn-lg px-5" disabled>Proses Penarikan</button>
                <a href="data_simpanan.php" class="btn btn-outline-secondary btn-lg px-4">Batal</a>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Fungsi untuk mengambil saldo via AJAX
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
                    
                    // Aktifkan input jika saldo lebih dari 0
                    if (saldo > 0) {
                        $('#jumlah_tarik').prop('disabled', false).focus();
                    } else {
                        $('#jumlah_tarik').prop('disabled', true);
                        $('#display_saldo').addClass('text-danger');
                    }
                },
                error: function() {
                    console.error("Gagal mengambil data saldo.");
                }
            });
        } else {
            $('#display_saldo').text('Rp 0');
            $('#jumlah_tarik').prop('disabled', true);
        }
    }

    /**
     * AUTO-TRIGGER: 
     * Saat halaman selesai dimuat, kita cek apakah ada ID yang sudah terpilih oleh PHP.
     */
    var initialId = $('#id_anggota').val();
    if (initialId && initialId !== "") {
        fetchSaldo(initialId);
    }

    // Trigger saat dropdown diubah manual oleh user
    $('#id_anggota').on('change', function() {
        fetchSaldo($(this).val());
        $('#jumlah_tarik').val(''); // Reset input nominal
        $('#msg_error').hide();
        $('#btnSubmit').prop('disabled', true);
    });

    // Validasi nominal penarikan terhadap saldo
    $('#jumlah_tarik').on('input', function() {
        var tarik = parseInt($(this).val()) || 0;
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