<?php 
session_start();
include 'config/koneksi.php';

// Sistem Login Sederhana
if(isset($_POST['login'])) {

    // Ambil data dari form
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['pass'];

    // Cek apakah email kosong
    if(empty($email) || empty($password)) {
        echo "<script>alert('Email dan Password wajib diisi!');</script>";
        return;
    }

    // Cek user berdasarkan email
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    // Jika email ditemukan
    if(mysqli_num_rows($result) === 1) {

        $user = mysqli_fetch_assoc($result);

        // Validasi Password
        if($password === $user['password']) {

            // Simpan session
            $_SESSION['login'] = true;
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];

            echo "<script>alert('Login berhasil!');window.location='index.php';</script>";
            exit;

        } else {
            echo "<script>alert('Password salah!');</script>";
        }

    } else {
        echo "<script>alert('Email tidak ditemukan!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login – Restoran Bali</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap"
        rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
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

    .site-header {
        background: linear-gradient(135deg, var(--orange) 0%, var(--orange-light) 60%, var(--yellow) 100%);
    }

    .logo-icon {
        width: 48px;
        height: 48px;
        background: rgba(255, 255, 255, .25);
        border-radius: 50%;
        font-size: 22px;
    }

    .logo-text h1 {
        font-family: 'Playfair Display', serif;
        font-size: 1.45rem;
        line-height: 1.1;
    }

    .logo-text p {
        font-size: .78rem;
        color: rgba(255, 255, 255, .85);
    }

    .bg-circles {
        position: relative;
        overflow: hidden;
    }

    .bg-circles::before {
        content: '';
        position: absolute;
        width: 420px;
        height: 420px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(244, 123, 32, .08) 0%, transparent 70%);
        top: -80px;
        right: -80px;
        pointer-events: none;
    }

    .bg-circles::after {
        content: '';
        position: absolute;
        width: 300px;
        height: 300px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255, 209, 102, .12) 0%, transparent 70%);
        bottom: -60px;
        left: -60px;
        pointer-events: none;
    }

    .auth-card {
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

    .auth-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 44px;
        right: 44px;
        height: 3px;
        background: linear-gradient(90deg, var(--orange), var(--yellow));
        border-radius: 0 0 4px 4px;
    }

    .card-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, var(--orange), var(--orange-light));
        border-radius: 16px;
        font-size: 26px;
        box-shadow: 0 4px 16px rgba(244, 123, 32, .35);
    }

    .card-title-text {
        font-family: 'Playfair Display', serif;
        color: var(--brown);
    }

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
    }

    .form-control {
        background: var(--cream);
        border: 1.5px solid var(--border);
        border-left: none;
        border-radius: 0;
        font-size: .9rem;
        color: var(--text);
    }

    .form-control:last-child {
        border-radius: 0 10px 10px 0;
    }

    .form-control:focus {
        border-color: var(--orange);
        box-shadow: 0 0 0 3px rgba(244, 123, 32, .12);
        background: #fff;
    }

    .input-group:focus-within .input-group-text {
        border-color: var(--orange);
    }

    .btn-main {
        background: linear-gradient(135deg, var(--orange), var(--orange-light));
        color: #fff;
        border: none;
        border-radius: 10px;
        font-size: .95rem;
        font-weight: 500;
        letter-spacing: .3px;
        box-shadow: 0 4px 16px rgba(244, 123, 32, .35);
        transition: transform .15s, box-shadow .15s;
    }

    .btn-main:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(244, 123, 32, .45);
        color: #fff;
    }

    .btn-main:active {
        transform: translateY(0);
    }

    .btn-google {
        background: #fff;
        color: var(--text);
        border: 1.5px solid var(--border);
        border-radius: 10px;
        font-size: .9rem;
        transition: background .2s, border-color .2s;
    }

    .btn-google:hover {
        background: var(--cream);
        border-color: var(--orange);
    }

    .divider {
        color: var(--muted);
        font-size: .8rem;
    }

    .divider::before,
    .divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--border);
    }

    input[type=checkbox] {
        accent-color: var(--orange);
    }

    .btn-back {
        background: transparent;
        color: #fff;
        border: 1.5px solid rgba(255, 255, 255, .6);
        border-radius: 20px;
        font-size: .82rem;
        padding: 5px 14px;
        text-decoration: none;
        transition: background .2s, border-color .2s;
    }

    .btn-back:hover {
        background: rgba(255, 255, 255, .2);
        border-color: #fff;
        color: #fff;
    }

    .site-footer {
        font-size: .78rem;
        color: var(--muted);
        border-top: 1px solid var(--border);
    }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">

    <header class="site-header px-4 py-3 d-flex align-items-center gap-3">
        <div class="logo-icon d-flex align-items-center justify-content-center">
            <i class="bi bi-egg-fried text-white fs-4"></i>
        </div>
        <div class="logo-text">
            <h1 class="text-white mb-0">Restoran Bali</h1>
            <p class="mb-0">Jelajahi Kuliner Terbaik Pulau Dewata</p>
        </div>
        <a href="index.php" class="btn-back ms-auto d-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> Beranda
        </a>
    </header>

    <main class="flex-grow-1 d-flex align-items-center justify-content-center py-5 bg-circles">
        <div class="auth-card card border-0 position-relative p-5 w-100" style="max-width:440px">
            <form method="POST">
                <div class="card-icon d-flex align-items-center justify-content-center mx-auto mb-4">
                    <i class="bi bi-box-arrow-in-right text-white fs-4"></i>
                </div>
                <h2 class="card-title-text text-center fs-3 mb-1">Masuk</h2>
                <p class="text-center mb-4" style="font-size:.875rem;color:var(--muted)">Selamat datang kembali! Masuk
                    untuk
                    melanjutkan.</p>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input name="email" type="email" class="form-control" placeholder="nama@email.com" />
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Kata Sandi</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input name="pass" type="password" class="form-control" id="loginPassword"
                            placeholder="Masukkan kata sandi" />
                        <span class="input-group-text" onclick="togglePassword('loginPassword', this)"
                            style="cursor:pointer;border-left:none;border:1.5px solid var(--border);border-left:none;border-radius:0 10px 10px 0">
                            <i class="bi bi-eye"></i>
                        </span>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <!-- <label class="d-flex align-items-center gap-2"
                    style="font-size:.83rem;color:var(--muted);cursor:pointer">
                    <input type="checkbox" style="width:15px;height:15px" /> Ingat saya
                </label> -->
                    <a href="#" style="font-size:.83rem;color:var(--orange);font-weight:500;text-decoration:none">Lupa
                        kata
                        sandi?</a>
                </div>

                <button type="submit" name="login" class="btn btn-main w-100 py-3">
                    Masuk Sekarang
                </button>

                <p class="text-center mt-4 mb-0" style="font-size:.875rem;color:var(--muted)">
                    Belum punya akun? <a href="register.php"
                        style="color:var(--orange);font-weight:500;text-decoration:none">Daftar sekarang</a>
                </p>
            </form>
        </div>
    </main>

    <footer class="site-footer text-center py-3">
        &copy; 2025 Restoran Bali — Jelajahi Kuliner Terbaik Pulau Dewata
    </footer>

    <script>
    function togglePassword(id, el) {
        const input = document.getElementById(id);
        const icon = el.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    }
    </script>
</body>

</html>