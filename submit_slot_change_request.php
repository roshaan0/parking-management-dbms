<?php
session_start();
require __DIR__ . '/vendor/autoload.php';
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Faculty') {
    $_SESSION['faculty_action_error'] = "Unauthorized access to submit slot change request.";
    header("Location: FacultyDashboard.php#request-change");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_SESSION['user_id'];
    // Assuming form fields are named: change_reason, preferred_lot_id, preferred_slot_number, effective_date, additional_notes
    // And you have a way to get CurrentSlotID for the user.
    // For simplicity, let's assume some are posted.
    // You'll need to fetch CurrentSlotID for the user from their active reservation or assigned slot.

    $reason = $_POST['change_reason'] ?? null;
    $requestedSlotId = $_POST['preferred_slot_id'] ?? null; // This should be an ID, not just number
    $effectiveDate = $_POST['effective_date'] ?? null;
    $notes = $_POST['additional_notes'] ?? null;
    $status = 'Pending'; // Initial status

    // Fetch CurrentSlotID (Placeholder logic - implement based on your DB structure)
    $currentSlotId = null; // Initialize
    $sql_get_current_slot = "SELECT SlotID FROM Reservation WHERE UserID = ? AND Status = 'Active' ORDER BY ReservationStartTime DESC LIMIT 1";
    // OR $sql_get_current_slot = "SELECT SlotID FROM ParkingSlot WHERE AssignedTo = ? LIMIT 1";
    $stmt_get_current = $conn->prepare($sql_get_current_slot);
    if($stmt_get_current){
        $stmt_get_current->bind_param("i", $userId);
        $stmt_get_current->execute();
        $res_current = $stmt_get_current->get_result();
        if($res_current->num_rows > 0){
            $currentSlotId = $res_current->fetch_assoc()['SlotID'];
        }
        $stmt_get_current->close();
    }

    if (empty($reason) || empty($requestedSlotId) || empty($effectiveDate)) {
        $_SESSION['faculty_action_error'] = "All required fields (reason, preferred slot, effective date) must be filled for a slot change request.";
        header("Location: FacultyDashboard.php#request-change");
        exit;
    }

    // Validate if requestedSlotId exists and is available (or handle this logic during admin review)
    $sql_check_req_slot = "SELECT SlotID FROM ParkingSlot WHERE SlotID = ?";
    $stmt_check_req = $conn->prepare($sql_check_req_slot);
    $stmt_check_req->bind_param("i", $requestedSlotId);
    $stmt_check_req->execute();
    if($stmt_check_req->get_result()->num_rows == 0){
        $_SESSION['faculty_action_error'] = "The requested preferred slot ID does not exist.";
        header("Location: FacultyDashboard.php#request-change");
        exit;
    }
    $stmt_check_req->close();


    // Insert into SlotChangeRequest table
    // Table: RequestID (PK), UserID, CurrentSlotID (NULLABLE), RequestedSlotID, Reason, PreferredSchedule (TEXT/JSON), EffectiveDate, Notes, Status, RequestDate (TIMESTAMP)
    $sql = "INSERT INTO SlotChangeRequest (UserID, CurrentSlotID, RequestedSlotID, Reason, EffectiveDate, Notes, Status) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        $_SESSION['faculty_action_error'] = "Database error preparing request: " . $conn->error;
    } else {
        $stmt->bind_param("iiissss", $userId, $currentSlotId, $requestedSlotId, $reason, $effectiveDate, $notes, $status);
        if ($stmt->execute()) {
            $_SESSION['faculty_action_success'] = "Slot change request submitted successfully. You will be notified once it's reviewed.";
        } else {
            $_SESSION['faculty_action_error'] = "Error submitting request: " . $stmt->error;
        }
        $stmt->close();
    }
    $conn->close();
} else {
    $_SESSION['faculty_action_error'] = "Invalid request method.";
}
header("Location: FacultyDashboard.php#request-change");
exit;
?>