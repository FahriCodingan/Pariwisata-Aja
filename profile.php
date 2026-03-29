<?php
session_start();
include "config/koneksi.php";

// Cek login
if(!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

// Ambil data user dari database
$id = $_SESSION['id_user'];

$query = mysqli_query($conn, "SELECT * FROM users WHERE id_user = $id");
$user = mysqli_fetch_assoc($query);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Profil Saya – Restoran Bali</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap"
        rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
    :root {
        --orange: #F47B20;
        --orange-light: #FFA040;
        --yellow: #FFD166;
        --cream: #FFF8F0;
        --brown: #3B1F0A;
        --brown-mid: #7A3B1E;
        --text: #2C1A0E;
        --muted: #9B7B6A;
        --border: rgba(244, 123, 32, 0.2);
    }

    body {
        font-family: 'Inter', sans-serif;
        background: var(--cream);
    }

    /* Header */
    header {
        background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
    }

    .profile-icon-header {
        width: 50px;
        height: 50px;
        background: #fff;
        border-radius: 50%;
        font-size: 1.5rem;
        color: #ff6b35;
    }

    .site-title h1 {
        color: #fff;
        font-size: 1.8rem;
        font-weight: 600;
        margin: 0;
    }

    .site-title p {
        color: #fff9f5;
        font-size: .9rem;
        margin: 0;
    }

    /* Navbar */
    nav {
        background: #fff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .05);
    }

    .nav-container a {
        color: #555;
        text-decoration: none;
        font-weight: 500;
        padding: 1.2rem 2.5rem;
        border-bottom: 3px solid transparent;
        transition: all .3s;
        display: inline-block;
    }

    .nav-container a:hover {
        color: #ff6b35;
        background: #fff5f0;
        border-bottom-color: #ff6b35;
    }

    /* Card profil */
    .profile-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 8px 40px rgba(244, 123, 32, .12), 0 2px 8px rgba(0, 0, 0, .06);
        animation: slideUp .5s cubic-bezier(.25, .8, .25, 1) both;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(24px)
        }

        to {
            opacity: 1;
            transform: translateY(0)
        }
    }

    .profile-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 44px;
        right: 44px;
        height: 3px;
        background: linear-gradient(90deg, var(--orange), var(--yellow));
        border-radius: 0 0 4px 4px;
    }

    /* Avatar */
    .avatar-wrap {
        width: 90px;
        height: 90px;
        background: linear-gradient(135deg, var(--orange), var(--orange-light));
        border-radius: 50%;
        font-size: 2.5rem;
        box-shadow: 0 4px 16px rgba(244, 123, 32, .35);
    }

    /* Input */
    .form-label {
        font-size: .82rem;
        font-weight: 500;
        color: var(--brown-mid);
        letter-spacing: .3px;
    }

    .input-group-text {
        background: var(--cream);
        border: 1.5px solid var(--border);
        border-right: none;
        border-radius: 10px 0 0 10px;
        color: var(--muted);
    }

    .form-control {
        background: var(--cream);
        border: 1.5px solid var(--border);
        border-left: none;
        border-radius: 0 10px 10px 0;
        font-size: .9rem;
        color: var(--text);
    }

    .form-control:disabled {
        background: var(--cream);
        opacity: 1;
    }

    /* Tombol */
    .btn-main {
        background: linear-gradient(135deg, var(--orange), var(--orange-light));
        color: #fff;
        border: none;
        border-radius: 10px;
        font-size: .95rem;
        font-weight: 500;
        box-shadow: 0 4px 16px rgba(244, 123, 32, .35);
        transition: transform .15s, box-shadow .15s;
    }

    .btn-main:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(244, 123, 32, .45);
        color: #fff;
    }

    .btn-logout {
        background: #fff;
        color: #dc3545;
        border: 1.5px solid #dc3545;
        border-radius: 10px;
        font-size: .9rem;
        font-weight: 500;
        transition: .3s;
    }

    .btn-logout:hover {
        background: #dc3545;
        color: #fff;
    }

    /* Footer */
    .site-footer {
        font-size: .78rem;
        color: var(--muted);
        border-top: 1px solid var(--border);
    }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">

    <!-- Header
    <header class="py-4">
        <div class="container-fluid px-4">
            <div class="row align-items-center">
                <div class="col d-flex align-items-center gap-3">
                    <div class="profile-icon-header d-flex align-items-center justify-content-center">
                        <i class="bi bi-egg-fried"></i>
                    </div>
                    <div class="site-title">
                        <h1>Restoran Bali</h1>
                        <p>Jelajahi Kuliner Terbaik Pulau Dewata</p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    Navbar
    <nav>
        <div class="container">
            <div class="d-flex justify-content-center flex-wrap nav-container">
                <a href="index.php">Beranda</a>
                <a href="restaurant.php">Restoran</a>
                <a href="#">Tentang</a>
                <a href="#">Kontak</a>
            </div>
        </div>
    </nav> -->

    <!-- Konten -->
    <main class="flex-grow-1 d-flex align-items-center justify-content-center py-5">
        <div class="profile-card position-relative p-5 w-100" style="max-width:480px">

            <!-- Avatar & nama -->
            <div class="text-center mb-4">
                <div class="avatar-wrap d-flex align-items-center justify-content-center mx-auto mb-3">
                    <i class="bi bi-person-fill text-white"></i>
                </div>
                <h4 class="mb-0 fw-bold" style="font-family:'Playfair Display',serif;color:var(--brown)">
                    <?= htmlspecialchars($user['username']) ?>
                </h4>
                <p class="mb-0" style="font-size:.875rem;color:var(--muted)">
                    <?= htmlspecialchars($user['email']) ?>
                </p>
            </div>

            <!-- Divider -->
            <hr style="border-color:var(--border);margin-bottom:1.5rem" />

            <!-- Field username -->
            <div class="mb-3">
                <label class="form-label">Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-at"></i></span>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']) ?>"
                        disabled />
                </div>
            </div>

            <!-- Field email -->
            <div class="mb-4">
                <label class="form-label">Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" disabled />
                </div>
            </div>

            <!-- Tombol -->
            <div class="d-flex gap-2">
                <a href="index.php" class="btn btn-main flex-grow-1 py-2">
                    <i class="bi bi-house-fill me-1"></i> Kembali ke Beranda
                </a>
                <a href="logout.php" class="btn btn-logout px-4 py-2">
                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                </a>
            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer class="site-footer text-center py-3">
        &copy; 2025 Restoran Bali — Jelajahi Kuliner Terbaik Pulau Dewata
    </footer>

</body>

</html>