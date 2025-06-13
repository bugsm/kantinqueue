<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['judul']; ?> - KantinQueue</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASEURL; ?>/css/style.css">
    <style>
        body { background-color: #f8f9fa; }
        .navbar { background-color: #ffffff; box-shadow: 0 2px 4px rgba(0,0,0,.1); }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top">
  <div class="container">
    <a class="navbar-brand" href="<?= BASEURL; ?>/home">
        <i class="bi bi-cup-hot-fill me-2"></i>KantinQueue
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <?php if(isset($_SESSION['user_id'])): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= BASEURL; ?>/home">
                    <i class="bi bi-house-door me-1"></i>Home
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= BASEURL; ?>/order">
                    <i class="bi bi-cart me-1"></i>Pesan Makanan
                </a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle me-1"></i><?= $_SESSION['user_nama']; ?>
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="<?= BASEURL; ?>/auth/logout">
                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                </a></li>
              </ul>
            </li>
        <?php else: ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= BASEURL; ?>/auth/login">
                    <i class="bi bi-box-arrow-in-right me-1"></i>Login
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= BASEURL; ?>/auth/register">
                    <i class="bi bi-person-plus me-1"></i>Register
                </a>
            </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<main class="container my-5 animate-fade-in">