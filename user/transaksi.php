<?php
session_start();
include '../service/koneksi.php';

if (!isset($_SESSION['is_login']) || !$_SESSION['is_user']) {
    header('Location: ../Admin/index.php');
    exit();
}

// Set jumlah item per halaman
$items_per_page = 10;

// Get current page
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $items_per_page;

// Get total items
$total_query = "SELECT COUNT(*) as total FROM pesanan WHERE id_user = ?";
$stmt_total = $koneksi->prepare($total_query);
$stmt_total->bind_param("i", $_SESSION['id_user']);
$stmt_total->execute();
$total_result = $stmt_total->get_result()->fetch_assoc();
$total_items = $total_result['total'];

// Calculate total pages
$total_pages = ceil($total_items / $items_per_page);

// Ambil data pesanan user
$sql = "SELECT p.*, pk.nama_paket, pk.game, a.nama as agent_name, a.phone as agent_phone 
        FROM pesanan p 
        JOIN paket pk ON p.id_paket = pk.id_paket 
        JOIN agent a ON p.id_agent = a.id_agent 
        WHERE p.id_user = ? 
        ORDER BY p.tanggal_dibuat DESC
        LIMIT ? OFFSET ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("iii", $_SESSION['id_user'], $items_per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();

// Handle pembatalan pesanan
if (isset($_POST['batalkan'])) {
    $id_pesanan = $_POST['id_pesanan'];
    $sql_delete = "DELETE FROM pesanan WHERE id_pesanan = ? AND id_user = ? AND status_pengerjaan = 'Menunggu Pembayaran'";
    $stmt_delete = $koneksi->prepare($sql_delete);
    $stmt_delete->bind_param("ii", $id_pesanan, $_SESSION['id_user']);

    if ($stmt_delete->execute()) {
        $_SESSION['success'] = "Pesanan berhasil dibatalkan";
        header('Location: transaksi.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi - Rakun Games</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Afacad+Flux:wght@100..1000&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="home.css">
    <style>
        body {
            background: linear-gradient(180deg, var(--accent-color) 0%, var(--light-color) 100%);
            min-height: 100vh;
            padding-top: 80px;
            display: flex;
            flex-direction: column;
        }

        .transaksi-section {
            padding: 50px 0;
            flex: 1;
        }

        .transaksi-card {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .transaksi-card:hover {
            transform: translateY(-5px);
        }

        .status-icon {
            font-size: 2rem;
            margin-right: 1rem;
        }

        .status-menunggu {
            color: var(--secondary-color);
        }

        .status-progress {
            color: #007bff;
        }

        .status-selesai {
            color: #28a745;
        }

        .btn-action {
            padding: 0.5rem 1rem;
            font-weight: 600;
            transition: all 0.3s;
            width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .game-badge {
            background-color: var(--primary-color);
            color: var(--light-color);
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .total-biaya {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--primary-color);
        }

        .btn-group-vertical {
            width: 100%;
            gap: 0.5rem;
        }

        .pagination .page-link {
            color: var(--primary-color);
            border-color: var(--primary-color);
            background-color: white;
        }

        .pagination .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .pagination .page-link:hover {
            background-color: var(--accent-color);
            border-color: var(--primary-color);
            color: var(--primary-color);
        }
    </style>

    <!-- Tambahkan script Midtrans di bagian head -->
    <script type="text/javascript"
        src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="SB-Mid-client-sez6QyAkJZvz1N2_"></script>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="../assets/rakungameswhite.png" alt="Rakun Games Logo" class="me-2">
                <span class="text-light">Rakun Games</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="store.php">Store</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="transaksi.php">Transaksi</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            Akun
                        </a>
                        <ul class="dropdown-menu">
                            <?php if (isset($_SESSION['is_login']) && $_SESSION['is_user']): ?>
                                <li><a class="dropdown-item" href="akun.php">Profile</a></li>
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            <?php else: ?>
                                <li><a class="dropdown-item" href="../Admin/index.php">Login</a></li>
                                <li><a class="dropdown-item" href="../Admin/register.php">Register</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="transaksi-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 fw-bold">List Transaksi</h2>
            </div>
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="transaksi-card">
                        <div class="d-flex align-items-center">
                            <!-- Icon sesuai status -->
                            <?php if ($row['status_pengerjaan'] == 'Menunggu Pembayaran'): ?>
                                <i class="fas fa-clock status-icon status-menunggu"></i>
                            <?php elseif ($row['status_pengerjaan'] == 'On-Progress'): ?>
                                <i class="fas fa-spinner fa-spin status-icon status-progress"></i>
                            <?php else: ?>
                                <i class="fas fa-check-circle status-icon status-selesai"></i>
                            <?php endif; ?>

                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="mb-0">
                                        <?php echo $row['nama_paket']; ?>
                                        <small class="text-muted">(<?php echo $row['status_pengerjaan']; ?>)</small>
                                    </h5>
                                </div>
                                <p class="mb-1">Game: <?php echo $row['game']; ?></p>
                                <p class="mb-1">Agent: <?php echo $row['agent_name']; ?></p>
                                <p class="total-biaya mb-0">Total: Rp <?php echo number_format($row['total_biaya'], 0, ',', '.'); ?></p>
                            </div>

                            <div class="ms-3">
                                <?php if ($row['status_pengerjaan'] == 'Menunggu Pembayaran'): ?>
                                    <div class="btn-group-vertical">
                                        <button type="button" class="btn btn-success btn-action"
                                            onclick="bayarPesanan(<?php echo $row['id_pesanan']; ?>, <?php echo $row['total_biaya']; ?>)">
                                            <i class="fas fa-money-bill-wave me-2"></i>Bayar Sekarang
                                        </button>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="id_pesanan" value="<?php echo $row['id_pesanan']; ?>">
                                            <button type="submit" name="batalkan" class="btn btn-danger btn-action"
                                                onclick="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?');">
                                                <i class="fas fa-times me-2"></i>Batalkan
                                            </button>
                                        </form>
                                    </div>
                                <?php elseif ($row['status_pengerjaan'] == 'On-Progress'): ?>
                                    <a href="https://wa.me/<?php echo $row['agent_phone']; ?>" class="btn btn-primary btn-action" target="_blank">
                                        <i class="fab fa-whatsapp me-2"></i>Hubungi Agent
                                    </a>
                                <?php elseif ($row['status_pengerjaan'] == 'Selesai'): ?>
                                    <a href="ulasan.php?id=<?php echo $row['id_pesanan']; ?>" class="btn btn-warning btn-action">
                                        <i class="fas fa-star me-2"></i>Beri Ulasan
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center">
                    <h4>Belum ada transaksi</h4>
                    <a href="store.php" class="btn btn-primary mt-3">Mulai Berbelanja</a>
                </div>
            <?php endif; ?>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($current_page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $current_page - 1; ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $i == $current_page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($current_page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $current_page + 1; ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="text-center">
        <div class="container">
            <a href="https://www.instagram.com/rakun_games.id/" class="social-link" target="_blank" style="text-decoration: none;">
                <i class="fab fa-instagram" style="transform: translateY(2px);"></i> : @rakun_games.id
            </a>
            <p>&copy; 2025 Rakun Games. All rights reserved.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Tambahkan script untuk Midtrans di bagian bawah -->
    <script>
        function bayarPesanan(idPesanan, totalBiaya) {
            console.log('Memulai proses pembayaran...', {
                idPesanan,
                totalBiaya
            });

            fetch('get_midtrans_token.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id_pesanan: idPesanan,
                        total_biaya: totalBiaya
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error('Network response error: ' + text);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Token received:', data);
                    if (!data.token) {
                        throw new Error('Token not received from server');
                    }

                    snap.pay(data.token, {
                        onSuccess: function(result) {
                            // Ambil nomor telepon agent
                            fetch('get_agent_phone.php?id_pesanan=' + idPesanan)
                                .then(response => response.json())
                                .then(data => {
                                    Swal.fire({
                                        title: 'Pembayaran Berhasil!',
                                        text: 'Silahkan hubungi agent untuk memberikan username dan password akun game Anda',
                                        icon: 'success',
                                        confirmButtonText: 'Hubungi Agent',
                                        showCancelButton: true,
                                        cancelButtonText: 'Nanti Saja'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.open('https://wa.me/' + data.phone, '_blank');
                                        }
                                        window.location.href = 'update_status.php?id=' + idPesanan + '&status=success';
                                    });
                                });
                        },
                        onPending: function(result) {
                            alert('Pembayaran pending, silahkan selesaikan pembayaran');
                        },
                        onError: function(result) {
                            alert('Pembayaran gagal');
                        },
                        onClose: function() {
                            alert('Popup pembayaran ditutup');
                        }
                    });
                })
                .catch(error => {
                    console.error('Error details:', error);
                    alert('Terjadi kesalahan saat memproses pembayaran: ' + error.message);
                });
        }
    </script>
</body>

</html>