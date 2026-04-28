<?php
include 'auth.php';
include 'koneksi.php';

// Ambil data pinjaman yang statusnya 'Disetujui' (belum lunas)
$query_pinjaman = mysqli_query($koneksi, "
    SELECT p.*, a.nama 
    FROM tb_pinjaman_ramdan p
    JOIN tb_anggota_ramdan a ON p.id_anggota = a.id_anggota
    WHERE p.status_pinjaman = 'Disetujui'
");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catat Angsuran</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=DM+Serif+Display&display=swap" rel="stylesheet">
</head>

<body>

    <h2>Form Pembayaran Angsuran</h2>
    <a href="data_angsuran.php">Kembali ke Data Angsuran</a>
    <br><br>

    <form method="POST" action="proses_tambah_angsuran.php">
        <label>Pilih Pinjaman Anggota:</label><br>
        <select name="id_pinjaman" id="id_pinjaman" required>
            <option value="">-- Pilih Pinjaman --</option>
            <?php while ($pinjaman = mysqli_fetch_assoc($query_pinjaman)) { ?>
                <option value="<?php echo $pinjaman['id_pinjaman']; ?>">
                    <?php echo $pinjaman['nama']; ?> </option>
            <?php } ?>
        </select>
        <div id="info_pinjaman" class="alert alert-info mt-3" style="display: none;">
            <table style="width: 100%; margin-bottom: 0;">
                <tr>
                    <td style="width: 40%;">Total Hutang (Pokok + Bunga)</td>
                    <td style="width: 2%;">:</td>
                    <th id="total_hutang"></th>
                </tr>
                <tr>
                    <td>Sudah Dibayar</td>
                    <td>:</td>
                    <th id="sudah_dibayar"></th>
                </tr>
                <tr>
                    <td>Sisa Hutang</td>
                    <td>:</td>
                    <th id="sisa_hutang"></th>
                </tr>
            </table>
        </div>
        <br><br>

        <label>Jumlah Bayar (Rp):</label><br>
        <input type="number" name="jumlah_bayar" required><br><br>

        <button type="submit">Proses Pembayaran</button>
    </form>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            // Kalau dropdown nama pinjaman diubah/dipilih
            $('#id_pinjaman').change(function() {
                var id_pinjaman = $(this).val();

                if (id_pinjaman != '') {
                    // Kirim request ke 'kalkulator gaib' tadi
                    $.ajax({
                        url: 'get_sisa_pinjaman.php',
                        type: 'POST',
                        data: {
                            id_pinjaman: id_pinjaman
                        },
                        dataType: 'json',
                        success: function(response) {
                            // Tampilkan kotak info dan isi angkanya
                            $('#info_pinjaman').slideDown();
                            $('#total_hutang').text(response.total_pinjaman);
                            $('#sudah_dibayar').text(response.sudah_dibayar);
                            $('#sisa_hutang').text(response.sisa_pinjaman);
                        }
                    });
                } else {
                    // Sembunyikan kalau admin milih "Pilih Pinjaman..." (Kosong)
                    $('#info_pinjaman').slideUp();
                }
            });
        });
    </script>
</body>

</html>