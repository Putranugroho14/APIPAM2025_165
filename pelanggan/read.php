<?php
require_once '../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') sendResponse('error', 'Method not allowed', null, 405);

$query = "SELECT p.*, a.nama_lengkap as nama_admin 
          FROM pelanggan p 
          LEFT JOIN admin_bengkel a ON p.id_admin = a.id_admin 
          ORDER BY p.created_at DESC";

$result = $conn->query($query);
$list = [];

while ($row = $result->fetch_assoc()) {
    $row['id_pelanggan'] = (int)$row['id_pelanggan'];
    $row['id_admin'] = (int)$row['id_admin'];
    $list[] = $row;
}

sendResponse('success', 'Data pelanggan berhasil diambil', $list);
$conn->close();