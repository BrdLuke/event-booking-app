<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventTeam - Navbar</title>

    <?php
    include_once("bootstrap_resources.html");
    session_start();
    $connection = new mysqli();

    try {
        $connection->connect("localhost", "root", "", "Event_Management");
    } catch (Exception $e) {
        echo "Error";
    }
    ?>

    <style>
        .nav-item {
            margin-left: 0.5rem;
            margin-right: 0.5rem;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="/event_booking_app/index.php">
                <img src="/event_booking_app/images/logo-event-team.png" alt="EventTeam Logo" width="130">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-lg-0 mt-1">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="/event_booking_app/index.php">Home</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="/event_booking_app/explore.php">Explore</a>
                    </li>

                    <?php
                    if (isset($_SESSION["user"])) {
                        echo '
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                . <b>' . $_SESSION["user"]["name"] . '</b>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="/event_booking_app/user_profile.php">Profile</a></li>';
                        if ($_SESSION["user"]["role"] === "Organizer") {
                            echo '<li><a class="dropdown-item" href="/event_booking_app/organizer/add_event.php">Add Event</a></li>';
                        } else {
                            echo '<li><a class="dropdown-item" href="/event_booking_app/user_subscribed_events.php">Subscribed Events</a></li>';
                        }

                        // Check if the user is an 'admin'
                        $userEmail = $_SESSION["user"]["email"];

                        $queryUserRole = "SELECT Role FROM Users INNER JOIN Roles ON Users.Role = Roles.RoleName WHERE Email = '$userEmail'";
                        $resultUserRole = $connection->query($queryUserRole);

                        if ($resultUserRole->num_rows == 1) {
                            $record = $resultUserRole->fetch_assoc();
                            $userRole = $record["Role"];
                        }

                        if ($userRole === "Admin") {
                            echo '<li><a class="dropdown-item" href="/event_booking_app/admin/index.php">Admin Panel</a></li>';
                        }

                        echo '
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/event_booking_app/logout.php">Logout</a></li>
                            </ul>
                        </li>
                        ';
                    } else {
                        echo '
                        <li class="nav-item mb-1">
                            <a class="nav-link btn btn-primary text-light" href="login.php">Login</a>
                        </li>
                        ';
                    }
                    ?>

                    <li class="nav-item">
                        <form method="POST" class="d-flex">
                            <input class="form-control me-2" name="searchText" type="search" placeholder="Search"
                                aria-label="Search">
                            <button class="btn btn-outline-success" type="submit" name="search">Search</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["search"])) {
        $text = strtolower($connection->real_escape_string($_POST["searchText"]));
        $searchText = urlencode($text);
        header("Location: /event_booking_app/explore.php?q=$searchText");
        exit;
    }
    ?>
</body>

</html>