<?php
require_once '../config/db_connect.php'; 
require_once '../utils/validator.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse('error', 'Method not allowed', null, 405);
}

$id_servis = intval($_POST['id_servis'] ?? 0);
$tgl_input = $_POST['tanggal_servis'] ?? '';

// Validasi format tanggal
$date = DateTime::createFromFormat('Y-m-d', $tgl_input);
if (!$date || $date->format('Y-m-d') !== $tgl_input) {
    sendResponse('error', 'Format tanggal tidak valid', null, 400);
}

$v_biaya = Validator::validateBiaya($_POST['biaya'] ?? 0);
$v_status = Validator::validateStatus($_POST['status'] ?? '');
$v_desc = Validator::validateDeskripsi($_POST['deskripsi'] ?? '');

if (!$v_desc['valid']) sendResponse('error', $v_desc['message'], null, 400);
if (!$v_biaya['valid']) sendResponse('error', $v_biaya['message'], null, 400);
if (!$v_status['valid']) sendResponse('error', $v_status['message'], null, 400);

// Update
$stmt = $conn->prepare("UPDATE servis SET deskripsi = ?, biaya = ?, status = ?, tanggal_servis = ? WHERE id_servis = ?");
$stmt->bind_param("sdssi", $v_desc['value'], $v_biaya['value'], $v_status['value'], $tgl_input, $id_servis);

if ($stmt->execute()) {
    // ✅ TAMBAHKAN: Ambil data lengkap setelah update
    $stmt_select = $conn->prepare("
        SELECT 
            s.id_servis,
            s.id_mobil,
            s.id_admin,
            s.deskripsi,
            s.biaya,
            s.status,
            s.tanggal_servis,
            m.plat_nomor,
            m.merek,
            p.nama_pelanggan
        FROM servis s
        JOIN mobil m ON s.id_mobil = m.id_mobil
        JOIN pelanggan p ON m.id_pelanggan = p.id_pelanggan
        WHERE s.id_servis = ?
    ");
    $stmt_select->bind_param("i", $id_servis);
    $stmt_select->execute();
    $result = $stmt_select->get_result();
    
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        sendResponse('success', 'Riwayat servis berhasil diperbarui', $data);
    } else {
        sendResponse('success', 'Riwayat servis berhasil diperbarui');
    }
    $stmt_select->close();
} else {
    sendResponse('error', 'Gagal update: ' . $stmt->error, null, 500);
}

$stmt->close();
$conn->close();
?>