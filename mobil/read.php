<?php
require_once '../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') sendResponse('error', 'Method not allowed', null, 405);

// JOIN ke tabel pelanggan untuk mendapatkan nama pemilik
$query = "SELECT m.*, p.nama_pelanggan 
          FROM mobil m 
          JOIN pelanggan p ON m.id_pelanggan = p.id_pelanggan 
          ORDER BY m.created_at DESC";

$result = $conn->query($query);
$list = [];

while ($row = $result->fetch_assoc()) {
    $row['id_mobil'] = (int)$row['id_mobil'];
    $row['id_pelanggan'] = (int)$row['id_pelanggan'];
    $list[] = $row;
}

sendResponse('success', 'Daftar mobil berhasil diambil', $list);
$conn->close();