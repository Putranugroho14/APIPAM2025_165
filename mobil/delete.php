<?php
require_once '../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendResponse('error', 'Method not allowed', null, 405);

$id = intval($_POST['id_mobil'] ?? 0);

// Cek riwayat servis
$stmt = $conn->prepare("SELECT id_servis FROM servis WHERE id_mobil = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    sendResponse('error', 'Tidak bisa hapus: Mobil memiliki riwayat servis.', null, 409);
}
$stmt->close();

$stmt = $conn->prepare("DELETE FROM mobil WHERE id_mobil = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    sendResponse('success', 'Mobil berhasil dihapus');
} else {
    sendResponse('error', 'Data tidak ditemukan', null, 404);
}
$conn->close();