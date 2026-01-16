<?php
require_once '../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendResponse('error', 'Method not allowed', null, 405);
}

// Ambil ID dari parameter URL (?id=...)
$id_servis = intval($_GET['id'] ?? 0);

if ($id_servis === 0) {
    sendResponse('error', 'ID servis tidak valid', null, 400);
}

// Query mengambil detail satu servis dengan JOIN lengkap
$stmt = $conn->prepare("SELECT s.*, 
                        m.plat_nomor, m.merek, 
                        p.nama_pelanggan, p.no_hp, p.alamat,
                        a.nama_lengkap as nama_admin 
                        FROM servis s 
                        LEFT JOIN mobil m ON s.id_mobil = m.id_mobil 
                        LEFT JOIN pelanggan p ON m.id_pelanggan = p.id_pelanggan 
                        LEFT JOIN admin_bengkel a ON s.id_admin = a.id_admin 
                        WHERE s.id_servis = ?");
$stmt->bind_param("i", $id_servis);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    sendResponse('error', 'Data servis tidak ditemukan', null, 404);
}

$servis = $result->fetch_assoc();

// Pastikan tipe data dikembalikan dengan benar untuk GSON Kotlin
$data = [
    'id_servis' => (int)$servis['id_servis'],
    'id_mobil' => (int)$servis['id_mobil'],
    'plat_nomor' => $servis['plat_nomor'],
    'merek' => $servis['merek'],
    'nama_pelanggan' => $servis['nama_pelanggan'],
    'no_hp' => $servis['no_hp'],
    'alamat' => $servis['alamat'],
    'id_admin' => (int)$servis['id_admin'],
    'nama_admin' => $servis['nama_admin'],
    'tanggal_servis' => $servis['tanggal_servis'],
    'deskripsi' => $servis['deskripsi'],
    'biaya' => (float)$servis['biaya'],
    'status' => $servis['status'],
    'created_at' => $servis['created_at'],
    'updated_at' => $servis['updated_at'] // Tambahkan ini agar sinkron dengan database baru
];

sendResponse('success', 'Detail servis berhasil diambil', $data);

$stmt->close();
$conn->close();
?>