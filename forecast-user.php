<?php
include 'auth.php'; 
if ($_SESSION['user_type'] !== 'User') {
    header('Location: login.php'); 
    exit;
}

$username = $_SESSION['username']; 
$preferred_city = $_SESSION['preferred_city'] ?? null; 
$other_cities = $_SESSION['other_cities'] ?? []; 
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ClimaCast Forecast</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        .forecast-title {
            font-size: 3rem;
            
            font-weight: bold;
            
            color: #ffffff;
            text-align: center;
            margin-bottom: 30px;
        }

        .custom-card {
            max-width: 1200px;
            
            margin: 0 auto;
            
            padding: 40px;
            background-color: #1c1e21;
            
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            color: #ffffff;
            
        }

        .custom-card h5 {
            font-size: 1.8rem;
            font-weight: bold;
        }

        .custom-card p {
            font-size: 1.5rem;
            
            margin-top: 15px;
        }

        .custom-card .btn {
            font-size: 1.2rem;
            font-weight: bold;
            background-color: #ffffff;
            
            color: #000000;
            border: none;
        }

        .custom-card .btn:hover {
            background-color: #e0e0e0;
            
        }

        .divider {
            height: 2px;
            background-color: #343a40;
            margin: 30px 0;
        }

        #notificationDropdown {
            position: relative;
            margin-right: 20px;
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
    </style>
</head>

<body style="background-color: #121212; color: #ffffff;">

    
    <?php include 'navbar-user.php'; ?>

    
    <div class="container mt-5">
        
        <div class="custom-card">
            
            <h1 class="forecast-title">Forecast</h1>

            
            <div class="card custom-card mb-4">
                <?php if (empty($preferred_city)): ?>
                    <div class="text-center">
                        <h3 class="text-light fw-bold">You don't have a preferred city set yet!</h3>
                        <p class="text-light">Set your preferred city to get personalized updates tailored for you.</p>
                        <a href="profile-user.php" class="btn btn-light">Go to Profile</a>
                    </div>
                <?php else: ?>
                    <h3 class="text-light text-center">
                        Forecast for <span id="preferred-city-country"><?php echo $preferred_city; ?></span>
                    </h3>


                    <div id="preferred-city-hourly-forecast">
                        <h4 class="forecast-title-small">Short-Term Forecast</h4>
                        <p class="forecast-subtitle">Weather updates every 3 hours for the next 24 hours.</p>
                    </div>
                    <div class="divider"></div>
                    <div id="preferred-city-daily-forecast">
                        <h4 class="forecast-title-small">Long-Term Forecast</h4>
                        <p class="forecast-subtitle">Daily trends for the next 6 days.</p>
                    </div>

                <?php endif; ?>
            </div>
            
            <div class="divider"></div>
            
            <div class="card custom-card mb-4">
                <h2 class="text-light text-center mb-4"><strong>Other Cities</strong></h2>
                <?php if (empty($other_cities)): ?>
                    <div class="text-center">
                        <p class="text-light">Add other cities you might be interested in keeping track of!</p>
                        <a href="locations-user.php" class="btn btn-light">Add Cities</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($other_cities as $index => $city): ?>
                        <div>
                            <h3 class="text-light text-center">
                                Forecast for <span id="other-city-country-<?php echo $index; ?>"><?php echo $city; ?></span>
                            </h3>
                            <div id="other-cities-hourly-forecast-<?php echo $index; ?>">
                                <h4 class="forecast-title-small">Short-Term Forecast</h4>
                                <p class="forecast-subtitle">Weather updates every 3 hours for the next 24 hours.</p>
                            </div>
                            <div class="divider"></div>
                            <div id="other-cities-daily-forecast-<?php echo $index; ?>">
                                <h4 class="forecast-title-small">Long-Term Forecast</h4>
                                <p class="forecast-subtitle">Daily trends for the next 6 days.</p>
                            </div>
                            <div class="divider"></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>



            
            <script>
                const apiKey = '88a128d5abfcf946ae52e487b5ac0ef9';

                
                const relevantPhenomena = [
                    "shower rain", "ragged shower rain", "shower rain and drizzle",
                    "heavy shower rain and drizzle", "light intensity drizzle", "shower drizzle", "heavy intensity shower rain",
                    "rain", "moderate rain", "heavy intensity rain", "very heavy rain", "extreme rain",
                    "freezing rain", "thunderstorm", "ragged thunderstorm", "heavy intensity drizzle", "light thunderstorm",
                    "heavy thunderstorm", "thunderstorm with rain", "thunderstorm with light rain",
                    "thunderstorm with heavy rain", "thunderstorm with drizzle", "thunderstorm with light drizzle",
                    "thunderstorm with heavy drizzle", "heavy intensity drizzle rain", "drizzle", "snow", "drizzle rain", "light intensity drizzle rain", "heavy snow", "light snow", "light shower snow",
                    "shower snow", "heavy shower snow", "sleet", "light rain", "light rain and snow", "rain and snow", "tornado",
                    "squalls", "volcanic ash", "sand", "dust", "smoke", "haze", "fog", "light intensity shower rain", "light shower sleet", "shower sleet", "shower snow"
                ];

                
                let allNotifications = [];

               
                const weatherIcons = {
                    "clear sky": {
                        icon: "bi-sun-fill",
                        color: "text-warning"
                    },
                    "few clouds": {
                        icon: "bi-cloud-sun-fill",
                        color: "text-warning"
                    },
                    "scattered clouds": {
                        icon: "bi-cloud-fill",
                        color: "text-secondary"
                    },
                    "broken clouds": {
                        icon: "bi-clouds-fill",
                        color: "text-secondary"
                    },
                    "shower rain": {
                        icon: "bi-cloud-drizzle-fill",
                        color: "text-info"
                    },
                    "ragged shower rain": {
                        icon: "bi-cloud-drizzle-fill",
                        color: "text-info"
                    },
                    "shower rain and drizzle": {
                        icon: "bi-cloud-drizzle-fill",
                        color: "text-info"
                    },
                    "heavy shower rain and drizzle": {
                        icon: "bi-cloud-drizzle-fill",
                        color: "text-info"
                    },
                    "shower drizzle": {
                        icon: "bi-cloud-drizzle-fill",
                        color: "text-info"
                    },
                    "heavy intensity shower rain": {
                        icon: "bi-cloud-drizzle-fill",
                        color: "text-info"
                    },
                    "rain": {
                        icon: "bi-cloud-rain-fill",
                        color: "text-primary"
                    },
                    "thunderstorm": {
                        icon: "bi-cloud-lightning-fill",
                        color: "text-danger"
                    },
                    "ragged thunderstorm": {
                        icon: "bi-cloud-lightning-fill",
                        color: "text-danger"
                    },
                    "light thunderstorm": {
                        icon: "bi-cloud-lightning-fill",
                        color: "text-danger"
                    },
                    "snow": {
                        icon: "bi-cloud-snow-fill",
                        color: "text-light"
                    },
                    "heavy snow": {
                        icon: "bi-cloud-snow-fill",
                        color: "text-light"
                    },
                    "sleet": {
                        icon: "bi-cloud-snow-fill",
                        color: "text-light"
                    },
                    "light shower sleet": {
                        icon: "bi-cloud-snow-fill",
                        color: "text-light"
                    },
                    "shower sleet": {
                        icon: "bi-cloud-snow-fill",
                        color: "text-light"
                    },
                    "light rain and snow": {
                        icon: "bi-cloud-drizzle-fill",
                        color: "text-info"
                    },
                    "rain and snow": {
                        icon: "bi-cloud-rain-fill",
                        color: "text-primary"
                    },
                    "shower snow": {
                        icon: "bi-cloud-snow-fill",
                        color: "text-info"
                    },
                    "heavy shower snow": {
                        icon: "bi-cloud-snow-fill",
                        color: "text-info"
                    },
                    "mist": {
                        icon: "bi-cloud-fog-fill",
                        color: "text-secondary"
                    },
                    "haze": {
                        icon: "bi-cloud-haze2-fill",
                        color: "text-warning"
                    },
                    "overcast clouds": {
                        icon: "bi-clouds-fill",
                        color: "text-secondary"
                    },
                    "light rain": {
                        icon: "bi-cloud-drizzle-fill",
                        color: "text-info"
                    },
                    "light intensity shower rain": {
                        icon: "bi-cloud-drizzle-fill",
                        color: "text-info"
                    },
                    "light intensity drizzle": {
                        icon: "bi-cloud-drizzle-fill",
                        color: "text-info"
                    },
                    "light intensity drizzle rain": {
                        icon: "bi-cloud-drizzle-fill",
                        color: "text-info"
                    },
                    "moderate rain": {
                        icon: "bi-cloud-rain-fill",
                        color: "text-primary"
                    },
                    "heavy intensity rain": {
                        icon: "bi-cloud-rain-heavy-fill",
                        color: "text-primary"
                    },
                    "heavy intensity drizzle": {
                        icon: "bi-cloud-rain-heavy-fill",
                        color: "text-primary"
                    },
                    "heavy intensity drizzle rain": {
                        icon: "bi-cloud-rain-heavy-fill",
                        color: "text-primary"
                    },
                    "extreme rain": {
                        icon: "bi-cloud-rain-heavy-fill",
                        color: "text-primary"
                    },
                    "freezing rain": {
                        icon: "bi-cloud-drizzle-fill",
                        color: "text-info"
                    },
                    "very heavy rain": {
                        icon: "bi-cloud-rain-heavy-fill",
                        color: "text-primary"
                    },
                    "thunderstorm with rain": {
                        icon: "bi-cloud-lightning-rain-fill",
                        color: "text-danger"
                    },
                    "thunderstorm with light rain": {
                        icon: "bi-cloud-lightning-rain-fill",
                        color: "text-danger"
                    },
                    "thunderstorm with heavy rain": {
                        icon: "bi-cloud-lightning-rain-fill",
                        color: "text-warning"
                    },
                    "thunderstorm with drizzle": {
                        icon: "bi-cloud-lightning-rain-fill",
                        color: "text-danger"
                    },
                    "thunderstorm with light drizzle": {
                        icon: "bi-cloud-lightning-rain-fill",
                        color: "text-danger"
                    },
                    "thunderstorm with heavy drizzle": {
                        icon: "bi-cloud-lightning-rain-fill",
                        color: "text-warning"
                    },
                    "heavy thunderstorm": {
                        icon: "bi-cloud-lightning-rain-fill",
                        color: "text-warning"
                    },
                    "light snow": {
                        icon: "bi-cloud-snow-fill",
                        color: "text-info"
                    },
                    "light shower snow": {
                        icon: "bi-cloud-snow-fill",
                        color: "text-info"
                    },
                    "fog": {
                        icon: "bi-cloud-fog-fill",
                        color: "text-secondary"
                    },
                    "smoke": {
                        icon: "bi-cloud-haze2-fill",
                        color: "text-secondary"
                    },
                    "dust": {
                        icon: "bi-cloud-dust-fill",
                        color: "text-secondary"
                    },
                    "sand": {
                        icon: "bi-cloud-dust-fill",
                        color: "text-secondary"
                    },
                    "ash": {
                        icon: "bi-cloud-snow-fill",
                        color: "text-secondary"
                    },
                    "squalls": {
                        icon: "bi-clouds-fill",
                        color: "text-primary"
                    },
                    "tornado": {
                        icon: "bi-cloud-hurricane",
                        color: "text-danger"
                    },
                    "drizzle": {
                        icon: "bi-cloud-drizzle-fill",
                        color: "text-primary"
                    },
                    "drizzle rain": {
                        icon: "bi-cloud-drizzle-fill",
                        color: "text-info"
                    }
                };

                function getWeatherIcon(description) {
                    const condition = weatherIcons[description.toLowerCase()] || {
                        icon: "bi-question-circle",
                        color: "text-muted"
                    };
                    return `<i class="bi ${condition.icon} ${condition.color}" style="font-size: 1.5rem;"></i>`;
                }

                
                function getWindDirection(deg) {
                    const sectors = ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW'];
                    return sectors[Math.round(deg / 45) % 8];
                }

                
                function addCurrentNotification(cityName, countryCode, desc) {
                    const lowerDesc = desc.toLowerCase();
                    if (relevantPhenomena.includes(lowerDesc)) {
                        allNotifications.push({
                            message: `Currently: ${desc} in ${cityName}, ${countryCode}.`,
                            type: "current"
                        });
                    }
                }

                
                function addForecastNotifications(cityName, countryCode, forecastList) {
                    const now = new Date();
                    const yearNow = now.getFullYear();
                    const monthNow = now.getMonth();
                    const dayNow = now.getDate();

                    forecastList.forEach(entry => {
                        const forecastDate = new Date(entry.dt * 1000);

                        
                        if (
                            forecastDate.getFullYear() === yearNow &&
                            forecastDate.getMonth() === monthNow &&
                            forecastDate.getDate() === dayNow
                        ) {
                            
                            if (forecastDate > now) {
                                const desc = entry.weather[0].description;
                                const lowerDesc = desc.toLowerCase();
                                if (relevantPhenomena.includes(lowerDesc)) {
                                    const timeString = forecastDate.toLocaleTimeString([], {
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    });
                                    allNotifications.push({
                                        message: `Today at ${timeString}: ${desc} in ${cityName}, ${countryCode}.`,
                                        type: "forecast"
                                    });
                                }
                            }
                        }
                    });
                }

                
                function displayNotifications() {
                    const notificationList = document.getElementById("notification-list");
                    const notificationCount = document.getElementById("notification-count");

                    if (!notificationList) return; 
                    notificationList.innerHTML = "";

                    if (allNotifications.length === 0) {
                        notificationList.innerHTML = `<p class="large-text">No current alerts.</p>`;
                        notificationCount.style.display = "none";
                        return;
                    }

                    notificationCount.style.display = "inline";
                    notificationCount.textContent = (allNotifications.length > 3) ?
                        "3+" :
                        allNotifications.length;

                    allNotifications.forEach(notif => {
                        const li = document.createElement("li");
                        li.className = "dropdown-item d-flex justify-content-between align-items-center";
                        li.innerHTML = `<span>${notif.message}</span>`;
                        notificationList.appendChild(li);
                    });
                }

               
                async function getCityAndCountry(city, containerId) {
                    const url = `https://api.openweathermap.org/data/2.5/weather?q=${city}&appid=${apiKey}&units=metric`;
                    try {
                        const response = await fetch(url);
                        const data = await response.json();
                        if (data.cod !== 200) {
                            throw new Error("City not found or invalid data");
                        }

                        const cityCountry = `${data.name}, ${data.sys.country}`;
                        document.getElementById(containerId).textContent = cityCountry;

                        
                        const desc = data.weather[0].description;
                        addCurrentNotification(data.name, data.sys.country, desc);

                    } catch (error) {
                        console.error('Error fetching city and country:', error);
                        document.getElementById(containerId).textContent = city;
                    }
                }

                
                async function getHourlyForecast(city, containerId) {
                    const url = `https://api.openweathermap.org/data/2.5/forecast?q=${city}&appid=${apiKey}&units=metric`;
                    try {
                        const response = await fetch(url);
                        const data = await response.json();
                        if (data.cod !== "200") {
                            throw new Error("Invalid forecast data");
                        }

                        let html = '<div class="row">';
                        for (let i = 0; i < 8; i++) {
                            const entry = data.list[i];
                            const time = new Date(entry.dt * 1000);
                            const temp = entry.main.temp;
                            const condition = entry.weather[0].description;

                            html += `
                                <div class="col-md-3 mb-3">
                                    <div class="card custom-card-small text-center p-3">
                                        <p class="mb-1" style="font-size: 1.2rem; font-weight: bold;">
                                            ${time.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                                        </p>
                                        <div class="my-2">${getWeatherIcon(condition)}</div>
                                        <p class="mb-1" style="font-size: 1.1rem; font-weight: bold;">
                                            <i class="bi bi-info-circle me-1"></i> Condition:
                                            <span style="font-weight: normal;">${condition.charAt(0).toUpperCase() + condition.slice(1)}</span>
                                        </p>
                                        <p class="mb-1" style="font-size: 1.1rem; font-weight: bold;">
                                            <i class="bi bi-thermometer-half me-1"></i> Temperature:
                                            <span style="font-weight: normal;">${temp.toFixed(1)}°C</span>
                                        </p>
                                        <p class="mb-1" style="font-size: 1.1rem; font-weight: bold;">
                                            <i class="bi bi-droplet me-1"></i> Humidity:
                                            <span style="font-weight: normal;">${entry.main.humidity}%</span>
                                        </p>
                                    </div>
                                </div>
                            `;
                        }
                        html += '</div>';
                        document.getElementById(containerId).insertAdjacentHTML('beforeend', html);

                        
                        addForecastNotifications(data.city.name, data.city.country, data.list);

                    } catch (error) {
                        document.getElementById(containerId).innerHTML = '<p>Error loading hourly forecast data.</p>';
                    }
                }

                
                async function getDailyForecast(city, containerId) {
                    const url = `https://api.openweathermap.org/data/2.5/forecast?q=${city}&appid=${apiKey}&units=metric`;
                    try {
                        const response = await fetch(url);
                        const data = await response.json();
                        if (data.cod !== "200") {
                            throw new Error("Invalid daily forecast data");
                        }

                        const dailyData = {};
                        data.list.forEach((entry) => {
                            const date = new Date(entry.dt * 1000).toISOString().split("T")[0];
                            if (!dailyData[date]) dailyData[date] = [];
                            dailyData[date].push(entry);
                        });

                        let html = '<div class="row">';
                        Object.entries(dailyData).slice(0, 6).forEach(([date, entries]) => {
                            const tempMax = Math.max(...entries.map((e) => e.main.temp_max));
                            const tempMin = Math.min(...entries.map((e) => e.main.temp_min));
                            const mainCondition = entries[0].weather[0].description;

                            const dateString = new Date(date).toLocaleDateString([], {
                                weekday: 'short',
                                month: 'short',
                                day: 'numeric'
                            });

                            html += `
                                <div class="col-md-2 mb-3">
                                    <div class="card custom-card-small text-center p-3">
                                        <p class="mb-1" style="font-size: 1.2rem; font-weight: bold;">
                                            ${dateString}
                                        </p>
                                        <div class="my-2">${getWeatherIcon(mainCondition)}</div>
                                        <p class="mb-1" style="font-size: 1.1rem; font-weight: bold;">
                                            <i class="bi bi-info-circle me-1"></i> Condition:
                                            <span style="font-weight: normal;">${mainCondition.charAt(0).toUpperCase() + mainCondition.slice(1)}</span>
                                        </p>
                                        <p class="mb-1" style="font-size: 1.1rem; font-weight: bold;">
                                            <i class="bi bi-thermometer-high me-1"></i> Max Temperature:
                                            <span style="font-weight: normal;">${tempMax.toFixed(1)}°C</span>
                                        </p>
                                        <p class="mb-1" style="font-size: 1.1rem; font-weight: bold;">
                                            <i class="bi bi-thermometer-low me-1"></i> Min Temperature:
                                            <span style="font-weight: normal;">${tempMin.toFixed(1)}°C</span>
                                        </p>
                                        <p class="mb-1" style="font-size: 1.1rem; font-weight: bold;">
                                            <i class="bi bi-droplet me-1"></i> Humidity:
                                            <span style="font-weight: normal;">${entries[0].main.humidity}%</span>
                                        </p>
                                    </div>
                                </div>
                            `;
                        });
                        html += '</div>';
                        document.getElementById(containerId).insertAdjacentHTML('beforeend', html);

                    } catch (error) {
                        document.getElementById(containerId).innerHTML = '<p>Error loading daily forecast data.</p>';
                    }
                }

               
                async function loadAllForecastData() {
                    
                    allNotifications = [];

                    
                    if (<?php echo $preferred_city ? 'true' : 'false'; ?>) {
                        const cityName = "<?php echo $preferred_city; ?>";
                        
                        await getCityAndCountry(cityName, 'preferred-city-country');
                        
                        await getHourlyForecast(cityName, 'preferred-city-hourly-forecast');
                        
                        await getDailyForecast(cityName, 'preferred-city-daily-forecast');
                    }

                    
                    <?php if (!empty($other_cities)): ?>
                        <?php foreach ($other_cities as $index => $city): ?> {
                                const cityName = "<?php echo $city; ?>";
                                
                                await getCityAndCountry(cityName, 'other-city-country-<?php echo $index; ?>');
                                
                                await getHourlyForecast(cityName, 'other-cities-hourly-forecast-<?php echo $index; ?>');
                                
                                await getDailyForecast(cityName, 'other-cities-daily-forecast-<?php echo $index; ?>');
                            }
                        <?php endforeach; ?>
                    <?php endif; ?>

                    
                    displayNotifications();
                }

                document.addEventListener("DOMContentLoaded", () => {
                    
                    loadAllForecastData();
                });
            </script>

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        </div>
    </div>
</body>

</html>