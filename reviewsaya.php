<?php
include 'config/koneksi.php';
session_start();

if(!isset($_SESSION['id_user'])){
    header("Location: login.php");
    exit;
}

$id_user = (int) $_SESSION['id_user'];

$query = mysqli_query($conn, "
SELECT 
    restoran.*,
    lokasi.nama_lokasi,
    rating.rating,
    rating.ulasan
FROM rating
JOIN restoran ON restoran.id = rating.id_restoran
LEFT JOIN lokasi ON lokasi.id = restoran.lokasi
WHERE rating.id_user = $id_user
");

$stat = mysqli_query($conn, "
SELECT 
    COUNT(*) AS total_review,
    COUNT(DISTINCT id_restoran) AS total_restoran,
    AVG(rating) AS rata_rating
FROM rating
WHERE id_user = $id_user
");

$dataStat = mysqli_fetch_assoc($stat);

// Query distribusi bintang
$queryBars = mysqli_query($conn, "
SELECT rating AS bintang, COUNT(*) AS jumlah
FROM rating
WHERE id_user = $id_user
GROUP BY rating
");
$barsData = [5=>0, 4=>0, 3=>0, 2=>0, 1=>0];
while($b = mysqli_fetch_assoc($queryBars)){
    $barsData[(int)$b['bintang']] = (int)$b['jumlah'];
}

if(!$query){
    die(mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Review Saya – Restoran Bali</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap"
        rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
    :root {
        --orange: #ff6b35;
        --orange2: #f7931e;
        --orange-soft: #F47B20;
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
        color: var(--text);
    }

    /* Header */
    .site-header {
        background: linear-gradient(135deg, var(--orange) 0%, var(--orange-light) 60%, var(--yellow) 100%);
        margin-bottom: 20px;
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

    .profile-icon {
        width: 50px;
        height: 50px;
        background: #fff;
        border-radius: 50%;
        font-size: 1.5rem;
        color: var(--orange);
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

    .btn-login {
        background: #fff;
        color: var(--orange);
        border: 2px solid var(--orange);
        border-radius: 25px;
        padding: .6rem 1.5rem;
        font-weight: 600;
        transition: .3s;
    }

    .btn-login:hover {
        background: var(--orange);
        color: #fff;
    }

    .btn-register {
        background: transparent;
        color: #fff;
        border: 2px solid #fff;
        border-radius: 25px;
        padding: .6rem 1.5rem;
        font-weight: 600;
        transition: .3s;
    }

    .btn-register:hover {
        background: #fff;
        color: var(--orange);
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

    .nav-container a:hover,
    .nav-container a.active {
        color: var(--orange);
        background: #fff5f0;
        border-bottom-color: var(--orange);
    }

    /* Breadcrumb */
    .breadcrumb-item a {
        color: var(--orange-soft);
        text-decoration: none;
        font-size: .85rem;
    }

    .breadcrumb-item.active {
        color: var(--muted);
        font-size: .85rem;
    }

    .breadcrumb-item+.breadcrumb-item::before {
        color: var(--muted);
    }

    /* Page title */
    .page-title {
        font-family: 'Playfair Display', serif;
        color: var(--brown);
        font-size: 2rem;
    }

    /* Stat card */
    .stat-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 20px 24px;
        box-shadow: 0 2px 10px rgba(244, 123, 32, .07);
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: var(--orange-soft);
        line-height: 1;
    }

    .stat-label {
        font-size: .8rem;
        color: var(--muted);
        margin-top: 4px;
    }

    /* Review card */
    .review-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 14px;
        box-shadow: 0 2px 10px rgba(244, 123, 32, .07);
        transition: all .3s;
        overflow: hidden;
    }

    .review-card:hover {
        box-shadow: 0 6px 20px rgba(244, 123, 32, .15);
        transform: translateY(-2px);
    }

    .resto-img {
        width: 100%;
        height: 160px;
        object-fit: cover;
    }

    .badge-rating {
        background: #F59E0B;
        color: #fff;
        border-radius: 20px;
        font-size: .8rem;
        padding: 4px 10px;
        font-weight: 600;
    }

    .bintang {
        color: #F59E0B;
        font-size: .85rem;
    }

    .nama-resto {
        font-family: 'Playfair Display', serif;
        color: var(--brown);
        font-size: 1.05rem;
    }

    .tgl-review {
        font-size: .75rem;
        color: var(--muted);
    }

    .teks-review {
        font-size: .875rem;
        color: var(--text);
        line-height: 1.65;
    }

    /* Tombol hapus */
    .btn-hapus {
        background: transparent;
        color: #dc3545;
        border: 1.5px solid #dc3545;
        border-radius: 8px;
        font-size: .8rem;
        padding: 5px 14px;
        transition: .2s;
    }

    .btn-hapus:hover {
        background: #dc3545;
        color: #fff;
    }

    /* Tombol lihat detail */
    .btn-detail {
        background: linear-gradient(135deg, var(--orange-soft), var(--orange-light));
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: .8rem;
        padding: 5px 14px;
        text-decoration: none;
        transition: transform .15s, box-shadow .15s;
        box-shadow: 0 2px 8px rgba(244, 123, 32, .25);
    }

    .btn-detail:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(244, 123, 32, .35);
        color: #fff;
    }

    /* Kosong state */
    .empty-state {
        color: var(--muted);
    }

    /* Footer */
    .site-footer {
        font-size: .78rem;
        color: var(--muted);
        border-top: 1px solid var(--border);
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

    <main class="container pb-5">

        <!-- Page Header -->
        <?php 
        if($dataStat['total_review'] != 0)        : ?>
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
            <div>
                <h1 class="page-title mb-1">Review Saya</h1>
                <p class="mb-0" style="font-size:.9rem;color:var(--muted)">Semua ulasan yang pernah kamu berikan</p>
            </div>
        </div>

        <!-- Stat Cards -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="stat-card text-center">
                    <div class="stat-number"><?= $dataStat['total_review']; ?></div>
                    <div class="stat-label">Total Review</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card text-center">
                    <div class="stat-number"><?= number_format($dataStat['rata_rating'], 1) ?></div>
                    <div class="stat-label">Rata-rata Rating</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card text-center">
                    <div class="stat-number"><?= $dataStat['total_restoran']; ?></div>
                    <div class="stat-label">Restoran Dikunjungi</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card text-center">
                    <div class="stat-number" style="font-size:1.5rem">
                        -
                    </div>
                    <div class="stat-label">Review Terbaru</div>
                </div>
            </div>
        </div>

        <!-- Daftar Review -->
        <div class="row g-4">
            <?php while($row = mysqli_fetch_assoc($query)): ?>
            <!-- Review  -->
            <div class="col-lg-4 col-md-6">
                <div class="review-card">
                    <img src="assets/<?= $row['gambar']; ?>" class="resto-img" alt="Warung Bali Asli" />
                    <div class="p-3">

                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="nama-resto mb-0"><?= $row['nama']; ?></h6>
                            <span class="badge-rating">
                                <i class="bi bi-star-fill me-1"></i><?= $row['rating']; ?>
                            </span>
                        </div>

                        <div class="bintang mb-2">
                            <?php
                            $r = round($row['rating'] * 2) / 2;
                            for($i = 1; $i <= 5; $i++){
                                if($r >= $i)         echo '<i class="bi bi-star-fill"></i>';
                                elseif($r >= $i-0.5) echo '<i class="bi bi-star-half"></i>';
                                else                 echo '<i class="bi bi-star"></i>';
                            }
                            ?>
                        </div>

                        <p class="teks-review mb-2">
                            <?= $row['ulasan'] ?>
                        </p>

                        <div class="d-flex justify-content-between align-items-center pt-2"
                            style="border-top:1px solid var(--border)">
                            <span class="tgl-review">
                                -
                            </span>
                            <div class="d-flex gap-2">
                                <a href="detail_restoran.php?id=<?= $row['id']; ?>" class="btn-detail">Lihat</a>
                                <button class="btn-hapus">Hapus</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <!-- Akhir daftar review -->

        <?php else: ?>
        <!-- CONTOH TAMPILAN KALAU TIDAK ADA REVIEW (uncomment kalau mau lihat): -->
        <div class="empty-state text-center py-5">
            <i class="bi bi-star" style="font-size:3rem;opacity:.3"></i>
            <h5 class="mt-3 mb-1" style="font-family:'Playfair Display',serif;color:var(--brown)">Belum Ada Review
            </h5>
            <p style="font-size:.875rem">Kamu belum pernah memberikan ulasan. Coba kunjungi restoran dan beri
                rating!
            </p>
            <a href="restaurant.php" class="btn-detail px-4 py-2 mt-2 d-inline-flex align-items-center gap-2">
                <i class="bi bi-search"></i> Cari Restoran
            </a>
        </div>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="site-footer text-center py-3 mt-auto">
        &copy; 2025 Restoran Bali — Jelajahi Kuliner Terbaik Pulau Dewata
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>