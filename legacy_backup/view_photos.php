<?php 
require_once "includes/koneksi.php";

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if($order_id === 0) {
    echo "ID Order tidak valid.";
    exit;
}

// Cek apakah user berhak melihat (Admin atau Pemilik Order)
$stmt = $conn->prepare("SELECT customer_id, order_number FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$res = $stmt->get_result();
if($res->num_rows === 0) {
    echo "Order tidak ditemukan.";
    exit;
}
$order = $res->fetch_assoc();

if($_SESSION['role'] != 'admin' && $_SESSION['user_id'] != $order['customer_id']) {
    echo "Akses ditolak.";
    exit;
}

// Ambil foto dari order_photos
$customer_photos = [];
$admin_photos = [];
$photo_stmt = $conn->prepare("SELECT * FROM order_photos WHERE order_id = ?");
$photo_stmt->bind_param("i", $order_id);
$photo_stmt->execute();
$photo_res = $photo_stmt->get_result();
while($row = $photo_res->fetch_assoc()) {
    if($row['stage'] === 'Sebelum Pickup') {
        $customer_photos[] = $row;
    } else {
        $admin_photos[] = $row;
    }
}

include "includes/header.php"; 
?>
<section class="view-php" style="padding: 2rem 5%;">
    <div class="glass-panel" style="max-width: 800px; margin: 0 auto; padding: 2rem;">
        <div class="d-flex justify-between align-center mb-4">
            <h3>Dokumentasi Order: <span class="text-primary"><?= $order['order_number'] ?></span></h3>
            <button class="btn btn-outline" onclick="history.back()">Kembali</button>
        </div>
        
        <div class="photo-section mb-5">
            <h4 class="mb-3" style="border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">1. Foto Customer (Saat Order)</h4>
            <?php if(count($customer_photos) > 0): ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1rem;">
                    <?php foreach($customer_photos as $p): ?>
                        <div style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px; text-align: center;">
                            <img src="<?= htmlspecialchars($p['photo_path']) ?>" alt="Customer Photo" style="max-width: 100%; max-height: 400px; border-radius: 8px; object-fit: contain;">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-light"><i>Customer tidak melampirkan foto.</i></p>
            <?php endif; ?>
        </div>
        
        <div class="photo-section">
            <h4 class="mb-3" style="border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">2. Foto Admin (Pengecekan/Timbangan)</h4>
            <?php if(count($admin_photos) > 0): ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1rem;">
                    <?php foreach($admin_photos as $p): ?>
                        <div style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px; text-align: center;">
                            <img src="<?= htmlspecialchars($p['photo_path']) ?>" alt="Admin Photo" style="max-width: 100%; max-height: 250px; object-fit: cover; border-radius: 4px; margin-bottom: 10px;">
                            <br>
                            <span class="badge" style="background: #e0f2fe; color: #0284c7;">Tahap: <?= htmlspecialchars($p['stage']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-light"><i>Admin belum melampirkan foto dokumentasi.</i></p>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php include "includes/footer.php"; ?>
