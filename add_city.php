<?php
include 'db.php';

$city_name = $_POST['city_name'] ?? '';
$country_id = $_POST['country_id'] ?? 0;

if ($city_name && $country_id) {
    
    $check_stmt = $conn->prepare("SELECT id FROM cities WHERE name = ? AND country_id = ?");
    $check_stmt->bind_param("si", $city_name, $country_id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "City already exists in this country."]);
    } else {
        
        $stmt = $conn->prepare("INSERT INTO cities (name, country_id) VALUES (?, ?)");
        $stmt->bind_param("si", $city_name, $country_id);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "City added successfully!"]);
        } else {
            echo json_encode(["success" => false, "message" => "Error adding city: " . $stmt->error]);
        }
        $stmt->close();
    }

    $check_stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid city name or country ID."]);
}

$conn->close();
?>
