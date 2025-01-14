<?php
session_start();
include '../service/koneksi.php';
if (isset($_SESSION["is_login"]) == false) {
  header("location: ../index.php");
}

if (isset($_POST['logout'])) {
  $_SESSION["is_login"] = false;
  $_SESSION["is_logout"] = true;
  header('location: index.php');
  session_unset();
  session_destroy();
  exit();
}

// Ambil total dari setiap tabel
$sql_pesanan = "SELECT COUNT(*) as total FROM pesanan";
$sql_user = "SELECT COUNT(*) as total FROM user";
$sql_agent = "SELECT COUNT(*) as total FROM agent";
$sql_paket = "SELECT COUNT(*) as total FROM paket";

$result_pesanan = $koneksi->query($sql_pesanan);
$result_user = $koneksi->query($sql_user);
$result_agent = $koneksi->query($sql_agent);
$result_paket = $koneksi->query($sql_paket);

$total_pesanan = $result_pesanan->fetch_assoc()['total'];
$total_user = $result_user->fetch_assoc()['total'];
$total_agent = $result_agent->fetch_assoc()['total'];
$total_paket = $result_paket->fetch_assoc()['total'];

// Query untuk notifikasi pesanan agent
if (!$_SESSION["is_admin"] && isset($_SESSION["id_agent"])) {
  $sql_notif = "SELECT p.nama_paket, u.username, ps.tanggal_dibuat 
                FROM pesanan ps 
                JOIN paket p ON ps.id_paket = p.id_paket 
                JOIN user u ON ps.id_user = u.id_user 
                WHERE ps.id_agent = ? AND ps.status_pengerjaan = 'On-Progress'
                ORDER BY ps.tanggal_dibuat DESC";
  $stmt_notif = $koneksi->prepare($sql_notif);
  $stmt_notif->bind_param("i", $_SESSION["id_agent"]);
  $stmt_notif->execute();
  $result_notif = $stmt_notif->get_result();
  $total_notif = $result_notif->num_rows;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>AdminRakunGames | Dashboard</title>

  <!-- Google Font: Source Sans Pro -->
  <link
    rel="stylesheet"
    href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css" />
  <!-- Ionicons -->
  <link
    rel="stylesheet"
    href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" />
  <!-- Tempusdominus Bootstrap 4 -->
  <link
    rel="stylesheet"
    href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css" />
  <!-- iCheck -->
  <link
    rel="stylesheet"
    href="plugins/icheck-bootstrap/icheck-bootstrap.min.css" />
  <!-- JQVMap -->
  <link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css" />
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css" />
  <link rel="stylesheet" href="dist/css/tambahan.css" />
  <!-- overlayScrollbars -->
  <link
    rel="stylesheet"
    href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css" />
  <!-- Daterange picker -->
  <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css" />
  <!-- summernote -->
  <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css" />
  <!-- FullCalendar -->
  <link rel="stylesheet" href="plugins/fullcalendar/main.css">
</head>

<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">
    <!-- Preloader -->
    <div
      class="preloader flex-column justify-content-center align-items-center">
      <img
        class="animation__shake"
        src="../assets/rakungames_white.png"
        alt="AdminLTELogo"
        height="60"
        width="60" />
    </div>

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
          <a href="dashboard.php" class="nav-link">Home</a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
          <a href="https://wa.me/089636733777" class="nav-link" target="_blank">Contact</a>
        </li>
      </ul>

      <!-- Right navbar links -->
      <ul class="navbar-nav ml-auto">
        <?php if (!$_SESSION["is_admin"]): ?>
          <!-- Notifications Dropdown Menu -->
          <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
              <i class="far fa-bell"></i>
              <?php if (isset($total_notif) && $total_notif > 0): ?>
                <span class="badge badge-warning navbar-badge"><?php echo $total_notif; ?></span>
              <?php endif; ?>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
              <?php if (isset($total_notif) && $total_notif > 0): ?>
                <span class="dropdown-item dropdown-header"><?php echo $total_notif; ?> Notifications</span>
                <div class="dropdown-divider"></div>

                <?php while ($notif = $result_notif->fetch_assoc()): ?>
                  <a href="pages/tables/tabel_pesanan.php" class="dropdown-item">
                    <i class="fas fa-file mr-2"></i> <?php echo $notif['nama_paket']; ?>
                    <p class="text-sm mb-0">User: <?php echo $notif['username']; ?></p>
                    <span class="float-right text-muted text-sm">
                      <?php echo date('d/m/Y', strtotime($notif['tanggal_dibuat'])); ?>
                    </span>
                  </a>
                  <div class="dropdown-divider"></div>
                <?php endwhile; ?>
              <?php else: ?>
                <span class="dropdown-item dropdown-header">No notifications</span>
              <?php endif; ?>
            </div>
          </li>
        <?php endif; ?>
      </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <!-- Brand Logo -->
      <a href="dashboard.php" class="brand-link">
        <img
          src="../assets/rakungames_white.png"
          alt="RakunGames Logo"
          class="brand-image img-circle elevation-3"
          style="opacity: 0.8" />
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
              <img
                src="dist/img/gigachad.jpeg"
                class="img-circle elevation-2"
                alt="User Image" />
            </div>
            <div class="info w-100">
              <button
                class="btn btn-link-white p-0 d-block w-100 text-left"
                type="button"
                data-toggle="collapse"
                data-target="#userMenu"
                aria-expanded="false"
                aria-controls="userMenu"
                id="userMenuToggle">
                <?php echo $_SESSION["username"]; ?>
                <i id="dropdownIcon" class="fas fa-chevron-left float-right mt-1"></i>
              </button>
            </div>
          </div>
          <div class="collapse mt-2" id="userMenu">
            <ul class="list-group list-group-flush">
              <li class="list-group-item p-0">
                <form action="dashboard.php" method="POST" style="margin: 5px; padding-left: 5px;">
                  <button type="submit" class="btn btn-link w-100 h-100 text-left"
                    style="color: inherit; text-decoration: none; display: block;"
                    id="logout" name="logout">
                    <i class="fas fa-sign-out-alt mr-2"></i>Sign Out
                  </button>
                </form>
              </li>
            </ul>
          </div>
        </div>

        <!-- SidebarMenu -->
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-item">
              <a href="dashboard.php" class="nav-link active">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
              </a>
            </li>

            <?php if ($_SESSION["is_admin"]): ?>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-edit"></i>
                  <p>
                    Input
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="pages/forms/input_admin.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Admin</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="pages/forms/input_user.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>User</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="pages/forms/input_agent.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Agent</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="pages/forms/input_paket.php" class="nav-link">
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
                    <a href="pages/tables/tabel_admin.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Admin</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="pages/tables/tabel_user.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>User</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="pages/tables/tabel_agent.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Agent</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="pages/tables/tabel_paket.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Paket</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="pages/tables/tabel_pesanan.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Pesanan</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="pages/tables/tabel_ulasan.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Ulasan</p>
                    </a>
                  </li>
                </ul>
              </li>
            <?php else: ?>
              <li class="nav-item">
                <a href="pages/tables/tabel_pesanan.php" class="nav-link">
                  <i class="nav-icon fas fa-shopping-cart"></i>
                  <p>Pesanan</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="pages/tables/tabel_ulasan.php" class="nav-link">
                  <i class="nav-icon fas fa-star"></i>
                  <p>Ulasan</p>
                </a>
              </li>
            <?php endif; ?>
          </ul>
        </nav>
        <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">Dashboard</h1>
            </div>
            <!-- /.col -->
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
              </ol>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
      </div>
      <!-- /.content-header -->

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <!-- Small boxes (Stat box) -->
          <div class="row">
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-info">
                <div class="inner">
                  <h3><?php echo $total_pesanan; ?></h3>
                  <p>Total Pesanan</p>
                </div>
                <div class="icon">
                  <i class="ion ion-bag"></i>
                </div>
              </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-success">
                <div class="inner">
                  <h3><?php echo $total_user; ?></h3>
                  <p>User Registrations</p>
                </div>
                <div class="icon">
                  <i class="ion ion-person-add"></i>
                </div>
              </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-warning">
                <div class="inner">
                  <h3><?php echo $total_agent; ?></h3>
                  <p>Total Agent</p>
                </div>
                <div class="icon">
                  <i class="ion ion-person"></i>
                </div>
              </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-danger">
                <div class="inner">
                  <h3><?php echo $total_paket; ?></h3>
                  <p>Total Paket</p>
                </div>
                <div class="icon">
                  <i class="ion ion-pie-graph"></i>
                </div>
              </div>
            </div>
            <!-- ./col -->
          </div>
          <!-- /.row -->

          <!-- Main row -->
          <div class="row">
            <!-- Calendar col -->
            <section class="col-lg-12 connectedSortable">
              <!-- Calendar -->
              <div class="card bg-gradient-success">
                <div class="card-header border-0">
                  <h3 class="card-title">
                    <i class="far fa-calendar-alt"></i>
                    Calendar
                  </h3>
                  <div class="card-tools">
                    <button type="button" class="btn btn-success btn-sm" data-card-widget="collapse">
                      <i class="fas fa-minus"></i>
                    </button>
                  </div>
                </div>
                <div class="card-body pt-0">
                  <div id="calendar" style="width: 100%"></div>
                </div>
              </div>
            </section>
          </div>
        </div>
      </section>
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

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
  </div>
  <!-- ./wrapper -->

  <!-- jQuery -->
  <script src="plugins/jquery/jquery.min.js"></script>
  <!-- jQuery UI 1.11.4 -->
  <script src="plugins/jquery-ui/jquery-ui.min.js"></script>
  <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
  <script>
    $.widget.bridge("uibutton", $.ui.button);
  </script>
  <!-- Bootstrap 4 -->
  <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- ChartJS -->
  <script src="plugins/chart.js/Chart.min.js"></script>
  <!-- Sparkline -->
  <script src="plugins/sparklines/sparkline.js"></script>
  <!-- JQVMap -->
  <script src="plugins/jqvmap/jquery.vmap.min.js"></script>
  <script src="plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
  <!-- jQuery Knob Chart -->
  <script src="plugins/jquery-knob/jquery.knob.min.js"></script>
  <!-- daterangepicker -->
  <script src="plugins/moment/moment.min.js"></script>
  <script src="plugins/daterangepicker/daterangepicker.js"></script>
  <!-- Tempusdominus Bootstrap 4 -->
  <script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
  <!-- Summernote -->
  <script src="plugins/summernote/summernote-bs4.min.js"></script>
  <!-- overlayScrollbars -->
  <script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
  <!-- AdminLTE App -->
  <script src="dist/js/adminlte.js"></script>
  <!-- AdminLTE for demo purposes -->
  <script src="dist/js/demo.js"></script>
  <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
  <script src="dist/js/pages/dashboard.js"></script>
  <script src="dist/js/tambahan.js"></script>

  <!-- FullCalendar -->
  <script src="plugins/moment/moment.min.js"></script>
  <script src="plugins/fullcalendar/main.js"></script>
  <link rel="stylesheet" href="plugins/fullcalendar/main.css">

  <script>
    $(function() {
      // Inisialisasi Calendar
      var Calendar = FullCalendar.Calendar;
      var calendarEl = document.getElementById('calendar');

      var calendar = new Calendar(calendarEl, {
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        themeSystem: 'bootstrap',
        events: [
          // Di sini bisa ditambahkan events dari database
          // Format: { title: 'Event', start: '2024-03-12', end: '2024-03-13' }
        ],
        editable: true,
        droppable: true,
        dateClick: function(info) {
          // Handler ketika tanggal diklik
          alert('Clicked on: ' + info.dateStr);
        }
      });

      calendar.render();
    });
  </script>
</body>

</html>