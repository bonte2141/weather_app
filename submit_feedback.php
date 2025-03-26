<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["success" => false, "message" => "User not logged in."]);
        exit;
    }

    $user_id = $_SESSION['user_id'];

    if (!isset($_POST['custom_feedback']) || empty(trim($_POST['custom_feedback']))) {
        echo json_encode(["success" => false, "message" => "Custom feedback cannot be empty."]);
        exit;
    }

    $content = $conn->real_escape_string($_POST['custom_feedback']);

    $stmt = $conn->prepare("INSERT INTO feedback (user_id, content, created_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("is", $user_id, $content);

    if ($stmt->execute()) {
        exec("C:/Users/bonte/AppData/Local/Microsoft/WindowsApps/python3.11.exe C:/xampp/htdocs/weather_app/sentiment_analysis.py > nul 2>&1");
        echo json_encode(["success" => true, "message" => "Feedback submitted successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Database error: " . $conn->error]);
    }
}
?>
