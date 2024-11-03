<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventTeam - Admin Users Management</title>

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
                    <li class="breadcrumb-item active" aria-current="page">Users Management</li>
                </ol>
            </nav>

            <h1 class="text-center"><b>Users Management</b></h1>

            <table class="table table-bordered text-center table-hover mt-5 table-responsive">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Surname</th>
                        <th scope="col">Email</th>
                        <th scope="col">BirthDate</th>
                        <th scope="col">Role</th>
                        <th scope="col">RegisterDate</th>
                        <th scope="col">EditDate</th>
                    </tr>
                </thead>

                <?php
                $queryUsers = "SELECT * FROM Users";
                $resultQueryUsers = $connection->query($queryUsers);

                if ($resultQueryUsers->num_rows > 0) {
                    while ($record = $resultQueryUsers->fetch_assoc()) {
                        $userID = $record["UserID"];
                        $userName = $record["Name"];
                        $userSurname = $record["Surname"];
                        $userEmail = $record["Email"];
                        $userBirthDate = $record["BirthDate"];
                        $userRole = $record["Role"];
                        $userRegisterDate = $record["RegisterDate"];
                        $userEditDate = $record["EditDate"];

                        echo "<tbody>";
                        echo "<td>$userID</td>";
                        echo "<td>$userName</td>";
                        echo "<td>$userSurname</td>";
                        echo "<td>$userEmail</td>";
                        echo "<td>$userBirthDate</td>";
                        echo "<td>$userRole</td>";
                        echo "<td>$userRegisterDate</td>";
                        echo "<td>$userEditDate</td>";
                        echo "</tbody>";
                    }
                } else {
                    echo '<tbody>';
                    echo '<tr>';
                    echo '<td colspan="9" class="text-center">No users found</td>';
                    echo '</tr>';
                    echo '</tbody>';
                }
                ?>
            </table>

        </div>

        <?php
    }
    ?>
</body>

</html>