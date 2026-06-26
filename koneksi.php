<?php
// Mengaktifkan laporan error untuk monitoring jaringan cloud
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Kredensial Database Cloud Aiven Sesuai Akun Kamu
$host     = "mysql-1ef6f5bb-haikalabdullah356-0cf1.l.aivencloud.com"; 
$user     = "avnadmin";
$password = "AVNS_mj_9TgmHIR6WUNfbdfG"; // <-- Ganti dengan password asli dari web Aiven kamu
$database = "defaultdb"; 
$port     = 22954; 

// Melakukan koneksi ke server cloud
$koneksi = mysqli_connect($host, $user, $password, $database, $port);

// Validasi koneksi
if (!$koneksi) {
    die("Koneksi Database Cloud Gagal: " . mysqli_connect_error());
}

// OTOMATIS MEMBUAT TABEL JIKA BELUM ADA DI CLOUD
$query_buat_tabel = "CREATE TABLE IF NOT EXISTS tbl_booking (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_lapangan INT NOT NULL,
    tanggal_main DATE NOT NULL,
    jam_main INT NOT NULL,
    nama_pemesan VARCHAR(100) NOT NULL,
    no_whatsapp VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

// Jalankan perintah pembuatan tabel
mysqli_query($koneksi, $query_buat_tabel);
?>
