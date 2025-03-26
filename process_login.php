<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']); 
    $password = trim($_POST['password']); 

    
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    
    if ($user && password_verify($password, $user['password'])) {
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_type'] = $user['user_type'];
        $_SESSION['preferred_city'] = $user['preferred_city'];

        
        if ($user['user_type'] === 'Admin') {
            header('Location: realtime-admin.php');
        } else {
            header('Location: realtime-user.php');
        }
        exit;
    } else {
        
        echo "<script>alert('Invalid email or password. Please try again.'); window.history.back();</script>";
        exit;
    }
}
?>
