<?php
include "../service/koneksi.php";
session_start();

if (isset($_SESSION["is_login"]) && $_SESSION["is_login"] == true) {
    if ($_SESSION["is_user"]) {
        header("location: ../user/akun.php");
    } else {
        header("location: dashboard.php");
    }
    exit();
}

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $retype_password = $_POST['retype_password'];

    if ($password !== $retype_password) {
        $_SESSION['register_error'] = "Password tidak cocok!";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Cek di tabel admin
    $sql = "SELECT * FROM admin WHERE username=? OR email=?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['register_error'] = "Username atau email sudah terdaftar di sistem!";
        $stmt->close();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Cek di tabel agent
    $sql = "SELECT * FROM agent WHERE username=? OR email=?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['register_error'] = "Username atau email sudah terdaftar di sistem!";
        $stmt->close();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Cek di tabel user
    $sql = "SELECT * FROM user WHERE username=? OR email=?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['register_error'] = "Username atau email sudah terdaftar di sistem!";
        $stmt->close();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Insert user baru
    $sql = "INSERT INTO user (username, email, phone, pass) VALUES (?, ?, ?, ?)";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("ssss", $username, $email, $phone, $password);

    if ($stmt->execute()) {
        $_SESSION['register_success'] = "Registrasi berhasil! Silahkan login.";
        $stmt->close();
        header("Location: index.php");
        exit();
    } else {
        $_SESSION['register_error'] = "Gagal mendaftar!";
        $stmt->close();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rakun Games | Registration</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>

<body class="hold-transition register-page">
    <!-- Home Button -->
    <a href="../user/index.php" class="btn btn-link text-dark position-fixed" style="top: 20px; left: 20px; z-index: 1000;">
        <i class="fas fa-home fa-2x"></i>
    </a>

    <style>
        body {
            padding-top: 80px;
        }

        .navbar {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .nav-link:hover {
            color: #deb887 !important;
        }

        /* Font */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        body,
        .navbar {
            font-family: 'Poppins', sans-serif;
        }
    </style>

    <div class="register-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="index.php" class="h2"><b>User</b> Registration</a>
            </div>
            <div class="card-body">
                <p class="login-box-msg">Register a new user account</p>

                <?php if (isset($_SESSION['register_error'])): ?>
                    <div class="alert alert-danger">
                        <?php
                        echo $_SESSION['register_error'];
                        unset($_SESSION['register_error']);
                        ?>
                    </div>
                <?php endif; ?>

                <form action="register.php" method="post">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Username" name="username" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="email" class="form-control" placeholder="Email" name="email" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Phone Number" name="phone" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-phone"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" placeholder="Password" name="password" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" placeholder="Retype password" name="retype_password" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <button type="submit" class="btn btn-primary btn-block" name="register" style="margin-bottom: 10px;">Register</button>
                        </div>
                    </div>
                </form>

                <a href="index.php" class="text-center">I already have an account</a>
            </div>
            <!-- /.form-box -->
        </div><!-- /.card -->
    </div>
    <!-- /.register-box -->

    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="plugins/sweetalert2/sweetalert2.min.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.min.js"></script>

    <?php if (isset($_SESSION['register_error'])): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '<?php echo $_SESSION['register_error']; ?>',
            });
        </script>
    <?php unset($_SESSION['register_error']);
    endif; ?>
</body>

</html>