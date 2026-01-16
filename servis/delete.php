<?php
require_once '../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse('error', 'Method not allowed', null, 405);
}

// Ambil ID dari POST
$id_servis = intval($_POST['id_servis'] ?? 0);

if ($id_servis === 0) {
    sendResponse('error', 'ID servis tidak valid', null, 400);
}

$stmt = $conn->prepare("DELETE FROM servis WHERE id_servis = ?");
$stmt->bind_param("i", $id_servis);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        sendResponse('success', 'Riwayat servis berhasil dihapus');
    } else {
        sendResponse('error', 'Data tidak ditemukan', null, 404);
    }
} else {
    sendResponse('error', 'Gagal menghapus: ' . $stmt->error, null, 500);
}

$stmt->close();
$conn->close();