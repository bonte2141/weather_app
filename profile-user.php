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
    <title>ClimaCast Profile</title>
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
            font-size: 1.1rem;
            font-weight: bold;
            text-align: center;
            color: #ffffff;
            margin: 20px 0;
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

        .btn-location {
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
        }



        .btn-location:hover {
            background-color: #e2e6ea;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            
            transform: scale(1.03);
            
        }

        .btn-location:focus,
        .btn-location:active {
            outline: none;
            box-shadow: 0 0 0 2px #ccc;
        }

        .btn-location {
            background-color: #ffffff;
            color: #000000;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            padding: 8px 12px;
            margin: 5px;
            transition: background-color 0.3s, color 0.3s, box-shadow 0.3s;
            text-decoration: none;
        }

        .btn-location:hover {
            background-color: #e2e6ea;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .location-card {
            background-color: #181a1d;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
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
            margin-bottom: 40px;
            
        }
    </style>
</head>

<body style="background-color: #121212; color: #ffffff;">

    
    <?php include 'navbar-user.php'; ?>

    <div class="container mt-5">
        <div class="card custom-card">
            <h1 class="text-center forecast-title">Your Profile</h1>
            <div class="location-card">
                
                <div class="text-center mb-3">
                    <i class="bi bi-person-circle" style="font-size: 4rem; color: #ffffff;"></i>
                </div>
                
                <h2 class="text-center mb-4" style="font-size: 2.5rem; font-weight: bold; color: #ffffff;"><?php echo $username; ?></h2>

                
                <p class="text-center"><strong>Email:</strong> <?php echo $user_email; ?></p>
                <p class="text-center"><strong>Preferred City:</strong> <?php echo $user_city; ?></p>
                <p class="text-center"><strong>Other Favorite Cities:</strong>
                    <?php
                    if (!empty($other_cities)) {
                        echo implode(', ', $other_cities); 
                    } else {
                        echo 'No other favorite cities.';
                    }
                    ?>
                </p>

                <p class="text-center"><strong>Created on:</strong> <?php echo $user_created_at = $user['created_at']; ?></p>
                <p class="text-center"><strong>User Type:</strong> <?php echo $user_type = $user['user_type']; ?></p>


                
                <div class="text-center mt-4">
                    <a href="edit_profile-user.php" class="btn-location me-2">Edit Profile</a>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>