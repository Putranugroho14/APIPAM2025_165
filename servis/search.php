<?php
require_once '../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') sendResponse('error', 'Method not allowed', null, 405);

$keyword = "%" . trim($_GET['keyword'] ?? '') . "%";
$stmt = $conn->prepare("SELECT s.*, m.plat_nomor, p.nama_pelanggan 
                        FROM servis s 
                        JOIN mobil m ON s.id_mobil = m.id_mobil 
                        JOIN pelanggan p ON m.id_pelanggan = p.id_pelanggan 
                        WHERE m.plat_nomor LIKE ? OR p.nama_pelanggan LIKE ? OR s.deskripsi LIKE ?
                        ORDER BY s.tanggal_servis DESC");
$stmt->bind_param("sss", $keyword, $keyword, $keyword);
$stmt->execute();
$result = $stmt->get_result();

$list = [];
while ($row = $result->fetch_assoc()) {
    $list[] = $row;
}

sendResponse('success', 'Hasil pencarian servis', $list);
$conn->close();