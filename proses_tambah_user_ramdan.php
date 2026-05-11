<?php
include 'auth_ramdan.php';
include 'koneksi_ramdan.php';

if (isset($_POST['submit'])) {
    // Hanya Admin yang boleh mengeksekusi ini
    if ($_SESSION['role'] != 'Admin') {
        die("Akses Ditolak!");
    }

    // Ambil data dari form
    $nama_ramdan = mysqli_real_escape_string($koneksi, trim($_POST['nama_ramdan']));
    $username_ramdan = mysqli_real_escape_string($koneksi, trim($_POST['username_ramdan']));
    $password_plain = $_POST['password_ramdan'];
    $id_role_ramdan = isset($_POST['id_role_ramdan']) ? mysqli_real_escape_string($koneksi, $_POST['id_role_ramdan']) : '';

    echo "<!DOCTYPE html><html><head><meta name='viewport' content='width=device-width, initial-scale=1'><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script><style>body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; display:flex; justify-content:center; align-items:center; height:100vh; margin:0; }</style></head><body>";

    // Validasi input role tidak boleh kosong
    if (empty($id_role_ramdan)) {
        echo "<script>
            Swal.fire({ icon: 'error', title: 'Data Tidak Lengkap', text: 'Pilih role pengguna terlebih dahulu.', confirmButtonColor: '#0d6efd' })
            .then(() => { window.history.back(); });
        </script></body></html>";
        exit();
    }

    // Cek apakah username_ramdan sudah dipakai
    $cek = mysqli_query($koneksi, "SELECT username_ramdan FROM tb_user_ramdan WHERE username_ramdan = '$username_ramdan'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>
            Swal.fire({ icon: 'warning', title: 'Username Terpakai', text: 'Gunakan username_ramdan lain.', confirmButtonColor: '#0d6efd' })
            .then(() => { window.history.back(); });
        </script></body></html>";
        exit();
    }

    // ENKRIPSI PASSWORD: Menggunakan password_hash (Standar Keamanan)
    $password_hashed = password_hash($password_plain, PASSWORD_DEFAULT); 

    // Simpan ke database
    $query = "INSERT INTO tb_user_ramdan (nama_ramdan, username_ramdan, password_ramdan, id_role_ramdan) VALUES ('$nama_ramdan', '$username_ramdan', '$password_hashed', '$id_role_ramdan')";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        echo "<script>
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Pengguna baru telah didaftarkan.', showConfirmButton: false, timer: 2000 })
            .then(() => { window.location.href = 'data_user_ramdan.php'; });
        </script>";
    } else {
        echo "<script>
            Swal.fire({ icon: 'error', title: 'Gagal Simpan', text: 'Database Error: " . mysqli_error($koneksi) . "', confirmButtonColor: '#d33' })
            .then(() => { window.history.back(); });
        </script>";
    }
    echo "</body></html>";
}
?>