<?php
session_start();
include '../service/koneksi.php';

// Ambil nama game dari parameter GET
$game = isset($_GET['game']) ? $_GET['game'] : '';

// Format nama game untuk tampilan
$game_display = '';
switch ($game) {
    case 'arknights':
        $game_display = 'Arknights';
        break;
    case 'genshinimpact':
        $game_display = 'Genshin Impact';
        break;
    case 'honkaiimpact':
        $game_display = 'Honkai Impact';
        break;
    case 'honkaistarrail':
        $game_display = 'Honkai Star Rail';
        break;
    case 'mobilelegends':
        $game_display = 'Mobile Legends';
        break;
    case 'valorant':
        $game_display = 'Valorant';
        break;
    case 'zenlesszonezero':
        $game_display = 'Zenless Zone Zero';
        break;
    default:
        header('Location: store.php');
        exit();
}

// Query untuk mengambil paket sesuai game
$sql = "SELECT * FROM paket WHERE game = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("s", $game_display);
$stmt->execute();
$result = $stmt->get_result();

// Debug
if ($result->num_rows === 0) {
    echo "<!-- Debug: Game = $game_display -->";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paket <?php echo $game_display; ?> - Rakun Games</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- AOS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
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

        .paket-section {
            padding: 50px 0;
            flex: 1;
        }

        .paket-card {
            height: 100%;
            transition: transform 0.3s;
            background-color: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 15px;
            overflow: hidden;
        }

        .paket-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .paket-card img {
            width: 100%;
            aspect-ratio: 16/9;
            object-fit: cover;
            object-position: center;
        }

        .card-body {
            padding: 1.5rem;
        }

        .price {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary-color);
        }

        .satuan {
            font-size: 0.9rem;
            color: var(--secondary-color);
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
                        <a class="nav-link active" href="store.php">Store</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="transaksi.php">Transaksi</a>
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

    <!-- Paket Section -->
    <section class="paket-section">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="display-4 fw-bold">Paket <?php echo $game_display; ?></h2>
                <p class="lead">Pilih paket yang sesuai dengan kebutuhan Anda</p>
            </div>
            <div class="row g-4">
                <?php
                if ($result->num_rows > 0) {
                    $delay = 100;
                    while ($row = $result->fetch_assoc()):
                ?>
                        <div class="col-md-4" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                            <div class="card paket-card">
                                <img src="../assets/paket/<?php echo $row['gambar']; ?>"
                                    class="card-img-top"
                                    alt="<?php echo $row['nama_paket']; ?>">
                                <div class="card-body d-flex flex-column">
                                    <h4 class="card-title fw-bold"><?php echo $row['nama_paket']; ?></h4>
                                    <p class="card-text flex-grow-1"><?php echo $row['deskripsi']; ?></p>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <span class="price">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></span>
                                            <span class="satuan">/ <?php echo $row['satuan']; ?></span>
                                        </div>
                                        <?php if (isset($_SESSION['is_login']) && $_SESSION['is_user']): ?>
                                            <a href="pesan.php?id=<?php echo $row['id_paket']; ?>"
                                                class="btn btn-primary">Pesan Sekarang</a>
                                        <?php else: ?>
                                            <a href="../Admin/index.php"
                                                class="btn btn-primary">Login untuk Memesan</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                <?php
                        $delay += 100;
                    endwhile;
                } else {
                    echo '<div class="col-12 text-center">
                            <h3>Tidak ada paket tersedia untuk game ini.</h3>
                          </div>';
                }
                ?>
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
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
</body>

</html>