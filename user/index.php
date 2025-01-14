<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rakun Games - Jasa Rawat Akun Game Online</title>

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
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="store.php">Store</a>
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
                                <li><a class="dropdown-item" href="profile.php">Profile</a></li>
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

    <!-- Hero Section -->
    <section class="hero-section" id="home">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6" data-aos="fade-right">
                    <h1 class="display-4 fw-bold">Selamat Datang di Rakun Games!</h1>
                    <p class="lead">Solusi terpercaya untuk perawatan dan peningkatan akun game online Anda.</p>
                    <a href="store.php" class="btn btn-light btn-lg">Mulai Sekarang</a>
                </div>
                <div class="col-md-6" data-aos="fade-left">
                    <div class="hero-image-container" data-aos="fade-left">
                        <img src="../assets/console.png" alt="Hero Image" class=" ms-5 img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about-section" id="about">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="display-4 fw-bold">Tentang Kami</h2>
                <p class="lead">Rakun Games adalah penyedia jasa profesional untuk perawatan akun game online Anda.</p>
            </div>
            <div class="row">
                <div class="col-md-8 mx-auto text-center fs-5" data-aos="fade-up">
                    <p>Kami hadir untuk membantu para gamers dalam merawat dan meningkatkan performa akun game mereka.
                        Dengan tim yang berpengalaman dan profesional, kami menjamin keamanan dan kepuasan pelanggan
                        dalam setiap layanan yang kami berikan.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services-section bg-light" id="services">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="display-4 fw-bold">Layanan Kami</h2>
                <p class="lead">Berbagai layanan profesional untuk akun game Anda</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card service-card h-100 text-center">
                        <div class="card-body">
                            <i class="fas fa-shield-alt fa-3x mb-3"></i>
                            <h3>Daily Account Maintenance</h3>
                            <p>Perawatan rutin untuk menjaga performa akun game Anda</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="card service-card h-100 text-center">
                        <div class="card-body">
                            <i class="fas fa-trophy fa-3x mb-3"></i>
                            <h3>Rank Up Partner</h3>
                            <p>Bantuan profesional untuk meningkatkan peringkat akun Anda</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="card service-card h-100 text-center">
                        <div class="card-body">
                            <i class="fas fa-lock fa-3x mb-3"></i>
                            <h3>Jaminan Keamanan Hack Back</h3>
                            <p>Perlindungan maksimal untuk keamanan akun Anda</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Games Section -->
    <section class="games-section" id="games">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="display-4 fw-bold">Game Yang Kami Layani</h2>
                <p class="lead">Berbagai game populer yang menjadi spesialisasi kami</p>
            </div>
            <div class="row g-4">
                <?php
                $games = [
                    'Arknights',
                    'Genshin Impact',
                    'Honkai Impact',
                    'Honkai Star Rail',
                    'Mobile Legends',
                    'Valorant',
                    'Zenless Zone Zero'
                ];

                foreach ($games as $index => $game):
                    $delay = ($index + 1) * 100;
                ?>
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                        <div class="card game-card">
                            <img src="../assets/<?php echo strtolower(str_replace(' ', '', $game)); ?>.png"
                                class="card-img-top" alt="<?php echo $game; ?>">
                            <div class="card-body text-center">
                                <h4 class="card-title fw-bold"><?php echo $game; ?></h4>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Staff Section -->
    <section class="staff-section bg-light" id="staff">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="display-4 fw-bold">Our Staff</h2>
            </div>
            <div class="row justify-content-center g-4">
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card staff-card text-center">
                        <div class="card-body">
                            <img src="../assets/daffa.jpg" alt="Staff 1" class="mb-3">
                            <h4>Daffa Al-Fathir Ismail</h4>
                            <p>Mahasiswa Teknik Komputer Universitas Pendidikan Indonesia</p>
                            <a href="https://instagram.com/daffa_alfathir_" class="social-link">
                                <i class="fab fa-instagram"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="card staff-card text-center">
                        <div class="card-body">
                            <img src="../assets/dina.jpg" alt="Staff 2" class="mb-3">
                            <h4>Dina Hanifah</h4>
                            <p>Mahasiswa Teknik Komputer Universitas Pendidikan Indonesia</p>
                            <a href="https://instagram.com/daffa_alfathir_" class="social-link">
                                <i class="fab fa-instagram"></i>
                            </a>
                        </div>
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
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
</body>

</html>