<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_name      = mysqli_real_escape_string($koneksi, $_POST['user_name']);
    $user_phone     = mysqli_real_escape_string($koneksi, $_POST['user_phone']);
    $tanggal        = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $lapangan_id    = mysqli_real_escape_string($koneksi, $_POST['lapangan_id']);
    $payment_method = mysqli_real_escape_string($koneksi, $_POST['payment_method']);
    $jam_pilihan    = isset($_POST['jam_pilihan']) ? $_POST['jam_pilihan'] : [];

    if (empty($jam_pilihan)) {
        echo "<script>alert('Pilih minimal 1 jam!'); window.history.back();</script>";
        exit;
    }

    // Validasi Ganda: Cek ulang sebelum menyimpan apakah jam sudah dipesan orang lain
    foreach ($jam_pilihan as $jam) {
        $cek_validasi = mysqli_query($koneksi, "SELECT d.id FROM booking_details d 
                                                 JOIN bookings b ON d.booking_id = b.id 
                                                 WHERE d.field_id = '$lapangan_id' 
                                                 AND b.booking_date = '$tanggal' 
                                                 AND b.status IN ('Pending', 'Paid') 
                                                 AND d.start_hour = '$jam'");
        if (mysqli_num_rows($cek_validasi) > 0) {
            echo "<script>alert('Maaf, Jam " . sprintf('%02d:00', $jam) . " baru saja dipesan orang lain! Silakan pilih jam lain.'); window.location.href='index.php';</script>";
            exit;
        }
    }

    $field_query = mysqli_query($koneksi, "SELECT price_per_hour FROM fields WHERE id = '$lapangan_id'");
    $field = mysqli_fetch_assoc($field_query);
    
    $total_jam   = count($jam_pilihan);
    $total_price = $total_jam * $field['price_per_hour'];

    $query_booking = "INSERT INTO bookings (user_name, user_phone, booking_date, total_price, payment_method, status) 
                      VALUES ('$user_name', '$user_phone', '$tanggal', '$total_price', '$payment_method', 'Pending')";
    
    if (mysqli_query($koneksi, $query_booking)) {
        $booking_id = mysqli_insert_id($koneksi);

        foreach ($jam_pilihan as $jam) {
            mysqli_query($koneksi, "INSERT INTO booking_details (booking_id, field_id, start_hour) 
                                    VALUES ('$booking_id', '$lapangan_id', '$jam')");
        }

        echo "<script>alert('Booking Berhasil! Total Bayar: Rp. " . number_format($total_price) . " via " . $payment_method . ". Status: Menunggu Konfirmasi Admin.'); window.location.href='index.php';</script>";
    } else {
        echo "Gagal: " . mysqli_error($koneksi);
    }
}
?>