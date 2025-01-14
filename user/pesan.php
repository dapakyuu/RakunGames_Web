<?php
session_start();
include '../service/koneksi.php';

if (!isset($_SESSION['is_login']) || !$_SESSION['is_user']) {
    header('Location: ../Admin/index.php');
    exit();
}

// Ambil data paket
$id_paket = isset($_GET['id']) ? $_GET['id'] : '';
$sql_paket = "SELECT * FROM paket WHERE id_paket = ?";
$stmt_paket = $koneksi->prepare($sql_paket);
$stmt_paket->bind_param("i", $id_paket);
$stmt_paket->execute();
$paket = $stmt_paket->get_result()->fetch_assoc();

if (!$paket) {
    header('Location: store.php');
    exit();
}

// Ambil data user
$sql_user = "SELECT * FROM user WHERE id_user = ?";
$stmt_user = $koneksi->prepare($sql_user);
$stmt_user->bind_param("i", $_SESSION['id_user']);
$stmt_user->execute();
$user = $stmt_user->get_result()->fetch_assoc();

// Ambil data agent berdasarkan game yang dipilih
$sql = "SELECT * FROM agent WHERE game LIKE ?";
$stmt = $koneksi->prepare($sql);
$game_param = "%$paket[game]%";
$stmt->bind_param("s", $game_param);
$stmt->execute();
$result = $stmt->get_result();

// Fungsi untuk mendapatkan rekomendasi agent
function getAgentRecommendations($game)
{
    try {
        $curl = curl_init();

        if ($curl === false) {
            error_log("Failed to initialize CURL");
            throw new Exception('Failed to initialize CURL');
        }

        $url = "http://localhost:5000/get_agent_recommendations";

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode(['game' => $game]),
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Accept: application/json",
                "Content-Length: " . strlen(json_encode(['game' => $game]))
            ],
            CURLOPT_VERBOSE => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);

        $response = curl_exec($curl);

        if ($response === false) {
            throw new Exception('CURL Error: ' . curl_error($curl));
        }

        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($httpCode !== 200) {
            throw new Exception('HTTP Error: ' . $httpCode . ' Response: ' . $response);
        }

        curl_close($curl);

        return json_decode($response, true);
    } catch (Exception $e) {
        error_log("Error in getAgentRecommendations: " . $e->getMessage());
        return null;
    }
}

// Pastikan $paket['game'] tidak kosong
$game = isset($paket['game']) ? $paket['game'] : '';

// Dapatkan rekomendasi agent
$recommended_agents = getAgentRecommendations($game);

// Handle form submission
if (isset($_POST['submit'])) {
    $id_user = $_SESSION['id_user'];
    $id_agent = $_POST['id_agent'];
    $jumlah_pesanan = $_POST['jumlah_pesanan'];
    $total_biaya = $paket['harga'] * $jumlah_pesanan;
    $tanggal_dibuat = date('j F Y');
    $status_pengerjaan = "Menunggu Pembayaran";
    $status_pembayaran = "UNPAID";

    $sql_insert = "INSERT INTO pesanan (id_user, id_agent, id_paket, total_biaya, status_pembayaran, 
                   status_pengerjaan, tanggal_dibuat, jumlah_pesanan) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert = $koneksi->prepare($sql_insert);
    $stmt_insert->bind_param(
        "iiidsssi",
        $id_user,
        $id_agent,
        $id_paket,
        $total_biaya,
        $status_pembayaran,
        $status_pengerjaan,
        $tanggal_dibuat,
        $jumlah_pesanan
    );

    if ($stmt_insert->execute()) {
        $_SESSION['pesan_sukses'] = "Pesanan berhasil dibuat!";
        header('Location: transaksi.php');
        exit();
    } else {
        $error = "Terjadi kesalahan saat membuat pesanan";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pemesanan - Rakun Games</title>

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

        .paket-section {
            padding: 50px 0;
            flex: 1;
        }

        .detail-section {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .detail-section:hover {
            transform: translateY(-5px);
        }

        .form-control,
        .form-select {
            border: 1px solid rgba(0, 0, 0, 0.1);
            background-color: rgba(255, 255, 255, 0.8);
        }

        .form-control:disabled {
            background-color: rgba(255, 255, 255, 0.5);
            cursor: not-allowed;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .total-biaya {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-top: 1rem;
            padding: 1.5rem;
            background-color: rgba(255, 255, 255, 0.7);
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.8rem 2rem;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background-color: var(--dark-color);
            border-color: var(--dark-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .form-label {
            font-weight: 600;
            color: var(--dark-color);
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

    <section class="paket-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 fw-bold">Detail Pemesanan</h2>
                <p class="lead">Mohon periksa detail pesanan Anda</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <!-- Detail Paket -->
                <div class="detail-section">
                    <h3 class="mb-4">Detail Paket</h3>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Paket</label>
                            <input type="text" class="form-control" value="<?php echo $paket['nama_paket']; ?>" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Game</label>
                            <input type="text" class="form-control" value="<?php echo $paket['game']; ?>" disabled>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" rows="3" disabled><?php echo $paket['deskripsi']; ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Harga</label>
                            <input type="text" class="form-control"
                                value="Rp <?php echo number_format($paket['harga'], 0, ',', '.'); ?>" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Satuan</label>
                            <input type="text" class="form-control" value="<?php echo $paket['satuan']; ?>" disabled>
                        </div>
                    </div>
                </div>

                <!-- Detail User -->
                <div class="detail-section">
                    <h3 class="mb-4">Detail User</h3>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" value="<?php echo $user['username']; ?>" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="<?php echo $user['email']; ?>" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" value="<?php echo $user['phone']; ?>" disabled>
                        </div>
                    </div>
                </div>

                <!-- Detail Pesanan -->
                <div class="detail-section">
                    <h3 class="mb-4">Detail Pesanan</h3>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Jumlah Pesanan</label>
                            <input type="number" class="form-control" name="jumlah_pesanan" min="1"
                                required onchange="hitungTotal()">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pilih Agent</label>
                            <select class="form-select" name="id_agent" required>
                                <option value="">Pilih Agent</option>
                                <?php if ($recommended_agents): ?>
                                    <optgroup label="Rekomendasi Agent (Berdasarkan Analisis Sentimen)">
                                        <?php foreach ($recommended_agents as $agent): ?>
                                            <?php
                                            $score = $agent['score'];
                                            $rating = number_format($score, 1);

                                            // Tentukan label sentimen
                                            $sentiment = "";
                                            if ($score <= 0) {
                                                $sentiment = "Belum Ada Ulasan";
                                                $badge_color = "secondary";
                                            } elseif ($score >= 4) {
                                                $sentiment = "Sangat Positif";
                                                $badge_color = "success";
                                            } elseif ($score >= 3) {
                                                $sentiment = "Positif";
                                                $badge_color = "info";
                                            } elseif ($score >= 2) {
                                                $sentiment = "Netral";
                                                $badge_color = "secondary";
                                            } elseif ($score >= 1) {
                                                $sentiment = "Negatif";
                                                $badge_color = "warning";
                                            } else {
                                                $sentiment = "Sangat Negatif";
                                                $badge_color = "danger";
                                            }
                                            ?>
                                            <option value="<?php echo $agent['id_agent']; ?>"
                                                data-score="<?php echo $score; ?>"
                                                data-sentiment="<?php echo $sentiment; ?>">
                                                <?php echo $agent['nama']; ?>
                                                <?php if ($score > 0): ?>
                                                    (Skor: <?php echo $rating; ?>)
                                                <?php else: ?>
                                                    (Belum Ada Skor)
                                                <?php endif; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                <?php else: ?>
                                    <optgroup label="Daftar Agent">
                                        <?php while ($agent = $result->fetch_assoc()): ?>
                                            <option value="<?php echo $agent['id_agent']; ?>">
                                                <?php echo $agent['nama']; ?> (Belum ada skor)
                                            </option>
                                        <?php endwhile; ?>
                                    </optgroup>
                                <?php endif; ?>
                            </select>
                            <small class="text-muted">Skor berdasarkan analisis sentimen dari ulasan dan rating (skala 0-5)</small>
                        </div>
                        <div class="col-12">
                            <div class="total-biaya">
                                Total Biaya: <span id="totalBiaya">Rp 0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" name="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-shopping-cart me-2"></i>Pesan Sekarang
                    </button>
                </div>
            </form>
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
        function hitungTotal() {
            const harga = <?php echo $paket['harga']; ?>;
            const jumlah = document.querySelector('input[name="jumlah_pesanan"]').value;
            const total = harga * jumlah;
            document.getElementById('totalBiaya').textContent =
                'Rp ' + total.toLocaleString('id-ID');
        }

        document.querySelector('select[name="id_agent"]').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const scoreDetails = document.getElementById('agentScoreDetails');

            if (selectedOption.value !== '') {
                const score = selectedOption.dataset.score;
                const sentiment = selectedOption.dataset.sentiment;

                document.getElementById('scoreValue').textContent = (score * 5).toFixed(1) + '/5';
                const sentimentSpan = document.getElementById('sentimentValue');
                sentimentSpan.textContent = sentiment;

                // Set warna badge berdasarkan sentimen
                sentimentSpan.className = 'badge';
                if (sentiment === 'Sangat Positif') {
                    sentimentSpan.classList.add('bg-success');
                } else if (sentiment === 'Positif') {
                    sentimentSpan.classList.add('bg-info');
                } else if (sentiment === 'Netral') {
                    sentimentSpan.classList.add('bg-secondary');
                } else if (sentiment === 'Negatif') {
                    sentimentSpan.classList.add('bg-warning');
                } else {
                    sentimentSpan.classList.add('bg-danger');
                }

                scoreDetails.style.display = 'block';
            } else {
                scoreDetails.style.display = 'none';
            }
        });
    </script>
</body>

</html>