<?php
include 'db.php';


$country_id = $_GET['country_id'] ?? 0;

if ($country_id > 0) {
    
    $sql = "SELECT id AS city_id, name AS city_name FROM cities WHERE country_id = ? ORDER BY name ASC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $country_id);
    $stmt->execute();
    $result = $stmt->get_result();

    
    $cities = [];
    while ($row = $result->fetch_assoc()) {
        $cities[] = $row;
    }

    echo json_encode([
        'success' => true,
        'cities' => $cities
    ]);

    $stmt->close();
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid country ID.'
    ]);
}

$conn->close();
?>
