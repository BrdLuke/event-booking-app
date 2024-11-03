<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventTeam - Profile</title>

    <?php
    include_once("../bootstrap_resources.html");
    ?>

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

        <h1 class="text-center"><b>Your Uploaded Events</b></h1>

        <div class="container">
            <?php
            // Check for POST requests. Logic for the delete confermation event.
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $eventIDClicked = $_POST["eventID"];
                if (isset($_POST["edit"])) {
                    $_SESSION["event_clicked_id"] = $eventIDClicked;
                    header("Location: /event_booking_app/organizer/edit_uploaded_post.php?event=$eventIDClicked");
                    exit;
                } else if (isset($_POST["delete"])) {
                    // Query to retrive the file name image
                    $queryEventImage = "SELECT Image FROM Events WHERE EventID = '$eventIDClicked'";
                    $resultEventImage = $connection->query($queryEventImage);
                    if ($resultEventImage->num_rows == 1) {
                        $recordEvent = $resultEventImage->fetch_assoc();
                        $eventImageName = $recordEvent["Image"];
                    }

                    // Query to delete the event
                    $deleteQuery = "DELETE FROM Events WHERE EventID = '$eventIDClicked'";

                    if ($connection->query($deleteQuery)) {
                        // Check if the file image exists before trying to delete it
                        $imagePath = "../images/posts/$eventImageName";
                        if (file_exists($imagePath)) {
                            unlink($imagePath);
                        }
                        $successMassage = urlencode("Event [$eventIDClicked] deleted successfully.");
                        header("Location: /event_booking_app/organizer/uploaded_posts.php?success=$successMassage");
                        exit;
                    } else {
                        $errorMessage = urlencode("Something goes wrong. Try again.");
                        header("Location: /event_booking_app/organizer/uploaded_posts.php?error=$errorMessage");
                        exit;
                    }
                }
            }

            if (isset($_SESSION["user"])) {
                if ($_SESSION["user"]["role"] === "Organizer") {

                    $userEmail = $_SESSION["user"]["email"];

                    echo '<ul class="nav nav-tabs mt-5">';
                    echo '<li class="nav-item">';
                    echo '    <a class="nav-link" href="/event_booking_app/user_profile.php">Profile Infos</a>';
                    echo '</li>';
                    echo '<li class="nav-item">';
                    echo '    <a class="nav-link" href="/event_booking_app/user_subscribed_events.php">Subscribed Events</a>';
                    echo '</li>';
                    echo '<li class="nav-item">';
                    echo '    <a class="nav-link active" aria-current="page" href="/event_booking_app/organizer/uploaded_posts.php">Uploaded Posts</a>';
                    echo '</li>';
                    echo '</ul>';

                    // Organizer Uploaded Posts - Table
                    $queryOrganizerUploadedPosts = "SELECT Events.* FROM Users INNER JOIN Events ON Users.UserID = Events.Organizer WHERE Email = '$userEmail'";
                    $resultOrganizerUploadedPosts = $connection->query($queryOrganizerUploadedPosts);

                    if ($resultOrganizerUploadedPosts->num_rows > 0) {
                        echo '<table class="table table-striped table-hover mt-5 table-responsive">';
                        echo '<thead>';
                        echo '<tr>';
                        echo '<th scope="col">#</th>';
                        echo '<th scope="col">Image</th>';
                        echo '<th scope="col">Title</th>';
                        echo '<th scope="col">Start Date</th>';
                        echo '<th scope="col">End Date</th>';
                        echo '<th scope="col">Place</th>';
                        echo '<th scope="col">Price</th>';
                        echo '<th scope="col">Seats</th>';
                        echo '<th scope="col"></th>';
                        echo '</tr>';
                        echo '</thead>';
                        echo '<tbody>';

                        while ($record = $resultOrganizerUploadedPosts->fetch_assoc()) {
                            $eventID = $record["EventID"];
                            $eventImage = $record["Image"];
                            $eventTitle = $record["Title"];
                            $eventStartDate = $record["StartDate"];
                            $eventEndDate = $record["EndDate"];
                            $eventPlace = $record["Place"];
                            $eventPrice = $record["Price"];
                            $eventSeats = $record["Seats"];

                            echo '<tr>';
                            echo "<td>$eventID</td>";
                            echo "<td><img src='../images/posts/$eventImage' alt='Event Image' style='width: 10%;'/> <br>";
                            echo "$eventImage</td>";
                            echo "<td>$eventTitle</td>";
                            echo "<td>$eventStartDate</td>";
                            echo "<td>$eventEndDate</td>";
                            echo "<td>$eventPlace</td>";
                            echo "<td>" . number_format($eventPrice, 2) . "</td>";
                            echo "<td>$eventSeats</td>";
                            echo "<td class='ps-5 d-flex gap-1 align-items-center pb-5'>
                            <form method='POST'>
                            <input name='eventID' value='$eventID' type='hidden'></input>
                            <button name='edit' type='submit' class='btn btn-warning btn-sm'>Edit</button>
                                        <button type='button' class='btn btn-danger btn-sm' data-bs-toggle='modal' data-bs-target='#staticBackdrop$eventID'>Delete</button>
                                    </form>
                                </td>";
                            echo '</tr>';

                            ?>
                            <!-- Delete Confermation Modal -->
                            <div class="modal fade" id="staticBackdrop<?php echo $eventID; ?>" data-bs-backdrop="static"
                                data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="staticBackdropLabel">Delete Confermation (Event:
                                                <?php echo "$eventTitle"; ?>)
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Are you sure to delete this event?
                                        </div>
                                        <div class="modal-footer">
                                            <form method='POST'>
                                                <input name='eventID' value='<?php echo $eventID; ?>' type='hidden' />
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
                        echo "<p class='mt-5'>No events found.</p>";
                    }
                }
            }
            ?>
        </div>

</body>

</html>