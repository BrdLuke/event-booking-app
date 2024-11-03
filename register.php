<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventTeam - Registration</title>

    <?php
    include_once("bootstrap_resources.html");
    ?>

    <link rel="stylesheet" href="/event_booking_app/css/styles.css">

    <style>
        .container {
            margin-top: 5rem;
            margin-bottom: 5rem;
        }
    </style>
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

    <div class="form-container container">
        <form method="POST" action="register_script.php" class="mt-5 mx-auto w-50 shadow-lg p-4 mb-4 bg-white">
            <h1 class="border p-2 rounded text-center"><b>Registration</b></h1>

            <!-- User Name -->
            <div class="mb-3 mt-5 input-group-sm">
                <label for="name" class="form-label">Name:</label>
                <input type="text" class="form-control" id="name" placeholder="Enter your name" name="name" required>
            </div>

            <!-- User Surname -->
            <div class="mb-3 mt-3 input-group-sm">
                <label for="surname" class="form-label">Surname:</label>
                <input type="text" class="form-control" id="surname" placeholder="Enter your surname" name="surname"
                    required>
            </div>

            <!-- User Birth Date -->
            <div class="mb-3 mt-3 input-group-sm">
                <label for="birthDate" class="form-label">Birth Date:</label>
                <input type="date" class="form-control" id="birthDate" placeholder="Enter your birth date"
                    name="birthDate" required>
            </div>

            <!-- User Email -->
            <div class="mb-3 mt-3 input-group-sm">
                <label for="email" class="form-label">Email:</label>
                <input type="email" class="form-control" id="email" placeholder="Enter your email" name="email"
                    required>
            </div>

            <!-- User Password -->
            <div class="mb-3 mt-3 input-group-sm">
                <label for="password1" class="form-label">Password:</label>
                <input type="password" class="form-control" id="password1" placeholder="Enter your password"
                    name="password1" required>
            </div>

            <!-- User Password Confermation -->
            <div class="mb-3 mt-3 input-group-sm">
                <label for="password2" class="form-label">Confirm password:</label>
                <input type="password" class="form-control" id="password2" placeholder="Confirm your password"
                    name="password2" required>
            </div>

            <!-- User Role -->
            <div class="mb-3 mt-3 input-group-sm">
                <label for="role" class="form-label">Choose you role:</label>
                <select name="role" class="form-select form-select-sm" aria-label=".form-select-sm example">
                    <option value="User" selected>User</option>
                    <option value="Organizer">Organizer</option>
                </select>
            </div>

            <!-- Error Message -->
            <?php
            if (isset($_GET["error"])) {
                echo '
                <div class="mt-4 mb-4 alert alert-danger d-flex align-items-center p-1 mx-auto w-50" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor"
                        class="bi bi-exclamation-triangle-fill flex-shrink-0 me-4" viewBox="0 0 16 16" role="img"
                        aria-label="Warning:">
                        <path
                            d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
                    </svg>
                    <div>
                        ' . $_GET["error"] . '
                    </div>
                    <a class="btn-close ms-5" data-bs-dismiss="alert" aria-label="Close" href="login.php"></a>
                </div>
                ';
            }
            ?>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary btn-form mx-auto w-50 mt-5 mb-5">Submit</button>

            <p class="mt-4 text-center">Do you have an account? <a href="login.php"><b>Login now</b></a></p>
        </form>
    </div>
</body>

</html>