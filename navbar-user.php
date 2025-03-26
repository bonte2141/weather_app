<nav class="navbar navbar-expand-lg bg-light" style="padding-top: 0; padding-bottom: 0;">
        <div class="container-fluid">
            <a class="navbar-brand" href="realtime-user.php">
                <img src="climacast.webp" alt="ClimaCast" width="125" height="125" class="d-inline-block align-top">
            </a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a href="realtime-user.php" class="btn btn-dark fw-bold me-2" style="font-size: 23px;">Real-Time Weather</a>
                    </li>
                    <li class="nav-item">
                        <a href="forecast-user.php" class="btn btn-dark fw-bold me-2" style="font-size: 23px;">Forecast</a>
                    </li>
                    <li class="nav-item">
                        <a href="locations-user.php" class="btn btn-dark fw-bold me-2" style="font-size: 23px;">Locations</a>
                    </li>
                    <li class="nav-item">
                        <a href="interactivemaps-user.php" class="btn btn-dark fw-bold me-2" style="font-size: 23px;">Interactive Maps</a>
                    </li>
                    <li class="nav-item">
                        <a href="feedback-user.php" class="btn btn-dark fw-bold me-2" style="font-size: 23px;">Feedback</a>
                    </li>
                </ul>
            </div>
            <div class="dropdown me-3">
                <button class="btn btn-dark" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-bell" style="font-size: 1.5rem;"></i>
                    <span id="notification-count" class="badge bg-danger"
                        style="position: absolute; top: 5px; right: 5px; display: none;">0</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown" style="width: 550px;">
                    <li class="dropdown-header"
                        style="color: #ffffff; font-weight: bold; font-size: 1.5rem; text-align: center;">
                        Weather Alerts
                    </li>
                    <li id="notification-list" style="max-height: 400px; overflow-y: auto;">
                        
                    </li>
                </ul>
            </div>

            <div class="dropdown">
                <button class="btn btn-dark fw-bold dropdown-toggle" type="button" id="userDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 23px;">
                    <i class="bi bi-person-circle" style="font-size: 1.5rem;"></i>
                    <?php echo $username; ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item fw-bold" href="profile-user.php">
                            <i class="bi bi-person"></i> My Profile</a></li>

                    </li>
                    <li><a class="dropdown-item text-danger fw-bold" href="logout.php">
                            <i class="bi bi-box-arrow-right"></i> Log Out</a></li>
                </ul>
            </div>
        </div>
        </div>
    </nav>