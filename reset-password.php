<?php
date_default_timezone_set('Europe/Bucharest');
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['token'])) {
    $token = $_GET['token'];

    
    $stmt = $conn->prepare("SELECT user_id FROM password_resets WHERE token = ? AND expires_at > ?");
    $current_time = date("Y-m-d H:i:s");
    $stmt->bind_param("ss", $token, $current_time);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $reset = $result->fetch_assoc();
        $user_id = $reset['user_id'];
    } else {
        echo "<script>alert('Invalid or expired token.'); window.location.href = 'login.php';</script>";
        exit;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $new_password = trim($_POST['password']);

    
    $stmt = $conn->prepare("SELECT user_id FROM password_resets WHERE token = ? AND expires_at > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $reset = $result->fetch_assoc();
        $user_id = $reset['user_id'];

        if (strlen($new_password) < 6) {
            echo "<script>alert('Password must be at least 6 characters long.'); window.history.back();</script>";
            exit;
        }
        


        
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashed_password, $user_id);
        $stmt->execute();

        
        $stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();

        echo "<script>alert('Password has been reset. Please log in.'); window.location.href = 'login.php';</script>";
        exit;
    } else {
        echo "<script>alert('Invalid or expired token.'); window.location.href = 'login.php';</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ClimaCast Password Recovery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
    <style>
        
        .custom-card {
            max-width: 600px;
            margin: 50px auto;
            padding: 40px;
            background-color: #1c1e21;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            color: #ffffff;
            text-align: center;
        }

        
        .login-card {
            background-color: #181a1d;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
            color: #ffffff;
        }

        
        .form-control {
            width: 60%;
            margin: 0 auto;
            border-radius: 8px;
            font-size: 1.2rem;
            padding: 10px 15px;
        }

        
        .login-card h3 {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .login-card label {
            font-size: 1.2rem;
            font-weight: bold;
        }

        
        .text-link {
            color: #ffffff;
            text-decoration: underline;
        }

        
        .btn-login {
            background-color: #ffffff;
            
            color: #000000;
            
            font-weight: bold;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-size: 1.2rem;
            margin-top: 20px;
            transition: background-color 0.3s, box-shadow 0.3s;
        }

        .btn-login:hover {
            background-color: #e2e6ea;
            
            color: #000000;
            
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transform: scale(1.03);
        }

        .btn-login:focus,
        .btn-login:active {
            outline: none;
            box-shadow: 0 0 0 2px #ccc;
        }
    </style>
</head>
<body style="background-color: #121212; color: #ffffff;">

    
    <nav class="navbar navbar-expand-lg bg-light" style="padding-top: 0px; padding-bottom: 0px;">
        <div class="container-fluid">
            <a class="navbar-brand" href="login.php">
                <img src="climacast.webp" alt="ClimaCast" width="125" height="125" class="d-inline-block align-top">
            </a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a href="login.php" class="btn btn-dark fw-bold me-2" style="font-size: 23px;">Real-Time
                            Weather</a>
                    </li>
                    <li class="nav-item">
                        <a href="login.php" class="btn btn-dark fw-bold me-2" style="font-size: 23px;">Forecast</a>
                    </li>
                    <li class="nav-item">
                        <a href="login.php" class="btn btn-dark fw-bold me-2" style="font-size: 23px;">Locations</a>
                    </li>
                    <li class="nav-item">
                        <a href="login.php" class="btn btn-dark fw-bold me-2" style="font-size: 23px;">Interactive
                            Maps</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="login.php" class="btn btn-dark fw-bold me-2" style="font-size: 23px;">Login</a>
                    <a href="register.html" class="btn btn-dark fw-bold" style="font-size: 23px;">Register</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
    <div class="custom-card">
        <h1 class="text-center fw-bold mb-4">Reset Password</h1>
        <div class="login-card">
            <p class="text-center mb-4 fw-bold fs-5">Enter your new password below to reset your account.</p>
            <form action="reset-password.php" method="POST">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <div class="mb-3">
    <label for="password" class="form-label fw-bold fs-5">New Password</label>
    <input type="password" class="form-control" id="password" name="password" placeholder="Enter your new password" required>
</div>
<div class="mb-3">
    <label for="confirm_password" class="form-label fw-bold fs-5">Confirm Password</label>
    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm your new password" required>
</div>

                <button type="submit" class="btn btn-login">Reset Password</button>
            </form>
        </div>
    </div>
</div>


<script>
    document.querySelector("form").addEventListener("submit", function (event) {
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirm_password").value;

    if (password.length < 6) {
        alert("Password must be at least 6 characters long.");
        event.preventDefault(); 
        return;
    }

    if (password !== confirmPassword) {
        alert("Passwords do not match. Please try again.");
        event.preventDefault(); 
    }
});

</script>

     
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
