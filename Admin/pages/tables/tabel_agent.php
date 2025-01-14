<?php
session_start();
include '../../../service/koneksi.php';

if (isset($_SESSION["is_login"]) == false) {
    header("location: ../../index.php");
}

if (!$_SESSION["is_admin"]) {
    header("location: ../../dashboard.php");
}

// Handle Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $id = intval($_POST['delete_id']);
    $sql = "DELETE FROM agent WHERE id_agent = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['input_success'] = "Data berhasil dihapus!";
    } else {
        $_SESSION['input_error'] = "Gagal menghapus data!";
    }
    $stmt->close();
    header("Location: tabel_agent.php");
    exit();
}

// Handle Edit
if (isset($_POST['submit'])) {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $game = implode(", ", $_POST['game']);
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    // Cek username/email duplikat di tabel admin
    $sql = "SELECT * FROM admin WHERE username=? OR email=?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['input_error'] = "Username atau email sudah terdaftar di sistem!";
        $stmt->close();
        header("Location: tabel_agent.php");
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
        header("Location: tabel_agent.php");
        exit();
    }

    // Cek username/email duplikat di tabel agent (kecuali data sendiri)
    $sql = "SELECT * FROM agent WHERE (username=? OR email=?) AND id_agent != ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("ssi", $username, $email, $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['input_error'] = "Username atau email sudah terdaftar di sistem!";
        $stmt->close();
        header("Location: tabel_agent.php");
        exit();
    }

    // Update data jika tidak ada duplikasi
    if ($password) {
        $query = "UPDATE agent SET username = ?, nama = ?, email = ?, game = ?, phone = ?, pass = ? WHERE id_agent = ?";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("ssssssi", $username, $nama, $email, $game, $phone, $password, $id);
    } else {
        $query = "UPDATE agent SET username = ?, nama = ?, email = ?, game = ?, phone = ? WHERE id_agent = ?";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("sssssi", $username, $nama, $email, $game, $phone, $id);
    }

    if ($stmt->execute()) {
        $_SESSION['input_success'] = "Data berhasil diupdate!";
    } else {
        $_SESSION['input_error'] = "Gagal mengupdate data!";
    }
    $stmt->close();
    header("Location: tabel_agent.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>AdminRakunGames | Tabel Agent</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css" />
    <!-- DataTables -->
    <link rel="stylesheet" href="../../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css" />
    <link rel="stylesheet" href="../../plugins/datatables-responsive/css/responsive.bootstrap4.min.css" />
    <link rel="stylesheet" href="../../plugins/datatables-buttons/css/buttons.bootstrap4.min.css" />
    <!-- Select2 -->
    <link rel="stylesheet" href="../../plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="../../plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
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
                                <p>
                                    Dashboard
                                </p>
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
                                        <a href="tabel_agent.php" class="nav-link active">
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
                                        <a href="tabel_ulasan.php" class="nav-link">
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
                            <h1 class="m-0">Tabel Agent</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../../dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active">Tabel Agent</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <?php if (isset($_SESSION['input_error'])): ?>
                        <div class="alert alert-danger">
                            <?php
                            echo $_SESSION['input_error'];
                            unset($_SESSION['input_error']);
                            ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['input_success'])): ?>
                        <div class="alert alert-success">
                            <?php
                            echo $_SESSION['input_success'];
                            unset($_SESSION['input_success']);
                            ?>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Data Agent</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr class="text-center">
                                                <th>No</th>
                                                <th>Username</th>
                                                <th>Nama</th>
                                                <th>Email</th>
                                                <th>Game</th>
                                                <th>Phone</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $nomor = 1;
                                            $sql = "SELECT * FROM agent";
                                            $query = mysqli_query($koneksi, $sql);
                                            while ($row = mysqli_fetch_array($query)) {
                                                echo "
                                                    <tr class='text-center'>
                                                        <td>$nomor</td>
                                                        <td>$row[username]</td>
                                                        <td>$row[nama]</td>
                                                        <td>$row[email]</td>
                                                        <td>$row[game]</td>
                                                        <td>$row[phone]</td>
                                                        <td class='project-actions text-center'>
                                                            <a class='btn btn-info btn-sm btn-edit' 
                                                                href='#' 
                                                                data-id='$row[id_agent]'
                                                                data-username='$row[username]'
                                                                data-nama='$row[nama]'
                                                                data-email='$row[email]'
                                                                data-game='$row[game]'
                                                                data-phone='$row[phone]'>
                                                                <i class='fas fa-pencil-alt'></i> Edit
                                                            </a>
                                                            <form method='POST' action='tabel_agent.php' style='display:inline;'>
                                                                <input type='hidden' name='delete_id' value='$row[id_agent]'>
                                                                <button type='submit' class='btn btn-danger btn-sm' onclick='return confirm(\"Apakah Anda yakin ingin menghapus agent ini?\");'>
                                                                    <i class='fas fa-trash'></i> Delete
                                                                </button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                ";
                                                $nomor++;
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.container-fluid -->
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
    </div>
    <!-- ./wrapper -->

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Agent</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editForm" action="tabel_agent.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit-id">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" id="edit-username" required>
                        </div>
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" id="edit-nama" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" id="edit-email" required>
                        </div>
                        <div class="form-group">
                            <label>Game</label>
                            <select class="form-control select2" id="edit-game" name="game[]" multiple="multiple" style="width: 100%;" required>
                                <option value="Arknights">Arknights</option>
                                <option value="Genshin Impact">Genshin Impact</option>
                                <option value="Honkai Star Rail">Honkai Star Rail</option>
                                <option value="Honkai Impact">Honkai Impact</option>
                                <option value="Mobile Legends">Mobile Legends</option>
                                <option value="Valorant">Valorant</option>
                                <option value="Zenless Zone Zero">Zenless Zone Zero</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" name="phone" class="form-control" id="edit-phone" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" id="edit-password" placeholder="Kosongkan jika tidak ingin mengubah password">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="submit">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
    <!-- Select2 -->
    <script src="../../plugins/select2/js/select2.full.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../../dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="../../dist/js/demo.js"></script>
    <script src="../../dist/js/tambahan.js"></script>

    <!-- Page specific script -->
    <script>
        $(function() {
            //Initialize Select2 Elements
            $('.select2').select2({
                theme: 'bootstrap4'
            });

            $("#example1").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        });

        // Event listener untuk tombol Edit
        $(document).on('click', '.btn-edit', function(e) {
            e.preventDefault();
            // Ambil data dari atribut data-*
            const id = $(this).data('id');
            const username = $(this).data('username');
            const nama = $(this).data('nama');
            const email = $(this).data('email');
            const game = $(this).data('game').split(", ");
            const phone = $(this).data('phone');

            // Isi form modal dengan data
            $('#edit-id').val(id);
            $('#edit-username').val(username);
            $('#edit-nama').val(nama);
            $('#edit-email').val(email);
            $('#edit-game').val(game).trigger('change');
            $('#edit-phone').val(phone);
            $('#edit-password').val(''); // Reset password field

            // Tampilkan modal
            $('#editModal').modal('show');
        });
    </script>
</body>

</html>