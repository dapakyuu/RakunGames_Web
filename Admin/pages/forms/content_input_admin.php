<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Input Admin</h3>
                    </div>

                    <?php if (isset($_SESSION['input_error'])): ?>
                        <div class="alert alert-danger m-3">
                            <?php
                            echo $_SESSION['input_error'];
                            unset($_SESSION['input_error']);
                            ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['input_success'])): ?>
                        <div class="alert alert-success m-3">
                            <?php
                            echo $_SESSION['input_success'];
                            unset($_SESSION['input_success']);
                            ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username" name="username" placeholder="Enter username" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter phone number" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                            </div>
                            <div class="form-group">
                                <label for="retype_password">Retype Password</label>
                                <input type="password" class="form-control" id="retype_password" name="retype_password" placeholder="Retype password" required>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>