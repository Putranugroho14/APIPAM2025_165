<?php
require_once '../config/db_connect.php';
require_once '../utils/validator.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse('error', 'Method not allowed', null, 405);
}

// Ambil data POST
$id_mobil = intval($_POST['id_mobil'] ?? 0);
$id_admin = intval($_POST['id_admin'] ?? 0);
$deskripsi = $_POST['deskripsi'] ?? '';
$biaya = $_POST['biaya'] ?? 0;
$status = $_POST['status'] ?? 'proses';
$tgl_input = $_POST['tanggal_servis'] ?? '';

// Validasi Tanggal via Validator Class
$tglRes = Validator::validateTanggalServis($tgl_input);
if (!$tglRes['valid']) sendResponse('error', $tglRes['message'], null, 400);

// Validasi Field lainnya
$v_biaya = Validator::validateBiaya($biaya);
$v_status = Validator::validateStatus($status);
$v_desc = Validator::validateDeskripsi($deskripsi);

if (!$v_biaya['valid']) sendResponse('error', $v_biaya['message'], null, 400);
if (!$v_status['valid']) sendResponse('error', $v_status['message'], null, 400);
if (!$v_desc['valid']) sendResponse('error', $v_desc['message'], null, 400);

// Query Insert
$stmt = $conn->prepare("INSERT INTO servis (id_mobil, id_admin, deskripsi, biaya, status, tanggal_servis) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iisdss", $id_mobil, $id_admin, $v_desc['value'], $v_biaya['value'], $v_status['value'], $tglRes['value']);

if ($stmt->execute()) {
    sendResponse('success', 'Servis berhasil dicatat', ["id_servis" => $stmt->insert_id], 201);
} else {
    sendResponse('error', 'Gagal simpan: ' . $stmt->error, null, 400);
}


$conn->close();