<?php 
require_once "includes/koneksi.php";

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
if($_SESSION['role'] != 'driver') {
    if($_SESSION['role'] == 'admin') header("Location: admin_dashboard.php");
    else header("Location: customer_dashboard.php");
    exit;
}

$driver_id = $_SESSION['user_id'];

// Mengambil Tugas Aktif (yang ditugaskan ke driver ini dan belum selesai diantar atau masih proses pickup)
$active_tasks = [];
$res_active = $conn->query("SELECT o.*, u.name as customer_name, s.name as service_name FROM orders o JOIN users u ON o.customer_id = u.id JOIN services s ON o.service_id = s.id WHERE o.driver_id = $driver_id AND o.order_status NOT IN ('Selesai Diantar', 'Order Selesai') ORDER BY o.pickup_time ASC");
while($row = $res_active->fetch_assoc()) {
    $active_tasks[] = $row;
}

// Mengambil Riwayat Tugas
$history_tasks = [];
$res_history = $conn->query("SELECT o.*, u.name as customer_name, s.name as service_name FROM orders o JOIN users u ON o.customer_id = u.id JOIN services s ON o.service_id = s.id WHERE o.driver_id = $driver_id AND o.order_status IN ('Selesai Diantar', 'Order Selesai') ORDER BY o.created_at DESC LIMIT 20");
while($row = $res_history->fetch_assoc()) {
    $history_tasks[] = $row;
}

include "includes/header.php"; 
?>
<section id="view-driver-dash" class="view-php" style="padding: 2rem 5%;">
    <div class="dashboard-header d-flex justify-between align-center mb-4">
        <div>
            <h2>Portal Kurir</h2>
            <p>Halo, <?= htmlspecialchars($_SESSION['name']) ?>. Berikut daftar tugas Anda.</p>
        </div>
    </div>
    
    <?php if(isset($_SESSION['flash_msg'])): ?>
        <div class="alert-info mt-3 mb-3" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
            <i class="fa-solid fa-circle-check"></i> <?= $_SESSION['flash_msg']; unset($_SESSION['flash_msg']); ?>
        </div>
    <?php endif; ?>

    <!-- Tabs -->
    <div class="dash-tabs mb-4" style="display: flex; gap: 15px; border-bottom: 2px solid rgba(255,255,255,0.1); padding-bottom: 10px;">
        <div class="dash-tab active" id="tab-active" style="cursor: pointer; font-weight: 600; color: var(--primary); border-bottom: 2px solid var(--primary); margin-bottom: -12px;" onclick="switchTab('active')">Tugas Aktif (<?= count($active_tasks) ?>)</div>
        <div class="dash-tab" id="tab-history" style="cursor: pointer; font-weight: 600; color: var(--text-light);" onclick="switchTab('history')">Riwayat Tugas</div>
    </div>

    <!-- View Active -->
    <div id="view-active">
        <div class="grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
            <?php foreach($active_tasks as $task): ?>
            <div class="glass-panel" style="padding: 1.5rem; position: relative;">
                <div class="d-flex justify-between mb-2">
                    <span class="badge" style="background: rgba(139, 92, 246, 0.2); color: #a78bfa; border: 1px solid rgba(139, 92, 246, 0.3); padding: 4px 10px; border-radius: 6px; font-weight: 600;"><?= $task['order_number'] ?></span>
                    <span class="badge badge-status <?= str_replace(' ', '', $task['order_status']) ?>"><?= $task['order_status'] ?></span>
                </div>
                
                <h3 style="margin-bottom: 10px; color: var(--text-main);"><?= htmlspecialchars($task['customer_name']) ?></h3>
                
                <?php if($task['order_status'] == 'Order Dibuat' || $task['order_status'] == 'Menunggu Pickup'): ?>
                    <p class="text-light" style="font-size: 0.9rem; margin-bottom: 5px;"><i class="fa-solid fa-location-arrow"></i> <strong>Pickup:</strong> <?= htmlspecialchars($task['pickup_address']) ?></p>
                    <p class="text-light" style="font-size: 0.9rem; margin-bottom: 15px;"><i class="fa-solid fa-clock"></i> Waktu: <?= date('d/m/Y H:i', strtotime($task['pickup_time'])) ?></p>
                    <a href="https://www.google.com/maps/search/?api=1&query=<?= urlencode($task['pickup_address']) ?>" target="_blank" class="btn btn-outline btn-sm w-100 mb-3" style="border-color: #38bdf8; color: #38bdf8; text-align: center;"><i class="fa-solid fa-map-location-dot"></i> Navigasi Maps (Pickup)</a>
                <?php else: ?>
                    <p class="text-light" style="font-size: 0.9rem; margin-bottom: 5px;"><i class="fa-solid fa-truck"></i> <strong>Delivery:</strong> <?= htmlspecialchars($task['delivery_address']) ?></p>
                    <p class="text-light" style="font-size: 0.9rem; margin-bottom: 15px;"><i class="fa-solid fa-clock"></i> Waktu: <?= date('d/m/Y H:i', strtotime($task['delivery_time'])) ?></p>
                    <a href="https://www.google.com/maps/search/?api=1&query=<?= urlencode($task['delivery_address']) ?>" target="_blank" class="btn btn-outline btn-sm w-100 mb-3" style="border-color: #38bdf8; color: #38bdf8; text-align: center;"><i class="fa-solid fa-map-location-dot"></i> Navigasi Maps (Delivery)</a>
                <?php endif; ?>
                
                <p class="text-light" style="font-size: 0.9rem; margin-bottom: 15px;"><i class="fa-solid fa-phone"></i> <?= htmlspecialchars($task['pickup_phone']) ?></p>
                
                <button class="btn btn-primary btn-glow w-100" onclick='openUpdateModal(<?= json_encode($task) ?>)'><i class="fa-solid fa-pen-to-square"></i> Update Status Kurir</button>
            </div>
            <?php endforeach; ?>
            
            <?php if(count($active_tasks) == 0): ?>
                <div class="alert-info w-100" style="background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.3); color: #60a5fa; padding: 1rem; border-radius: 12px;">Belum ada tugas aktif yang diberikan admin kepada Anda.</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- View History -->
    <div id="view-history" style="display:none;">
        <div class="table-responsive glass-panel">
            <table class="table modern-table" style="width: 100%;">
                <thead>
                    <tr>
                        <th>No Order / Tgl</th>
                        <th>Customer</th>
                        <th>Layanan</th>
                        <th>Status Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($history_tasks as $hist): ?>
                    <tr>
                        <td><strong><?= $hist['order_number'] ?></strong><br><small class="text-light"><?= date('d/m/Y', strtotime($hist['created_at'])) ?></small></td>
                        <td><?= htmlspecialchars($hist['customer_name']) ?></td>
                        <td><?= $hist['service_name'] ?></td>
                        <td><span class="badge" style="background: #10b981; color: white; border-radius: 6px; padding: 4px 8px;"><?= $hist['order_status'] ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(count($history_tasks) == 0): ?>
                        <tr><td colspan="4" class="text-center" style="padding: 2rem; color: var(--text-light);">Belum ada riwayat tugas.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Update Driver -->
    <div id="driver-action-modal" class="modal">
        <div class="modal-content glass-panel">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3 id="modal-order-id" class="mb-3" style="color: var(--text-main); font-size: 1.25rem;">Update Tugas</h3>
            
            <form action="update_driver_status.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="order_id" id="modal-order-id-input">
                
                <div class="form-group mb-3">
                    <label>Update Status</label>
                    <select name="order_status" id="modal-status-select" class="input-form custom-select" required>
                        <option value="Laundry Dijemput">Telah Dijemput (Bawa ke Outlet)</option>
                        <option value="Sedang Diantar">Sedang Diantar ke Customer</option>
                        <option value="Selesai Diantar">Selesai Diantar (Diterima Customer)</option>
                    </select>
                </div>
                
                <div class="form-group mb-4">
                    <label>Upload Bukti Foto (Opsional)</label>
                    <input type="file" name="driver_photo" class="input-form" accept="image/*">
                    <small class="text-light" style="display: block; margin-top: 6px;">Upload foto barang/lokasi sebagai bukti.</small>
                </div>
                
                <button type="submit" class="btn btn-primary btn-glow w-100" style="padding: 0.8rem;">Simpan Laporan</button>
            </form>
        </div>
    </div>

</section>

<script>
function switchTab(tab) {
    document.getElementById('tab-active').style.color = 'var(--text-light)';
    document.getElementById('tab-active').style.borderBottom = 'none';
    document.getElementById('tab-history').style.color = 'var(--text-light)';
    document.getElementById('tab-history').style.borderBottom = 'none';
    
    document.getElementById('view-active').style.display = 'none';
    document.getElementById('view-history').style.display = 'none';
    
    document.getElementById(`tab-${tab}`).style.color = 'var(--primary)';
    document.getElementById(`tab-${tab}`).style.borderBottom = '2px solid var(--primary)';
    document.getElementById(`view-${tab}`).style.display = 'block';
}

function openUpdateModal(task) {
    document.getElementById('modal-order-id').innerText = 'Update: ' + task.order_number;
    document.getElementById('modal-order-id-input').value = task.id;
    
    // Set default select based on current status
    let sel = document.getElementById('modal-status-select');
    if(task.order_status === 'Menunggu Pickup' || task.order_status === 'Order Dibuat') {
        sel.value = 'Laundry Dijemput';
    } else if(task.order_status === 'Siap Diantar') {
        sel.value = 'Sedang Diantar';
    } else if(task.order_status === 'Sedang Diantar') {
        sel.value = 'Selesai Diantar';
    }
    
    document.getElementById('driver-action-modal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('driver-action-modal').style.display = 'none';
}

window.onclick = function(event) {
    let modal = document.getElementById('driver-action-modal');
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
</script>
<?php include "includes/footer.php"; ?>
