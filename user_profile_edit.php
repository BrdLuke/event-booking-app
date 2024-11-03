<?php
ob_start();  // Start buffer output
?>


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
        } else if (isset($_GET["error"])) {
            echo "<div class='alert alert-danger mx-auto w-50 mb-5'>";
            echo $_GET["error"];
            echo "</div>";
        }
        ?>

        <h1 class="text-center"><b>Edit Profile</b></h1>

        <div class="container">
            <?php
            if (isset($_SESSION["user"])) {
                $userEmail = $_SESSION["user"]["email"];

                echo '<ul class="nav nav-tabs mt-5">';
                echo '<li class="nav-item">';
                echo '    <a class="nav-link active" aria-current="page" href="/event_booking_app/user_profile.php">Profile</a>';
                echo '</li>';
                echo '</ul>';

                // User Profile Information
                $queryUserInfo = "SELECT * FROM Users WHERE Email = '$userEmail'";
                $resultUserInfo = $connection->query($queryUserInfo);

                if ($resultUserInfo->num_rows == 1) {
                    while ($record = $resultUserInfo->fetch_assoc()) {
                        $userID = $record["UserID"];
                        $userName = $record["Name"];
                        $userSurname = $record["Surname"];
                        $userBirthDate = $record["BirthDate"];
                        $userRole = $record["Role"];
                        $userRegistrateDate = $record["RegisterDate"];
                    }
                }
            }
            ?>

            <!-- Form Edit Profile -->
            <div class='mx-auto w-75 shadow mt-5 mb-5 p-4'>
                <form method='POST'>
                    <input type="hidden" name="userID" value="<?php echo $userID; ?>">
                    <div class="row">
                        <!-- User Name -->
                        <div class="col-md-6 mb-3 input-group-sm">
                            <label for="name" class="form-label">Name:</label>
                            <input type="text" class="form-control" id="name" value="<?php echo $userName; ?>"
                                name="name" required>
                        </div>

                        <!-- User Surname -->
                        <div class="col-md-6 mb-3 input-group-sm">
                            <label for="surname" class="form-label">Surname:</label>
                            <input type="text" class="form-control" id="surname" value="<?php echo $userSurname; ?>"
                                name="surname" required>
                        </div>
                    </div>

                    <div class="row">
                        <!-- User Birth Date -->
                        <div class="col-md-6 mb-3 input-group-sm">
                            <label for="birthDate" class="form-label">Birth Date:</label>
                            <input type="date" class="form-control" id="birthDate" name="birthDate"
                                value="<?php echo $userBirthDate; ?>" required>
                        </div>

                        <!-- User Email -->
                        <div class="col-md-6 mb-3 input-group-sm">
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" class="form-control" id="email" value="<?php echo $userEmail; ?>"
                                name="email" required>
                        </div>
                    </div>

                    <div class="row">
                        <!-- User Password -->
                        <div class="col-md-6 mb-3 input-group-sm">
                            <label for="password1" class="form-label">Password:</label>
                            <input type="password" class="form-control" id="password1"
                                placeholder="Enter a new password" name="password1" required>
                        </div>

                        <!-- User Password Confirmation -->
                        <div class="col-md-6 mb-3 input-group-sm">
                            <label for="password2" class="form-label">Confirm password:</label>
                            <input type="password" class="form-control" id="password2"
                                placeholder="Confirm your password" name="password2" required>
                        </div>
                    </div>

                    <!-- User Role -->
                    <div class="mb-3 input-group-sm">
                        <label for="role" class="form-label">Choose your role:</label>
                        <select name="role" class="form-select form-select-sm">
                            <?php
                            if ($userRole === 'User') {
                                echo " <option value='User' selected>User</option>";
                                echo "<option value='Organizer'>Organizer</option>";
                            } else {
                                echo " <option value='User'>User</option>";
                                echo "<option value='Organizer' selected>Organizer</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary btn-form w-10 mt-4">Save Changes</button>
                </form>
            </div>
        </div>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $userID = $connection->real_escape_string($_POST["userID"]);
            $userNameEdit = ucfirst(mysqli_real_escape_string($connection, $_POST["name"]));
            $userSurnameEdit = ucfirst(mysqli_real_escape_string($connection, $_POST["surname"]));
            $userBirthDateEdit = mysqli_real_escape_string($connection, $_POST["birthDate"]);
            $userEmailEdit = mysqli_real_escape_string($connection, $_POST["email"]);
            $userPassword1 = mysqli_real_escape_string($connection, $_POST["password1"]);
            $userPassword2 = mysqli_real_escape_string($connection, $_POST["password2"]);
            $userRoleEdit = ucfirst(mysqli_real_escape_string($connection, $_POST["role"]));

            if ($userPassword1 == $userPassword2) {
                $hashedPassword = password_hash($userPassword1, PASSWORD_BCRYPT);

                if ($userEmail !== $userEmailEdit) {
                    $checkEmail = $connection->prepare("SELECT * FROM Users WHERE Email = ?");
                    $checkEmail->bind_param("s", $userEmailEdit);
                    $checkEmail->execute();
                    $result = $checkEmail->get_result();
                    if ($result->num_rows > 0) {
                        $errorMessage = urldecode("Email already exists.");
                        header("Location: /event_booking_app/user_profile_edit.php?error=$errorMessage");
                        exit;
                    }
                }

                $updateUserInfo = $connection->prepare("UPDATE Users SET Name = ?, Surname = ?, Email = ?, Password = ?, BirthDate = ?, Role = ?, RegisterDate = ? WHERE UserID = ?");
                $updateUserInfo->bind_param("sssssssi", $userNameEdit, $userSurnameEdit, $userEmailEdit, $hashedPassword, $userBirthDateEdit, $userRoleEdit, $userRegistrateDate, $userID);

                if ($updateUserInfo->execute()) {
                    $_SESSION["user"] = array(
                        "name" => $userNameEdit,
                        "surname" => $userSurnameEdit,
                        "email" => $userEmailEdit,
                        "role" => $userRoleEdit,
                        "birthdate" => $userBirthDateEdit
                    );
                    $successMassage = urlencode("Your changes was saved successfully.");
                    header("Location: /event_booking_app/user_profile.php?success=$successMassage");
                    exit;
                } else {
                    $errorMessage = urldecode("Something went wrong: " . $connection->error);
                    header("Location: /event_booking_app/user_profile_edit.php?error=$errorMessage");
                    exit;
                }
            } else {
                $errorMessage = urldecode("Passwords do not match.");
                header("Location: /event_booking_app/user_profile_edit.php?error=$errorMessage");
                exit;
            }
        }
        ?>
</body>

</html>

<?php
ob_end_flush();  // Free buffer output
?>