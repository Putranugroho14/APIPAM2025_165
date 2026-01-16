<?php
require_once '../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') sendResponse('error', 'Method not allowed', null, 405);

$id = intval($_GET['id'] ?? 0);
if ($id === 0) sendResponse('error', 'ID tidak valid', null, 400);

// Ambil Data Pelanggan
$stmt = $conn->prepare("SELECT p.*, a.nama_lengkap as nama_admin FROM pelanggan p 
                        LEFT JOIN admin_bengkel a ON p.id_admin = a.id_admin 
                        WHERE p.id_pelanggan = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$pelanggan = $stmt->get_result()->fetch_assoc();

if (!$pelanggan) sendResponse('error', 'Pelanggan tidak ditemukan', null, 404);

// Ambil Mobil Pelanggan
$stmt_m = $conn->prepare("SELECT id_mobil, plat_nomor, merek FROM mobil WHERE id_pelanggan = ?");
$stmt_m->bind_param("i", $id);
$stmt_m->execute();
$res_m = $stmt_m->get_result();

$mobil = [];
while($m = $res_m->fetch_assoc()) {
    $m['id_mobil'] = (int)$m['id_mobil'];
    $mobil[] = $m;
}

$pelanggan['id_pelanggan'] = (int)$pelanggan['id_pelanggan'];
$pelanggan['mobil'] = $mobil;

sendResponse('success', 'Detail pelanggan ditemukan', $pelanggan);
$conn->close();