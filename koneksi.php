<?php
$koneksi = mysqli_connect("localhost", "root", "", "db_koperasi_ramdan");

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>