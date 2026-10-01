<?php 
require_once "includes/koneksi.php";

if(isset($_SESSION['user_id'])) {
    if($_SESSION['role'] == 'admin') header("Location: admin_dashboard.php");
    elseif($_SESSION['role'] == 'driver') header("Location: driver_dashboard.php");
    else header("Location: customer_dashboard.php");
    exit;
}

$error = '';
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email_phone = sanitize($_POST['email_phone']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, name, role, password FROM users WHERE email = ? OR phone = ?");
    $stmt->bind_param("ss", $email_phone, $email_phone);
    $stmt->execute();
    $res = $stmt->get_result();

    if($res->num_rows > 0) {
        $user = $res->fetch_assoc();
        if(password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            if($user['role'] == 'admin') {
                header("Location: admin_dashboard.php");
            } elseif($user['role'] == 'driver') {
                header("Location: driver_dashboard.php");
            } else {
                header("Location: customer_dashboard.php");
            }
            exit;
        } else {
            $error = 'Password salah!';
        }
    } else {
        $error = 'Akun tidak ditemukan!';
    }
}
include "includes/header.php"; 
?>
<section id="view-auth" class="view-php">
    <div class="auth-container glass-panel">
        <div class="auth-tabs">
            <div class="auth-tab active" onclick="window.location.href='login.php'">Login</div>
            <div class="auth-tab" onclick="window.location.href='register.php'">Register</div>
        </div>
        
        <!-- Form Login -->
        <div id="form-login" class="auth-form-container active">
            <h2>Selamat Datang Kembali</h2>
            <p class="subtitle">Masuk untuk mengelola pesanan Anda</p>
            
            <?php if($error): ?>
                <div class="alert-info" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; margin-bottom: 1rem;">
                    <i class="fa-solid fa-circle-exclamation"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="form-group">
                    <label>Email / No HP</label>
                    <div class="input-icon-wrapper">
                        <i class="fa-solid fa-user"></i>
                        <input type="text" name="email_phone" class="input-form" placeholder="Masukkan Email atau No HP" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Password</label>
                    <div class="input-icon-wrapper">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="password" class="input-form" placeholder="Masukkan Password" required>
                    </div>
                </div>
                
                <button type="submit" name="login" class="btn btn-glow w-100 mt-4">Login</button>
            </form>
        </div>
    </div>
</section>
<?php include "includes/footer.php"; ?>