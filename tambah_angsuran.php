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
        <select name="id_pinjaman" required>
            <option value="">-- Pilih Pinjaman --</option>
            <?php while($pinjaman = mysqli_fetch_assoc($query_pinjaman)) { ?>
                <option value="<?php echo $pinjaman['id_pinjaman']; ?>">
                    <?php echo $pinjaman['nama']; ?> - Total Hutang: Rp <?php echo number_format($pinjaman['total_pinjaman'], 0, ',', '.'); ?>
                </option>
            <?php } ?>
        </select><br><br>

        <label>Jumlah Bayar (Rp):</label><br>
        <input type="number" name="jumlah_bayar" required><br><br>

        <button type="submit">Proses Pembayaran</button>
    </form>

</body>
</html>