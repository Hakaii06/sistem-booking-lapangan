<?php
include 'koneksi.php';

// Ambil data bookings terbaru
$query = mysqli_query($koneksi, "SELECT * FROM bookings ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Halaman Admin - Kelola Booking</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
    </style>
</head>
<body>
    <h2>Daftar Pesanan Lapangan (Admin)</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Pelanggan</th>
                <th>No. HP</th>
                <th>Tanggal</th>
                <th>Total Harga</th>
                <th>Metode</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($query)) : ?>
            <tr>
                <td>#<?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                <td><?php echo htmlspecialchars($row['user_phone']); ?></td>
                <td><?php echo htmlspecialchars($row['booking_date']); ?></td>
                <td>Rp <?php echo number_format($row['total_price'], 0, ',', '.'); ?></td>
                <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                <td><strong><?php echo htmlspecialchars($row['status']); ?></strong></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
