<?php
include 'auth.php';
require 'db.php';

if ($_SESSION['user_type'] !== 'Admin') {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}


$sentiment_stats = ['positive' => 0, 'neutral' => 0, 'negative' => 0];


$feedback_stats = $conn->query("SELECT sentiment, COUNT(*) as count FROM feedback GROUP BY sentiment")->fetch_all(MYSQLI_ASSOC);
foreach ($feedback_stats as $row) {
    $sentiment_stats[$row['sentiment']] += $row['count'];
}


$survey_sentiments = $conn->query("SELECT sentiment, COUNT(*) as count FROM survey_feedback GROUP BY sentiment")->fetch_all(MYSQLI_ASSOC);
foreach ($survey_sentiments as $row) {
    if ($row['sentiment']) {
        $sentiment_stats[$row['sentiment']] += $row['count'];
    }
}


$type_distribution = [
    'custom' => (int) $conn->query("SELECT COUNT(*) as count FROM feedback")->fetch_assoc()['count'],
    'survey' => (int) $conn->query("SELECT COUNT(*) as count FROM survey_feedback")->fetch_assoc()['count']
];


$category_stats = $conn->query("SELECT category, COUNT(*) as count FROM feedback GROUP BY category")->fetch_all(MYSQLI_ASSOC);
$category_distribution = [];
foreach ($category_stats as $row) {
    $category_distribution[$row['category']] = $row['count'];
}


echo json_encode([
    "success" => true,
    "sentiment_stats" => $sentiment_stats,
    "type_distribution" => $type_distribution,
    "category_distribution" => $category_distribution
]);

$conn->close();
?>
