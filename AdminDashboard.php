<?php


session_start();
require __DIR__ . '/vendor/autoload.php';
// Include your database connection
require_once 'db.php'; // Ensure db.php is in the same directory or adjust path

// Check if the user is logged in and is an Admin, if not then redirect to login page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin' || !isset($_SESSION['full_name'])) {
    header("location: Login.php");
    exit;
}

// You can now access user information like:
$loggedInUserName = $_SESSION['full_name'];
$loggedInUserRole = $_SESSION['role'];

// --- NEW: Display registration/admin action messages (if any) ---
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
} elseif (isset($_SESSION['admin_action_success'])) {
    $message = $_SESSION['admin_action_success'];
    $messageType = 'success';
    unset($_SESSION['admin_action_success']);
} elseif (isset($_SESSION['admin_action_error'])) {
    $message = $_SESSION['admin_action_error'];
    $messageType = 'error';
    unset($_SESSION['admin_action_error']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BU-CPMS - <?php echo ucfirst($loggedInUserRole); ?> Dashboard</title>
    <style>
        /* CSS from AdminDashboard.html */
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --success: #27ae60;
            --danger: #e74c3c;
            --warning: #f39c12;
            --info: #9b59b6;
            --light: #ecf0f1;
            --dark: #34495e;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            display: flex;
            min-height: 100vh;
            background-color: var(--light);
            color: var(--dark);
        }

        .sidebar {
            width: 250px;
            background-color: var(--primary);
            color: white;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .logo {
            font-size: 2em;
            font-weight: bold;
            margin-bottom: 30px;
            color: var(--secondary);
        }

        .user-info {
            text-align: center;
            margin-bottom: 30px;
        }

        .user-info h3 {
            margin: 5px 0;
            color: var(--light);
        }

        .user-info p {
            font-size: 0.9em;
            color: #bdc3c7;
        }

        .nav-links {
            list-style: none;
            padding: 0;
            width: 100%;
        }

        .nav-links li {
            width: 100%;
            margin-bottom: 10px;
        }

        .nav-links a, .logout-btn {
            display: block;
            color: white;
            text-decoration: none;
            padding: 12px 15px;
            border-radius: 8px;
            transition: background-color 0.3s ease;
            text-align: center;
            background-color: transparent;
            border: none;
            cursor: pointer;
            width: 100%;
            font-size: 1em;
        }

        .nav-links a:hover, .logout-btn:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .main-content {
            flex-grow: 1;
            padding: 30px;
            background-color: var(--light);
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        header h1 {
            color: var(--primary);
            margin: 0;
        }

        .card-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .card {
            background-color: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card h3 {
            color: var(--secondary);
            margin-bottom: 10px;
        }

        .card p {
            font-size: 1.8em;
            font-weight: bold;
            color: var(--primary);
        }

        .section {
            background-color: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .section h2 {
            color: var(--primary);
            margin-top: 0;
            margin-bottom: 20px;
            border-bottom: 2px solid var(--light);
            padding-bottom: 10px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: var(--dark);
        }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"],
        .form-group input[type="number"],
        .form-group input[type="date"],
        .form-group select {
            width: calc(100% - 20px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
        }

        .form-group input[type="checkbox"] {
            margin-right: 10px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            background-color: var(--secondary);
            border: none;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #2980b9;
        }

        .btn-danger {
            background-color: var(--danger);
        }

        .btn-danger:hover {
            background-color: #c0392b;
        }

        .btn-success {
            background-color: var(--success);
        }

        .btn-success:hover {
            background-color: #229954;
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: left;
            vertical-align: middle;
        }

        table th {
            background-color: var(--primary);
            color: white;
            font-weight: bold;
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        table tr:hover {
            background-color: #e0e0e0;
        }

        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: bold;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }

        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto; /* 15% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 80%; /* Could be more or less, depending on screen size */
            border-radius: 10px;
            position: relative;
        }

        .close-button {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close-button:hover,
        .close-button:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo">BU-CPMS</div>
        <div class="user-info">
            <p>Welcome,</p>
            <h3><?php echo htmlspecialchars($loggedInUserName); ?></h3>
            <p>(<?php echo htmlspecialchars($loggedInUserRole); ?>)</p>
        </div>
        <ul class="nav-links">
            <li><a href="#overview">Overview</a></li>
            <li><a href="#user-management">User Management</a></li>
            <li><a href="#slot-management">Slot Management</a></li>
            <li><a href="#parking-requests">Parking Requests</a></li>
            <li><a href="#reports">Reports</a></li>
            <li><button class="logout-btn">Logout</button></li>
        </ul>
    </div>

    <div class="main-content">
        <header>
            <h1>Admin Dashboard</h1>
        </header>

        <?php if (!empty($message)): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <section id="overview" class="section">
            <h2>Overview</h2>
            <div class="card-container">
                <div class="card">
                    <h3>Total Slots</h3>
                    <?php
                    $sql_total_slots = "SELECT COUNT(*) AS total_slots FROM ParkingSlot";
                    $result_total_slots = $conn->query($sql_total_slots);
                    $total_slots = $result_total_slots->fetch_assoc()['total_slots'];
                    echo "<p>" . htmlspecialchars($total_slots) . "</p>";
                    ?>
                </div>
                <div class="card">
                    <h3>Available Slots</h3>
                    <?php
                    $sql_available_slots = "SELECT COUNT(*) AS available_slots FROM ParkingSlot WHERE Status = 'Available'";
                    $result_available_slots = $conn->query($sql_available_slots);
                    $available_slots = $result_available_slots->fetch_assoc()['available_slots'];
                    echo "<p>" . htmlspecialchars($available_slots) . "</p>";
                    ?>
                </div>
                <div class="card">
                    <h3>Occupied Slots</h3>
                    <?php
                    $sql_occupied_slots = "SELECT COUNT(*) AS occupied_slots FROM ParkingSlot WHERE Status = 'Occupied'";
                    $result_occupied_slots = $conn->query($sql_occupied_slots);
                    $occupied_slots = $result_occupied_slots->fetch_assoc()['occupied_slots'];
                    echo "<p>" . htmlspecialchars($occupied_slots) . "</p>";
                    ?>
                </div>
                <div class="card">
                    <h3>Total Users</h3>
                    <?php
                    $sql_total_users = "SELECT COUNT(*) AS total_users FROM User";
                    $result_total_users = $conn->query($sql_total_users);
                    $total_users = $result_total_users->fetch_assoc()['total_users'];
                    echo "<p>" . htmlspecialchars($total_users) . "</p>";
                    ?>
                </div>
            </div>
        </section>

        <section id="user-management" class="section">
            <h2>User Management</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql_users = "SELECT UserID, Name, Email, Role FROM User";
                        $result_users = $conn->query($sql_users);

                        if ($result_users->num_rows > 0) {
                            while ($row = $result_users->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['UserID']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['Name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['Email']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['Role']) . "</td>";
                                echo "<td>
                                        <form action='admin_actions.php' method='POST' style='display:inline-block;'>
                                            <input type='hidden' name='action' value='change_role'>
                                            <input type='hidden' name='user_id' value='" . htmlspecialchars($row['UserID']) . "'>
                                            <select name='new_role' class='form-control' onchange='this.form.submit()'>
                                                <option value='Student' " . ($row['Role'] == 'Student' ? 'selected' : '') . ">Student</option>
                                                <option value='Faculty' " . ($row['Role'] == 'Faculty' ? 'selected' : '') . ">Faculty</option>
                                                <option value='Security' " . ($row['Role'] == 'Security' ? 'selected' : '') . ">Security</option>
                                                <option value='Admin' " . ($row['Role'] == 'Admin' ? 'selected' : '') . ">Admin</option>
                                            </select>
                                        </form>
                                        <button class='btn btn-danger remove-user-btn' data-user-id='" . htmlspecialchars($row['UserID']) . "'>Remove</button>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>No users found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section id="slot-management" class="section">
    <h2>Slot Management</h2>
    <form id="add-slot-form" action="admin_actions.php" method="POST">
        <input type="hidden" name="action" value="add_slot">
        <div class="form-group">
            <label for="slot-number">Slot Number:</label>
            <input type="text" id="slot-number" name="slot_number" required>
        </div>
        <div class="form-group">
            <label for="slot-type">Slot Type:</label>
            <select id="slot-type" name="slot_type" required>
                <option value="Regular">Regular</option>
                <option value="Handicap">Handicap</option>
                <option value="Electric">Electric Vehicle</option>
                <option value="Faculty">Faculty</option>
                <option value="Student">Student</option>
            </select>
        </div>
        <div class="form-group">
            <label for="slot-zone">Zone:</label>
            <input type="text" id="slot-zone" name="slot_zone" placeholder="e.g., A, B, Student Zone 1" required>
        </div>
        <button type="submit" class="btn btn-success">Add New Slot</button>
    </form>

    <h3 style="margin-top: 30px;">Current Parking Slots</h3>
    <form method="GET" action="AdminDashboard.php#slot-management" style="margin-bottom: 15px;">
        <input type="hidden" name="search_slot" value="1">
        <div class="form-group">
            <label for="search_slot_term">Search Slot (by Number, Type, Zone, or Status):</label>
            <input type="text" id="search_slot_term" name="search_slot_term" value="<?php echo isset($_GET['search_slot_term']) ? htmlspecialchars($_GET['search_slot_term']) : ''; ?>" placeholder="Enter search term">
        </div>
        <button type="submit" class="btn">Search Slot</button>
        <?php if (isset($_GET['search_slot'])): ?>
            <a href="AdminDashboard.php#slot-management" class="btn btn-secondary">Clear Search</a>
        <?php endif; ?>
    </form>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Slot ID</th>
                    <th>Number</th>
                    <th>Type</th>
                    <th>Zone</th>
                    <th>Status</th> <th>Is Occupied?</th> <th>Assigned To (User ID)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch slots from database
                $search_condition = "";
                $search_params = [];
                $search_types = "";

                if (isset($_GET['search_slot']) && !empty($_GET['search_slot_term'])) {
                    $searchTerm = "%" . $_GET['search_slot_term'] . "%";
                    $search_condition = " WHERE ps.SlotNumber LIKE ? OR ps.SlotType LIKE ? OR ps.Zone LIKE ? OR ps.Status LIKE ?";
                    $search_params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm];
                    $search_types = "ssss";
                }

                $sql_slots = "SELECT ps.SlotID, ps.SlotNumber, ps.SlotType, ps.Zone, ps.Status, ps.IsOccupied, ps.AssignedTo, u.Name as AssignedUserName
                              FROM ParkingSlot ps
                              LEFT JOIN User u ON ps.AssignedTo = u.UserID
                              $search_condition
                              ORDER BY ps.SlotID";
                $stmt_slots = $conn->prepare($sql_slots);

                if ($stmt_slots) {
                    if (!empty($search_params)) {
                        $stmt_slots->bind_param($search_types, ...$search_params);
                    }
                    $stmt_slots->execute();
                    $result_slots = $stmt_slots->get_result();

                    if ($result_slots->num_rows > 0) {
                        while ($row = $result_slots->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['SlotID']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['SlotNumber']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['SlotType']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['Zone']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['Status']) . "</td>";
                            echo "<td>" . ($row['IsOccupied'] ? 'Yes' : 'No') . "</td>";
                            echo "<td>" . ($row['AssignedTo'] ? htmlspecialchars($row['AssignedTo']) . ($row['AssignedUserName'] ? ' (' . htmlspecialchars($row['AssignedUserName']) . ')' : '') : 'N/A') . "</td>";
                            echo "<td>
                                    <form action='admin_actions.php' method='POST' style='display:inline-block; margin-bottom:5px;'>
                                        <input type='hidden' name='action' value='change_slot_admin_status'>
                                        <input type='hidden' name='slot_id' value='" . htmlspecialchars($row['SlotID']) . "'>
                                        <select name='new_status_text' onchange='this.form.submit()' style='padding: 5px;'>
                                            <option value='Available' " . ($row['Status'] == 'Available' ? 'selected' : '') . ">Available</option>
                                            <option value='Occupied' " . ($row['Status'] == 'Occupied' ? 'selected' : '') . ">Occupied (System)</option>
                                            <option value='Reserved' " . ($row['Status'] == 'Reserved' ? 'selected' : '') . ">Reserved (System)</option>
                                            <option value='Maintenance' " . ($row['Status'] == 'Maintenance' ? 'selected' : '') . ">Maintenance</option>
                                        </select>
                                    </form>
                                    <form action='admin_actions.php' method='POST' style='display:inline-block; margin-bottom:5px;'>
                                        <input type='hidden' name='action' value='toggle_slot_occupied'>
                                        <input type='hidden' name='slot_id' value='" . htmlspecialchars($row['SlotID']) . "'>
                                        <input type='hidden' name='new_status' value='" . ($row['IsOccupied'] ? 0 : 1) . "'>
                                        <button type='submit' class='btn " . ($row['IsOccupied'] ? "btn-warning" : "btn-success") . "' style='padding: 5px 10px; font-size: 0.9em;'>" . ($row['IsOccupied'] ? "Mark Free" : "Mark Occupied") . "</button>
                                    </form>
                                    <button class='btn btn-info assign-slot-btn' data-slot-id='" . htmlspecialchars($row['SlotID']) . "' style='padding: 5px 10px; font-size: 0.9em;'>Assign User</button>
                                    <form action='admin_actions.php' method='POST' onsubmit='return confirm(\"Are you sure you want to remove slot " . htmlspecialchars($row['SlotNumber']) . "?\");' style='display:inline-block;'>
                                        <input type='hidden' name='action' value='remove_slot'>
                                        <input type='hidden' name='slot_id' value='" . htmlspecialchars($row['SlotID']) . "'>
                                        <button type='submit' class='btn btn-danger' style='padding: 5px 10px; font-size: 0.9em;'>Remove</button>
                                    </form>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8'>No parking slots found" . (isset($_GET['search_slot']) ? " matching your search." : ".") . "</td></tr>";
                    }
                    $stmt_slots->close();
                } else {
                    echo "<tr><td colspan='8'>Error fetching slots: " . $conn->error . "</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</section>

<div id="assignSlotModal" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="document.getElementById('assignSlotModal').style.display='none'">&times;</span>
        <h2>Assign Slot to User</h2>
        <form id="assignSlotForm" action="admin_actions.php" method="POST">
            <input type="hidden" name="action" value="assign_slot">
            <input type="hidden" id="modal_slot_id" name="slot_id">
            <div class="form-group">
                <label for="modal_user_id_to_assign">User ID to Assign:</label>
                <input type="number" id="modal_user_id_to_assign" name="user_id_to_assign" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Assign Slot</button>
        </form>
    </div>
</div>

        <section id="parking-requests" class="section">
            <h2>Parking Requests</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>User Name</th>
                            <th>Requested Slot</th>
                            <th>Request Date</th>
                            <th>Request Time</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql_requests = "SELECT pr.RequestID, u.Name as UserName, ps.SlotNumber as RequestedSlotNumber, pr.RequestDate, pr.RequestTime, pr.Status
                                         FROM parkingrequest pr
                                         LEFT JOIN User u ON pr.UserID = u.UserID
                                         LEFT JOIN ParkingSlot ps ON pr.SlotID = ps.SlotID
                                         ORDER BY pr.RequestDate DESC, pr.RequestTime DESC";
                        $result_requests = $conn->query($sql_requests);

                        if ($result_requests->num_rows > 0) {
                            while ($row = $result_requests->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['RequestID']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['UserName'] ? $row['UserName'] : 'N/A') . "</td>";
                                echo "<td>" . htmlspecialchars($row['RequestedSlotNumber'] ? $row['RequestedSlotNumber'] : 'Any') . "</td>";
                                echo "<td>" . htmlspecialchars($row['RequestDate']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['RequestTime']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['Status']) . "</td>";
                                echo "<td>
                                        <form action='admin_actions.php' method='POST' style='display:inline-block;'>
                                            <input type='hidden' name='action' value='update_request_status'>
                                            <input type='hidden' name='request_id' value='" . htmlspecialchars($row['RequestID']) . "'>
                                            <select name='new_status' class='form-control' onchange='this.form.submit()'>
                                                <option value='Pending' " . ($row['Status'] == 'Pending' ? 'selected' : '') . ">Pending</option>
                                                <option value='Approved' " . ($row['Status'] == 'Approved' ? 'selected' : '') . ">Approved</option>
                                                <option value='Rejected' " . ($row['Status'] == 'Rejected' ? 'selected' : '') . ">Rejected</option>
                                                <option value='Cancelled' " . ($row['Status'] == 'Cancelled' ? 'selected' : '') . ">Cancelled</option>
                                            </select>
                                        </form>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7'>No parking requests found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section id="reports" class="section">
            <h2>Generate Reports</h2>
            <form id="report-form">
                <div class="form-group">
                    <label for="report-type">Report Type:</label>
                    <select id="report-type" name="report_type" required>
                        <option value="daily_occupancy">Daily Occupancy</option>
                        <option value="monthly_occupancy">Monthly Occupancy</option>
                        <option value="user_activity">User Activity</option>
                        </select>
                </div>
                <div class="form-group">
                    <label for="report-format">Format:</label>
                    <select id="report-format" name="format" required>
                        <option value="pdf">PDF</option>
                        </select>
                </div>
                <div class="form-group">
                    <label for="start-date">Start Date (Optional):</label>
                    <input type="date" id="start-date" name="start_date">
                </div>
                <div class="form-group">
                    <label for="end-date">End Date (Optional):</label>
                    <input type="date" id="end-date" name="end_date">
                </div>
                <div class="form-group">
                    <label for="user-id-filter">Filter by User ID (Optional):</label>
                    <input type="number" id="user-id-filter" name="user_id_filter" placeholder="Enter User ID">
                </div>
                <div class="form-group">
                    <label for="slot-id-filter">Filter by Slot ID (Optional):</label>
                    <input type="number" id="slot-id-filter" name="slot_id_filter" placeholder="Enter Slot ID">
                </div>
                <button type="submit" class="btn btn-primary">Generate Report</button>
            </form>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Smooth scrolling for navigation links
            document.querySelectorAll('.nav-links a').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    document.querySelector(this.getAttribute('href')).scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            });

            // Handle user removal
            document.querySelectorAll('.remove-user-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.dataset.userId;
                    if (confirm('Are you sure you want to remove user ID ' + userId + '?')) {
                        fetch('admin_actions.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: 'action=remove_user&user_id=' + userId
                        })
                        .then(response => response.text())
                        .then(data => {
                            alert('User removal request sent. Please refresh page to see changes.');
                            window.location.reload();
                        })
                        .catch(error => {
                            console.error('Error removing user:', error);
                            alert('Failed to remove user.');
                        });
                    }
                });
            });

            // ... (existing script) ...
            // Handle assign slot button
            document.querySelectorAll('.assign-slot-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const slotId = this.dataset.slotId;
                    document.getElementById('modal_slot_id').value = slotId;
                    document.getElementById('assignSlotModal').style.display = 'block';
                });
            });

            // Close modal logic (if not already present for other modals)
            var assignModal = document.getElementById('assignSlotModal');
            var assignSpan = assignModal.querySelector('.close-button'); // Assuming one modal for assign
            assignSpan.onclick = function() {
                assignModal.style.display = "none";
            }
            window.onclick = function(event) {
                if (event.target == assignModal) {
                    assignModal.style.display = "none";
                }
            }
// ... (rest of the script) ...

            // Handle slot removal
            document.querySelectorAll('.remove-slot-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const slotId = this.dataset.slotId;
                    if (confirm('Are you sure you want to remove slot ID ' + slotId + '?')) {
                        fetch('admin_actions.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: 'action=remove_slot&slot_id=' + slotId
                        })
                        .then(response => response.text())
                        .then(data => {
                            alert('Slot removal request sent. Please refresh page to see changes.');
                            window.location.reload();
                        })
                        .catch(error => {
                            console.error('Error removing slot:', error);
                            alert('Failed to remove slot.');
                        });
                    }
                });
            });


            // Report Form (Client-side simulation)
            document.getElementById('report-form').addEventListener('submit', function(e) {
                e.preventDefault();
                const format = document.getElementById('report-format').value;
                const reportType = document.getElementById('report-type').value;
                const startDate = document.getElementById('start-date').value;
                const endDate = document.getElementById('end-date').value;
                const userIdFilter = document.getElementById('user-id-filter').value;
                const slotIdFilter = document.getElementById('slot-id-filter').value;

                // Construct URL-encoded string for POST request
                const formData = new URLSearchParams();
                formData.append('report_type', reportType);
                formData.append('format', format);
                if (startDate) formData.append('start_date', startDate);
                if (endDate) formData.append('end_date', endDate);
                if (userIdFilter) formData.append('user_id_filter', userIdFilter);
                if (slotIdFilter) formData.append('slot_id_filter', slotIdFilter);

                fetch('generate_report.php', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                })
                .then(response => {
                    if (response.ok) {
                        // If the response is a file, trigger download
                        return response.blob();
                    }
                    throw new Error('Network response was not ok.');
                })
                .then(blob => {
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = url;
                    a.download = `${reportType}_report.${format}`; // Suggest filename
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    alert(`Report generated successfully! Downloading ${reportType.replace('_', ' ').toUpperCase()} report in ${format.toUpperCase()} format.`);
                })
                .catch(error => {
                    console.error('Error generating report:', error);
                    alert('Failed to generate report: ' + error.message);
                });
            });

            // Logout Button
            document.querySelector('.logout-btn').addEventListener('click', function() {
                if (confirm('Are you sure you want to logout?')) {
                    window.location.href = 'logout.php';
                }
            });
        });
    </script>
</body>
</html>