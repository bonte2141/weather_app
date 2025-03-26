<?php
require 'db.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $user_type = isset($_POST['user_type']) ? $_POST['user_type'] : 'User';
    
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match.'); window.history.back();</script>";
        exit;
    }

    if (strlen($password) < 6) {
        echo "<script>alert('Password must be at least 6 characters long.'); window.history.back();</script>";
        exit;
    }
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Username or email already exists. Please choose another.'); window.history.back();</script>";
        exit;
    }
    
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, user_type, preferred_city) VALUES (?, ?, ?, ?, NULL)");
    $stmt->bind_param("ssss", $username, $email, $hashedPassword, $user_type);

    if ($stmt->execute()) {
        echo "<script>alert('Account created successfully! Redirecting to login page.'); window.location.href = 'login.php';</script>";
        exit;
    } else {
        echo "<script>alert('An error occurred. Please try again.'); window.history.back();</script>";
        exit;
    }
}

?>
