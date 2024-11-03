<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventTeam - Profile</title>

    <?php
    include_once("bootstrap_resources.html");
    ?>

    <link rel="stylesheet" href="/event_booking_app/css/styles.css">
</head>

<body>
    <?php
    include_once("navbar.php");
    $connection = new mysqli();

    try {
        $connection->connect("localhost", "root", "", "Event_Management");
    } catch (Exception $e) {
        echo "Error";
    }
    ?>

    <div class="container text-center mt-5">

        <?php
        if (isset($_GET["success"])) {
            echo "<div class='alert alert-success mx-auto w-50 mb-5'>";
            echo $_GET["success"];
            echo "</div>";
        }
        ?>

        <h1 class="text-center"><b>Your Subscribed Events</b></h1>

        <div class="container">
            <?php

            // Check for POST requests. Logic for the unsubscribe confermation.
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $eventIDSubscribe = $_POST["eventID"];
                $userIDSubscribe = $_POST["userID"];

                if (isset($_POST["delete"])) {
                    // Update seats event number
                    $queryUpdateSeats = "UPDATE Events SET Seats = Seats + 1 WHERE EventID = '$eventIDSubscribe'";
                    $resultUpdateSeats = $connection->query($queryUpdateSeats);

                    if ($resultUpdateSeats === TRUE) {
                        // Check if there are seats available
                        $queryEventSeats = "SELECT Seats FROM Events WHERE EventID = '$eventIDSubscribe'";
                        $resultEventSeats = $connection->query($queryEventSeats);
                        if ($resultEventSeats->num_rows == 1) {
                            $record = $resultEventSeats->fetch_assoc();
                            $eventSeats = $record["Seats"];
                        }

                        // Unsubscribe user to the event
                        $deleteQuery = "DELETE FROM Subscribers WHERE Event = '$eventIDSubscribe' AND Participant = '$userIDSubscribe'";
                        
                        if ($connection->query($deleteQuery)) {
                            $successMassage = urlencode("You unsubscribed for the event [$eventIDSubscribe] successfully.");
                            header("Location: /event_booking_app/user_subscribed_events.php?success=$successMassage");
                            exit;
                        } else {
                            $errorMessage = urlencode("Something goes wrong. Try again.");
                            header("Location: /event_booking_app/user_subscribed_events.php?error=$errorMessage");
                            exit;
                        }
                    }
                }
            }

            // Show subscribed events
            if (isset($_SESSION["user"])) {

                if ($_SESSION["user"]["role"] === "Organizer") {
                    echo '<ul class="nav nav-tabs mt-5">';
                    echo '<li class="nav-item">';
                    echo '    <a class="nav-link" aria-current="page" href="/event_booking_app/user_profile.php">Profile Infos</a>';
                    echo '</li>';
                    echo '<li class="nav-item">';
                    echo '    <a class="nav-link active" href="/event_booking_app/user_subscribed_events.php">Subscribed Events</a>';
                    echo '</li>';
                    echo '<li class="nav-item">';
                    echo '    <a class="nav-link" href="/event_booking_app/organizer/uploaded_posts.php">Uploaded Posts</a>';
                    echo '</li>';
                    echo '</ul>';
                } else {
                    echo '<ul class="nav nav-tabs mt-5">';
                    echo '<li class="nav-item">';
                    echo '    <a class="nav-link" aria-current="page" href="/event_booking_app/user_profile.php">Profile Infos</a>';
                    echo '</li>';
                    echo '<li class="nav-item">';
                    echo '    <a class="nav-link active" href="/event_booking_app/user_subscribed_events.php">Subscribed Events</a>';
                    echo '</li>';
                    echo '</ul>';
                }

                echo '<table class="table table-striped table-hover mt-5 table-responsive">';
                echo '<thead>';
                echo '<tr>';
                echo '<th scope="col">Title</th>';
                echo '<th scope="col">Start Date</th>';
                echo '<th scope="col">End Date</th>';
                echo '<th scope="col">Place</th>';
                echo '<th scope="col">Price</th>';
                echo '<th scope="col">Subscribe Date</th>';
                echo '<th scope="col"></th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';

                $userEmail = $_SESSION["user"]["email"];
                $querySubscribedEvents = "SELECT Subscribers.Date AS 'SubscribersDate', Events.Title AS 'EventTitle', Events.StartDate AS 'EventStartDate', Events.EndDate AS 'EventEndDate', Events.Place AS 'EventPlace', Events.Price AS 'EventPrice', Events.EventID AS 'EventID', Users.UserID AS 'UserID' FROM Users, Subscribers, Events WHERE Users.UserID = Subscribers.Participant AND Events.EventID = Subscribers.Event AND Users.Email = '$userEmail'";
                $resultSubscribedEvents = $connection->query($querySubscribedEvents);
                while ($record = $resultSubscribedEvents->fetch_assoc()) {
                    $eventID = $record["EventID"];
                    $eventTitle = $record["EventTitle"];
                    $eventStartDate = $record["EventStartDate"];
                    $eventEndDate = $record["EventEndDate"];
                    $eventPlace = $record["EventPlace"];
                    $eventPrice = $record["EventPrice"];
                    $subscribedDate = $record["SubscribersDate"];
                    $userID = $record["UserID"];

                    echo '<tr>';
                    echo "<td>$eventTitle</td>";
                    echo "<td>$eventStartDate</td>";
                    echo "<td>$eventEndDate</td>";
                    echo "<td>$eventPlace</td>";
                    echo "<td>" . number_format($eventPrice, 2) . "</td>";
                    echo "<td>$subscribedDate</td>";
                    echo "<td class='ps-5 d-flex gap-1'>
                            <form method='POST'>
                                <button type='button' class='btn btn-danger btn-sm' data-bs-toggle='modal' data-bs-target='#staticBackdrop$eventID'>Unsubscribe</button>
                            </form>
                        </td>";
                    echo '</tr>';

                    ?>

                    <!-- Unsubscribe Confermation Modal -->
                    <div class="modal fade" id="staticBackdrop<?php echo $eventID; ?>" data-bs-backdrop="static"
                        data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="staticBackdropLabel">Unsubscribe Confermation (Event:
                                        <?php echo "$eventTitle"; ?>)
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Are you sure to unsubscribe for this event?
                                </div>
                                <div class="modal-footer">
                                    <form method='POST'>
                                        <input name='eventID' value='<?php echo $eventID; ?>' type='hidden' />
                                        <input name='userID' value='<?php echo $userID; ?>' type='hidden' />
                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">No</button>
                                        <button type='submit' name='delete' class="btn btn-success">Yes</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                echo '</tbody>';
                echo '</table>';

            } else {
                echo "<p>No events found.</p>";
            }
            ?>
        </div>
</body>

</html>