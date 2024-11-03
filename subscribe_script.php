<?php
$connection = new mysqli();
try {
    $connection->connect("localhost", "root", "", "Event_Management");
} catch (Exception $e) {
    echo "<div class='alert alert-danger text-center'>Errore di connessione al database</div>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $eventIDForm = $_POST["eventID"];
    $userIDForm = $_POST["userID"];

    // Check if the user is already subscribed to the event
    $queryCheckSubscription = "SELECT * FROM Subscribers WHERE Participant = '$userIDForm' AND Event = '$eventIDForm'";
    $resultCheckSubscription = $connection->query($queryCheckSubscription);

    if ($resultCheckSubscription->num_rows > 0) {
        $errorMessage = urlencode("You have already subscribed for this event.");
        header("Location: index.php?error=$errorMessage");
        exit;
    } else {
        // Check the number of available seats first
        $queryEventSeats = "SELECT Seats FROM Events WHERE EventID = '$eventIDForm'";
        $resultEventSeats = $connection->query($queryEventSeats);
        if ($resultEventSeats->num_rows == 1) {
            $record = $resultEventSeats->fetch_assoc();
            $eventSeats = $record["Seats"];

            if ($eventSeats > 0) {
                //Check if the user is already subscribed to the event
                $queryCheckSubscription = "SELECT * FROM Subscribers WHERE Participant = '$userIDForm' AND Event = '$eventIDForm'";
                $resultCheckSubscription = $connection->query($queryCheckSubscription);

                if ($resultCheckSubscription->num_rows > 0) {
                    $errorMessage = urlencode("You have already subscribed for this event: '$eventTitle'");
                    header("Location: index.php?error=$errorMessage");
                    exit;
                } else {
                    // Decrement the seat count if available
                    $queryUpdateSeats = "UPDATE Events SET Seats = Seats - 1 WHERE EventID = '$eventIDForm'";
                    $resultUpdateSeats = $connection->query($queryUpdateSeats);

                    if ($resultUpdateSeats === TRUE) {
                        // Insert the user subscription
                        $queryInsertSubscription = "INSERT INTO Subscribers(Participant, Event) VALUES ('$userIDForm', '$eventIDForm')";
                        $resultInsertSubscription = $connection->query($queryInsertSubscription);

                        if ($resultInsertSubscription === TRUE) {
                            $successMessage = urlencode("You have successfully registered for the event.");
                            header("Location: index.php?success=$successMessage");
                            exit;
                        } else {
                            $errorMessage = urlencode("There was an error during registration. Please try again.");
                            header("Location: subscribe.php?error=$errorMessage");
                            exit;
                        }
                    } else {
                        $errorMessage = urlencode("Unable to update seats. Please try again.");
                        header("Location: index.php?error=$errorMessage");
                        exit;
                    }
                }
            } else {
                $errorMessage = urlencode("No seats available for this event.");
                header("Location: index.php?error=$errorMessage");
                exit;
            }
        }
    }
}

?>