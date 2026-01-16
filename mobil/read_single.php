<?php
require_once '../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') sendResponse('error', 'Method not allowed', null, 405);

$id = intval($_GET['id'] ?? 0);
if ($id === 0) sendResponse('error', 'ID tidak valid', null, 400);

$stmt = $conn->prepare("SELECT m.*, p.nama_pelanggan, p.no_hp as hp_pemilik 
                        FROM mobil m 
                        JOIN pelanggan p ON m.id_pelanggan = p.id_pelanggan 
                        WHERE m.id_mobil = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$mobil = $stmt->get_result()->fetch_assoc();

if (!$mobil) sendResponse('error', 'Mobil tidak ditemukan', null, 404);

$mobil['id_mobil'] = (int)$mobil['id_mobil'];
sendResponse('success', 'Detail mobil ditemukan', $mobil);
$conn->close();