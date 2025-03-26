<?php
session_start();
require 'db.php'; 


$username = trim($_POST['username']);
$preferred_city = trim($_POST['preferred_city']);


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id']; 


$stmt = $conn->prepare("UPDATE users SET username = ?, preferred_city = ? WHERE id = ?");
$stmt->bind_param("ssi", $username, $preferred_city, $user_id);

if ($stmt->execute()) {
    
    $_SESSION['username'] = $username;
    $_SESSION['preferred_city'] = $preferred_city;

    
    echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
}

$stmt->close();
$conn->close();
?>
