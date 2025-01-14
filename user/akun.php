<?php
session_start();
include '../service/koneksi.php';

if (!isset($_SESSION['is_login']) || !$_SESSION['is_user']) {
    header('Location: ../Admin/index.php');
    exit();
}

// Ambil data user
$sql = "SELECT * FROM user WHERE id_user = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $_SESSION['id_user']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Handle update profil
if (isset($_POST['update'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $new_password = $_POST['new_password'];

    // Validasi password lama
    if (!empty($new_password)) {
        if ($password !== $user['pass']) {
            $error = "Password lama tidak sesuai!";
        } else {
            $password_to_update = $new_password;
        }
    } else {
        $password_to_update = $user['pass'];
    }

    if (!isset($error)) {
        // Cek username/email duplikat
        $sql_check = "SELECT * FROM user WHERE (username = ? OR email = ?) AND id_user != ?";
        $stmt_check = $koneksi->prepare($sql_check);
        $stmt_check->bind_param("ssi", $username, $email, $_SESSION['id_user']);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            $error = "Username atau email sudah digunakan!";
        } else {
            // Update data
            $sql_update = "UPDATE user SET username = ?, email = ?, phone = ?, pass = ? WHERE id_user = ?";
            $stmt_update = $koneksi->prepare($sql_update);
            $stmt_update->bind_param("ssssi", $username, $email, $phone, $password_to_update, $_SESSION['id_user']);

            if ($stmt_update->execute()) {
                $_SESSION['success'] = "Profil berhasil diperbarui!";
                header('Location: akun.php');
                exit();
            } else {
                $error = "Gagal memperbarui profil!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Rakun Games</title>

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

        .profile-section {
            padding: 50px 0;
            flex: 1;
        }

        .profile-card {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 1rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-update {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.8rem 2rem;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-update:hover {
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
                        <a class="nav-link" href="store.php">Store</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="transaksi.php">Transaksi</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle active" href="#" role="button" data-bs-toggle="dropdown">
                            Akun
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="akun.php">Profile</a></li>
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="profile-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 fw-bold">Profil Saya</h2>
                <p class="lead">Kelola informasi profil Anda</p>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="profile-card">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username"
                                    value="<?php echo $user['username']; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="<?php echo $user['email']; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Nomor Telepon</label>
                                <input type="text" class="form-control" id="phone" name="phone"
                                    value="<?php echo $user['phone']; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password Lama</label>
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Masukkan password lama jika ingin mengubah password">
                            </div>

                            <div class="mb-4">
                                <label for="new_password" class="form-label">Password Baru</label>
                                <input type="password" class="form-control" id="new_password" name="new_password"
                                    placeholder="Kosongkan jika tidak ingin mengubah password">
                            </div>

                            <div class="text-center">
                                <button type="submit" name="update" class="btn btn-primary btn-update">
                                    <i class="fas fa-save me-2"></i>Simpan Perubahan
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
</body>

</html>