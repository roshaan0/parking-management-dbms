<?php


session_start();
require __DIR__ . '/vendor/autoload.php';



// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || !isset($_SESSION['full_name'])) {
    header("location: Login.php");
    exit;
}

// You can now access user information like:
$loggedInUserName = $_SESSION['full_name'];
$loggedInUserRole = $_SESSION['role'];


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
    </head>
<body>
    <div class="sidebar">
        <div class="logo">BU-CPMS</div>
        <div class="user-info">
            <p>Welcome,</p>
            <h3><?php echo htmlspecialchars($loggedInUserName); ?></h3>
            <p>(<?php echo htmlspecialchars($loggedInUserRole); ?>)</p>
        </div>
        </div>
    </body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BU-CPMS - Faculty Dashboard</title>
    <style>
        <style>
    /* ... existing CSS ... */

    /* NEW CSS for messages */
    .message-container {
        padding: 10px;
        margin-bottom: 20px;
        border-radius: 5px;
        font-weight: bold;
        text-align: center; /* Added for better centering */
    }
    .message-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    .message-error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
</style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --success: #27ae60;
            --danger: #e74c3c;
            --warning: #f39c12;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --gray: #95a5a6;
            --faculty-accent: #8e44ad;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f6fa;
            color: #2c3e50;
            overflow-x: hidden;
        }
        
        .dashboard {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar - identical to student dashboard */
        .sidebar {
            width: 250px;
            background-color: var(--primary);
            color: white;
            padding: 20px 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        
        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }
        
        .system-title {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .system-subtitle {
            font-size: 12px;
            color: var(--gray);
            margin-bottom: 20px;
        }
        
        .profile {
            text-align: center;
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .profile-img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
            border: 3px solid var(--secondary); /* Same blue border as student */
        }
        
        .profile-name {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .profile-role {
            font-size: 14px;
            color: var(--gray);
            background-color: rgba(0,0,0,0.2);
            padding: 3px 10px;
            border-radius: 20px;
            display: inline-block;
        }
        
        .nav-menu {
            margin-top: 20px;
        }
        
        .nav-item {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }
        
        .nav-item:hover, .nav-item.active {
            background-color: rgba(255,255,255,0.1);
            border-left: 4px solid var(--faculty-accent); /* Purple accent for faculty */
        }
        
        .nav-item i {
            margin-right: 10px;
            font-size: 18px;
            width: 24px;
            text-align: center;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 250px;
            flex: 1;
            padding: 30px;
            max-width: calc(100vw - 250px);
            overflow-x: hidden;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .page-title {
            font-size: 24px;
            font-weight: 600;
        }
        
        .user-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .notification-btn, .logout-btn {
            background: none;
            border: none;
            font-size: 20px;
            color: var(--dark);
            cursor: pointer;
            position: relative;
        }
        
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--danger);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Cards */
        .cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            padding: 20px;
            transition: transform 0.3s;
            height: 100%;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .card-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--dark);
        }
        
        .card-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        
        .card-icon.parking {
            background-color: var(--faculty-accent); /* Purple for faculty */
        }
        
        .card-icon.vehicle {
            background-color: var(--success);
        }
        
        .card-icon.time {
            background-color: var(--warning);
        }
        
        .card-value {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .card-footer {
            font-size: 14px;
            color: var(--gray);
            display: flex;
            align-items: center;
        }
        
        /* Reserved Parking */
        .reserved-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 20px;
        }
        
        .reserved-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            padding: 20px;
            border-left: 4px solid var(--faculty-accent);
        }
        
        .reserved-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        
        .reserved-title {
            font-size: 18px;
            font-weight: 600;
        }
        
        .reserved-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            background-color: #d5f5e3;
            color: #27ae60;
        }
        
        .reserved-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .detail-label {
            font-size: 14px;
            color: var(--gray);
            margin-bottom: 5px;
        }
        
        .detail-value {
            font-weight: 600;
        }
        
        /* Forms */
        .form-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            padding: 25px;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }
        
        .form-control:focus {
            border-color: var(--faculty-accent);
            outline: none;
        }
        
        .btn {
            padding: 12px 20px;
            border-radius: 5px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            font-size: 16px;
        }
        
        .btn-primary {
            background-color: var(--faculty-accent);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #7d3c98;
        }
        
        .btn-secondary {
            background-color: #ecf0f1;
            color: var(--dark);
        }
        
        .btn-secondary:hover {
            background-color: #d5dbdb;
        }
        
        /* Tables */
        .table-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            padding: 20px;
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        
        /* History */
        .history-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            padding: 20px;
        }
        
        .history-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .history-item:last-child {
            border-bottom: none;
        }
        
        .history-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .history-date {
            font-weight: 600;
            color: var(--faculty-accent);
        }
        
        .history-status {
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            background-color: #d5f5e3;
            color: #27ae60;
        }
        
        .history-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            font-size: 14px;
        }
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .modal-content {
            background-color: white;
            border-radius: 10px;
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            overflow-y: auto;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .modal-title {
            font-size: 20px;
            font-weight: 600;
        }
        
        .close-modal {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--gray);
        }
        
        .slots-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 15px;
            margin: 25px 0;
        }
        
        .slot {
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .slot.available {
            background-color: #d5f5e3;
            border: 1px solid #2ecc71;
        }
        
        .slot.available:hover {
            background-color: #abebc6;
        }
        
        .slot.occupied {
            background-color: #fadbd8;
            border: 1px solid #e74c3c;
            cursor: not-allowed;
        }
        
        .slot.selected {
            background-color: var(--faculty-accent);
            color: white;
            border-color: var(--faculty-accent);
        }
        
        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 20px;
        }
        
        /* Tab Content */
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .dashboard {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                position: relative;
                height: auto;
            }
            
            .main-content {
                margin-left: 0;
                max-width: 100%;
                padding: 20px;
            }
            
            .cards-container, .reserved-container {
                grid-template-columns: 1fr;
            }
            
            .history-details {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar with identical BU-CPMS header and profile as student dashboard -->
        <div class="sidebar">
            <div class="sidebar-header">
                <div class="system-title">BU-CPMS</div>
                <div class="system-subtitle">Bahria University Campus Parking Management System</div>
            </div>
            
            <div class="profile">
                <img src="profile.jpg" alt="Faculty" class="profile-img">
                <div class="profile-name">Ali Ahmed</div>
                <div class="profile-role">Faculty</div> <!-- Only changed "Student" to "Faculty" -->
            </div>
            
            <div class="nav-menu">
                <div class="nav-item active" data-tab="dashboard">
                    <i class="icon">📊</i> Dashboard
                </div>
                <div class="nav-item" data-tab="reserved-lots">
                    <i class="icon">🅿️</i> Reserved Parking
                </div>
                <div class="nav-item" data-tab="registered-vehicles">
                    <i class="icon">🚗</i> Registered Vehicles
                </div>
                <div class="nav-item" data-tab="register-vehicle">
                    <i class="icon">➕</i> Register Vehicle
                </div>
                <div class="nav-item" data-tab="request-change">
                    <i class="icon">🔄</i> Request Change
                </div>
                <div class="nav-item" data-tab="parking-history">
                    <i class="icon">📅</i> Parking History
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
             <?php if ($message): ?>
        <div class="message-container message-<?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <section id="live-slots" class="dashboard-section">
        <h2>Live Parking Slots</h2>
            <div class="header">
                <div class="page-title">Faculty Dashboard</div>
                <div class="user-actions">
                    <button class="notification-btn">
                        <i class="icon">🔔</i>
                        <span class="notification-badge">2</span>
                    </button>
                    <button class="logout-btn">
                        <i class="icon">🚪</i>
                    </button>
                </div>
            </div>
            
            <!-- Dashboard Tab -->
            <div id="dashboard" class="tab-content active">
                <div class="cards-container">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Assigned Parking</div>
                            <div class="card-icon parking">
                                <i class="icon">🅿️</i>
                            </div>
                        </div>
                        <div class="card-value">F-12</div>
                        <div class="card-footer">
                            <i class="icon">⏱️</i> Mon-Fri, 8AM-5PM
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Registered Vehicles</div>
                            <div class="card-icon vehicle">
                                <i class="icon">🚗</i>
                            </div>
                        </div>
                        <div class="card-value">2</div>
                        <div class="card-footer">
                            <i class="icon">ℹ️</i> Last added 3 weeks ago
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Days Reserved</div>
                            <div class="card-icon time">
                                <i class="icon">📅</i>
                            </div>
                        </div>
                        <div class="card-value">5</div>
                        <div class="card-footer">
                            <i class="icon">ℹ️</i> Per week
                        </div>
                    </div>
                </div>
                
                <div class="reserved-card">
                    <div class="reserved-header">
                        <div class="reserved-title">Current Parking Assignment</div>
                        <div class="reserved-status">Active</div>
                    </div>
                    <div class="reserved-details">
                        <div>
                            <div class="detail-label">Parking Lot</div>
                            <div class="detail-value">Faculty Lot A</div>
                        </div>
                        <div>
                            <div class="detail-label">Assigned Slot</div>
                            <div class="detail-value">F-12</div>
                        </div>
                        <div>
                            <div class="detail-label">Schedule</div>
                            <div class="detail-value">Monday to Friday</div>
                        </div>
                        <div>
                            <div class="detail-label">Time Slot</div>
                            <div class="detail-value">8:00 AM - 5:00 PM</div>
                        </div>
                    </div>
                    <button class="btn btn-primary" id="modify-reservation" style="margin-top: 15px;">
                        Modify Reservation
                    </button>
                </div>
            </div>
            
            <!-- Reserved Parking Lots Tab -->
            <div id="reserved-lots" class="tab-content">
                <h2 style="margin-bottom: 20px;">Your Reserved Parking</h2>
                
                <div class="reserved-container">
                    <div class="reserved-card">
                        <div class="reserved-header">
                            <div class="reserved-title">Faculty Lot A - F-12</div>
                            <div class="reserved-status">Permanent</div>
                        </div>
                        <div class="reserved-details">
                            <div>
                                <div class="detail-label">Days</div>
                                <div class="detail-value">Monday to Friday</div>
                            </div>
                            <div>
                                <div class="detail-label">Time Slot</div>
                                <div class="detail-value">8:00 AM - 5:00 PM</div>
                            </div>
                            <div>
                                <div class="detail-label">Vehicle</div>
                                <div class="detail-value">LE-1234 (Default)</div>
                            </div>
                            <div>
                                <div class="detail-label">Since</div>
                                <div class="detail-value">January 15, 2023</div>
                            </div>
                        </div>
                        <div style="display: flex; gap: 10px; margin-top: 15px;">
                            <button class="btn btn-primary modify-btn" data-lot="faculty-a">
                                Modify
                            </button>
                            <button class="btn btn-secondary">
                                Change Vehicle
                            </button>
                        </div>
                    </div>
                    
                    <div class="reserved-card">
                        <div class="reserved-header">
                            <div class="reserved-title">North Campus Lot - N-08</div>
                            <div class="reserved-status">Temporary</div>
                        </div>
                        <div class="reserved-details">
                            <div>
                                <div class="detail-label">Date</div>
                                <div class="detail-value">June 15, 2023</div>
                            </div>
                            <div>
                                <div class="detail-label">Time Slot</div>
                                <div class="detail-value">9:00 AM - 3:00 PM</div>
                            </div>
                            <div>
                                <div class="detail-label">Vehicle</div>
                                <div class="detail-value">KHI-5678</div>
                            </div>
                            <div>
                                <div class="detail-label">Purpose</div>
                                <div class="detail-value">Guest Lecture</div>
                            </div>
                        </div>
                        <div style="display: flex; gap: 10px; margin-top: 15px;">
                            <button class="btn btn-primary modify-btn" data-lot="north">
                                Modify
                            </button>
                            <button class="btn btn-secondary">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Registered Vehicles Tab -->
            <div id="registered-vehicles" class="tab-content">
                <h2 style="margin-bottom: 20px;">Registered Vehicles</h2>
                
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Vehicle Type</th>
                                <th>Plate Number</th>
                                <th>Color</th>
                                <th>Registered On</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                             <?php
                            // Ensure db.php is included if not already, and session started
                            // require_once 'db.php'; // If not already included at the top
                            $current_user_id_faculty = $_SESSION['user_id']; // Assuming user_id is in session

                            $sql_faculty_vehicles = "SELECT VehicleID, LicensePlate, VehicleType, StickerNumber, IsDefault, RegistrationDate FROM Vehicle WHERE UserID = ? ORDER BY RegistrationDate DESC";
                            $stmt_faculty_vehicles = $conn->prepare($sql_faculty_vehicles);

                            if ($stmt_faculty_vehicles) {
                                $stmt_faculty_vehicles->bind_param("i", $current_user_id_faculty);
                                $stmt_faculty_vehicles->execute();
                                $result_faculty_vehicles = $stmt_faculty_vehicles->get_result();

                                if ($result_faculty_vehicles->num_rows > 0) {
                                    while ($vehicle_row = $result_faculty_vehicles->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($vehicle_row['VehicleID']) . "</td>";
                                        echo "<td>" . htmlspecialchars($vehicle_row['LicensePlate']) . "</td>";
                                        echo "<td>" . htmlspecialchars($vehicle_row['VehicleType']) . "</td>";
                                        echo "<td>" . htmlspecialchars($vehicle_row['StickerNumber'] ?: 'N/A') . "</td>";
                                        echo "<td>" . ($vehicle_row['IsDefault'] ? 'Yes' : 'No') . "</td>";
                                        echo "<td>" . htmlspecialchars(date('M j, Y', strtotime($vehicle_row['RegistrationDate']))) . "</td>";
                                        echo "<td>
                                                <form action='remove_vehicle.php' method='POST' onsubmit='return confirm(\"Are you sure you want to remove vehicle " . htmlspecialchars($vehicle_row['LicensePlate']) . "?\");' style='display:inline;'>
                                                    <input type='hidden' name='vehicle_id' value='" . htmlspecialchars($vehicle_row['VehicleID']) . "'>
                                                    <button type='submit' class='btn btn-secondary' style='padding: 5px 10px; font-size: 14px;'>Remove</button>
                                                </form>
                                              </td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='7'>No vehicles registered for this user.</td></tr>";
                                }
                                $stmt_faculty_vehicles->close();
                            } else {
                                echo "<tr><td colspan='7'>Error fetching vehicles: " . $conn->error . "</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Register New Vehicle Tab -->
            <div id="register-vehicle" class="tab-content">
                <h2 style="margin-bottom: 20px;">Register New Vehicle</h2>
                
                <div class="form-container">
                    <form id="vehicle-registration" action="register_vehicle.php" method="POST">
                        <div class="form-group">
                            <label class="form-label" for="vehicle-type">Vehicle Type</label>
                            <select class="form-control" id="vehicle-type" required>
                                <option value="">Select vehicle type</option>
                                <option value="sedan">Sedan</option>
                                <option value="suv">SUV</option>
                                <option value="hatchback">Hatchback</option>
                                <option value="pickup">Pickup Truck</option>
                                <option value="motorcycle">Motorcycle</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="plate-number">Plate Number</label>
                            <input type="text" class="form-control" id="plate-number" placeholder="e.g. LE-1234" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="vehicle-color">Color</label>
                            <input type="text" class="form-control" id="vehicle-color" placeholder="e.g. White" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Ownership Document</label>
                            <div style="border: 2px dashed #ddd; border-radius: 5px; padding: 20px; text-align: center; cursor: pointer;">
                                <i class="icon" style="font-size: 24px;">📄</i>
                                <div>Click to upload document</div>
                                <input type="file" style="display: none;">
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            Register Vehicle
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Request Slot Change Tab -->
            <div id="request-change" class="tab-content">
                <h2 style="margin-bottom: 20px;">Request Slot Change</h2>
                
                <div class="form-container">
                    <div id="request-change" class="tab-content">
                <h2 style="margin-bottom: 20px;">Request Slot Change</h2>
                <?php
                if (isset($_SESSION['faculty_action_success'])) {
                    echo '<div class="message-container message-success">' . htmlspecialchars($_SESSION['faculty_action_success']) . '</div>';
                    unset($_SESSION['faculty_action_success']);
                }
                if (isset($_SESSION['faculty_action_error'])) {
                    echo '<div class="message-container message-error">' . htmlspecialchars($_SESSION['faculty_action_error']) . '</div>';
                    unset($_SESSION['faculty_action_error']);
                }
                ?>
                <div class="form-container">
                    <form action="submit_slot_change_request.php" method="POST">
                        <div class="form-group">
                            <label class="form-label" for="change-reason">Reason for Change</label>
                            <select class="form-control" id="change-reason" name="change_reason" required>
                                <option value="">Select reason</option>
                                <option value="schedule_conflict">Schedule Conflict</option>
                                <option value="location_preference">Better Location Preference</option>
                                <option value="vehicle_change">Vehicle Change Accommodation</option>
                                <option value="accessibility">Accessibility Needs</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="preferred_slot_id">Preferred Slot (ID)</label>
                             <select class="form-control" id="preferred_slot_id" name="preferred_slot_id" required>
                                <option value="">-- Select Preferred Slot --</option>
                                <?php
                                // Populate with available/suitable slots
                                // require_once 'db.php'; // if not already included
                                $sql_avail_slots = "SELECT SlotID, SlotNumber, Zone, SlotType FROM ParkingSlot WHERE Status = 'Available' OR AssignedTo IS NULL ORDER BY Zone, SlotNumber";
                                $res_avail_slots = $conn->query($sql_avail_slots);
                                if($res_avail_slots && $res_avail_slots->num_rows > 0){
                                    while($slot = $res_avail_slots->fetch_assoc()){
                                        echo "<option value='".htmlspecialchars($slot['SlotID'])."'>".htmlspecialchars($slot['Zone'] . ' - ' . $slot['SlotNumber'] . ' (' . $slot['SlotType']) .")</option>";
                                    }
                                }
                                ?>
                            </select>
                            <small>Choose from currently available slots. Your current slot will be freed if approved.</small>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="effective-date">Preferred Effective Date</label>
                            <input type="date" class="form-control" id="effective-date" name="effective_date" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="additional-notes">Additional Notes (Optional)</label>
                            <textarea class="form-control" id="additional-notes" name="additional_notes" rows="3" placeholder="Any additional information..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            Submit Change Request
                        </button>
                    </form>
                </div>
            </div>
                </div>
            </div>
            
            <!-- Parking History Tab -->
            <div id="parking-history" class="tab-content">
                <h2 style="margin-bottom: 20px;">Parking History</h2>
                
                <div class="history-container">
                    <div class="history-item">
                        <div class="history-header">
                            <div class="history-date">May 25, 2023</div>
                            <div class="history-status">Completed</div>
                        </div>
                        <div class="history-details">
                            <div>
                                <div>Parking Lot: Faculty Lot A</div>
                                <div>Slot: F-12</div>
                            </div>
                            <div>
                                <div>Vehicle: LE-1234</div>
                                <div>Type: Sedan</div>
                            </div>
                            <div>
                                <div>Time: 8:00 AM - 5:00 PM</div>
                                <div>Duration: 9 hours</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="history-item">
                        <div class="history-header">
                            <div class="history-date">May 24, 2023</div>
                            <div class="history-status">Completed</div>
                        </div>
                        <div class="history-details">
                            <div>
                                <div>Parking Lot: Faculty Lot A</div>
                                <div>Slot: F-12</div>
                            </div>
                            <div>
                                <div>Vehicle: LE-1234</div>
                                <div>Type: Sedan</div>
                            </div>
                            <div>
                                <div>Time: 8:00 AM - 5:00 PM</div>
                                <div>Duration: 9 hours</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reservation Modal -->
    <div class="modal" id="reservation-modal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Available Slots in <span id="modal-lot-name">Faculty Lot A</span></div>
                <button class="close-modal">&times;</button>
            </div>
            
            <div class="slots-grid">
                <div class="slot available">
                    <div class="slot-number">F-11</div>
                    <div class="slot-status">Available</div>
                </div>
                <div class="slot occupied">
                    <div class="slot-number">F-12</div>
                    <div class="slot-status">Your Current</div>
                </div>
                <div class="slot available">
                    <div class="slot-number">F-13</div>
                    <div class="slot-status">Available</div>
                </div>
                <div class="slot occupied">
                    <div class="slot-number">F-14</div>
                    <div class="slot-status">Occupied</div>
                </div>
                <div class="slot available">
                    <div class="slot-number">F-15</div>
                    <div class="slot-status">Available</div>
                </div>
                <div class="slot available">
                    <div class="slot-number">F-16</div>
                    <div class="slot-status">Available</div>
                </div>
            </div>
            
            <div class="modal-actions">
                <button class="btn btn-secondary close-modal">Cancel</button>
                <button class="btn btn-primary" id="confirm-reservation">Confirm Reservation</button>
            </div>
        </div>
    </div>

    <script>
        // Tab Navigation
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', function() {
                // Remove active class from all nav items and tabs
                document.querySelectorAll('.nav-item').forEach(nav => nav.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
                
                // Add active class to clicked nav item and corresponding tab
                this.classList.add('active');
                const tabId = this.getAttribute('data-tab');
                document.getElementById(tabId).classList.add('active');
            });
        });

        // Modal Functionality
        const modal = document.getElementById('reservation-modal');
        const openModalButtons = document.querySelectorAll('#modify-reservation, .modify-btn');
        const closeModalButton = document.querySelector('.close-modal');
        const confirmReservationButton = document.getElementById('confirm-reservation');
        
        openModalButtons.forEach(button => {
            button.addEventListener('click', function() {
                const lotName = this.getAttribute('data-lot') || 'faculty-a';
                document.getElementById('modal-lot-name').textContent = 
                    lotName === 'faculty-a' ? 'Faculty Lot A' : 'North Campus Lot';
                modal.style.display = 'flex';
            });
        });
        
        closeModalButton.addEventListener('click', function() {
            modal.style.display = 'none';
        });
        
        window.addEventListener('click', function(event) {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
        
        // Slot Selection
        document.querySelectorAll('.slot.available').forEach(slot => {
            slot.addEventListener('click', function() {
                document.querySelectorAll('.slot').forEach(s => s.classList.remove('selected'));
                this.classList.add('selected');
            });
        });
        
        confirmReservationButton.addEventListener('click', function() {
            const selectedSlot = document.querySelector('.slot.selected');
            if (selectedSlot) {
                alert(`Reservation confirmed for slot ${selectedSlot.querySelector('.slot-number').textContent}`);
                modal.style.display = 'none';
            } else {
                alert('Please select a slot first');
            }
        });
        
        // Vehicle Registration Form
        document.getElementById('vehicle-registration').addEventListener('submit', function(e) {
            this.reset();
        });
        
        // Slot Change Request Form
        document.querySelector('#request-change form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Slot change request submitted for approval');
            this.reset();
        });
        
        // Remove Vehicle Buttons
        document.querySelectorAll('#registered-vehicles .btn-secondary').forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Are you sure you want to remove this vehicle?')) {
                    this.closest('tr').remove();
                }
            });
        });
        
        // Logout Button
        document.querySelector('.logout-btn').addEventListener('click', function() {
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = 'Login.php';
            }
        });
    </script>
</body>
</html>