<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventTeam - Admin Users Management</title>

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

        <div class="mt-5 container">

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/event_booking_app/admin/index.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Events Management</li>
                </ol>
            </nav>

            <h1 class="text-center"><b>Events Management</b></h1>

            <table class="table table-bordered text-center table-hover mt-5 table-responsive">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Image</th>
                        <th scope="col">Title</th>
                        <th scope="col">Description</th>
                        <th scope="col">Start Date</th>
                        <th scope="col">End Date</th>
                        <th scope="col">Seats</th>
                        <th scope="col">Place</th>
                        <th scope="col">Price[€]</th>
                        <th scope="col">Organizer</th>
                        <th scope="col">Uploaded</th>
                    </tr>
                </thead>

                <?php
                $queryUsers = "SELECT * FROM Events";
                $resultQueryUsers = $connection->query($queryUsers);

                if ($resultQueryUsers->num_rows > 0) {
                    while ($record = $resultQueryUsers->fetch_assoc()) {
                        $eventID = $record["EventID"];
                        $eventImage = $record["Image"];
                        $eventTitle = $record["Title"];
                        $eventDescription = $record["Description"];
                        $eventStartDate = $record["StartDate"];
                        $eventEndDate = $record["EndDate"];
                        $eventSeats = $record["Seats"];
                        $eventPlace = $record["Place"];
                        $eventPrice = $record["Price"];
                        $eventOrganizer = $record["Organizer"];
                        $eventUploaded = $record["Uploaded"];

                        $queryOrganizer = "SELECT * FROM Users WHERE UserID = '$eventOrganizer'";
                        $resultOrganizer = $connection->query($queryOrganizer);
                        if ($resultOrganizer->num_rows == 1) {
                            while ($record = $resultOrganizer->fetch_assoc()) {
                                $organizerName = $record["Name"];
                                $organizerSurname = $record["Surname"];
                                $organizerEmail = $record["Email"];
                            }
                        }

                        echo '<tbody>';
                        echo '<tr>';
                        echo "<td>$eventID</td>";
                        echo "<td>$eventImage</td>";
                        echo "<td>$eventTitle</td>";
                        echo "<td>$eventDescription</td>";
                        echo "<td>$eventStartDate</td>";
                        echo "<td>$eventEndDate</td>";
                        echo "<td>";
                        if ($eventSeats === 0) {
                            echo "<span class='badge bg-warning'>SOULD OUT</span>";
                        } else {
                            echo "$eventSeats";
                        }
                        echo "</td>";
                        echo "<td>$eventPlace</td>";
                        echo "<td>" . number_format($eventPrice, 2) . "</td>";
                        echo "<td>$organizerName $organizerSurname <br> $organizerEmail</td>";
                        echo "<td>$eventUploaded</td>";
                        echo '</tr>';
                        echo "</tbody>";
                    }
                } else {
                    echo '<tbody>';
                    echo '<tr>';
                    echo '<td colspan="11" class="text-center">No events found</td>';
                    echo '</tr>';
                    echo '</tbody>';
                }
                ?>
            </table>

        </div>

        <?php
    }
    ?>
</body>

</html>