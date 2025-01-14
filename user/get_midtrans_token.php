<?php
session_start();
include '../service/koneksi.php';
// Check if Midtrans library exists
$midtransPath = 'midtrans-php-master/Midtrans.php';
if (!file_exists($midtransPath)) {
    http_response_code(500);
    exit(json_encode(['error' => 'Midtrans library not found']));
}
require_once $midtransPath;

if (!isset($_SESSION['is_login']) || !$_SESSION['is_user']) {
    http_response_code(403);
    exit('Unauthorized');
}

// Terima data dari request
$data = json_decode(file_get_contents('php://input'), true);
// Debug
error_log('Request data: ' . print_r($data, true));

$id_pesanan = $data['id_pesanan'];
$total_biaya = $data['total_biaya'];

// Ambil data pesanan
$sql = "SELECT p.*, u.username, u.email, pk.nama_paket, u.phone 
        FROM pesanan p 
        JOIN user u ON p.id_user = u.id_user 
        JOIN paket pk ON p.id_paket = pk.id_paket 
        WHERE p.id_pesanan = ? AND p.id_user = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("ii", $id_pesanan, $_SESSION['id_user']);
$stmt->execute();
$pesanan = $stmt->get_result()->fetch_assoc();

// Debug
error_log('Pesanan data: ' . print_r($pesanan, true));

if (!$pesanan) {
    http_response_code(404);
    exit(json_encode(['error' => 'Pesanan tidak ditemukan']));
}

// Set your Merchant Server Key
\Midtrans\Config::$serverKey = 'SB-Mid-server-uhA-_jSC4dfXrlfIs7F9_fTs';
// Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
\Midtrans\Config::$isProduction = false;
// Set sanitization on (default)
\Midtrans\Config::$isSanitized = true;
// Set 3DS transaction for credit card to true
\Midtrans\Config::$is3ds = true;

$params = array(
    'transaction_details' => array(
        'order_id' => 'RG-' . $id_pesanan,
        'gross_amount' => $total_biaya,
    ),
    'customer_details' => array(
        'first_name' => $pesanan['username'],
        'email' => $pesanan['email'],
        'phone' => $pesanan['phone'],
    ),
    'item_details' => array(
        array(
            'id' => $pesanan['id_paket'],
            'price' => $total_biaya,
            'quantity' => 1,
            'name' => $pesanan['nama_paket']
        )
    )
);

try {
    // Get Snap Payment Page URL
    $snapToken = \Midtrans\Snap::getSnapToken($params);
    echo json_encode(['token' => $snapToken]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
