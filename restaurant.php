<?php
session_start();
$active = 'restaurant';
include "config/koneksi.php";

$query = mysqli_query($conn, "
SELECT 
    restoran.*,
    jadwal.*,
    lokasi.nama_lokasi,
    COALESCE(r.rata_rating,0) AS rating,
    COALESCE(r.total_review,0) AS total_review
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
ORDER BY rating DESC
");

$restaurants = [];
while($row = mysqli_fetch_assoc($query)){
    $restaurants[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Restoran di Bali - Wisata Kuliner</title>
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
        --primary: #E67E22;
        --primary-dark: #D35400;
    }

    body {
        font-family: 'Inter', sans-serif;
        background: var(--cream);
        color: var(--text);
    }

    /* Header */
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
        font-weight: bold;
    }

    /* Button Login Dan Register */
    .btn-login {
        background: white;
        color: #ff6b35;
        border-radius: 25px;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        border: 2px solid #ff6b35;
        transition: 0.3s;
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
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        transition: 0.3s;
    }

    .btn-register:hover {
        background: white;
        color: #ff6b35;
    }

    .search-box {
        position: relative;
    }

    /* Search Box */
    .search-box input {
        padding: 0.8rem 3rem 0.8rem 1.2rem;
        border: none;
        border-radius: 25px;
        font-size: 1rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .search-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #ff6b35;
        font-size: 1.2rem;
    }

    /* Navigasi Bar */
    nav {
        background: #fff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .nav-container a {
        color: #555;
        text-decoration: none;
        font-weight: 500;
        padding: 1.2rem 2.5rem;
        border-bottom: 3px solid transparent;
        transition: all 0.3s;
    }

    .nav-container a:hover,
    .nav-container a.active {
        color: #ff6b35;
        background: #fff5f0;
        border-bottom-color: #ff6b35;
    }

    /* Breadcrumb */
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

    /* ── PAGE HEADER ── */
    .page-title {
        font-family: 'Playfair Display', serif;
        font-size: 2.2rem;
        color: var(--brown);
    }

    .info-icon {
        display: inline-block;
        width: 18px;
        height: 18px;
        background: var(--muted);
        color: #fff;
        border-radius: 50%;
        text-align: center;
        line-height: 18px;
        font-size: .72rem;
        margin-left: 5px;
        cursor: help;
    }

    /* ── FILTER CHIPS ── */
    .chip {
        padding: 7px 18px;
        border: 2px solid var(--border);
        border-radius: 20px;
        background: #fff;
        cursor: pointer;
        transition: all .2s;
        font-size: .88rem;
        font-weight: 500;
        color: var(--text);
    }

    .chip:hover {
        border-color: var(--primary);
        color: var(--primary);
    }

    .chip.active {
        background: var(--primary);
        color: #fff;
        border-color: var(--primary);
    }

    .sort-select {
        padding: 7px 14px;
        border: 2px solid var(--border);
        border-radius: 8px;
        font-size: .88rem;
        font-family: 'Inter', sans-serif;
        color: var(--text);
        background: #fff;
        cursor: pointer;
    }

    .sort-select:focus {
        outline: none;
        border-color: var(--primary);
    }

    /* ── RESTAURANT CARD ── */
    .restaurant-card {
        background: #fff;
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid var(--border);
        box-shadow: 0 2px 10px rgba(244, 123, 32, .07);
        transition: all .3s;
        display: grid;
        grid-template-columns: 280px 1fr;
    }

    .restaurant-card:hover {
        box-shadow: 0 8px 24px rgba(244, 123, 32, .15);
        transform: translateY(-2px);
    }

    .card-image {
        width: 100%;
        height: 100%;
        min-height: 220px;
        max-height: 260px;
        object-fit: cover;
    }

    .card-body-inner {
        padding: 22px 26px;
        display: flex;
        flex-direction: column;
    }

    /* Rating badge */
    .badge-rating {
        background: #F59E0B;
        color: #fff;
        border-radius: 20px;
        font-size: .82rem;
        padding: 5px 12px;
        font-weight: 600;
    }

    /* Info tags */
    .tag-item {
        font-size: .85rem;
        color: var(--muted);
    }

    .tag-item i {
        color: var(--orange);
    }

    /* Tombol detail */
    .btn-detail {
        background: linear-gradient(135deg, var(--orange), var(--orange-light));
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: .85rem;
        font-weight: 500;
        padding: 7px 18px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: transform .15s, box-shadow .15s;
        box-shadow: 0 3px 10px rgba(244, 123, 32, .3);
    }

    .btn-detail:hover {
        transform: translateY(-1px);
        box-shadow: 0 5px 14px rgba(244, 123, 32, .4);
        color: #fff;
    }

    /* Status jam */
    .status-open {
        color: #16A085;
        font-size: .85rem;
        font-weight: 600;
    }

    .status-closed {
        color: #E74C3C;
        font-size: .85rem;
        font-weight: 600;
    }

    /* Footer */
    .site-footer {
        font-size: .78rem;
        color: var(--muted);
        border-top: 1px solid var(--border);
    }

    /* Responsive */
    @media (max-width:768px) {
        .restaurant-card {
            grid-template-columns: 1fr;
        }

        .card-image {
            height: 200px;
            min-height: unset;
        }
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
                <!-- <a href="#">Kategori</a> -->
                <a href="#">Tentang</a>
                <a href="#">Kontak</a>
            </div>
        </div>
    </nav>

    <div class="container py-3">

        <!-- Breadcrumb -->
        <div aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php">Beranda</a></li>
                <li class="breadcrumb-item active">Restoran</li>
            </ol>
        </div>

    </div>

    <!-- ======================================================= -->
    <!-- KONTEN UTAMA                                             -->
    <!-- ======================================================= -->
    <div class="container pb-5">

        <!-- Page Header -->
        <div class="mb-4">
            <h1 class="page-title">Restoran di Bali</h1>
            <p class="mb-0" style="color:var(--muted);font-size:.95rem">
                Restoran terpopuler di Bali
                <span class="info-icon" title="Berdasarkan rating dan ulasan">ⓘ</span>
            </p>
        </div>

        <!-- Filter & Sort -->
        <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
            <span class="fw-semibold" style="color:var(--text)">Filter:</span>
            <div class="d-flex flex-wrap gap-2" id="filterChips">
                <button class="chip active" data-filter="all">Semua</button>
                <button class="chip" data-filter="Lokal">Lokal</button>
                <button class="chip" data-filter="Masakan Bali">Masakan Bali</button>
                <button class="chip" data-filter="Masakan Jogja">Masakan Jogja</button>
            </div>
            <div class="ms-auto">
                <select class="sort-select" id="sortSelect">
                    <option value="rating">Rating Tertinggi</option>
                    <option value="price-low">Harga Termurah</option>
                    <option value="price-high">Harga Termahal</option>
                    <option value="name">Nama A-Z</option>
                </select>
            </div>
        </div>

        <p class="mb-3" style="color:var(--muted);font-size:.9rem">
            <span id="resultCount">0</span> hasil
        </p>

        <!-- Daftar Restoran -->
        <div class="d-flex flex-column gap-4" id="restaurantList"></div>

    </div>

    <!-- ======================================================= -->
    <!-- FOOTER                                                   -->
    <!-- ======================================================= -->
    <footer class="site-footer text-center py-3">
        &copy; 2025 Restoran Bali — Jelajahi Kuliner Terbaik Pulau Dewata
    </footer>

    <script>
    const restaurants = <?php echo json_encode($restaurants); ?>;
    let filteredRestaurants = [...restaurants];

    function renderRestaurants(list) {
        const container = document.getElementById('restaurantList');
        if (list.length === 0) {
            container.innerHTML = `
          <div class="text-center py-5" style="color:var(--muted)">
            <i class="bi bi-search" style="font-size:2.5rem;opacity:.4"></i>
            <p class="mt-3">Tidak ada restoran ditemukan.</p>
          </div>`;
            document.getElementById('resultCount').textContent = 0;
            return;
        }

        container.innerHTML = list.map(r => {
            const rating = parseFloat(r.rating).toFixed(1);
            const stars = renderStars(parseFloat(r.rating));
            return `
          <div class="restaurant-card">
            <img src="assets/${r.gambar}" class="card-image" alt="${r.nama}" />
            <div class="card-body-inner">

              <!-- Nama & Lokasi -->
              <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                <h3 class="mb-0 fw-bold" style="font-size:1.3rem;color:var(--brown)">${r.nama}</h3>
                <span class="tag-item"><i class="bi bi-geo-alt-fill me-1"></i>${r.nama_lokasi}</span>
              </div>

              <!-- Rating -->
              <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge-rating">
                  <i class="bi bi-star-fill me-1"></i>${rating}
                  <span style="opacity:.85;font-weight:400">(${r.total_review} review)</span>
                </span>
                <span style="color:#F59E0B;font-size:.85rem">${stars}</span>
              </div>

              <!-- Tags -->
              <div class="d-flex flex-wrap gap-3 mb-3">
                <span class="tag-item"><i class="bi bi-tag-fill me-1"></i>${r.kategori}</span>
                <span class="tag-item"><i class="bi bi-cash me-1"></i>Rp ${Number(r.harga).toLocaleString('id-ID')}</span>
              </div>

              <!-- Deskripsi -->
              <p class="mb-3 flex-grow-1" style="font-size:.875rem;color:var(--muted);line-height:1.65">
                ${r.deskripsi}
              </p>

              <!-- Footer card -->
              <div class="d-flex justify-content-between align-items-center pt-3" style="border-top:1px solid var(--border)">
                <span class="status-open">
                  <i class="bi bi-clock-fill me-1"></i>${r.jam_buka} – ${r.jam_tutup}
                </span>
                <a href="detail_restoran.php?id=${r.id}" class="btn-detail">
                  Lihat Detail <i class="bi bi-arrow-right"></i>
                </a>
              </div>

            </div>
          </div>`;
        }).join('');

        document.getElementById('resultCount').textContent = list.length;
    }

    function renderStars(rating) {
        let html = '';
        for (let i = 1; i <= 5; i++) {
            if (rating >= i) html += '<i class="bi bi-star-fill"></i>';
            else if (rating >= i - 0.6) html += '<i class="bi bi-star-half"></i>';
            else html += '<i class="bi bi-star"></i>';
        }
        return html;
    }

    // Filter chips
    document.querySelectorAll('.chip').forEach(chip => {
        chip.addEventListener('click', function() {
            document.querySelectorAll('.chip').forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            const filter = this.dataset.filter;
            filteredRestaurants = filter === 'all' ? [...restaurants] :
                restaurants.filter(r => r.kategori === filter);
            renderRestaurants(filteredRestaurants);
        });
    });

    // Search
    document.getElementById('searchInput').addEventListener('input', function() {
        const term = this.value.toLowerCase();
        const filtered = restaurants.filter(r =>
            r.nama.toLowerCase().includes(term) ||
            r.deskripsi.toLowerCase().includes(term) ||
            r.kategori.toLowerCase().includes(term) ||
            r.lokasi.toLowerCase().includes(term)
        );
        renderRestaurants(filtered);
    });

    // Sort
    document.getElementById('sortSelect').addEventListener('change', function() {
        let sorted = [...filteredRestaurants];
        switch (this.value) {
            case 'rating':
                sorted.sort((a, b) => b.rating - a.rating);
                break;
            case 'price-low':
                sorted.sort((a, b) => a.harga - b.harga);
                break;
            case 'price-high':
                sorted.sort((a, b) => b.harga - a.harga);
                break;
            case 'name':
                sorted.sort((a, b) => a.nama.localeCompare(b.nama));
                break;
        }
        renderRestaurants(sorted);
    });

    // Initial render
    document.getElementById('sortSelect').dispatchEvent(new Event('change'));
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>