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
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Input Agent</h3>
                    </div>
                    <!-- form start -->
                    <form action="input_agent.php" method="POST">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username" name="username" placeholder="Enter username" required>
                            </div>
                            <div class="form-group">
                                <label for="nama">Nama Lengkap</label>
                                <input type="text" class="form-control" id="nama" name="nama" placeholder="Enter nama lengkap" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" required>
                            </div>
                            <div class="form-group">
                                <label for="game">Game</label>
                                <select class="form-control select2" id="game" name="game[]" multiple="multiple" style="width: 100%;" required>
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
                                <label for="phone">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter phone number" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
                            </div>
                            <div class="form-group">
                                <label for="retype_password">Retype Password</label>
                                <input type="password" class="form-control" id="retype_password" name="retype_password" placeholder="Retype password" required>
                            </div>
                        </div>
                        <!-- /.card-body -->

                        <div class="card-footer">
                            <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
                <!-- /.card -->
            </div>
        </div>
    </div>
</section>