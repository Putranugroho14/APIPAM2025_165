<?php
require_once '../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendResponse('error', 'Method not allowed', null, 405);
}

// Query mengambil data lengkap dengan info mobil, pelanggan, dan updated_at
$query = "SELECT s.*, m.plat_nomor, m.merek, p.nama_pelanggan, a.nama_lengkap as nama_admin 
          FROM servis s 
          JOIN mobil m ON s.id_mobil = m.id_mobil 
          JOIN pelanggan p ON m.id_pelanggan = p.id_pelanggan 
          JOIN admin_bengkel a ON s.id_admin = a.id_admin 
          ORDER BY s.updated_at DESC"; // Urutkan berdasarkan update terbaru

$result = $conn->query($query);
$list = [];

while ($row = $result->fetch_assoc()) {
    // Casting tipe data agar sesuai dengan Model di Kotlin
    $row['id_servis'] = (int)$row['id_servis'];
    $row['id_mobil'] = (int)$row['id_mobil'];
    $row['id_admin'] = (int)$row['id_admin'];
    $row['biaya'] = (float)$row['biaya'];
    $list[] = $row;
}

sendResponse('success', 'Daftar servis berhasil diambil', $list);
$conn->close();