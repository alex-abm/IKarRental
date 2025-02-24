<?php
require_once 'storage.php';
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}


if ($_SESSION['user']['id'] === '1') {
    header('Location: admin_profile.php');
    exit;
}


$bookingStorage = new Storage(new JsonIO('bookings.json'));
$carStorage = new Storage(new JsonIO('cars.json'));


$userEmail = $_SESSION['user']['email'];
$bookings = $bookingStorage->findAll(['user_email' => $userEmail]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>iKarRental - Profile</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Your Bookings</h1>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['user']['full_name']); ?>! <a href="logout.php" class="button">Logout</a></p>
    <ul>
        <?php foreach ($bookings as $booking): ?>
            <?php $car = $carStorage->findById($booking['car_id']); ?>
            <li>
                <h2><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h2>
                <p>Start Date: <?php echo htmlspecialchars($booking['start_date']); ?></p>
                <p>End Date: <?php echo htmlspecialchars($booking['end_date']); ?></p>
            </li>
        <?php endforeach; ?>
    </ul>
    <a href="index.php" class="button">Back to Homepage</a>
</body>
</html>