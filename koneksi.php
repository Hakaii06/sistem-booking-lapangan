<?php
// Mengabaikan laporan error standar bawaan PHP agar tidak merusak layout HTML di Vercel
error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);
mysqli_report(MYSQLI_REPORT_OFF);

// Konfigurasi Database Lokal (XAMPP)
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_booking_lapangan"; 

// Mencoba melakukan koneksi ke database dengan peredam error (@)
$koneksi = @mysqli_connect($host, $user, $pass, $db);

// Menentukan status koneksi untuk digunakan di file index
if (!$koneksi) {
    $database_error_mode = true;
} else {
    $database_error_mode = false;
}
?>
