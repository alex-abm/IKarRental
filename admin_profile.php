<?php
require_once 'storage.php';
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

if ($_SESSION['user']['id'] !== '1') {
    header('Location: login.php');
    exit;
}

$bookingStorage = new Storage(new JsonIO('bookings.json'));
$carStorage = new Storage(new JsonIO('cars.json'));

if (isset($_POST['delete_car'])) {
    $carId = $_POST['car_id'];
    $carStorage->delete($carId);
    $bookingStorage->deleteMany(function ($booking) use ($carId) {
        return $booking['car_id'] === $carId;
    });
}

if (isset($_POST['edit_car'])) {
    $carId = $_POST['car_id'];
    $car = $carStorage->findById($carId);
    $car['brand'] = $_POST['brand'];
    $car['model'] = $_POST['model'];
    $car['year'] = $_POST['year'];
    $car['transmission'] = $_POST['transmission'];
    $car['fuel_type'] = $_POST['fuel_type'];
    $car['passengers'] = $_POST['passengers'];
    $car['daily_price_huf'] = $_POST['daily_price_huf'];
    $car['image'] = $_POST['image'];
    $carStorage->update($carId, $car);
}

if (isset($_POST['delete_booking'])) {
    $bookingId = $_POST['booking_id'];
    $bookingStorage->delete($bookingId);
}

if (isset($_POST['add_car'])) {
    $carStorage->add([
        'brand' => $_POST['brand'],
        'model' => $_POST['model'],
        'year' => $_POST['year'],
        'transmission' => $_POST['transmission'],
        'fuel_type' => $_POST['fuel_type'],
        'passengers' => $_POST['passengers'],
        'daily_price_huf' => $_POST['daily_price_huf'],
        'image' => $_POST['image']
    ]);
}

$bookings = $bookingStorage->findAll();
$cars = $carStorage->findAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>iKarRental - Admin Profile</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Admin Profile</h1>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['user']['full_name']); ?>! <a href="logout.php" class="button">Logout</a></p>
    <h2>All Bookings</h2>
    <ul>
        <?php foreach ($bookings as $booking): ?>
            <?php $car = $carStorage->findById($booking['car_id']); ?>
            <li>
                <h2><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h2>
                <p>User: <?php echo htmlspecialchars($booking['user_email']); ?></p>
                <p>Start Date: <?php echo htmlspecialchars($booking['start_date']); ?></p>
                <p>End Date: <?php echo htmlspecialchars($booking['end_date']); ?></p>
                <form method="POST" action="admin_profile.php">
                    <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking['id']); ?>">
                    <button type="submit" name="delete_booking" class="button">Delete Booking</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
    <h2>All Cars</h2>
    <ul>
        <?php foreach ($cars as $car): ?>
            <li>
                <h2><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h2>
                <form method="POST" action="admin_profile.php">
                    <input type="hidden" name="car_id" value="<?php echo htmlspecialchars($car['id']); ?>">
                    <label for="brand">Brand:</label>
                    <input type="text" name="brand" value="<?php echo htmlspecialchars($car['brand']); ?>" required>
                    <label for="model">Model:</label>
                    <input type="text" name="model" value="<?php echo htmlspecialchars($car['model']); ?>" required>
                    <label for="year">Year:</label>
                    <input type="number" name="year" value="<?php echo htmlspecialchars($car['year']); ?>" required>
                    <label for="transmission">Transmission:</label>
                    <input type="text" name="transmission" value="<?php echo htmlspecialchars($car['transmission']); ?>" required>
                    <label for="fuel_type">Fuel Type:</label>
                    <input type="text" name="fuel_type" value="<?php echo htmlspecialchars($car['fuel_type']); ?>" required>
                    <label for="passengers">Passengers:</label>
                    <input type="number" name="passengers" value="<?php echo htmlspecialchars($car['passengers']); ?>" required>
                    <label for="daily_price_huf">Daily Price (HUF):</label>
                    <input type="number" name="daily_price_huf" value="<?php echo htmlspecialchars($car['daily_price_huf']); ?>" required>
                    <label for="image">Image URL:</label>
                    <input type="text" name="image" value="<?php echo htmlspecialchars($car['image']); ?>" required>
                    <button type="submit" name="edit_car" class="button">Edit</button>
                    <button type="submit" name="delete_car" class="button">Delete</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
    <h2>Add New Car</h2>
    <form method="POST" action="admin_profile.php">
        <label for="brand">Brand:</label>
        <input type="text" name="brand" required>
        <label for="model">Model:</label>
        <input type="text" name="model" required>
        <label for="year">Year:</label>
        <input type="number" name="year" required>
        <label for="transmission">Transmission:</label>
        <input type="text" name="transmission" required>
        <label for="fuel_type">Fuel Type:</label>
        <input type="text" name="fuel_type" required>
        <label for="passengers">Passengers:</label>
        <input type="number" name="passengers" required>
        <label for="daily_price_huf">Daily Price (HUF):</label>
        <input type="number" name="daily_price_huf" required>
        <label for="image">Image URL:</label>
        <input type="text" name="image" required>
        <button type="submit" name="add_car" class="button">Add Car</button>
    </form>
    <a href="index.php" class="button">Back to Homepage</a>
</body>
</html>
