<?php
include 'auth.php';
include 'koneksi.php';
include 'header.php';

// Menangkap ID Pinjaman dari URL jika ada (fitur shortcut)
$id_pinjaman_get = isset($_GET['id']) ? mysqli_real_escape_string($koneksi, $_GET['id']) : '';
?>

<style>
    /* CSS Minimalis & Konsisten */
    :root {
        --ksp-dark: #212529;
        --ksp-primary: #0d6efd;
        --ksp-muted: #6c757d;
        --ksp-bg-light: #f8f9fa;
    }

    .form-angsuran-card {
        background: #ffffff;
        border: 1px solid #dee2e6;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    }

    .info-panel {
        background-color: var(--ksp-bg-light);
        border-left: 4px solid var(--ksp-primary);
        border-radius: 8px;
    }

    .label-minimal {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--ksp-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Base input style */
    .input-clean {
        border: 1px solid #ced4da;
        padding: 12px;
        border-radius: 8px;
        font-size: 1rem;
        height: 50px; 
        width: 100%;
        outline: none;
        transition: border-color 0.2s;
    }

    .input-clean:focus {
        border-color: var(--ksp-primary);
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.1);
    }

    .btn-save {
        padding: 14px;
        font-weight: 600;
        border-radius: 8px;
        transition: 0.2s;
    }

    .btn-save:hover {
        transform: translateY(-1px);
    }
</style>

<div class="content mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="form-angsuran-card p-4 p-md-5">
                
                <!-- Judul Section -->
                <div class="text-center mb-4">
                    <h3 class="fw-bold mb-1" style="color: var(--ksp-dark);">Input Angsuran</h3>
                    <p class="text-muted small">Kelola pelunasan pinjaman anggota dengan tepat.</p>
                </div>

                <form action="proses_tambah_angsuran.php" method="POST">
                    
                    <!-- Pilih Anggota & Pinjaman -->
                    <div class="mb-4">
                        <label class="label-minimal d-block mb-2">Anggota dengan Pinjaman Aktif</label>
                        <select name="id_pinjaman" id="id_pinjaman" class="form-select input-clean" required onchange="updateDebtInfo()">
                            <option value="">-- Cari Nama Anggota --</option>
                            <?php
                            /**
                             * PERUBAHAN: Order by id_anggota ASC (melalui tabel p)
                             * TAMPILAN: Hanya menampilkan nama
                             */
                            $query = mysqli_query($koneksi, "SELECT p.*, a.nama 
                                                            FROM tb_pinjaman_ramdan p 
                                                            JOIN tb_anggota_ramdan a ON p.id_anggota = a.id_anggota 
                                                            WHERE p.status_pinjaman = 'Disetujui' 
                                                            ORDER BY a.id_anggota ASC");
                            
                            while ($row = mysqli_fetch_assoc($query)) {
                                $selected = ($id_pinjaman_get == $row['id_pinjaman']) ? 'selected' : '';
                                
                                $sisa = (int)$row['sisa_pinjaman'];
                                $total = (int)$row['total_pinjaman'];
                                $bunga = $row['bunga'];

                                echo "<option value='".$row['id_pinjaman']."' 
                                              data-sisa='$sisa' 
                                              data-total='$total' 
                                              data-bunga='$bunga'
                                              $selected>";
                                echo htmlspecialchars($row['nama']);
                                echo "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Panel Informasi Hutang -->
                    <div id="panel_info" class="info-panel p-4 mb-4" style="display: none;">
                        <div class="row text-center text-md-start">
                            <div class="col-6">
                                <span class="label-minimal d-block mb-1">Total Pinjam</span>
                                <strong id="txt_total" class="text-dark">Rp 0</strong>
                            </div>
                            <div class="col-6 text-md-end">
                                <span class="label-minimal d-block mb-1">Bunga</span>
                                <strong id="txt_bunga" class="text-primary">0%</strong>
                            </div>
                        </div>
                        <hr class="my-3 opacity-10">
                        <div class="text-center">
                            <span class="label-minimal d-block mb-1 text-primary">Sisa Hutang Saat Ini</span>
                            <h2 id="display_sisa" class="fw-bold mb-0 text-primary">Rp 0</h2>
                            <input type="hidden" name="sisa_pinjaman_input" id="sisa_pinjaman_input">
                        </div>
                    </div>

                    <!-- Nominal Bayar (Tanpa RP, dengan Format Angka) -->
                    <div class="mb-4">
                        <label class="label-minimal d-block mb-2">Jumlah Pembayaran</label>
                        <input type="text" id="jumlah_bayar_format" class="input-clean fw-bold" placeholder="Masukkan nominal (contoh: 500.000)" required oninput="handlePaymentInput(this)">
                        <!-- Input hidden untuk dikirim ke database (angka polos) -->
                        <input type="hidden" name="jumlah_bayar" id="jumlah_bayar">
                        
                        <div id="msg_error" class="text-danger small mt-2 fw-bold" style="display: none;">
                            <i class="bi bi-info-circle me-1"></i> Jumlah bayar melebihi sisa hutang!
                        </div>
                    </div>

                    <!-- Tanggal Pembayaran -->
                    <div class="mb-4">
                        <label class="label-minimal d-block mb-2">Tanggal Pembayaran</label>
                        <input type="date" name="tanggal_bayar" class="form-control input-clean w-100" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>

                    <!-- Tombol Konfirmasi -->
                    <div class="d-grid mt-5">
                        <button type="submit" name="submit" id="btn_submit" class="btn btn-primary btn-save shadow-sm">
                            Simpan Pembayaran
                        </button>
                        <a href="data_angsuran.php" class="btn btn-link mt-2 text-decoration-none text-muted small text-center">Kembali ke Daftar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Fungsi Format ke Rupiah untuk tampilan teks statis
    function formatRP(angka) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(angka);
    }

    // Fungsi Format Ribuan untuk Input
    function formatNumber(n) {
        return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // Menangani Input Pembayaran (Format & Validasi)
    function handlePaymentInput(input) {
        // Ambil nilai mentah (hanya angka)
        let rawValue = input.value.replace(/\D/g, "");
        
        // Update input tersembunyi untuk form submit
        document.getElementById('jumlah_bayar').value = rawValue;
        
        // Tampilkan format titik di input visual
        input.value = formatNumber(rawValue);
        
        checkPayment();
    }

    // Update tampilan saat anggota dipilih
    function updateDebtInfo() {
        const sel = document.getElementById('id_pinjaman');
        const opt = sel.options[sel.selectedIndex];
        const pan = document.getElementById('panel_info');
        
        if (opt.value) {
            pan.style.display = 'block';
            
            const sisa = parseInt(opt.getAttribute('data-sisa'));
            const total = parseInt(opt.getAttribute('data-total'));
            const bunga = opt.getAttribute('data-bunga');

            document.getElementById('display_sisa').innerText = formatRP(sisa);
            document.getElementById('txt_total').innerText = formatRP(total);
            document.getElementById('txt_bunga').innerText = bunga + '%';
            document.getElementById('sisa_pinjaman_input').value = sisa;
            
            checkPayment();
        } else {
            pan.style.display = 'none';
        }
    }

    // Validasi input bayar
    function checkPayment() {
        const bayarRaw = document.getElementById('jumlah_bayar').value;
        const bayar = parseInt(bayarRaw) || 0;
        const sisa = parseInt(document.getElementById('sisa_pinjaman_input').value) || 0;
        
        const btn = document.getElementById('btn_submit');
        const msg = document.getElementById('msg_error');
        const inp = document.getElementById('jumlah_bayar_format');

        if (bayar > sisa) {
            msg.style.display = 'block';
            inp.style.borderColor = '#e74c3c';
            btn.disabled = true;
        } else if (bayar <= 0) {
            msg.style.display = 'none';
            inp.style.borderColor = '#ced4da';
            btn.disabled = true;
        } else {
            msg.style.display = 'none';
            inp.style.borderColor = '#0d6efd';
            btn.disabled = false;
        }
    }

    // Inisialisasi awal
    window.onload = updateDebtInfo;
</script>

<?php include 'footer.php'; ?>