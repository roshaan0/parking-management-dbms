<?php
session_start();
require __DIR__ . '/vendor/autoload.php';

require_once 'db.php'; // Include your database connection

// Ensure only Admin users can access this script
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    $_SESSION['admin_action_error'] = "Unauthorized access.";
    header("location: AdminDashboard.php");
    exit;
}

// Check if an action was submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $action = $_POST['action'];
    $responseMessage = '';
    $responseType = '';

    switch ($action) {
        case 'change_role':
            if (isset($_POST['user_id']) && isset($_POST['new_role'])) {
                $userId = $_POST['user_id'];
                $newRole = $_POST['new_role'];

                // Validate new role
                $allowedRoles = ['Student', 'Faculty', 'Security', 'Admin'];
                if (!in_array($newRole, $allowedRoles)) {
                    $responseMessage = "Invalid role selected.";
                    $responseType = 'error';
                    break;
                }

                // Prepare and execute the update statement
                $sql = "UPDATE User SET Role = ? WHERE UserID = ?";
                $stmt = $conn->prepare($sql);
                if ($stmt === false) {
                    $responseMessage = "Database prepare error: " . $conn->error;
                    $responseType = 'error';
                } else {
                    $stmt->bind_param("si", $newRole, $userId);
                    if ($stmt->execute()) {
                        $responseMessage = "User ID " . htmlspecialchars($userId) . "'s role changed to " . htmlspecialchars($newRole) . " successfully!";
                        $responseType = 'success';
                    } else {
                        $responseMessage = "Error changing role: " . $stmt->error;
                        $responseType = 'error';
                    }
                    $stmt->close();
                }
            } else {
                $responseMessage = "Required data for changing role is missing.";
                $responseType = 'error';
            }
            break;

        case 'remove_user':
            if (isset($_POST['user_id'])) {
                $userId = $_POST['user_id'];

                // Prevent admin from deleting themselves
                if ($userId == $_SESSION['user_id']) {
                    $responseMessage = "You cannot remove your own admin account.";
                    $responseType = 'error';
                    break;
                }

                // Prepare and execute the delete statement
                $sql = "DELETE FROM User WHERE UserID = ?";
                $stmt = $conn->prepare($sql);
                if ($stmt === false) {
                    $responseMessage = "Database prepare error: " . $conn->error;
                    $responseType = 'error';
                } else {
                    $stmt->bind_param("i", $userId);
                    if ($stmt->execute()) {
                        if ($stmt->affected_rows > 0) {
                            $responseMessage = "User ID " . htmlspecialchars($userId) . " removed successfully.";
                            $responseType = 'success';
                        } else {
                            $responseMessage = "User ID " . htmlspecialchars($userId) . " not found or already removed.";
                            $responseType = 'warning'; // Use warning for not found
                        }
                    } else {
                        $responseMessage = "Error removing user: " . $stmt->error;
                        $responseType = 'error';
                    }
                    $stmt->close();
                }
            } else {
                $responseMessage = "User ID for removal is missing.";
                $responseType = 'error';
            }
            break;

        case 'toggle_slot_occupied':
            if (isset($_POST['slot_id']) && isset($_POST['new_status'])) {
                $slotId = $_POST['slot_id'];
                $newStatus = (int)$_POST['new_status']; // 0 for false, 1 for true

                // Prepare and execute the update statement for IsOccupied
                $sql = "UPDATE ParkingSlot SET IsOccupied = ? WHERE SlotID = ?";
                $stmt = $conn->prepare($sql);
                if ($stmt === false) {
                    $responseMessage = "Database prepare error: " . $conn->error;
                    $responseType = 'error';
                } else {
                    $stmt->bind_param("ii", $newStatus, $slotId);
                    if ($stmt->execute()) {
                        $actionVerb = ($newStatus == 1) ? 'occupied' : 'freed';
                        $responseMessage = "Slot ID " . htmlspecialchars($slotId) . " status changed to " . htmlspecialchars($actionVerb) . " successfully.";
                        $responseType = 'success';
                    } else {
                        $responseMessage = "Error changing slot status: " . $stmt->error;
                        $responseType = 'error';
                    }
                    $stmt->close();
                }
            } else {
                $responseMessage = "Required data for toggling slot status is missing.";
                $responseType = 'error';
            }
            break;

        case 'assign_slot':
            if (isset($_POST['slot_id']) && isset($_POST['user_id_to_assign'])) {
                $slotId = $_POST['slot_id'];
                $userIdToAssign = $_POST['user_id_to_assign'];

                // Validate if the user ID exists (optional but recommended)
                $sql_check_user = "SELECT UserID FROM User WHERE UserID = ?";
                $stmt_check_user = $conn->prepare($sql_check_user);
                $stmt_check_user->bind_param("i", $userIdToAssign);
                $stmt_check_user->execute();
                $result_check_user = $stmt_check_user->get_result();

                if ($result_check_user->num_rows === 0) {
                    $responseMessage = "User ID " . htmlspecialchars($userIdToAssign) . " does not exist.";
                    $responseType = 'error';
                    $stmt_check_user->close();
                    break;
                }
                $stmt_check_user->close();

                // Prepare and execute the update statement for AssignedTo
                $sql = "UPDATE ParkingSlot SET AssignedTo = ?, IsOccupied = 1 WHERE SlotID = ?";
                $stmt = $conn->prepare($sql);
                if ($stmt === false) {
                    $responseMessage = "Database prepare error: " . $conn->error;
                    $responseType = 'error';
                } else {
                    $stmt->bind_param("ii", $userIdToAssign, $slotId);
                    if ($stmt->execute()) {
                        $responseMessage = "Slot ID " . htmlspecialchars($slotId) . " assigned to User ID " . htmlspecialchars($userIdToAssign) . " successfully.";
                        $responseType = 'success';
                    } else {
                        $responseMessage = "Error assigning slot: " . $stmt->error;
                        $responseType = 'error';
                    }
                    $stmt->close();
                }
            } else {
                $responseMessage = "Required data for assigning slot is missing.";
                $responseType = 'error';
            }
            break;

        case 'approve_slot_request':
        case 'reject_slot_request':
            if (isset($_POST['request_id'])) {
                $requestId = $_POST['request_id'];
                $newStatus = ($action === 'approve_slot_request') ? 'Approved' : 'Rejected';

                // Get request details to update ParkingSlot if approved
                $sql_get_request = "SELECT UserID, RequestedSlotID, CurrentSlotID FROM SlotChangeRequest WHERE RequestID = ?";
                $stmt_get_request = $conn->prepare($sql_get_request);
                $stmt_get_request->bind_param("i", $requestId);
                $stmt_get_request->execute();
                $result_get_request = $stmt_get_request->get_result();
                $requestDetails = $result_get_request->fetch_assoc();
                $stmt_get_request->close();

                if (!$requestDetails) {
                    $responseMessage = "Slot change request not found.";
                    $responseType = 'error';
                    break;
                }

                // Start transaction for atomicity
                $conn->begin_transaction();
                $success = true;

                // 1. Update SlotChangeRequest status
                $sql_update_request = "UPDATE SlotChangeRequest SET Status = ?, ReviewedBy = ?, ReviewedAt = NOW() WHERE RequestID = ?";
                $stmt_update_request = $conn->prepare($sql_update_request);
                if ($stmt_update_request === false) {
                    $responseMessage = "Database prepare error (request update): " . $conn->error;
                    $responseType = 'error';
                    $success = false;
                } else {
                    $stmt_update_request->bind_param("sii", $newStatus, $loggedInUserId, $requestId);
                    if (!$stmt_update_request->execute()) {
                        $responseMessage = "Error updating request status: " . $stmt_update_request->error;
                        $responseType = 'error';
                        $success = false;
                    }
                    $stmt_update_request->close();
                }

                // 2. If approved, update ParkingSlot assignments
                if ($success && $newStatus === 'Approved') {
                    $userId = $requestDetails['UserID'];
                    $requestedSlotId = $requestDetails['RequestedSlotID'];
                    $currentSlotId = $requestDetails['CurrentSlotID'];

                    // Unassign from current slot (if it was assigned)
                    $sql_unassign_current = "UPDATE ParkingSlot SET AssignedTo = NULL, IsOccupied = 0 WHERE SlotID = ?";
                    $stmt_unassign_current = $conn->prepare($sql_unassign_current);
                    if ($stmt_unassign_current === false) {
                        $responseMessage = "Database prepare error (unassign current): " . $conn->error;
                        $responseType = 'error';
                        $success = false;
                    } else {
                        $stmt_unassign_current->bind_param("i", $currentSlotId);
                        if (!$stmt_unassign_current->execute()) {
                            $responseMessage = "Error unassigning current slot: " . $stmt_unassign_current->error;
                            $responseType = 'error';
                            $success = false;
                        }
                        $stmt_unassign_current->close();
                    }

                    // Assign to requested slot
                    if ($success) {
                        $sql_assign_new = "UPDATE ParkingSlot SET AssignedTo = ?, IsOccupied = 1 WHERE SlotID = ?";
                        $stmt_assign_new = $conn->prepare($sql_assign_new);
                        if ($stmt_assign_new === false) {
                            $responseMessage = "Database prepare error (assign new): " . $conn->error;
                            $responseType = 'error';
                            $success = false;
                        } else {
                            $stmt_assign_new->bind_param("ii", $userId, $requestedSlotId);
                            if (!$stmt_assign_new->execute()) {
                                $responseMessage = "Error assigning new slot: " . $stmt_assign_new->error;
                                $responseType = 'error';
                                $success = false;
                            }
                            $stmt_assign_new->close();
                        }
                    }
                }

                if ($success) {
                    $conn->commit();
                    $responseMessage = "Slot change request ID " . htmlspecialchars($requestId) . " " . htmlspecialchars(strtolower($newStatus)) . " successfully.";
                    $responseType = 'success';
                } else {
                    $conn->rollback();
                    // $responseMessage already set by specific error
                }
            } else {
                $responseMessage = "Request ID for slot change action is missing.";
                $responseType = 'error';
            }
            break;
// ... (other cases) ...

        case 'add_slot':
            if (isset($_POST['slot_number'], $_POST['slot_type'], $_POST['slot_zone'])) {
                $slotNumber = $_POST['slot_number'];
                $slotType = $_POST['slot_type'];
                $slotZone = $_POST['slot_zone'];
                // Default status for a new slot is 'Available' and IsOccupied = 0
                $status = 'Available';
                $isOccupied = 0;

                $sql = "INSERT INTO ParkingSlot (SlotNumber, SlotType, Zone, Status, IsOccupied) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                if ($stmt === false) {
                    $responseMessage = "Database prepare error (add slot): " . $conn->error;
                    $responseType = 'error';
                } else {
                    $stmt->bind_param("ssssi", $slotNumber, $slotType, $slotZone, $status, $isOccupied);
                    if ($stmt->execute()) {
                        $responseMessage = "Parking slot " . htmlspecialchars($slotNumber) . " added successfully.";
                        $responseType = 'success';
                    } else {
                        if ($conn->errno == 1062) { // Check for duplicate entry
                            $responseMessage = "Error adding slot: Slot Number '" . htmlspecialchars($slotNumber) . "' already exists.";
                        } else {
                            $responseMessage = "Error adding slot: " . $stmt->error;
                        }
                        $responseType = 'error';
                    }
                    $stmt->close();
                }
            } else {
                $responseMessage = "Required data for adding slot is missing.";
                $responseType = 'error';
            }
            break;

        case 'remove_slot':
            if (isset($_POST['slot_id'])) {
                $slotId = $_POST['slot_id'];

                // Optional: Check if the slot is occupied or has active reservations before deleting
                // For simplicity, direct deletion is shown here.

                $sql = "DELETE FROM ParkingSlot WHERE SlotID = ?";
                $stmt = $conn->prepare($sql);
                if ($stmt === false) {
                    $responseMessage = "Database prepare error (remove slot): " . $conn->error;
                    $responseType = 'error';
                } else {
                    $stmt->bind_param("i", $slotId);
                    if ($stmt->execute()) {
                        if ($stmt->affected_rows > 0) {
                            $responseMessage = "Slot ID " . htmlspecialchars($slotId) . " removed successfully.";
                            $responseType = 'success';
                        } else {
                            $responseMessage = "Slot ID " . htmlspecialchars($slotId) . " not found or already removed.";
                            $responseType = 'warning';
                        }
                    } else {
                        $responseMessage = "Error removing slot: " . $stmt->error;
                        $responseType = 'error';
                    }
                    $stmt->close();
                }
            } else {
                $responseMessage = "Slot ID for removal is missing.";
                $responseType = 'error';
            }
            break;

        // The 'toggle_slot_occupied' case seems to cover what was requested for 'change_slot_status'
        // in AdminDashboard.php. If 'change_slot_status' refers to the 'Status' column (Available, Occupied, Maintenance)
        // then a new case would be needed. The current 'toggle_slot_occupied' updates 'IsOccupied'.
        // For clarity, I'll rename the 'change_slot_status' action in AdminDashboard.php to use 'toggle_slot_occupied'
        // or 'assign_slot' where appropriate, or create a dedicated 'update_slot_admin_status' if the text status is meant.

        // Let's assume 'change_slot_status' from the AdminDashboard was meant to change the textual 'Status' field.
        case 'change_slot_admin_status': // Renamed to be specific
            if (isset($_POST['slot_id']) && isset($_POST['new_status_text'])) {
                $slotId = $_POST['slot_id'];
                $newStatusText = $_POST['new_status_text'];
                $newIsOccupied = 0; // Default to not occupied

                // Validate new_status_text
                $allowedStatus = ['Available', 'Occupied', 'Maintenance', 'Reserved'];
                if (!in_array($newStatusText, $allowedStatus)) {
                    $responseMessage = "Invalid slot status selected.";
                    $responseType = 'error';
                    break;
                }

                if ($newStatusText === 'Occupied' || $newStatusText === 'Reserved') {
                    $newIsOccupied = 1;
                }

                // If changing to 'Available', ensure AssignedTo is NULL
                if ($newStatusText === 'Available') {
                    $sql = "UPDATE ParkingSlot SET Status = ?, IsOccupied = ?, AssignedTo = NULL WHERE SlotID = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sii", $newStatusText, $newIsOccupied, $slotId);
                } else {
                    $sql = "UPDATE ParkingSlot SET Status = ?, IsOccupied = ? WHERE SlotID = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sii", $newStatusText, $newIsOccupied, $slotId);
                }


                if ($stmt === false) {
                    $responseMessage = "Database prepare error: " . $conn->error;
                    $responseType = 'error';
                } else {
                    if ($stmt->execute()) {
                        $responseMessage = "Slot ID " . htmlspecialchars($slotId) . " status changed to " . htmlspecialchars($newStatusText) . " successfully.";
                        $responseType = 'success';
                    } else {
                        $responseMessage = "Error changing slot status: " . $stmt->error;
                        $responseType = 'error';
                    }
                    $stmt->close();
                }
            } else {
                $responseMessage = "Required data for changing slot status is missing.";
                $responseType = 'error';
            }
            break;
// ... (rest of the cases) ...
        default:
            $responseMessage = "Invalid action specified.";
            $responseType = 'error';
            break;
    }

    // Store message in session and redirect back to dashboard
    if ($responseType === 'success') {
        $_SESSION['admin_action_success'] = $responseMessage;
    } else {
        $_SESSION['admin_action_error'] = $responseMessage;
    }
    header("location: AdminDashboard.php");
    exit;

} else {
    // If accessed directly without POST or action
    $_SESSION['admin_action_error'] = "Invalid request.";
    header("location: AdminDashboard.php");
    exit;
}
?>