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
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>ClimaCast Home</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

    <style>
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
            font-size: 1.2rem;
            margin-top: 15px;
        }

        .custom-card .btn {
            font-size: 1.2rem;
            font-weight: bold;
        }

        .divider {
            height: 2px;
            background-color: #343a40;
            margin: 30px 0;
        }

        .city-header {
            text-align: center;
            margin: 15px 0;
        }

        .weather-overview-icon {
            text-align: center;
            margin-bottom: 15px;
        }

        .custom-card-small-horizontal {
            margin-bottom: 20px;
        }

        .card-title-horizontal {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 1.1rem;
        }

        .card-content-horizontal .icon-text {
            display: block;
            margin-bottom: 5px;
        }

        .cities-row {
            margin-bottom: 25px;
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
        
        <div class="card custom-card">
            
            <h2 class="text-light text-center mb-4">
                Welcome back, <strong><?php echo $username; ?></strong>!
            </h2>

            <div class="container mt-5">
                <?php if (empty($preferred_city)): ?>
                    
                    <div class="card custom-card">
                        <h2 class="text-light text-center mb-4">
                            <strong>You don't have a preferred city set yet!</strong>
                        </h2>
                        <p class="text-center">
                            Set your preferred city to get personalized updates tailored for you.
                        </p>
                        <div class="text-center">
                            <a href="profile-user.php" class="btn btn-light">Go to Profile</a>
                        </div>
                    </div>
                <?php else: ?>
                    
                    <div class="weather-overview-card">
                        <h2 class="weather-title">
                            Current Weather in <strong><span id="city-country"></span></strong>
                        </h2>
                        <div class="text-center weather-overview-icon" id="main-weather-icon"></div>
                        <div class="text-center weather-condition">
                            <p>
                                <i class="bi bi-info-circle"></i>
                                <strong id="condition-main"></strong>:
                                <span id="condition-description"></span>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card custom-card-small">
                                <div class="card-body">
                                    <h5 class="card-title text-center">Temperature</h5>
                                    <p><i class="bi bi-thermometer"></i>
                                        <strong>Temperature:</strong> <span id="temperature"></span>°C
                                    </p>
                                    <p><i class="bi bi-thermometer-half"></i>
                                        <strong>Feels Like:</strong> <span id="feels-like"></span>°C
                                    </p>
                                    <p><i class="bi bi-thermometer-low"></i>
                                        <strong>Min Temp:</strong> <span id="temp-min"></span>°C
                                    </p>
                                    <p><i class="bi bi-thermometer-high"></i>
                                        <strong>Max Temp:</strong> <span id="temp-max"></span>°C
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card custom-card-small">
                                <div class="card-body">
                                    <h5 class="card-title text-center">General</h5>
                                    <p><i class="bi bi-droplet"></i>
                                        <strong>Humidity:</strong> <span id="humidity"></span>%
                                    </p>
                                    <p><i class="bi bi-wind"></i>
                                        <strong>Wind:</strong> <span id="wind-speed"></span> km/h
                                        <span id="wind-dir"></span>
                                    </p>
                                    <p><i class="bi bi-speedometer2"></i>
                                        <strong>Pressure:</strong> <span id="pressure"></span> hPa
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card custom-card-small">
                                <div class="card-body">
                                    <h5 class="card-title text-center">Additional</h5>
                                    <p><i class="bi bi-eye"></i>
                                        <strong>Visibility:</strong> <span id="visibility"></span> km
                                    </p>
                                    <p><i class="bi bi-sunrise"></i>
                                        <strong>Sunrise:</strong> <span id="sunrise"></span>
                                    </p>
                                    <p><i class="bi bi-sunset"></i>
                                        <strong>Sunset:</strong> <span id="sunset"></span>
                                    </p>
                                    <p><i class="bi bi-cloud"></i>
                                        <strong>Cloud Coverage:</strong> <span id="cloud-coverage"></span>%
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            
            <div class="divider"></div>

            
            <div class="container mt-5">
                <?php if (empty($other_cities)): ?>
                    <div class="card custom-card">
                        <h2 class="text-light text-center mb-4">
                            <strong>Other Cities</strong>
                        </h2>
                        <p class="text-center">
                            Add other cities you might be interested in keeping track of!
                        </p>
                        <div class="text-center">
                            <a href="locations-user.php" class="btn btn-light">Add Cities</a>
                        </div>
                    </div>
                <?php else: ?>
                    <h4 class="other-section-title">Other Cities</h4>
                    <div id="other-cities-weather" class="row">
                        
                    </div>
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

                const preferredCityPhp = <?php echo $preferred_city ? json_encode($preferred_city) : "null"; ?>;
                const otherCitiesPhp = <?php echo json_encode($other_cities); ?>;

            
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

                function getIconClass(description) {
                    const condition = weatherIcons[description.toLowerCase()];
                    if (!condition) {
                        return `<i class="bi bi-question-circle-fill text-muted" style="font-size: 3rem;"></i>`;
                    }
                    return `<i class="bi ${condition.icon} ${condition.color}" style="font-size: 3rem;"></i>`;
                }

                function getWindDirection(deg) {
                    const sectors = ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW'];
                    return sectors[Math.round(deg / 45) % 8];
                }

      
                async function fetchCityData(city, isPreferred = false) {
                    try {
                        
                        const urlCurr = `https://api.openweathermap.org/data/2.5/weather?q=${encodeURIComponent(city)}&appid=${apiKey}&units=metric`;
                        const resCurr = await fetch(urlCurr);
                        const dataCurr = await resCurr.json();

                        
                        if (!dataCurr || dataCurr.cod !== 200) {
                            throw new Error("Invalid city data or city not found: " + city);
                        }

                        if (isPreferred) {
                            updatePreferredCityUI(dataCurr);
                        }

                        const cityCardHtml = buildOtherCityCard(dataCurr);

                        
                        const desc = dataCurr.weather[0].description;
                        addCurrentNotification(dataCurr.name, dataCurr.sys.country, desc);

                        
                        const urlForecast = `https://api.openweathermap.org/data/2.5/forecast?q=${encodeURIComponent(city)}&appid=${apiKey}&units=metric`;
                        const resForecast = await fetch(urlForecast);
                        const dataForecast = await resForecast.json();

                        
                        if (!dataForecast || dataForecast.cod !== "200") {
                            throw new Error("Invalid forecast data for city: " + city);
                        }

                        addForecastNotifications(dataForecast.city.name, dataForecast.city.country, dataForecast);

                        return cityCardHtml;
                    } catch (err) {
                        console.error("Eroare la fetchCityData pentru " + city, err);
                        return `<div class="card custom-card">
                                  <h5 class="text-center">Error loading data for ${city}</h5>
                                </div>`;
                    }
                }

                function buildOtherCityCard(data) {
                    const iconHtml = getIconClass(data.weather[0].description);
                    return `
                      <div class="card custom-card">
                        <div class="city-header">
                          <h4>${data.name}, ${data.sys.country}</h4>
                          <div class="weather-overview-icon">${iconHtml}</div>
                          <div class="weather-condition">
                            <p>
                              <i class="bi bi-info-circle"></i>
                              <strong>Condition:</strong>
                              ${data.weather[0].description.charAt(0).toUpperCase() + data.weather[0].description.slice(1)}
                            </p>
                          </div>
                        </div>
                        <div class="row card-row">
                          
                          <div class="custom-card-small-horizontal">
                            <div class="card-title-horizontal">Temperature</div>
                            <div class="card-content-horizontal">
                              <span class="icon-text">
                                <i class="bi bi-thermometer"></i>
                                <strong>Temperature:</strong> ${data.main.temp}°C
                              </span>
                              <span class="icon-text">
                                <i class="bi bi-thermometer-half"></i>
                                <strong>Feels Like:</strong> ${data.main.feels_like}°C
                              </span>
                              <span class="icon-text">
                                <i class="bi bi-thermometer-low"></i>
                                <strong>Min Temp:</strong> ${data.main.temp_min}°C
                              </span>
                              <span class="icon-text">
                                <i class="bi bi-thermometer-high"></i>
                                <strong>Max Temp:</strong> ${data.main.temp_max}°C
                              </span>
                            </div>
                          </div>
                          
                          <div class="custom-card-small-horizontal">
                            <div class="card-title-horizontal">General</div>
                            <div class="card-content-horizontal">
                              <span class="icon-text">
                                <i class="bi bi-droplet"></i>
                                <strong>Humidity:</strong> ${data.main.humidity}%
                              </span>
                              <span class="icon-text">
                                <i class="bi bi-wind"></i>
                                <strong>Wind:</strong> ${data.wind.speed} km/h
                              </span>
                              <span class="icon-text">
                                <i class="bi bi-speedometer2"></i>
                                <strong>Pressure:</strong> ${data.main.pressure} hPa
                              </span>
                            </div>
                          </div>
                          
                          <div class="custom-card-small-horizontal">
                            <div class="card-title-horizontal">Additional</div>
                            <div class="card-content-horizontal">
                              <span class="icon-text">
                                <i class="bi bi-eye"></i>
                                <strong>Visibility:</strong> ${(data.visibility / 1000).toFixed(1)} km
                              </span>
                              <span class="icon-text">
                                <i class="bi bi-sunrise"></i>
                                <strong>Sunrise:</strong>
                                ${new Date(data.sys.sunrise * 1000).toLocaleTimeString()}
                              </span>
                              <span class="icon-text">
                                <i class="bi bi-sunset"></i>
                                <strong>Sunset:</strong>
                                ${new Date(data.sys.sunset * 1000).toLocaleTimeString()}
                              </span>
                              <span class="icon-text">
                                <i class="bi bi-cloud"></i>
                                <strong>Cloud Coverage:</strong> ${data.clouds.all}%
                              </span>
                            </div>
                          </div>
                        </div>
                      </div>
                    `;
                }

                function updatePreferredCityUI(data) {
                    const condition = data.weather[0].description.toLowerCase();
                    const mainIcon = weatherIcons[condition] || {
                        icon: "bi-question-circle",
                        color: "text-secondary"
                    };
                    document.getElementById('main-weather-icon').innerHTML =
                        `<i class="bi ${mainIcon.icon} ${mainIcon.color}" style="font-size: 5rem;"></i>`;

                    document.getElementById('condition-main').textContent = "Condition";
                    document.getElementById('condition-description').textContent =
                        condition.charAt(0).toUpperCase() + condition.slice(1);

                    const cityCountry = `${data.name}, ${data.sys.country}`;
                    document.getElementById('city-country').textContent = cityCountry;

                    document.getElementById('humidity').textContent = data.main.humidity;
                    document.getElementById('wind-speed').textContent = data.wind.speed;
                    document.getElementById('wind-dir').textContent = getWindDirection(data.wind.deg);
                    document.getElementById('pressure').textContent = data.main.pressure;
                    document.getElementById('temperature').textContent = data.main.temp;
                    document.getElementById('feels-like').textContent = data.main.feels_like;
                    document.getElementById('temp-min').textContent = data.main.temp_min;
                    document.getElementById('temp-max').textContent = data.main.temp_max;
                    document.getElementById('visibility').textContent = (data.visibility / 1000).toFixed(1);
                    document.getElementById('sunrise').textContent =
                        new Date(data.sys.sunrise * 1000).toLocaleTimeString();
                    document.getElementById('sunset').textContent =
                        new Date(data.sys.sunset * 1000).toLocaleTimeString();
                    document.getElementById('cloud-coverage').textContent = data.clouds.all;
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

                
                function addForecastNotifications(cityName, countryCode, forecastData) {
                    const nowLocal = new Date();
                    const yearNow = nowLocal.getFullYear();
                    const monthNow = nowLocal.getMonth();
                    const dayNow = nowLocal.getDate();

                    forecastData.list.forEach(entry => {
                        const forecastDate = new Date(entry.dt * 1000);

                        if (
                            forecastDate.getFullYear() === yearNow &&
                            forecastDate.getMonth() === monthNow &&
                            forecastDate.getDate() === dayNow
                        ) {
                            if (forecastDate > nowLocal) {
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

             
                async function loadAllData() {
                    
                    allNotifications = [];

                    
                    
                    

                    
                    if (preferredCityPhp) {
                        await fetchCityData(preferredCityPhp, true);
                    }

                    
                    const otherCitiesContainer = document.getElementById('other-cities-weather');
                    if (otherCitiesContainer && otherCitiesPhp && otherCitiesPhp.length > 0) {
                        
                        if (!otherCitiesContainer.hasChildNodes()) {
                            otherCitiesContainer.innerHTML = '<p class="text-muted">Loading ...</p>';
                        }

                        const cardsPromises = [];
                        for (let city of otherCitiesPhp) {
                            cardsPromises.push(fetchCityData(city, false));
                        }
                        const cards = await Promise.all(cardsPromises);

                        let finalHtml = '';
                        for (let i = 0; i < cards.length; i += 2) {
                            const group = cards.slice(i, i + 2).join('');
                            finalHtml += `<div class="row cities-row">${group}</div>`;
                        }
                        otherCitiesContainer.innerHTML = finalHtml;
                    }

                    
                    displayNotifications();
                }


                document.addEventListener("DOMContentLoaded", () => {
                    loadAllData();
                });
            </script>

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        </div>
    </div>
</body>

</html>