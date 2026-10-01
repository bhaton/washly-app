<?php include "includes/header.php"; ?>
<section id="view-home" class="view-php">
            <header class="hero">
                <div class="hero-content">
                    <div class="badge-premium">Premium Quality</div>
                    <h1>Laundry tanpa ribet,<br><span class="gradient-text">hidup lebih ringan</span></h1>
                    <p>Kami siap menjemput, mencuci, merawat hingga mengantarkan kembali pakaian Anda dengan kualitas terbaik.</p>
                    <div class="hero-btns">
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <button class="btn btn-glow" onclick="window.location.href='create_order.php'">
                                Buat Pesanan <i class="fa-solid fa-arrow-right"></i>
                            </button>
                        <?php else: ?>
                            <button class="btn btn-glow" onclick="window.location.href='login.php'">
                                Pesan Sekarang <i class="fa-solid fa-arrow-right"></i>
                            </button>
                        <?php endif; ?>
                        <button class="btn btn-outline" style="border: 2px solid #4f46e5; color: #4f46e5;" onclick="window.location.href='area.php'">Cek Area Layanan</button>
                    </div>
                </div>
                <div class="hero-image-placeholder">
                    <!-- A beautiful illustration or graphic element can be here -->
                    <div class="floating-bubble bubble-1"></div>
                    <div class="floating-bubble bubble-2"></div>
                    <div class="floating-bubble bubble-3"></div>
                    <div class="glass-card mockup-card">
                        <i class="fa-solid fa-shirt"></i>
                        <h4>Cuci & Setrika</h4>
                        <p>Mulai Rp 10.000 / kg</p>
                    </div>
                </div>
            </header>
            
            <div class="features-section">
                <h2>Mengapa Memilih Kami?</h2>
                <div class="features">
                    <div class="feature-card glass-panel hover-lift">
                        <div class="icon-wrapper"><i class="fa-solid fa-truck-fast"></i></div>
                        <h3>Jemput & Antar</h3>
                        <p>Maksimal 10 KM dari outlet kami. Tidak perlu keluar rumah.</p>
                    </div>
                    <div class="feature-card glass-panel hover-lift">
                        <div class="icon-wrapper"><i class="fa-solid fa-spray-can-sparkles"></i></div>
                        <h3>Proses Higienis</h3>
                        <p>Perawatan pakaian profesional dengan deterjen ramah lingkungan.</p>
                    </div>
                    <div class="feature-card glass-panel hover-lift">
                        <div class="icon-wrapper"><i class="fa-solid fa-mobile-screen"></i></div>
                        <h3>Pantau Real-time</h3>
                        <p>Tracking status laundry Anda dengan mudah melalui sistem kami.</p>
                    </div>
                </div>
            </div>
        </section>
<?php include "includes/footer.php"; ?>