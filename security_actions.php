<?php
session_start();
require __DIR__ . '/vendor/autoload.php';
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Security') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit;
}

$action = $_POST['action'] ?? null;
$response = ['status' => 'error', 'message' => 'Invalid action.'];

switch ($action) {
    case 'reserve_slot_security':
        // For reserving a slot (e.g., for a visitor or temporary faculty assignment)
        if (isset($_POST['slot_id'], $_POST['reserve_for_type'], $_POST['reserve_date'], $_POST['reserve_start_time'], $_POST['reserve_end_time'])) {
            $slotId = $_POST['slot_id'];
            $reserveForType = $_POST['reserve_for_type']; // 'faculty', 'visitor', 'event', 'maintenance'
            $reserveDate = $_POST['reserve_date'];
            $startTime = $_POST['reserve_start_time'];
            $endTime = $_POST['reserve_end_time'];
            $purpose = $_POST['reserve_purpose'] ?? 'N/A';
            $assignedUserId = null; // For visitor or event, might be null
            $statusToSet = 'Reserved';

            if ($reserveForType === 'faculty' && isset($_POST['faculty_user_id'])) {
                $assignedUserId = $_POST['faculty_user_id'];
            } elseif ($reserveForType === 'visitor') {
                $visitorName = $_POST['visitor_name'] ?? 'Visitor';
                $purpose = "Visitor: " . $visitorName . " - " . $purpose;
                // Could log visitor details elsewhere or in a 'notes' field in Reservation
            } elseif ($reserveForType === 'maintenance') {
                $statusToSet = 'Maintenance';
            }


            $conn->begin_transaction();
            try {
                // Check slot availability
                $sql_check = "SELECT SlotID FROM ParkingSlot WHERE SlotID = ? AND (Status = 'Available' OR Status IS NULL) FOR UPDATE";
                $stmt_check = $conn->prepare($sql_check);
                $stmt_check->bind_param("i", $slotId);
                $stmt_check->execute();
                if ($stmt_check->get_result()->num_rows === 0) {
                    throw new Exception("Slot " . htmlspecialchars($slotId) . " is not available for reservation.");
                }
                $stmt_check->close();

                // Update ParkingSlot
                $sql_update = "UPDATE ParkingSlot SET Status = ?, IsOccupied = 1, AssignedTo = ? WHERE SlotID = ?";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bind_param("sii", $statusToSet, $assignedUserId, $slotId);
                if (!$stmt_update->execute() || $stmt_update->affected_rows === 0) {
                    throw new Exception("Failed to update slot status for reservation.");
                }
                $stmt_update->close();

                // Create Reservation record (optional but recommended)
                $reservationStart = $reserveDate . ' ' . $startTime;
                $reservationEnd = $reserveDate . ' ' . $endTime;
                $resStatus = ($statusToSet === 'Maintenance') ? 'Maintenance' : 'Active';

                $sql_res = "INSERT INTO Reservation (UserID, SlotID, ReservationStartTime, ReservationEndTime, Status, Notes) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt_res = $conn->prepare($sql_res);
                // UserID for reservation can be security personnel's ID who made the reservation, or $assignedUserId if faculty
                $reservingUserId = ($assignedUserId) ? $assignedUserId : $_SESSION['user_id'];
                $stmt_res->bind_param("iissss", $reservingUserId, $slotId, $reservationStart, $reservationEnd, $resStatus, $purpose);
                if (!$stmt_res->execute()) {
                    throw new Exception("Failed to create reservation entry: " . $stmt_res->error);
                }
                $stmt_res->close();

                $conn->commit();
                $response = ['status' => 'success', 'message' => 'Slot ' . htmlspecialchars($slotId) . ' ' . strtolower($statusToSet) . ' successfully.'];
            } catch (Exception $e) {
                $conn->rollback();
                $response = ['status' => 'error', 'message' => $e->getMessage()];
            }
        } else {
            $response['message'] = 'Missing data for slot reservation.';
        }
        break;

    case 'free_slot_security':
        if (isset($_POST['slot_id'])) {
            $slotId = $_POST['slot_id'];
            $reason = $_POST['free_reason'] ?? 'N/A';
            $notes = $_POST['free_notes'] ?? '';

            $conn->begin_transaction();
            try {
                // Update ParkingSlot
                $sql_update = "UPDATE ParkingSlot SET Status = 'Available', IsOccupied = 0, AssignedTo = NULL WHERE SlotID = ?";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bind_param("i", $slotId);
                if (!$stmt_update->execute() || $stmt_update->affected_rows === 0) {
                    // If no rows affected, maybe it was already available, or an issue occurred
                    // For simplicity, we'll assume success if execute doesn't fail, but you might want more robust checking.
                   // throw new Exception("Failed to update slot status or slot was already available.");
                }
                $stmt_update->close();

                // Update corresponding active Reservation to 'Completed' or 'Cancelled'
                $sql_update_res = "UPDATE Reservation SET Status = 'Completed', Notes = CONCAT(IFNULL(Notes,''), ' Freed by security: ', ?) WHERE SlotID = ? AND Status = 'Active'"; // Or a status like 'ManuallyFreed'
                $stmt_update_res = $conn->prepare($sql_update_res);
                $freeLog = "Reason: $reason. Notes: $notes";
                $stmt_update_res->bind_param("si", $freeLog, $slotId);
                $stmt_update_res->execute(); // Don't throw error if no reservation found, slot might have been occupied without one
                $stmt_update_res->close();

                $conn->commit();
                $response = ['status' => 'success', 'message' => 'Slot ' . htmlspecialchars($slotId) . ' freed successfully.'];
            } catch (Exception $e) {
                $conn->rollback();
                $response = ['status' => 'error', 'message' => $e->getMessage()];
            }
        } else {
            $response['message'] = 'Missing slot ID for freeing slot.';
        }
        break;

    default:
        // $response already set to invalid action
        break;
}

echo json_encode($response);
$conn->close();
?>