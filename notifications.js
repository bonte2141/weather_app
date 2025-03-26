
const apiKey = '88a128d5abfcf946ae52e487b5ac0ef9';
const apiUrl = 'http://localhost/weather_app';


const relevantPhenomena = [
    "shower rain", "ragged shower rain", "shower rain and drizzle",
    "heavy shower rain and drizzle", "light intensity drizzle", "shower drizzle", "heavy intensity shower rain",
    "rain", "moderate rain", "heavy intensity rain", "very heavy rain", "extreme rain",
    "freezing rain", "thunderstorm", "ragged thunderstorm", "heavy intensity drizzle", "light thunderstorm",
    "heavy thunderstorm", "thunderstorm with rain", "thunderstorm with light rain",
    "thunderstorm with heavy rain", "thunderstorm with drizzle", "thunderstorm with light drizzle",
    "thunderstorm with heavy drizzle", "heavy intensity drizzle rain", "drizzle", "snow", "drizzle rain",
    "light intensity drizzle rain", "heavy snow", "light snow", "light shower snow", "shower snow",
    "heavy shower snow", "sleet", "light rain", "light rain and snow", "rain and snow", "tornado", "squalls",
    "volcanic ash", "sand", "dust", "smoke", "haze", "fog", "light intensity shower rain", "light shower sleet",
    "shower sleet", "shower snow"
];

let allNotifications = [];

function addCurrentNotification(cityName, countryCode, desc) {
    if (relevantPhenomena.includes(desc.toLowerCase())) {
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
                if (relevantPhenomena.includes(desc.toLowerCase())) {
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
    notificationCount.textContent = (allNotifications.length > 3) ? "3+" : allNotifications.length;

    allNotifications.forEach(notif => {
        const li = document.createElement("li");
        li.className = "dropdown-item d-flex justify-content-between align-items-center";
        li.innerHTML = `<span>${notif.message}</span>`;
        notificationList.appendChild(li);
    });
}


async function getCityAndCountry(city) {
    const url = `https://api.openweathermap.org/data/2.5/weather?q=${encodeURIComponent(city)}&appid=${apiKey}&units=metric`;
    try {
        const response = await fetch(url);
        const data = await response.json();
        if (data.cod !== 200) throw new Error("City not found or invalid data");

        addCurrentNotification(data.name, data.sys.country, data.weather[0].description);
    } catch (error) {
        console.error("Error fetching city and country:", error);
    }
}

async function getHourlyForecast(city) {
    const url = `https://api.openweathermap.org/data/2.5/forecast?q=${encodeURIComponent(city)}&appid=${apiKey}&units=metric`;
    try {
        const response = await fetch(url);
        const data = await response.json();
        if (data.cod !== "200") throw new Error("Invalid forecast data");

        addForecastNotifications(data.city.name, data.city.country, data.list);
    } catch (error) {
        console.error("Error getHourlyForecast:", error);
    }
}

async function getDailyForecast(city) {
    const url = `https://api.openweathermap.org/data/2.5/forecast?q=${encodeURIComponent(city)}&appid=${apiKey}&units=metric`;
    try {
        const response = await fetch(url);
        const data = await response.json();
        if (data.cod !== "200") throw new Error("Invalid daily forecast data");
    } catch (error) {
        console.error("Error getDailyForecast:", error);
    }
}


async function loadAllForecastData(preferredCity, otherCities) {
    allNotifications = [];

    if (preferredCity) {
        await getCityAndCountry(preferredCity);
        await getHourlyForecast(preferredCity);
        await getDailyForecast(preferredCity);
    }

    for (const cityName of otherCities) {
        await getCityAndCountry(cityName);
        await getHourlyForecast(cityName);
        await getDailyForecast(cityName);
    }

    displayNotifications();
}
