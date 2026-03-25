<?php 
include 'config/koneksi.php';

if(isset($_POST['register'])) {

    $email            = mysqli_real_escape_string($conn, $_POST['email']);
    $username         = mysqli_real_escape_string($conn, $_POST['username']);
    $password         = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if(empty($email) || empty($username) || empty($password) || empty($confirm_password)){
        echo "<script>alert('Semua field wajib diisi!');</script>";
    }
    elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        echo "<script>alert('Format email tidak valid!');</script>";
    }
    elseif(strlen($password) < 5){
        echo "<script>alert('Password minimal 5 karakter!');</script>";
    }
    elseif($password !== $confirm_password){
        echo "<script>alert('Konfirmasi password tidak cocok!');</script>";
    }
    else{
        $cek = mysqli_query($conn,"SELECT * FROM users WHERE email='$email'");
        if(mysqli_num_rows($cek) > 0){
            echo "<script>alert('Email sudah terdaftar!');</script>";
        } else {
            $sql = "INSERT INTO users (email, username, password) VALUES ('$email','$username','$password')";
            if(mysqli_query($conn,$sql)){
                $registerSuccess = true;
            } else{
                echo "<script>alert('Gagal mendaftar');</script>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Daftar – Restoran Bali</title>
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
        --success: #22C55E;
        --error: #EF4444;
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
        width: 500px;
        height: 500px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(244, 123, 32, .07) 0%, transparent 70%);
        top: -100px;
        right: -100px;
        pointer-events: none;
    }

    .bg-circles::after {
        content: '';
        position: absolute;
        width: 350px;
        height: 350px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255, 209, 102, .10) 0%, transparent 70%);
        bottom: -80px;
        left: -80px;
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
        font-size: .9rem;
        color: var(--muted);
        font-weight: 500;
    }

    .form-control {
        background: var(--cream);
        border: 1.5px solid var(--border);
        border-left: none;
        border-radius: 0 10px 10px 0;
        font-size: .9rem;
        color: var(--text);
    }

    .form-control:focus {
        border-color: var(--orange);
        box-shadow: 0 0 0 3px rgba(244, 123, 32, .12);
        background: #fff;
    }

    .input-group:focus-within .input-group-text {
        border-color: var(--orange);
    }

    .bar {
        flex: 1;
        height: 3px;
        background: var(--border);
        border-radius: 4px;
        transition: background .3s;
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
        <div class="auth-card card border-0 position-relative p-5 w-100" style="max-width:480px">

            <div class="card-icon d-flex align-items-center justify-content-center mx-auto mb-4">
                <i class="bi bi-person-plus text-white fs-4"></i>
            </div>
            <h2 class="card-title-text text-center fs-3 mb-1">Buat Akun</h2>
            <p class="text-center mb-4" style="font-size:.875rem;color:var(--muted)">Bergabunglah dan nikmati kuliner
                terbaik Bali.</p>

            <!-- FORM DAFTAR -->
            <form method="POST">
                <div id="step1">

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input id="email" name="email" type="email" class="form-control"
                                placeholder="nama@email.com" required />
                        </div>
                        <small id="emailMsg"></small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-at"></i></span>
                            <input id="username" name="username" type="text" class="form-control"
                                placeholder="username unik Anda" required />
                        </div>
                        <small id="userMsg"></small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kata Sandi</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input name="password" type="password" class="form-control" id="regPassword"
                                placeholder="Min. 5 karakter" oninput="checkStrength(this.value)" required />
                            <span class="input-group-text" onclick="togglePassword('regPassword', this)"
                                style="cursor:pointer;border-left:none;border:1.5px solid var(--border);border-radius:0 10px 10px 0">
                                <i class="bi bi-eye"></i>
                            </span>
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <div class="bar" id="b1"></div>
                            <div class="bar" id="b2"></div>
                            <div class="bar" id="b3"></div>
                            <div class="bar" id="b4"></div>
                            <span id="slabel" style="font-size:.72rem;color:var(--muted);white-space:nowrap">—</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Konfirmasi Kata Sandi</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input id="regConfirm" name="confirm_password" type="password" class="form-control"
                                placeholder="Ulangi kata sandi" required />
                            <span class="input-group-text" onclick="togglePassword('regConfirm', this)"
                                style="cursor:pointer;border-left:none;border:1.5px solid var(--border);border-radius:0 10px 10px 0">
                                <i class="bi bi-eye"></i>
                            </span>
                        </div>
                        <small id="passMsg"></small>
                    </div>
                    <!-- 
                    <label class="d-flex align-items-start gap-2 mb-0"
                        style="font-size:.83rem;color:var(--muted);cursor:pointer">
                        <input type="checkbox" style="width:16px;height:16px;margin-top:2px" required />
                        Saya Setuju <a href="#" style="color:var(--orange);font-weight:500;text-decoration:none">Syarat
                            &amp; Ketentuan</a>
                        dan <a href="#" style="color:var(--orange);font-weight:500;text-decoration:none">Kebijakan
                            Privasi</a>
                    </label> -->

                    <input name="register" class="btn btn-main w-100 py-3 mt-4" type="submit" value="Daftar Sekarang" />
                </div>
            </form>

            <!-- SUKSES -->
            <div id="sukses" class="text-center" style="display:none">
                <div class="d-flex align-items-center justify-content-center mx-auto mb-4"
                    style="width:72px;height:72px;background:linear-gradient(135deg,#22C55E,#4ADE80);border-radius:50%;font-size:32px;box-shadow:0 4px 16px rgba(34,197,94,.3)">
                    <i class="bi bi-check-lg text-white"></i>
                </div>
                <h3 style="font-family:'Playfair Display',serif;font-size:1.4rem;color:var(--brown)" class="mb-2">
                    Pendaftaran Berhasil!</h3>
                <p style="color:var(--muted);font-size:.875rem;line-height:1.6" class="mb-4">
                    Selamat datang di Restoran Bali! Akun Anda telah berhasil dibuat.
                </p>
                <button class="btn btn-main w-100 py-3" onclick="window.location.href='login.php'">Masuk
                    Sekarang</button>
            </div>

            <p class="text-center mt-4 mb-0" id="loginLink" style="font-size:.875rem;color:var(--muted)">
                Sudah punya akun? <a href="login.php"
                    style="color:var(--orange);font-weight:500;text-decoration:none">Masuk di sini</a>
            </p>
        </div>
    </main>

    <footer class="site-footer text-center py-3">
        &copy; 2025 Restoran Bali — Jelajahi Kuliner Terbaik Pulau Dewata
    </footer>

    <script>
    let registerSuccess = <?php echo isset($registerSuccess) ? 'true' : 'false'; ?>;

    // Cek email realtime
    document.getElementById("email").addEventListener("keyup", function() {
        fetch("cek_user.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "email=" + this.value
            })
            .then(res => res.text())
            .then(data => {
                let msg = document.getElementById("emailMsg");
                if (this.value === "") {
                    msg.innerHTML = "";
                } else {
                    if (data === "email_ada") {
                        msg.innerHTML = "❌ Email sudah terdaftar";
                        msg.style.color = "red";
                    } else {
                        msg.innerHTML = "✅ Email tersedia";
                        msg.style.color = "green";
                    }
                }
            });
    });

    // Cek username realtime
    document.getElementById("username").addEventListener("keyup", function() {
        fetch("cek_user.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "username=" + this.value
            })
            .then(res => res.text())
            .then(data => {
                let msg = document.getElementById("userMsg");
                if (this.value === "") {
                    msg.innerHTML = "";
                } else {
                    if (data === "username_ada") {
                        msg.innerHTML = "❌ Username sudah dipakai";
                        msg.style.color = "red";
                    } else {
                        msg.innerHTML = "✅ Username tersedia";
                        msg.style.color = "green";
                    }
                }
            });
    });

    // Cek password cocok
    document.getElementById("regConfirm").addEventListener("keyup", function() {
        let pass = document.getElementById("regPassword").value;
        let msg = document.getElementById("passMsg");
        if (this.value === "") {
            msg.innerHTML = "";
        } else {
            if (pass !== this.value) {
                msg.innerHTML = "❌ Password tidak sama";
                msg.style.color = "red";
            } else {
                msg.innerHTML = "✅ Password cocok";
                msg.style.color = "green";
            }
        }
    });

    function showSukses() {
        document.getElementById('step1').style.display = 'none';
        document.getElementById('sukses').style.display = '';
        document.getElementById('loginLink').style.display = 'none';
    }

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

    function checkStrength(val) {
        const bars = ['b1', 'b2', 'b3', 'b4'].map(id => document.getElementById(id));
        const label = document.getElementById('slabel');
        let score = 0;
        if (val.length >= 5) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;
        const colors = ['#EF4444', '#F97316', '#EAB308', '#22C55E'];
        const labels = ['Lemah', 'Sedang', 'Kuat', 'Sangat Kuat'];
        bars.forEach((b, i) => {
            b.style.background = i < score ? colors[score - 1] : 'var(--border)';
        });
        label.textContent = score > 0 ? labels[score - 1] : '—';
        label.style.color = score > 0 ? colors[score - 1] : 'var(--muted)';
    }

    window.onload = function() {
        if (registerSuccess) showSukses();
    }
    </script>

</body>

</html>