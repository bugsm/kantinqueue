<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header text-center">
                <h4>Login Mahasiswa</h4>
            </div>
            <div class="card-body">
                <form action="<?= BASEURL; ?>/auth/prosesLogin" method="post">
                    <div class="mb-3">
                        <label for="nim" class="form-label">NIM</label>
                        <input type="text" class="form-control" id="nim" name="nim" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                    <p class="text-center mt-3">
                        Belum punya akun? <a href="<?= BASEURL; ?>/auth/register">Register di sini</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>