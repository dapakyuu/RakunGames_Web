<?php
session_start();
include '../service/koneksi.php';

if (!isset($_SESSION['is_login']) || !$_SESSION['is_user']) {
    http_response_code(403);
    exit(json_encode(['error' => 'Unauthorized']));
}

if (!isset($_GET['id_pesanan'])) {
    http_response_code(400);
    exit(json_encode(['error' => 'ID Pesanan tidak ditemukan']));
}

$id_pesanan = $_GET['id_pesanan'];

// Ambil nomor telepon agent
$sql = "SELECT a.phone 
        FROM pesanan p 
        JOIN agent a ON p.id_agent = a.id_agent 
        WHERE p.id_pesanan = ? AND p.id_user = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("ii", $id_pesanan, $_SESSION['id_user']);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if ($data) {
    echo json_encode(['phone' => $data['phone']]);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Data tidak ditemukan']);
}
