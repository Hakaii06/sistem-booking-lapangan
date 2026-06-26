<?php
include 'koneksi.php';

// Tangkap data booking dari URL
$user_name   = isset($_GET['user_name']) ? $_GET['user_name'] : '';
$user_phone  = isset($_GET['user_phone']) ? $_GET['user_phone'] : '';
$tanggal     = isset($_GET['tanggal']) ? $_GET['tanggal'] : '';
$lapangan_id = isset($_GET['lapangan_id']) ? $_GET['lapangan_id'] : '';
$total_price = isset($_GET['total_price']) ? $_GET['total_price'] : 0;
$jam_pilihan = isset($_GET['jam_pilihan']) ? $_GET['jam_pilihan'] : '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Proses Pembayaran</title>
</head>
<body>
    <h2>Detail Pembayaran</h2>
    <p>Nama: <?php echo htmlspecialchars($user_name); ?></p>
    <p>Total Harga: Rp <?php echo number_format($total_price, 0, ',', '.'); ?></p>

    <form action="simpan_booking.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="user_name" value="<?php echo htmlspecialchars($user_name); ?>">
        <input type="hidden" name="user_phone" value="<?php echo htmlspecialchars($user_phone); ?>">
        <input type="hidden" name="tanggal" value="<?php echo htmlspecialchars($tanggal); ?>">
        <input type="hidden" name="lapangan_id" value="<?php echo htmlspecialchars($lapangan_id); ?>">
        <input type="hidden" name="total_price" value="<?php echo htmlspecialchars($total_price); ?>">
        <?php 
        $arr_jam = explode(",", $jam_pilihan);
        foreach($arr_jam as $j) {
            echo '<input type="hidden" name="jam_pilihan[]" value="'.htmlspecialchars($j).'">';
        }
        ?>

        <label>Metode Pembayaran:</label>
        <select name="payment_method" id="payment_method" required>
            <option value="Format Cash">Cash / COD</option>
            <option value="Transfer Bank">Transfer Bank</option>
        </select>
        <br><br>

        <label>Bukti Transfer (Jika memilih Transfer):</label>
        <input type="file" name="bukti_transfer">
        <br><br>

        <button type="submit">Konfirmasi & Simpan Booking</button>
    </form>
</body>
</html>
