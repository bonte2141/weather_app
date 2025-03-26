<?php
include 'auth.php';
include 'db.php';

if ($_SESSION['user_type'] !== 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$username = $_SESSION['username'];


$sql = "SELECT region_preferences FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($preferences_json);
$stmt->fetch();
$stmt->close();

$preferences = json_decode($preferences_json, true) ?? [];


$preferences['chk-manage-cities'] = $data['manageCities'];


$new_preferences_json = json_encode($preferences);
$sql = "UPDATE users SET region_preferences = ? WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $new_preferences_json, $username);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Preferences saved successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save preferences']);
}

$stmt->close();
$conn->close();
?>
