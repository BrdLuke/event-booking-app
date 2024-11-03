<?php
session_start();
if (isset($_SESSION["user"])) {
    session_destroy();
    $successMassage = urlencode("You logged out without any problems. Log in soon!");
    header("Location: /event_booking_app/index.php?success=$successMassage");
    exit;
}
?>