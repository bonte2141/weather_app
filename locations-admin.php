<?php
include 'auth.php'; 
if ($_SESSION['user_type'] !== 'Admin') {
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

        .section-subtitle {
            text-align: center;
            margin-bottom: 15px;
            font-size: 1.2rem;
            font-weight: bold;
            color: #ffffff;
            
        }

        
        


        
        .btn-admin-action {
            background-color: #ffffff;
            color: #000000;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            padding: 6px 10px;
            margin: 3px;
            font-size: 1rem;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: background-color 0.3s, box-shadow 0.3s;
        }

        .btn-admin-action:hover {
            background-color: #e2e6ea;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transform: scale(1.03);
        }

        .btn-admin-action:focus,
        .btn-admin-action:active {
            outline: none;
            box-shadow: 0 0 0 2px #ccc;
        }

        
        .table-dark th,
        .table-dark td:not(:last-child) {
            font-size: 1.2rem;
        }

        #search-country {
            max-width: 400px;
            margin: 0 auto;
            padding: 10px;
            font-size: 1.1rem;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        
        #chk-admin+label {
            font-weight: bold;
            font-size: 1.1rem;
            color: #ffffff;
        }
    </style>
</head>

<body style="background-color: #121212; color: #ffffff;">
    
    <?php include 'navbar-admin.php'; ?>

    
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
            <h4 class="text-center mb-4" style="font-size:1.2rem;">
                Add or delete countries from the database!
            </h4>

            <div class="d-flex justify-content-center flex-wrap mb-4" style="gap:20px;">
                <div class="form-check form-switch">
                    <input class="form-check-input region-checkbox" type="checkbox" id="chk-adminpanel" checked>
                    <label class="form-check-label" for="chk-adminpanel">Manage Countries</label>
                </div>
            </div>


            
            
            <div id="card-adminpanel" class="continent-card">
                <h2>Manage Countries</h2>

                
                <form id="add-country-form" class="mb-4">
                    <div class="row g-3 align-items-center justify-content-center">
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="country-name" name="country_name" placeholder="Country Name" required>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" id="continent-name" name="continent" required>
                                <option value="" disabled selected>Select Continent</option>
                                <option value="Europe">Europe</option>
                                <option value="North America">North America</option>
                                <option value="South America">South America</option>
                                <option value="Asia">Asia</option>
                                <option value="Africa">Africa</option>
                                <option value="Oceania">Oceania</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn-admin-action w-100">Add Country</button>
                        </div>
                    </div>
                </form>
                <div class="mb-4">
                    <input type="text" id="search-country" class="form-control" placeholder="Search country...">
                </div>

                
                <div id="country-list">
                    <table class="table table-dark table-striped text-center">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Country Name</th>
                                <th>Continent</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="countries-table-body">
                            
                        </tbody>
                    </table>
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
            <a href="city_details-admin.php?country=${c.id}"
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

            
            const adminCheckbox = document.getElementById('chk-adminpanel');
            const adminPanel = document.getElementById('card-adminpanel');

            if (adminCheckbox && adminPanel) {
                adminPanel.classList.toggle('hidden', !adminCheckbox.checked);
            }
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
                    headers: {
                        'Content-Type': 'application/json'
                    },
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




        
        async function loadAdminCountries() {
            const response = await fetch('admin_get_countries.php');
            const data = await response.json();

            if (data.success) {
                const tbody = document.getElementById('countries-table-body');
                tbody.innerHTML = '';

                data.countries.forEach(country => {
                    tbody.innerHTML += `
                <tr>
                    <td>${country.id}</td>
                    <td>${country.name}</td>
                    <td>${country.continent}</td>
                    <td>
<button onclick="deleteCountry(${country.id})" class="btn-admin-action">Delete</button>
                    </td>
                </tr>`;
                });
            } else {
                alert(data.message);
            }
        }

        
        async function deleteCountry(id) {
            if (confirm("Are you sure you want to delete this country?")) {
                const formData = new FormData();
                formData.append('country_id', id);

                try {
                    const response = await fetch('delete_country.php', {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();
                    alert(result.message);

                    if (result.success) {
                        
                        loadAdminCountries();

                        
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
                    }

                } catch (error) {
                    console.error('Error:', error);
                    alert("An error occurred while deleting the country.");
                }
            }
        }


        
        document.addEventListener('DOMContentLoaded', loadAdminCountries);

        
        document.getElementById('add-country-form').addEventListener('submit', async function(e) {
            e.preventDefault(); 

            const countryName = document.getElementById('country-name').value.trim();
            const continentName = document.getElementById('continent-name').value;

            if (!countryName || !continentName) {
                alert("Please fill in all fields.");
                return;
            }

            const formData = new FormData();
            formData.append('country_name', countryName);
            formData.append('continent', continentName);

            try {
                const response = await fetch('add_country.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                alert(result.message);

                if (result.success) {
                    document.getElementById('add-country-form').reset(); 
                    loadAdminCountries(); 

                    
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
                }

            } catch (error) {
                console.error('Error:', error);
                alert("An error occurred. Please try again.");
            }
        });

        
        document.getElementById('search-country').addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const rows = document.querySelectorAll('#countries-table-body tr');

            rows.forEach(row => {
                const countryName = row.cells[1].textContent.toLowerCase();
                const continentName = row.cells[2].textContent.toLowerCase();

                if (countryName.includes(searchValue) || continentName.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>