<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-card-list"></i> Pilih Menu Makanan</h2>
</div>

<form id="orderForm" onsubmit="return handleOrder(event)">
    <div class="row">
        <?php foreach ($data['menu'] as $item) : ?>
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <?php 
                // Mapping nama menu ke nama file
                $imageMap = [
                    'Es Teh Manis' => 'esteh',
                    'Nasi Goreng Spesial' => 'nasigoreng',
                    'Mie Ayam Bakso' => 'mieayambakso',
                    'Ayam Geprek Mozzarella' => 'ayamgeprekkeju'
                ];
                
                $imageName = isset($imageMap[$item['nama_menu']]) ? $imageMap[$item['nama_menu']] : strtolower(str_replace(' ', '', $item['nama_menu']));
                $imagePath = '../public/images/' . $imageName . '.png';
                $imageUrl = BASEURL . '/images/' . $imageName . '.png';
                
                // Debug information
                error_log("Menu: " . $item['nama_menu']);
                error_log("Image Name: " . $imageName);
                error_log("Image Path: " . $imagePath);
                error_log("File exists: " . (file_exists($imagePath) ? 'Yes' : 'No'));
                ?>
                <?php if (file_exists($imagePath)) : ?>
                <div class="card-img-container" style="height: 200px; overflow: hidden;">
                    <img src="<?= $imageUrl; ?>" class="card-img-top" alt="<?= htmlspecialchars($item['nama_menu']); ?>" style="width: 100%; height: 100%; object-fit: contain; background-color: #f8f9fa;">
                </div>
                <?php else: ?>
                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                    <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                    <small class="text-muted mt-2"><?= htmlspecialchars($item['nama_menu']); ?></small>
                </div>
                <?php endif; ?>
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

<script>
function showError(message) {
    const errorContainer = document.createElement('div');
    errorContainer.className = 'error-container';
    errorContainer.innerHTML = `
        <div class="error-message order-error">
            <div class="error-icon">⚠️</div>
            <div class="error-content">
                <div class="error-title">${message}</div>
            </div>
        </div>
    `;
    document.body.appendChild(errorContainer);

    setTimeout(() => {
        errorContainer.style.animation = 'slideOut 0.5s ease-out';
        setTimeout(() => errorContainer.remove(), 500);
    }, 5000);
}

function handleOrder(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    
    fetch('<?= BASEURL; ?>/order/checkout', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.status === 'success') {
            window.location.href = data.redirect;
        } else {
            showError(data.message);
        }
    })
    .catch(error => {
        showError('Terjadi kesalahan saat checkout. Silakan coba lagi.');
        console.error('Error:', error);
    });
    
    return false;
}
</script>

<style>
.error-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    max-width: 400px;
    animation: slideIn 0.5s ease-out;
}
.error-message {
    padding: 15px;
    border-radius: 4px;
    margin-bottom: 10px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    display: flex;
    align-items: center;
}
.order-error {
    background-color: #ffebee;
    border-left: 4px solid #f44336;
    color: #c62828;
}
.error-icon {
    margin-right: 10px;
    font-size: 20px;
}
.error-content {
    flex-grow: 1;
}
.error-title {
    font-weight: bold;
    margin-bottom: 5px;
}
@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
@keyframes slideOut {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}
</style>