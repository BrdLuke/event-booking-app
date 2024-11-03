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

        <h1 class="text-center"><b>Your Profile</b></h1>

        <div class="container">
            <?php
            if (isset($_SESSION["user"])) {
                $userEmail = $_SESSION["user"]["email"];
                if ($_SESSION["user"]["role"] === "Organizer") {
                    echo '<ul class="nav nav-tabs mt-5">';
                    echo '<li class="nav-item">';
                    echo '    <a class="nav-link active" aria-current="page" href="/event_booking_app/user_profile.php">Profile Infos</a>';
                    echo '</li>';
                    echo '<li class="nav-item">';
                    echo '    <a class="nav-link" href="/event_booking_app/user_subscribed_events.php">Subscribed Events</a>';
                    echo '</li>';
                    echo '<li class="nav-item">';
                    echo '    <a class="nav-link" href="/event_booking_app/organizer/uploaded_posts.php">Uploaded Posts</a>';
                    echo '</li>';
                    echo '</ul>';

                    // User Profile Information - Table
                    showUserInfos($connection, $userEmail);

                    echo "<a class='btn btn-warning btn-sm' href='/event_booking_app/user_profile_edit.php'>Edit Profile</a>";
                } else {
                    echo '<ul class="nav nav-tabs mt-5">';
                    echo '<li class="nav-item">';
                    echo '    <a class="nav-link active" aria-current="page" href="/event_booking_app/user_profile.php">Profile Infos</a>';
                    echo '</li>';
                    echo '<li class="nav-item">';
                    echo '    <a class="nav-link" href="/event_booking_app/user_subscribed_events.php">Subscribed Events</a>';
                    echo '</li>';
                    echo '</ul>';

                    // User Profile Information - Table
                    showUserInfos($connection, $userEmail);
                    
                    echo "<a class='btn btn-warning btn-sm' href='/event_booking_app/user_profile_edit.php'>Edit Profile</a>";
                }
            }


            function showUserInfos($connection, $userEmail) {
                // User Profile Information - Table
                $queryUserInfo = "SELECT * FROM Users WHERE Email = '$userEmail'";
                $resultUserInfo = $connection->query($queryUserInfo);
                if ($resultUserInfo->num_rows == 1) {
                    while ($record = $resultUserInfo->fetch_assoc()) {
                        $userName = $record["Name"];
                        $userSurname = $record["Surname"];
                        $userBirthDate = $record["BirthDate"];
                        $userRole = $record["Role"];
                        $userRegistrateDate = $record["RegisterDate"];
                        $userEditDate = $record["EditDate"];

                        // Display User Info in a Table
                        echo "<div class='mx-auto w-50 shadow mt-5'>";
                        echo "<h5 class='pt-3'><b>$userName's Info<b></h5><hr>";
                        echo "<table class='table table-borderless mt-4'>";
                        echo "<tbody>";
                        echo "<tr>";
                        echo "<td id='table-element-title'>Name</td>";
                        echo "<td>$userName</td>";
                        echo "</tr>";
                        echo "<tr>";
                        echo "<td  id='table-element-title'>Surname</td>";
                        echo "<td>$userSurname</td>";
                        echo "</tr>";
                        echo "<tr>";
                        echo "<td  id='table-element-title'>Email</td>";
                        echo "<td>$userEmail</td>";
                        echo "</tr>";
                        echo "<tr>";
                        echo "<td  id='table-element-title'>Password</td>";
                        echo "<td>*************</td>";
                        echo "</tr>";
                        echo "<tr>";
                        echo "<td id='table-element-title'>Birth Date</td>";
                        echo "<td>$userBirthDate</td>";
                        echo "</tr>";
                        echo "<tr>";
                        echo "<td id='table-element-title'>Role</td>";
                        echo "<td>$userRole</td>";
                        echo "</tr>";
                        echo "<tr>";
                        echo "<td id='table-element-title'>Registration Date</td>";
                        echo "<td>$userRegistrateDate</td>";
                        echo "</tr>";
                        echo "<td id='table-element-title'>Update Date</td>";
                        echo "<td>$userEditDate</td>";
                        echo "</tr>";
                        echo "</tbody>";
                        echo "</table>";
                        echo "</div>";
                    }
                }
            }
            ?>

        </div>
</body>

</html>