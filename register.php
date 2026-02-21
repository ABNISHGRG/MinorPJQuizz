<?php
session_start(); 
include_once '../app/user.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = new User();
    
    // Sanitize inputs slightly for safety
    $user->name = htmlspecialchars($_POST['username']); 
    $user->email = $_POST['email'];
    $user->password = $_POST['password'];

    if ($user->register()) {
        // 1. Log them in immediately
        $_SESSION['user_id'] = $user->id; 
        $_SESSION['user_name'] = $user->name;
        
        // Remove the guest ID now that they are a member
        if(isset($_SESSION['guest_id'])) {
            unset($_SESSION['guest_id']);
        }

        // 2. TRANSFER GUEST DATA
        if (isset($_SESSION['temp_score'])) {
            // If you have the saveScore method ready:
            // $user->saveScore($_SESSION['user_id'], $_SESSION['temp_score']);
            
            unset($_SESSION['temp_score']);
            header("Location: ../index.php?msg=welcome_score_transferred");
        } else {
            header("Location: ../index.php?msg=welcome");
        }
        exit();
    } else {
        // This triggers if the email is taken or DB error occurs
        $error_msg = "Registration failed. This email might already be registered.";
    }
}
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div>
        <?php if (isset($error_msg)): ?>
            <div class="alert alert-danger py-2">
                <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="container">
        <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
            <div class="col-sm-6">
                <div class="card p-4 shadow"> <h2 class="text-center">Register</h2>
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" autocomplete="off">
                         <div class="mb-3"> <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" aria-describedby="emailHelp">
                        </div>
                        <div class="mb-3"> <label for="email" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp">
                            <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
                        </div>
                        <div class="mb-3">
                            <label for="exampleInputPassword1" class="form-label">Password</label>
                            <input type="password" class="form-control" id="exampleInputPassword1" name="password">
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <button type="reset" class="btn btn-secondary">Clear</button>
                        Already have an account?<a href="login.php" class="btn btn-link">  Login</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>