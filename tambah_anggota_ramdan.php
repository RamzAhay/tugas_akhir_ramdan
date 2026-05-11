<?php
include 'auth_ramdan.php';
include 'header_ramdan.php';
?>

<div class="content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                
                <!-- Breadcrumb / Navigation Back -->
                <div class="mb-4">
                    <a href="data_anggota_ramdan.php" class="text-decoration-none text-muted small fw-bold">
                        <i class="bi bi-arrow-left me-1"></i> KEMBALI KE DATA ANGGOTA
                    </a>
                </div>

                <!-- Form Card -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <!-- Card Header dengan Gradien sesuai Tema -->
                    <div class="card-header p-4 border-0" style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);">
                        <div class="d-flex align-items-center text-white">
                            <div class="bg-white bg-opacity-25 rounded-3 p-3 me-3">
                                <i class="bi bi-person-plus-fill fs-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0">Registrasi Anggota Baru</h5>
                                <p class="small mb-0 opacity-75">Silakan lengkapi identitas nasabah di bawah ini.</p>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4 p-lg-5 bg-white">
                        <form method="POST" action="proses_tambah_anggota_ramdan.php">
                            
                            <!-- Input Nama -->
                            <div class="mb-4">
                                <label class="form-label fw-bold small text-muted text-uppercase mb-2">Nama Lengkap Anggota</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-person"></i></span>
                                    <input type="text" name="nama_ramdan" class="form-control bg-light border-start-0" 
                                           placeholder="Masukkan nama sesuai KTP" required autofocus>
                                </div>
                            </div>

                            <!-- Input No HP -->
                            <div class="mb-4">
                                <label class="form-label fw-bold small text-muted text-uppercase mb-2">Nomor Handphone (WhatsApp)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-phone"></i></span>
                                    <input type="number" name="no_hp_ramdan" class="form-control bg-light border-start-0" 
                                           placeholder="Contoh: 081234567xxx" required>
                                </div>
                                <div class="form-text mt-1 text-muted small">Pastikan nomor aktif untuk keperluan notifikasi transaksi.</div>
                            </div>

                            <!-- Input Alamat -->
                            <div class="mb-4">
                                <label class="form-label fw-bold small text-muted text-uppercase mb-2">Alamat Lengkap Rumah</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 align-items-start pt-2"><i class="bi bi-geo-alt"></i></span>
                                    <textarea name="alamat_ramdan" class="form-control bg-light border-start-0" 
                                              rows="3" placeholder="Jl. Nama Jalan, No. Rumah, RT/RW, Kecamatan..." required></textarea>
                                </div>
                            </div>

                            <hr class="my-4 opacity-50">

                            <!-- Action Buttons -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg rounded-3 fw-bold shadow-sm py-3">
                                    <i class="bi bi-check-circle-fill me-2"></i>Simpan Data Anggota
                                </button>
                                <button type="reset" class="btn btn-link text-muted text-decoration-none fw-medium py-2">
                                    Kosongkan Form
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

                <!-- Footer Tip -->
                <div class="text-center mt-4 text-muted small">
                    <i class="bi bi-shield-check me-1 text-success"></i> Seluruh data yang diinputkan akan terenkripsi dan aman.
                </div>

            </div>
        </div>
    </div>
</div>

<?php include 'footer_ramdan.php'; ?>