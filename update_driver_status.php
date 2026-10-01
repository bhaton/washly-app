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
if($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: driver_dashboard.php");
    exit;
}

$driver_id = $_SESSION['user_id'];
$order_id = intval($_POST['order_id']);
$order_status = sanitize($_POST['order_status']);

// Verifikasi kepemilikan tugas
$check = $conn->query("SELECT id FROM orders WHERE id = $order_id AND driver_id = $driver_id");
if($check->num_rows === 0) {
    $_SESSION['flash_msg'] = "Gagal: Pesanan tidak valid atau bukan tugas Anda.";
    header("Location: driver_dashboard.php");
    exit;
}

// Update Order
$stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
$stmt->bind_param("si", $order_status, $order_id);
if($stmt->execute()) {
    
    // Upload Driver Photo Documentation
    if(isset($_FILES['driver_photo']) && $_FILES['driver_photo']['error'] == 0) {
        $upload_dir = 'uploads/photos/';
        if(!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $ext = pathinfo($_FILES['driver_photo']['name'], PATHINFO_EXTENSION);
        $filename = "DRIVER_ORD" . $order_id . "_" . time() . "." . $ext;
        $dest = $upload_dir . $filename;
        
        if(move_uploaded_file($_FILES['driver_photo']['tmp_name'], $dest)) {
            $stmt_photo = $conn->prepare("INSERT INTO order_photos (order_id, uploader_id, stage, photo_path) VALUES (?, ?, ?, ?)");
            $stmt_photo->bind_param("iiss", $order_id, $driver_id, $order_status, $dest);
            $stmt_photo->execute();
        }
    }
    
    // Insert Tracking History
    $stmt_track = $conn->prepare("INSERT INTO order_tracking (order_id, status, updated_by) VALUES (?, ?, ?)");
    $stmt_track->bind_param("isi", $order_id, $order_status, $driver_id);
    $stmt_track->execute();
    
    $_SESSION['flash_msg'] = "Status tugas berhasil diperbarui!";
} else {
    $_SESSION['flash_msg'] = "Gagal mengupdate tugas: " . $conn->error;
}

header("Location: driver_dashboard.php");
exit;
