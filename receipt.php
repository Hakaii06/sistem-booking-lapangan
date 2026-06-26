<?php
include 'koneksi.php';

// Validasi parameter ID booking
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Akses Ilegal: ID Booking tidak ditemukan.");
}

$id_booking = mysqli_real_escape_string($koneksi, $_GET['id']);

// Ambil detail data booking, join dengan nama lapangan
$query = "SELECT b.*, f.field_name, f.price_per_hour, GROUP_CONCAT(d.start_hour ORDER BY d.start_hour ASC) as jam_main 
          FROM bookings b
          JOIN booking_details d ON b.id = d.booking_id
          JOIN fields f ON d.field_id = f.id
          WHERE b.id = '$id_booking' AND b.status = 'Paid'
          GROUP BY b.id";

$result = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($result);

// Jika data tidak ditemukan atau belum berstatus Paid
if (!$data) {
    die("Struk tidak dapat ditampilkan. Pastikan pesanan Anda telah dikonfirmasi Lunas oleh Admin.");
}

$array_jam = explode(',', $data['jam_main']);
$total_jam = count($array_jam);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Pembayaran - Booking Lapangan #<?= $data['id']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Courier New', Courier, monospace; }
        .receipt-card { max-width: 500px; margin: 40px auto; background: #fff; padding: 30px; border: 1px dashed #ced4da; box-shadow: 0px 0px 10px rgba(0,0,0,0.05); }
        .line { border-top: 1px dashed #000; margin: 15px 0; }
        @media print {
            body { background-color: #fff; }
            .receipt-card { border: none; box-shadow: none; margin: 0; padding: 0; max-width: 100%; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="receipt-card">
        <div class="text-center">
            <h4 class="fw-bold mb-1">STRUK DIGITAL</h4>
            <h5 class="text-uppercase fw-bold"><?= $data['field_name']; ?></h5>
            <small class="text-muted">Jl. Pemuda No. 123, Komplek Olahraga Lokal</small>
            <br>
            <small>No. Nota: #BKG-00<?= $data['id']; ?></small>
        </div>

        <div class="line"></div>

        <table class="table table-sm table-borderless small">
            <tr>
                <td>Nama Pelanggan</td>
                <td>: <?= htmlspecialchars($data['user_name']); ?></td>
            </tr>
            <tr>
                <td>WhatsApp</td>
                <td>: <?= htmlspecialchars($data['user_phone']); ?></td>
            </tr>
            <tr>
                <td>Tanggal Main</td>
                <td>: <strong><?= date('d M Y', strtotime($data['booking_date'])); ?></strong></td>
            </tr>
            <tr>
                <td valign="top">Jadwal Jam</td>
                <td>: 
                    <?php 
                    foreach($array_jam as $jm) {
                        echo "[" . sprintf('%02d:00', $jm) . "] ";
                    }
                    ?>
                </td>
            </tr>
        </table>

        <div class="line"></div>

        <table class="table table-sm table-borderless small mb-0">
            <tr>
                <td>Biaya Sewa Lapangan</td>
                <td class="text-end">Rp. <?= number_format($data['price_per_hour']); ?> /Jam</td>
            </tr>
            <tr>
                <td>Durasi Main</td>
                <td class="text-end"><?= $total_jam; ?> Jam</td>
            </tr>
            <tr>
                <td>Metode Bayar</td>
                <td class="text-end"><em><?= $data['payment_method']; ?></em></td>
            </tr>
            <tr class="fw-bold">
                <td><span class="fs-5">TOTAL LUNAS</span></td>
                <td class="text-end"><span class="fs-5">Rp. <?= number_format($data['total_price']); ?></span></td>
            </tr>
        </table>

        <div class="line"></div>

        <div class="text-center small">
            <p class="mb-1 fw-bold text-success">✓ STATUS: PEMBAYARAN BERHASIL</p>
            <p class="text-muted mb-4">Terima kasih telah berolahraga di tempat kami!</p>
            
            <div class="no-print d-grid gap-2">
                <button onclick="window.print()" class="btn btn-primary btn-sm">🖨️ Cetak / Simpan PDF</button>
                <a href="index.php" class="btn btn-outline-secondary btn-sm">Kembali ke Beranda</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>