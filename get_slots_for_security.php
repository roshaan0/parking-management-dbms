<?php
session_start();
require __DIR__ . '/vendor/autoload.php';
require_once 'db.php'; // Ensure db.php is in the same directory or adjust path

header('Content-Type: application/json');

// Check if the user is logged in and is a Security officer, if not then return an error
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Security') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit;
}

$response = ['status' => 'error', 'message' => 'An unknown error occurred.', 'data' => ['slots' => []]];

try {
    // In get_slots_for_security.php

$lotName = $_GET['lot_name'] ?? null; // Get the lot_name from the GET request

// Add this line to log the received lot name for debugging
error_log("get_slots_for_security.php received lot_name: " . ($lotName ?? 'NULL'));

$zoneMapping = [
    "Faculty Lot" => "A",
    "Disabled Lot" => "D",
    "Student Lot" => "B",
    "Visitor Lot" => "C",
];

$dbZone = null;
if ($lotName && isset($zoneMapping[$lotName])) {
    $dbZone = $zoneMapping[$lotName];
    $sql_conditions[] = "ps.Zone = ?";
    $sql_types .= 's';
    $sql_params[] = $dbZone;
    // Log the mapped zone
    error_log("Mapped lot_name '{$lotName}' to dbZone '{$dbZone}'");
} else {
    error_log("Lot name '{$lotName}' not found in mapping or is NULL. No specific zone filter applied.");
}

// ... (rest of your SQL query and fetching logic)


    $sql = "
        SELECT
            ps.SlotID,
            ps.SlotNumber,
            ps.SlotType,
            ps.Status AS SlotStatus,
            ps.IsOccupied,
            ps.AssignedTo,
            ps.Zone,
            r.ReservationID,
            r.UserID AS ReservedUserID,
            r.VehicleID AS ReservedVehicleID,
            r.ReservationStartTime,
            r.ReservationEndTime,
            r.Status AS ReservationStatus,
            u.Name AS ReservedUserName, 
            v.LicensePlate AS ReservedVehiclePlate
        FROM
            ParkingSlot ps
        LEFT JOIN
            Reservation r ON ps.SlotID = r.SlotID AND r.Status = 'Active' AND NOW() BETWEEN r.ReservationStartTime AND r.ReservationEndTime
        LEFT JOIN
            User u ON r.UserID = u.UserID -- <--- CHANGED FROM 'Users' to 'User'
        LEFT JOIN
            Vehicle v ON r.VehicleID = v.VehicleID
    ";

    if (!empty($sql_conditions)) {
        $sql .= " WHERE " . implode(" AND ", $sql_conditions);
    }

    $sql .= " ORDER BY ps.SlotNumber ASC";

    $stmt = $conn->prepare($sql);

    if (!empty($sql_params)) {
        $stmt->bind_param($sql_types, ...$sql_params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $slots = [];
    while ($row = $result->fetch_assoc()) {
        // Determine the actual status and details for display in the frontend
        $displayStatus = $row['SlotStatus'];
        $displayDetails = '';

        if ($row['IsOccupied'] == 1) {
            $displayStatus = 'Occupied';
            $displayDetails = ''; // You might fetch current vehicle details if available
        } elseif ($row['ReservationID'] && $row['ReservationStatus'] === 'Active') {
            $displayStatus = 'Reserved';
            $reservedFor = $row['ReservedUserName'] ?? 'Unknown User';
            $reservedVehicle = $row['ReservedVehiclePlate'] ?? 'Unknown Vehicle';
            $startTime = new DateTime($row['ReservationStartTime']);
            $endTime = new DateTime($row['ReservationEndTime']);
            $displayDetails = "For {$reservedFor} ({$startTime->format('g:i A')} - {$endTime->format('g:i A')})";
        } else {
            $displayStatus = 'Available';
        }

        $slots[] = [
            'slot_id' => $row['SlotID'],
            'slot_number' => $row['SlotNumber'],
            'slot_type' => $row['SlotType'],
            'status' => $displayStatus, // This will be used for CSS classes
            'is_occupied' => $row['IsOccupied'],
            'zone' => $row['Zone'],
            'details' => $displayDetails, // This will be displayed under slot status
            'reservation_id' => $row['ReservationID'],
            'reserved_user_id' => $row['ReservedUserID'],
            'reserved_vehicle_id' => $row['ReservedVehicleID'],
            'reservation_start_time' => $row['ReservationStartTime'],
            'reservation_end_time' => $row['ReservationEndTime'],
        ];
    }

    $stmt->close();
    $conn->close();

    $response = ['status' => 'success', 'message' => 'Slot data fetched successfully.', 'slots' => $slots];

} catch (Exception $e) {
    $response['message'] = 'Database error: ' . $e->getMessage();
    // In case of error, still ensure 'slots' key exists to prevent JS errors
    $response['slots'] = [];
}

echo json_encode($response);
?>