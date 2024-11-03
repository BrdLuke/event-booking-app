<?php
ob_start();  // Start buffer output
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventTeam - Subscription</title>

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
        echo "<div class='alert alert-danger text-center'>Errore di connessione al database</div>";
    }
    ?>

    <div class="container text-center mt-5">

        <?php
        if (isset($_GET["error"])) {
            echo "<div class='alert alert-danger mx-auto w-50 mb-5'>";
            echo $_GET["error"];
            echo "</div>";
        }
        ?>

        <h1><b>Subscription</b></h1>
        <p>Complete the subscription for this event</p>

        <div class="card shadow-lg p-5 mx-auto mt-5 mb-5" style="max-width: 50rem;">
            <?php
            if (isset($_SESSION["user"])) {
                $userName = $_SESSION["user"]["name"];
                $userEmail = $_SESSION["user"]["email"];

                $queryUser = "SELECT UserID FROM Users WHERE Email = '$userEmail'";
                $resultUser = $connection->query($queryUser);
                if ($resultUser->num_rows == 1) {
                    $recordUser = $resultUser->fetch_assoc();
                    $userID = $recordUser["UserID"];
                }
            }

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $eventID = $connection->real_escape_string($_POST["eventID"]);

                $queryEvent = "SELECT * FROM Events WHERE EventID = '$eventID'";
                $resultEvent = $connection->query($queryEvent);

                if ($resultEvent->num_rows == 1) {
                    while ($recordEvent = $resultEvent->fetch_assoc()) {
                        $eventTitle = $recordEvent["Title"];
                        $eventDescription = $recordEvent["Description"];
                        $eventStartDate = $recordEvent["StartDate"];
                        $eventEndDate = $recordEvent["EndDate"];
                        $eventPlace = $recordEvent["Place"];
                        $eventPrice = $recordEvent["Price"];
                        $eventImage = $recordEvent["Image"];
                        $eventOrganizer = $recordEvent["Organizer"];
                        $eventSeats = $recordEvent["Seats"];

                        echo "<h4 class='card-title'><b>$eventTitle</b></h4>";
                        if ($eventSeats == 0) {
                            echo "<p><span class='badge bg-warning'>SOULD OUT</span>";
                        } else {
                            echo "<p class='border'><b>Total seats:</b> $eventSeats</p>";
                        }
                        echo "<p>$eventDescription</p>";
                        echo "<p><b>Date: </b>" . date('F d, Y', strtotime($eventStartDate)) . " - " . date('F d, Y', strtotime($eventEndDate)) . "</p>";
                        echo "<p><b>Location:</b> $eventPlace</p>";
                        echo "<p><b>Price:</b> € $eventPrice</p>";
                    }
                }
            } else {
                $errorMessage = urlencode("Something goes wrong.");
                header("Location: index.php?error=$errorMessage");
                exit;
            }
            ?>
        </div>

        <!-- Form -->
        <div class="container mt-4">
            <form method="POST" action="subscribe_script.php" class="row g-3 justify-content-center">
                <input type="hidden" name="eventID" value="<?php echo $eventID; ?>">
                <input type="hidden" name="userID" value="<?php echo $userID; ?>">

                <div class="col-md-6">
                    <label for="name" class="form-label">Your Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo $userName; ?>"
                        required readonly>
                </div>

                <div class="col-md-6">
                    <label for="email" class="form-label">Your Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $userEmail; ?>"
                        required readonly>
                </div>

                <div class="col-12">
                    <button type="submit" name="submit" class="btn btn-primary w-50">Subscribe</button>
                </div>
            </form>
        </div>
    </div>

</body>

</html>

<?php
ob_end_flush();  // Free buffer output
?>