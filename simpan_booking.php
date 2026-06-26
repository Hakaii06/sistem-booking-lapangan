<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_name      = mysqli_real_escape_string($koneksi, $_POST['user_name']);
    $user_phone     = mysqli_real_escape_string($koneksi, $_POST['user_phone']);
    $tanggal        = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $lapangan_id    = mysqli_real_escape_string($koneksi, $_POST['lapangan_id']);
    $payment_method = mysqli_real_escape_string($koneksi, $_POST['payment_method']);
    $total_price    = mysqli_real_escape_string($koneksi, $_POST['total_price']);
    $jam_pilihan    = isset($_POST['jam_pilihan']) ? $_POST['jam_pilihan'] : [];

    $nama_file_bukti = "";

    // Logika upload file jika menggunakan metode Transfer
    if (strpos($payment_method, 'Transfer') !== false) {
        if (isset($_FILES['bukti_transfer']) && $_FILES['bukti_transfer']['error'] == 0) {
            $target_dir = "uploads/";
            
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $extension = pathinfo($_FILES['bukti_transfer']['name'], PATHINFO_EXTENSION);
            // Rename file secara unik berdasarkan waktu dan nomor HP agar tidak bentrok
            $nama_file_bukti = "BUKTI_" . time() . "_" . $user_phone . "." . $extension;
            $target_file = $target_dir . $nama_file_bukti;

            if (!move_uploaded_file($_FILES['bukti_transfer']['tmp_name'], $target_file)) {
                echo "<script>alert('Gagal mengupload file bukti gambar!'); window.history.back();</script>";
                exit;
            }
        } else {
            echo "<script>alert('Bukti transfer wajib diunggah untuk metode pembayaran Bank!'); window.history.back();</script>";
            exit;
        }
    }

    // UPDATE: Query INSERT sekarang sudah memasukkan variabel $nama_file_bukti ke kolom bukti_transfer
    $query_booking = "INSERT INTO bookings (user_name, user_phone, booking_date, total_price, payment_method, bukti_transfer, status) 
                      VALUES ('$user_name', '$user_phone', '$tanggal', '$total_price', '$payment_method', '$nama_file_bukti', 'Pending')";
    
    if (mysqli_query($koneksi, $query_booking)) {
        $booking_id = mysqli_insert_id($koneksi);

        foreach ($jam_pilihan as $jam) {
            mysqli_query($koneksi, "INSERT INTO booking_details (booking_id, field_id, start_hour) 
                                    VALUES ('$booking_id', '$lapangan_id', '$jam')");
        }

        echo "<script>alert('Pemesanan Berhasil Disimpan! Status: Pending Menunggu Validasi Admin.'); window.location.href='index.php';</script>";
    } else {
        echo "Gagal menyimpan data: " . mysqli_error($koneksi);
    }
}
?>