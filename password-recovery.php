<?php
date_default_timezone_set('Europe/Bucharest');
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
            <h1 class="text-center forecast-title">Password Recovery</h1>
            <div class="login-card">
                <p class="text-center mb-4 fw-bold fs-5">
                    Enter your email to reset your password. We will send you a reset link if your account exists.
                </p>
                <form action="process_password_recovery.php" method="POST">
                    <div class="mb-4">
                        <label for="email" class="form-label fw-bold fs-5">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                    </div>
                    <button type="submit" class="btn btn-login">Send Reset Link</button>
                </form>
            </div>

        </div>
    </div>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>