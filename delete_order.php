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

if(isset($_GET['id'])) {
    $id = intval($_GET['id']);
    // Delete photo files associated if needed
    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->bind_param("i", $id);
    if($stmt->execute()) {
        $_SESSION['flash_msg'] = "Pesanan berhasil dihapus.";
    } else {
        $_SESSION['flash_msg'] = "Gagal menghapus pesanan.";
    }
}

header("Location: admin_dashboard.php");
exit;
