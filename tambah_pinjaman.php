<?php
include 'auth.php';
include 'koneksi.php';
include 'header.php';

// Ambil daftar anggota
$query_anggota = mysqli_query($koneksi, "SELECT * FROM tb_anggota_ramdan ORDER BY nama ASC");
?>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Form Pengajuan Pinjaman</h2>
        <a href="data_pinjaman.php" class="btn btn-outline-secondary">← Kembali</a>
    </div>

    <form method="POST" action="proses_tambah_pinjaman.php">
        <label>Pilih Anggota:</label><br>
        <select name="id_anggota" required>
            <option value="">-- Pilih Anggota --</option>
            <?php while($anggota = mysqli_fetch_assoc($query_anggota)) { ?>
                <option value="<?php echo $anggota['id_anggota']; ?>">
                    <?php echo $anggota['nama']; ?>
                </option>
            <?php } ?>
        </select><br><br>

        <label>Jumlah Pinjaman (Rp):</label><br>
        <input type="number" name="jumlah_pinjaman" required><br><br>

        <label>Bunga (%):</label><br>
        <input type="number" step="0.01" name="bunga" value="5" readonly><br><br>

        <label>Lama Pinjaman (Bulan):</label><br>
            <select name="lama_pinjaman" required class="form-control" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc; margin-bottom: 15px;">
                <option value="" disabled selected>-- Pilih Lama Pinjaman --</option>
                <?php 
                for($i = 1; $i <= 12; $i++){
                    echo "<option value='$i'>$i Bulan</option>";
                }
                ?>
            </select><br>

        <button type="submit">Ajukan Pinjaman</button>
    </form>

</body>
</html>