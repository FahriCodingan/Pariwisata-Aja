<?php
include 'config/koneksi.php';
session_start();

if(!isset($_SESSION['id_user'])){
    header("Location: login.php");
    exit;
}

$id_user = (int) $_SESSION['id_user'];
 
// ── Proses hapus review ──
if(isset($_POST['delete'])){
    $id_rating = (int) $_POST['id_rating'];
 
    // AND id_user → pastikan hanya milik user yang login yang bisa dihapus
    mysqli_query($conn, "DELETE FROM rating WHERE id = $id_rating AND id_user = $id_user");
 
    // Redirect agar tidak hapus ulang kalau halaman di-refresh
    header("Location: reviewsaya.php");
    exit;
}

$id_user = (int) $_SESSION['id_user'];

$query = mysqli_query($conn, "
SELECT 
    restoran.*,
    lokasi.nama_lokasi,
    rating.id AS id_rating,
    rating.rating,
    rating.ulasan,
    rating.created_at
FROM rating
JOIN restoran ON restoran.id = rating.id_restoran
LEFT JOIN lokasi ON lokasi.id = restoran.lokasi
WHERE rating.id_user = $id_user
ORDER BY rating.id DESC
");

$stat = mysqli_query($conn, "
SELECT 
    COUNT(*) AS total_review,
    COUNT(DISTINCT id_restoran) AS total_restoran,
    AVG(rating) AS rata_rating,
    MAX(created_at) AS terbaru
FROM rating
WHERE id_user = $id_user
");

$dataStat = mysqli_fetch_assoc($stat);

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

if(!$query){ die(mysqli_error($conn)); }
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

    .page-title {
        font-family: 'Playfair Display', serif;
        color: var(--brown);
        font-size: 2rem;
    }

    /* Stat */
    .stat-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 16px 20px;
        box-shadow: 0 2px 10px rgba(244, 123, 32, .07);
    }

    .stat-number {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--orange-soft);
        line-height: 1;
    }

    .stat-label {
        font-size: .78rem;
        color: var(--muted);
        margin-top: 3px;
    }

    /* ── Card horizontal compact ── */
    .review-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(244, 123, 32, .07);
        transition: all .3s;
        display: flex;
        align-items: stretch;
        overflow: hidden;
    }

    .review-card:hover {
        box-shadow: 0 5px 18px rgba(244, 123, 32, .14);
        transform: translateY(-1px);
    }

    .resto-img {
        width: 110px;
        min-width: 110px;
        object-fit: cover;
        flex-shrink: 0;
    }

    .review-body {
        padding: 12px 14px;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-width: 0;
    }

    .badge-rating {
        background: #F59E0B;
        color: #fff;
        border-radius: 20px;
        font-size: .75rem;
        padding: 3px 9px;
        font-weight: 600;
        white-space: nowrap;
    }

    .bintang {
        color: #F59E0B;
        font-size: .78rem;
    }

    .nama-resto {
        font-family: 'Playfair Display', serif;
        color: var(--brown);
        font-size: .95rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .tgl-review {
        font-size: .72rem;
        color: var(--muted);
    }

    /* Teks ulasan max 2 baris */
    .teks-review {
        font-size: .82rem;
        color: var(--text);
        line-height: 1.5;
        margin: 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .btn-hapus {
        background: transparent;
        color: #dc3545;
        border: 1.5px solid #dc3545;
        border-radius: 7px;
        font-size: .75rem;
        padding: 4px 10px;
        transition: .2s;
    }

    .btn-hapus:hover {
        background: #dc3545;
        color: #fff;
    }

    .btn-detail {
        background: linear-gradient(135deg, var(--orange-soft), var(--orange-light));
        color: #fff;
        border: none;
        border-radius: 7px;
        font-size: .75rem;
        padding: 4px 10px;
        text-decoration: none;
        transition: transform .15s, box-shadow .15s;
        box-shadow: 0 2px 6px rgba(244, 123, 32, .2);
    }

    .btn-detail:hover {
        transform: translateY(-1px);
        box-shadow: 0 3px 10px rgba(244, 123, 32, .3);
        color: #fff;
    }

    .empty-state {
        color: var(--muted);
    }

    .site-footer {
        font-size: .78rem;
        color: var(--muted);
        border-top: 1px solid var(--border);
    }

    @media (max-width:480px) {
        .resto-img {
            width: 80px;
            min-width: 80px;
        }
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

        <?php if($dataStat['total_review'] != 0): ?>

        <div class="mb-4">
            <h1 class="page-title mb-1">Review Saya</h1>
            <p class="mb-0" style="font-size:.9rem;color:var(--muted)">Semua ulasan yang pernah kamu berikan</p>
        </div>

        <!-- Stat Cards -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="stat-card text-center">
                    <div class="stat-number"><?= $dataStat['total_review'] ?></div>
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
                    <div class="stat-number"><?= $dataStat['total_restoran'] ?></div>
                    <div class="stat-label">Restoran Dikunjungi</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card text-center">
                    <div class="stat-number" style="font-size:1.05rem;padding-top:6px">
                        <?= $dataStat['terbaru'] ? date('d M Y', strtotime($dataStat['terbaru'])) : '-' ?>
                    </div>
                    <div class="stat-label">Review Terbaru</div>
                </div>
            </div>
        </div>

        <!-- Daftar Review — List Horizontal -->
        <div class="d-flex flex-column gap-3">
            <?php while($row = mysqli_fetch_assoc($query)): ?>
            <div class="review-card">

                <!-- Gambar kecil kiri -->
                <img src="assets/<?= htmlspecialchars($row['gambar']) ?>" class="resto-img"
                    alt="<?= htmlspecialchars($row['nama']) ?>" />

                <!-- Konten kanan -->
                <div class="review-body">

                    <!-- Nama + badge rating -->
                    <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                        <span class="nama-resto"><?= htmlspecialchars($row['nama']) ?></span>
                        <span class="badge-rating flex-shrink-0">
                            <i class="bi bi-star-fill me-1"></i><?= $row['rating'] ?>
                        </span>
                    </div>

                    <!-- Bintang + lokasi -->
                    <div class="bintang mb-1">
                        <?php
                        $r = round($row['rating'] * 2) / 2;
                        for($i = 1; $i <= 5; $i++){
                            if($r >= $i)         echo '<i class="bi bi-star-fill"></i>';
                            elseif($r >= $i-0.5) echo '<i class="bi bi-star-half"></i>';
                            else                 echo '<i class="bi bi-star"></i>';
                        }
                        ?>
                        <span class="ms-1" style="font-size:.72rem;color:var(--muted)">
                            · <?= htmlspecialchars($row['nama_lokasi']) ?>
                        </span>
                    </div>

                    <!-- Teks ulasan (2 baris, terpotong kalau panjang) -->
                    <?php if(!empty($row['ulasan'])): ?>
                    <p class="teks-review mb-2"><?= htmlspecialchars($row['ulasan']) ?></p>
                    <?php else: ?>
                    <p class="mb-2" style="font-size:.78rem;color:var(--muted);font-style:italic">Tidak ada teks ulasan
                    </p>
                    <?php endif; ?>

                    <!-- Tanggal + tombol -->
                    <div class="d-flex justify-content-between align-items-center pt-1"
                        style="border-top:1px solid var(--border)">
                        <span class="tgl-review">
                            <i class="bi bi-calendar3 me-1"></i>
                            <?= isset($row['created_at']) ? date('d M Y', strtotime($row['created_at'])) : '-' ?>
                        </span>
                        <div class="d-flex gap-2">
                            <a href="detail_restoran.php?id=<?= $row['id'] ?>" class="btn-detail">
                                <i class="bi bi-eye me-1"></i>Lihat
                            </a>
                            <form method="POST" action="reviewsaya.php"
                                onsubmit="return confirm('Yakin ingin menghapus ulasan ini?')">
                                <input type="hidden" name="id_rating" value="<?= $row['id_rating'] ?>">
                                <button type="submit" name="delete" class="btn-hapus">
                                    <i class="bi bi-trash me-1"></i>Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <?php else: ?>
        <!-- Kosong -->
        <div class="empty-state text-center py-5">
            <i class="bi bi-star" style="font-size:3rem;opacity:.3"></i>
            <h5 class="mt-3 mb-1" style="font-family:'Playfair Display',serif;color:var(--brown)">Belum Ada Review</h5>
            <p style="font-size:.875rem">Kamu belum pernah memberikan ulasan. Coba kunjungi restoran dan beri rating!
            </p>
            <a href="restaurant.php" class="btn-detail px-4 py-2 mt-2 d-inline-flex align-items-center gap-2">
                <i class="bi bi-search"></i> Cari Restoran
            </a>
        </div>
        <?php endif; ?>

    </main>

    <footer class="site-footer text-center py-3 mt-auto">
        &copy; 2025 Restoran Bali — Jelajahi Kuliner Terbaik Pulau Dewata
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>