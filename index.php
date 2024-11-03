<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventTeam - Homepage</title>

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
        } else if (isset($_GET["error"])) {
            echo "<div class='alert alert-danger mx-auto w-50 mb-5'>";
            echo $_GET["error"];
            echo "</div>";
        }
        ?>

        <h1><b>EvenTeam</b></h1>
        <p>Your trusted team for event research</p>

        <div id="carouselExampleIndicators" class="carousel slide mt-5" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active"
                    aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1"
                    aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2"
                    aria-label="Slide 3"></button>
            </div>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="./images/event-image-1.png" class="d-block w-100" alt="...">
                </div>
                <div class="carousel-item">
                    <img src="./images/event-image-2.jpg" class="d-block w-100" alt="...">
                </div>
                <div class="carousel-item">
                    <img src="./images/event-image-3.png" class="d-block w-100" alt="...">
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators"
                data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators"
                data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>

        <h2 style="margin-top: 5rem;" class="border p-3"><b>Explore some of events available</b></h2>

        <!-- Events -->
        <div class="container row text-center" id="container-cards-events">
            <?php
            $queryEvents = "SELECT * FROM Events WHERE StartDate >= CURRENT_DATE() AND EndDate >= CURRENT_DATE() LIMIT 3"; // Query to show at least 3 events that have the start date 'today'
            $resultQueryEvents = $connection->query($queryEvents);
            if ($resultQueryEvents->num_rows > 0) {
                while ($recordEvent = $resultQueryEvents->fetch_assoc()) {
                    $eventID = $recordEvent["EventID"];
                    $eventTitle = $recordEvent["Title"];
                    $eventDescription = $recordEvent["Description"];
                    $eventStartDate = $recordEvent["StartDate"];
                    $eventEndDate = $recordEvent["EndDate"];
                    $eventPlace = $recordEvent["Place"];
                    $eventPrice = $recordEvent["Price"];
                    $eventImage = $recordEvent["Image"];
                    $eventOrganizer = $recordEvent["Organizer"];
                    $eventSeats = $recordEvent["Seats"];

                    $queryOrganizer = "SELECT * FROM Users WHERE UserID = '$eventOrganizer' AND Role = 'Organizer'";
                    $resultOrganizer = $connection->query($queryOrganizer);
                    if ($resultOrganizer->num_rows == 1) {
                        while ($record = $resultOrganizer->fetch_assoc()) {
                            $organizerName = $record["Name"];
                            $organizerSurname = $record["Surname"];
                        }
                    }

                    // Section to show events
                    echo '<div class="col-12 col-md-4 d-flex justify-content-center">';
                    echo '<div class="card mt-5 shadow-sm" style="width: 80%; max-width: 20rem; border-radius: 10px;">';
                    showEvents($connection, $eventID, $eventTitle, $eventDescription, $eventStartDate, $eventEndDate, $eventPlace, $eventPrice, $eventImage, $eventOrganizer, $eventSeats);
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo "No events available.";
            }
            ?>
        </div>

        <h2 style="margin-top: 5rem;" class="border p-3"><b>Last events</b></h2>
        <!-- Events -->
        <div class="container row text-center" id="container-cards-events">
            <?php
            $queryEvents = "SELECT * FROM Events WHERE StartDate < CURRENT_DATE() AND EndDate < CURRENT_DATE() LIMIT 3"; // Query to show at least 3 events that don't have the start date 'today'
            $resultQueryEvents = $connection->query($queryEvents);
            if ($resultQueryEvents->num_rows > 0) {
                while ($recordEvent = $resultQueryEvents->fetch_assoc()) {
                    $eventID = $recordEvent["EventID"];
                    $eventTitle = $recordEvent["Title"];
                    $eventDescription = $recordEvent["Description"];
                    $eventStartDate = $recordEvent["StartDate"];
                    $eventEndDate = $recordEvent["EndDate"];
                    $eventPlace = $recordEvent["Place"];
                    $eventPrice = $recordEvent["Price"];
                    $eventImage = $recordEvent["Image"];
                    $eventOrganizer = $recordEvent["Organizer"];

                    $queryOrganizer = "SELECT * FROM Users WHERE UserID = '$eventOrganizer' AND Role = 'Organizer'";
                    $resultOrganizer = $connection->query($queryOrganizer);
                    if ($resultOrganizer->num_rows == 1) {
                        while ($record = $resultOrganizer->fetch_assoc()) {
                            $organizerName = $record["Name"];
                            $organizerSurname = $record["Surname"];
                        }
                    }

                    // Section to show events
                    echo '<div class="col-12 col-md-4 d-flex justify-content-center">';
                    echo '<div class="card mt-5 shadow-sm" style="width: 80%; max-width: 20rem; border-radius: 10px;">';
                    showLastEvents($connection, $eventID, $eventTitle, $eventDescription, $eventStartDate, $eventEndDate, $eventPlace, $eventPrice, $eventImage, $eventOrganizer);
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo "<p>No events available.</p>";
            }
            ?>
        </div>

        <?php
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

        function showLastEvents($connection, $eventID, $eventTitle, $eventDescription, $eventStartDate, $eventEndDate, $eventPlace, $eventPrice, $eventImage, $eventOrganizer)
        {
            $queryOrganizer = "SELECT * FROM Users WHERE UserID = '$eventOrganizer' AND Role = 'Organizer'";
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
            echo '</div>';
        }
        ?>


    </div>

</body>

</html>