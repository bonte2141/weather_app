<?php
include 'auth.php'; 
if ($_SESSION['user_type'] !== 'User') {
    header('Location: login.php');
    exit;
}

$username       = $_SESSION['username'];
$preferred_city = $_SESSION['preferred_city'] ?? null;
$other_cities   = $_SESSION['other_cities'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>ClimaCast Locations</title>
    
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

        .continent-card {
            background-color: #181a1d;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
            color: #ffffff;
        }

        .continent-card h2 {
            font-size: 2.5rem;
            
            font-weight: bold;
            text-align: center;
            margin-bottom: 15px;
            color: #ffffff;
            
        }


        .continent-subtitle {
            text-align: center;
            margin-bottom: 15px;
            font-size: 1.1rem;
            color: #cccccc;
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
            display: none !important;
        }

        .continent-search {
            max-width: 300px;
        }
        
    </style>
</head>

<body style="background-color: #121212; color: #ffffff;">
    
    <?php include 'navbar-user.php'; ?>

    
    <div class="container mt-5">
        <div class="custom-card">
            <h1 class="text-center forecast-title">Locations</h1>
            <h4 class="text-center mb-4" style="font-size:1.2rem;">
                Choose which regions you want to display!
            </h4>

            
            <div class="d-flex justify-content-center flex-wrap mb-4" style="gap:20px;">
                <div class="form-check form-switch">
                    <input class="form-check-input region-checkbox" type="checkbox" id="chk-europe" checked>
                    <label class="form-check-label" for="chk-europe">Europe</label>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input region-checkbox" type="checkbox" id="chk-namerica" checked>
                    <label class="form-check-label" for="chk-namerica">North America</label>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input region-checkbox" type="checkbox" id="chk-samerica" checked>
                    <label class="form-check-label" for="chk-samerica">South America</label>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input region-checkbox" type="checkbox" id="chk-asia" checked>
                    <label class="form-check-label" for="chk-asia">Asia</label>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input region-checkbox" type="checkbox" id="chk-africa" checked>
                    <label class="form-check-label" for="chk-africa">Africa</label>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input region-checkbox" type="checkbox" id="chk-oceania" checked>
                    <label class="form-check-label" for="chk-oceania">Oceania</label>
                </div>
            </div>

            
            <div id="card-europe" class="continent-card">
                <h2>Europe</h2>
                <p class="continent-subtitle">
                    Browse the countries below and select your favorite locations to add to your list!
                </p>
                <div class="mb-4 d-flex justify-content-center">
                    <input class="form-control continent-search" id="search-europe" type="text"
                        placeholder="Search countries...">
                </div>
                <div class="d-flex flex-wrap justify-content-center" id="buttons-europe"></div>
            </div>

            
            <div id="card-namerica" class="continent-card">
                <h2>North America</h2>
                <p class="continent-subtitle">
                    Browse the countries below and select your favorite locations to add to your list!
                </p>
                <div class="mb-4 d-flex justify-content-center">
                    <input class="form-control continent-search" id="search-namerica" type="text"
                        placeholder="Search countries...">
                </div>
                <div class="d-flex flex-wrap justify-content-center" id="buttons-namerica"></div>
            </div>

            
            <div id="card-samerica" class="continent-card">
                <h2>South America</h2>
                <p class="continent-subtitle">
                    Browse the countries below and select your favorite locations to add to your list!
                </p>
                <div class="mb-4 d-flex justify-content-center">
                    <input class="form-control continent-search" id="search-samerica" type="text"
                        placeholder="Search countries...">
                </div>
                <div class="d-flex flex-wrap justify-content-center" id="buttons-samerica"></div>
            </div>

            
            <div id="card-asia" class="continent-card">
                <h2>Asia</h2>
                <p class="continent-subtitle">
                    Browse the countries below and select your favorite locations to add to your list!
                </p>
                <div class="mb-4 d-flex justify-content-center">
                    <input class="form-control continent-search" id="search-asia" type="text"
                        placeholder="Search countries...">
                </div>
                <div class="d-flex flex-wrap justify-content-center" id="buttons-asia"></div>
            </div>

            
            <div id="card-africa" class="continent-card">
                <h2>Africa</h2>
                <p class="continent-subtitle">
                    Browse the countries below and select your favorite locations to add to your list!
                </p>
                <div class="mb-4 d-flex justify-content-center">
                    <input class="form-control continent-search" id="search-africa" type="text"
                        placeholder="Search countries...">
                </div>
                <div class="d-flex flex-wrap justify-content-center" id="buttons-africa"></div>
            </div>

            
            <div id="card-oceania" class="continent-card">
                <h2>Oceania</h2>
                <p class="continent-subtitle">
                    Browse the countries below and select your favorite locations to add to your list!
                </p>
                <div class="mb-4 d-flex justify-content-center">
                    <input class="form-control continent-search" id="search-oceania" type="text"
                        placeholder="Search countries...">
                </div>
                <div class="d-flex flex-wrap justify-content-center" id="buttons-oceania"></div>
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
        

        
        let allCountries = [];

        async function fetchAllCountries() {
            const resp = await fetch(`${apiUrl}/get_countries.php`);
            const data = await resp.json();
            if (!data.success) {
                throw new Error("Error fetching countries: " + data.message);
            }
            return data.countries; 
        }

        function renderContinentButtons(continentName, containerId) {
            const container = document.getElementById(containerId);
            if (!container) return;
            
            const subset = allCountries.filter(co => co.continent === continentName);
            let html = "";
            subset.forEach(c => {
                html += `
            <a href="city_details-user.php?country=${c.id}"
               class="btn-continent-country"
               data-country-name="${c.name.toLowerCase()}">
               ${c.name}
            </a>`;
            });
            container.innerHTML = html;
        }

        function addSearchFilter(searchInputId, containerId) {
            const searchInput = document.getElementById(searchInputId);
            const container = document.getElementById(containerId);
            if (!searchInput || !container) return;

            searchInput.addEventListener('keyup', () => {
                const val = searchInput.value.toLowerCase();
                const btns = container.querySelectorAll('.btn-continent-country');
                btns.forEach(btn => {
                    const nm = btn.getAttribute('data-country-name') || "";
                    btn.style.display = nm.includes(val) ? "" : "none";
                });
            });
        }

        function toggleRegionCards() {
            document.getElementById('card-europe').classList.toggle(
                'hidden', !document.getElementById('chk-europe').checked
            );
            document.getElementById('card-namerica').classList.toggle(
                'hidden', !document.getElementById('chk-namerica').checked
            );
            document.getElementById('card-samerica').classList.toggle(
                'hidden', !document.getElementById('chk-samerica').checked
            );
            document.getElementById('card-asia').classList.toggle(
                'hidden', !document.getElementById('chk-asia').checked
            );
            document.getElementById('card-africa').classList.toggle(
                'hidden', !document.getElementById('chk-africa').checked
            );
            document.getElementById('card-oceania').classList.toggle(
                'hidden', !document.getElementById('chk-oceania').checked
            );
        }

      
        document.addEventListener('DOMContentLoaded', async () => {

            
            const checkboxes = document.querySelectorAll('.region-checkbox');
            checkboxes.forEach(chk => {
                chk.addEventListener('change', toggleRegionCards);
            });
            toggleRegionCards();

            
            try {
                allCountries = await fetchAllCountries();
                renderContinentButtons("Europe", "buttons-europe");
                renderContinentButtons("North America", "buttons-namerica");
                renderContinentButtons("South America", "buttons-samerica");
                renderContinentButtons("Asia", "buttons-asia");
                renderContinentButtons("Africa", "buttons-africa");
                renderContinentButtons("Oceania", "buttons-oceania");

                addSearchFilter("search-europe", "buttons-europe");
                addSearchFilter("search-namerica", "buttons-namerica");
                addSearchFilter("search-samerica", "buttons-samerica");
                addSearchFilter("search-asia", "buttons-asia");
                addSearchFilter("search-africa", "buttons-africa");
                addSearchFilter("search-oceania", "buttons-oceania");
            } catch (err) {
                console.error("Error init continents:", err);
            }
        });

        
function saveCheckboxState() {
    const checkboxes = document.querySelectorAll('.region-checkbox');
    const state = {};

    checkboxes.forEach(chk => {
        state[chk.id] = chk.checked;
    });

    fetch('save_region_preferences.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(state)
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            console.error("Error saving preferences:", data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}


function restoreCheckboxState() {
    fetch('get_region_preferences.php')
    .then(response => response.json())
    .then(data => {
        if (data.success && data.preferences) {
            const state = JSON.parse(data.preferences);
            const checkboxes = document.querySelectorAll('.region-checkbox');

            checkboxes.forEach(chk => {
                if (state.hasOwnProperty(chk.id)) {
                    chk.checked = state[chk.id];
                }
            });
            toggleRegionCards();  
        }
    })
    .catch(error => console.error('Error fetching preferences:', error));
}


document.querySelectorAll('.region-checkbox').forEach(chk => {
    chk.addEventListener('change', saveCheckboxState);
});


document.addEventListener('DOMContentLoaded', restoreCheckboxState);

    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>