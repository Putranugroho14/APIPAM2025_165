<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: POST");

require_once '../config/db_connect.php';
require_once '../utils/validator.php'; // Pastikan lowercase sesuai sidebar

// Mengambil data JSON raw atau POST biasa
$data = json_decode(file_get_contents("php://input"), true) ?? $_POST;

if (!empty($data['plat_nomor']) && !empty($data['merek']) && !empty($data['id_pelanggan']) && !empty($data['id_admin'])) {
    
    // 1. Sanitasi Input
    $plat_raw = sanitizeInput($data['plat_nomor']);
    $merek_raw = sanitizeInput($data['merek']);
    $id_pelanggan = intval($data['id_pelanggan']);
    $id_admin = intval($data['id_admin']);

    // 2. Validasi Plat Nomor (Regex: B 1234 ABC)
    $platValidation = Validator::validatePlatNomor($plat_raw);
    if (!$platValidation['valid']) {
        sendResponse("error", $platValidation['message'], null, 400);
    }
    $plat_nomor = $platValidation['value']; // Menggunakan 'value' sesuai Validator class

    // 3. Validasi Merek
    $merekValidation = Validator::validateMerek($merek_raw);
    if (!$merekValidation['valid']) {
        sendResponse("error", $merekValidation['message'], null, 400);
    }
    $merek = $merekValidation['value'];

    // 4. Cek Duplikasi Plat Nomor
    $checkQuery = "SELECT id_mobil FROM mobil WHERE plat_nomor = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("s", $plat_nomor);
    $checkStmt->execute();
    if ($checkStmt->get_result()->num_rows > 0) {
        sendResponse("error", "Plat nomor sudah terdaftar", null, 409);
    }
    $checkStmt->close();

    // 5. Insert Data
    $query = "INSERT INTO mobil (plat_nomor, merek, id_pelanggan, id_admin) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssii", $plat_nomor, $merek, $id_pelanggan, $id_admin);

    if ($stmt->execute()) {
        $resultData = [
            "id_mobil" => $stmt->insert_id,
            "plat_nomor" => $plat_nomor
        ];
        sendResponse("success", "Mobil berhasil ditambahkan", $resultData, 201);
    } else {
        sendResponse("error", "Gagal menyimpan data: " . $stmt->error, null, 500);
    }
    $stmt->close();

} else {
    sendResponse("error", "Data tidak lengkap. Pastikan plat_nomor, merek, id_pelanggan, dan id_admin terisi.", null, 400);
}

$conn->close();
?>