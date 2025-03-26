<?php
include 'auth.php'; 
if ($_SESSION['user_type'] !== 'Admin') {
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
    <title>ClimaCast Users</title>
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
            margin: 50px auto;
            padding: 40px;
            background-color: #1c1e21;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            color: #ffffff;
            text-align: center;
        }

        
        .location-card {
            background-color: #181a1d;
            
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
            color: #ffffff;
        }

        
        .forecast-title {
            font-size: 3rem;
            font-weight: bold;
            color: #ffffff;
            text-align: center;
            margin-bottom: 30px;
        }

        
        .table-dark th,
        .table-dark td {
            font-size: 1.2rem;
            vertical-align: middle;
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
    </style>
</head>

<body style="background-color: #121212; color: #ffffff;">

    
    <?php include 'navbar-admin.php'; ?>

    <div class="container mt-5">
        <div class="custom-card">
            <h1 class="forecast-title">Users</h1>
            <h4 class="text-center mb-4" style="font-size:1.2rem;">
                Manage the registered users below.
            </h4>

            <div class="location-card">
                <div class="table-responsive">
                    <table class="table table-dark table-striped text-center">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>User Type</th>
                                <th>Created At</th>
                                <th>Preferred City</th>
                                <th>Action</th> 
                            </tr>
                        </thead>
                        <tbody id="users-table-body">
                            
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
        
        
        document.addEventListener('DOMContentLoaded', loadUsers);



        async function deleteUser(userId) {
            if (confirm("Are you sure you want to delete this user?")) {
                const formData = new FormData();
                formData.append('user_id', userId);

                try {
                    const response = await fetch('delete_user.php', {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();
                    alert(result.message);

                    if (result.success) {
                        loadUsers(); 
                    }

                } catch (error) {
                    console.error('Error:', error);
                    alert("An error occurred while deleting the user.");
                }
            }
        }

        async function loadUsers() {
    try {
        const response = await fetch('get_users.php');
        const data = await response.json();

        if (data.success) {
            const tbody = document.getElementById('users-table-body');
            tbody.innerHTML = '';  

            data.users.forEach(user => {
                const row = document.createElement('tr');

                
                const actionButtons = `
                    <button onclick="deleteUser(${user.id})" class="btn-admin-action">Delete</button>
                `;

                
                row.innerHTML = `
                    <td>${user.id}</td>
                    <td>${user.username}</td>
                    <td>${user.email}</td>
                    <td>${user.user_type}</td>
                    <td>${user.created_at}</td>
                    <td>${user.preferred_city || 'N/A'}</td>
                    <td>${actionButtons}</td>
                `;
                tbody.appendChild(row);
            });
        } else {
            alert('Failed to load users.');
        }
    } catch (error) {
        console.error('Error loading users:', error);
    }
}

    </script>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>