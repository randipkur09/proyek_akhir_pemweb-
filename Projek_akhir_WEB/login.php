<?php
require_once 'database.php';

if (isLoggedIn()) {
    if (isCustomer()) {
        redirect('customer_dashboard.php');
    } else {
        redirect('barbershop_dashboard.php');
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    
    if ($role == 'customer') {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nama'];
            redirect('customer_dashboard.php');
        } else {
            $error = 'Email atau password salah!';
        }
    } else {
        $stmt = $pdo->prepare("SELECT * FROM barbershops WHERE email = ?");
        $stmt->execute([$email]);
        $barbershop = $stmt->fetch();
        
        if ($barbershop && password_verify($password, $barbershop['password'])) {
            $_SESSION['barbershop_id'] = $barbershop['id'];
            $_SESSION['barbershop_name'] = $barbershop['nama_barbershop'];
            redirect('barbershop_dashboard.php');
        } else {
            $error = 'Email atau password salah!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BarberBook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold">Login</h2>
                            <p class="text-muted">Masuk ke akun Anda</p>
                        </div>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Role</label>
                                <select name="role" class="form-select" required>
                                    <option value="">Pilih Role</option>
                                    <option value="customer">Pelanggan</option>
                                    <option value="barbershop">Barbershop</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            
                            <button type="submit" class="btn btn-dark w-100 mb-3">Login</button>
                        </form>
                        
                        <div class="text-center">
                            <p class="mb-0">Belum punya akun? <a href="register.php" class="text-decoration-none">Daftar di sini</a></p>
                            <a href="index.php" class="text-muted text-decoration-none">← Kembali ke Beranda</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>