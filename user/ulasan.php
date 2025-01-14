<?php
session_start();
include '../service/koneksi.php';

if (!isset($_SESSION['is_login']) || !$_SESSION['is_user']) {
    header('Location: ../Admin/index.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: transaksi.php');
    exit();
}

// Ambil data pesanan dan agent
$sql = "SELECT p.*, a.nama as agent_name, u.rating, u.ulasan 
        FROM pesanan p 
        JOIN agent a ON p.id_agent = a.id_agent 
        LEFT JOIN ulasan u ON p.id_pesanan = u.id_pesanan
        WHERE p.id_pesanan = ? AND p.id_user = ? AND p.status_pengerjaan = 'Selesai'";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("ii", $_GET['id'], $_SESSION['id_user']);
$stmt->execute();
$result = $stmt->get_result();
$pesanan = $result->fetch_assoc();

if (!$pesanan) {
    header('Location: transaksi.php');
    exit();
}

// Handle submit ulasan
if (isset($_POST['submit'])) {
    $rating = $_POST['rating'];
    $ulasan = $_POST['ulasan'];
    $id_pesanan = $_GET['id'];

    // Cek apakah ulasan sudah ada
    if ($pesanan['rating'] !== null) {
        // Update ulasan
        $sql = "UPDATE ulasan SET rating = ?, ulasan = ? WHERE id_pesanan = ?";
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("isi", $rating, $ulasan, $id_pesanan);
    } else {
        // Insert ulasan baru
        $sql = "INSERT INTO ulasan (id_pesanan, rating, ulasan) VALUES (?, ?, ?)";
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("iis", $id_pesanan, $rating, $ulasan);
    }

    if ($stmt->execute()) {
        $_SESSION['success'] = $pesanan['rating'] !== null ? "Ulasan berhasil diperbarui!" : "Terima kasih atas ulasan Anda!";
        header('Location: transaksi.php');
        exit();
    } else {
        $error = "Gagal " . ($pesanan['rating'] !== null ? "memperbarui" : "menyimpan") . " ulasan. Silakan coba lagi.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beri Ulasan - Rakun Games</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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

        .ulasan-section {
            padding: 50px 0;
            flex: 1;
        }

        .ulasan-card {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 1rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .rating {
            font-size: 2rem;
            color: #ddd;
            cursor: pointer;
        }

        .rating .fas {
            margin: 0 5px;
            transition: color 0.3s ease;
        }

        .rating .fas.active {
            color: #ffc107;
        }

        .btn-submit {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.8rem 2rem;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-submit:hover {
            background-color: var(--dark-color);
            border-color: var(--dark-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        textarea {
            resize: none;
            height: 150px !important;
        }
    </style>
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

    <section class="ulasan-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 fw-bold">Berikan Ulasanmu</h2>
                <p class="lead">Kepada <?php echo $pesanan['agent_name']; ?></p>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="ulasan-card">
                        <form method="POST" action="">
                            <div class="text-center mb-4">
                                <div class="rating" id="rating">
                                    <i class="fas fa-star" data-rating="1"></i>
                                    <i class="fas fa-star" data-rating="2"></i>
                                    <i class="fas fa-star" data-rating="3"></i>
                                    <i class="fas fa-star" data-rating="4"></i>
                                    <i class="fas fa-star" data-rating="5"></i>
                                </div>
                                <input type="hidden" name="rating" id="selected-rating"
                                    value="<?php echo $pesanan['rating'] ?? '0'; ?>" required>
                            </div>

                            <div class="mb-4">
                                <label for="ulasan" class="form-label">Ulasan Anda</label>
                                <textarea class="form-control" id="ulasan" name="ulasan"
                                    placeholder="Bagaimana pengalaman Anda dengan agent ini?"
                                    required><?php echo $pesanan['ulasan'] ?? ''; ?></textarea>
                            </div>

                            <div class="text-center">
                                <button type="submit" name="submit" class="btn btn-primary btn-submit">
                                    <i class="fas fa-paper-plane me-2"></i>
                                    <?php echo $pesanan['rating'] !== null ? 'Simpan Perubahan' : 'Kirim Ulasan'; ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ratingStars = document.querySelectorAll('.rating .fas');
            const ratingInput = document.getElementById('selected-rating');

            // Set rating awal jika ada
            const initialRating = ratingInput.value;
            if (initialRating > 0) {
                ratingStars.forEach(s => {
                    if (s.dataset.rating <= initialRating) {
                        s.classList.add('active');
                    }
                });
            }

            ratingStars.forEach(star => {
                star.addEventListener('click', function() {
                    const rating = this.dataset.rating;
                    ratingInput.value = rating;

                    // Reset semua bintang
                    ratingStars.forEach(s => s.classList.remove('active'));

                    // Aktifkan bintang dari kiri ke kanan
                    ratingStars.forEach(s => {
                        if (s.dataset.rating <= rating) {
                            s.classList.add('active');
                        }
                    });
                });
            });
        });
    </script>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
</body>

</html>