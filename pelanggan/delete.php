<?php
require_once '../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse('error', 'Method not allowed', null, 405);
}

$id_pelanggan = intval($_POST['id_pelanggan'] ?? 0);

if ($id_pelanggan === 0) {
    sendResponse('error', 'ID pelanggan tidak valid', null, 400);
}

// Cek apakah ada data mobil (Foreign Key Protection)
$stmt = $conn->prepare("SELECT id_mobil FROM mobil WHERE id_pelanggan = ? LIMIT 1");
$stmt->bind_param("i", $id_pelanggan);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    sendResponse('error', 'Gagal hapus: Pelanggan masih memiliki data mobil.', null, 409);
}
$stmt->close();

// Delete
$stmt = $conn->prepare("DELETE FROM pelanggan WHERE id_pelanggan = ?");
$stmt->bind_param("i", $id_pelanggan);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        sendResponse('success', 'Pelanggan berhasil dihapus');
    } else {
        sendResponse('error', 'Data tidak ditemukan', null, 404);
    }
} else {
    sendResponse('error', 'Gagal menghapus: ' . $stmt->error, null, 500);
}
$conn->close();