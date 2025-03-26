<?php
include 'auth.php'; 

if ($_SESSION['user_type'] !== 'Admin') {
    header('Location: login.php'); 
    exit;
}


$country_id = $_GET['country'] ?? 0;


$sql = "SELECT name FROM countries WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $country_id);
$stmt->execute();
$result = $stmt->get_result();
$country_name = $result->fetch_assoc()['name'] ?? 'Unknown Country';

$stmt->close();

$username = $_SESSION['username'];
$preferred_city = $_SESSION['preferred_city'] ?? null;
$other_cities   = $_SESSION['other_cities'] ?? [];
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ClimaCast City Details</title>
    
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
            max-width: 1200px;
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
            color: #ffffff;
            ;
            
        }

        .location-search {
            max-width: 300px;
            margin: 0 auto 20px auto;
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

        .forecast-title {
            font-size: 3rem;
            font-weight: bold;
            color: #ffffff;
            text-align: center;
            margin-bottom: 30px;
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

        #favorite-locations,
        #other-locations {
            gap: 10px;
            
        }

        #divider-line {
            height: 2px;
            background-color: #343a40;
            
            border: none;
            margin: 30px 0;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            
        }

        
        .table-dark {
            background-color: #181a1d;
            color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
        }

        .table-dark th {
            background-color: #2a2d31;
            color: #ffffff;
            font-weight: bold;
            font-size: 1.2rem;
            text-align: center;
            padding: 12px;
        }

        .table-dark td {
            text-align: center;
            padding: 12px;
            font-size: 1.2rem;
        }

        
        .btn-delete-city {
            background-color: #dc3545;
            color: #ffffff;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            padding: 6px 10px;
            transition: background-color 0.3s, box-shadow 0.3s;
        }

        .btn-delete-city:hover {
            background-color: #c82333;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transform: scale(1.03);
        }

        .btn-delete-city:focus,
        .btn-delete-city:active {
            outline: none;
            box-shadow: 0 0 0 2px #ccc;
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

        #search-city {
            max-width: 400px;
            margin: 0 auto;
            padding: 10px;
            font-size: 1.1rem;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        .hidden {
            display: none !important;
        }
    </style>
</head>

<body style="background-color: #121212; color: #ffffff;">
    
    <?php include 'navbar-admin.php'; ?>

    
    <div class="container mt-5">
        
        <div class="custom-card">
            <h1 class="text-center forecast-title">Locations</h1>
            <h4 class="text-center mb-4" style="font-size:1.2rem;">Choose which locations you want to favorite!</h4>



            
            <div class="location-card">
                <h2 id="country-name"><?php echo $country_name; ?></h2>
                <input class="form-control location-search" id="search-locations" type="text" placeholder="Search locations...">
                <h4 id="cities-in-use-title" class="text-center mt-4" style="display: none; color: #ffffff; font-size: 1.5rem;">Cities in Use</h4>
                <div class="d-flex flex-column" id="location-container">
                    <div class="d-flex justify-content-center flex-wrap" id="favorite-locations"></div>
                    <hr id="divider-line" style="display: none; border: 1px solid #ccc; margin: 20px 0;">
                    <div class="d-flex justify-content-center flex-wrap" id="other-locations"></div>
                </div>
            </div>
            
            <h4 class="text-center mb-4" style="font-size:1.2rem;">Add or delete cities from the database!</h4>

            <div class="d-flex justify-content-center flex-wrap mb-4" style="gap:20px;">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="chk-manage-cities">
                    <label class="form-check-label fw-bold" for="chk-manage-cities">Manage Cities</label>
                </div>
            </div>

            
            <div id="card-manage-cities" class="location-card hidden">
                <h2>Manage Cities in <?php echo $country_name; ?></h2>

                
                <form id="add-city-form" class="mb-4">
                    <div class="row g-3 align-items-center justify-content-center">
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="city-name" name="city_name" placeholder="City Name" required>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn-admin-action w-100">Add City</button>
                        </div>
                    </div>
                </form>


                <div class="mb-4">
                    <input type="text" id="search-city" class="form-control" placeholder="Search city...">
                </div>

                
                <div id="city-list">
                    <table class="table table-dark table-striped text-center">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>City Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="cities-table-body">
                            
                        </tbody>
                    </table>
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
        
        async function fetchLocations(countryId) {
            try {
                const response = await fetch(`${apiUrl}/get_cities.php?country_id=${countryId}`);
                const data = await response.json();
                if (data.success) {
                    renderLocationButtons(data.cities); 
                } else {
                    console.error("Failed to fetch locations:", data.message);
                }
            } catch (error) {
                console.error("Error fetching locations:", error);
            }
        }


        function renderLocationButtons(locations) {
            const favoriteContainer = document.getElementById('favorite-locations');
            const otherContainer = document.getElementById('other-locations');
            const dividerLine = document.getElementById('divider-line');
            const citiesInUseTitle = document.querySelector('#cities-in-use-title'); 

            favoriteContainer.innerHTML = '';
            otherContainer.innerHTML = '';

            let hasFavorites = false;

            locations.forEach(location => {
                const button = document.createElement('a');
                button.className = 'btn-location';
                button.textContent = location.city_name;
                button.dataset.cityId = location.city_id;
                button.addEventListener('click', () => toggleFavoriteCity(location.city_id, location.is_favorite));

                if (location.is_favorite) {
                    favoriteContainer.appendChild(button);
                    hasFavorites = true;
                } else {
                    otherContainer.appendChild(button);
                }
            });

            
            dividerLine.style.display = hasFavorites ? 'block' : 'none';
            citiesInUseTitle.style.display = hasFavorites ? 'block' : 'none';
        }



        
        async function toggleFavoriteCity(cityId, isFavorite) {
            try {
                const response = await fetch(`${apiUrl}/toggle_favorite_city.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        city_id: cityId,
                        is_favorite: isFavorite
                    }),
                });
                const data = await response.json();
                if (data.success) {
                    fetchLocations(<?php echo $country_id; ?>); 
                } else {
                    console.error("Failed to toggle favorite:", data.error);
                }
            } catch (error) {
                console.error("Error toggling favorite:", error);
            }
        }

        
        document.addEventListener('DOMContentLoaded', () => {
            fetchLocations(<?php echo $country_id; ?>);
        });


        
        function filterLocations() {
            const searchInput = document.getElementById('search-locations');
            const filter = searchInput.value.toLowerCase();
            const favoriteButtons = document.querySelectorAll('#favorite-locations .btn-location');
            const otherButtons = document.querySelectorAll('#other-locations .btn-location');

            [...favoriteButtons, ...otherButtons].forEach(button => {
                const cityName = button.textContent.toLowerCase();
                if (cityName.includes(filter)) {
                    button.style.display = '';
                } else {
                    button.style.display = 'none';
                }
            });
        }


        
        document.getElementById('search-locations').addEventListener('input', filterLocations);

        document.addEventListener('DOMContentLoaded', function() {
            
            document.getElementById('chk-manage-cities').addEventListener('change', function() {
                document.getElementById('card-manage-cities').classList.toggle('hidden', !this.checked);
            });

            
            loadCities();

            
            document.getElementById('add-city-form').addEventListener('submit', async function(e) {
                e.preventDefault();
                const cityName = document.getElementById('city-name').value.trim();

                if (cityName) {
                    const formData = new FormData();
                    formData.append('city_name', cityName);
                    formData.append('country_id', <?php echo $country_id; ?>);

                    const response = await fetch('add_city.php', {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();
                    alert(result.message);

                    if (result.success) {
                        document.getElementById('add-city-form').reset();
                        loadCities(); 
                        fetchLocations(<?php echo $country_id; ?>); 
                    }
                }
            });


            
            document.getElementById('search-city').addEventListener('keyup', function() {
                const filter = this.value.toLowerCase();
                const rows = document.querySelectorAll('#cities-table-body tr');

                rows.forEach(row => {
                    const cityName = row.cells[1].textContent.toLowerCase();
                    row.style.display = cityName.includes(filter) ? '' : 'none';
                });
            });
        });

        
        async function loadCities() {
            const response = await fetch(`admin_get_cities.php?country_id=<?php echo $country_id; ?>`);
            const data = await response.json();

            if (data.success) {
                const tbody = document.getElementById('cities-table-body');
                tbody.innerHTML = '';

                data.cities.forEach(city => {
                    tbody.innerHTML += `
        <tr>
            <td>${city.city_id}</td>
            <td>${city.city_name}</td>
            <td>
                <button onclick="deleteCity(${city.city_id})" class="btn-admin-action">Delete</button>
            </td>
        </tr>
    `;
                });

            } else {
                alert(data.message);
            }
        }


        
        async function deleteCity(cityId) {
            if (confirm("Are you sure you want to delete this city?")) {
                const formData = new FormData();
                formData.append('city_id', cityId);

                const response = await fetch('delete_city.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                alert(result.message);

                if (result.success) {
                    loadCities(); 
                    fetchLocations(<?php echo $country_id; ?>); 
                }
            }
        }
        
        function saveCityManageState() {
            const isChecked = document.getElementById('chk-manage-cities').checked;
            fetch('save_city_preferences.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        manageCities: isChecked
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        console.error("Error saving city preferences:", data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
        }



        
        
        function restoreCityManageState() {
            fetch('get_city_preferences.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('chk-manage-cities').checked = data.manageCities;
                        document.getElementById('card-manage-cities').classList.toggle('hidden', !data.manageCities);
                    }
                })
                .catch(error => console.error('Error fetching city preferences:', error));
        }

        
        document.getElementById('chk-manage-cities').addEventListener('change', function() {
            document.getElementById('card-manage-cities').classList.toggle('hidden', !this.checked);
            saveCityManageState();
        });

        
        document.addEventListener('DOMContentLoaded', restoreCityManageState);
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>