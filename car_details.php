<?php
require_once 'storage.php';
session_start();


$carStorage = new Storage(new JsonIO('cars.json'));


$carId = $_GET['id'] ?? null;
$car = $carStorage->findById($carId);

if (!$car) {
    echo "Car not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>iKarRental - Car Details</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="car-details">
        <h1><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h1>
        <img src="<?php echo htmlspecialchars($car['image']); ?>" alt="<?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?>" class="car-image">
        <p><strong>Year:</strong> <?php echo htmlspecialchars($car['year']); ?></p>
        <p><strong>Transmission:</strong> <?php echo htmlspecialchars($car['transmission']); ?></p>
        <p><strong>Fuel Type:</strong> <?php echo htmlspecialchars($car['fuel_type']); ?></p>
        <p><strong>Passengers:</strong> <?php echo htmlspecialchars($car['passengers']); ?></p>
        <p><strong>Daily Price:</strong> <?php echo htmlspecialchars($car['daily_price_huf']); ?> HUF</p>
        <?php if (isset($_SESSION['user'])): ?>
            <a href="book_car.php?id=<?php echo urlencode($car['id']); ?>" class="button">Book</a>
            <p><a href="logout.php" class="button">Logout</a></p>
        <?php else: ?>
            <p><a href="login.php" class="button">Login</a> or <a href="register.php" class="button">Register</a> to book this car.</p>
        <?php endif; ?>
    </div>
</body>
</html>