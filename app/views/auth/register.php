<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header text-center">
                <h4>Registrasi Akun Mahasiswa</h4>
            </div>
            <div class="card-body">
                <form action="<?= BASEURL; ?>/auth/prosesRegister" method="post">
                    <div class="mb-3">
                        <label for="nim" class="form-label">NIM</label>
                        <input type="text" class="form-control" id="nim" name="nim" required>
                    </div>
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="nama" name="nama" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirm" class="form-label">Konfirmasi Password</label>
                        <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Register</button>
                     <p class="text-center mt-3">
                        Sudah punya akun? <a href="<?= BASEURL; ?>/auth/login">Login di sini</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>