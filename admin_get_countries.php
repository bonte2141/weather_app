<?php
require 'db.php';  
session_start();

header('Content-Type: application/json');


if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Access denied.']);
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
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
