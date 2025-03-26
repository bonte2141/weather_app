<?php
include 'db.php';
include 'auth.php';

if (!isset($_SESSION['username'])) {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Not authenticated."]);
    exit;
}

$username = $_SESSION['username'];
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid data."]);
    exit;
}

$preferences = json_encode($data);

try {
    $stmt = $conn->prepare("UPDATE users SET region_preferences = ? WHERE username = ?");
    $stmt->bind_param("ss", $preferences, $username);
    $stmt->execute();

    echo json_encode(["success" => true, "message" => "Preferences saved successfully."]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
?>
