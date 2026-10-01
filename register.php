<?php 
require_once "includes/koneksi.php";

if(isset($_SESSION['user_id'])) {
    if($_SESSION['role'] == 'admin') header("Location: admin_dashboard.php");
    elseif($_SESSION['role'] == 'driver') header("Location: driver_dashboard.php");
    else header("Location: customer_dashboard.php");
    exit;
}

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $name = sanitize($_POST['name']);
    $phone = sanitize($_POST['phone']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $password_conf = $_POST['password_conf'];
    $address = sanitize($_POST['address']);

    if($password !== $password_conf) {
        $error = "Konfirmasi password tidak cocok!";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE phone = ? OR email = ?");
        $check->bind_param("ss", $phone, $email);
        $check->execute();
        if($check->get_result()->num_rows > 0) {
            $error = "Email atau Nomor HP sudah terdaftar!";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $role = 'customer';
            
            $stmt = $conn->prepare("INSERT INTO users (role, name, phone, email, password, address) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $role, $name, $phone, $email, $hashed, $address);
            
            if($stmt->execute()) {
                $success = "Registrasi berhasil! Silakan login.";
            } else {
                $error = "Terjadi kesalahan sistem.";
            }
        }
    }
}

include "includes/header.php"; 
?>
<section id="view-auth" class="view-php">
    <div class="auth-container glass-panel">
        <div class="auth-tabs">
            <div class="auth-tab" onclick="window.location.href='login.php'">Login</div>
            <div class="auth-tab active" onclick="window.location.href='register.php'">Register</div>
        </div>
        
        <!-- Form Register -->
        <div id="form-register" class="auth-form-container active">
            <h2>Buat Akun Baru</h2>
            <p class="subtitle">Bergabunglah dengan layanan laundry terbaik</p>
            
            <?php if($error): ?>
                <div class="alert-info" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; margin-bottom: 1rem;">
                    <i class="fa-solid fa-circle-exclamation"></i> <?= $error ?>
                </div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="alert-info" style="background: rgba(16, 185, 129, 0.1); color: #10b981; margin-bottom: 1rem;">
                    <i class="fa-solid fa-check-circle"></i> <?= $success ?>
                </div>
            <?php endif; ?>

            <form action="register.php" method="POST">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="name" class="input-form" placeholder="Contoh: John Doe" required>
                </div>
                
                <div class="form-group-row">
                    <div class="form-group">
                        <label>Nomor HP</label>
                        <input type="tel" name="phone" class="input-form" placeholder="0812..." required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="input-form" placeholder="john@example.com" required>
                    </div>
                </div>
                
                <div class="form-group-row">
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="input-form" placeholder="Minimal 6 karakter" required minlength="6">
                    </div>
                    <div class="form-group">
                        <label>Konfirmasi Password</label>
                        <input type="password" name="password_conf" class="input-form" placeholder="Ulangi password" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Alamat Lengkap</label>
                    <textarea name="address" class="input-form" placeholder="Alamat untuk penjemputan..." required></textarea>
                </div>
                
                <button type="submit" name="register" class="btn btn-glow w-100 mt-4">Daftar Sekarang</button>
            </form>
        </div>
    </div>
</section>
<?php include "includes/footer.php"; ?>