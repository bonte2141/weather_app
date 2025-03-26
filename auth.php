<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include 'db.php';

try {
    $userId = $_SESSION['user_id'];

    
    $stmt = $conn->prepare("
        SELECT c.name 
        FROM favorite_cities fc
        JOIN cities c ON fc.city_id = c.id
        WHERE fc.user_id = ?
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $otherCities = [];
    while ($row = $result->fetch_assoc()) {
        $otherCities[] = $row['name'];
    }

    
    $_SESSION['other_cities'] = $otherCities;

    $stmt->close();
} catch (Exception $e) {
    error_log('Error in auth.php: ' . $e->getMessage());
}

?>
