<?php 
include 'koneksi.php'; 

$tanggal = isset($_POST['tanggal']) ? $_POST['tanggal'] : date('Y-m-d');
$lapangan_id = isset($_POST['lapangan_id']) ? $_POST['lapangan_id'] : 1;

$jam_booked = [];
if (isset($_POST['cek_jadwal'])) {
    $query = "SELECT start_hour FROM booking_details 
              JOIN bookings ON booking_details.booking_id = bookings.id
              WHERE booking_details.field_id = '$lapangan_id' 
              AND bookings.booking_date = '$tanggal'
              AND bookings.status IN ('Pending', 'Paid')";
    $result = mysqli_query($koneksi, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $jam_booked[] = $row['start_hour'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sistem Booking Lapangan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f3f4f6;
            font-family: 'Poppins', sans-serif;
            color: #2d3748;
        }
        .hero-banner {
            background: linear-gradient(135deg, #1e3a1e 0%, #2e7d32 100%);
            color: white;
            padding: 40px 20px;
            border-radius: 0 0 25px 25px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.04);
            margin-bottom: 25px;
        }
        .card-title {
            font-weight: 600;
            color: #1e3a1e;
            border-left: 4px solid #2e7d32;
            padding-left: 10px;
        }
        .form-control, .form-select {
            border-radius: 10px;
            padding: 10px 15px;
            border: 1px solid #cbd5e1;
        }
        .form-control:focus, .form-select:focus {
            border-color: #2e7d32;
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.2);
        }
        .btn-primary {
            background-color: #1e3a1e;
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #2e7d32;
        }
        .btn-success {
            background-color: #2e7d32;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
        }
        .btn-success:hover {
            background-color: #1b5e20;
        }
        /* Style Card Jam Interaktif */
        .jam-card {
            cursor: pointer;
            transition: all 0.25s ease;
            user-select: none;
            border-radius: 12px;
            border: 2px solid #e2e8f0 !important;
            background: #fff;
        }
        .jam-card:not(.disabled):hover {
            background-color: #f0fdf4 !important;
            border-color: #2e7d32 !important;
            transform: translateY(-3px);
        }
        .jam-card.selected {
            background-color: #2e7d32 !important;
            color: #fff !important;
            border-color: #1b5e20 !important;
            box-shadow: 0 6px 12px rgba(46, 125, 50, 0.2);
        }
        .jam-checkbox { display: none; }
        .jam-card.disabled {
            cursor: not-allowed;
            background-color: #fef2f2 !important;
            border-color: #fca5a5 !important;
            color: #ef4444 !important;
            opacity: 0.7;
        }
        .badge {
            border-radius: 8px;
            padding: 6px 10px;
        }
    </style>
</head>
<body>

<div class="hero-banner text-center">
    <h1 class="fw-bold m-0">Arena Sport Center</h1>
    <p class="lead opacity-75 m-0 small mt-1">Booking lapangan andalanmu dengan cepat, mudah, dan aman</p>
</div>

<div class="container">
    <div class="card">
        <div class="card-body p-4">
            <h5 class="card-title mb-4">1. Cek Ketersediaan Jadwal</h5>
            <form method="POST" class="row g-3">
                <div class="col-md-5">
                    <label class="form-label fw-medium">Pilih Tanggal Main</label>
                    <input type="date" name="tanggal" class="form-control" value="<?= $tanggal; ?>" required>
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-medium">Pilih Lapangan</label>
                    <select name="lapangan_id" class="form-select">
                        <?php 
                        $fields = mysqli_query($koneksi, "SELECT * FROM fields");
                        while($f = mysqli_fetch_assoc($fields)) {
                            $selected = ($f['id'] == $lapangan_id) ? 'selected' : '';
                            echo "<option value='".$f['id']."' $selected>".$f['field_name']." (Rp. ".number_format($f['price_per_hour'])." /jam)</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" name="cek_jadwal" class="btn btn-primary w-100 py-2">Cek Jadwal</button>
                </div>
            </form>
        </div>
    </div>

    <?php if (isset($_POST['cek_jadwal'])) : ?>
    <div class="card">
        <div class="card-body p-4">
            <h5 class="card-title mb-4">2. Isikan Data Booking</h5>
            <form action="proses_pembayaran.php" method="POST">
                <input type="hidden" name="tanggal" value="<?= $tanggal; ?>">
                <input type="hidden" name="lapangan_id" value="<?= $lapangan_id; ?>">
                
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label fw-medium">Nama Pemesan</label>
                        <input type="text" name="user_name" class="form-control" placeholder="Masukkan nama lengkap" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">No. WhatsApp</label>
                        <input type="text" name="user_phone" class="form-control" placeholder="Contoh: 08123456789" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold mb-3">Pilih Jam Main (Klik pada kotak jam di bawah):</label>
                    <div class="row">
                        <?php 
                        for ($jam = 8; $jam <= 15; $jam++) {
                            $is_booked = in_array($jam, $jam_booked);
                            $card_class = $is_booked ? 'disabled' : 'text-success';
                            $status_text = $is_booked ? 'Sudah Dipesan' : 'Tersedia';
                            ?>
                            <div class="col-md-3 col-6 mb-3">
                                <div class="card jam-card text-center p-3 <?= $card_class; ?>" 
                                     <?= !$is_booked ? "onclick='pilihJam(this)'" : ""; ?>>
                                    <span class="fw-bold d-block">Jam <?= sprintf('%02d:00', $jam); ?></span>
                                    <small class="opacity-75" style="font-size: 0.75rem;"><?= $status_text; ?></small>
                                    <input class="jam-checkbox" type="checkbox" name="jam_pilihan[]" value="<?= $jam; ?>" <?= $is_booked ? 'disabled' : ''; ?>>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <button type="submit" name="lanjut_bayar" class="btn btn-success w-100 py-3 shadow-sm">Booking Sekarang (Lanjut Pembayaran) ➔</button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <div class="card border-0 bg-white">
        <div class="card-body p-4">
            <h5 class="card-title mb-4 text-dark" style="border-left-color: #0dcaf0;">3. Cek History & Cetak Struk Digital</h5>
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-10">
                    <input type="text" name="cari_wa" class="form-control" placeholder="Masukkan nomor WhatsApp Anda untuk melacak history..." value="<?= isset($_GET['cari_wa']) ? htmlspecialchars($_GET['cari_wa']) : ''; ?>" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-info w-100 py-2 text-white fw-bold" style="border-radius: 10px;">Cari History</button>
                </div>
            </form>

            <?php 
            if (isset($_GET['cari_wa']) && !empty($_GET['cari_wa'])) {
                $cari_wa = mysqli_real_escape_string($koneksi, $_GET['cari_wa']);
                $query_history = "SELECT b.*, GROUP_CONCAT(d.start_hour ORDER BY d.start_hour ASC) as jam_main 
                                  FROM bookings b
                                  LEFT JOIN booking_details d ON b.id = d.booking_id
                                  WHERE b.user_phone = '$cari_wa'
                                  GROUP BY b.id ORDER BY b.id DESC";
                $result_history = mysqli_query($koneksi, $query_history);

                if (mysqli_num_rows($result_history) > 0) {
                    echo '<div class="table-responsive"><table class="table table-hover align-middle">';
                    echo '<thead class="table-light"><tr><th>Tanggal</th><th>Jam Main</th><th>Total Bayar</th><th>Metode</th><th>Status</th><th class="text-center">Aksi</th></tr></thead><tbody>';
                    while ($h = mysqli_fetch_assoc($result_history)) {
                        $st_badge = ($h['status'] == 'Paid') ? 'bg-success' : (($h['status'] == 'Pending') ? 'bg-warning text-dark' : 'bg-danger');
                        echo '<tr>';
                        echo '<td><span class="fw-medium">'.$h['booking_date'].'</span></td>';
                        echo '<td>';
                        $jams = explode(',', $h['jam_main']);
                        foreach($jams as $jm) {
                            echo "<span class='badge bg-secondary me-1'>".sprintf('%02d:00', $jm)."</span>";
                        }
                        echo '</td>';
                        echo '<td><span class="fw-bold text-success">Rp. '.number_format($h['total_price']).'</span></td>';
                        echo '<td><small class="text-muted">'.$h['payment_method'].'</small></td>';
                        echo '<td><span class="badge '.$st_badge.'">'.$h['status'].'</span></td>';
                        echo '<td class="text-center">';
                        if ($h['status'] == 'Paid') {
                            echo '<a href="receipt.php?id='.$h['id'].'" target="_blank" class="btn btn-sm btn-dark px-3" style="border-radius:8px;">📄 Struk</a>';
                        } else {
                            echo '<span class="badge bg-light text-muted border">Menunggu Lunas</span>';
                        }
                        echo '</td>';
                        echo '</tr>';
                    }
                    echo '</tbody></table></div>';
                } else {
                    echo '<div class="alert alert-warning border-0" style="border-radius:12px;">Tidak ditemukan data booking aktif untuk nomor tersebut.</div>';
                }
            }
            ?>
        </div>
    </div>
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