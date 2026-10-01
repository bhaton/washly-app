<?php 
require_once "includes/koneksi.php";

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
if($_SESSION['role'] != 'admin') {
    if($_SESSION['role'] == 'driver') header("Location: driver_dashboard.php");
    else header("Location: customer_dashboard.php");
    exit;
}

// Mengambil Statistik
$today = date('Y-m-d');
$stat_today = $conn->query("SELECT COUNT(id) as total FROM orders WHERE DATE(created_at) = '$today'")->fetch_assoc()['total'];
$stat_pickup = $conn->query("SELECT COUNT(id) as total FROM orders WHERE order_status = 'Menunggu Pickup' OR order_status = 'Order Dibuat'")->fetch_assoc()['total'];
$stat_process = $conn->query("SELECT COUNT(id) as total FROM orders WHERE order_status IN ('Diterima Outlet','Diproses')")->fetch_assoc()['total'];
$stat_done = $conn->query("SELECT COUNT(id) as total FROM orders WHERE order_status IN ('Siap Diantar','Sedang Diantar','Selesai Diantar','Order Selesai')")->fetch_assoc()['total'];

// Mengambil Semua Order
$orders = [];
$res = $conn->query("SELECT o.*, u.name as customer_name, s.name as service_name, s.price_regular, s.price_express, s.unit, d.name as driver_name FROM orders o JOIN users u ON o.customer_id = u.id JOIN services s ON o.service_id = s.id LEFT JOIN users d ON o.driver_id = d.id ORDER BY o.created_at DESC");
while($row = $res->fetch_assoc()) {
    $orders[] = $row;
}

// Mengambil Semua Driver
$drivers = [];
$res_drv = $conn->query("SELECT id, name FROM users WHERE role = 'driver'");
if($res_drv) {
    while($r = $res_drv->fetch_assoc()) {
        $drivers[] = $r;
    }
}

// List of available statuses
$statuses = [
    'Order Dibuat',
    'Menunggu Pickup',
    'Laundry Dijemput',
    'Diterima Outlet',
    'Diproses',
    'Siap Diantar',
    'Sedang Diantar',
    'Selesai Diantar',
    'Order Selesai'
];

include "includes/header.php"; 
?>
<section id="view-admin-dash" class="view-php">
    <div class="dashboard-header d-flex justify-between align-center">
        <div>
            <h2>Admin Portal</h2>
            <p>Pusat pengelolaan operasional WASHLY</p>
        </div>
        <div class="user-badge" style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 0.5rem 1rem; border-radius: 8px;">
            <i class="fa-solid fa-user-tie"></i> <?= htmlspecialchars($_SESSION['name']) ?>
        </div>
    </div>
    
    <?php if(isset($_SESSION['flash_msg'])): ?>
        <div class="alert-info mt-3 mb-3" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
            <i class="fa-solid fa-circle-check"></i> <?= $_SESSION['flash_msg']; unset($_SESSION['flash_msg']); ?>
        </div>
    <?php endif; ?>

    <div class="admin-stats mt-4">
        <div class="stat-card glass-panel" style="border-left: 4px solid #3b82f6;">
            <div class="stat-icon" style="color: #3b82f6;"><i class="fa-solid fa-inbox"></i></div>
            <div class="stat-info">
                <p>Order Hari Ini</p>
                <h3><?= $stat_today ?></h3>
            </div>
        </div>
        <div class="stat-card glass-panel" style="border-left: 4px solid #f59e0b;">
            <div class="stat-icon" style="color: #f59e0b;"><i class="fa-solid fa-truck-pickup"></i></div>
            <div class="stat-info">
                <p>Belum Dijemput</p>
                <h3><?= $stat_pickup ?></h3>
            </div>
        </div>
        <div class="stat-card glass-panel" style="border-left: 4px solid #8b5cf6;">
            <div class="stat-icon" style="color: #8b5cf6;"><i class="fa-solid fa-spinner"></i></div>
            <div class="stat-info">
                <p>Diproses</p>
                <h3><?= $stat_process ?></h3>
            </div>
        </div>
        <div class="stat-card glass-panel" style="border-left: 4px solid #10b981;">
            <div class="stat-icon" style="color: #10b981;"><i class="fa-solid fa-check-double"></i></div>
            <div class="stat-info">
                <p>Siap / Selesai</p>
                <h3><?= $stat_done ?></h3>
            </div>
        </div>
    </div>

    <div class="admin-actions mt-4">
        <button class="btn btn-primary" onclick="window.location.href='create_order.php'"><i class="fa-solid fa-plus"></i> Buat Order Walk-in</button>
    </div>

    <div class="table-responsive glass-panel mt-4">
        <div class="d-flex justify-between align-center mb-3">
            <h3>Kelola Pesanan</h3>
        </div>
        <table class="table modern-table" style="width: 100%;">
            <thead>
                <tr>
                    <th>No Order / Tgl</th>
                    <th>Customer Info</th>
                    <th>Layanan & Berat</th>
                    <th>Status Tracking</th>
                    <th>Total & Pembayaran</th>
                    <th>Aksi Update</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($orders as $ord): ?>
                <tr>
                    <td>
                        <strong><?= $ord['order_number'] ?></strong><br>
                        <small class="text-light"><?= date('d/m/Y H:i', strtotime($ord['created_at'])) ?></small>
                    </td>
                    <td>
                        <?= htmlspecialchars($ord['customer_name']) ?><br>
                        <small class="text-light"><i class="fa-solid fa-phone"></i> <?= htmlspecialchars($ord['pickup_phone']) ?></small>
                    </td>
                    <td>
                        <?= $ord['service_name'] ?> <span class="badge" style="background:#e0e7ff; color:#4338ca;"><?= $ord['speed'] ?></span><br>
                        <small class="text-light">
                            Berat: <?= $ord['weight'] ? $ord['weight'].' '.$ord['unit'] : '-' ?>
                        </small>
                    </td>
                    <td>
                        <span class="badge badge-status <?= str_replace(' ', '', $ord['order_status']) ?>"><?= $ord['order_status'] ?></span>
                    </td>
                    <td>
                        <strong><?= $ord['total_price'] ? 'Rp '.number_format($ord['total_price'],0,',','.') : '-' ?></strong><br>
                        <small class="badge badge-pay <?= $ord['payment_status'] ?>"><?= $ord['payment_status'] ?></small>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline mb-1 w-100" style="border-color: #3b82f6; color: #3b82f6;" onclick='openUpdateModal(<?= json_encode($ord) ?>)'>
                            <i class="fa-solid fa-pen"></i> Update
                        </button>
                        <a href="view_photos.php?id=<?= $ord['id'] ?>" class="btn btn-sm btn-outline mb-1 w-100" style="border-color: #10b981; color: #10b981; display: inline-block; text-align: center;">
                            <i class="fa-solid fa-image"></i> Cek Foto
                        </a>
                        <a href="delete_order.php?id=<?= $ord['id'] ?>" class="btn btn-sm btn-outline w-100" style="border-color: #ef4444; color: #ef4444; display: inline-block; text-align: center;" onclick="return confirm('Anda yakin ingin menghapus data pesanan ini secara permanen?')">
                            <i class="fa-solid fa-trash"></i> Hapus
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(count($orders) == 0): ?>
                    <tr><td colspan="6" class="text-center">Belum ada data pesanan.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal Update Status -->
    <div id="admin-action-modal" class="modal">
        <div class="modal-content glass-panel">
            <span class="close" onclick="closeAdminModal()">&times;</span>
            <h3 id="modal-order-id" class="mb-3" style="color: var(--text-main);">Update Order</h3>
            
            <form action="update_order_status.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="order_id" id="modal-order-id-input">
                <input type="hidden" name="base_price" id="modal-base-price">
                <input type="hidden" name="unit" id="modal-unit">
                
                <div class="form-group mb-3">
                    <label>Ubah Status Tracking</label>
                    <select name="order_status" id="modal-status-select" class="input-form custom-select" required>
                        <?php foreach($statuses as $st): ?>
                            <option value="<?= $st ?>"><?= $st ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group mb-3">
                    <label>Assign Driver (Kurir)</label>
                    <select name="driver_id" id="modal-driver-select" class="input-form custom-select">
                        <option value="">-- Belum Di-assign --</option>
                        <?php foreach($drivers as $drv): ?>
                            <option value="<?= $drv['id'] ?>"><?= $drv['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group mb-3">
                    <label>Status Pembayaran</label>
                    <select name="payment_status" id="modal-payment-status" class="input-form custom-select">
                        <option value="Belum Dibayar">Belum Dibayar</option>
                        <option value="Menunggu Pembayaran">Menunggu Pembayaran</option>
                        <option value="Lunas">Lunas</option>
                        <option value="Gagal">Gagal</option>
                    </select>
                </div>
                
                <div class="alert-info mb-3" style="font-size: 0.85rem;">
                    Input berat akan otomatis menghitung Total Tagihan berdasarkan tarif layanan.
                </div>
                
                <div class="form-group mb-3">
                    <label>Input Berat/Qty (<span id="modal-unit-label">kg</span>)</label>
                    <input type="number" step="0.1" name="weight" id="modal-weight" class="input-form" placeholder="Contoh: 2.5">
                </div>
                
                <div class="form-group mb-4">
                    <label>Upload Dokumentasi Admin (Opsional)</label>
                    <input type="file" name="admin_photo" class="input-form" accept="image/*">
                </div>
                
                <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
            </form>
        </div>
    </div>

</section>

<script>
function openUpdateModal(order) {
    document.getElementById('modal-order-id').innerText = 'Update: ' + order.order_number;
    document.getElementById('modal-order-id-input').value = order.id;
    
    // Set Status
    document.getElementById('modal-status-select').value = order.order_status;
    document.getElementById('modal-payment-status').value = order.payment_status;
    document.getElementById('modal-driver-select').value = order.driver_id ? order.driver_id : '';
    
    // Set Weight
    document.getElementById('modal-weight').value = order.weight ? order.weight : '';
    
    // Set Price Reference
    let price = (order.speed === 'Express') ? order.price_express : order.price_regular;
    document.getElementById('modal-base-price').value = price;
    
    document.getElementById('modal-unit-label').innerText = order.unit;
    document.getElementById('modal-unit').value = order.unit;
    
    let modal = document.getElementById('admin-action-modal');
    modal.style.display = 'flex';
}

function closeAdminModal() {
    document.getElementById('admin-action-modal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    let modal = document.getElementById('admin-action-modal');
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
</script>
<?php include "includes/footer.php"; ?>