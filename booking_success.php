<?php
require_once 'storage.php';
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}


$carStorage = new Storage(new JsonIO('cars.json'));


$carId = $_GET['id'] ?? null;
$startDate = $_GET['start_date'] ?? null;
$endDate = $_GET['end_date'] ?? null;
$car = $carStorage->findById($carId);

if (!$car) {
    echo "Car not found.";
    exit;
}

$days = (strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24) + 1;
$totalPrice = $days * $car['daily_price_huf'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>iKarRental - Booking Success</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Booking Successful</h1>
    <p>Car: <?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></p>
    <p>Start Date: <?php echo htmlspecialchars($startDate); ?></p>
    <p>End Date: <?php echo htmlspecialchars($endDate); ?></p>
    <p>Total Price: <?php echo htmlspecialchars($totalPrice); ?> HUF</p>
    <a href="index.php" class="button">Back to Homepage</a> | <a href="profile.php" class="button">Profile</a>
    <p><a href="logout.php" class="button">Logout</a></p>
</body>
</html>