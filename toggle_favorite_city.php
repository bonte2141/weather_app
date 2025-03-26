<?php
include 'db.php'; 
include 'auth.php'; 

header('Content-Type: application/json'); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $userId = $_SESSION['user_id'];
    $cityId = $input['city_id'];
    $isFavorite = $input['is_favorite'];

    try {
        
        if ($isFavorite) {
            $stmt = $conn->prepare("DELETE FROM favorite_cities WHERE user_id = ? AND city_id = ?");
            $stmt->bind_param("ii", $userId, $cityId);
        } else {
            $stmt = $conn->prepare("INSERT INTO favorite_cities (user_id, city_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $userId, $cityId);
        }

        $stmt->execute();
        $stmt->close();

        
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    } finally {
        $conn->close(); 
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}

?>
