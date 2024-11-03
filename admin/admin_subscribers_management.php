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
                    <li class="breadcrumb-item active" aria-current="page">Subscribers Management</li>
                </ol>
            </nav>

            <h1 class="text-center"><b>Subscribers Management</b></h1>

            <table class="table table-bordered text-center table-hover mt-5 table-responsive">

                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Event</th>
                        <th scope="col">Participant</th>
                        <th scope="col">Date</th>
                    </tr>
                </thead>

                <?php
                $queryUsers = "SELECT * FROM Subscribers";
                $resultQuerySubscribers = $connection->query($queryUsers);

                if ($resultQuerySubscribers->num_rows > 0) {
                    while ($record = $resultQuerySubscribers->fetch_assoc()) {
                        $subscribersID = $record["SubscriberID"];
                        $subscribersEvent = $record["Event"];
                        $subscribersParticipant = $record["Participant"];
                        $subscribersDate = $record["Date"];

                        // Event's Title
                        $queryEvent = "SELECT Title FROM Events WHERE EventID = '$subscribersEvent'";
                        $resultEvent = $connection->query($queryEvent);
                        $eventTitle = $resultEvent->fetch_assoc()["Title"];

                        // User's name, surname and email
                        $queryUser = "SELECT Name, Surname, Email FROM Users WHERE UserID = '$subscribersParticipant'";
                        $resultUser = $connection->query($queryUser);
                        $userRecord = $resultUser->fetch_assoc();
                        $participantName = $userRecord["Name"];
                        $participantSurname = $userRecord["Surname"];
                        $participantEmail = $userRecord["Email"];

                        echo '<tbody>';
                        echo '<tr>';
                        echo "<td>$subscribersID</td>";
                        echo "<td>$eventTitle</td>";
                        echo "<td>$participantName $participantSurname - [ $participantEmail ]</td>";
                        echo "<td>$subscribersDate</td>";
                        echo '</tr>';
                        echo '</tbody>';
                    }
                } else {
                    echo '<tbody>';
                    echo '<tr>';
                    echo '<td colspan="4" class="text-center">No subscribers found</td>';
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