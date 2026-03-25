<?php 
session_start();
$active = '';
include "config/koneksi.php";

$query = mysqli_query($conn, "
SELECT 
restoran.*,
lokasi.nama_lokasi,
COALESCE(AVG(rating.rating),0) AS rata_rating,
COUNT(rating.id) AS total_review
FROM restoran
LEFT JOIN lokasi
ON lokasi.id = restoran.lokasi
LEFT JOIN rating 
ON rating.id_restoran = restoran.id
GROUP BY restoran.id
ORDER BY rata_rating DESC
LIMIT 3
");

if(!$query){
    die(mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Restoran Bali - Temukan Kuliner Terbaik</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap"
        rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
    :root {
        --orange: #ff6b35;
        --orange2: #f7931e;
        --border: rgba(244, 123, 32, 0.2);
        --muted: #9B7B6A;
    }

    body {
        font-family: 'Inter', sans-serif;
        background: #fff;
    }

    /* Hero */
    .hero {
        background: url('assets/image.png') center/cover no-repeat;
        border-radius: 20px;
        padding: 4rem 2rem;
    }

    .hero h2 {
        color: var(--orange);
        font-size: 2.5rem;
        -webkit-text-stroke: .3px white;
        font-family: 'Playfair Display', serif;
    }

    .hero p {
        color: #fff;
        font-size: 1.1rem;
    }

    /* Section title */
    .section-title h3 {
        font-size: 2rem;
        font-family: 'Playfair Display', serif;
        color: #333;
    }

    .section-title p {
        color: #666;
    }

    /* Restaurant card */
    .restaurant-card {
        background: #fff;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(255, 107, 53, .1);
        transition: .3s;
        height: 500px;
    }

    .restaurant-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(255, 107, 53, .2);
    }

    .restaurant-image {
        height: 250px;
        overflow: hidden;
    }

    .restaurant-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .restaurant-info h4 {
        color: var(--orange);
        font-size: 1.4rem;
    }

    .restaurant-rating {
        color: var(--orange2);
        font-weight: 600;
    }

    /* Buttons */
    .btn-detail,
    .btn-view-more {
        width: 100%;
        padding: .8rem;
        background: linear-gradient(135deg, var(--orange) 0%, var(--orange2) 100%);
        color: #fff;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: .3s;
    }

    .btn-detail:hover,
    .btn-view-more:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(255, 107, 53, .3);
    }

    /* Footer */
    .site-footer {
        font-size: .78rem;
        color: var(--muted);
        border-top: 1px solid var(--border);
    }
    </style>
</head>

<body>

    <!-- Header -->
    <?php include 'header.php'; ?>

    <!-- Navbar -->
    <nav>
        <div class="container">
            <div class="d-flex justify-content-center flex-wrap nav-container">
                <a href="#" class="active">Beranda</a>
                <a href="restaurant.php">Restoran</a>
                <a href="#">Tentang</a>
                <a href="#">Kontak</a>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <div class="container mt-3">
        <section class="hero text-center">
            <h2>Nikmati Kelezatan Kuliner Bali</h2>
            <p>Temukan restoran terbaik dengan pemandangan menakjubkan dan cita rasa autentik khas Pulau Dewata</p>
        </section>
    </div>

    <!-- Restoran Populer -->
    <section class="py-5">
        <div class="container">

            <div class="section-title text-center mb-5">
                <h3>Restoran Populer di Bali</h3>
                <p>Pilihan restoran terbaik dengan rating tertinggi</p>
            </div>

            <div class="row g-4 justify-content-center">
                <?php while ($row = mysqli_fetch_assoc($query)): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="restaurant-card">

                        <div class="restaurant-image">
                            <img src="assets/<?= $row['gambar'] ?>" alt="<?= $row['nama'] ?>" />
                        </div>

                        <div class="restaurant-info p-4">
                            <h4 class="mb-1"><?= $row['nama'] ?></h4>

                            <p class="mb-2" style="color:#666;font-size:.9rem">
                                <i class="bi bi-geo-alt-fill me-1"
                                    style="color:var(--orange)"></i><?= $row['nama_lokasi'] ?>
                            </p>

                            <p class="mb-2 restaurant-rating">
                                <i class="bi bi-star-fill me-1"></i>
                                <?= $row['rata_rating'] ? round($row['rata_rating'],1) : '0.0' ?>/5
                                <span style="font-weight:400;color:#888;font-size:.85rem">(<?= $row['total_review'] ?>
                                    review)</span>
                            </p>

                            <p class="restaurant-description mb-3" style="color:#555;font-size:.875rem;line-height:1.6">
                                <?= $row['deskripsi'] ?>
                            </p>

                            <a href="detail_restoran.php?id=<?= $row['id']; ?>" style="text-decoration:none">
                                <button class="btn-detail">Lihat Detail</button>
                            </a>
                        </div>

                    </div>
                </div>
                <?php endwhile; ?>
            </div>

            <div class="text-center mt-5">
                <a href="restaurant.php" style="text-decoration:none">
                    <button class="btn-view-more" style="max-width:300px">Lihat Semua Restoran</button>
                </a>
            </div>

        </div>
    </section>

    <!-- Footer -->
    <footer class="site-footer text-center py-3">
        &copy; 2025 Restoran Bali — Jelajahi Kuliner Terbaik Pulau Dewata
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>