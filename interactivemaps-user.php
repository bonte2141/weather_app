<?php
include 'auth.php'; 
if ($_SESSION['user_type'] !== 'User') {
    header('Location: login.php'); 
    exit;
}
$username = $_SESSION['username']; 
$preferred_city = $_SESSION['preferred_city'] ?? null;
$other_cities   = $_SESSION['other_cities'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ClimaCast Interactive Maps</title>
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

        
        .btn-custom-light {
            background-color: #ffffff;
            
            color: #000000;
            
            border-radius: 8px;
            
            padding: 8px 14px;
            
            font-size: 1.2rem;
            
            font-weight: bold;
            
            text-decoration: none;
            
            transition: background-color 0.3s, color 0.3s, box-shadow 0.3s;
            
        }

        .btn-custom-light:hover {
            background-color: #e2e6ea;
            
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            
            transform: scale(1.03);
            
        }

        
        .card-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #ffffff;
            
        }

        .card-text {
            font-size: 1rem;
            color: #cccccc;
            
        }
    </style>
</head>

<body style="background-color: #121212; color: #ffffff;">

    
    <?php include 'navbar-user.php'; ?>

    
    <div class="container mt-5">
        <div class="custom-card"> 
            <h1 class="text-center forecast-title">Interactive Maps</h1>
            <h2 class="text-center mt-4">Which type of map do you want to see?</h2>
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card custom-card-small text-center">
                        <img src="temperature.png" class="card-img-top" alt="Temperature Map">
                        <div class="card-body">
                            <h5 class="card-title">Temperature</h5>
                            <p class="card-text">View the current temperature across different regions.</p>
                            <a href="temperature-map-user.php" class="btn btn-custom-light">View Map</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card custom-card-small text-center">
                        <img src="wind.png" class="card-img-top" alt="Wind Map">
                        <div class="card-body">
                            <h5 class="card-title">Wind</h5>
                            <p class="card-text">See the wind patterns and speeds across the globe.</p>
                            <a href="wind-map-user.php" class="btn btn-custom-light">View Map</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card custom-card-small text-center">
                        <img src="radar.png" class="card-img-top" alt="Radar Map">
                        <div class="card-body">
                            <h5 class="card-title">Radar</h5>
                            <p class="card-text">Check the radar for precipitation and storm tracking.</p>
                            <a href="radar-map-user.php" class="btn btn-custom-light">View Map</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mt-4">
                    <div class="card custom-card-small text-center">
                        <img src="precipitation.png" class="card-img-top" alt="Precipitation Map">
                        <div class="card-body">
                            <h5 class="card-title">Precipitation</h5>
                            <p class="card-text">Monitor precipitation levels and forecasts.</p>
                            <a href="precipitation-map-user.php" class="btn btn-custom-light">View Map</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mt-4">
                    <div class="card custom-card-small text-center">
                        <img src="cloud-coverage.png" class="card-img-top" alt="Cloud Coverage Map">
                        <div class="card-body">
                            <h5 class="card-title">Cloud Coverage</h5>
                            <p class="card-text">Observe cloud coverage patterns globally.</p>
                            <a href="cloud-coverage-map-user.php" class="btn btn-custom-light">View Map</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mt-4">
                    <div class="card custom-card-small text-center">
                        <img src="pressure.png" class="card-img-top" alt="Pressure Map">
                        <div class="card-body">
                            <h5 class="card-title">Pressure</h5>
                            <p class="card-text">Analyze atmospheric pressure variations.</p>
                            <a href="pressure-map-user.php" class="btn btn-custom-light">View Map</a>
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

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>