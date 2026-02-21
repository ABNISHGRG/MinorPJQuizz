<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error_msg = "";

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include_once '../app/user.php';
    
    $user = new User();
    $user->email = $_POST['email'];
    $user->password = $_POST['password'];
    
    if ($user->login()) {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_name'] = $user->name;
        $_SESSION['user_role'] = $user->role;
        
        // Remove guest ID
        if(isset($_SESSION['guest_id'])) {
            unset($_SESSION['guest_id']);
        }
        
        header("Location: ../index.php");
        exit();
    } else {
        $error_msg = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
            <div class="col-sm-6">
                <div class="card p-4 shadow">
                    <h2 class="text-center">Login</h2>
                    
                    <?php if($error_msg): ?>
                        <div class="alert alert-danger"><?php echo $error_msg; ?></div>
                    <?php endif; ?>
                    
                    <form action="login.php" method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                        Don't have an account? <a href="register.php">Register</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>