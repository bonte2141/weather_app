<?php
include 'auth.php'; 
require 'db.php'; 

if ($_SESSION['user_type'] !== 'User') {
    header('Location: login.php'); 
    exit;
}

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

        .form-label {
            font-size: 1.4rem;
            
            font-weight: bold;
            display: block;
            margin-bottom: 15px;
            
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

        .hidden {
            display: none;
        }

        .radio-group {
            display: flex;
            gap: 30px;
            
            flex-wrap: wrap;
            align-items: center;
            font-size: 1.1rem;
            
            margin-bottom: 30px;
            
        }

        .radio-group label {
            cursor: pointer;
            padding: 12px 20px;
            
            border-radius: 5px;
            transition: background 0.3s;
        }

        .radio-group input[type="radio"] {
            display: none;
            
        }

        .radio-group input[type="radio"]:checked+label {
            background: #333;
            color: white;
            font-weight: bold;
        }

        #star-rating .star {
            font-size: 3rem;
            cursor: pointer;
            color: gray;
            
            transition: color 0.3s;
        }

        #star-rating .star.filled {
            color: gold;
            
        }
    </style>
</head>

<body style="background-color: #121212; color: #ffffff;">

    
    <?php include 'navbar-user.php'; ?>


    <div class="container mt-5">
        
        <div class="custom-card">
            <h1 class="forecast-title">Feedback</h1>
            <p class="large-text fw-bold">We value your input to improve ClimaCast!</p>
            
            <div id="feedback-selection" class="location-card">
                <h2 class="text-center">Choose Feedback Type</h2>
                <p class="text-center mb-4 fw-bold" style="font-size: 1.3rem;">Select an option below:</p>
                <div class="d-flex justify-content-center">
                    <button class="btn-continent-country" onclick="showFeedback('detailed')">Detailed Survey</button>
                    <button class="btn-continent-country" onclick="showFeedback('custom')">Custom Feedback</button>
                </div>
            </div>

            
            <div id="detailed-feedback" class="location-card hidden">
                <h2 class="text-center large-text" style="margin-bottom: 60px;">Tell us about your experience with ClimaCast.</h2>

                <form id="detailed-feedback-form" method="POST" novalidate>

                    <?php
                    $questions = [
                        "How accurate do you find the real-time weather data?" => ["Very accurate", "Mostly accurate", "Sometimes inaccurate", "Mostly inaccurate", "Completely inaccurate"],
                        "How reliable is the forecast data?" => ["Very reliable", "Mostly reliable", "Occasionally incorrect", "Often incorrect", "Completely unreliable"],
                        "How useful do you find the interactive maps?" => ["Very useful", "Somewhat useful", "Neutral", "Not very useful", "Not useful at all"],
                        "How well does the locations feature work for you?" => ["Excellent", "Good", "Average", "Poor", "Very poor"],
                        "How relevant are the weather notifications?" => ["Always relevant", "Mostly relevant", "Sometimes irrelevant", "Often irrelevant", "Completely useless"],
                        "How easy is it to find weather information for your city?" => ["Very easy", "Somewhat easy", "Neutral", "Difficult", "Very difficult"],
                        "How well does the continent-country-city navigation work?" => ["Flawless", "Good", "Neutral", "Confusing", "Completely broken"],
                        "How often do you experience performance issues (e.g. slow loading)?" => ["Never", "Rarely", "Sometimes", "Often", "Always"],
                        "How visually appealing is the ClimaCast interface?" => ["Very appealing", "Somewhat appealing", "Neutral", "Not very appealing", "Not appealing at all"],
                        "Would you recommend ClimaCast to others?" => ["Definitely", "Probably", "Not sure", "Probably not", "Definitely not"]
                    ];

                    $index = 1;
                    foreach ($questions as $question => $options) {
                        echo "<div class='mb-4'>";
                        echo "<label class='form-label fw-bold'>$index. $question</label>";
                        echo "<div class='radio-group'>"; 
                        foreach ($options as $option) {
                            $option_id = "q$index-" . strtolower(str_replace(" ", "-", $option));
                            echo "<input type='radio' id='$option_id' name='q$index' value='$option' required>";
                            echo "<label for='$option_id'>$option</label>";
                        }
                        echo "</div></div>"; 
                        $index++;
                    }

                    ?>
                    <div class="mb-4 text-center">
                        <label class="form-label fw-bold">Rate your overall experience:</label>
                        <div id="star-rating" class="d-flex justify-content-center">
                            <i class="bi bi-star star" data-value="1"></i>
                            <i class="bi bi-star star" data-value="2"></i>
                            <i class="bi bi-star star" data-value="3"></i>
                            <i class="bi bi-star star" data-value="4"></i>
                            <i class="bi bi-star star" data-value="5"></i>
                        </div>
                        <input type="hidden" name="type" value="survey"> 
                        <input type="hidden" name="rating" id="rating-value" required>
                    </div>

                    <div class="d-flex justify-content-center">
                        <button type="submit" class="btn-continent-country" id="submit-feedback">Submit Feedback</button>
                    </div>
                </form>
            </div>


            
            <div id="custom-feedback" class="location-card hidden">
                <h2 class="text-center">Custom Feedback</h2>
                <p class="text-center mb-4 fw-bold" style="font-size: 1.3rem;">Tell us anything.</p>
                <form id="custom-feedback-form" method="POST" novalidate>
                <input type="hidden" name="type" value="custom"> 
                    <div class="mb-4">
                        <label for="custom-feedback-input" class="form-label fw-bold">Your feedback:</label>
                        <textarea class="form-control" id="custom-feedback-input" name="custom_feedback" rows="5"
                            placeholder="Enter your feedback here..." required></textarea>
                    </div>

                    <div class="d-flex justify-content-center">
                        <button type="submit" class="btn-continent-country">Submit Feedback</button>
                    </div>
                </form>
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
        


        function showFeedback(type) {
            document.getElementById('feedback-selection').classList.add('hidden'); 

            if (type === 'detailed') {
                document.getElementById('detailed-feedback').classList.remove('hidden');
            } else if (type === 'custom') {
                document.getElementById('custom-feedback').classList.remove('hidden');
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            const stars = document.querySelectorAll("#star-rating .star");
            const ratingInput = document.getElementById("rating-value");
            const feedbackForm = document.getElementById("detailed-feedback-form");

            
            stars.forEach(star => {
                star.addEventListener("mouseover", function() {
                    resetStars();
                    fillStars(this.dataset.value);
                });

                star.addEventListener("click", function() {
                    ratingInput.value = this.dataset.value;
                    resetStars();
                    fillStars(this.dataset.value);
                });

                star.addEventListener("mouseout", function() {
                    resetStars();
                    if (ratingInput.value) {
                        fillStars(ratingInput.value);
                    }
                });
            });

            
            feedbackForm.addEventListener("submit", function(event) {
                event.preventDefault(); 

                let isValid = true;
                let unansweredQuestions = [];

                
                if (!ratingInput.value) {
                    isValid = false;
                    alert("Please provide a rating before submitting your feedback.");
                }

                
                for (let i = 1; i <= 10; i++) {
                    const questionName = "q" + i;
                    const options = document.querySelectorAll(`input[name="${questionName}"]:checked`);
                    if (options.length === 0) {
                        unansweredQuestions.push(i);
                    }
                }

                
                if (unansweredQuestions.length > 0) {
                    isValid = false;
                    alert("Please answer all questions before submitting.\nUnanswered questions: " + unansweredQuestions.join(", "));
                }

                
                if (isValid) {
                    this.submit();
                }
            });

            
            function fillStars(value) {
                for (let i = 0; i < value; i++) {
                    stars[i].classList.remove("bi-star"); 
                    stars[i].classList.add("bi-star-fill"); 
                    stars[i].style.color = "gold"; 
                }
            }

            function resetStars() {
                stars.forEach(star => {
                    star.classList.remove("bi-star-fill"); 
                    star.classList.add("bi-star"); 
                    star.style.color = "gray"; 
                });
            }
        });

        document.addEventListener("DOMContentLoaded", function() {
    function submitFeedback(event, formId, actionUrl) {
        event.preventDefault(); 

        const form = document.getElementById(formId);
        const formData = new FormData(form);

        
        const submitButton = form.querySelector("button[type='submit']");
        submitButton.disabled = true;

        fetch(actionUrl, {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message); 
                form.reset(); 
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(error => console.error("Error:", error))
        .finally(() => {
            submitButton.disabled = false; 
        });
    }

    
    document.getElementById("custom-feedback-form").addEventListener("submit", function(event) {
        submitFeedback(event, "custom-feedback-form", "submit_feedback.php");
    });

    document.getElementById("detailed-feedback-form").addEventListener("submit", function(event) {
        submitFeedback(event, "detailed-feedback-form", "submit_survey.php");
    });
});



    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>