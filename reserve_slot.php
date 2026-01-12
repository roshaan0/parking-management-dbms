<?php
session_start();
require __DIR__ . '/vendor/autoload.php';
require_once 'db.php'; // Database connection

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['slot_id'], $_POST['reserve_date'], $_POST['reserve_time'], $_POST['vehicle_id'])) {
    $slotId = $_POST['slot_id'];
    $reserveDate = $_POST['reserve_date'];
    $reserveTime = $_POST['reserve_time'];
    $vehicleId = $_POST['vehicle_id']; // Vehicle to be used for parking
    $userId = $_SESSION['user_id'];

    // Combine date and time for a DATETIME field if needed (e.g., ReservationStart)
    $reservationStartDateTime = $reserveDate . ' ' . $reserveTime;

    // Start transaction
    $conn->begin_transaction();

    try {
        // 1. Check if the slot is actually available and of correct type for the user
        // (e.g., student can only reserve student slots)
        $sql_check_slot = "SELECT SlotID, Status, SlotType FROM ParkingSlot WHERE SlotID = ? AND (Status = 'Available' OR Status IS NULL) FOR UPDATE"; // Lock the row
        $stmt_check_slot = $conn->prepare($sql_check_slot);
        $stmt_check_slot->bind_param("i", $slotId);
        $stmt_check_slot->execute();
        $result_check_slot = $stmt_check_slot->get_result();

        if ($result_check_slot->num_rows === 0) {
            throw new Exception("Slot is not available, already occupied/reserved, or does not exist.");
        }
        $slotDetails = $result_check_slot->fetch_assoc();
        $stmt_check_slot->close();

        // Add role-based slot type validation if necessary
        // Example: if ($_SESSION['role'] === 'Student' && $slotDetails['SlotType'] !== 'Student') {
        // throw new Exception("Students can only reserve student-designated slots.");
        // }

        // 2. Check if the user already has an active reservation (optional, based on rules)
        $sql_check_existing_res = "SELECT ReservationID FROM Reservation WHERE UserID = ? AND Status = 'Active'";
        $stmt_check_existing_res = $conn->prepare($sql_check_existing_res);
        $stmt_check_existing_res->bind_param("i", $userId);
        $stmt_check_existing_res->execute();
        if ($stmt_check_existing_res->get_result()->num_rows > 0) {
            // throw new Exception("You already have an active reservation.");
            // Or, if users can have multiple, adjust logic. For now, let's assume one active reservation.
        }
        $stmt_check_existing_res->close();


        // 3. Update ParkingSlot table
        $sql_update_slot = "UPDATE ParkingSlot SET Status = 'Reserved', IsOccupied = 1, AssignedTo = ? WHERE SlotID = ?";
        $stmt_update_slot = $conn->prepare($sql_update_slot);
        $stmt_update_slot->bind_param("ii", $userId, $slotId);
        if (!$stmt_update_slot->execute() || $stmt_update_slot->affected_rows === 0) {
            throw new Exception("Failed to update parking slot status.");
        }
        $stmt_update_slot->close();

        // 4. Create a record in Reservation table
        // Assuming Reservation table columns: UserID, SlotID, VehicleID, ReservationStartTime, Status (e.g., 'Pending', 'Active')
        // ReservationEndTime might be NULL initially or set based on rules
        $reservationStatus = 'Active'; // Or 'Pending' if admin approval is needed
        $sql_insert_reservation = "INSERT INTO Reservation (UserID, SlotID, VehicleID, ReservationStartTime, Status) VALUES (?, ?, ?, ?, ?)";
        $stmt_insert_reservation = $conn->prepare($sql_insert_reservation);
        $stmt_insert_reservation->bind_param("iiiss", $userId, $slotId, $vehicleId, $reservationStartDateTime, $reservationStatus);
        if (!$stmt_insert_reservation->execute()) {
            throw new Exception("Failed to create reservation record: " . $stmt_insert_reservation->error);
        }
        $stmt_insert_reservation->close();

        // Commit transaction
        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => 'Slot ' . htmlspecialchars($slotId) . ' reserved successfully!']);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }

    $conn->close();

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request data.']);
}
?>