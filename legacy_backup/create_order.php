<?php 
require_once "includes/koneksi.php";

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Ambil data layanan dari DB
$services = [];
$res = $conn->query("SELECT * FROM services WHERE status = 'Aktif'");
if($res) {
    while($row = $res->fetch_assoc()) {
        $services[] = $row;
    }
}

include "includes/header.php"; 
?>
<section id="view-create-order" class="view-php">
    
    <!-- Original Stepper UI (Text Only) -->
    <div class="stepper-simple" style="display: flex; justify-content: center; gap: 40px; margin-bottom: 2rem; color: #94a3b8; font-weight: 500;">
        <div class="step active" id="step-1-indicator" style="color: #4f46e5; border-bottom: 2px solid #4f46e5; padding-bottom: 5px;">Layanan</div>
        <div class="step" id="step-2-indicator" style="padding-bottom: 5px;">Barang & Foto</div>
        <div class="step" id="step-3-indicator" style="padding-bottom: 5px;">Pengiriman</div>
        <div class="step" id="step-4-indicator" style="padding-bottom: 5px;">Konfirmasi</div>
    </div>
    
    <form action="process_order.php" method="POST" enctype="multipart/form-data" id="orderForm" onsubmit="return validateFinalOrder();">
        <div class="order-form-card glass-panel" style="max-width: 800px; margin: 0 auto; padding: 2rem; border-radius: 12px;">
            
            <!-- Step 1: Layanan & Kecepatan -->
            <div id="order-step-1" class="order-step active animate-slide-up">
                <h3 class="mb-4" style="font-size: 1.25rem;">Pilih Layanan & Kecepatan</h3>
                
                <div class="form-group mb-4">
                    <label style="font-weight: 500; display: block; margin-bottom: 8px;">Pilih Layanan</label>
                    <select name="service_id" id="service_id" class="input-form custom-select">
                        <option value="" disabled selected>-- Pilih Layanan --</option>
                        <?php foreach($services as $srv): ?>
                            <option value="<?= $srv['id'] ?>" data-regular="<?= $srv['price_regular'] ?>" data-express="<?= $srv['price_express'] ?>" data-est-reg="<?= $srv['est_regular'] ?>" data-est-exp="<?= $srv['est_express'] ?>" data-unit="<?= $srv['unit'] ?>">
                                <?= $srv['name'] ?> (Rp <?= number_format($srv['price_regular'], 0, ',', '.') ?>/<?= $srv['unit'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group mb-4">
                    <div class="radio-cards" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <label class="radio-card" style="border-radius: 8px; cursor: pointer; text-align: center;">
                            <input type="radio" name="order_speed" value="Regular" checked style="display:none;">
                            <div class="card-content">
                                <h4 style="margin-bottom: 5px;">Regular</h4>
                                <p id="desc-reg" class="text-light" style="font-size: 0.85rem;">Sesuai estimasi layanan</p>
                            </div>
                        </label>
                        <label class="radio-card" style="border-radius: 8px; cursor: pointer; text-align: center;">
                            <input type="radio" name="order_speed" value="Express" style="display:none;">
                            <div class="card-content">
                                <h4 style="margin-bottom: 5px;">Express</h4>
                                <p id="desc-exp" class="text-light" style="font-size: 0.85rem;">Lebih cepat (+Biaya)</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="step-actions" style="margin-top: 2rem; display: flex; gap: 15px;">
                    <button type="button" class="btn btn-outline" style="border: 1px solid var(--primary); color: var(--primary); background: transparent; padding: 10px 24px; border-radius: 8px; font-weight: 500;" onclick="window.location.href='customer_dashboard.php'">Batal</button>
                    <button type="button" class="btn btn-primary btn-glow" style="padding: 10px 24px; border-radius: 8px; border: none; font-weight: 500; display: flex; align-items: center; gap: 8px;" onclick="nextStep(1, 2)">Selanjutnya <i class="fa-solid fa-arrow-right"></i></button>
                </div>
            </div>

            <!-- Step 2: Barang & Foto -->
            <div id="order-step-2" class="order-step" style="display:none;">
                <h3 class="mb-4" style="font-size: 1.25rem;">Data Barang & Upload Foto</h3>
                
                <div class="form-group-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div class="form-group">
                        <label style="font-size: 0.9rem;">Baju (pcs)</label>
                        <input type="number" name="items[Baju]" class="input-form qty-input" min="0" value="0">
                    </div>
                    <div class="form-group">
                        <label style="font-size: 0.9rem;">Celana (pcs)</label>
                        <input type="number" name="items[Celana]" class="input-form qty-input" min="0" value="0">
                    </div>
                </div>
                <div class="form-group-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div class="form-group">
                        <label style="font-size: 0.9rem;">Jaket (pcs)</label>
                        <input type="number" name="items[Jaket]" class="input-form qty-input" min="0" value="0">
                    </div>
                    <div class="form-group">
                        <label style="font-size: 0.9rem;">Selimut (pcs)</label>
                        <input type="number" name="items[Selimut]" class="input-form qty-input" min="0" value="0">
                    </div>
                </div>
                <div class="form-group-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div class="form-group">
                        <label style="font-size: 0.9rem;">Bed Cover (pcs)</label>
                        <input type="number" name="items[Bed Cover]" class="input-form qty-input" min="0" value="0">
                    </div>
                    <div class="form-group">
                        <label style="font-size: 0.9rem;">Sepatu (pasang)</label>
                        <input type="number" name="items[Sepatu]" class="input-form qty-input" min="0" value="0">
                    </div>
                </div>
                
                <div class="form-group mb-4">
                    <label style="font-size: 0.9rem;">Upload Foto Barang (Wajib)</label>
                    <div class="file-upload-box" style="margin-top: 5px; border: 2px dashed rgba(255,255,255,0.2); padding: 1.5rem; text-align: center; border-radius: 12px; background: rgba(0,0,0,0.2); cursor: pointer; position: relative;">
                        <i class="fa-solid fa-cloud-arrow-up" id="upload-icon" style="font-size: 2rem; color: var(--primary); margin-bottom: 10px;"></i>
                        <p id="upload-text" style="margin-bottom: 0; color: var(--text-main);">Klik atau drag file foto ke sini</p>
                        <input type="file" name="order_photo" id="order_photo" accept="image/*" class="file-input" style="position: absolute; top:0; left:0; width:100%; height:100%; opacity:0; cursor:pointer;" onchange="showFileName(this)">
                    </div>
                </div>
                
                <div class="form-group">
                    <label style="font-size: 0.9rem;">Catatan Khusus</label>
                    <textarea name="order_note" id="order_note" class="input-form" placeholder="Contoh: Pisahkan baju putih..." style="min-height: 80px;"></textarea>
                </div>
                
                <div class="step-actions" style="margin-top: 2rem; display: flex; gap: 15px;">
                    <button type="button" class="btn btn-outline" style="border: 1px solid var(--primary); color: var(--primary); background: transparent; padding: 10px 24px; border-radius: 8px; font-weight: 500;" onclick="nextStep(2, 1)">Kembali</button>
                    <button type="button" class="btn btn-primary btn-glow" style="padding: 10px 24px; border-radius: 8px; border: none; font-weight: 500; display: flex; align-items: center; gap: 8px;" onclick="nextStep(2, 3)">Selanjutnya <i class="fa-solid fa-arrow-right"></i></button>
                </div>
            </div>

            <!-- Step 3: Pengiriman -->
            <div id="order-step-3" class="order-step" style="display:none;">
                <h3 class="mb-4" style="font-size: 1.25rem;">Alamat Pengiriman (Pickup & Delivery)</h3>
                
                <div class="address-box mb-4">
                    <h4 style="margin-bottom: 15px;">Detail Penjemputan (Pickup)</h4>
                    
                    <div class="alert-info mb-3" style="font-size: 0.85rem; background: rgba(245, 158, 11, 0.1); color: var(--warning); border: 1px solid rgba(245, 158, 11, 0.2); padding: 0.8rem; border-radius: 8px;">
                        <i class="fa-solid fa-circle-exclamation"></i> <strong>Penting:</strong> Layanan kurir hanya mencakup radius <strong>maksimal 10 KM</strong> dari outlet kami.
                    </div>

                    <div class="form-group-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                        <input type="text" name="pickup_name" id="pickup_name" class="input-form" placeholder="Nama Pengirim" value="<?= htmlspecialchars($_SESSION['name']) ?>">
                        <input type="text" name="pickup_phone" id="pickup_phone" class="input-form" placeholder="No HP (WhatsApp)">
                    </div>
                    <textarea name="pickup_address" id="pickup_address" class="input-form mb-3" placeholder="Alamat lengkap (Jalan, RT/RW, No. Rumah)..." style="min-height: 60px;"></textarea>
                    
                    <div class="form-group mb-3">
                        <label class="custom-checkbox" style="font-weight: 500; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" id="radius_confirm">
                            <span class="checkmark"></span>
                            Saya mengonfirmasi alamat penjemputan/pengiriman berjarak maksimal 10 KM.
                        </label>
                    </div>

                    <div class="form-group">
                        <label style="font-size: 0.9rem; margin-bottom: 5px; display: block;">Waktu Penjemputan</label>
                        <input type="datetime-local" name="pickup_time" id="pickup_time" class="input-form">
                    </div>
                </div>
                
                <div class="form-group mb-4" style="padding-left: 5px;">
                    <label class="custom-checkbox" style="font-weight: 500; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="same_delivery_address" id="same-delivery-address" checked onchange="toggleDelivery()">
                        <span class="checkmark"></span>
                        Alamat pengantaran (Delivery) sama dengan penjemputan
                    </label>
                </div>
                
                <div class="address-box" id="delivery-address-box" style="display:none; margin-bottom: 20px;">
                    <h4 style="margin-bottom: 15px;">Detail Pengantaran (Delivery)</h4>
                    <div class="form-group-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                        <input type="text" name="delivery_name" id="delivery_name" class="input-form" placeholder="Nama Penerima">
                        <input type="text" name="delivery_phone" id="delivery_phone" class="input-form" placeholder="No HP">
                    </div>
                    <textarea name="delivery_address" id="delivery_address" class="input-form mb-3" placeholder="Alamat lengkap..." style="min-height: 60px;"></textarea>
                    <input type="datetime-local" name="delivery_time" id="delivery_time" class="input-form">
                </div>
                
                <div class="step-actions" style="margin-top: 2rem; display: flex; gap: 15px;">
                    <button type="button" class="btn btn-outline" style="border: 1px solid var(--primary); color: var(--primary); background: transparent; padding: 10px 24px; border-radius: 8px; font-weight: 500;" onclick="nextStep(3, 2)">Kembali</button>
                    <button type="button" class="btn btn-primary btn-glow" style="padding: 10px 24px; border-radius: 8px; border: none; font-weight: 500; display: flex; align-items: center; gap: 8px;" onclick="if(validateStep3()) { prepareConfirmation(); nextStep(3, 4); }">Selanjutnya <i class="fa-solid fa-arrow-right"></i></button>
                </div>
            </div>

            <!-- Step 4: Konfirmasi -->
            <div id="order-step-4" class="order-step" style="display:none; background: transparent; padding: 0; box-shadow: none; border: none;">
                <h3 class="mb-4" style="font-size: 1.25rem;">Konfirmasi Pesanan</h3>
                
                <div class="konfirmasi-layout">
                    <!-- Left Grid -->
                    <div class="k-left-grid">
                        <div class="k-card">
                            <div class="k-icon-wrap" style="background: rgba(139, 92, 246, 0.2); color: var(--primary);"><i class="fa-solid fa-shirt"></i></div>
                            <div class="k-card-content">
                                <div class="k-card-header">
                                    <h4 class="text-light" style="font-size: 0.85rem;">Layanan & Kecepatan</h4>
                                </div>
                                <h5 class="k-title" id="conf_service_name">-</h5>
                                <p id="conf_speed" style="color: var(--primary); font-weight: 600; font-size: 0.9rem; margin-top: 5px;">-</p>
                            </div>
                        </div>
                        
                        <div class="k-card">
                            <div class="k-icon-wrap" style="background: rgba(245, 158, 11, 0.2); color: var(--warning);"><i class="fa-solid fa-clock"></i></div>
                            <div class="k-card-content">
                                <div class="k-card-header">
                                    <h4 class="text-light" style="font-size: 0.85rem;">Estimasi Waktu</h4>
                                </div>
                                <h5 class="k-title" id="conf_est_time">-</h5>
                            </div>
                        </div>
                        
                        <div class="k-card" style="grid-column: span 2;">
                            <div class="k-icon-wrap" style="background: rgba(236, 72, 153, 0.2); color: var(--secondary);"><i class="fa-solid fa-location-dot"></i></div>
                            <div class="k-card-content">
                                <div class="k-card-header">
                                    <h4 class="text-light" style="font-size: 0.85rem;">Alamat Penjemputan</h4>
                                </div>
                                <p class="k-desc" id="conf_pickup_address">-</p>
                                <small id="conf_pickup_time" class="fw-bold" style="color: var(--primary);"></small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Sidebar -->
                    <div class="k-sidebar">
                        <div class="k-summary-box">
                            <div class="k-summary-header-main">
                                <i class="fa-solid fa-clipboard-list" style="color: var(--primary);"></i>
                                <h3>Ringkasan Barang</h3>
                            </div>
                            
                            <div class="k-summary-section">
                                <div class="k-sum-item">
                                    <div class="k-sum-text">
                                        <span class="label">Total Kuantitas</span>
                                        <span class="value fw-bold" id="conf_total_items">0 pcs</span>
                                    </div>
                                </div>
                                <div class="k-sum-item mt-2">
                                    <div class="k-sum-text">
                                        <span class="label">Catatan</span>
                                        <span class="value" id="conf_notes">-</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert-info" style="font-size: 0.8rem; background: rgba(139, 92, 246, 0.1); color: #c4b5fd; border: 1px solid rgba(139, 92, 246, 0.2); padding: 0.8rem; border-radius: 8px; margin-top: 1rem;">
                                <strong>Info Tagihan:</strong> Biaya pasti akan dihitung oleh kasir setelah barang ditimbang di outlet. Pembayaran dapat dilakukan secara tunai/QRIS setelah barang selesai diantar.
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="k-footer-actions" style="margin-top: 2rem; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
                    <button type="button" class="btn btn-outline" style="border: 1px solid var(--primary); color: var(--primary); background: transparent; padding: 10px 24px; border-radius: 8px; font-weight: 500;" onclick="nextStep(4, 3)">Kembali</button>
                    
                    <div style="display: flex; align-items: center; gap: 20px;">
                        <label class="custom-checkbox" style="color: var(--text-main); font-weight: 500; font-size: 0.9rem; margin-bottom: 0; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" id="agree-terms">
                            <span class="checkmark"></span>
                            Data yang diisi sudah benar.
                        </label>
                        <button type="submit" class="btn btn-primary btn-glow" style="padding: 12px 30px; border-radius: 8px; border: none; font-weight: 600;">Buat Pesanan <i class="fa-solid fa-check"></i></button>
                    </div>
                </div>
            </div>

        </div>
    </form>
</section>

<script>
document.querySelectorAll('input[name="order_speed"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.radio-card').forEach(c => {
            c.style.borderColor = 'rgba(255,255,255,0.1)';
            c.style.background = 'rgba(0,0,0,0.2)';
        });
        this.closest('.radio-card').style.borderColor = 'var(--primary)';
        this.closest('.radio-card').style.background = 'rgba(139, 92, 246, 0.1)';
    });
});

function validateStep3() {
    let pName = document.getElementById('pickup_name').value.trim();
    let pPhone = document.getElementById('pickup_phone').value.trim();
    let pAddr = document.getElementById('pickup_address').value.trim();
    let pTime = document.getElementById('pickup_time').value.trim();
    
    if(!pName || !pPhone || !pAddr || !pTime) {
        alert("Harap isi nama, no HP, alamat, dan waktu penjemputan dengan lengkap.");
        return false;
    }
    if(!document.getElementById('radius_confirm').checked) {
        alert("Anda harus mengonfirmasi bahwa lokasi berada dalam radius maksimal 10 KM.");
        return false;
    }
    return true;
}

// JS Logic for the 4-step wizard
function nextStep(current, next) {
    if(next > current) {
        if(current === 1 && !document.getElementById('service_id').value) {
            alert("Harap pilih layanan terlebih dahulu.");
            return;
        }
        if(current === 2 && !document.getElementById('order_photo').value) {
            alert("Harap upload foto barang.");
            return;
        }
        if(current === 3 && !validateStep3()) {
            return;
        }
    }
    
    for(let i=1; i<=4; i++) {
        let el = document.getElementById(`order-step-${i}`);
        if(el) {
            el.style.display = (i === next) ? 'block' : 'none';
            if(i === next) el.classList.add('active');
            else el.classList.remove('active');
        }
    }
    
    // Save state to sessionStorage so refresh doesn't reset it
    sessionStorage.setItem('currentOrderStep', next);
    
    // Update Text Stepper UI
    for(let i=1; i<=4; i++) {
        let ind = document.getElementById(`step-${i}-indicator`);
        if(ind) {
            if(i === next) {
                ind.style.color = 'var(--primary)';
                ind.style.borderBottom = '2px solid var(--primary)';
            } else {
                ind.style.color = 'var(--text-light)';
                ind.style.borderBottom = 'none';
            }
        }
    }

    if(next === 4) {
        prepareConfirmation();
    }
}

function validateFinalOrder() {
    let sId = document.getElementById('service_id').value;
    if(!sId) {
        alert("Harap pilih layanan pada Langkah 1 terlebih dahulu!");
        nextStep(4, 1);
        return false;
    }
    
    let photo = document.getElementById('order_photo');
    if(!photo.files || photo.files.length === 0) {
        alert("Foto barang wajib di-upload! Silakan upload pada Langkah 2.");
        nextStep(4, 2);
        return false;
    }
    
    if(!validateStep3()) {
        nextStep(4, 3);
        return false;
    }
    
    let agree = document.getElementById('agree-terms').checked;
    if(!agree) {
        alert("Harap centang 'Data yang diisi sudah benar' sebelum membuat pesanan.");
        return false;
    }
    
    sessionStorage.removeItem('currentOrderStep');
    return true;
}

// Restore step on page load
window.addEventListener('DOMContentLoaded', () => {
    let savedStep = sessionStorage.getItem('currentOrderStep');
    // If service_id is not selected, always force back to step 1
    if(!document.getElementById('service_id').value) {
        savedStep = 1;
        sessionStorage.setItem('currentOrderStep', 1);
    }
    if(savedStep && savedStep >= 1 && savedStep <= 4) {
        nextStep(1, parseInt(savedStep));
    }
});

document.getElementById('service_id').addEventListener('change', function() {
    let sel = this.options[this.selectedIndex];
    if(sel && sel.value) {
        document.getElementById('desc-reg').innerText = sel.getAttribute('data-est-reg') || "Sesuai estimasi layanan";
        document.getElementById('desc-exp').innerText = sel.getAttribute('data-est-exp') ? sel.getAttribute('data-est-exp') : "Tidak tersedia";
    }
});

function toggleDelivery() {
    let cb = document.getElementById('same-delivery-address');
    let box = document.getElementById('delivery-address-box');
    if(cb.checked) box.style.display = 'none';
    else box.style.display = 'block';
}

function showFileName(input) {
    if(input.files && input.files[0]) {
        document.getElementById('upload-text').innerText = input.files[0].name;
        document.getElementById('upload-icon').className = "fa-solid fa-check text-success";
    }
}

function prepareConfirmation() {
    let sel = document.getElementById('service_id');
    if(sel && sel.selectedIndex >= 0 && sel.value) {
        let srvName = sel.options[sel.selectedIndex].text;
        document.getElementById('conf_service_name').innerText = srvName;
        
        let speedRadio = document.querySelector('input[name="order_speed"]:checked');
        let speed = speedRadio ? speedRadio.value : 'Regular';
        document.getElementById('conf_speed').innerText = speed;
        
        let estTime = (speed === 'Express') ? sel.options[sel.selectedIndex].getAttribute('data-est-exp') : sel.options[sel.selectedIndex].getAttribute('data-est-reg');
        document.getElementById('conf_est_time').innerText = estTime || "-";
    } else {
        document.getElementById('conf_service_name').innerText = "Belum dipilih";
        document.getElementById('conf_speed').innerText = "-";
        document.getElementById('conf_est_time').innerText = "-";
    }
    
    document.getElementById('conf_pickup_address').innerText = document.getElementById('pickup_address').value || "Belum diisi";
    let pTime = document.getElementById('pickup_time').value;
    document.getElementById('conf_pickup_time').innerText = pTime ? pTime.replace('T', ' ') : "";
    
    let totalItems = 0;
    document.querySelectorAll('.qty-input').forEach(input => {
        totalItems += parseInt(input.value) || 0;
    });
    document.getElementById('conf_total_items').innerText = totalItems + " pcs";
    document.getElementById('conf_notes').innerText = document.getElementById('order_note').value || "-";
}
</script>
<?php include "includes/footer.php"; ?>