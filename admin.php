<?php 
include 'koneksi.php'; 

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    if ($_GET['action'] == 'bayar') {
        mysqli_query($koneksi, "UPDATE bookings SET status = 'Paid' WHERE id = '$id'");
    } elseif ($_GET['action'] == 'batal') {
        mysqli_query($koneksi, "UPDATE bookings SET status = 'Cancelled' WHERE id = '$id'");
    }
    header("Location: admin.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Panel Admin - Kelola Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; font-family: 'Poppins', sans-serif; }
        .navbar-admin { background-color: #1e293b; color: white; padding: 15px 0; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .card-dashboard { border: none; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.02); background: white; }
        .table thead { background-color: #0f172a; color: white; }
        .table thead th { padding: 15px; font-weight: 500; border: none; }
        .table tbody td { padding: 15px; }
        .badge { border-radius: 6px; padding: 6px 10px; font-weight: 500; }
        .btn { border-radius: 8px; font-weight: 500; }
    </style>
</head>
<body>

<div class="navbar-admin mb-5">
    <div class="container d-flex justify-content-between align-items-center">
        <h4 class="m-0 fw-bold">⚙️ Dashboard Manajemen Lapangan</h4>
        <a href="index.php" class="btn btn-outline-light btn-sm px-3">Lihat Tampilan Utama</a>
    </div>
</div>

<div class="container">
    <div class="card card-dashboard p-4">
        <h5 class="fw-bold mb-4 text-dark">Daftar Reservasi Pelanggan</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pelanggan</th>
                        <th>WhatsApp</th>
                        <th>Tanggal</th>
                        <th>Sesi Jam</th>
                        <th>Total Tarif</th>
                        <th>Metode</th>
                        <th>Bukti Transaksi</th>
                        <th>Status</th>
                        <th class="text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT b.*, GROUP_CONCAT(d.start_hour ORDER BY d.start_hour ASC) as jam_main 
                              FROM bookings b
                              LEFT JOIN booking_details d ON b.id = d.booking_id
                              GROUP BY b.id ORDER BY b.id DESC";
                    $result = mysqli_query($koneksi, $query);
                    
                    if(mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $badge = ($row['status'] == 'Paid') ? 'bg-success' : (($row['status'] == 'Pending') ? 'bg-warning text-dark' : 'bg-danger');
                            $method_badge = (strpos($row['payment_method'], 'Transfer') !== false) ? 'bg-info text-dark' : 'bg-light text-dark border';
                            ?>
                            <tr>
                                <td>#<?= $row['id']; ?></td>
                                <td class="fw-semibold text-dark"><?= htmlspecialchars($row['user_name']); ?></td>
                                <td><small class="text-muted"><?= htmlspecialchars($row['user_phone']); ?></small></td>
                                <td><?= $row['booking_date']; ?></td>
                                <td>
                                    <?php 
                                    if (!empty($row['jam_main'])) {
                                        $array_jam = explode(',', $row['jam_main']);
                                        foreach($array_jam as $j) {
                                            echo "<span class='badge bg-secondary me-1' style='font-size:0.75rem;'>".sprintf('%02d:00', $j)."</span>";
                                        }
                                    } else { echo "-"; }
                                    ?>
                                </td>
                                <td class="fw-bold text-success">Rp. <?= number_format($row['total_price']); ?></td>
                                <td><span class="badge <?= $method_badge; ?>" style="font-size: 0.75rem;"><?= $row['payment_method']; ?></span></td>
                                
                                <td>
                                    <?php if (strpos($row['payment_method'], 'Transfer') !== false) : ?>
                                        <?php if (!empty($row['bukti_transfer'])) : ?>
                                            <button type="button" class="btn btn-sm btn-primary px-3" 
                                                    onclick="lihatBukti('uploads/<?= $row['bukti_transfer']; ?>', '<?= htmlspecialchars($row['user_name']); ?>')">
                                                🔍 Lihat Gambar
                                            </button>
                                        <?php else : ?>
                                            <span class="text-danger small fw-medium">Belum Upload</span>
                                        <?php endif; ?>
                                    <?php else : ?>
                                        <span class="text-muted small">N/A (Cash)</span>
                                    <?php endif; ?>
                                </td>

                                <td><span class="badge <?= $badge; ?>"><?= $row['status']; ?></span></td>
                                <td class="text-center">
                                    <?php if($row['status'] == 'Pending') : ?>
                                        <a href="admin.php?action=bayar&id=<?= $row['id']; ?>" class="btn btn-sm btn-success px-2 me-1">Setujui</a>
                                        <a href="admin.php?action=batal&id=<?= $row['id']; ?>" class="btn btn-sm btn-outline-danger px-2" onclick="return confirm('Batalkan booking ini?')">Tolak</a>
                                    <?php else: ?>
                                        <span class="text-muted small fw-medium">- Selesai -</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php 
                        } 
                    } else {
                        echo "<tr><td colspan='10' class='text-center text-muted p-4'>Belum ada pemesanan masuk.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalBukti" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fs-6">Verifikasi Bukti Transfer - <span id="namaPelangganModal"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center bg-light p-4">
                <img src="" id="gambarBuktiModal" class="img-fluid rounded-3 shadow-sm" style="max-height: 480px;" alt="Pratinjau Gambar">
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Tutup Pratinjau</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function lihatBukti(urlGambar, namaPelanggan) {
    document.getElementById('namaPelangganModal').innerText = namaPelanggan;
    document.getElementById('gambarBuktiModal').src = urlGambar;
    var myModal = new bootstrap.Modal(document.getElementById('modalBukti'));
    myModal.show();
}
</script>
</body>
</html>