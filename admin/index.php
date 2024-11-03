<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventTeam - Admin</title>

    <?php include_once("../bootstrap_resources.html"); ?>

    <link rel="stylesheet" href="/event_booking_app/css/styles.css">
</head>

<body>
    <?php
    include_once("../navbar.php");
    $connection = new mysqli();

    try {
        $connection->connect("localhost", "root", "", "Event_Management");
    } catch (Exception $e) {
        echo "Error";
    }

    if (!isset($_SESSION["user"])) {
        $errorMessage = urlencode("Register now. If you have an account, log in.");
        header("Location: /event_booking_app/index.php?error=$errorMessage");
        exit;
    }

    // Check if the user is an 'admin'
    $userEmail = $_SESSION["user"]["email"];

    $queryUserRole = "SELECT Role FROM Users INNER JOIN Roles ON Users.Role = Roles.RoleName WHERE Email = '$userEmail'";
    $resultUserRole = $connection->query($queryUserRole);

    if ($resultUserRole->num_rows == 1) {
        $record = $resultUserRole->fetch_assoc();
        $userRole = $record["Role"];
    }

    if ($userRole !== "Admin") {
        $errorMessage = urlencode("You are not available to use this service.");
        header("Location: /event_booking_app/index.php?error=$errorMessage");
        exit;
    } else {

        ?>

        <div class="form-container container shadow-lg mx-auto w-100">

            <h1 class="text-center"><b>Admin Panel</b></h1>
            <div class="row text-center">
                <div class="admin-section col-md-6 admin-element mt-5 border p-5">
                    <img src="/event_booking_app/images/admin-user-management-icon.png" alt="Users management" width="10%">
                    <h5><a href="admin_users_management.php">Users management</a></h5>
                    <small class="tooltip-text">Check all users</small>
                </div>

                <div class="admin-section col-md-6 admin-element mt-5 border p-5">
                    <img src="/event_booking_app/images/admin-events-management-icon.png" alt="Events management" width="10%">
                    <h5><a href="admin_events_management.php">Events management</a></h5>
                    <small class="tooltip-text">Check all the events with thier seats available</small>
                </div>  

                <div class="admin-section col-md-6 admin-element border p-5">
                    <img src="/event_booking_app/images/admin-subscribers-management-icon.png" alt="Subscribers management" width="10%">
                    <h5><a href="admin_subscribers_management.php">Subscribers management</a></h5>
                    <small class="tooltip-text">Check the subscribers at the specific event</small>
                </div>

                <div class="admin-section col-md-6 admin-element border p-5">
                    <img src="/event_booking_app/images/admin-statistics-management-icon.png" alt="Statistics management" width="10%">
                    <h5><a href="admin_statistics_management.php">Statistics management</a></h5>
                    <small class="tooltip-text">Check your statistics</small>
                </div>
            </div>

        </div>

        <?php
    }
    ?>
</body>

</html>