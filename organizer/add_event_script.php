<?php

$connection = new mysqli("localhost", "root", "", "Event_Management");

if ($connection->connect_error) {
    http_response_code(502);
    echo "Bad Gateway: The service is currently unavailable.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    session_start();

    $default_images_folder_path = "../images/posts/"; // Root folder for event images

    $eventImageTempName = $_FILES["image"]["tmp_name"]; // Temp name image
    $eventImageName = $_FILES["image"]["name"]; // File name image

    $eventTitle = ucwords($connection->real_escape_string($_POST["title"]));
    $eventDescription = ucfirst($connection->real_escape_string($_POST["description"]));
    $eventStartDate = ucfirst($connection->real_escape_string($_POST["start_date"]));
    $eventEndDate = ucfirst($connection->real_escape_string($_POST["end_date"]));
    $eventSeats = $connection->real_escape_string($_POST["seats"]);
    $eventPlace = ucwords($connection->real_escape_string($_POST["place"]));
    $eventPrice = $connection->real_escape_string($_POST["price"]);

    // Retrive OrganizerID
    if (isset($_SESSION["user"])) {
        if ($_SESSION["user"]["role"] === "Organizer") {
            $organizerEmail = $_SESSION["user"]["email"];
            $queryOrganizer = "SELECT UserID FROM Users WHERE Email = '$organizerEmail' AND Role = 'Organizer'";
            $resultOrganizer = $connection->query($queryOrganizer);
            if ($resultOrganizer->num_rows == 1) {
                while ($record = $resultOrganizer->fetch_assoc()) {
                    $organizerID = $record["UserID"];
                }
            }
        }
    } else {
        $errorMessage = urlencode("Log in.");
        header("Location: index.php?error=$errorMessage");
        exit;
    }

    // Check if the folder not exist, create the direcotory
    if (!file_exists($default_images_folder_path)) {
        mkdir($default_images_folder_path, 0777, true);
    }

    $target_file_event_image = $default_images_folder_path . basename($eventImageName); // Complete file image path

    //Check if the file name already exist
    if (file_exists($target_file_event_image)) {
        $errorMessage = urlencode("Try to change the file name.");
        header("Location: add_event.php?error=$errorMessage");
        exit;
    }

    // Move the image file to the right folder
    if (move_uploaded_file($eventImageTempName, $target_file_event_image)) {
        // Insert the event
        $queryInsertEvent = $connection->prepare("INSERT INTO Events(Image, Title, Description, StartDate, EndDate, Seats, Place, Price, Organizer) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $queryInsertEvent->bind_param("sssssissi", $eventImageName, $eventTitle, $eventDescription, $eventStartDate, $eventEndDate, $eventSeats, $eventPlace, $eventPrice, $organizerID);

        if ($queryInsertEvent->execute()) {
            $successMassage = urlencode("Event correctly created.");
            header("Location: /event_booking_app/index.php?success=$successMassage");
            exit;
        } else {
            $errorMessage = urlencode("Something goes wrong. Try again.");
            header("Location: add_event.php?error=$errorMessage");
            exit;
        }
    } else {
        $errorMessage = urlencode("There was an error uploading your file.");
        header("Location: add_event.php?error=$errorMessage");
        exit;
    }


}
?>