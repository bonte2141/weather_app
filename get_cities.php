<?php
include 'db.php';


session_start();
$country_id = $_GET['country_id'];
$user_id = $_SESSION['user_id']; 


$sql = "
    SELECT 
        c.id AS city_id, 
        c.name AS city_name, 
        c.country_id, 
        IF(f.user_id IS NOT NULL, 1, 0) AS is_favorite
    FROM 
        cities c
    LEFT JOIN 
        favorite_cities f 
    ON 
        c.id = f.city_id AND f.user_id = ?
    WHERE 
        c.country_id = ?
    ORDER BY is_favorite DESC, c.name ASC"; 


$stmt = $conn->prepare($sql); 
$stmt->bind_param("ii", $user_id, $country_id); 
$stmt->execute(); 
$result = $stmt->get_result(); 


$cities = array();
while ($row = $result->fetch_assoc()) {
    $cities[] = $row; 
}


echo json_encode([
    'success' => true,
    'cities' => $cities
]);

$stmt->close();
$conn->close();
?>
