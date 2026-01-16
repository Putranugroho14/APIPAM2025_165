<?php
require_once '../config/db_connect.php';
require_once '../utils/validator.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendResponse('error', 'Method not allowed', null, 405);

$id = intval($_POST['id_mobil'] ?? 0);
$plat = sanitizeInput($_POST['plat_nomor'] ?? '');
$merek = sanitizeInput($_POST['merek'] ?? '');

if ($id === 0) sendResponse('error', 'ID tidak valid', null, 400);

// Validasi
$v_plat = Validator::validatePlatNomor($plat);
$v_merek = Validator::validateMerek($merek);

if (!$v_plat['valid']) sendResponse('error', $v_plat['message'], null, 400);

// Cek Duplikasi Plat lain
$stmt = $conn->prepare("SELECT id_mobil FROM mobil WHERE plat_nomor = ? AND id_mobil != ?");
$stmt->bind_param("si", $v_plat['value'], $id);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) sendResponse('error', 'Plat nomor sudah digunakan mobil lain', null, 409);
$stmt->close();

$stmt = $conn->prepare("UPDATE mobil SET plat_nomor = ?, merek = ? WHERE id_mobil = ?");
$stmt->bind_param("ssi", $v_plat['value'], $v_merek['value'], $id);

if ($stmt->execute()) {
    sendResponse('success', 'Data mobil diperbarui');
} else {
    sendResponse('error', 'Gagal update: ' . $stmt->error, null, 500);
}
$conn->close();