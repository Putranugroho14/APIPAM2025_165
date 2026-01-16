<?php
require_once '../config/db_connect.php';
require_once '../utils/validator.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse('error', 'Method not allowed', null, 405);
}

// Get POST data
$username = sanitizeInput($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Validasi required
if (empty($username) || empty($password)) {
    sendResponse('error', 'Username dan password wajib diisi', null, 400);
}

// Query admin
$stmt = $conn->prepare("SELECT id_admin, username, password, nama_lengkap, kode_registrasi, created_at 
                        FROM admin_bengkel 
                        WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    sendResponse('error', 'Username tidak ditemukan', null, 404);
}

$admin = $result->fetch_assoc();

// Verifikasi password
if (!password_verify($password, $admin['password'])) {
    sendResponse('error', 'Password salah', null, 401);
}

// Login berhasil
$admin_data = [
    'id_admin' => (int)$admin['id_admin'],
    'username' => $admin['username'],
    'nama_lengkap' => $admin['nama_lengkap'],
    'kode_registrasi' => $admin['kode_registrasi'],
    'created_at' => $admin['created_at']
];

sendResponse('success', 'Login berhasil', $admin_data, 200);

$stmt->close();
$conn->close();
?>