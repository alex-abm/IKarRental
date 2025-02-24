<?php
require_once 'storage.php';
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}


$carId = $_GET['id'] ?? null;
$startDate = $_GET['start_date'] ?? null;
$endDate = $_GET['end_date'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>iKarRental - Booking Failure</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Booking Failed</h1>
    <p>The car is already booked for the selected dates:</p>
    <p>Start Date: <?php echo htmlspecialchars($startDate); ?></p>
    <p>End Date: <?php echo htmlspecialchars($endDate); ?></p>
    <a href="index.php">Back to Homepage</a>
    <p><a href="logout.php">Logout</a></p>
</body>
</html>