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
  <title>Temperature Map</title>
  <script src="https://cdn.maptiler.com/maptiler-sdk-js/v2.0.3/maptiler-sdk.umd.min.js"></script>
  <link href="https://cdn.maptiler.com/maptiler-sdk-js/v2.0.3/maptiler-sdk.css" rel="stylesheet" />
  <script src="https://cdn.maptiler.com/maptiler-weather/v2.0.0/maptiler-weather.umd.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: sans-serif;
      background-color: #121212;
      color: #ffffff;
    }

    #map {
      position: relative;
      width: 100%;
      height: 700px;
      border-radius: 10px;
      overflow: hidden;
    }



    #pointer-data {
      z-index: 1;
      position: absolute;
      
      top: 40px;
      
      left: 10px;
      
      font-size: 18px;
      
      font-weight: bold;
      
      color: #fff;
      
      text-shadow: 0px 0px 10px #0007;
      
      margin: 0;
      
    }

    #variable-name {
      z-index: 1;
      position: absolute;
      
      top: 10px;
      
      left: 10px;
      
      font-size: 20px;
      font-weight: bold;
      
      color: #fff;
      
      text-shadow: 0px 0px 10px #0007;
      
      margin: 0;
      
    }

    #time-info {
      position: absolute;
      bottom: 10px;
      left: 50%;
      transform: translateX(-50%);
      text-align: center;
      text-shadow: 0px 0px 5px black;
      color: white;
      font-size: 18px;
      font-weight: 500;
      padding: 5px 10px;
      background: rgba(0, 0, 0, 0.5);
      border-radius: 10px;
      display: inline-block;
      white-space: nowrap;
      z-index: 10;
      
    }



    #time-text {
      font-size: 12px;
      font-weight: 600;
    }

    .navbar .dropdown-toggle {
      background-color: #212529;
      
      color: #ffffff;
      
      font-weight: bold;
      border: 2px solid #ffffff;
      
      font-size: 23px;
      
      transition: all 0.3s ease;
    }

    
    .navbar .dropdown-toggle:hover {
      background-color: #343a40;
      
      color: #ffffff;
      
    }

    
    .dropdown-menu {
      background-color: #212529;
      
      border: 1px solid #ffffff;
      
      color: #ffffff;
      
      box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.15);
      
    }

    
    .dropdown-menu .dropdown-item {
      color: #ffffff;
      
      font-weight: bold;
      
      font-size: 18px;
      
    }

    
    .dropdown-menu .dropdown-item:hover,
    .dropdown-menu .dropdown-item:focus {
      background-color: #343a40;
      
      color: #ffffff;
      
    }

    
    .dropdown-menu .dropdown-item.text-danger {
      color: #ff4d4d;
      
      font-weight: bold;
      
    }

    .dropdown-menu .dropdown-item.text-danger:hover,
    .dropdown-menu .dropdown-item.text-danger:focus {
      background-color: #721c24;
      
      color: #ffffff;
      
    }

    
    .dropdown-menu .dropdown-item:active {
      background-color: #343a40 !important;
      
      color: #ffffff !important;
    }

    
    .navbar .dropdown-toggle {
      outline: none;
      
      box-shadow: none;
      
      border: none;
      
    }

    
    .dropdown-menu {
      border: none;
      
      outline: none;
      
      box-shadow: none;
      
    }

    
    .navbar .dropdown-toggle:focus,
    .navbar .dropdown-toggle:hover {
      outline: none;
      box-shadow: none;
      border: none;
    }

    
    .dropdown-menu .dropdown-item:focus,
    .dropdown-menu .dropdown-item:hover {
      outline: none;
      box-shadow: none;
      border: none;
    }

    .custom-card {
      background-color: #1c1e21;
      
      border-radius: 10px;
      
      padding: 20px;
      
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
      
      color: #ffffff;
      
      max-width: 95%;
      
      margin: 20px auto;
      
    }


    .custom-card h1 {
      font-size: 2.5rem;
      
      margin-bottom: 20px;
      font-weight: bold;
    }

    #controls {
      display: flex;
      flex-direction: row;
      
      align-items: center;
      gap: 10px;
      justify-content: center;
      
      margin-top: 10px;
      
    }

    #progress-bar {
      appearance: none;
      background: #555;
      height: 5px;
      border-radius: 5px;
      outline: none;
      cursor: pointer;
    }

    #progress-bar::-webkit-slider-thumb {
      appearance: none;
      width: 15px;
      height: 15px;
      background: #fff;
      border: 2px solid #000;
      border-radius: 50%;
      cursor: pointer;
    }

    #controls {
      margin-top: 20px;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    #controls .btn {
      width: 60px;
      
      height: 60px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      
      margin: 0 10px;
      
    }

    #progress-bar {
      width: 70%;
      
      margin: 0 auto;
      margin-bottom: 10px;
      
    }

    #controls .btn i {
      font-size: 1.5rem;
      
    }

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
  </style>
</head>

<body style="background-color: #121212; color: #ffffff;">
  
  <?php include 'navbar-user.php'; ?>

  
  <div class="container mt-5">
    <div class="custom-card">
      
      <h1 class="text-center fw-bold" style="font-size: 2.5rem; margin-bottom: 20px;">Temperature Map</h1>

      
      <div id="map">
        <div id="variable-name">Temperature</div>
        <div id="pointer-data"></div>
        <div id="time-info">
          <span id="time-text"></span>
        </div>
      </div>
      <div id="controls">
        <input id="progress-bar" type="range" min="0" max="100" value="0" step="1">
        <div style="display: flex; justify-content: center; align-items: center;">
          <button id="start-btn" class="btn btn-light">
            <i class="bi bi-skip-start"></i>
          </button>
          <button id="play-pause-btn" class="btn btn-light">
            <i id="play-pause-icon" class="bi bi-play"></i>
          </button>
          <button id="end-btn" class="btn btn-light">
            <i class="bi bi-skip-end"></i>
          </button>
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
        

        
        maptilersdk.config.apiKey = 't4S6s9hjSYwe0TefAWB2';
        const map = (window.map = new maptilersdk.Map({
          container: 'map',
          style: maptilersdk.MapStyle.BACKDROP,
          zoom: 3,
          center: [-94.77, 38.57],
          hash: true,
        }));

        const timeTextDiv = document.getElementById("time-text");
        const pointerDataDiv = document.getElementById("pointer-data");
        let pointerLngLat = null;

        
        const weatherLayer = new maptilerweather.TemperatureLayer({
          colorramp: maptilerweather.ColorRamp.builtin.TEMPERATURE_3
        });

        weatherLayer.on("sourceReady", () => {
          weatherLayer.animateByFactor(0); 
          refreshTime();
          updateProgressBar();
        });


        weatherLayer.on("tick", () => {
          updateProgressBar(); 
          refreshTime(); 
          updatePointerValue(pointerLngLat); 
        });


        map.on('load', function() {
          map.setPaintProperty("Water", 'fill-color', "rgba(0, 0, 0, 0.4)");
          map.addLayer(weatherLayer, 'Water');
          weatherLayer.animateByFactor(3600);
        });

        map.on('mouseout', function(evt) {
          if (!evt.originalEvent.relatedTarget) {
            pointerDataDiv.innerText = "";
            pointerLngLat = null;
          }
        });

        
        function refreshTime() {
          const d = weatherLayer.getAnimationTimeDate();
          const timeTextDiv = document.getElementById("time-text");
          if (timeTextDiv) {
            timeTextDiv.innerText = d ? d.toString() : "No Data";
          }
        }


        
        function updatePointerValue(lngLat) {
          if (!lngLat) return;
          pointerLngLat = lngLat;
          const value = weatherLayer.pickAt(lngLat.lng, lngLat.lat);
          if (!value) {
            pointerDataDiv.innerText = "";
            return;
          }
          pointerDataDiv.innerText = `${value.value.toFixed(1)}°`
        }

        map.on('mousemove', (e) => {
          updatePointerValue(e.lngLat);
        });
        
        const playPauseBtn = document.getElementById("play-pause-btn");
        const playPauseIcon = document.getElementById("play-pause-icon");
        const startBtn = document.getElementById("start-btn");
        const endBtn = document.getElementById("end-btn");
        const progressBar = document.getElementById("progress-bar");

        let isPlaying = false; 

        playPauseBtn.addEventListener("click", () => {
          if (isPlaying) {
            
            weatherLayer.animateByFactor(0);
            playPauseIcon.classList.replace("bi-pause", "bi-play");
          } else {
            
            weatherLayer.animateByFactor(3600);
            playPauseIcon.classList.replace("bi-play", "bi-pause");
          }
          isPlaying = !isPlaying; 
        });

        
        startBtn.addEventListener("click", () => {
          weatherLayer.setAnimationTime(weatherLayer.getAnimationStart());
          weatherLayer.animateByFactor(0); 
          isPlaying = false;
          playPauseIcon.classList.replace("bi-pause", "bi-play");
          refreshTime();
          updateProgressBar();
        });

        
        endBtn.addEventListener("click", () => {
          weatherLayer.setAnimationTime(weatherLayer.getAnimationEnd());
          weatherLayer.animateByFactor(0); 
          isPlaying = false;
          playPauseIcon.classList.replace("bi-pause", "bi-play");
          refreshTime();
          updateProgressBar();
        });



        progressBar.addEventListener("input", (e) => {
          const value = e.target.value;
          const startTime = weatherLayer.getAnimationStart();
          const endTime = weatherLayer.getAnimationEnd();
          const selectedTime = startTime + (value / 100) * (endTime - startTime);
          weatherLayer.setAnimationTime(selectedTime); 
          refreshTime(); 
        });



        
        function updateProgressBar() {
          const currentTime = weatherLayer.getAnimationTime();
          const startTime = weatherLayer.getAnimationStart();
          const endTime = weatherLayer.getAnimationEnd();
          const progress = ((currentTime - startTime) / (endTime - startTime)) * 100;
          progressBar.value = progress;
        }
      </script>
      
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>