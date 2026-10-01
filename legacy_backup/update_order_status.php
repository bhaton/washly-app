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
if($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: admin_dashboard.php");
    exit;
}

$admin_id = $_SESSION['user_id'];
$order_id = intval($_POST['order_id']);
$order_status = sanitize($_POST['order_status']);
$payment_status = sanitize($_POST['payment_status']);
$weight = floatval($_POST['weight']);
$base_price = floatval($_POST['base_price']);
$driver_id = !empty($_POST['driver_id']) ? intval($_POST['driver_id']) : null;

// Calculate Total Price
$total_price = 0;
if($weight > 0) {
    $total_price = $weight * $base_price;
} else {
    // Keep existing total price if weight not provided
    $res = $conn->query("SELECT total_price FROM orders WHERE id = $order_id");
    if($res && $res->num_rows > 0) $total_price = $res->fetch_assoc()['total_price'];
}

// Update Order
$stmt = $conn->prepare("UPDATE orders SET order_status = ?, payment_status = ?, weight = ?, total_price = ?, driver_id = ? WHERE id = ?");
$total_to_update = $total_price > 0 ? $total_price : null;
$weight_to_update = $weight > 0 ? $weight : null;

$stmt->bind_param("ssddii", $order_status, $payment_status, $weight_to_update, $total_to_update, $driver_id, $order_id);
if($stmt->execute()) {
    
    // Upload Admin Photo Documentation
    if(isset($_FILES['admin_photo']) && $_FILES['admin_photo']['error'] == 0) {
        $upload_dir = 'uploads/photos/';
        if(!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $ext = pathinfo($_FILES['admin_photo']['name'], PATHINFO_EXTENSION);
        $filename = "ADMIN_ORD" . $order_id . "_" . time() . "." . $ext;
        $dest = $upload_dir . $filename;
        
        if(move_uploaded_file($_FILES['admin_photo']['tmp_name'], $dest)) {
            $stmt_photo = $conn->prepare("INSERT INTO order_photos (order_id, uploader_id, stage, photo_path) VALUES (?, ?, ?, ?)");
            $stmt_photo->bind_param("iiss", $order_id, $admin_id, $order_status, $dest);
            $stmt_photo->execute();
        }
    }
    
    // Check if status actually changed (to avoid duplicate history logs)
    $res_last_track = $conn->query("SELECT status FROM order_tracking WHERE order_id = $order_id ORDER BY id DESC LIMIT 1");
    $last_status = ($res_last_track && $res_last_track->num_rows > 0) ? $res_last_track->fetch_assoc()['status'] : '';
    
    if($last_status != $order_status) {
        $stmt_track = $conn->prepare("INSERT INTO order_tracking (order_id, status, updated_by) VALUES (?, ?, ?)");
        $stmt_track->bind_param("isi", $order_id, $order_status, $admin_id);
        $stmt_track->execute();
    }
    
    $_SESSION['flash_msg'] = "Order berhasil di-update!";
} else {
    $_SESSION['flash_msg'] = "Gagal mengupdate order: " . $conn->error;
}

header("Location: admin_dashboard.php");
exit;
