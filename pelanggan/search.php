<?php
require_once '../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendResponse('error', 'Method not allowed', null, 405);
}

// Ambil keyword dari GET parameter
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

// Jika keyword kosong, return error
if (empty($keyword)) {
    sendResponse('error', 'Keyword tidak boleh kosong', null, 400);
}

// ✅ Pattern untuk nama: hanya dari huruf AWAL (keyword%)
$namaKeyword = $keyword . "%";

// ✅ Pattern untuk no HP: bisa di mana saja (%keyword%)
$hpKeyword = "%" . $keyword . "%";

// ✅ SEARCH: Nama (huruf awal saja) ATAU No HP (di mana saja)
$stmt = $conn->prepare("SELECT p.*, a.nama_lengkap as nama_admin,
                        (SELECT COUNT(*) FROM mobil WHERE id_pelanggan = p.id_pelanggan) as total_mobil
                        FROM pelanggan p 
                        LEFT JOIN admin_bengkel a ON p.id_admin = a.id_admin 
                        WHERE p.nama_pelanggan LIKE ? 
                        OR p.no_hp LIKE ?
                        ORDER BY p.nama_pelanggan ASC");

$stmt->bind_param("ss", $namaKeyword, $hpKeyword);
$stmt->execute();
$result = $stmt->get_result();

$list = [];
while ($row = $result->fetch_assoc()) {
    $row['id_pelanggan'] = (int)$row['id_pelanggan'];
    $row['id_admin'] = (int)$row['id_admin'];
    $row['total_mobil'] = (int)($row['total_mobil'] ?? 0);
    $list[] = $row;
}

// Selalu return success dengan array (kosong atau isi)
if (empty($list)) {
    sendResponse('success', 'Tidak ada hasil yang cocok', []);
} else {
    sendResponse('success', count($list) . ' data ditemukan', $list);
}

$stmt->close();
$conn->close();