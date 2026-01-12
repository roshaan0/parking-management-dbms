<?php


session_start();
require __DIR__ . '/vendor/autoload.php';
require_once 'db.php'; // Include your database connection

// Check if the user is logged in and has a valid role (Student or Faculty)
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || ($_SESSION['role'] !== 'Student' && $_SESSION['role'] !== 'Faculty')) {
    // Redirect to login if not authorized or not logged in
    header("location: Login.php");
    exit;
}

// Check if form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userID = $_SESSION['user_id']; // Get UserID from session
    $licensePlate = $_POST['license_plate'];
    $vehicleType = $_POST['vehicle_type'];
    $stickerNumber = isset($_POST['sticker_number']) ? $_POST['sticker_number'] : NULL;
    $isDefault = isset($_POST['is_default']) ? 1 : 0; // Checkbox value: 1 if checked, 0 if not

    // Basic validation (you can add more robust validation)
    if (empty($licensePlate) || empty($vehicleType)) {
        $_SESSION['registration_error'] = "License Plate and Vehicle Type are required.";
        header("Location: " . $_SESSION['role'] . "Dashboard.php"); // Redirect back to their dashboard
        exit;
    }

    // Prepare and execute the SQL statement to insert into the 'Vehicle' table
    $sql = "INSERT INTO Vehicle (UserID, LicensePlate, VehicleType, StickerNumber, IsDefault) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        $_SESSION['registration_error'] = "Database prepare error: " . $conn->error;
        header("Location: " . $_SESSION['role'] . "Dashboard.php");
        exit;
    }

    $stmt->bind_param("isssi", $userID, $licensePlate, $vehicleType, $stickerNumber, $isDefault);

    if ($stmt->execute()) {
        $_SESSION['registration_success'] = "Vehicle registered successfully!";
    } else {
        // Check for duplicate entry error specifically for LicensePlate or StickerNumber
        if ($conn->errno == 1062) {
            $_SESSION['registration_error'] = "Error: A vehicle with this License Plate or Sticker Number already exists.";
        } else {
            $_SESSION['registration_error'] = "Error: " . $stmt->error;
        }
    }

    $stmt->close();
    $conn->close();

    // Redirect back to the appropriate dashboard
    header("Location: " . $_SESSION['role'] . "Dashboard.php");
    exit;

} else {
    // If accessed directly without POST, redirect to login or dashboard
    header("location: Login.html");
    exit;
}
?>