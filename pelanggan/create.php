<?php
// 1. Header JSON + CORS
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 2. Sertakan file koneksi dan validator
require_once '../config/db_connect.php'; 
require_once '../utils/validator.php'; 

// 3. CEK METHOD
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse('error', 'Method not allowed. Gunakan POST.', null, 405);
}

// 4. AMBIL DATA & SANITASI
$nama_pelanggan = sanitizeInput($_POST['nama_pelanggan'] ?? '');
$no_hp          = sanitizeInput($_POST['no_hp'] ?? '');
$alamat         = sanitizeInput($_POST['alamat'] ?? '');
$id_admin       = intval($_POST['id_admin'] ?? 0);

// 5. VALIDASI FIELD WAJIB (Menggunakan fungsi dari db_connect.php)
$missing = validateRequired(['nama_pelanggan', 'no_hp', 'id_admin'], $_POST);
if (!empty($missing)) {
    sendResponse('error', 'Field berikut wajib diisi: ' . implode(', ', $missing), null, 400);
}

// 6. VALIDASI FORMAT (Menggunakan Class Validator)
$nama_valid = Validator::validateNama($nama_pelanggan);
if (!$nama_valid['valid']) sendResponse('error', $nama_valid['message'], null, 400);
$nama_pelanggan = $nama_valid['value'];

$no_hp_valid = Validator::validateNoHP($no_hp);
if (!$no_hp_valid['valid']) sendResponse('error', $no_hp_valid['message'], null, 400);
$no_hp = $no_hp_valid['value'];

// 7. CEK DUPLIKASI NOMOR HP
$stmt = $conn->prepare("SELECT id_pelanggan FROM pelanggan WHERE no_hp = ?");
$stmt->bind_param("s", $no_hp);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    $stmt->close();
    sendResponse('error', 'Nomor HP pelanggan ini sudah terdaftar', null, 409);
}
$stmt->close();

// 8. CEK VALIDITAS ID ADMIN (Foreign Key Check)
$stmt = $conn->prepare("SELECT id_admin FROM admin_bengkel WHERE id_admin = ?");
$stmt->bind_param("i", $id_admin);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    $stmt->close();
    sendResponse('error', 'ID Admin tidak ditemukan di database', null, 400);
}
$stmt->close();

// 9. INSERT DATA
$stmt = $conn->prepare("INSERT INTO pelanggan (nama_pelanggan, no_hp, alamat, id_admin) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sssi", $nama_pelanggan, $no_hp, $alamat, $id_admin);

if ($stmt->execute()) {
    $new_id = $stmt->insert_id;
    
    // Ambil data yang baru dibuat untuk dikirim balik
    $stmt_get = $conn->prepare("SELECT * FROM pelanggan WHERE id_pelanggan = ?");
    $stmt_get->bind_param("i", $new_id);
    $stmt_get->execute();
    $pelanggan = $stmt_get->get_result()->fetch_assoc();
    $stmt_get->close();

    sendResponse('success', 'Pelanggan berhasil ditambahkan', $pelanggan, 201);
} else {
    sendResponse('error', 'Gagal menyimpan data: ' . $stmt->error, null, 500);
}

$stmt->close();
$conn->close();
?>