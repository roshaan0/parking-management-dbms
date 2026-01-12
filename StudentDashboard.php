<?php


session_start();
require __DIR__ . '/vendor/autoload.php';
// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || !isset($_SESSION['full_name'])) {
    header("location: Login.php");
    exit;
}

$loggedInUserName = $_SESSION['full_name'];
$loggedInUserRole = $_SESSION['role'];

// --- NEW CODE STARTS HERE ---
// Display registration messages
$message = '';
$messageType = '';
if (isset($_SESSION['registration_success'])) {
    $message = $_SESSION['registration_success'];
    $messageType = 'success';
    unset($_SESSION['registration_success']);
} elseif (isset($_SESSION['registration_error'])) {
    $message = $_SESSION['registration_error'];
    $messageType = 'error';
    unset($_SESSION['registration_error']);
}
elseif (isset($_SESSION['vehicle_action_success'])) {
    $message = $_SESSION['vehicle_action_success'];
    $messageType = 'success';
    unset($_SESSION['vehicle_action_success']);
} elseif (isset($_SESSION['vehicle_action_error'])) {
    $message = $_SESSION['vehicle_action_error'];
    $messageType = 'error';
    unset($_SESSION['vehicle_action_error']);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BU-CPMS - <?php echo ucfirst($loggedInUserRole); ?> Dashboard</title>
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --success: #27ae60;
            --danger: #e74c3c;
            --warning: #f39c12;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --gray: #95a5a6;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f8f9fa;
            color: #333;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background-color: var(--primary);
            color: white;
            padding: 20px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            position: fixed;
            height: 100%;
        }

        .sidebar .logo {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 30px;
            color: var(--secondary);
        }

        .sidebar .user-info {
            text-align: center;
            margin-bottom: 30px;
            padding: 0 15px;
        }

        .sidebar .user-info h3 {
            margin-top: 5px;
            font-size: 20px;
            color: var(--light);
        }

        .sidebar .user-info p {
            font-size: 14px;
            color: var(--gray);
        }

        .sidebar nav ul {
            list-style: none;
            width: 100%;
        }

        .sidebar nav ul li {
            width: 100%;
        }

        .sidebar nav ul li a {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: white;
            text-decoration: none;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .sidebar nav ul li a i {
            margin-right: 10px;
            font-size: 18px;
        }

        .sidebar nav ul li a:hover,
        .sidebar nav ul li a.active {
            background-color: #34495e;
            border-left: 5px solid var(--secondary);
        }

        /* Main Content Styles */
        .main-content {
            margin-left: 250px;
            flex-grow: 1;
            padding: 20px;
            background-color: #ecf0f1;
        }

        .header {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 24px;
            color: var(--primary);
        }

        .header .logout-btn {
            background-color: var(--danger);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .header .logout-btn:hover {
            background-color: #c0392b;
        }

        .widget-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .widget {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .widget h2 {
            font-size: 20px;
            color: var(--primary);
            margin-bottom: 15px;
            border-bottom: 2px solid var(--secondary);
            padding-bottom: 10px;
        }

        .widget p {
            font-size: 16px;
            color: #555;
            line-height: 1.6;
        }

        .slot-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
            gap: 10px;
        }

        .slot {
            background-color: var(--gray);
            color: white;
            padding: 15px 0;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .slot.available {
            background-color: var(--success);
        }

        .slot.occupied {
            background-color: var(--danger);
            cursor: not-allowed;
        }

        .slot.selected {
            background-color: var(--secondary);
            border: 2px solid #2980b9;
        }

        .slot.available:hover {
            background-color: #2ecc71;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 30px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            position: relative;
        }

        .close-button {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            position: absolute;
            top: 10px;
            right: 20px;
        }

        .close-button:hover,
        .close-button:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .modal-content h2 {
            margin-top: 0;
            color: var(--primary);
            margin-bottom: 20px;
            text-align: center;
        }

        .modal-content form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .modal-content label {
            font-weight: bold;
            color: #555;
        }

        .modal-content input[type="text"],
        .modal-content input[type="email"],
        .modal-content input[type="password"],
        .modal-content input[type="date"],
        .modal-content select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .modal-content button {
            background-color: var(--secondary);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            transition: background-color 0.3s ease;
        }

        .modal-content button:hover {
            background-color: #2980b9;
        }

        /* Table Styles */
        .table-responsive {
            overflow-x: auto;
            margin-top: 20px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        .data-table th {
            background-color: var(--primary);
            color: white;
            font-weight: bold;
        }

        .data-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .data-table tr:hover {
            background-color: #ddd;
        }

        .data-table .btn-secondary {
            background-color: var(--danger);
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .data-table .btn-secondary:hover {
            background-color: #c0392b;
        }

        /* File Upload */
        .file-upload-section {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .file-upload-section input[type="file"] {
            display: none;
        }

        .file-upload-section .btn {
            background-color: var(--secondary);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .file-upload-section .btn:hover {
            background-color: #2980b9;
        }
        /* Message styling */
        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <div class="logo">BU-CPMS</div>
            <div class="user-info">
                <p>Welcome,</p>
                <h3><?php echo htmlspecialchars($loggedInUserName); ?></h3>
                <p>(<?php echo htmlspecialchars($loggedInUserRole); ?>)</p>
            </div>
            <nav>
                <ul>
                    <li><a href="#" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="#live-slots"><i class="fas fa-parking"></i> Live Slots View</a></li>
                    <li><a href="#vehicle-registration"><i class="fas fa-car"></i> Vehicle Registration</a></li>
                    <li><a href="#registered-vehicles"><i class="fas fa-list"></i> Registered Vehicles</a></li>
                    <li><a href="#" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </div>

        <div class="main-content">
            <div class="header">
                <h1>Student Dashboard</h1>
                <button class="logout-btn">Logout</button>
            </div>

            <?php if ($message): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <div class="widget-container">
                <div id="live-slots" class="widget">
                    <h2>Live Parking Slots (Student Zone)</h2>
                    <div class="slot-grid">
                        <div class="slot available" data-slot-id="S101">S101</div>
                        <div class="slot occupied" data-slot-id="S102">S102</div>
                        <div class="slot available" data-slot-id="S103">S103</div>
                        <div class="slot available" data-slot-id="S104">S104</div>
                        <div class="slot occupied" data-slot-id="S105">S105</div>
                        <div class="slot available" data-slot-id="S106">S106</div>
                        <div class="slot available" data-slot-id="S107">S107</div>
                        <div class="slot occupied" data-slot-id="S108">S108</div>
                        <div class="slot available" data-slot-id="S109">S109</div>
                        <div class="slot available" data-slot-id="S110">S110</div>
                    </div>
                    <p style="margin-top: 15px;">Click on an available slot to request reservation.</p>
                </div>

                <div id="vehicle-registration" class="widget">
                    <h2>Register New Vehicle</h2>
                    <form action="register_vehicle.php" method="POST" id="vehicle-form">
                        <div class="form-group">
                            <label for="license-plate">License Plate Number:</label>
                            <input type="text" id="license-plate" name="license_plate" placeholder="e.g., ABC-123" required>
                        </div>
                        <div class="form-group">
                            <label for="vehicle-type">Vehicle Type:</label>
                            <select id="vehicle-type" name="vehicle_type" required>
                                <option value="">Select Type</option>
                                <option value="Car">Car</option>
                                <option value="Motorcycle">Motorcycle</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="sticker-number">Sticker Number (Optional):</label>
                            <input type="text" id="sticker-number" name="sticker_number" placeholder="e.g., BUCPMS-001">
                        </div>
                        <div class="form-group">
                            <input type="checkbox" id="is-default" name="is_default" value="1">
                            <label for="is-default">Set as default vehicle</label>
                        </div>
                        <button type="submit">Register Vehicle</button>
                    </form>
                </div>
            </div>

            <div id="registered-vehicles" class="widget">
                <h2>Your Registered Vehicles</h2>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>License Plate</th>
                                <th>Vehicle Type</th>
                                <th>Sticker Number</th>
                                <th>Default</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                           <?php
                            require_once 'db.php'; // Ensure $conn is available
                            $current_user_id = $_SESSION['user_id'];
                            $sql_vehicles = "SELECT VehicleID, LicensePlate, VehicleType, StickerNumber, IsDefault, RegistrationDate FROM Vehicle WHERE UserID = ? ORDER BY RegistrationDate DESC";
                            $stmt_vehicles = $conn->prepare($sql_vehicles);
                            if ($stmt_vehicles) {
                                $stmt_vehicles->bind_param("i", $current_user_id);
                                $stmt_vehicles->execute();
                                $result_vehicles = $stmt_vehicles->get_result();

                                if ($result_vehicles->num_rows > 0) {
                                    while ($vehicle = $result_vehicles->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($vehicle['LicensePlate']) . "</td>";
                                        echo "<td>" . htmlspecialchars($vehicle['VehicleType']) . "</td>";
                                        echo "<td>" . htmlspecialchars($vehicle['StickerNumber'] ? $vehicle['StickerNumber'] : 'N/A') . "</td>";
                                        echo "<td>" . ($vehicle['IsDefault'] ? 'Yes' : 'No') . "</td>";
                                        echo "<td>" . htmlspecialchars(date('M j, Y', strtotime($vehicle['RegistrationDate']))) . "</td>";
                                        echo "<td>
                                                <form action='remove_vehicle.php' method='POST' onsubmit='return confirm(\"Are you sure you want to remove vehicle " . htmlspecialchars($vehicle['LicensePlate']) . "?\");' style='display:inline;'>
                                                    <input type='hidden' name='vehicle_id' value='" . htmlspecialchars($vehicle['VehicleID']) . "'>
                                                    <button type='submit' class='btn-secondary vehicle-action'>Remove</button>
                                                </form>
                                              </td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6'>No vehicles registered yet.</td></tr>";
                                }
                                $stmt_vehicles->close();
                            } else {
                                 echo "<tr><td colspan='6'>Error fetching vehicles: ".$conn->error."</td></tr>";
                            }
                            // $conn->close(); // Close connection if db.php doesn't do it or if it's the end of script
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="file-upload-section" class="widget">
                <h2>Upload Documents</h2>
                <p>Upload necessary documents (e.g., student ID, vehicle registration documents).</p>
                <div class="file-upload-section">
                    <input type="file" id="document-upload" accept=".pdf, .doc, .docx, .jpg, .png">
                    <button id="file-upload" class="btn">Choose File</button>
                    <span id="file-name">No file chosen</span>
                    <button class="btn" onclick="alert('File upload functionality will be implemented here.')">Upload Selected File</button>
                </div>
            </div>
        </div>
    </div>

    <div id="reserveModal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h2>Confirm Reservation</h2>
            <p>You are about to reserve slot <strong id="selectedSlotNumber"></strong>.</p>
            <form id="reserve-form">
                <div class="form-group">
                    <label for="modal_slot_id_display">Slot Number:</label>
                    <input type="text" id="modal_slot_id_display" readonly>
                </div>
                <div class="form-group">
                    <label for="reserve-vehicle">Select Vehicle:</label>
                    <select id="reserve-vehicle" name="vehicle_id" required>
                        <option value="">-- Select Your Vehicle --</option>
                        <?php
                        // Fetch user's registered vehicles
                        // require_once 'db.php'; // if not already included
                        $stmt_user_vehicles = $conn->prepare("SELECT VehicleID, LicensePlate, VehicleType FROM Vehicle WHERE UserID = ?");
                        if ($stmt_user_vehicles) {
                            $stmt_user_vehicles->bind_param("i", $_SESSION['user_id']);
                            $stmt_user_vehicles->execute();
                            $result_user_vehicles = $stmt_user_vehicles->get_result();
                            while ($veh = $result_user_vehicles->fetch_assoc()) {
                                echo "<option value='" . htmlspecialchars($veh['VehicleID']) . "'>" . htmlspecialchars($veh['LicensePlate']) . " (" . htmlspecialchars($veh['VehicleType']) . ")</option>";
                            }
                            $stmt_user_vehicles->close();
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="reserve-date">Date:</label>
                    <input type="date" id="reserve-date" name="reserve_date" required>
                </div>
                <div class="form-group">
                    <label for="reserve-time">Time:</label>
                    <input type="time" id="reserve-time" name="reserve_time" required>
                </div>
                <button type="submit">Confirm Reservation</button>
            </form>
            <div id="reservation-message" style="margin-top:10px; text-align:center;"></div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const logoutButtons = document.querySelectorAll('.logout-btn');
            logoutButtons.forEach(button => {
                button.addEventListener('click', function() {
                    if (confirm('Are you sure you want to logout?')) {
                        window.location.href = 'logout.php';
                    }
                });
            });

            // Handle slot selection and modal display
            const reserveModal = document.getElementById('reserveModal');
            const closeButton = document.querySelector('.close-button');
            const selectedSlotNumberSpan = document.getElementById('selectedSlotNumber');
            let currentSelectedSlot = null;

            document.querySelectorAll('.slot.available').forEach(slot => {
                slot.addEventListener('click', function() {
                    // Remove 'selected' from all slots first
                    document.querySelectorAll('.slot').forEach(s => s.classList.remove('selected'));
                    
                    // Add 'selected' to the clicked slot
                    this.classList.add('selected');
                    currentSelectedSlot = this; // Store the currently selected slot element

                    // Display modal
                    selectedSlotNumberSpan.textContent = this.dataset.slotId;
                    reserveModal.style.display = 'flex'; // Use flex to center
                });
            });

            closeButton.addEventListener('click', function() {
                reserveModal.style.display = 'none';
                if (currentSelectedSlot) {
                    currentSelectedSlot.classList.remove('selected'); // Deselect the slot when modal closes
                }
            });

            // Close modal when clicking outside
            window.addEventListener('click', function(e) {
                if (e.target === reserveModal) {
                    reserveModal.style.display = 'none';
                    if (currentSelectedSlot) {
                        currentSelectedSlot.classList.remove('selected'); // Deselect the slot when modal closes
                    }
                }
            });

            // Handle file upload button click
            document.getElementById('file-upload').addEventListener('click', function() {
                document.getElementById('document-upload').click();
            });

            // Display selected file name
            document.getElementById('document-upload').addEventListener('change', function() {
                const fileNameSpan = document.getElementById('file-name');
                if (this.files.length > 0) {
                    fileNameSpan.textContent = this.files[0].name;
                } else {
                    fileNameSpan.textContent = 'No file chosen';
                }
            });

            // Placeholder for vehicle form submission (actual submission handled by register_vehicle.php)
            document.getElementById('vehicle-form').addEventListener('submit', function(e) {
                // The form will submit to register_vehicle.php, so no AJAX here.
                // The PHP script will redirect back with success/error messages.
            });

            // Handle remove vehicle button click
            document.querySelectorAll('.vehicle-action').forEach(action => {
                action.addEventListener('click', function() {
                    if (confirm('Are you sure you want to remove this vehicle?')) {
                        // In a real application, you would send an AJAX request here
                        // to delete the vehicle from the backend database.
                        // For now, we just remove the row from the table.
                        this.closest('tr').remove();
                        alert('Vehicle removed (client-side only).'); // Indicate it's client-side
                    }
                });
            });

           // Handle reservation form submission
            document.getElementById('reserve-form').addEventListener('submit', function(e) {
                e.preventDefault();
                const reservationMessageDiv = document.getElementById('reservation-message');
                reservationMessageDiv.textContent = ''; // Clear previous messages

                if (currentSelectedSlot) {
                    const slotId = currentSelectedSlot.dataset.slotId;
                    const reserveDate = document.getElementById('reserve-date').value;
                    const reserveTime = document.getElementById('reserve-time').value;
                    const vehicleId = document.getElementById('reserve-vehicle').value;

                    if (!vehicleId) {
                        reservationMessageDiv.textContent = 'Please select a vehicle.';
                        reservationMessageDiv.style.color = 'red';
                        return;
                    }

                    const formData = new FormData();
                    formData.append('slot_id', slotId);
                    formData.append('reserve_date', reserveDate);
                    formData.append('reserve_time', reserveTime);
                    formData.append('vehicle_id', vehicleId);

                    fetch('reserve_slot.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            reservationMessageDiv.textContent = data.message;
                            reservationMessageDiv.style.color = 'green';
                            // currentSelectedSlot.classList.remove('available', 'selected');
                            // currentSelectedSlot.classList.add('occupied'); // Or 'reserved' based on your flow
                            // currentSelectedSlot.style.cursor = 'not-allowed';
                            // setTimeout(() => { // Optionally close modal after delay
                            //     reserveModal.style.display = 'none';
                            //     if(currentSelectedSlot) currentSelectedSlot.classList.remove('selected');
                            //     currentSelectedSlot = null;
                            //     window.location.reload(); // Refresh to see updated slot status & reservations
                            // }, 2000);
                             alert(data.message); // Simple alert for now
                             window.location.reload();


                        } else {
                            reservationMessageDiv.textContent = 'Error: ' + data.message;
                            reservationMessageDiv.style.color = 'red';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        reservationMessageDiv.textContent = 'An unexpected error occurred.';
                        reservationMessageDiv.style.color = 'red';
                    });
                } else {
                    reservationMessageDiv.textContent = 'No slot selected!';
                    reservationMessageDiv.style.color = 'red';
                }
            });
            // Update modal display to show slot ID
            document.querySelectorAll('.slot.available').forEach(slot => {
                slot.addEventListener('click', function() {
                    // ... (existing code to add 'selected' class) ...
                    document.getElementById('modal_slot_id_display').value = this.dataset.slotId; // Update display input
                    // ... (existing code to show modal) ...
                });
            });
        });
    </script>
</body>
</html>