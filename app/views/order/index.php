<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-card-list"></i> Pilih Menu Makanan</h2>
</div>

<form action="<?= BASEURL; ?>/order/checkout" method="post">
    <div class="row">
        <?php foreach ($data['menu'] as $item) : ?>
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><?= htmlspecialchars($item['nama_menu']); ?></h5>
                    <p class="card-text text-success fw-bold">Rp <?= number_format($item['harga'], 0, ',', '.'); ?></p>
                    <p class="card-text mt-auto"><small class="text-muted">Stok tersedia: <?= $item['stok']; ?></small></p>
                    <div class="input-group mt-2">
                        <span class="input-group-text">Jumlah</span>
                        <input type="number" name="jumlah[<?= $item['id_menu']; ?>]" class="form-control" value="0" min="0" max="<?= $item['stok']; ?>">
                        <input type="hidden" name="harga[<?= $item['id_menu']; ?>]" value="<?= $item['harga']; ?>">
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="d-grid gap-2 mt-4">
        <button type="submit" class="btn btn-success btn-lg"><i class="bi bi-cart-check-fill"></i> Checkout & Dapatkan Nomor Antrian</button>
    </div>
</form>