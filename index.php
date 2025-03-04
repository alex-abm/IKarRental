<?php
require_once 'storage.php';
session_start();


$carStorage = new Storage(new JsonIO('cars.json'));
$bookingStorage = new Storage(new JsonIO('bookings.json'));


$cars = $carStorage->findAll();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $filters = [
        'transmission' => $_GET['transmission'] ?? null,
        'passengers' => $_GET['passengers'] ?? null,
        'min_price' => $_GET['min_price'] ?? null,
        'max_price' => $_GET['max_price'] ?? null,
        'start_date' => $_GET['start_date'] ?? null,
        'end_date' => $_GET['end_date'] ?? null,
    ];

    $cars = array_filter($cars, function ($car) use ($filters, $bookingStorage) {
        if ($filters['transmission'] && $car['transmission'] !== $filters['transmission']) {
            return false;
        }
        if ($filters['passengers'] && $car['passengers'] < $filters['passengers']) {
            return false;
        }
        if ($filters['min_price'] && $car['daily_price_huf'] < $filters['min_price']) {
            return false;
        }
        if ($filters['max_price'] && $car['daily_price_huf'] > $filters['max_price']) {
            return false;
        }
        if ($filters['start_date'] && $filters['end_date']) {
            $existingBookings = $bookingStorage->findAll(['car_id' => $car['id']]);
            foreach ($existingBookings as $booking) {
                if ((strtotime($filters['start_date']) <= strtotime($booking['end_date'])) && (strtotime($filters['end_date']) >= strtotime($booking['start_date']))) {
                    return false;
                }
            }
        }
        return true;
    });
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>iKarRental - Homepage</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>iKarRental</h1>
    <?php if (isset($_SESSION['user'])): ?>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['user']['full_name']); ?>! <a href="logout.php" class="button">Logout</a> | <a href="profile.php" class="button">Profile</a></p>
    <?php else: ?>
        <p><a href="login.php" class="button">Login</a> | <a href="register.php" class="button">Register</a></p>
    <?php endif; ?>
    <form method="GET" action="index.php">
        <label for="transmission">Transmission:</label>
        <select name="transmission" id="transmission">
            <option value="">Any</option>
            <option value="Automatic" <?php echo isset($filters['transmission']) && $filters['transmission'] === 'Automatic' ? 'selected' : ''; ?>>Automatic</option>
            <option value="Manual" <?php echo isset($filters['transmission']) && $filters['transmission'] === 'Manual' ? 'selected' : ''; ?>>Manual</option>
        </select>
        <label for="passengers">Passengers:</label>
        <input type="number" name="passengers" id="passengers" min="1" value="<?php echo htmlspecialchars($filters['passengers'] ?? ''); ?>">
        <label for="min_price">Min Price:</label>
        <input type="number" name="min_price" id="min_price" min="0" value="<?php echo htmlspecialchars($filters['min_price'] ?? ''); ?>">
        <label for="max_price">Max Price:</label>
        <input type="number" name="max_price" id="max_price" min="0" value="<?php echo htmlspecialchars($filters['max_price'] ?? ''); ?>">
        <label for="start_date">Start Date:</label>
        <input type="date" name="start_date" id="start_date" value="<?php echo htmlspecialchars($filters['start_date'] ?? ''); ?>">
        <label for="end_date">End Date:</label>
        <input type="date" name="end_date" id="end_date" value="<?php echo htmlspecialchars($filters['end_date'] ?? ''); ?>">
        <button type="submit">Filter</button>
    </form>
    <div class="car-list">
        <?php foreach ($cars as $car): ?>
            <div class="car-box">
                <a href="car_details.php?id=<?php echo urlencode($car['id']); ?>">
                    <img src="<?php echo htmlspecialchars($car['image']); ?>" alt="<?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?>">
                    <h2><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h2>
                    <p>Transmission: <?php echo htmlspecialchars($car['transmission']); ?></p>
                    <p>Passengers: <?php echo htmlspecialchars($car['passengers']); ?></p>
                    <p>Daily Price: <?php echo htmlspecialchars($car['daily_price_huf']); ?> HUF</p>
                </a>
                <form method="POST" action="book_car.php?id=<?php echo urlencode($car['id']); ?>">
                    <button type="submit" class="button">Book</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>