<?php
session_start();
include '../../../service/koneksi.php';

if (isset($_SESSION["is_login"]) == false) {
    header("location: ../../index.php");
}

if (!$_SESSION["is_admin"]) {
    header("location: ../../dashboard.php");
}

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $retype_password = $_POST['retype_password'];

    if ($password !== $retype_password) {
        $_SESSION['input_error'] = "Password tidak cocok!";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Cek username/email duplikat di tabel admin
    $sql = "SELECT * FROM admin WHERE username=? OR email=?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['input_error'] = "Username atau email sudah terdaftar di sistem!";
        $stmt->close();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Cek username/email duplikat di tabel agent
    $sql = "SELECT * FROM agent WHERE username=? OR email=?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['input_error'] = "Username atau email sudah terdaftar di sistem!";
        $stmt->close();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Cek username/email duplikat di tabel user
    $sql = "SELECT * FROM user WHERE username=? OR email=?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['input_error'] = "Username atau email sudah terdaftar di sistem!";
        $stmt->close();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Insert user baru jika username dan email belum digunakan
    $sql = "INSERT INTO user (username, email, pass, phone) VALUES (?, ?, ?, ?)";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("ssss", $username, $email, $password, $phone);

    if ($stmt->execute()) {
        $_SESSION['input_success'] = "User berhasil ditambahkan!";
    } else {
        $_SESSION['input_error'] = "Gagal menambahkan user!";
    }
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>AdminRakunGames | Input User</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css" />
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" />
    <!-- Theme style -->
    <link rel="stylesheet" href="../../dist/css/adminlte.min.css" />
    <link rel="stylesheet" href="../../dist/css/tambahan.css" />
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button" id="dorongmenu">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="../../dashboard.php" class="nav-link">Home</a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="https://wa.me/089636733777" class="nav-link" target="_blank">Contact</a>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="../../dashboard.php" class="brand-link">
                <img src="../../../assets/rakungames_white.png" alt="RakunGames Logo" class="brand-image img-circle elevation-3" style="opacity: 0.8" />
                <span class="brand-text font-weight-light mx-2">
                    <?php echo $_SESSION["is_admin"] ? "AdminRakunGames" : "AgentRakunGames"; ?>
                </span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3">
                    <div class="d-flex align-items-center">
                        <div class="image">
                            <img src="../../dist/img/gigachad.jpeg" class="img-circle elevation-2" alt="User Image" />
                        </div>
                        <div class="info w-100">
                            <button class="btn btn-link-white p-0 d-block w-100 text-left" type="button" data-toggle="collapse"
                                data-target="#userMenu" aria-expanded="false" aria-controls="userMenu" id="userMenuToggle">
                                <?php echo $_SESSION["username"]; ?>
                                <i id="dropdownIcon" class="fas fa-chevron-left float-right mt-1"></i>
                            </button>
                        </div>
                    </div>
                    <div class="collapse mt-2" id="userMenu">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item p-0">
                                <form action="../../dashboard.php" method="POST" style="margin: 5px; padding-left: 5px;">
                                    <button type="submit" class="btn btn-link w-100 h-100 text-left"
                                        style="color: inherit; text-decoration: none; display: block;" id="logout" name="logout">
                                        <i class="fas fa-sign-out-alt mr-2"></i>Sign Out
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <li class="nav-item">
                            <a href="../../dashboard.php" class="nav-link">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                        <?php if ($_SESSION["is_admin"]): ?>
                            <li class="nav-item">
                                <a href="#" class="nav-link active">
                                    <i class="nav-icon fas fa-edit"></i>
                                    <p>
                                        Input
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="input_admin.php" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Admin</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="input_user.php" class="nav-link active">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>User</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="input_agent.php" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Agent</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="input_paket.php" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Paket</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-table"></i>
                                    <p>
                                        Tables
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="../tables/tabel_admin.php" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Admin</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="../tables/tabel_user.php" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>User</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="../tables/tabel_agent.php" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Agent</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="../tables/tabel_paket.php" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Paket</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="../tables/tabel_pesanan.php" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Pesanan</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="../tables/tabel_ulasan.php" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Ulasan</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Input User</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../../dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active">Input User</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <?php include 'content_input_user.php'; ?>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <footer class="main-footer">
            <strong>Copyright &copy; 2014-2021
                <a href="https://adminlte.io">AdminLTE.io</a>.</strong>
            All rights reserved.
            <div class="float-right d-none d-sm-inline-block">
                <b>Version</b> 3.2.0
            </div>
        </footer>
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="../../plugins/jquery/jquery.min.js"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="../../plugins/jquery-ui/jquery-ui.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../../dist/js/adminlte.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="../../dist/js/demo.js"></script>
    <script src="../../dist/js/tambahan.js"></script>
</body>

</html>