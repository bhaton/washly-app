<?php
require_once "includes/koneksi.php";

$pass = password_hash("password", PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO users (name, email, phone, password, role) VALUES ('Driver Antar', 'driver@washly.id', '08111222333', ?, 'driver')");
$stmt->bind_param("s", $pass);
if($stmt->execute()) {
    echo "Driver berhasil ditambahkan.";
} else {
    echo "Gagal: " . $conn->error;
}
?>
