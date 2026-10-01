<?php 
require_once __DIR__ . '/koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WASHLY - Premium Laundry Service</title>
    <meta name="description" content="Layanan laundry modern, cepat, dan higienis. Antar jemput gratis untuk area tertentu.">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar glass-nav">
        <div class="logo" onclick="window.location.href='index.php'">
            <i class="fa-solid fa-water"></i> WASHLY
        </div>
        <ul class="nav-links" id="nav-links">
            <li><a href="index.php" class="nav-item">Home</a></li>
            <li><a href="services.php" class="nav-item">Layanan</a></li>
            <li><a href="area.php" class="nav-item">Area Layanan</a></li>
            <li><a href="contact.php" class="nav-item">Kontak</a></li>
        </ul>
        <div class="nav-actions" id="nav-actions">
            <?php if(isset($_SESSION['user_id'])): ?>
                <?php if($_SESSION['role'] == 'admin'): ?>
                    <button class="btn btn-primary btn-sm" onclick="window.location.href='admin_dashboard.php'">Dashboard Admin</button>
                <?php elseif($_SESSION['role'] == 'driver'): ?>
                    <button class="btn btn-primary btn-sm" onclick="window.location.href='driver_dashboard.php'">Dashboard Kurir</button>
                <?php else: ?>
                    <button class="btn btn-primary btn-sm" onclick="window.location.href='customer_dashboard.php'">Dashboard</button>
                <?php endif; ?>
                <button class="btn btn-outline btn-sm" onclick="window.location.href='logout.php'">Logout</button>
            <?php else: ?>
                <button class="btn btn-outline btn-sm" onclick="window.location.href='login.php'">Masuk</button>
                <button class="btn btn-primary btn-sm" onclick="window.location.href='register.php'">Daftar</button>
            <?php endif; ?>
        </div>
        <div class="mobile-menu-btn" onclick="app.toggleMobileMenu()">
            <i class="fa-solid fa-bars"></i>
        </div>
    </nav>

    <!-- Main Content Area -->
    <main id="app-content">