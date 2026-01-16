<?php
require_once '../config/db_connect.php';
require_once '../utils/validator.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendResponse('error', 'Method not allowed', null, 405);

$id = intval($_POST['id_pelanggan'] ?? 0);
$nama = sanitizeInput($_POST['nama_pelanggan'] ?? '');
$no_hp = sanitizeInput($_POST['no_hp'] ?? '');
$alamat = sanitizeInput($_POST['alamat'] ?? '');

if ($id === 0) sendResponse('error', 'ID tidak valid', null, 400);

// Validasi via Validator
$v_nama = Validator::validateNama($nama);
$v_hp = Validator::validateNoHP($no_hp);

if (!$v_nama['valid']) sendResponse('error', $v_nama['message'], null, 400);
if (!$v_hp['valid']) sendResponse('error', $v_hp['message'], null, 400);

// Update
$stmt = $conn->prepare("UPDATE pelanggan SET nama_pelanggan = ?, no_hp = ?, alamat = ? WHERE id_pelanggan = ?");
$stmt->bind_param("sssi", $v_nama['value'], $v_hp['value'], $alamat, $id);

if ($stmt->execute()) {
    sendResponse('success', 'Data pelanggan diperbarui');
} else {
    sendResponse('error', 'Gagal update: ' . $stmt->error, null, 500);
}
$conn->close();