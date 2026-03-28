<?php
// ── Tambahan: session untuk cek login ──
session_start();
$active = '';
include 'config/koneksi.php';

// Ambil id dari URL, kalau tidak ada redirect ke restaurant.php
if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    header("Location: restaurant.php");
    exit;
}

$id = (int)$_GET['id'];

// Query detail restoran berdasarkan id
$query = mysqli_query($conn, "
SELECT 
    restoran.*,
    jadwal.*,
    lokasi.nama_lokasi,
    COALESCE(r.rata_rating, 0) AS rating,
    COALESCE(r.total_review, 0) AS total_review
FROM restoran
LEFT JOIN lokasi 
    ON lokasi.id = restoran.lokasi
LEFT JOIN jadwal
    ON jadwal.id_restoran = restoran.id
LEFT JOIN (
    SELECT 
        id_restoran,
        AVG(rating) AS rata_rating,
        COUNT(*) AS total_review
    FROM rating
    GROUP BY id_restoran
) r 
    ON r.id_restoran = restoran.id
WHERE restoran.id = $id
LIMIT 1
");

$row = mysqli_fetch_assoc($query);

// Kalau restoran tidak ditemukan redirect
if(!$row){
    header("Location: restaurant.php");
    exit;
}

// Query ulasan preview (3 terbaru) untuk tampilan utama
$queryUlasan = mysqli_query($conn, "
SELECT rating.*, users.username
FROM rating
LEFT JOIN users ON users.id_user = rating.id_user
WHERE rating.id_restoran = $id
ORDER BY rating.id DESC
LIMIT 3
");

// Query SEMUA ulasan untuk modal (tanpa limit)
$querySemuaUlasan = mysqli_query($conn, "
SELECT rating.*, users.username
FROM rating
LEFT JOIN users ON users.id_user = rating.id_user
WHERE rating.id_restoran = $id
ORDER BY rating.id DESC
");
$semuaUlasan = [];
while($u = mysqli_fetch_assoc($querySemuaUlasan)){
    $semuaUlasan[] = $u;
}

// Query distribusi bintang
$queryBars = mysqli_query($conn, "
SELECT rating AS bintang, COUNT(*) AS jumlah
FROM rating
WHERE id_restoran = $id
GROUP BY rating
");
$barsData = [5=>0, 4=>0, 3=>0, 2=>0, 1=>0];
while($b = mysqli_fetch_assoc($queryBars)){
    $barsData[(int)$b['bintang']] = (int)$b['jumlah'];
}
$totalReview = array_sum($barsData);

// ── Proses kirim rating (hanya jika sudah login) ──
if(isset($_POST['kirim_rating'])){
    if(!isset($_SESSION['login'])){
        header("Location: login.php?redirect=detail_restoran.php?id=$id");
        exit;
    }

    $ratingVal = (int)$_POST['rating_value'];
    $ulasan    = mysqli_real_escape_string($conn, $_POST['ulasan']);
    $id_user   = $_SESSION['login']; // ← pakai session

    if($ratingVal >= 1 && $ratingVal <= 5){
        $sqlRating = "INSERT INTO rating (id_restoran, id_user, rating, ulasan)
                      VALUES ($id, $id_user, $ratingVal, '$ulasan')";
        if(mysqli_query($conn, $sqlRating)){
            header("Location: detail_restoran.php?id=$id&rated=1");
            exit;
        }
    }
}

// ── Cek apakah user sudah login ──
$sudahLogin = isset($_SESSION['login']);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($row['nama']) ?> – Restoran Bali</title>
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
        color: var(--text);
    }

    header {
        background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
        padding: 1.5rem 0;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .site-title h1 {
        color: white;
        font-size: 1.8rem;
        font-weight: 600;
    }

    .site-title p {
        color: #fff9f5;
        font-size: 0.9rem;
    }

    .profile-icon {
        width: 50px;
        height: 50px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: #ff6b35;
    }

    .btn-login {
        background: white;
        color: #ff6b35;
        border-radius: 25px;
        padding: .6rem 1.5rem;
        font-weight: 600;
        border: 2px solid #ff6b35;
        transition: .3s;
    }

    .btn-login:hover {
        background: #ff6b35;
        color: white;
    }

    .btn-register {
        background: transparent;
        color: white;
        border: 2px solid white;
        border-radius: 25px;
        padding: .6rem 1.5rem;
        font-weight: 600;
        transition: .3s;
    }

    .btn-register:hover {
        background: white;
        color: #ff6b35;
    }

    .search-box {
        position: relative;
    }

    .search-box input {
        padding: .8rem 3rem .8rem 1.2rem;
        border: none;
        border-radius: 25px;
        font-size: 1rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .1);
    }

    .search-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #ff6b35;
        font-size: 1.2rem;
    }

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
    }

    .nav-container a:hover,
    .nav-container a.active {
        color: #ff6b35;
        background: #fff5f0;
        border-bottom-color: #ff6b35;
    }

    .breadcrumb-item a {
        color: var(--orange);
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

    .foto-restoran {
        width: 100%;
        height: 340px;
        object-fit: cover;
        border-radius: 16px;
    }

    .card-detail {
        border: 1px solid var(--border);
        border-radius: 16px;
        background: #fff;
    }

    .nama-restoran {
        font-family: 'Playfair Display', serif;
        color: var(--brown);
    }

    .badge-rating {
        background: #F59E0B;
        color: #fff;
        border-radius: 20px;
        font-size: .85rem;
        padding: 5px 12px;
    }

    .info-item {
        font-size: .9rem;
        color: var(--muted);
    }

    .info-item i {
        color: var(--orange);
    }

    .btn-rating {
        background: linear-gradient(135deg, var(--orange), var(--orange-light));
        color: #fff;
        border: none;
        border-radius: 10px;
        font-size: .9rem;
        font-weight: 500;
        box-shadow: 0 4px 14px rgba(244, 123, 32, .3);
        transition: transform .15s, box-shadow .15s;
    }

    .btn-rating:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(244, 123, 32, .4);
        color: #fff;
    }

    .star-input {
        display: flex;
        flex-direction: row-reverse;
        justify-content: center;
        gap: 6px;
    }

    .star-input input {
        display: none;
    }

    .star-input label {
        font-size: 2rem;
        color: #D1D5DB;
        cursor: pointer;
        transition: color .15s;
    }

    .star-input input:checked~label,
    .star-input label:hover,
    .star-input label:hover~label {
        color: #F59E0B;
    }

    .card-ulasan {
        border: 1px solid var(--border);
        border-radius: 12px;
        background: #fff;
    }

    .nama-reviewer {
        font-weight: 500;
        color: var(--brown);
        font-size: .9rem;
    }

    .tgl-review {
        font-size: .78rem;
        color: var(--muted);
    }

    .teks-ulasan {
        font-size: .875rem;
        color: var(--text);
    }

    .bintang-kecil {
        color: #F59E0B;
        font-size: .85rem;
    }

    .section-title {
        font-family: 'Playfair Display', serif;
        color: var(--brown);
        font-size: 1.3rem;
    }

    .site-footer {
        font-size: .78rem;
        color: var(--muted);
        border-top: 1px solid var(--border);
    }

    .modal-header {
        border-bottom: 1px solid var(--border);
    }

    .modal-footer {
        border-top: 1px solid var(--border);
    }

    .form-label {
        font-size: .82rem;
        font-weight: 500;
        color: var(--brown-mid);
    }

    .form-control:focus {
        border-color: var(--orange);
        box-shadow: 0 0 0 3px rgba(244, 123, 32, .12);
    }

    /* ── Filter bintang di modal semua ulasan ── */
    .filter-bintang {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .filter-bintang button {
        padding: 4px 12px;
        border-radius: 20px;
        border: 1.5px solid var(--border);
        background: #fff;
        font-size: .8rem;
        font-weight: 500;
        color: var(--muted);
        cursor: pointer;
        transition: all .2s;
    }

    .filter-bintang button:hover {
        border-color: var(--orange);
        color: var(--orange);
    }

    .filter-bintang button.active {
        background: var(--orange);
        border-color: var(--orange);
        color: #fff;
    }

    .filter-bintang button.active-star {
        background: #F59E0B;
        border-color: #F59E0B;
        color: #fff;
    }

    /* Scroll area ulasan di modal */
    .ulasan-scroll {
        max-height: 420px;
        overflow-y: auto;
    }

    .ulasan-scroll::-webkit-scrollbar {
        width: 4px;
    }

    .ulasan-scroll::-webkit-scrollbar-track {
        background: var(--cream);
        border-radius: 4px;
    }

    .ulasan-scroll::-webkit-scrollbar-thumb {
        background: var(--border);
        border-radius: 4px;
    }

    /* Tombol lihat semua */
    .btn-lihat-semua {
        background: transparent;
        color: var(--orange);
        border: 1.5px solid var(--border);
        border-radius: 10px;
        font-size: .85rem;
        font-weight: 500;
        padding: 8px 20px;
        transition: all .2s;
    }

    .btn-lihat-semua:hover {
        background: var(--cream);
        border-color: var(--orange);
    }

    /* Kosong state */
    .empty-ulasan {
        color: var(--muted);
        font-size: .875rem;
    }
    </style>
</head>

<body>

    <!-- Header -->
    <?php include 'header.php'; ?>

    <!-- Navigasi -->
    <nav>
        <div class="container">
            <div class="d-flex justify-content-center flex-wrap nav-container">
                <a href="index.php">Beranda</a>
                <a href="restaurant.php" class="active">Restoran</a>
                <a href="#">Tentang</a>
                <a href="#">Kontak</a>
            </div>
        </div>
    </nav>

    <!-- Breadcrumb -->
    <div class="container py-3">
        <div aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php">Beranda</a></li>
                <li class="breadcrumb-item"><a href="restaurant.php">Restoran</a></li>
                <li class="breadcrumb-item active"><?= htmlspecialchars($row['nama']) ?></li>
            </ol>
        </div>
    </div>

    <!-- Alert ketika udah berhasil kirim ulasan -->
    <?php if(isset($_GET['rated'])): ?>
    <div class="container mb-2">
        <div class="alert alert-success d-flex align-items-center gap-2 py-2"
            style="border-radius:10px;font-size:.875rem">
            <i class="bi bi-check-circle-fill"></i> Rating berhasil dikirim! Terima kasih 😊
        </div>
    </div>
    <?php endif; ?>

    <main class="container py-3 pb-5">
        <div class="row g-4">

            <!-- KOLOM KIRI -->
            <div class="col-lg-7">
                <img src="assets/<?= htmlspecialchars($row['gambar']) ?>" alt="<?= htmlspecialchars($row['nama']) ?>"
                    class="foto-restoran mb-4" />

                <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-3">
                    <h1 class="nama-restoran fs-2 mb-0"><?= htmlspecialchars($row['nama']) ?></h1>
                    <span class="badge-rating d-flex align-items-center gap-1">
                        <i class="bi bi-star-fill"></i>
                        <?= number_format($row['rating'], 1) ?>
                        <span style="opacity:.85">(<?= $row['total_review'] ?> review)</span>
                    </span>
                </div>

                <div class="d-flex flex-wrap gap-3 mb-4">
                    <span class="info-item"><i
                            class="bi bi-geo-alt-fill me-1"></i><?= htmlspecialchars($row['nama_lokasi']) ?></span>
                    <span class="info-item"><i
                            class="bi bi-tag-fill me-1"></i><?= htmlspecialchars($row['kategori']) ?></span>
                    <span class="info-item"><i class="bi bi-currency-dollar me-1"></i>Rp
                        <?= number_format($row['harga'], 0, ',', '.') ?> hingga Rp
                        <?= number_format($row['max_harga'], 0, ',', '.') ?></span>
                    <span class="info-item"><i class="bi bi-clock-fill me-1"></i><?= $row['jam_buka'] ?> –
                        <?= $row['jam_tutup'] ?></span>
                </div>

                <div class="card-detail p-4 mb-4">
                    <h6 class="fw-semibold mb-2" style="color:var(--brown)">Tentang Restoran</h6>
                    <p class="mb-0" style="font-size:.9rem;line-height:1.7;color:var(--text)">
                        <?= htmlspecialchars($row['deskripsi']) ?>
                    </p>
                </div>

                <div class="d-flex gap-2">
                    <?php if($sudahLogin): ?>
                    <button class="btn btn-rating px-4 py-2 d-flex align-items-center gap-2" data-bs-toggle="modal"
                        data-bs-target="#modalRating">
                        <i class="bi bi-star-fill"></i> Beri Rating
                    </button>
                    <?php else: ?>
                    <button class="btn btn-rating px-4 py-2 d-flex align-items-center gap-2" data-bs-toggle="modal"
                        data-bs-target="#modalLoginDulu">
                        <i class="bi bi-star-fill"></i> Beri Rating
                    </button>
                    <?php endif; ?>
                    <a href="restaurant.php" style="text-decoration:none;">
                        <button style="background:white;color:var(--orange);border:1px solid var(--orange);"
                            class="btn btn-rating px-4 py-2 d-flex align-items-center gap-2">
                            <i class="bi bi-arrow-bar-left"></i> Kembali
                        </button>
                    </a>
                </div>
            </div>

            <!-- KOLOM KANAN -->
            <div class="col-lg-5">
                <h5 class="section-title mb-3">Ulasan Pengunjung</h5>

                <!-- Ringkasan bintang -->
                <div class="card-detail p-3 mb-4 d-flex align-items-center gap-3">
                    <div class="text-center">
                        <div style="font-size:2.5rem;font-weight:700;color:var(--orange);line-height:1">
                            <?= number_format($row['rating'], 1) ?>
                        </div>
                        <div class="bintang-kecil">
                            <?php
                            $r = round($row['rating'] * 2) / 2;
                            for($i = 1; $i <= 5; $i++){
                                if($r >= $i)         echo '<i class="bi bi-star-fill"></i>';
                                elseif($r >= $i-0.5) echo '<i class="bi bi-star-half"></i>';
                                else                 echo '<i class="bi bi-star"></i>';
                            }
                            ?>
                        </div>
                        <div style="font-size:.75rem;color:var(--muted)"><?= $totalReview ?> ulasan</div>
                    </div>
                    <div class="flex-grow-1">
                        <?php foreach($barsData as $star => $count): ?>
                        <?php $pct = $totalReview > 0 ? round(($count/$totalReview)*100) : 0; ?>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span style="font-size:.75rem;color:var(--muted);width:10px"><?= $star ?></span>
                            <i class="bi bi-star-fill" style="font-size:.7rem;color:#F59E0B"></i>
                            <div class="progress flex-grow-1" style="height:6px;border-radius:4px">
                                <div class="progress-bar"
                                    style="width:<?= $pct ?>%;background:var(--orange);border-radius:4px"></div>
                            </div>
                            <span style="font-size:.75rem;color:var(--muted);width:10px"><?= $count ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Preview 3 ulasan terbaru -->
                <div class="d-flex flex-column gap-3 mb-3">
                    <?php if(mysqli_num_rows($queryUlasan) > 0): ?>
                    <?php while($ulasan = mysqli_fetch_assoc($queryUlasan)): ?>
                    <div class="card-ulasan p-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="nama-reviewer">
                                <i class="bi bi-person-circle me-1" style="color:var(--orange)"></i>
                                <?= htmlspecialchars($ulasan['username'] ?? 'Anonim') ?>
                            </span>
                            <span class="tgl-review">
                                <?= isset($ulasan['created_at']) ? date('d M Y', strtotime($ulasan['created_at'])) : '' ?>
                            </span>
                        </div>
                        <div class="bintang-kecil mb-2">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                            <i class="bi <?= $i <= $ulasan['rating'] ? 'bi-star-fill' : 'bi-star' ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <?php if(!empty($ulasan['ulasan'])): ?>
                        <p class="teks-ulasan mb-0"><?= htmlspecialchars($ulasan['ulasan']) ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endwhile; ?>
                    <?php else: ?>
                    <div class="text-center py-4" style="color:var(--muted);font-size:.875rem">
                        <i class="bi bi-chat-square-text" style="font-size:2rem;opacity:.4"></i>
                        <p class="mt-2 mb-0">Belum ada ulasan. Jadilah yang pertama!</p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Tombol Lihat Semua Ulasan -->
                <?php if($totalReview > 0): ?>
                <div class="text-center">
                    <button class="btn-lihat-semua d-inline-flex align-items-center gap-2" data-bs-toggle="modal"
                        data-bs-target="#modalSemuaUlasan">
                        <i class="bi bi-chat-square-text"></i>
                        Lihat Semua <?= $totalReview ?> Ulasan
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- ============================================================ -->
    <!-- MODAL SEMUA ULASAN + FILTER BINTANG                         -->
    <!-- ============================================================ -->
    <div class="modal fade" id="modalSemuaUlasan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border-radius:16px;border:1px solid var(--border)">

                <div class="modal-header px-4 pt-4">
                    <div>
                        <h5 class="modal-title fw-semibold mb-1"
                            style="font-family:'Playfair Display',serif;color:var(--brown)">
                            <i class="bi bi-chat-square-text me-2" style="color:var(--orange)"></i>
                            Semua Ulasan
                        </h5>
                        <p class="mb-0" style="font-size:.8rem;color:var(--muted)">
                            <?= htmlspecialchars($row['nama']) ?> · <?= $totalReview ?> ulasan
                        </p>
                    </div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body px-4 py-3">

                    <!-- Filter bintang -->
                    <div class="filter-bintang mb-3">
                        <button class="active" onclick="filterUlasan(0, this)">Semua</button>
                        <button onclick="filterUlasan(5, this)"><i class="bi bi-star-fill" style="color:#F59E0B"></i>
                            5</button>
                        <button onclick="filterUlasan(4, this)"><i class="bi bi-star-fill" style="color:#F59E0B"></i>
                            4</button>
                        <button onclick="filterUlasan(3, this)"><i class="bi bi-star-fill" style="color:#F59E0B"></i>
                            3</button>
                        <button onclick="filterUlasan(2, this)"><i class="bi bi-star-fill" style="color:#F59E0B"></i>
                            2</button>
                        <button onclick="filterUlasan(1, this)"><i class="bi bi-star-fill" style="color:#F59E0B"></i>
                            1</button>
                    </div>

                    <!-- Counter hasil filter -->
                    <p id="filterInfo" class="mb-3" style="font-size:.8rem;color:var(--muted)">
                        Menampilkan <span id="filterCount"><?= $totalReview ?></span> ulasan
                    </p>

                    <!-- Daftar semua ulasan (scrollable) -->
                    <div class="ulasan-scroll d-flex flex-column gap-3" id="listSemuaUlasan">
                        <?php foreach($semuaUlasan as $u): ?>
                        <!-- data-rating dipakai JavaScript untuk filter -->
                        <div class="card-ulasan p-3" data-rating="<?= $u['rating'] ?>">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="nama-reviewer">
                                    <i class="bi bi-person-circle me-1" style="color:var(--orange)"></i>
                                    <?= htmlspecialchars($u['username'] ?? 'Anonim') ?>
                                </span>
                                <div class="d-flex align-items-center gap-2">
                                    <!-- Badge rating angka -->
                                    <span
                                        style="background:#F59E0B;color:#fff;border-radius:20px;font-size:.75rem;padding:2px 8px;font-weight:600">
                                        <i class="bi bi-star-fill"></i> <?= $u['rating'] ?>
                                    </span>
                                    <span class="tgl-review">
                                        <?= isset($u['created_at']) ? date('d M Y', strtotime($u['created_at'])) : '' ?>
                                    </span>
                                </div>
                            </div>
                            <div class="bintang-kecil mb-2">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                <i class="bi <?= $i <= $u['rating'] ? 'bi-star-fill' : 'bi-star' ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <?php if(!empty($u['ulasan'])): ?>
                            <p class="teks-ulasan mb-0"><?= htmlspecialchars($u['ulasan']) ?></p>
                            <?php else: ?>
                            <p class="mb-0" style="font-size:.8rem;color:var(--muted);font-style:italic">Tidak ada teks
                                ulasan</p>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>

                        <!-- Pesan kalau hasil filter kosong -->
                        <div id="emptyFilter" class="text-center py-4 empty-ulasan" style="display:none">
                            <i class="bi bi-star" style="font-size:2rem;opacity:.3"></i>
                            <p class="mt-2 mb-0">Tidak ada ulasan dengan bintang ini.</p>
                        </div>
                    </div>

                </div>

                <div class="modal-footer px-4 pb-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal"
                        style="border-radius:10px;border:1.5px solid var(--border);font-size:.9rem">
                        Tutup
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- MODAL BERI RATING (hanya kalau sudah login) -->
    <?php if($sudahLogin): ?>
    <div class="modal fade" id="modalRating" tabindex="-1" aria-labelledby="modalRatingLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:16px;border:1px solid var(--border)">
                <div class="modal-header px-4 pt-4">
                    <h5 class="modal-title fw-semibold" id="modalRatingLabel"
                        style="font-family:'Playfair Display',serif;color:var(--brown)">
                        <i class="bi bi-star-fill me-2" style="color:var(--orange)"></i>Beri Rating
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body px-4 py-3">
                        <p class="mb-3" style="font-size:.875rem;color:var(--muted)">
                            Bagaimana pengalaman kamu di <strong
                                style="color:var(--brown)"><?= htmlspecialchars($row['nama']) ?></strong>?
                        </p>
                        <div class="mb-3 text-center">
                            <label class="form-label d-block mb-2">Pilih Rating</label>
                            <div class="star-input">
                                <input type="radio" name="rating_value" id="s5" value="5" required /><label for="s5"><i
                                        class="bi bi-star-fill"></i></label>
                                <input type="radio" name="rating_value" id="s4" value="4" /><label for="s4"><i
                                        class="bi bi-star-fill"></i></label>
                                <input type="radio" name="rating_value" id="s3" value="3" /><label for="s3"><i
                                        class="bi bi-star-fill"></i></label>
                                <input type="radio" name="rating_value" id="s2" value="2" /><label for="s2"><i
                                        class="bi bi-star-fill"></i></label>
                                <input type="radio" name="rating_value" id="s1" value="1" /><label for="s1"><i
                                        class="bi bi-star-fill"></i></label>
                            </div>
                            <small id="ratingLabel" style="color:var(--muted);font-size:.8rem">Belum dipilih</small>
                        </div>
                        <div class="mb-1">
                            <label class="form-label">Ulasan <span
                                    style="color:var(--muted);font-weight:400">(opsional)</span></label>
                            <textarea name="ulasan" class="form-control" rows="3"
                                placeholder="Ceritakan pengalaman kamu..."
                                style="border-radius:10px;border:1.5px solid var(--border);font-size:.875rem"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer px-4 pb-4 gap-2">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal"
                            style="border-radius:10px;border:1.5px solid var(--border);font-size:.9rem">Batal</button>
                        <button type="submit" name="kirim_rating" class="btn btn-rating px-4 py-2">
                            <i class="bi bi-send-fill me-1"></i> Kirim Rating
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- MODAL PERINGATAN BELUM LOGIN -->
    <?php if(!$sudahLogin): ?>
    <div class="modal fade" id="modalLoginDulu" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:380px">
            <div class="modal-content" style="border-radius:16px;border:1px solid var(--border)">
                <div class="modal-body px-4 py-4 text-center">
                    <div
                        style="width:60px;height:60px;background:linear-gradient(135deg,var(--orange),var(--orange-light));border-radius:16px;font-size:26px;box-shadow:0 4px 16px rgba(244,123,32,.35);display:flex;align-items:center;justify-content:center;margin:0 auto 16px">
                        <i class="bi bi-lock-fill text-white fs-4"></i>
                    </div>
                    <h5 class="fw-semibold mb-2" style="font-family:'Playfair Display',serif;color:var(--brown)">
                        Kamu Harus Login Dulu!
                    </h5>
                    <p class="mb-4" style="font-size:.875rem;color:var(--muted);line-height:1.6">
                        Untuk memberikan rating dan ulasan, silakan login terlebih dahulu.
                    </p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal"
                            style="border-radius:10px;border:1.5px solid var(--border);font-size:.9rem">
                            Nanti Saja
                        </button>
                        <a href="login.php?redirect=detail_restoran.php?id=<?= $id ?>" class="btn btn-rating px-4 py-2">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Login Sekarang
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <footer class="site-footer text-center py-3">
        &copy; 2025 Restoran Bali — Jelajahi Kuliner Terbaik Pulau Dewata
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Label teks rating bintang
    const labelTeks = {
        1: 'Sangat Buruk 😞',
        2: 'Buruk 😕',
        3: 'Cukup 😐',
        4: 'Bagus 😊',
        5: 'Sangat Bagus 🤩'
    };
    document.querySelectorAll('input[name="rating_value"]').forEach(input => {
        input.addEventListener('change', () => {
            document.getElementById('ratingLabel').textContent = labelTeks[input.value];
            document.getElementById('ratingLabel').style.color = 'var(--orange)';
        });
    });

    // ── Filter ulasan berdasarkan bintang ──
    function filterUlasan(bintang, btn) {

        // Update tombol aktif
        document.querySelectorAll('.filter-bintang button').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        // Ambil semua card ulasan
        const cards = document.querySelectorAll('#listSemuaUlasan .card-ulasan');
        let tampil = 0;

        cards.forEach(card => {
            // Ambil nilai rating dari atribut data-rating
            const rating = parseInt(card.getAttribute('data-rating'));

            if (bintang === 0 || rating === bintang) {
                // Tampilkan kalau cocok atau filter "Semua"
                card.style.display = '';
                tampil++;
            } else {
                // Sembunyikan kalau tidak cocok
                card.style.display = 'none';
            }
        });

        // Update counter
        document.getElementById('filterCount').textContent = tampil;

        // Tampilkan pesan kosong kalau tidak ada hasil
        document.getElementById('emptyFilter').style.display = tampil === 0 ? '' : 'none';
    }
    </script>

</body>

</html>