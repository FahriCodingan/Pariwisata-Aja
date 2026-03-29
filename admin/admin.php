<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>👩🏻‍🎓 Dashboard Admin</title>
</head>

<body class="bg-secondary-subtle">
    <div class="">
        <nav class=" navbar navbar-expand-lg bg-body-tertiary bg-light shadow-sm">
            <div class="container-fluid">
                <a class="navbar-brand" href="#"><b>Dashboard Admin</b></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-link" aria-current="page">
                            <a href="admin.php" class="text-dark nav-link">
                                <b>Home</b>
                            </a>
                        </li>
                        <li class="nav-link" aria-current="page">
                            <a href="PageUser.php" class="text-secondary  nav-link">
                                <b>User</b>
                            </a>
                        </li>
                        <li class="nav-link" aria-current="page">
                            <a href="PageRestaurant.php" class="text-secondary nav-link">
                                <b>Restaurant</b>
                            </a>
                        </li>
                        <li class="nav-link" aria-current="page">
                            <a href="PageJadwal.php" class="text-secondary nav-link">
                                <b>Jadwal</b>
                            </a>
                        </li>
                        <li class="nav-link" aria-current="page">
                            <a href="PageLokasi.php" class="text-secondary nav-link">
                                <b>Lokasi</b>
                            </a>
                        </li>
                        <li class="nav-link">
                            <a href="logout.php" class="text-secondary nav-link">
                                Log Out
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
    <div class="card p-4 pb-1 m-3 rounded-3 shadow-sm">
        <h3 class="mb-4 text-center" style="font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;">
            Selamat Datang di Dashboard Admin Restoran Bali
        </h3>
    </div>
</body>

</html>