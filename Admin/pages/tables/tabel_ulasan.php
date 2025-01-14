<?php
session_start();
include '../../../service/koneksi.php';

if (isset($_SESSION["is_login"]) == false) {
    header("location: ../../index.php");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>AdminRakunGames | Tabel Ulasan</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css" />
    <!-- DataTables -->
    <link rel="stylesheet" href="../../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css" />
    <link rel="stylesheet" href="../../plugins/datatables-responsive/css/responsive.bootstrap4.min.css" />
    <link rel="stylesheet" href="../../plugins/datatables-buttons/css/buttons.bootstrap4.min.css" />
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
                        <?php if ($_SESSION["is_admin"]) : ?>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-edit"></i>
                                    <p>
                                        Forms
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="../forms/input_admin.php" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Input Admin</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="../forms/input_user.php" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Input User</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="../forms/input_agent.php" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Input Agent</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="../forms/input_paket.php" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Input Paket</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item menu-open">
                                <a href="#" class="nav-link active">
                                    <i class="nav-icon fas fa-table"></i>
                                    <p>
                                        Tables
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="tabel_admin.php" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Admin</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="tabel_user.php" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>User</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="tabel_agent.php" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Agent</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="tabel_paket.php" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Paket</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="tabel_pesanan.php" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Pesanan</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="tabel_ulasan.php" class="nav-link active">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Ulasan</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        <?php else : ?>
                            <li class="nav-item">
                                <a href="tabel_pesanan.php" class="nav-link">
                                    <i class="nav-icon fas fa-shopping-cart"></i>
                                    <p>Pesanan</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="tabel_ulasan.php" class="nav-link active">
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

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Tabel Ulasan</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../../dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active">Tabel Ulasan</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Data Ulasan</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr class="text-center">
                                                <th>No</th>
                                                <th>User</th>
                                                <?php if ($_SESSION["is_admin"]) : ?>
                                                    <th>Agent</th>
                                                <?php endif; ?>
                                                <th>Paket</th>
                                                <th>Tanggal Selesai</th>
                                                <th>Rating</th>
                                                <th>Ulasan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $nomor = 1;
                                            $sql = "SELECT u.*, p.tanggal_selesai, us.username, a.nama as agent_name, pk.nama_paket 
                                                       FROM ulasan u 
                                                       JOIN pesanan p ON u.id_pesanan = p.id_pesanan 
                                                       JOIN user us ON p.id_user = us.id_user 
                                                       JOIN agent a ON p.id_agent = a.id_agent 
                                                       JOIN paket pk ON p.id_paket = pk.id_paket";

                                            if (!$_SESSION["is_admin"]) {
                                                $sql .= " WHERE p.id_agent = " . $_SESSION["id_agent"];
                                            }

                                            $query = mysqli_query($koneksi, $sql);
                                            while ($row = mysqli_fetch_array($query)) {
                                                echo "
                                                        <tr class='text-center'>
                                                            <td>$nomor</td>
                                                            <td>$row[username]</td>";
                                                if ($_SESSION["is_admin"]) {
                                                    echo "<td>$row[agent_name]</td>";
                                                }
                                                echo "
                                                            <td>$row[nama_paket]</td>
                                                            <td>$row[tanggal_selesai]</td>
                                                            <td>$row[rating]</td>
                                                            <td>$row[ulasan]</td>
                                                        </tr>
                                                    ";
                                                $nomor++;
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

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

    <!-- Scripts -->
    <!-- jQuery -->
    <script src="../../plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables & Plugins -->
    <script src="../../plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="../../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="../../plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="../../plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="../../plugins/jszip/jszip.min.js"></script>
    <script src="../../plugins/pdfmake/pdfmake.min.js"></script>
    <script src="../../plugins/pdfmake/vfs_fonts.js"></script>
    <script src="../../plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="../../plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="../../plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../../dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="../../dist/js/demo.js"></script>
    <script src="../../dist/js/tambahan.js"></script>

    <!-- Page specific script -->
    <script>
        $(function() {
            $("#example1").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        });
    </script>
</body>

</html>