<?php
include 'db.php';
include 'auth.php';

if (!isset($_SESSION['username'])) {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Not authenticated."]);
    exit;
}

$username = $_SESSION['username'];

try {
    $stmt = $conn->prepare("SELECT region_preferences FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($preferences);
    $stmt->fetch();

    echo json_encode(["success" => true, "preferences" => $preferences]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
?>
