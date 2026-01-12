<?php
session_start();
require __DIR__ . '/vendor/autoload.php';
require_once 'db.php'; // Include your database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    $_SESSION['general_error'] = "You must be logged in to perform this action.";
    header("location: Login.php"); // Redirect to login
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['vehicle_id'])) {
    $vehicleIdToRemove = $_POST['vehicle_id'];
    $loggedInUserId = $_SESSION['user_id'];

    // Verify that the vehicle belongs to the logged-in user before deleting
    $sql_check = "SELECT UserID FROM Vehicle WHERE VehicleID = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("i", $vehicleIdToRemove);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows === 1) {
        $vehicle_owner = $result_check->fetch_assoc();
        if ($vehicle_owner['UserID'] == $loggedInUserId) {
            // User owns this vehicle, proceed with deletion
            $sql_delete = "DELETE FROM Vehicle WHERE VehicleID = ?";
            $stmt_delete = $conn->prepare($sql_delete);
            if ($stmt_delete === false) {
                $_SESSION['vehicle_action_error'] = "Database prepare error (delete vehicle): " . $conn->error;
            } else {
                $stmt_delete->bind_param("i", $vehicleIdToRemove);
                if ($stmt_delete->execute()) {
                    if ($stmt_delete->affected_rows > 0) {
                        $_SESSION['vehicle_action_success'] = "Vehicle removed successfully.";
                    } else {
                        $_SESSION['vehicle_action_error'] = "Vehicle not found or already removed.";
                    }
                } else {
                    $_SESSION['vehicle_action_error'] = "Error removing vehicle: " . $stmt_delete->error;
                }
                $stmt_delete->close();
            }
        } else {
            $_SESSION['vehicle_action_error'] = "Unauthorized action. You do not own this vehicle.";
        }
    } else {
        $_SESSION['vehicle_action_error'] = "Vehicle not found.";
    }
    $stmt_check->close();
    $conn->close();

} else {
    $_SESSION['vehicle_action_error'] = "Invalid request to remove vehicle.";
}

// Redirect back to the user's dashboard
$dashboardFile = ($_SESSION['role'] === 'Student' ? 'StudentDashboard.php' : ($_SESSION['role'] === 'Faculty' ? 'FacultyDashboard.php' : 'Login.php'));
header("Location: " . $dashboardFile . "#registered-vehicles");
exit;
?>