<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Input Paket</h3>
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

                    <form method="post" action="" enctype="multipart/form-data">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="game">Game</label>
                                <select class="form-control select2" id="game" name="game" style="width: 100%;" required>
                                    <option value="">Select Game</option>
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
                                <label for="nama_paket">Nama Paket</label>
                                <input type="text" class="form-control" id="nama_paket" name="nama_paket" placeholder="Enter nama paket" required>
                            </div>
                            <div class="form-group">
                                <label for="deskripsi">Deskripsi</label>
                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" placeholder="Enter deskripsi" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="gambar">Gambar</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="gambar" name="gambar" accept="image/*" onchange="previewImage(this)" required>
                                    <label class="custom-file-label" for="gambar">Choose file</label>
                                </div>
                                <div class="mt-2">
                                    <img id="preview" src="" alt="Preview" style="max-width: 200px; display: none;">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="harga">Harga</label>
                                <input type="number" class="form-control" id="harga" name="harga" placeholder="Enter harga" required>
                            </div>
                            <div class="form-group">
                                <label for="satuan">Satuan</label>
                                <input type="text" class="form-control" id="satuan" name="satuan" placeholder="Enter satuan" required>
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

<script src="../../plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
<script>
    $(function() {
        bsCustomFileInput.init();
    });

    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#preview').attr('src', e.target.result).show();
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>