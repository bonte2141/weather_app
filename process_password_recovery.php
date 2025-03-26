<?php
date_default_timezone_set(timezoneId: 'Europe/Bucharest');

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        
        $user = $result->fetch_assoc();
        $token = bin2hex(random_bytes(16)); 
        $expires = date("Y-m-d H:i:s", time() + 3600); 


        
        $stmt = $conn->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user['id'], $token, $expires);
        $stmt->execute();

        
        echo "<script>alert('A reset link has been generated. Use the link to reset your password: http://localhost/weather_app/reset-password.php?token=$token'); window.location.href = 'login.php';</script>";
        exit;
    } else {
        echo "<script>alert('This email does not exist.'); window.location.href = 'password-recovery.php';</script>";
        exit;
    }
}
?>
