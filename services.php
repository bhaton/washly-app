<?php 
require_once "includes/koneksi.php";

$services = [];
$res = $conn->query("SELECT * FROM services WHERE status = 'Aktif'");
while($row = $res->fetch_assoc()) {
    $services[] = $row;
}

include "includes/header.php"; 
?>
<section id="view-services" class="view-php">
    <div class="section-header text-center">
        <h2>Layanan <span class="gradient-text">Premium</span> Kami</h2>
        <p>Pilih layanan yang sesuai dengan kebutuhan pakaian Anda</p>
    </div>
    
    <div class="services-grid mt-4">
        <?php foreach($services as $s): ?>
        <div class="service-card glass-panel hover-lift">
            <div class="service-icon"><i class="fa-solid fa-shirt"></i></div>
            <h3><?= $s['name'] ?></h3>
            <p class="service-desc mt-2"><?= $s['description'] ?></p>
            <div class="service-price">
                Rp <?= number_format($s['price_regular'], 0, ',', '.') ?> 
                <span style="font-size:1rem; font-weight:normal; color:var(--text-light)">/ <?= $s['unit'] ?></span>
            </div>
            <div class="service-meta mt-3 d-flex justify-between" style="font-size: 0.9rem;">
                <span class="text-light"><i class="fa-solid fa-clock"></i> <?= $s['est_regular'] ?></span>
                <?php if($s['price_express']): ?>
                    <span class="text-warning"><i class="fa-solid fa-bolt"></i> Express Ada</span>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <div class="text-center mt-5">
        <?php if(isset($_SESSION['user_id'])): ?>
            <button class="btn btn-glow" onclick="window.location.href='create_order.php'">Pesan Sekarang</button>
        <?php else: ?>
            <button class="btn btn-glow" onclick="window.location.href='login.php'">Login untuk Pesan</button>
        <?php endif; ?>
    </div>
</section>
<?php include "includes/footer.php"; ?>