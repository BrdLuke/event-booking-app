<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventTeam - Add Event</title>

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

    ?>

    <div class="form-container container shadow-lg mx-auto w-50">

        <h1 class="text-center"><b>Add Event</b></h1>
        <p class="text-center">Complete all the fields to add your event</p>

        <form action="add_event_script.php" method="POST" enctype="multipart/form-data">
            <!-- Event Image -->
            <div class="mb-4">
                <label for="image" class="form-label">Event Image</label>
                <input type="file" class="form-control" id="image" name="image"
                    accept="image/png, image/jpeg, image/jpg" required>
                <small class="tooltip-text">Upload a high-quality image (JPG, PNG or JPEG)</small>
            </div>


            <!-- Event Title -->
            <div class="mb-4">
                <label for="title" class="form-label">Event Title</label>
                <input type="text" class="form-control" id="title" name="title" maxlength="100"
                    placeholder="Enter event title" required>
                <small class="tooltip-text">Make the title catchy and clear (max 100 characters)</small>
            </div>

            <!-- Event Description -->
            <div class="mb-4">
                <label for="description" class="form-label">Event Description</label>
                <textarea class="form-control" id="description" name="description" maxlength="120" rows="3"
                    placeholder="Enter event description" required></textarea>
                <small class="tooltip-text">Describe your event briefly (max 120 characters)</small>
            </div>

            <!-- Event Dates -->
            <div class="row">
                <div class="col-md-6 mb-4">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date"
                        min="<?php echo date('Y-m-d'); ?>" 
                        oninput="document.getElementById('end_date').setAttribute('value', this.value);
                                document.getElementById('end_date').setAttribute('min', this.value);" 
                        required>
                </div>

                <div class="col-md-6 mb-4">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" required>
                </div>
            </div>

            <!-- Event Seats -->
            <div class="mb-4">
                <label for="seats" class="form-label">Event Seats</label>
                <input type="number" class="form-control" id="seats" name="seats"
                    placeholder="Enter event seats" min="0" required>
                <small class="tooltip-text">Enter the maximus seats</small>
            </div>

            <!-- Event Place -->
            <div class="mb-4">
                <label for="place" class="form-label">Event Location</label>
                <input type="text" class="form-control" id="place" name="place" maxlength="255"
                    placeholder="Enter event location" required>
                <small class="tooltip-text">Specify the exact venue or address</small>
            </div>

            <!-- Event Price -->
            <div class="mb-4">
                <label for="price" class="form-label">Event Price (€)</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price"
                    placeholder="Enter event price" min="0" required>
                <small class="tooltip-text">Enter 0 if the event is free</small>
            </div>

            <!-- Submit Button -->
            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-form">Submit Event</button>
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