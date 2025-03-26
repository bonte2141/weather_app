<?php
require 'db.php';
session_start();

header('Content-Type: application/json');


if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Access denied.']);
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $country_id = intval($_POST['country_id']);

    if ($country_id > 0) {
        $stmt = $conn->prepare("DELETE FROM countries WHERE id = ?");
        $stmt->bind_param("i", $country_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Country deleted successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error deleting country.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid country ID.']);
    }
}
?>
