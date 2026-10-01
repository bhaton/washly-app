<?php 
require_once "includes/koneksi.php";

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
if($_SESSION['role'] != 'customer') {
    if($_SESSION['role'] == 'admin') header("Location: admin_dashboard.php");
    elseif($_SESSION['role'] == 'driver') header("Location: driver_dashboard.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$success_msg = '';
$error_msg = '';

// Proses Update Profil
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $name = sanitize($_POST['name']);
    $phone = sanitize($_POST['phone']);
    $email = sanitize($_POST['email']);
    $address = sanitize($_POST['address']);
    
    $stmt = $conn->prepare("UPDATE users SET name=?, phone=?, email=?, address=? WHERE id=?");
    $stmt->bind_param("ssssi", $name, $phone, $email, $address, $user_id);
    
    if($stmt->execute()) {
        $_SESSION['name'] = $name;
        $success_msg = "Profil berhasil diperbarui!";
    } else {
        $error_msg = "Gagal memperbarui profil: " . $conn->error;
    }
}

// Ambil data profil
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_data = $stmt->get_result()->fetch_assoc();

// Ambil Pesanan Aktif
$active_orders = [];
$res_active = $conn->query("SELECT o.*, s.name as service_name FROM orders o JOIN services s ON o.service_id = s.id WHERE o.customer_id = $user_id AND o.order_status != 'Order Selesai' ORDER BY o.created_at DESC");
while($row = $res_active->fetch_assoc()){
    $active_orders[] = $row;
}

// Ambil Riwayat Pesanan
$history_orders = [];
$res_history = $conn->query("SELECT o.*, s.name as service_name FROM orders o JOIN services s ON o.service_id = s.id WHERE o.customer_id = $user_id AND o.order_status = 'Order Selesai' ORDER BY o.created_at DESC");
while($row = $res_history->fetch_assoc()){
    $history_orders[] = $row;
}

include "includes/header.php"; 
?>
<section id="view-customer-dash" class="view-php">
    <div class="dashboard-header d-flex justify-between align-center">
        <div>
            <h2>Dashboard Customer</h2>
            <p>Selamat datang, <span class="fw-bold gradient-text"><?= htmlspecialchars($user_data['name']) ?></span></p>
        </div>
        <button class="btn btn-glow" onclick="window.location.href='create_order.php'">
            <i class="fa-solid fa-plus"></i> Buat Pesanan Baru
        </button>
    </div>
    
    <?php if(isset($_SESSION['flash_msg'])): ?>
        <div class="alert-info mt-3 mb-3" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
            <i class="fa-solid fa-circle-check"></i> <?= $_SESSION['flash_msg']; unset($_SESSION['flash_msg']); ?>
        </div>
    <?php endif; ?>
    
    <?php if($success_msg): ?>
        <div class="alert-info mt-3 mb-3" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
            <i class="fa-solid fa-circle-check"></i> <?= $success_msg ?>
        </div>
    <?php endif; ?>
    
    <div class="dashboard-tabs mt-4">
        <button class="dash-tab active" onclick="switchTab('active')" id="tab-btn-active">Pesanan Aktif & Tracking</button>
        <button class="dash-tab" onclick="switchTab('history')" id="tab-btn-history">Riwayat Pesanan</button>
        <button class="dash-tab" onclick="switchTab('profile')" id="tab-btn-profile">Profil</button>
    </div>
    
    <!-- Tab Pesanan Aktif -->
    <div id="c-view-active" class="c-dash-view active mt-4">
        <div class="table-responsive glass-panel">
            <table class="table modern-table" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Order ID & Tanggal</th>
                        <th>Layanan & Kecepatan</th>
                        <th>Status Tracking</th>
                        <th>Total Tagihan</th>
                        <th>Status Pembayaran</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($active_orders) > 0): ?>
                        <?php foreach($active_orders as $ord): ?>
                        <tr>
                            <td>
                                <strong><?= $ord['order_number'] ?></strong><br>
                                <small class="text-light"><?= date('d M Y, H:i', strtotime($ord['created_at'])) ?></small>
                            </td>
                            <td>
                                <?= $ord['service_name'] ?><br>
                                <span class="badge" style="background:#e0e7ff; color:#4338ca;"><?= $ord['speed'] ?></span>
                            </td>
                            <td>
                                <span class="badge badge-status <?= str_replace(' ', '', $ord['order_status']) ?>"><?= $ord['order_status'] ?></span>
                            </td>
                            <td>
                                <strong><?= $ord['total_price'] ? 'Rp '.number_format($ord['total_price'],0,',','.') : 'Belum Dihitung' ?></strong>
                            </td>
                            <td>
                                <?php if($ord['payment_status'] == 'Lunas'): ?>
                                    <span class="badge badge-pay Lunas">Lunas</span>
                                <?php elseif($ord['payment_status'] == 'Menunggu Pembayaran' && $ord['order_status'] == 'Selesai Diantar'): ?>
                                    <!-- Logika: Jika selesai diantar, baru bisa bayar -->
                                    <span class="badge badge-pay" style="background:#fef08a; color:#ca8a04;">Menunggu Pembayaran</span>
                                    <br><button class="btn btn-sm btn-primary mt-2">Bayar Sekarang</button>
                                <?php else: ?>
                                    <span class="badge badge-pay">Belum Dibayar</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-light" style="padding: 2rem;">Belum ada pesanan aktif. Yuk buat pesanan pertama Anda!</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Tab Riwayat Pesanan -->
    <div id="c-view-history" class="c-dash-view mt-4">
        <div class="table-responsive glass-panel">
            <table class="table modern-table" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Order ID & Tanggal</th>
                        <th>Layanan</th>
                        <th>Total Item</th>
                        <th>Total Tagihan</th>
                        <th>Status Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($history_orders) > 0): ?>
                        <?php foreach($history_orders as $ord): ?>
                        <tr>
                            <td>
                                <strong><?= $ord['order_number'] ?></strong><br>
                                <small class="text-light"><?= date('d M Y, H:i', strtotime($ord['created_at'])) ?></small>
                            </td>
                            <td><?= $ord['service_name'] ?></td>
                            <td><?= $ord['total_items'] ?> pcs</td>
                            <td><strong>Rp <?= number_format($ord['total_price'],0,',','.') ?></strong></td>
                            <td><span class="badge badge-status Selesai">Order Selesai</span></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-light" style="padding: 2rem;">Belum ada riwayat pesanan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Tab Profil -->
    <div id="c-view-profile" class="c-dash-view mt-4">
        <div class="glass-panel" style="max-width: 600px;">
            <h3 class="mb-3">Data Profil</h3>
            <form action="customer_dashboard.php" method="POST">
                <input type="hidden" name="update_profile" value="1">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <div class="input-icon-wrapper">
                        <i class="fa-solid fa-user"></i>
                        <input type="text" name="name" class="input-form" value="<?= htmlspecialchars($user_data['name']) ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Nomor HP</label>
                    <div class="input-icon-wrapper">
                        <i class="fa-solid fa-phone"></i>
                        <input type="tel" name="phone" class="input-form" value="<?= htmlspecialchars($user_data['phone']) ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <div class="input-icon-wrapper">
                        <i class="fa-solid fa-envelope"></i>
                        <input type="email" name="email" class="input-form" value="<?= htmlspecialchars($user_data['email']) ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Alamat Lengkap (Untuk Default Pickup/Delivery)</label>
                    <textarea name="address" class="input-form" rows="3" required><?= htmlspecialchars($user_data['address']) ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary mt-3 w-100">Simpan Perubahan</button>
            </form>
        </div>
    </div>
</section>

<script>
function switchTab(tabId) {
    document.querySelectorAll('.dash-tab').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.c-dash-view').forEach(el => el.classList.remove('active'));
    
    document.getElementById('tab-btn-' + tabId).classList.add('active');
    document.getElementById('c-view-' + tabId).classList.add('active');
}
</script>
<?php include "includes/footer.php"; ?>