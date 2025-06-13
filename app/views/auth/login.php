<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header text-center">
                <h4>Login Mahasiswa</h4>
            </div>
            <div class="card-body">
                <form id="loginForm" onsubmit="return handleLogin(event)">
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

<script>
function showError(message) {
    const errorContainer = document.createElement('div');
    errorContainer.className = 'error-container';
    errorContainer.innerHTML = `
        <div class="error-message login-error">
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

function handleLogin(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    
    fetch('<?= BASEURL; ?>/auth/prosesLogin', {
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
        showError('Terjadi kesalahan saat login. Silakan coba lagi.');
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
.login-error {
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