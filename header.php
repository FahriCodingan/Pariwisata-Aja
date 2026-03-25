<link rel="stylesheet" href="style.css">
<!-- Header -->
<header class="py-4">
    <div class="container">
        <div class="row align-items-center gy-3">
            <div class="col-md-6 d-flex align-items-center gap-3">
                <div class="profile-icon d-flex align-items-center justify-content-center">
                    <i class="bi bi-egg-fried"></i>
                </div>
                <div class="site-title">
                    <h1>Restoran Bali</h1>
                    <p>Jelajahi Kuliner Terbaik Pulau Dewata</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-center gap-3">
                    <div class="search-box flex-grow-1">
                        <?php 
                        if($active == 'restaurant'): ?>
                        <input type="text" id="searchInput" class="w-100" placeholder="Cari restoran di Bali...">
                        <span class="search-icon"><i class="bi bi-search"></i></span>
                        <?php endif; ?>
                    </div>
                    <?php if(isset($_SESSION['login'])): ?>

                    <div class="dropdown">
                        <button class="btn btn-login dropdown-toggle d-flex align-items-center gap-2"
                            data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i>
                            <?= $_SESSION['username']; ?>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="profile.php">
                                    <i class="bi bi-person me-2"></i> Profile
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="reviewsaya.php">
                                    <i class="bi bi-star me-2"></i> Review Saya
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item text-danger" href="logout.php">
                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- <a href="logout.php">
                        <button class="btn btn-login">Logout</button>
                    </a> -->

                    <?php else: ?>

                    <!-- Kalau BELUM LOGIN -->
                    <a href="login.php"><button class="btn btn-login">Login</button></a>
                    <a href="register.php"><button class="btn btn-register">Register</button></a>

                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</header>