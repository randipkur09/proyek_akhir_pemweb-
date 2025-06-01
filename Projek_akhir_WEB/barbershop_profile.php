<?php
require_once 'database.php';

if (!isBarbershop()) {
    redirect('login.php');
}

// Ambil data barbershop
$stmt = $pdo->prepare("SELECT * FROM barbershops WHERE id = ?");
$stmt->execute([$_SESSION['barbershop_id']]);
$barbershop = $stmt->fetch();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_barbershop = $_POST['nama_barbershop'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];
    $jam_buka = $_POST['jam_buka'];
    $jam_tutup = $_POST['jam_tutup'];
    $harga_potong = $_POST['harga_potong'];
    $deskripsi = $_POST['deskripsi'];
    
    try {
        $stmt = $pdo->prepare("UPDATE barbershops SET nama_barbershop = ?, alamat = ?, no_hp = ?, jam_buka = ?, jam_tutup = ?, harga_potong = ?, deskripsi = ? WHERE id = ?");
        $stmt->execute([$nama_barbershop, $alamat, $no_hp, $jam_buka, $jam_tutup, $harga_potong, $deskripsi, $_SESSION['barbershop_id']]);
        
        $_SESSION['barbershop_name'] = $nama_barbershop;
        $success = 'Profil berhasil diperbarui!';
        
        // Refresh data
        $stmt = $pdo->prepare("SELECT * FROM barbershops WHERE id = ?");
        $stmt->execute([$_SESSION['barbershop_id']]);
        $barbershop = $stmt->fetch();
    } catch (PDOException $e) {
        $error = 'Terjadi kesalahan: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Barbershop - BarberBook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="../index.php">BarberBook</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="dashboard.php">Dashboard</a>
                <a class="nav-link" href="profile.php">Profil</a>
                <a class="nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Profil Barbershop</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?= $success ?></div>
                        <?php endif; ?>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Nama Barbershop</label>
                                <input type="text" name="nama_barbershop" class="form-control" 
                                       value="<?= htmlspecialchars($barbershop['nama_barbershop']) ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" 
                                       value="<?= htmlspecialchars($barbershop['email']) ?>" disabled>
                                <small class="text-muted">Email tidak dapat diubah</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea name="alamat" class="form-control" rows="3" required><?= htmlspecialchars($barbershop['alamat']) ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">No. HP</label>
                                <input type="text" name="no_hp" class="form-control" 
                                       value="<?= htmlspecialchars($barbershop['no_hp']) ?>">
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Jam Buka</label>
                                    <input type="time" name="jam_buka" class="form-control" 
                                           value="<?= $barbershop['jam_buka'] ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Jam Tutup</label>
                                    <input type="time" name="jam_tutup" class="form-control" 
                                           value="<?= $barbershop['jam_tutup'] ?>" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Harga Potong Rambut</label>
                                <input type="number" name="harga_potong" class="form-control" 
                                       value="<?= $barbershop['harga_potong'] ?>" required>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Deskripsi</label>
                                <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($barbershop['deskripsi']) ?></textarea>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="barbershop_dashboard.php" class="btn btn-outline-secondary me-md-2">Kembali</a>
                                <button type="submit" class="btn btn-dark">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>