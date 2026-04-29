<?php
include 'auth.php';
include 'koneksi.php';
include 'header.php';

$query_anggota = mysqli_query($koneksi, "SELECT * FROM tb_anggota_ramdan ORDER BY nama ASC");
?>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Catat Transaksi Simpanan Baru</h2>
        <a href="data_simpanan.php" class="btn btn-outline-secondary">← Kembali</a>
    </div>

    <form method="POST" action="proses_tambah_simpanan.php" class="card p-4 shadow-sm">
        <div class="mb-3">
            <label class="form-label fw-bold">Pilih Anggota:</label>
            <select name="id_anggota" class="form-select" required>
                <option value="">-- Pilih Anggota --</option>
                <?php while($anggota = mysqli_fetch_assoc($query_anggota)) { ?>
                    <option value="<?php echo $anggota['id_anggota']; ?>">
                        <?php echo $anggota['nama']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Jenis Simpanan:</label>
            <select name="jenis_simpanan" class="form-select" required>
                <option value="">-- Pilih Jenis --</option>
                <option value="Pokok">Simpanan Pokok</option>
                <option value="Wajib">Simpanan Wajib</option>
                <option value="Sukarela">Simpanan Sukarela</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="form-label fw-bold">Jumlah Setoran (Rp):</label>
            <input type="number" name="jumlah" class="form-control" placeholder="Contoh: 50000" required>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success">Simpan Transaksi</button>
            <a href="data_simpanan.php" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
</div>

<?php include 'footer.php'; ?>