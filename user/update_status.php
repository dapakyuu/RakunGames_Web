<?php
session_start();
include '../service/koneksi.php';

if (!isset($_SESSION['is_login']) || !$_SESSION['is_user']) {
    header('Location: ../Admin/index.php');
    exit();
}

if (isset($_GET['id']) && isset($_GET['status']) && $_GET['status'] == 'success') {
    $id_pesanan = $_GET['id'];

    // Update status pesanan
    $sql = "UPDATE pesanan SET status_pembayaran = 'PAID', status_pengerjaan = 'On-Progress' 
            WHERE id_pesanan = ? AND id_user = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("ii", $id_pesanan, $_SESSION['id_user']);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Pembayaran berhasil! Pesanan sedang diproses. Silahkan hubungi agent untuk memberikan username dan password akun game Anda.";
    } else {
        $_SESSION['error'] = "Gagal mengupdate status pesanan.";
    }
}

header('Location: transaksi.php');
exit();
