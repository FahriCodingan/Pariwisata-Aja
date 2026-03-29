<?php 
include '../config/koneksi.php';

$query = "SELECT * FROM users";
$result = mysqli_query($conn, $query);
if(!$result) {
    die("Query Error: " . mysqli_error($conn));
}

// Menghapus Data Siswa Eskul
if(isset($_POST['delete'])){
    $id = $_POST['id_user'];
    
    $deleteQuery = "DELETE FROM users WHERE id_user = $id";
    $deleteResult = mysqli_query($conn, $deleteQuery);
    if($deleteResult) {
        header("Location: PageUser.php");
        exit();
    } else {
        die("Error deleting record: " . mysqli_error($conn));
    }
}

if(isset($_POST['UpdateUser'])){
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];

    $updateQuery = "UPDATE users SET username='$nama', email='$email' WHERE id_user=$id";
    $updateResult = mysqli_query($conn, $updateQuery);
    if($updateResult) {
        header("Location: PageUser.php");
        exit();
    } else {
        die("Error updating record: " . mysqli_error($conn));
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Data Users</title>
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
                            <a href="admin.php" class="text-secondary nav-link">
                                <b>Home</b>
                            </a>
                        </li>
                        <li class="nav-link" aria-current="page">
                            <a href="PageUser.php" class="text-dark nav-link">
                                <b>User</b>
                            </a>
                        </li>
                        <li class="nav-link" aria-current="page">
                            <a href="PageRestaurant.php" class="text-secondary  nav-link">
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
    <div class="card p-4 pb-2 m-3 rounded-3 shadow-sm">
        <div class="d-flex mb-5">
            <h2 class="">Data Users</h2>
            <div class="d-flex justify-content-end ms-auto">
                <a href="admin.php" class="btn btn-warning text-light ps-3 pe-3">
                    <i class="fa fa-arrow-left"></i>
                </a>
            </div>
        </div>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= $row['username']; ?></td>
                    <td><?= $row['email']; ?></td>
                    <td>
                        <form method="POST" action="pageUser.php"
                            onsubmit="return confirm('Yakin ingin menghapus Users ini?')">
                            <input type="hidden" name="id_user" value="<?= $row['id_user'] ?>">
                            <button type="submit" name="delete" class="btn btn-danger">
                                <i class="bi bi-trash me-1"></i>Hapus
                            </button>

                            <button type="button" class="btn btn-primary editBtnUsers" data-id="<?= $row['id_user'] ?>"
                                data-nm="<?= htmlspecialchars($row['username']) ?>"
                                data-email="<?= htmlspecialchars($row['email']) ?>"
                                data-password="<?= htmlspecialchars($row['password']) ?>" data-bs-toggle="modal"
                                data-bs-target="#editModalUsers">
                                Edit
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <!-- Modal Edit User -->
    <div class="modal fade" id="editModalUsers" tabindex="-1">
        <div class="modal-dialog">
            <form method="post">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <input type="hidden" name="id" id="modal_id_Users">

                        <label>Username</label>
                        <input type="text" class="form-control mb-3" name="nama" id="modal_namaUsers">

                        <label>Email</label>
                        <input type="email" class="form-control mb-3" name="email" id="modal_emailUsers">

                        <label>Password</label>
                        <input type="text" class="form-control mb-3" name="password" id="modal_passwordUsers">


                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <input type="submit" name="UpdateUser" class="btn btn-primary" value="Simpan">
                    </div>
                </div>

            </form>
        </div>
    </div>
</body>
<script>
document.querySelectorAll('.editBtnUsers').forEach(btn => {
    btn.addEventListener('click', function() {

        document.getElementById('modal_id_Users').value = this.dataset.id;
        document.getElementById('modal_namaUsers').value = this.dataset.nm;
        document.getElementById('modal_emailUsers').value = this.dataset.email;
        document.getElementById('modal_passwordUsers').value = this.dataset.password;

    });
});
</script>

</html>