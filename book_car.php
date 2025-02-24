<?php
require_once 'storage.php';
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$carStorage = new Storage(new JsonIO('cars.json'));
$bookingStorage = new Storage(new JsonIO('bookings.json'));

$carId = $_GET['id'] ?? null;
$car = $carStorage->findById($carId);

if (!$car) {
    echo "Car not found.";
    exit;
}

$errors = [];
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $startDate = $_POST['start_date'] ?? '';
    $endDate = $_POST['end_date'] ?? '';

    if (empty($startDate)) {
        $errors[] = 'Start Date is required.';
    }
    if (empty($endDate)) {
        $errors[] = 'End Date is required.';
    }
    if (strtotime($startDate) > strtotime($endDate)) {
        $errors[] = 'End Date must be after Start Date.';
    }

    $existingBookings = $bookingStorage->findAll(['car_id' => $carId]);
    foreach ($existingBookings as $booking) {
        if ((strtotime($startDate) <= strtotime($booking['end_date'])) && (strtotime($endDate) >= strtotime($booking['start_date']))) {
            header('Location: booking_failure.php?id=' . urlencode($carId) . '&start_date=' . urlencode($startDate) . '&end_date=' . urlencode($endDate));
            exit;
        }
    }

    if (empty($errors)) {
        $bookingStorage->add([
            'car_id' => $carId,
            'user_email' => $_SESSION['user']['email'],
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
        header('Location: booking_success.php?id=' . urlencode($carId) . '&start_date=' . urlencode($startDate) . '&end_date=' . urlencode($endDate));
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>iKarRental - Book Car</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Book <?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h1>
    <?php if ($success): ?>
        <p class="success">Booking successful!</p>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
        <ul class="errors">
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <form method="POST" action="book_car.php?id=<?php echo htmlspecialchars($car['id']); ?>" novalidate>
        <label for="start_date">Start Date:</label>
        <input type="date" name="start_date" id="start_date" required>
        <label for="end_date">End Date:</label>
        <input type="date" name="end_date" id="end_date" required>
        <button type="submit" class="button">Book</button>
    </form>
    <p><a href="logout.php" class="button">Logout</a></p>
</body>
</html>
