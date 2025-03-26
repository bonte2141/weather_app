<?php
require 'db.php'; 
session_start();

header('Content-Type: application/json');


$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated.']);
    exit;
}


try {
    $sql = "SELECT id, name, continent FROM countries ORDER BY name ASC";
    $result = $conn->query($sql);

    $countries = [];
    while ($row = $result->fetch_assoc()) {
        $countries[] = $row;
    }

    echo json_encode(['success' => true, 'countries' => $countries]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
