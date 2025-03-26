<?php
include 'auth.php';
require 'db.php';

if ($_SESSION['user_type'] !== 'Admin') {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $feedback_id = $_POST['feedback_id'] ?? null;

    if (!$feedback_id) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid request"]);
        exit;
    }

    
    $stmt = $conn->prepare("SELECT sentiment, category FROM feedback WHERE id = ?");
    $stmt->bind_param("i", $feedback_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $feedback = $result->fetch_assoc();
    $stmt->close();

    if ($feedback) {
        
        $stmt = $conn->prepare("DELETE FROM feedback WHERE id = ?");
    } else {
        
        $stmt = $conn->prepare("SELECT sentiment FROM survey_feedback WHERE id = ?");
        $stmt->bind_param("i", $feedback_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $feedback = $result->fetch_assoc();
        $stmt->close();

        if (!$feedback) {
            http_response_code(404);
            echo json_encode(["error" => "Feedback not found"]);
            exit;
        }

        
        $stmt = $conn->prepare("DELETE FROM survey_feedback WHERE id = ?");
    }

    $stmt->bind_param("i", $feedback_id);

    if ($stmt->execute()) {
        
        $stats_feedback = $conn->query("
            SELECT sentiment, COUNT(*) as count 
            FROM feedback 
            GROUP BY sentiment
        ")->fetch_all(MYSQLI_ASSOC);

        
        $stats_survey = $conn->query("
            SELECT sentiment, COUNT(*) as count 
            FROM survey_feedback 
            GROUP BY sentiment
        ")->fetch_all(MYSQLI_ASSOC);

        
        $sentiment_stats = [
            'positive' => 0,
            'neutral' => 0,
            'negative' => 0
        ];

        foreach ($stats_feedback as $row) {
            $sentiment_stats[$row['sentiment']] += $row['count'];
        }

        foreach ($stats_survey as $row) {
            $sentiment_stats[$row['sentiment']] += $row['count'];
        }

        
        $category_stats = $conn->query("
            SELECT category, COUNT(*) as count 
            FROM feedback 
            GROUP BY category
        ")->fetch_all(MYSQLI_ASSOC);

        $category_distribution = [];
        foreach ($category_stats as $row) {
            $category_distribution[$row['category']] = $row['count'];
        }

        echo json_encode([
            "success" => true,
            "message" => "Feedback deleted",
            "sentiment_stats" => $sentiment_stats,
            "category_distribution" => $category_distribution
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Failed to delete feedback"]);
    }

    $stmt->close();
    $conn->close();
}
?>
