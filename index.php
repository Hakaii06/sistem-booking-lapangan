<?php 
include 'koneksi.php'; 

// Set tanggal hari ini sebagai default jika user belum memilih tanggal
$tanggal = isset($_POST['tanggal']) ? $_POST['tanggal'] : date('Y-m-d');
$lapangan_id = isset($_POST['lapangan_id']) ? $_POST['lapangan_id'] : 1;

$jam_booked = [];

// Mengambil data jam yang sudah dipesan dari database cloud
if (isset($_POST['cek_jadwal'])) {
    // Sesuaikan nama tabel dan kolom di bawah jika nama di database lokalmu berbeda
    // Contoh asumsi: tabel 'tbl_booking' dengan kolom 'tanggal_main', 'jam_main', dan 'id_lapangan'
    $query = "SELECT jam_main FROM tbl_booking 
              WHERE id_lapangan = '$lapangan_id' 
              AND tanggal_main = '$tanggal'";
              
    $result = mysqli_query($koneksi, $query);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $jam_booked[] = $row['jam_main'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Booking Lapangan - Cloud Live</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f3f4f6; font-family: 'Poppins', sans-serif; color: #2d3748; }
        .hero-banner { background: linear-gradient(135deg, #111827 0%, #065f46 100%); color: white; padding: 40px 20px; border-radius: 0 0 25px 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .card { border: none; border-radius: 16px; box-shadow: 0 8px 24px rgba(0,0,0,0.04); margin-bottom: 25px; }
        .card-title { font-weight: 600; color: #065f46; border-left: 4px solid #059669; padding-left: 10px; }
        .form-control, .form-select { border-radius: 10px; padding: 10px 15px; border: 1px solid #cbd5e1; }
        .form-control:focus, .form-select:focus { border-color: #059669; box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.2); }
        .btn-primary { background-color: #065f46; border: none; border-radius: 10px; padding: 10px 20px; font-weight: 600; }
        .btn-primary:hover { background-color: #059669; }
        .btn-success { background-color: #059669; border: none; border-radius: 10px; padding: 12px; font-weight: 600; }
        .btn-success:hover { background-color: #047857; }
        .jam-card { cursor: pointer; transition: all 0.2s ease; border: 2px solid #e2e8f0 !important; background: #fff; border-radius: 12px; }
        .jam-card:not(.disabled):hover { background-color: #ecfdf5 !important; border-color: #059669 !important; transform: translateY(-2px); }
        .jam-card.selected { background-color: #059669 !important; color: #fff !important; border-color: #047857 !important; }
        .jam-checkbox { display: none; }
        .jam-card.disabled { cursor: not-allowed; background-color: #fef2f2 !important; border-color: #fca5a5 !important; color: #ef4444 !important; opacity: 0.6; text-decoration: line-through; }
    </style>
</head>
<body>

<div class="hero-banner text-center">
    <h1 class="fw-bold m-0">Sport Arena Center</h1>
    <p class="lead opacity-75 m-0 small mt-1">Sistem Informasi Booking Lapangan Realtime Berbasis Cloud</p>
</div>

<div class="container mt-4 px-3">
    <div class="card">
        <div class="card-body p-4">
            <h5 class="card-title mb-4">1. Tentukan Tanggal & Lapangan</h5>
            <form method="POST" class="row g-3">
                <div class="col-md-5">
                    <label class="form-label fw-medium">Pilih Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" value="<?= $tanggal; ?>" required>
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-medium">Pilih Lapangan</label>
                    <select name="lapangan_id" class="form-select">
                        <option value="1" <?= $lapangan_id == 1 ? 'selected' : ''; ?>>Lapangan Futsal (Vinyl)</option>
                        <option value="2" <?= $lapangan_id == 2 ? 'selected' : ''; ?>>Lapangan Badminton 1</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" name="cek_jadwal" class="btn btn-primary w-100 py-2">Cek Ketersediaan</button>
                </div>
            </form>
        </div>
    </div>

    <?php if (isset($_POST['cek_jadwal'])) : ?>
    <div class="card">
        <div class="card-body p-4">
            <h5 class="card-title mb-4">2. Lengkapi Data Pemesan & Pilih Jam</h5>
            <form action="proses_booking.php" method="POST">
                <input type="hidden" name="tanggal" value="<?= $tanggal; ?>">
                <input type="hidden" name="lapangan_id" value="<?= $lapangan_id; ?>">
                
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label fw-medium">Nama Lengkap</label>
                        <input type="text" name="nama_pelanggan" class="form-control" placeholder="Masukkan nama pemesan" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Nomor WhatsApp</label>
                        <input type="text" name="no_whatsapp" class="form-control" placeholder="Contoh: 08123456xxx" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold mb-3">Pilih Jam Bermain (Bisa pilih lebih dari 1 jam):</label>
                    <div class="row g-2">
                        <?php 
                        // Loop Jam Operasional (08:00 sampai 15:00)
                        for ($jam = 8; $jam <= 15; $jam++) {
                            $is_booked = in_array($jam, $jam_booked);
                            $card_class = $is_booked ? 'disabled' : 'text-success';
                            $status_text = $is_booked ? 'Booked' : 'Tersedia';
                            ?>
                            <div class="col-md-3 col-6 mb-2">
                                <div class="card jam-card text-center p-3 <?= $card_class; ?>" 
                                     <?= !$is_booked ? "onclick='pilihJam(this)'" : ""; ?>>
                                    <span class="fw-bold d-block">Jam <?= sprintf('%02d:00', $jam); ?></span>
                                    <small class="opacity-75" style="font-size: 0.73rem;"><?= $status_text; ?></small>
                                    <input class="jam-checkbox" type="checkbox" name="jam_pilihan[]" value="<?= $jam; ?>" <?= $is_booked ? 'disabled' : ''; ?>>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <button type="submit" name="simpan_booking" class="btn btn-success w-100 py-3 shadow-sm">Proses Booking Sekarang ➔</button>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function pilihJam(element) {
    var checkbox = element.querySelector('.jam-checkbox');
    checkbox.checked = !checkbox.checked;
    if(checkbox.checked) {
        element.classList.add('selected');
        element.classList.remove('text-success');
    } else {
        element.classList.remove('selected');
        element.classList.add('text-success');
    }
}
</script>
</body>
</html>
