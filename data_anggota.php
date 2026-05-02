<?php
include 'auth.php';
include 'koneksi.php';
include 'header.php';

$query = mysqli_query($koneksi, "SELECT * FROM tb_anggota_ramdan ORDER BY id_anggota DESC");
?>

<div class="content">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="page-title mb-1">Data Anggota Koperasi</h4>
            <p class="page-subtitle">Kelola seluruh data anggota yang terdaftar di KSP Ramdan.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="cetak_anggota.php" target="_blank" class="btn btn-warning">
                <i class="bi bi-printer"></i> Cetak Laporan
            </a>
            <a href="tambah_anggota.php" class="btn btn-primary">
                <i class="bi bi-person-plus"></i> Tambah Anggota
            </a>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-ksp-head">
                        <tr>
                            <th class="text-center" style="width:60px;">No</th>
                            <th>Nama Lengkap</th>
                            <th>Alamat</th>
                            <th class="text-center">No HP</th>
                            <th class="text-center">Tgl Daftar</th>
                            <th class="text-center" style="width:160px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        if (mysqli_num_rows($query) == 0) {
                            echo "<tr><td colspan='6' class='text-center py-5 text-muted small'>
                                    <i class='bi bi-people fs-2 d-block mb-2 opacity-25'></i>
                                    Belum ada anggota terdaftar.
                                  </td></tr>";
                        }
                        while ($data = mysqli_fetch_assoc($query)) :
                        ?>
                        <tr>
                            <td class="text-center text-muted small"><?php echo $no++; ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <!-- Avatar Inisial -->
                                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center flex-shrink-0 text-white fw-bold"
                                         style="width:34px;height:34px;font-size:0.8rem;">
                                        <?php echo strtoupper(substr($data['nama'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($data['nama']); ?></div>
                                        <div class="text-muted small">ID: <?php echo $data['id_anggota']; ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-muted small"><?php echo htmlspecialchars($data['alamat']); ?></td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border" style="font-size:0.78rem;font-weight:600;">
                                    <i class="bi bi-telephone me-1 text-muted"></i><?php echo htmlspecialchars($data['no_hp']); ?>
                                </span>
                            </td>
                            <td class="text-center small text-muted">
                                <?php
                                    $tgl = $data['tanggal_daftar'] ?? null;
                                    echo $tgl ? date('d M Y', strtotime($tgl)) : '-';
                                ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="edit_anggota.php?id=<?php echo $data['id_anggota']; ?>"
                                       class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="hapus_anggota.php?id=<?php echo $data['id_anggota']; ?>"
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Yakin ingin menghapus anggota <?php echo htmlspecialchars(addslashes($data['nama'])); ?>?')">
                                        <i class="bi bi-trash"></i> Hapus
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer count -->
        <div class="card-footer d-flex justify-content-between align-items-center py-2 px-4">
            <span class="small text-muted">
                Total <strong class="text-dark"><?php echo mysqli_num_rows($query); ?></strong> anggota terdaftar
            </span>
            <a href="tambah_anggota.php" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Tambah Baru
            </a>
        </div>
    </div>

</div>

<?php include 'footer.php'; ?>