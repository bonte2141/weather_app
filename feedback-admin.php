<?php
include 'auth.php'; 
require 'db.php'; 

if ($_SESSION['user_type'] !== 'Admin') {
    header('Location: login.php'); 
    exit;
}


$questions = [
    "How accurate do you find the real-time weather data?",
    "How reliable is the forecast data?",
    "How useful do you find the interactive maps?",
    "How well does the locations feature work for you?",
    "How relevant are the weather notifications?",
    "How easy is it to find weather information for your city?",
    "How well does the continent-country-city navigation work?",
    "How often do you experience performance issues (e.g. slow loading)?",
    "How visually appealing is the ClimaCast interface?",
    "Would you recommend ClimaCast to others?"
];



$feedback = $conn->query("
    SELECT f.id, f.user_id, u.username, f.content, f.sentiment, NULL AS rating, f.category, f.created_at, 'custom' AS source
FROM feedback f
JOIN users u ON f.user_id = u.id
UNION
SELECT s.id, s.user_id, u.username, s.content, s.sentiment, s.rating, NULL AS category, s.created_at, 'survey' AS source
FROM survey_feedback s
JOIN users u ON s.user_id = u.id
ORDER BY created_at DESC
LIMIT 50

")->fetch_all(MYSQLI_ASSOC) ?? [];




$custom_feedback = [];
$survey_feedback = [];

foreach ($feedback as $item) {
    if ($item['source'] === 'custom') {
        $custom_feedback[] = $item;
    } else {
        
        $rating_text = [
            1 => "very bad",
            2 => "bad",
            3 => "average",
            4 => "good",
            5 => "very good"
        ];

        
        $survey_feedback[] = [
            "id" => $item["id"],
            "user_id" => $item["user_id"],
            "username" => $item["username"],
            "content" => $item["content"],
            "sentiment" => $item["sentiment"],
            "rating" => isset($item["rating"]) ? (int)$item["rating"] : 0, 
            "created_at" => $item["created_at"]
        ];
    }
}


$sentiment_stats = [
    'positive' => 0,
    'neutral' => 0,
    'negative' => 0
];

foreach ($feedback as $item) {
    if (isset($sentiment_stats[$item['sentiment']])) {
        $sentiment_stats[$item['sentiment']]++;
    }
}


$category_distribution = [];
foreach ($custom_feedback as $item) {
    $category = $item['category'] ?? 'Other';
    if (!isset($category_distribution[$category])) {
        $category_distribution[$category] = 0;
    }
    $category_distribution[$category]++;
}

$hasFeedback = !empty($feedback);
$feedback_type_distribution = [
    'custom' => count($custom_feedback),
    'survey' => count($survey_feedback)
];
?>
<script>
    let sentimentData = <?php echo json_encode($sentiment_stats); ?>;
    let feedbackTypeData = <?php echo json_encode($feedback_type_distribution); ?>;
    let categoryData = <?php echo json_encode($category_distribution); ?>;
    let hasFeedback = <?php echo $hasFeedback ? 'true' : 'false'; ?>;
</script>


<?php

$user_id = $_SESSION['user_id']; 
$preferred_city = $_SESSION['preferred_city'] ?? null;
$other_cities   = $_SESSION['other_cities'] ?? [];

$stmt = $conn->prepare("SELECT username, email, password, preferred_city, created_at, user_type FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $username = $user['username'];
    $user_email = $user['email'];
    $user_password = $user['password']; 
    $user_city = $user['preferred_city'] ?? 'No Favorite City Set';
    
    $stmt_favorites = $conn->prepare("
SELECT c.name AS city_name 
FROM favorite_cities fc
JOIN cities c ON fc.city_id = c.id
WHERE fc.user_id = ?
");
    $stmt_favorites->bind_param("i", $user_id);
    $stmt_favorites->execute();
    $result_favorites = $stmt_favorites->get_result();

    $other_cities = [];
    while ($row = $result_favorites->fetch_assoc()) {
        $other_cities[] = $row['city_name'];
    }
    $stmt_favorites->close();
} else {
    
    header('Location: logout.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ClimaCast Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const surveyQuestions = <?php echo json_encode($questions, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE); ?>;
    </script>



    <style>
        #notificationDropdown {
            position: relative;
            margin-right: 26px;
        }

        #notification-count {
            position: absolute;
            top: 0;
            right: 0;
            font-size: 0.8rem;
            padding: 2px 6px;
            border-radius: 50%;
        }

        .dropdown-menu p.text-muted {
            color: #ffffff !important;
        }

        #notification-list {
            max-height: 180px;
            overflow-y: auto;
            padding: 10px;
        }

        .dropdown-item {
            font-size: 1rem;
            padding: 10px 15px;
            color: #ffffff;
            border-top: 1px solid #444;
            border-bottom: 1px solid #444;
        }

        .dropdown-item:hover {
            background-color: #555;
        }

        .large-text {
            font-size: 1.3rem;
            text-align: center;
            color: #ffffff;
            margin-top: 30px;
            
            margin-bottom: 50px;
            
        }

        .custom-card {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
            background-color: #1c1e21;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            color: #ffffff;
        }



        .location-card {
            background-color: #181a1d;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
            color: #ffffff;
        }

        .location-card h2 {
            font-size: 2.5rem;
            font-weight: bold;
            text-align: center;
            margin-bottom: 15px;
        }

        .forecast-title {
            font-size: 3rem;
            font-weight: bold;
            color: #ffffff;
            text-align: center;
            margin-bottom: 20px;
            
        }



        .btn-continent-country {
            background-color: #ffffff;
            color: #000000;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            padding: 8px 12px;
            margin: 5px;
            font-size: 1.2rem;
            text-decoration: none;
            display: inline-block;
            max-width: none;
            
            white-space: normal;
            
            text-align: center;
            transition: background-color 0.3s, box-shadow 0.3s;
        }

        .btn-continent-country:hover {
            background-color: #e2e6ea;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transform: scale(1.03);
            
        }

        .btn-continent-country:focus,
        .btn-continent-country:active {
            outline: none;
            box-shadow: 0 0 0 2px #ccc;
        }

        .feedback-container {
            display: block;
            
        }

        .feedback-card {
            font-size: 1.4rem;
            
            background-color: #1c1e21;
            
            border-radius: 10px;
            padding: 50px;
            
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            
            color: #ffffff;
            width: 100%;
            margin-bottom: 20px;
            
        }

        .feedback-card h3 {
            font-size: 2rem;
            
            font-weight: bold;
            margin-bottom: 5px;
        }

        .feedback-card p {
            font-size: 1.3rem;
            
            margin-bottom: 15px;
            
            margin: 5px 0;
        }

        .feedback-card span.badge {
            font-size: 1.2rem;
            
            padding: 10px 15px;
        }

        
        .modal.fade .modal-dialog {
            transition: transform 0.3s ease-out, opacity 0.3s ease-out;
        }

        
        .modal-dialog {
            max-width: 900px;
            
        }

        
        .modal-content {
            background-color: rgba(28, 30, 33, 0.95) !important;
            color: #ffffff !important;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #444;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.5);
            border-bottom: none;
        }

        
        .modal-backdrop.show {
            backdrop-filter: blur(4px);
        }

        
        .modal-title {
            font-size: 1.6rem;
            font-weight: bold;
            color: #ffffff;
            
            display: flex;
            align-items: center;
            justify-content: center;
        }

        
        .modal-title i {
            display: none;
        }

        
        .modal-header .btn-close {
            display: none !important;
        }

        
        .survey-response-container {
            max-height: none;
            
            overflow-y: visible;
            
        }

        
        .survey-question {
            background-color: #222;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
            border-left: 4px solid #ffffff;
            
            transition: transform 0.2s ease;
        }

        .survey-question:hover {
            transform: scale(1.02);
        }

        
        .survey-question strong {
            font-size: 1.2rem;
            color: #f8f9fa;
        }

        
        .survey-question p {
            font-size: 1.1rem;
            color: #cfcfcf;
            margin: 0;
        }

        
        
        .rating-container {
            display: flex;
            flex-direction: row;
            
            align-items: center;
            justify-content: center;
            
            font-size: 2rem;
            margin-top: 20px;
            gap: 10px;
            
        }

        .rating-container strong {
            font-size: 1.5rem;
        }

        
        .modal-footer {
            display: flex;
            justify-content: center;
            padding-top: 15px;
        }

        
        .custom-close-btn {
            background-color: #ffffff;
            
            color: #000;
            font-weight: bold;
            border-radius: 8px;
            padding: 10px 15px;
            font-size: 1.2rem;
            transition: background-color 0.3s, transform 0.2s;
        }

        .custom-close-btn:hover {
            background-color: #e2e6ea;
            transform: scale(1.08);
        }

        
        .chart-title {
            font-size: 1.2rem;
            
            font-weight: bold;
            text-align: center;
            white-space: nowrap;
            
        }

        
        .no-category-message {
            font-size: 1.2rem;
            
            color: white;
            
            text-align: center;
            margin-top: 100px;
            
        }
    </style>
</head>

<body style="background-color: #121212; color: #ffffff;">

    
    <?php include 'navbar-admin.php'; ?>


    <div class="container mt-5">
        <div class="custom-card">
            <h1 class="forecast-title">Feedback Dashboard</h1>
            <p class="large-text fw-bold">Manage and analyze user feedback</p>

            <div class="location-card mt-4">
                <h2 class="text-center mb-4" style="font-size: 2.5rem; font-weight: bold;">Statistics</h2>
                <div class="row">
                    <div class="col-md-6">
                        <canvas id="sentimentChart"></canvas> 
                    </div>
                    <div class="col-md-3">
                        <canvas id="feedbackTypeChart"></canvas> 
                    </div>
                    <div class="col-md-3">
                        <canvas id="categoryChart"></canvas> 
                    </div>
                </div>
            </div>




            
            <div class="location-card mt-4">
                <h2 class="text-center mb-4" style="font-size: 2.5rem;">Custom Feedback</h2>
                <div class="feedback-container">
                    <?php if (!empty($custom_feedback)): ?>
                        <?php foreach ($custom_feedback as $item): ?>
                            <div class="feedback-card">
                                <h3 class="mb-3"><strong>Username:</strong> <?= htmlspecialchars($item['username']) ?></h3>
                                <p class="mb-2"><strong>Feedback Type:</strong> <?= 'Custom' ?></p>

                                <p class="mb-2"><strong>Category:</strong> <?= ucfirst($item['category'] ?? 'N/A') ?></p>
                                <p class="mb-3"><strong>Feedback:</strong> <?= htmlspecialchars(stripslashes($item['content'] ?? '')) ?></p>

                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-<?= $item['sentiment'] === 'positive' ? 'success' : ($item['sentiment'] === 'neutral' ? 'warning' : 'danger') ?> p-2" style="font-size: 1.2rem;">
                                        <?= ucfirst($item['sentiment'] ?? 'N/A') ?>
                                    </span>
                                    <p class="mb-0"><strong>Date:</strong> <?= $item['created_at'] ?></p>
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    <button class="btn btn-continent-country delete-feedback" data-id="<?= $item['id'] ?>">Delete</button>

                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-center text-white fw-bold fs-4">No feedback available.</p>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="location-card mt-4">
                <h2 class="text-center mb-4" style="font-size: 2.5rem;">Surveys</h2>
                <div class="container">
                    <?php if (!empty($survey_feedback)): ?>
                        <div class="row">
                            <?php foreach ($survey_feedback as $item): ?>
                                <div class="col-md-6 mb-4">
                                    <div class="feedback-card">
                                        
                                        <h3 class="mb-3"><strong>Username:</strong> <?= htmlspecialchars($item['username']) ?></h3>
                                        <p class="mb-2"><strong>Feedback Type:</strong> <?= 'Survey' ?></p>

                                        <div class="d-flex align-items-center">
                                            <p class="mb-0 me-2"><strong>Feedback:</strong></p>
                                            <button class="btn btn-continent-country btn-xs view-survey px-2 py-1"
                                                data-id="<?= $item['id'] ?>"
                                                data-feedback='<?= htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8') ?>'>
                                                View
                                            </button>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <span class="badge bg-<?= $item['sentiment'] === 'positive' ? 'success' : ($item['sentiment'] === 'neutral' ? 'warning' : 'danger') ?> p-2" style="font-size: 1.2rem;">
                                                <?= ucfirst($item['sentiment'] ?? 'N/A') ?>
                                            </span>
                                            <p class="mb-0"><strong>Date:</strong> <?= $item['created_at'] ?></p>
                                        </div>
                                        <div class="d-flex justify-content-end mt-4">
                                            <button class="btn btn-continent-country delete-feedback" data-id="<?= $item['id'] ?>">Delete</button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-center text-white fw-bold fs-4">No feedback available.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="modal fade" id="surveyModal" tabindex="-1" aria-labelledby="surveyModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="surveyModalLabel">Survey Feedback</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="surveyResponses">
                            
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-continent-country" data-bs-dismiss="modal">Close</button>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="notifications.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const preferredCity = "<?php echo $_SESSION['preferred_city'] ?? ''; ?>";
            const otherCities = <?php echo json_encode($_SESSION['other_cities'] ?? []); ?>;
            loadAllForecastData(preferredCity, otherCities);
        });
    </script>
    <script>
        function reorganizeSurveys() {
            let surveySection = document.querySelector(".location-card.mt-4 .container .row");
            if (!surveySection) return;

            let allSurveys = document.querySelectorAll(".location-card.mt-4 .container .row .feedback-card");

            surveySection.innerHTML = ""; 

            if (allSurveys.length === 0) {
                surveySection.innerHTML = `<p class="text-center text-white fw-bold fs-4">No feedback available.</p>`;
            } else {
                allSurveys.forEach((survey, index) => {
                    let colClass = "col-md-6 mb-4";
                    let surveyWrapper = survey.closest(".col-md-6, .col-md-12");
                    if (surveyWrapper) {
                        surveyWrapper.className = colClass;
                        surveySection.appendChild(surveyWrapper);
                    }
                });
            }
        }




        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".view-survey").forEach(button => {
                button.addEventListener("click", function() {
                    let surveyContainer = document.getElementById("surveyResponses");
                    if (!surveyContainer) {
                        console.error("Error: surveyResponses container not found. Check if the modal exists in the HTML.");
                        alert("Survey modal is missing. Please refresh the page.");
                        return;
                    }

                    let feedbackData = JSON.parse(this.getAttribute("data-feedback") || "{}");
                    surveyContainer.innerHTML = "";

                    if (!feedbackData.content || feedbackData.content.trim() === "null" || feedbackData.content.trim() === "") {
                        surveyContainer.innerHTML = "<p class='text-center text-muted'>No survey data available.</p>";
                        return;
                    }

                    let surveyContent;
                    try {
                        surveyContent = JSON.parse(feedbackData.content);
                    } catch (error) {
                        surveyContainer.innerHTML = "<p class='text-danger text-center'>Error loading survey data.</p>";
                        return;
                    }

                    if (Object.keys(surveyContent).length === 0) {
                        surveyContainer.innerHTML = "<p class='text-center text-muted'>No survey data available.</p>";
                    } else {
                        let surveyHtml = `<div class="survey-content">`;
                        let questionNumber = 1;

                        Object.entries(surveyContent).forEach(([questionKey, answer], index) => {
                            let actualQuestion = surveyQuestions[index] || `Question ${questionNumber}`;
                            surveyHtml += `
                        <div class="survey-question">
                            <strong>${actualQuestion}</strong>
                            <p>${answer}</p>
                        </div>
                    `;
                            questionNumber++;
                        });

                        surveyHtml += "</div>";

                        
                        if (feedbackData.rating) {
                            surveyHtml += `
        <div class="rating-container">
            <strong>Rating:</strong> 
            ${"<i class='bi bi-star-fill text-warning'></i>".repeat(feedbackData.rating)}
            ${"<i class='bi bi-star text-secondary'></i>".repeat(5 - feedbackData.rating)}
        </div>
    `;
                        }

                        surveyContainer.innerHTML = surveyHtml;
                    }

                    let modal = document.getElementById("surveyModal");
                    if (modal) {
                        new bootstrap.Modal(modal).show();
                    }
                });
            });
        });

        let sentimentChartInstance = null;
        let feedbackTypeChartInstance = null;
        let categoryChartInstance = null;

        function initializeSentimentChart() {
            const ctx = document.getElementById('sentimentChart').getContext('2d');
            if (sentimentChartInstance) sentimentChartInstance.destroy();

            sentimentChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Positive', 'Neutral', 'Negative'],
                    datasets: [{
                        data: [
                            sentimentData.positive || 0,
                            sentimentData.neutral || 0,
                            sentimentData.negative || 0
                        ],
                        backgroundColor: [
                            '#4CAF50', 
                            '#FFC107', 
                            '#F44336' 
                        ],
                        borderColor: 'white', 
                        borderWidth: 2 
                    }]
                },
                options: {
                    plugins: {
                        title: {
                            display: true,
                            text: 'AI-Powered Sentiment Analysis',
                            font: {
                                size: 18,
                                weight: 'bold'
                            },
                            color: 'white' 
                        },
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                color: 'white', 
                                font: {
                                    weight: 'bold'
                                },
                                generateLabels: function(chart) {
                                    const data = chart.data;
                                    if (data.labels.length && data.datasets.length) {
                                        return data.labels.map((label, i) => {
                                            return {
                                                text: label,
                                                fillStyle: data.datasets[0].backgroundColor[i],
                                                fontColor: 'white', 
                                                strokeStyle: 'white', 
                                                lineWidth: 2, 
                                                hidden: false
                                            };
                                        });
                                    }
                                    return [];
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1, 
                                precision: 0, 
                                color: 'white' 
                            },
                            suggestedMin: 0, 
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)' 
                            }
                        },
                        x: {
                            ticks: {
                                display: false 
                            },
                            grid: {
                                display: false 
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return `${tooltipItem.label}: ${tooltipItem.raw}`;
                            }
                        }
                    }
                }
            });
        }




        
        function initializeFeedbackTypeChart() {
            const ctx = document.getElementById('feedbackTypeChart').getContext('2d');
            if (feedbackTypeChartInstance) feedbackTypeChartInstance.destroy();

            feedbackTypeChartInstance = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Custom', 'Survey'],
                    datasets: [{
                        data: [feedbackTypeData.custom || 0, feedbackTypeData.survey || 0],
                        backgroundColor: ['#007BFF', '#FF5722']
                    }]
                },
                options: {
                    plugins: {
                        title: {
                            display: true,
                            text: 'Feedback Type Distribution',
                            font: {
                                size: 18,
                                weight: 'bold'
                            },
                            color: 'white'
                        },
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                font: {
                                    weight: 'bold'
                                },
                                color: 'white'
                            }
                        }
                    }
                }
            });
        }




        
        function initializeCategoryChart() {
            const ctx = document.getElementById('categoryChart').getContext('2d');

            if (Object.keys(categoryData).length === 0) {
                
                document.getElementById('categoryChart').parentNode.innerHTML = `
            <h3 class="chart-title">Feedback Category Breakdown</h3>
            <p class="no-category-message">No custom feedback available to display categories.</p>
        `;
            } else {
                
                if (categoryChartInstance) categoryChartInstance.destroy();

                categoryChartInstance = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(categoryData),
                        datasets: [{
                            data: Object.values(categoryData),
                            backgroundColor: ['#E57373', '#81C784', '#64B5F6', '#9C27B0']
                        }]
                    },
                    options: {
                        plugins: {
                            title: {
                                display: true,
                                text: 'Feedback Category Breakdown',
                                font: {
                                    size: 18,
                                    weight: 'bold'
                                },
                                color: 'white'
                            },
                            legend: {
                                display: true,
                                position: 'bottom',
                                labels: {
                                    font: {
                                        weight: 'bold'
                                    },
                                    color: 'white'
                                }
                            }
                        }
                    }
                });
            }
        }





        
        document.addEventListener("DOMContentLoaded", function() {
            if (hasFeedback) {
                initializeSentimentChart();
                initializeFeedbackTypeChart();
                initializeCategoryChart();
            } else {
                
                const statsSection = document.querySelector(".location-card.mt-4");
                statsSection.innerHTML = `
            <h2 class="text-center mb-4" style="font-size: 2.5rem;">Statistics</h2>
            <p class="text-center text-white fw-bold fs-4">No feedback available.</p>
        `;
            }
        });

        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".delete-feedback").forEach(button => {
                button.addEventListener("click", function() {
                    let feedbackId = this.getAttribute("data-id");
                    let card = this.closest('.feedback-card');
                    let container = card.closest(".location-card")?.querySelector(".feedback-container");

                    if (!confirm("Are you sure you want to delete this feedback?")) return;

                    fetch("delete_feedback.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/x-www-form-urlencoded"
                            },
                            body: `feedback_id=${feedbackId}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                card.remove();
                                reorganizeSurveys();
                                updateCharts(); 

                                if (container) {
                                    let remainingSurveys = container.querySelectorAll(".feedback-card");
                                    if (remainingSurveys.length === 0) {
                                        container.innerHTML = `<p class="text-center text-white fw-bold fs-4">No feedback available.</p>`;
                                    }
                                }
                            } else {
                                alert("Error deleting feedback: " + data.error);
                            }
                        })
                        .catch(error => alert("Network error: " + error));
                });
            });
        });


        function updateCharts() {
            fetch("get_feedback_stats.php")
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        sentimentData = data.sentiment_stats;
                        feedbackTypeData = data.type_distribution;
                        categoryData = data.category_distribution;

                        const isEmpty =
                            sentimentData.positive === 0 &&
                            sentimentData.neutral === 0 &&
                            sentimentData.negative === 0 &&
                            feedbackTypeData.custom === 0 &&
                            feedbackTypeData.survey === 0 &&
                            Object.values(categoryData).reduce((a, b) => a + b, 0) === 0;

                        const statsSection = document.querySelector(".location-card.mt-4");

                        if (isEmpty) {
                            
                            if (sentimentChartInstance) {
                                sentimentChartInstance.destroy();
                                sentimentChartInstance = null;
                            }
                            if (feedbackTypeChartInstance) {
                                feedbackTypeChartInstance.destroy();
                                feedbackTypeChartInstance = null;
                            }
                            if (categoryChartInstance) {
                                categoryChartInstance.destroy();
                                categoryChartInstance = null;
                            }

                            
                            statsSection.innerHTML = `
                        <h2 class="text-center mb-4" style="font-size: 2.5rem;">Statistics</h2>
                        <p class="text-center text-white fw-bold fs-4">No feedback available.</p>
                    `;
                        } else {
                            
                            initializeSentimentChart();
                            initializeFeedbackTypeChart();
                            initializeCategoryChart();
                        }
                    }
                })
                .catch(error => console.error("Error updating charts:", error));
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>