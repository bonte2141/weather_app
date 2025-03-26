<?php
include 'auth.php';
include 'db.php';

if ($_SESSION['user_type'] !== 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$username = $_SESSION['username'];
$sql = "SELECT region_preferences FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($preferences_json);
$stmt->fetch();

if ($preferences_json) {
    $preferences = json_decode($preferences_json, true);
    $manageCities = $preferences['chk-manage-cities'] ?? false;

    echo json_encode(['success' => true, 'manageCities' => $manageCities]);
} else {
    echo json_encode(['success' => false, 'message' => 'No preferences found']);
}

$stmt->close();
$conn->close();
?>
