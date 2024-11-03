<?php
$connection = new mysqli();

try {
    $connection->connect("localhost", "root", "", "Event_Management");
} catch (Exception $e) {
    echo "Error";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $default_images_folder_path = "../images/posts/"; // Root folder for event images

    $eventImage = $_POST["eventImage"]; // Origin file name image

    $eventImageEdit = $_FILES["image"]["name"]; // New file name image
    $eventImageTempNameEdit = $_FILES["image"]["tmp_name"]; // New file temp image
    $eventImageTempName = $_FILES["image"]["tmp_name"]; // Temp name image

    $eventID = $_POST["eventID"];
    $eventTitle = ucwords($connection->real_escape_string($_POST["title"]));
    $eventDescription = ucfirst($connection->real_escape_string($_POST["description"]));
    $eventStartDate = ucfirst($connection->real_escape_string($_POST["start_date"]));
    $eventEndDate = ucfirst($connection->real_escape_string($_POST["end_date"]));
    $eventPlace = ucwords($connection->real_escape_string($_POST["place"]));
    $eventPrice = $connection->real_escape_string($_POST["price"]);

    if (is_uploaded_file($eventImageTempNameEdit)) {
        unlink(dirname(__FILE__) . $default_images_folder_path . $eventImage); // Delete past image file
        $eventImage = $eventImageEdit; // New image name

        // Check if the folder doesn't exist, create it
        if (!file_exists($default_images_folder_path)) {
            mkdir($default_images_folder_path, 0777, true);
        }

        $target_file_event_image = $default_images_folder_path . basename($eventImageEdit); // Complete file image path

        // Check if the file name already exists
        if (file_exists($target_file_event_image)) {
            $errorMessage = urlencode("Try to change the file name.");
            header("Location: edit_uploaded_post.php?event=$eventID&error=$errorMessage");
            exit;
        }

        // Move the image file to the right folder
        if (!move_uploaded_file($eventImageTempNameEdit, $target_file_event_image)) {
            $errorMessage = urlencode("There was an error uploading your file.");
            header("Location: edit_uploaded_post.php?event=$eventID&error=$errorMessage");
            exit;
        }
    } else {
        $eventImage = $_POST["eventImage"]; // Origin file name image
    }


    // Insert the event
    $queryEditEvent = $connection->prepare("UPDATE Events SET Image = ?, Title = ?, Description = ?, StartDate = ?, EndDate = ?, Place = ?, Price = ? WHERE EventID = ?");
    $queryEditEvent->bind_param("ssssssdi", $eventImage, $eventTitle, $eventDescription, $eventStartDate, $eventEndDate, $eventPlace, $eventPrice, $eventID);
    if ($queryEditEvent->execute()) {
        $successMassage = urlencode("Event edited successfully.");
        header("Location: /event_booking_app/organizer/uploaded_posts.php?success=$successMassage");
        exit;
    } else {
        $errorMessage = urlencode("Something goes wrong. Try again.");
        header("Location: /event_booking_app/organizer/uploaded_posts.php?error=$errorMessage");
        exit;
    }
}
?>