<?php
include 'auth.php';
require 'db.php';

if ($_SESSION['user_type'] !== 'User') {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['user_id'];
    $survey_responses = [];

    for ($i = 1; $i <= 10; $i++) {
        $question_key = "q$i";
        if (!isset($_POST[$question_key])) {
            echo json_encode(["success" => false, "message" => "All questions must be answered."]);
            exit;
        }
        $survey_responses[$question_key] = $_POST[$question_key];
    }

    $rating = $_POST['rating'] ?? null;
    if (!$rating || !in_array($rating, [1, 2, 3, 4, 5])) {
        echo json_encode(["success" => false, "message" => "Invalid rating"]);
        exit;
    }

    $survey_json = json_encode($survey_responses, JSON_UNESCAPED_UNICODE);

    $stmt = $conn->prepare("INSERT INTO survey_feedback (user_id, content, rating, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("isi", $user_id, $survey_json, $rating);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Survey submitted successfully."]);
        exec("C:/Users/bonte/AppData/Local/Microsoft/WindowsApps/python3.11.exe C:/xampp/htdocs/weather_app/sentiment_analysis.py > nul 2>&1");
        exit; 
    } else {
        echo json_encode(["success" => false, "message" => "Failed to submit survey."]);
        exit; 
    }
}
