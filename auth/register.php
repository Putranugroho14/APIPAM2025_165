<?php
require_once '../config/db_connect.php';
require_once '../utils/validator.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse('error', 'Method not allowed', null, 405);
}

// Ambil Data & Sanitasi (Gunakan fungsi dari db_connect.php)
$kode_registrasi = sanitizeInput($_POST['kode_registrasi'] ?? '');
$username = sanitizeInput($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$nama_lengkap = sanitizeInput($_POST['nama_lengkap'] ?? '');

// Validasi Required (Gunakan fungsi dari db_connect.php)
$missing = validateRequired(['kode_registrasi', 'username', 'password', 'nama_lengkap'], $_POST);
if (!empty($missing)) {
    sendResponse('error', 'Field wajib diisi: ' . implode(', ', $missing), null, 400);
}

// Validasi Format via Validator Class
$kode_valid = Validator::validateKodeRegistrasi($kode_registrasi);
if (!$kode_valid['valid'])
    sendResponse('error', $kode_valid['message'], null, 400);

$username_valid = Validator::validateUsername($username);
if (!$username_valid['valid'])
    sendResponse('error', $username_valid['message'], null, 400);

$password_valid = Validator::validatePassword($password);
if (!$password_valid['valid'])
    sendResponse('error', $password_valid['message'], null, 400);

$nama_valid = Validator::validateNama($nama_lengkap);
if (!$nama_valid['valid'])
    sendResponse('error', $nama_valid['message'], null, 400);

// Cek Duplikasi Database
$stmt = $conn->prepare("SELECT id_admin FROM admin_bengkel WHERE kode_registrasi = ? OR username = ?");
$stmt->bind_param("ss", $kode_registrasi, $username);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    sendResponse('error', 'Username atau Kode Registrasi sudah terdaftar', null, 409);
}
$stmt->close();

// Hash & Insert
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO admin_bengkel (username, kode_registrasi, password, nama_lengkap) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $username, $kode_registrasi, $hashed_password, $nama_lengkap);

if ($stmt->execute()) {
    sendResponse('success', 'Registrasi berhasil', ['username' => $username], 201);
} else {
    sendResponse('error', 'Gagal menyimpan data', null, 500);
}

$stmt->close();
$conn->close();
?>