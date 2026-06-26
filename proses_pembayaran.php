<?php
include 'koneksi.php';

if (!isset($_POST['lanjut_bayar'])) {
    header("Location: index.php");
    exit;
}

$user_name   = $_POST['user_name'];
$user_phone  = $_POST['user_phone'];
$tanggal     = $_POST['tanggal'];
$lapangan_id = $_POST['lapangan_id'];
$jam_pilihan = isset($_POST['jam_pilihan']) ? $_POST['jam_pilihan'] : [];

if (empty($jam_pilihan)) {
    echo "<script>alert('Pilih minimal 1 jam!'); window.history.back();</script>";
    exit;
}

$field_query = mysqli_query($koneksi, "SELECT * FROM fields WHERE id = '$lapangan_id'");
$field = mysqli_fetch_assoc($field_query);

$total_jam   = count($jam_pilihan);
$price_per_hour = $field['price_per_hour'];
$total_price = $total_jam * $price_per_hour;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Konfirmasi Pembayaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f3f4f6; font-family: 'Poppins', sans-serif; }
        .card { border: none; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .card-header { border-radius: 16px 16px 0 0 !important; font-weight: 600; }
        .form-select, .form-control { border-radius: 10px; padding: 12px; }
        .btn { border-radius: 10px; padding: 10px 20px; font-weight: 600; }
    </style>
</head>
<body>
<div class="container my-5" style="max-width: 650px;">
    <h3 class="text-center fw-bold mb-4" style="color: #1e3a1e;">Checkout Pembayaran</h3>

    <div class="card mb-4">
        <div class="card-header bg-dark text-white p-3">
            <h5 class="m-0 fs-6">Detail Ringkasan Pemesanan</h5>
        </div>
        <div class="card-body p-4">
            <div class="row mb-2">
                <div class="col-5 text-muted">Nama Pemesan</div>
                <div class="col-7 fw-bold"><?= htmlspecialchars($user_name); ?></div>
            </div>
            <div class="row mb-2">
                <div class="col-5 text-muted">No. WhatsApp</div>
                <div class="col-7"><?= htmlspecialchars($user_phone); ?></div>
            </div>
            <div class="row mb-2">
                <div class="col-5 text-muted">Fasilitas Lapangan</div>
                <div class="col-7"><?= $field['field_name']; ?></div>
            </div>
            <div class="row mb-2">
                <div class="col-5 text-muted">Tanggal Main</div>
                <div class="col-7 fw-medium text-primary"><?= date('d F Y', strtotime($tanggal)); ?></div>
            </div>
            <div class="row mb-3">
                <div class="col-5 text-muted">Durasi Jam</div>
                <div class="col-7">
                    <?php foreach($jam_pilihan as $j) { echo "<span class='badge bg-secondary me-1'>".sprintf('%02d:00', $j)."</span>"; } ?>
                </div>
            </div>
            <hr>
            <div class="row my-2 small text-muted">
                <div class="col-6">Kalkulasi Tarif</div>
                <div class="col-6 text-end">Rp. <?= number_format($price_per_hour); ?> × <?= $total_jam; ?> Jam</div>
            </div>
            <div class="row align-items-center bg-light p-3 rounded-3 mt-3">
                <div class="col-6 fw-bold text-dark m-0 fs-5">Total Pembayaran</div>
                <div class="col-6 text-end fw-bold text-success m-0 fs-4">Rp. <?= number_format($total_price); ?></div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-success text-white p-3">
            <h5 class="m-0 fs-6">Pilih Opsi Metode Bayar</h5>
        </div>
        <div class="card-body p-4">
            <form action="simpan_booking.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="user_name" value="<?= htmlspecialchars($user_name); ?>">
                <input type="hidden" name="user_phone" value="<?= htmlspecialchars($user_phone); ?>">
                <input type="hidden" name="tanggal" value="<?= $tanggal; ?>">
                <input type="hidden" name="lapangan_id" value="<?= $lapangan_id; ?>">
                <input type="hidden" name="total_price" value="<?= $total_price; ?>">
                <?php foreach($jam_pilihan as $jam) { ?>
                    <input type="hidden" name="jam_pilihan[]" value="<?= $jam; ?>">
                <?php } ?>

                <div class="mb-4">
                    <label class="form-label fw-medium">Pilih Bank / Cash</label>
                    <select name="payment_method" id="payment_method" class="form-select" onchange="toggleUpload()" required>
                        <option value="Cash (Bayar di Tempat)">Cash (Bayar di Tempat)</option>
                        <option value="Transfer Bank BCA">Transfer Bank BCA (Rek: 123-456-789 a/n Admin)</option>
                        <option value="Transfer Bank BNI">Transfer Bank BNI (Rek: 098-765-432 a/n Admin)</option>
                        <option value="Transfer Bank BRI">Transfer Bank BRI (Rek: 444-555-666 a/n Admin)</option>
                        <option value="Transfer Bank BSI">Transfer Bank BSI (Rek: 777-888-999 a/n Admin)</option>
                        <option value="Transfer Bank Mandiri">Transfer Bank Mandiri (Rek: 111-222-333 a/n Admin)</option>
                    </select>
                </div>

                <div id="upload_section" class="mb-4 p-3 border border-warning rounded-3 bg-light" style="display: none;">
                    <label class="form-label fw-bold text-dark">Wajib Upload Gambar Bukti Transaksi Anda</label>
                    <input type="file" name="bukti_transfer" id="bukti_transfer" class="form-control">
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <a href="index.php" class="text-decoration-none text-muted fw-medium">⬅ Kembali</a>
                    <button type="submit" class="btn btn-success px-4 shadow-sm">Selesaikan Transaksi ➔</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleUpload() {
    var method = document.getElementById("payment_method").value;
    var uploadSection = document.getElementById("upload_section");
    var fileInput = document.getElementById("bukti_transfer");

    if(method.includes("Transfer")) {
        uploadSection.style.display = "block";
        fileInput.setAttribute("required", "required");
    } else {
        uploadSection.style.display = "none";
        fileInput.removeAttribute("required");
    }
}
</script>
</body>
</html>