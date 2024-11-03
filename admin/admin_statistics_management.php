<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventTeam - Admin Users Management</title>

    <?php include_once("../bootstrap_resources.html"); ?>

    <link rel="stylesheet" href="/event_booking_app/css/styles.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
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

    if (!isset($_SESSION["user"])) {
        $errorMessage = urlencode("Register now. If you have an account, log in.");
        header("Location: /event_booking_app/index.php?error=$errorMessage");
        exit;
    }

    // Check if the user is an 'admin'
    $userEmail = $_SESSION["user"]["email"];

    $queryUserRole = "SELECT Role FROM Users INNER JOIN Roles ON Users.Role = Roles.RoleName WHERE Email = '$userEmail'";
    $resultUserRole = $connection->query($queryUserRole);

    if ($resultUserRole->num_rows == 1) {
        $record = $resultUserRole->fetch_assoc();
        $userRole = $record["Role"];
    }

    if ($userRole !== "Admin") {
        $errorMessage = urlencode("You are not available to use this service.");
        header("Location: /event_booking_app/index.php?error=$errorMessage");
        exit;
    } else {

        ?>

        <div class="mt-5 container">

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/event_booking_app/admin/index.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Statistics</li>
                </ol>
            </nav>

            <h1 class="text-center"><b>Statistics Management</b></h1>

            <!-- Users Statistics -->
            <!-- Users Statistics -->
            <!-- Users Statistics -->
            <h3 class="mt-5 text-center"><b>User Statistics</b></h3>
            <!-- Users Count -->
            <?php
            $queryCountUsers = "SELECT COUNT(*) AS NumberUsers FROM Users";
            $resultCountUsers = $connection->query($queryCountUsers);
            $record = $resultCountUsers->fetch_assoc();
            $countNumberUsers = $record["NumberUsers"];
            ?>

            <!-- Users Count for each roles -->
            <?php
            $queryCountAdmin = "SELECT COUNT(*) AS NumberAdmin FROM Users WHERE Role = 'Admin'";
            $resultCountAdmin = $connection->query($queryCountAdmin);
            $recordAdmin = $resultCountAdmin->fetch_assoc();
            $countNumberAdmin = $recordAdmin["NumberAdmin"];

            $queryCountOrganizer = "SELECT COUNT(*) AS NumberOrganizer FROM Users WHERE Role = 'Organizer'";
            $resultCountOrganizer = $connection->query($queryCountOrganizer);
            $recordOrganizer = $resultCountOrganizer->fetch_assoc();
            $countNumberOrganizer = $recordOrganizer["NumberOrganizer"];

            $queryCountUser = "SELECT COUNT(*) AS NumberUser FROM Users WHERE Role = 'User'";
            $resultCountUser = $connection->query($queryCountUser);
            $recordUser = $resultCountUser->fetch_assoc();
            $countNumberUser = $recordUser["NumberUser"];
            ?>

            <canvas id="numberUsers" class="mx-auto" style="max-width: 400px;"></canvas>

            <hr>

            <!-- Events Statistics -->
            <!-- Events Statistics -->
            <!-- Events Statistics -->
            <h3 class="mt-5 text-center"><b>Event Statistics</b></h3>
            <!-- Events Count -->
            <?php
            $queryCountEvents = "SELECT COUNT(*) AS NumberEvents FROM Events";
            $resultCountEvents = $connection->query($queryCountEvents);
            $recordEvent = $resultCountEvents->fetch_assoc();
            $countNumberEvents = $recordEvent["NumberEvents"];
            echo "<p class='text-center'>Total number of events created: <b> $countNumberEvents </b></p>";
            ?>

            <!-- Event Subscribers -->
            <?php
            $eventTitleArray = array();
            $eventSubscribersArray = array();

            $queryAmountEvents = "SELECT Title, COUNT(Subscribers.Event) AS SubscriberCounts FROM Events INNER JOIN Subscribers ON Events.EventID = Subscribers.Event GROUP BY Events.EventID";
            $resultAmountEvents = $connection->query($queryAmountEvents);
            if ($resultAmountEvents->num_rows > 0) {
                while ($recordEvent = $resultAmountEvents->fetch_assoc()) {
                    $eventTitle = $recordEvent["Title"];
                    $eventSubscribers = $recordEvent["SubscriberCounts"];

                    array_push($eventTitleArray, $eventTitle);
                    array_push($eventSubscribersArray, $eventSubscribers);
                }
            }
            ?>

            <!-- Events Total Amount -->
            <?php
            $view = "CREATE OR REPLACE VIEW SubscriberCounts AS SELECT Event AS EventID, COUNT(SubscriberID) AS SubscriberCount FROM Subscribers GROUP BY Event";
            $connection->query($view);
            $queryTotalAmountEvents = "SELECT SUM(Events.Price * SubscriberCounts.SubscriberCount) AS TotalAmount FROM Events INNER JOIN SubscriberCounts ON Events.EventID = SubscriberCounts.EventID";
            $resultTotalAmountEvents = $connection->query($queryTotalAmountEvents);
            $recordTotalAmountEvent = $resultTotalAmountEvents->fetch_assoc();
            $totalAmountEvents = $recordTotalAmountEvent["TotalAmount"];

            echo "<p class='text-center'>Total amount: <b> € $totalAmountEvents </b>";
            ?>
            <canvas id="totalAmountEvents" class="mx-auto" style="max-width: 400px;"></canvas>

            <hr>
        </div>

        <?php
    }
    ?>

    <script>
        // Users Statistics
        // Users Statistics
        // Users Statistics
        const xValuesUsers = ["Admin", "Organizers", "Users"];
        const yValuesUsers = [<?php echo $countNumberAdmin; ?>, <?php echo $countNumberOrganizer; ?>, <?php echo $countNumberUser; ?>];
        const barColorsUsers = [
            "#b91d47",
            "#00aba9",
            "#2b5797"
        ];

        new Chart("numberUsers", {
            type: "pie",
            data: {
                labels: xValuesUsers,
                datasets: [{
                    backgroundColor: barColorsUsers,
                    data: yValuesUsers
                }]
            },
            options: {
                title: {
                    display: true,
                    text: "<?php echo "Total number of registered users: $countNumberUsers"; ?>"
                }
            }
        });


        // Events Statistics
        // Events Statistics
        // Events Statistics
        const xValuesEvents = <?php echo json_encode($eventTitleArray); ?>;
        const yValuesEvents = <?php echo json_encode($eventSubscribersArray); ?>;
        const barColorsEvents = ["red", "green", "blue", "orange", "brown"];

        new Chart("totalAmountEvents", {
            type: "bar",
            data: {
                labels: xValuesEvents,
                datasets: [{
                    backgroundColor: barColorsEvents,
                    data: yValuesEvents
                }]
            },
            options: {
                legend: { display: false },
                title: {
                    display: true,
                    text: "Total Subscribers for Each Event"
                }
            }
        }); 
    </script>
</body>

</html>