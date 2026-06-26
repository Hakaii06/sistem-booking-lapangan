<?php
include 'koneksi.php';

// Ambil ID dari URL, bukan Session
$booking_id = isset($_GET['booking_id']) ? mysqli_real_escape_string($koneksi, $_GET['booking_id']) : '';

if (empty($booking_id)) {
    echo "ID Pemesanan tidak valid.";
    exit;
}

$query = mysqli_query($koneksi, "SELECT * FROM bookings WHERE id = '$booking_id'");
$data  = mysqli_fetch_assoc($query);

if (!$data) {
    echo "Data booking tidak ditemukan.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Receipt Pembayaran</title>
</head>
<body>
    <h2>Nota Pembayaran (Receipt)</h2>
    <hr>
    <p><strong>ID Booking:</strong> #<?php echo $data['id']; ?></p>
    <p><strong>Nama Pelanggan:</strong> <?php echo htmlspecialchars($data['user_name']); ?></p>
    <p><strong>Nomor Telepon:</strong> <?php echo htmlspecialchars($data['user_phone']); ?></p>
    <p><strong>Tanggal Main:</strong> <?php echo htmlspecialchars($data['booking_date']); ?></p>
    <p><strong>Total Bayar:</strong> Rp <?php echo number_format($data['total_price'], 0, ',', '.'); ?></p>
    <p><strong>Metode Pembayaran:</strong> <?php echo htmlspecialchars($data['payment_method']); ?></p>
    <p><strong>Status:</strong> <?php echo htmlspecialchars($data['status']); ?></p>
    <hr>
    <a href="index.php">Kembali ke Beranda</a>
</body>
</html>
