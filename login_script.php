<?php

$connection = new mysqli("localhost", "root", "", "Event_Management");

if ($connection->connect_error) {
    http_response_code(502);
    echo "Bad Gateway: The service is currently unavailable.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userEmail = mysqli_real_escape_string($connection, $_POST["email"]);
    $userPassword = mysqli_real_escape_string($connection, $_POST["password"]);

    $queryCheckPassword = $connection->prepare("SELECT Password, Name, Surname, Role, BirthDate FROM Users WHERE Email = ?");
    $queryCheckPassword->bind_param("s", $userEmail);
    $queryCheckPassword->execute();
    $resultCheckPassword = $queryCheckPassword->get_result();

    if ($resultCheckPassword->num_rows == 1) {
        $record = $resultCheckPassword->fetch_assoc();
        $hashedPassword = $record["Password"];

        if (password_verify($userPassword, $hashedPassword)) {
            session_start();

            $_SESSION["user"] = array(
                "name" => $record["Name"],
                "surname" => $record["Surname"],
                "email" => $userEmail,
                "role" => $record["Role"],
                "birthdate" => $record["BirthDate"]
            );
            $successMassage = urlencode("You're now logged in.");
            header("Location: /event_booking_app/index.php?success=$successMassage");
            exit;
        } else {
            $errorMessage = urlencode("Password not correct.");
            header("Location: login.php?error=$errorMessage");
            exit;
        }
    } else {
        $errorMessage = urlencode("User not found.");
        header("Location: login.php?error=$errorMessage");
        exit;
    }
}
?>