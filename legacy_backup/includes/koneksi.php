<?php
// includes/koneksi.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = 'localhost';
$username = 'root'; // Sesuaikan jika menggunakan user lain
$password = ''; // Sesuaikan jika ada password (misal 'root' di MAMP/XAMPP Mac)
$database = 'washly_db';

// Membuat koneksi
$conn = new mysqli($host, $username, $password, $database);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi Database Gagal: " . $conn->connect_error);
}

// Mengatur zona waktu default (sesuai WIB)
date_default_timezone_set('Asia/Jakarta');

// Fungsi helper untuk keamanan input
function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(strip_tags(trim($data))));
}
