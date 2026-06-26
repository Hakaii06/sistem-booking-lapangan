<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_name   = mysqli_real_escape_string($koneksi, $_POST['user_name']);
    $user_phone  = mysqli_real_escape_string($koneksi, $_POST['user_phone']);
    $tanggal     = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $lapangan_id = mysqli_real_escape_string($koneksi, $_POST['lapangan_id']);
    $jam_pilihan = isset($_POST['jam_pilihan']) ? $_POST['jam_pilihan'] : [];

    if (empty($jam_pilihan)) {
        echo "<script>alert('Silakan pilih jam terlebih dahulu!'); window.history.back();</script>";
        exit;
    }

    // Hitung total harga (Misal: per jam Rp 50.000, sesuaikan dengan logic kamu)
    $harga_per_jam = 50000; 
    $total_price   = count($jam_pilihan) * $harga_per_jam;

    // Convert array jam ke string untuk dilempar lewat form/URL
    $jam_string = implode(",", $jam_pilihan);

    // Alihkan ke proses pembayaran dengan membawa data via GET/URL agar tidak hilang di Vercel
    $query_string = http_build_query([
        'user_name'   => $user_name,
        'user_phone'  => $user_phone,
        'tanggal'     => $tanggal,
        'lapangan_id' => $lapangan_id,
        'total_price' => $total_price,
        'jam_pilihan' => $jam_string
    ]);

    header("Location: proses_pembayaran.php?" . $query_string);
    exit;
}
?>
