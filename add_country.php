<?php
require 'db.php';  
session_start();

header('Content-Type: application/json');


if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Access denied.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $country_name = trim($_POST['country_name'] ?? '');
    $continent = trim($_POST['continent'] ?? '');

    if (!empty($country_name) && !empty($continent)) {
        
        $checkStmt = $conn->prepare("SELECT id FROM countries WHERE name = ?");
        $checkStmt->bind_param("s", $country_name);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Country already exists.']);
        } else {
            
            $stmt = $conn->prepare("INSERT INTO countries (name, continent) VALUES (?, ?)");
            $stmt->bind_param("ss", $country_name, $continent);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Country added successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error adding country.']);
            }

            $stmt->close();
        }

        $checkStmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    }
}
?>
