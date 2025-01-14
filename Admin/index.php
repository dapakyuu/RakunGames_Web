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

if (isset($_POST['login'])) {
  $username = $_POST['username'];
  $password = $_POST['password'];
  $email = $_POST['username'];

  // Cek di tabel admin
  $sql = "SELECT * FROM admin WHERE (username=? OR email=?) AND pass=?";
  $stmt = $koneksi->prepare($sql);
  $stmt->bind_param("sss", $username, $email, $password);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    $_SESSION["username"] = $data["username"];
    $_SESSION["id_admin"] = $data["id_admin"];
    $_SESSION["is_login"] = true;
    $_SESSION["is_admin"] = true;
    $_SESSION["is_user"] = false;
    $_SESSION["first_login"] = true;
    $_SESSION["is_logout"] = false;

    $stmt->close();
    $koneksi->close();

    header("location: dashboard.php");
    exit();
  }

  // Cek di tabel agent
  $sql = "SELECT * FROM agent WHERE (username=? OR email=?) AND pass=?";
  $stmt = $koneksi->prepare($sql);
  $stmt->bind_param("sss", $username, $email, $password);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    $_SESSION["username"] = $data["username"];
    $_SESSION["id_agent"] = $data["id_agent"];
    $_SESSION["is_login"] = true;
    $_SESSION["is_admin"] = false;
    $_SESSION["is_user"] = false;
    $_SESSION["first_login"] = true;
    $_SESSION["is_logout"] = false;

    $stmt->close();
    $koneksi->close();

    header("location: dashboard.php");
    exit();
  }

  // Cek di tabel user
  $sql = "SELECT * FROM user WHERE (username=? OR email=?) AND pass=?";
  $stmt = $koneksi->prepare($sql);
  $stmt->bind_param("sss", $username, $email, $password);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    $_SESSION["username"] = $data["username"];
    $_SESSION["id_user"] = $data["id_user"];
    $_SESSION["is_login"] = true;
    $_SESSION["is_admin"] = false;
    $_SESSION["is_user"] = true;
    $_SESSION["first_login"] = true;
    $_SESSION["is_logout"] = false;

    $stmt->close();
    $koneksi->close();

    header("location: ../user/akun.php");
    exit();
  }

  // Jika tidak ditemukan di semua tabel
  $stmt->close();
  $koneksi->close();

  $_SESSION['loginfailed'] = true;
  header("Location: " . $_SERVER['PHP_SELF']);
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>AdminEsportHub | Login</title>

  <!-- Google Font: Source Sans Pro -->
  <link
    rel="stylesheet"
    href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
  <!-- Font Awesome -->
  <link
    rel="stylesheet"
    href="plugins/fontawesome-free/css/all.min.css" />
  <!-- icheck bootstrap -->
  <link
    rel="stylesheet"
    href="plugins/icheck-bootstrap/icheck-bootstrap.min.css" />
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css" />
  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
</head>

<body class="hold-transition login-page">
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

  <div class="login-box">
    <!-- /.login-logo -->
    <div class="card card-outline card-primary">
      <div class="card-header text-center">
        <a href="index.php" class="h2"><b>Rakun Games</b> Login</a>
      </div>
      <div class="card-body">
        <p class="login-box-msg">Sign in to start your session</p>

        <form action="index.php" method="post">
          <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Username or Email" name="username" required />
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-envelope"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input
              type="password"
              class="form-control"
              placeholder="Password"
              name="password"
              required />
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <!-- /.col -->
            <div class="col">
              <button type="submit" class="btn btn-primary btn-block" name="login">
                Sign In
              </button>
            </div>
            <!-- /.col -->
          </div>
        </form>

        <p class="mb-1 mt-3">
          <a href="register.php">Register new account</a>
        </p>
      </div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->
  </div>
  <!-- /.login-box -->

  <!-- jQuery -->
  <script src="plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- SweetAlert2 -->
  <script src="plugins/sweetalert2/sweetalert2.min.js"></script>
  <!-- AdminLTE App -->
  <script src="dist/js/adminlte.min.js"></script>

  <?php if (isset($_SESSION['register_success'])): ?>
    <script>
      Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '<?php echo $_SESSION['register_success']; ?>',
      });
    </script>
  <?php unset($_SESSION['register_success']);
  endif; ?>

  <?php if (isset($_SESSION['loginfailed'])): ?>
    <script>
      Swal.fire({
        icon: 'error',
        title: 'Login Failed',
        text: 'Username/Email atau Password salah!',
      });
    </script>
  <?php unset($_SESSION['loginfailed']);
  endif; ?>
</body>

</html>