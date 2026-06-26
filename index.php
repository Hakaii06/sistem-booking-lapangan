<?php
// 1. Hubungkan ke file koneksi terlebih dahulu
include 'koneksi.php';

// 2. Ambil data booking dari database jika koneksi aktif
$jam_booked = [];
if ($database_error_mode === false) {
    $query = mysqli_query($koneksi, "SELECT jam_main FROM tbl_booking");
    if ($query) {
        while ($row = mysqli_fetch_assoc($query)) {
            $jam_booked[] = $row['jam_main'];
        }
    }
}
// Jika database error, variabel $jam_booked otomatis tetap berupa array kosong [] agar tidak memicu error in_array()
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Booking Lapangan</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .hero-banner { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 40px 20px; border-radius: 0 0 20px 20px; }
        .card-lapangan { border: none; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); transition: transform 0.2s; }
        .card-lapangan:hover { transform: translateY(-5px); }
        .btn-jam { font-weight: 600; font-size: 0.9rem; transition: all 0.2s; }
    </style>
</head>
<body>

    <!-- BANNER NOTIFIKASI OTOMATIS JIKA DI-DEPLOY DI VERCEL -->
    <?php if ($database_error_mode === true) : ?>
        <div class="alert alert-warning text-center m-0 border-0 rounded-0" style="font-size: 0.85rem; background-color: #fffbeb; color: #b45309;">
            ⚠️ <strong>Mode Pratinjau Antarmuka (UI/UX) Aktif:</strong> Koneksi database dinonaktifkan di server cloud. Fitur transaksi penuh dapat diuji pada lingkungan <code>localhost</code> di dalam video demo tugas.
        </div>
    <?php endif; ?>

    <div class="container max-width-md p-0">
        <!-- Hero Section -->
        <div class="hero-banner text-center mb-4">
            <h1 class="fw-bold h3">Futsal & Badminton Center</h1>
            <p class="lead m-0" style="font-size: 0.95rem; opacity: 0.9;">Pilih Jam Terbaik dan Amankan Slot Bermainmu Sekarang!</p>
        </div>

        <div class="px-3">
            <!-- Card Utama Konten Lapangan -->
            <div class="card card-lapangan p-4 mb-4">
                <div class="d-flex align-items-center mb-3">
                    <span class="badge bg-success me-2">Tersedia</span>
                    <h5 class="m-0 fw-bold text-dark">Lapangan Utama (Vinyl)</h5>
                </div>
                <p class="text-muted small mb-4">Harga: <strong class="text-success">Rp 120.000 / Jam</strong></p>
                
                <h6 class="fw-bold mb-3 text-secondary" style="font-size: 0.85rem; letter-spacing: 0.5px; text-transform: uppercase;">Pilih Jam Main:</h6>
                
                <!-- Grid Pilihan Jam Main -->
                <div class="row g-2">
                    <?php 
                    // Looping Jam Operasional Lapangan (Jam 08:00 s/d 15:00)
                    for ($jam = 8; $jam <= 15; $jam++) {
                        $format_jam = str_pad($jam, 2, "0", STR_PAD_LEFT) . ".00";
                        
                        // Cek apakah jam sudah dibooking orang lain
                        $is_booked = in_array($jam, $jam_booked);
                        
                        if ($is_booked) {
                            // Tampilan tombol jika jam sudah penuh/booked
                            echo "<div class='col-6 col-sm-3'>
                                    <button class='btn btn-light text-muted w-100 btn-jam py-2 border' disabled style='text-decoration: line-through;'>$format_jam</button>
                                  </div>";
                        } else {
                            // Tampilan tombol jika jam masih kosong/tersedia
                            // Klik jam main akan langsung mengarahkan pengguna ke form booking dengan membawa data parameter jam
                            echo "<div class='col-6 col-sm-3'>
                                    <a href='booking.php?jam=$jam' class='btn btn-outline-success w-100 btn-jam py-2'>$format_jam</a>
                                  </div>";
                        }
                    }
                    ?>
                </div>
            </div>
            
            <footer class="text-center text-muted small py-3 mt-4">
                <p>&copy; 2026 Sistem Informasi - Tugas Besar Pemrograman Web</p>
            </footer>
        </div>
    </div>

    <!-- Bootstrap JS Bundle CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
