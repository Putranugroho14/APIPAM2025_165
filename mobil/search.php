<?php
require_once '../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') sendResponse('error', 'Method not allowed', null, 405);

$keyword = "%" . trim($_GET['keyword'] ?? '') . "%";

$stmt = $conn->prepare("SELECT m.*, p.nama_pelanggan 
                        FROM mobil m 
                        JOIN pelanggan p ON m.id_pelanggan = p.id_pelanggan 
                        WHERE m.plat_nomor LIKE ? OR m.merek LIKE ?");
$stmt->bind_param("ss", $keyword, $keyword);
$stmt->execute();
$result = $stmt->get_result();

$list = [];
while ($row = $result->fetch_assoc()) {
    $row['id_mobil'] = (int)$row['id_mobil'];
    $list[] = $row;
}

sendResponse('success', 'Hasil pencarian mobil', $list);
$conn->close();