<div class="text-center">
    <div class="card mx-auto shadow-sm" style="width: 25rem;">
        <div class="card-body p-4">
            <h5 class="card-title text-success"><i class="bi bi-check-circle-fill"></i> Pemesanan Berhasil!</h5>
            <p class="card-text">Silakan tunjukkan nomor antrian ini ke kasir.</p>
            <div class="alert alert-info mt-4">
                <h6 class="text-uppercase">Nomor Antrian Anda</h6>
                <h1 class="display-1 fw-bold"><?= htmlspecialchars($data['order']['nomor_antrian']); ?></h1>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Waktu Pesan:</strong> <?= date('d M Y, H:i:s', strtotime($data['order']['waktu_pesan'])); ?></li>
                <li class="list-group-item"><strong>Estimasi Selesai:</strong> <?= date('H:i:s', strtotime($data['order']['estimasi_selesai'])); ?></li>
                <li class="list-group-item"><strong>Total Bayar:</strong> Rp <?= number_format($data['order']['total_harga'], 0, ',', '.'); ?></li>
            </ul>
             <a href="<?= BASEURL; ?>/order" class="btn btn-primary mt-4">Pesan Lagi</a>
        </div>
    </div>
</div>