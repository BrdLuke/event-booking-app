<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventTeam - Explore</title>

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

        <!-- Events -->
        <div class="container row text-center" id="container-cards-events">
            <h1><b>Explore all the events around the world</b></h1>
            <?php
            if (isset($_GET["q"])) {
                $searchText = $_GET["q"];
                $searchQuery = $connection->prepare("SELECT * FROM Events WHERE Title LIKE CONCAT('%', ?, '%') OR Description LIKE CONCAT('%', ?, '%') OR StartDate LIKE CONCAT('%', ?, '%') OR EndDate LIKE CONCAT('%', ?, '%') OR Place LIKE CONCAT('%', ?, '%') OR Price LIKE CONCAT('%', ?, '%') OR Organizer LIKE CONCAT('%', ?, '%')");
                $searchQuery->bind_param("sssssss", $searchText, $searchText, $searchText, $searchText, $searchText, $searchText, $searchText);
                $searchQuery->execute();

                if ($resultSearchQuery = $searchQuery->get_result()) {
                    resultsQuery($connection, $resultSearchQuery);
                } else {
                    echo "<p>The search didn't return any events</p>";
                }
            } else {
                ?>

                <!-- Events -->
                <div class="container row text-center" id="container-cards-events">
                    <?php
                    $queryEvents = "SELECT * FROM Events WHERE StartDate >= CURRENT_DATE() AND EndDate >= CURRENT_DATE()"; // Query to show the events that have the start date 'today'
                    if ($resultQueryEvents = $connection->query($queryEvents)) {
                        resultsQuery($connection, $resultQueryEvents);
                    } else {
                        echo "<p>No events found.</p>";
                    }
                    ?>
                </div>

                <?php
            }

            function resultsQuery($connection, $result)
            {
                if ($result->num_rows > 0) {
                    while ($record = $result->fetch_assoc()) {
                        $eventID = $record["EventID"];
                        $eventTitle = $record["Title"];
                        $eventDescription = $record["Description"];
                        $eventStartDate = $record["StartDate"];
                        $eventEndDate = $record["EndDate"];
                        $eventPlace = $record["Place"];
                        $eventPrice = $record["Price"];
                        $eventImage = $record["Image"];
                        $eventOrganizer = $record["Organizer"];
                        $eventSeats = $record["Seats"];

                        echo '<div class="col-12 col-md-4 d-flex justify-content-center">';
                        echo '<div class="card mt-5 mb-5 shadow-sm" style="width: 80%; max-width: 20rem; border-radius: 10px;">';
                        showEvents($connection, $eventID, $eventTitle, $eventDescription, $eventStartDate, $eventEndDate, $eventPlace, $eventPrice, $eventImage, $eventOrganizer, $eventSeats);
                        echo '</div>';
                        echo '</div>';
                    }
                }
            }
            function showEvents($connection, $eventID, $eventTitle, $eventDescription, $eventStartDate, $eventEndDate, $eventPlace, $eventPrice, $eventImage, $eventOrganizer, $eventSeats)
            {
                $queryOrganizer = "SELECT * FROM Users WHERE UserID = '$eventOrganizer' AND (Role = 'Organizer' OR Role = 'Admin')";
                $resultOrganizer = $connection->query($queryOrganizer);
                if ($resultOrganizer->num_rows == 1) {
                    while ($record = $resultOrganizer->fetch_assoc()) {
                        $organizerName = $record["Name"];
                        $organizerSurname = $record["Surname"];
                    }
                }

                // If there is an event image, show it. Else put a default image
                if ($eventImage) {
                    echo '<img src="./images/posts/' . $eventImage . '" class="card-img-top" alt="Event Image" style="border-top-left-radius: 10px; border-top-right-radius: 10px;">';
                } else {
                    echo '<img src="/event_booking_app/images/no-image.png" class="card-img-top" alt="Event Image" style="border-top-left-radius: 10px; border-top-right-radius: 10px;">';
                }

                echo '<div class="card-body text-center">';

                // Event Title
                echo '<h5 class="card-title"><b>' . $eventTitle . '</b></h5>';

                // Event Seats
                if ($eventSeats == 0) {
                    echo "<p><span class='badge bg-warning'>SOULD OUT</span>";
                } else {
                    echo "<p class='border'><b>Total seats:</b> $eventSeats</p>";
                }

                // Event Date
                echo '<p class="text-muted mb-1" style="font-size: 0.9rem;">';
                echo '<i class="bi bi-calendar"></i> ' . date('F d, Y', strtotime($eventStartDate)) . ' - ' . date('F d, Y', strtotime($eventEndDate));
                echo '</p>';

                // Event Place
                echo '<h6 class="card-subtitle mb-2 text-muted">' . $eventPlace . '</h6>';

                // Event Description
                echo '<p class="card-text" style="font-size: 0.95rem;">' . $eventDescription . '</p>';

                // Event Price
                if ($eventPrice == 0) {
                    echo '<p><b>Entrance price:</b> <span class="badge bg-info">FREE</span></p>';
                } else {
                    echo '<p><b>Entrance price:</b> € ' . number_format($eventPrice, 2) . '</p>';
                }

                // Event Organizer
                echo '<hr>';
                echo '<p style="font-size: 0.9rem;"><b>Organizer</b>: ' . $organizerName . ' ' . $organizerSurname . '</p>';
                echo '<hr>';

                // Subscribe button
                if ($eventStartDate >= date("Y-m-d") && $eventEndDate >= date("Y-m-d")) {
                    echo '<form method="POST" action="subscribe.php">';
                    echo '<input type="hidden" name="eventID" value=" ' . $eventID . '" />';
                    echo '<button class="btn btn-primary" type="submit" style="background-color: #007bff; border-radius: 50px; padding: 10px 20px;">Subscribe</a>';
                    echo '</form>';
                    echo '</div>';
                }
            }
            ?>
        </div>
</body>

</html>