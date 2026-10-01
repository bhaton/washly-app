<?php 
require_once "includes/koneksi.php";

$areas = [];
$res = $conn->query("SELECT * FROM service_areas WHERE status = 'Aktif'");
while($row = $res->fetch_assoc()) {
    $areas[] = $row;
}

include "includes/header.php"; 
?>
<section id="view-area" class="view-php">
    <div class="section-header text-center">
        <h2>Area Layanan <span class="gradient-text">Gratis Jemput</span></h2>
        <p>Maksimal 10 KM dari outlet kami untuk menjaga kualitas dan kecepatan</p>
    </div>
    
    <div class="area-check-container glass-panel mx-auto" style="max-width: 600px;">
        <h3 class="mb-3 text-center">Daftar Area Terjangkau:</h3>
        <ul style="list-style: none; padding: 0; text-align: center;">
            <?php foreach($areas as $a): ?>
                <li style="margin-bottom: 0.5rem; color: #1e293b; font-weight: 500;">
                    <i class="fa-solid fa-location-dot text-primary"></i> <?= $a['area_name'] ?> 
                    <small class="text-light">(± <?= $a['distance_km'] ?> km)</small>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="alert-info text-center mt-4">
            Berada di luar area ini? Jangan khawatir, Anda tetap bisa datang langsung (walk-in) ke outlet kami!
        </div>
    </div>
</section>
<?php include "includes/footer.php"; ?>