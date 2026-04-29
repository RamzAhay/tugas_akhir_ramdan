<?php
include 'auth.php';
include 'koneksi.php';

// Proteksi: Hanya Admin/Petugas yang bisa melakukan penarikan
if ($_SESSION['role'] == 'Anggota') {
    header("Location: dashboard_admin.php");
    exit();
}

include 'header.php';
?>

<div class="content">
    <div class="form-container">
        <h2 class="mb-4">Tarik Simpanan Anggota</h2>
        <p class="text-muted">Gunakan form ini untuk memproses pengambilan uang simpanan anggota.</p>
        <hr>

        <form action="proses_tarik_simpanan.php" method="POST" id="formTarik">
            <div class="form-group mb-4">
                <label class="text-dark font-weight-bold">Pilih Anggota</label>
                <select name="id_anggota" id="id_anggota" class="form-control" required>
                    <option value="">-- Pilih Anggota --</option>
                    <?php
                    $q_anggota = mysqli_query($koneksi, "SELECT * FROM tb_anggota_ramdan");
                    while ($d = mysqli_fetch_assoc($q_anggota)) {
                        echo "<option value='".$d['id_anggota']."'>".$d['nama']."</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="card p-3 bg-light shadow-sm" style="border-left: 5px solid #198754;">
                        <label class="text-success font-weight-bold mb-1">Total Saldo Saat Ini</label>
                        <h3 id="display_saldo" style="color: #212529 !important; font-weight: 800; margin: 0;">Rp 0</h3>
                        <input type="hidden" id="saldo_hidden">
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <label class="text-dark font-weight-bold">Jumlah Penarikan (Rp)</label>
                        <input type="number" name="jumlah_tarik" id="jumlah_tarik" class="form-control form-control-lg" placeholder="Masukkan nominal..." required disabled>
                        <small class="text-danger" id="msg_error" style="display:none; font-weight: 700;">⚠️ Saldo tidak cukup untuk penarikan ini!</small>
                    </div>
                </div>
            </div>

            <div class="form-group mb-4">
                <label class="text-dark font-weight-bold">Tanggal Penarikan</label>
                <input type="date" name="tanggal" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <div class="form-actions d-flex gap-2">
                <button type="submit" name="submit" id="btnSubmit" class="btn btn-success btn-lg px-4" disabled>Proses Penarikan</button>
                <a href="data_simpanan.php" class="btn btn-outline-secondary btn-lg px-4">Batal</a>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Ambil saldo saat anggota dipilih
    $('#id_anggota').change(function() {
        var id = $(this).val();
        if (id != "") {
            $.ajax({
                url: 'get_total_simpanan.php',
                type: 'POST',
                data: {id_anggota: id},
                success: function(data) {
                    var saldo = parseInt(data);
                    $('#saldo_hidden').val(saldo);
                    $('#display_saldo').text('Rp ' + saldo.toLocaleString('id-ID'));
                    
                    // Aktifkan input tarik jika saldo > 0
                    if (saldo > 0) {
                        $('#jumlah_tarik').prop('disabled', false);
                    } else {
                        $('#jumlah_tarik').prop('disabled', true);
                    }
                }
            });
        }
    });

    // Validasi penarikan tidak boleh melebihi saldo
    $('#jumlah_tarik').on('input', function() {
        var tarik = parseInt($(this).val());
        var saldo = parseInt($('#saldo_hidden').val());

        if (tarik > saldo) {
            $('#msg_error').show();
            $('#btnSubmit').prop('disabled', true);
            $(this).addClass('is-invalid');
        } else if (tarik <= 0 || isNaN(tarik)) {
            $('#btnSubmit').prop('disabled', true);
        } else {
            $('#msg_error').hide();
            $('#btnSubmit').prop('disabled', false);
            $(this).removeClass('is-invalid');
        }
    });
});
</script>

<?php include 'footer.php'; ?>