<?php
// Mengaktifkan laporan error untuk mempermudah monitoring jaringan cloud
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Kredensial Database Cloud Aiven Sesuai Gambar Kamu
$host     = "mysql-1ef6f5bb-haikalabdullah356-0cf1.l.aivencloud.com"; 
$user     = "avnadmin";
$password = "AVNS_mj_9TgmHIR6WUNfbdfG"; // <-- Ganti dengan password asli dari web Aiven
$database = "defaultdb"; 
$port     = 22954; 

// Melakukan koneksi dengan menyertakan Port khusus Aiven
$koneksi = mysqli_connect($host, $user, $password, $database, $port);

// Validasi koneksi
if (!$koneksi) {
    die("Koneksi Database Cloud Gagal: " . mysqli_connect_error());
}
?>
