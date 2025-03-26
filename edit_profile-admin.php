<?php
include 'auth.php'; 
require 'db.php'; 

if ($_SESSION['user_type'] !== 'Admin') {
    header('Location: login.php'); 
    exit;
}

$user_id = $_SESSION['user_id']; 
$preferred_city = $_SESSION['preferred_city'] ?? null;

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
    SELECT c.name 
    FROM favorite_cities fc
    JOIN cities c ON fc.city_id = c.id
    WHERE fc.user_id = ?
");
    $stmt_favorites->bind_param("i", $user_id);
    $stmt_favorites->execute();
    $result_favorites = $stmt_favorites->get_result();

    $other_cities = [];
    while ($row = $result_favorites->fetch_assoc()) {
        $other_cities[] = $row['name'];
    }
    $_SESSION['other_cities'] = $other_cities;

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


        .edit-profile-card {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #1c1e21;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            color: #ffffff;
            height: auto;
        }

        .edit-profile-label {
            font-size: 1.5rem;
            
            font-weight: bold;
            color: #ffffff;
            text-align: center;
            display: block;
        }

        .edit-profile-input {
            font-size: 1.1rem;
            
            padding: 12px;
            
            background-color: #181a1d;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            width: 100%;
        }

        .form-select {
            background-color: #181a1d;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            padding: 10px;
        }

        .edit-profile-input:focus {
            outline: none;
            box-shadow: 0 0 0 2px #ccc;
        }

        .form-select:focus {
            outline: none;
            box-shadow: 0 0 0 2px #ccc;
        }

        .edit-profile-button {
            background-color: #ffffff;
            color: #000000;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-size: 1.2rem;
            transition: all 0.3s;
            margin: 20px auto 0;
            
            display: block;
            width: auto;
            
        }

        .edit-profile-button:hover {
            background-color: #e2e6ea;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            transform: scale(1.03);
        }

        .edit-profile-title {
            font-size: 3rem;
            font-weight: bold;
            color: #ffffff;
            text-align: center;
            margin-bottom: 30px;
            
        }
    </style>
</head>

<body style="background-color: #121212; color: #ffffff;">

    
    <?php include 'navbar-admin.php'; ?>

    <div class="container mt-5">
        <div class="card edit-profile-card">
            <h1 class="edit-profile-title">Edit Your Profile</h1>
            <form method="POST" action="edit_profile-admin.php">

                
                <div class="mb-4">
                    <label for="username" class="edit-profile-label">Username</label>
                    <input type="text" class="edit-profile-input" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                </div>

                
                <div class="mb-4">
                    <label for="preferred_city" class="edit-profile-label">Preferred City</label>
                    <input type="text" class="edit-profile-input" id="preferred_city" name="preferred_city" value="<?php echo htmlspecialchars($user_city); ?>">
                </div>

                
                <button type="submit" class="edit-profile-button">Save Changes</button>
            </form>
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
        
        document.querySelector('form').addEventListener('submit', async (e) => {
            e.preventDefault(); 

            const formData = new FormData(e.target);

            try {
                const response = await fetch('process_edit_profile.php', {
                    method: 'POST',
                    body: formData,
                });

                const data = await response.json();

                if (data.success) {
                    alert(data.message);
                    location.reload(); 
                } else {
                    alert(data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while saving changes. Please try again.');
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>