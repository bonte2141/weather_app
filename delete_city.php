<?php
include 'db.php';

$city_id = $_POST['city_id'] ?? 0;

if ($city_id) {
    $stmt = $conn->prepare("DELETE FROM cities WHERE id = ?");
    $stmt->bind_param("i", $city_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "City deleted successfully!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error deleting city."]);
    }
    $stmt->close();
}
?>
