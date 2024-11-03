<?php

$connection = new mysqli("localhost", "root", "", "Event_Management");

if ($connection->connect_error) {
    http_response_code(502);
    echo "Bad Gateway: The service is currently unavailable.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userName = ucfirst(mysqli_real_escape_string($connection, $_POST["name"]));
    $userSurname = ucfirst(mysqli_real_escape_string($connection, $_POST["surname"]));
    $userBirthDate = mysqli_real_escape_string($connection, $_POST["birthDate"]);
    $userEmail = mysqli_real_escape_string($connection, $_POST["email"]);
    $userPassword1 = mysqli_real_escape_string($connection, $_POST["password1"]);
    $userPassword2 = mysqli_real_escape_string($connection, $_POST["password2"]);
    $userRole = ucfirst(mysqli_real_escape_string($connection, $_POST["role"]));

    if ($userPassword1 == $userPassword2) {
        $hashedPassword = password_hash($userPassword1, PASSWORD_BCRYPT);

        $checkEmail = $connection->prepare("SELECT * FROM Users WHERE Email = ?");
        $checkEmail->bind_param("s", $userEmail);
        $checkEmail->execute();
        $result = $checkEmail->get_result();

        if ($result->num_rows > 0) {
            $errorMessage = urldecode("Email already exists.");
            header("Location: register.php?error=$errorMessage");
            exit;
        }

        $insertUser = $connection->prepare("INSERT INTO Users (Name, Surname, Email, Password, BirthDate, Role) VALUES (?, ?, ?, ?, ?, ?)");
        $insertUser->bind_param("ssssss", $userName, $userSurname, $userEmail, $hashedPassword, $userBirthDate, $userRole);

        if ($insertUser->execute()) {
            session_start();
            $_SESSION["user"] = array (
                "name" => $userName,
                "surname" => $userSurname,
                "email" => $userEmail,
                "role" => $userRole,
                "birthdate" => $userBirthDate
            );

            $successMassage = urlencode("You registered well.");
            header("Location: /event_booking_app/index.php?success=$successMassage");
            exit;
        } else {
            $errorMessage = urldecode("Something went wrong: " . $connection->error);
            header("Location: register.php?error=$errorMessage");
            exit;
        }
    } else {
        $errorMessage = urldecode("Passwords do not match.");
        header("Location: register.php?error=$errorMessage");
        exit;
    }
}

?>