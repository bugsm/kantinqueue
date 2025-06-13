<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header text-center">
                <h4>Registrasi Akun Mahasiswa</h4>
            </div>
            <div class="card-body">
                <form id="registerForm" onsubmit="return handleRegister(event)">
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
                        <label for="konfirmasi_password" class="form-label">Konfirmasi Password</label>
                        <input type="password" class="form-control" id="konfirmasi_password" name="konfirmasi_password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Register</button>
                    <p class="text-center mt-3">
                        Sudah punya akun? <a href="<?= BASEURL; ?>/auth">Login di sini</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function showError(message) {
    const errorContainer = document.createElement('div');
    errorContainer.className = 'error-container';
    errorContainer.innerHTML = `
        <div class="error-message register-error">
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

function handleRegister(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    
    fetch('<?= BASEURL; ?>/auth/prosesRegister', {
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
        showError('Terjadi kesalahan saat registrasi. Silakan coba lagi.');
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
.register-error {
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