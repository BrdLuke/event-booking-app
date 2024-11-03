<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventTeam - Edit Event</title>

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

    // Event information
    if (isset($_GET["event"]) && $_GET["event"] != NULL && intval($_GET["event"]) && $_SESSION["event_clicked_id"] === $_GET["event"]) {
        $getEventID = $_GET["event"];
    } else {
        $errorMessage = urlencode("The event is null. Try again.");
        header("Location: /event_booking_app/organizer/uploaded_posts.php?error=$errorMessage");
        exit;
    }

    // Check if the session 'user' is set, then retrive the user email in order to retrive the user ID
    if (isset($_SESSION["user"])) {
        $userEmail = $_SESSION["user"]["email"];
        $queryUserID = "SELECT UserID FROM Users WHERE Email = '$userEmail'";
        $resultQueryUserID = $connection->query($queryUserID);
        if ($resultQueryUserID->num_rows == 1) {
            $record = $resultQueryUserID->fetch_assoc();
            $userID = $record["UserID"];
        }
    }

    $queryEvent = $connection->prepare("SELECT * FROM Events WHERE EventID = ? AND Organizer = ?");
    $queryEvent->bind_param("is", $getEventID, $userID);
    $queryEvent->execute();
    $resultQueryEvent = $queryEvent->get_result();

    if ($resultQueryEvent->num_rows == 1) {
        $eventInfo = $resultQueryEvent->fetch_assoc();
    } else {
        $errorMessage = urlencode("Something goes wrong. Try again.");
        header("Location: /event_booking_app/organizer/uploaded_posts.php?error=$errorMessage");
        exit;
    }
    ?>

    <div class="form-container container shadow-lg mx-auto w-50">

        <h1 class="text-center"><b>Edit Event</b></h1>

        <form action="edit_uploaded_post_script.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="eventID" value="<?php echo $getEventID; ?>">
            <input type="hidden" name="eventImage" value="<?php echo $eventInfo["Image"]; ?>">
            <?php echo $eventInfo["Image"]; ?>
            <?php echo $getEventID; ?>
            <!-- Event Image -->
            <div class="mb-4">
                <label for="image" class="form-label">Event Image</label>
                <input type="file" class="form-control" id="image" name="image"
                    accept="image/png, image/jpeg, image/jpg">
                <small class="tooltip-text">Upload a high-quality image (JPG, PNG or JPEG)</small>
            </div>


            <!-- Event Title -->
            <div class="mb-4">
                <label for="title" class="form-label">Event Title</label>
                <input type="text" class="form-control" id="title" name="title" maxlength="100"
                    value="<?php echo $eventInfo["Title"]; ?>" required>
                <small class="tooltip-text">Make the title catchy and clear (max 100 characters)</small>
            </div>

            <!-- Event Description -->
            <div class="mb-4">
                <label for="description" class="form-label">Event Description</label>
                <textarea class="form-control" id="description" name="description" maxlength="120" rows="3"
                    required><?php echo $eventInfo["Description"]; ?></textarea>
                <small class="tooltip-text">Describe your event briefly (max 120 characters)</small>
            </div>

            <!-- Event Dates -->
            <div class="row">
                <div class="col-md-6 mb-4">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date"
                        value="<?php echo $eventInfo["StartDate"]; ?>"
                        min="<?php echo date('Y-m-d'); ?>" 
                        oninput="document.getElementById('end_date').setAttribute('min', this.value);
                                document.getElementById('end_date').setAttribute('value', this.value);" 
                        required>
                </div>

                <div class="col-md-6 mb-4">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" 
                    value="<?php echo $eventInfo["EndDate"]; ?>"
                    min="<?php echo $eventInfo["StartDate"]; ?>"
                    required>
                </div>
            </div>

            <!-- Event Place -->
            <div class="mb-4">
                <label for="place" class="form-label">Event Location</label>
                <input type="text" class="form-control" id="place" name="place" maxlength="255"
                    value="<?php echo $eventInfo["Place"]; ?>" required>
                <small class="tooltip-text">Specify the exact venue or address</small>
            </div>

            <!-- Event Price -->
            <div class="mb-4">
                <label for="price" class="form-label">Event Price (€)</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price"
                    value="<?php echo $eventInfo["Price"]; ?>" min="0" required>
                <small class="tooltip-text">Enter 0 if the event is free</small>
            </div>

            <!-- Submit Button -->
            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-form">Edit Event</button>
            </div>
        </form>

        <?php
        if (isset($_GET["success"])) {
            echo "<div class='alert alert-success mx-auto w-50 mt-5'>";
            echo $_GET["success"];
            echo "</div>";
        } else if (isset($_GET["error"])) {
            echo "<div class='alert alert-danger mx-auto w-50 mt-5'>";
            echo $_GET["error"];
            echo "</div>";
        }
        ?>
    </div>
</body>

</html>