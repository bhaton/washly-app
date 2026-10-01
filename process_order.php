<?php
require_once "includes/koneksi.php";

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
if($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: create_order.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$service_id = intval($_POST['service_id']);
$speed = sanitize($_POST['order_speed']);
$notes = sanitize($_POST['order_note']);

// Pickup
$pickup_name = sanitize($_POST['pickup_name']);
$pickup_phone = sanitize($_POST['pickup_phone']);
$pickup_address = sanitize($_POST['pickup_address']);
$pickup_time = sanitize($_POST['pickup_time']);

// Delivery
$same_delivery = isset($_POST['same_delivery_address']) ? true : false;
if($same_delivery || empty($_POST['delivery_address'])) {
    $delivery_name = $pickup_name;
    $delivery_phone = $pickup_phone;
    $delivery_address = $pickup_address;
    $delivery_time = $pickup_time; // Asumsi bisa dinegosiasikan
} else {
    $delivery_name = sanitize($_POST['delivery_name']);
    $delivery_phone = sanitize($_POST['delivery_phone']);
    $delivery_address = sanitize($_POST['delivery_address']);
    $delivery_time = sanitize($_POST['delivery_time']);
}

// Total Items Estimate
$total_items = 0;
if(isset($_POST['items'])) {
    foreach($_POST['items'] as $name => $qty) {
        $total_items += intval($qty);
    }
}

// Generate Order Number
$date_prefix = date("Ymd");
// Get last order of today
$res = $conn->query("SELECT order_number FROM orders WHERE order_number LIKE 'ORD-$date_prefix-%' ORDER BY id DESC LIMIT 1");
if($res && $res->num_rows > 0) {
    $last_ord = $res->fetch_assoc()['order_number'];
    $num = intval(substr($last_ord, -3)) + 1;
} else {
    $num = 1;
}
$order_number = "ORD-" . $date_prefix . "-" . str_pad($num, 3, "0", STR_PAD_LEFT);

// Insert Order
$stmt = $conn->prepare("INSERT INTO orders (order_number, customer_id, service_id, speed, pickup_name, pickup_phone, pickup_address, pickup_time, delivery_name, delivery_phone, delivery_address, delivery_time, notes, total_items) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("siissssssssssi", $order_number, $user_id, $service_id, $speed, $pickup_name, $pickup_phone, $pickup_address, $pickup_time, $delivery_name, $delivery_phone, $delivery_address, $delivery_time, $notes, $total_items);

if($stmt->execute()) {
    $order_id = $conn->insert_id;
    
    // Insert Items
    if(isset($_POST['items'])) {
        $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, item_name, quantity) VALUES (?, ?, ?)");
        foreach($_POST['items'] as $item_name => $qty) {
            $qty = intval($qty);
            if($qty > 0) {
                $name = sanitize($item_name);
                $stmt_item->bind_param("isi", $order_id, $name, $qty);
                $stmt_item->execute();
            }
        }
    }
    
    // Upload Photo
    if(isset($_FILES['order_photo']) && $_FILES['order_photo']['error'] == 0) {
        $upload_dir = 'uploads/photos/';
        if(!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $ext = pathinfo($_FILES['order_photo']['name'], PATHINFO_EXTENSION);
        $filename = $order_number . "_pickup." . $ext;
        $dest = $upload_dir . $filename;
        
        if(move_uploaded_file($_FILES['order_photo']['tmp_name'], $dest)) {
            $stmt_photo = $conn->prepare("INSERT INTO order_photos (order_id, uploader_id, stage, photo_path) VALUES (?, ?, 'Sebelum Pickup', ?)");
            $stmt_photo->bind_param("iis", $order_id, $user_id, $dest);
            $stmt_photo->execute();
        }
    }
    
    // Insert Tracking History
    $status = 'Order Dibuat';
    $stmt_track = $conn->prepare("INSERT INTO order_tracking (order_id, status, updated_by) VALUES (?, ?, ?)");
    $stmt_track->bind_param("isi", $order_id, $status, $user_id);
    $stmt_track->execute();
    
    // Redirect to Success/Dashboard
    $_SESSION['flash_msg'] = "Order $order_number berhasil dibuat!";
    header("Location: customer_dashboard.php");
} else {
    die("Error: " . $conn->error);
}
