<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/db_connect.php'; // Sesuaikan path koneksi DB Anda

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $kode_registrasi = $_POST['kode_registrasi'] ?? '';
    $new_password = $_POST['new_password'] ?? '';

    if (empty($username) || empty($kode_registrasi) || empty($new_password)) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Semua data harus diisi"]);
        exit;
    }

    // Cek apakah username dan kode registrasi cocok di tabel admin_bengkel
    $stmt = $conn->prepare("SELECT id_admin FROM admin_bengkel WHERE username = ? AND kode_registrasi = ?");
    $stmt->bind_param("ss", $username, $kode_registrasi);
    $stmt->execute();

    if ($stmt->get_result()->num_rows > 0) {
        // Jika cocok, reset password dengan hash baru
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE admin_bengkel SET password = ? WHERE username = ?");
        $update->bind_param("ss", $hashed_password, $username);

        if ($update->execute()) {
            echo json_encode(["status" => "success", "message" => "Password berhasil direset"]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Gagal update database"]);
        }
    } else {
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "Username atau Kode Registrasi tidak cocok"]);
    }
    $stmt->close();
    $conn->close();
}
?>