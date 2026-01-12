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
    <title>BU-CPMS - Security Dashboard</title>
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
            --security-accent: #e67e22; /* Orange accent for security */
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
        
        /* Sidebar */
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
            border: 3px solid var(--security-accent); /* Orange border for security */
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
            border-left: 4px solid var(--security-accent); /* Orange accent for security */
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
            background-color: var(--security-accent); /* Orange for security */
        }
        
        .card-icon.vehicle {
            background-color: var(--success);
        }
        
        .card-icon.entry {
            background-color: var(--secondary);
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
            border-color: var(--security-accent);
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
            background-color: var(--security-accent);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #d35400;
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
        
        /* Slot Management */
        .lot-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .lot-title {
            font-size: 20px;
            font-weight: 600;
        }
        
        .lot-nav {
            display: flex;
            gap: 10px;
        }
        
        .slots-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 15px;
            margin: 25px 0;
        }
        
        .slot {
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
        }
        
        .slot-number {
            font-weight: 600;
            font-size: 18px;
            margin-bottom: 5px;
        }
        
        .slot-status {
            font-size: 14px;
        }
        
        .slot-details {
            font-size: 12px;
            margin-top: 5px;
            color: #555;
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
        }
        
        .slot.reserved {
            background-color: #fdebd0;
            border: 1px solid #f39c12;
        }
        
        .slot-actions {
            display: flex;
            gap: 5px;
            margin-top: 10px;
        }
        
        .slot-action-btn {
            padding: 5px 10px;
            font-size: 12px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
        }
        
        .free-btn {
            background-color: var(--success);
            color: white;
        }
        
        .reserve-btn {
            background-color: var(--warning);
            color: white;
        }
        
        /* Search Results */
        .search-results {
            margin-top: 20px;
        }
        
        .result-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            padding: 20px;
            margin-bottom: 15px;
        }
        
        .result-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        
        .result-title {
            font-size: 18px;
            font-weight: 600;
        }
        
        .result-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            background-color: #d5f5e3;
            color: #27ae60;
        }
        
        .result-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .detail-label {
            font-size: 14px;
            color: var(--gray);
            margin-bottom: 5px;
        }
        
        .detail-value {
            font-weight: 600;
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
            max-width: 600px;
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
            
            .cards-container {
                grid-template-columns: 1fr;
            }
            
            .slots-grid {
                grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <div class="system-title">BU-CPMS</div>
                <div class="system-subtitle">Bahria University Campus Parking Management System</div>
            </div>
            
            <div class="profile">
                <img src="profile.jpg" alt="Security" class="profile-img">
                <div class="profile-name">Ahmed Khan</div>
                <div class="profile-role">Security</div>
            </div>
            
            <div class="nav-menu">
                <div class="nav-item active" data-tab="dashboard">
                    <i class="icon">📊</i> Dashboard
                </div>
                <div class="nav-item" data-tab="manual-entry">
                    <i class="icon">📝</i> Manual Entry
                </div>
                <div class="nav-item" data-tab="slot-management">
                    <i class="icon">🅿️</i> Slot Management
                </div>
                <div class="nav-item" data-tab="search-vehicle">
                    <i class="icon">🔍</i> Search Vehicle
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <div class="page-title">Security Dashboard</div>
                <div class="user-actions">
                    <button class="notification-btn">
                        <i class="icon">🔔</i>
                        <span class="notification-badge">3</span>
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
                            <div class="card-title">Occupied Slots</div>
                            <div class="card-icon parking">
                                <i class="icon">🅿️</i>
                            </div>
                        </div>
                        <div class="card-value">142</div>
                        <div class="card-footer">
                            <i class="icon">ℹ️</i> Out of 200 total slots
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Today's Entries</div>
                            <div class="card-icon vehicle">
                                <i class="icon">🚗</i>
                            </div>
                        </div>
                        <div class="card-value">87</div>
                        <div class="card-footer">
                            <i class="icon">⏱️</i> Last hour: 12 entries
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Manual Entries</div>
                            <div class="card-icon entry">
                                <i class="icon">📝</i>
                            </div>
                        </div>
                        <div class="card-value">5</div>
                        <div class="card-footer">
                            <i class="icon">ℹ️</i> Today's manual entries
                        </div>
                    </div>
                </div>
                
                <div class="table-container">
                    <h2 style="margin-bottom: 15px;">Recent Entries</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Vehicle</th>
                                <th>Type</th>
                                <th>Parking Lot</th>
                                <th>Slot</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>10:25 AM</td>
                                <td>LE-1234</td>
                                <td>Sedan</td>
                                <td>Faculty Lot A</td>
                                <td>F-12</td>
                                <td><span style="color: var(--success);">✔</span> Verified</td>
                            </tr>
                            <tr>
                                <td>10:18 AM</td>
                                <td>KHI-5678</td>
                                <td>SUV</td>
                                <td>North Campus</td>
                                <td>N-08</td>
                                <td><span style="color: var(--success);">✔</span> Verified</td>
                            </tr>
                            <tr>
                                <td>10:05 AM</td>
                                <td>LHE-9012</td>
                                <td>Hatchback</td>
                                <td>Main Campus</td>
                                <td>M-15</td>
                                <td><span style="color: var(--warning);">⚠</span> Manual Entry</td>
                            </tr>
                            <tr>
                                <td>9:48 AM</td>
                                <td>ISB-3456</td>
                                <td>Motorcycle</td>
                                <td>Student Lot B</td>
                                <td>S-22</td>
                                <td><span style="color: var(--success);">✔</span> Verified</td>
                            </tr>
                            <tr>
                                <td>9:30 AM</td>
                                <td>RWP-7890</td>
                                <td>Pickup</td>
                                <td>Visitor Area</td>
                                <td>V-05</td>
                                <td><span style="color: var(--danger);">✖</span> No Reservation</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Manual Entry Tab -->
            <div id="manual-entry" class="tab-content">
                <h2 style="margin-bottom: 20px;">Manual Vehicle Entry</h2>
                
                <div class="form-container">
                    <form id="manual-entry-form">
                        <div class="form-group">
                            <label class="form-label" for="entry-vehicle-type">Vehicle Type</label>
                            <select class="form-control" id="entry-vehicle-type" required>
                                <option value="">Select vehicle type</option>
                                <option value="sedan">Sedan</option>
                                <option value="suv">SUV</option>
                                <option value="hatchback">Hatchback</option>
                                <option value="pickup">Pickup Truck</option>
                                <option value="motorcycle">Motorcycle</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="entry-plate-number">Plate Number</label>
                            <input type="text" class="form-control" id="entry-plate-number" placeholder="e.g. LE-1234" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="entry-vehicle-color">Color</label>
                            <input type="text" class="form-control" id="entry-vehicle-color" placeholder="e.g. White" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="entry-parking-lot">Parking Lot</label>
                            <select class="form-control" id="entry-parking-lot" required>
                                <option value="">Select parking lot</option>
                                <option value="faculty-a">Faculty Lot A</option>
                                <option value="faculty-b">Faculty Lot B</option>
                                <option value="student-a">Student Lot A</option>
                                <option value="student-b">Student Lot B</option>
                                <option value="north">North Campus Lot</option>
                                <option value="main">Main Campus Lot</option>
                                <option value="visitor">Visitor Area</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="entry-slot-number">Slot Number</label>
                            <input type="text" class="form-control" id="entry-slot-number" placeholder="e.g. F-12">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="entry-purpose">Purpose of Visit</label>
                            <input type="text" class="form-control" id="entry-purpose" placeholder="e.g. Meeting with faculty">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="entry-visitor-name">Visitor Name (if applicable)</label>
                            <input type="text" class="form-control" id="entry-visitor-name" placeholder="e.g. John Smith">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="entry-contact">Contact Number</label>
                            <input type="tel" class="form-control" id="entry-contact" placeholder="e.g. 03001234567">
                        </div>
                        
                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            Record Manual Entry
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Slot Management Tab -->
            <div id="slot-management" class="tab-content">
                <div class="lot-controls">
                    <div class="lot-title">Parking Slots</div>
                    <div class="lot-nav">
                        <button class="btn btn-secondary" id="prev-lot">
                            &larr; Previous Lot
                        </button>
                        <button class="btn btn-secondary" id="next-lot">
                            Next Lot &rarr;
                        </button>
                    </div>
                </div>
                
                <div class="slots-grid">
                    <div class="slot occupied">
                        <div class="slot-number">F-01</div>
                        <div class="slot-status">Occupied</div>
                        <div class="slot-details">LE-1234 (Sedan)</div>
                        <div class="slot-actions">
                            <button class="slot-action-btn free-btn">Free</button>
                        </div>
                    </div>
                    <div class="slot occupied">
                        <div class="slot-number">F-02</div>
                        <div class="slot-status">Occupied</div>
                        <div class="slot-details">KHI-5678 (SUV)</div>
                        <div class="slot-actions">
                            <button class="slot-action-btn free-btn">Free</button>
                        </div>
                    </div>
                    <div class="slot available">
                        <div class="slot-number">F-03</div>
                        <div class="slot-status">Available</div>
                        <div class="slot-actions">
                            <button class="slot-action-btn reserve-btn">Reserve</button>
                        </div>
                    </div>
                    <div class="slot reserved">
                        <div class="slot-number">F-04</div>
                        <div class="slot-status">Reserved</div>
                        <div class="slot-details">For Dr. Ali (3-5PM)</div>
                        <div class="slot-actions">
                            <button class="slot-action-btn free-btn">Free</button>
                        </div>
                    </div>
                    <div class="slot occupied">
                        <div class="slot-number">F-05</div>
                        <div class="slot-status">Occupied</div>
                        <div class="slot-details">LHE-9012 (Hatchback)</div>
                        <div class="slot-actions">
                            <button class="slot-action-btn free-btn">Free</button>
                        </div>
                    </div>
                    <div class="slot available">
                        <div class="slot-number">F-06</div>
                        <div class="slot-status">Available</div>
                        <div class="slot-actions">
                            <button class="slot-action-btn reserve-btn">Reserve</button>
                        </div>
                    </div>
                    <div class="slot occupied">
                        <div class="slot-number">F-07</div>
                        <div class="slot-status">Occupied</div>
                        <div class="slot-details">ISB-3456 (Motorcycle)</div>
                        <div class="slot-actions">
                            <button class="slot-action-btn free-btn">Free</button>
                        </div>
                    </div>
                    <div class="slot available">
                        <div class="slot-number">F-08</div>
                        <div class="slot-status">Available</div>
                        <div class="slot-actions">
                            <button class="slot-action-btn reserve-btn">Reserve</button>
                        </div>
                    </div>
                    <div class="slot occupied">
                        <div class="slot-number">F-09</div>
                        <div class="slot-status">Occupied</div>
                        <div class="slot-details">RWP-7890 (Pickup)</div>
                        <div class="slot-actions">
                            <button class="slot-action-btn free-btn">Free</button>
                        </div>
                    </div>
                    <div class="slot available">
                        <div class="slot-number">F-10</div>
                        <div class="slot-status">Available</div>
                        <div class="slot-actions">
                            <button class="slot-action-btn reserve-btn">Reserve</button>
                        </div>
                    </div>
                    <div class="slot occupied">
                        <div class="slot-number">F-11</div>
                        <div class="slot-status">Occupied</div>
                        <div class="slot-details">LE-2468 (Sedan)</div>
                        <div class="slot-actions">
                            <button class="slot-action-btn free-btn">Free</button>
                        </div>
                    </div>
                    <div class="slot occupied">
                        <div class="slot-number">F-12</div>
                        <div class="slot-status">Occupied</div>
                        <div class="slot-details">KHI-1357 (SUV)</div>
                        <div class="slot-actions">
                            <button class="slot-action-btn free-btn">Free</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Search Vehicle Tab -->
            <div id="search-vehicle" class="tab-content">
                <h2 style="margin-bottom: 20px;">Search Vehicle</h2>
                
                <div class="form-container">
                    <form id="vehicle-search-form">
                        <div class="form-group">
                            <label class="form-label" for="search-vehicle-type">Vehicle Type</label>
                            <select class="form-control" id="search-vehicle-type">
                                <option value="">Any type</option>
                                <option value="sedan">Sedan</option>
                                <option value="suv">SUV</option>
                                <option value="hatchback">Hatchback</option>
                                <option value="pickup">Pickup Truck</option>
                                <option value="motorcycle">Motorcycle</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="search-plate-number">Plate Number</label>
                            <input type="text" class="form-control" id="search-plate-number" placeholder="e.g. LE-1234 (partial matches allowed)">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="search-vehicle-color">Color</label>
                            <input type="text" class="form-control" id="search-vehicle-color" placeholder="e.g. White">
                        </div>
                        
                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            Search Vehicle
                        </button>
                    </form>
                </div>
                
                <div class="search-results" id="search-results" style="display: none;">
                    <h3 style="margin: 20px 0 15px;">Search Results</h3>
                    
                    <div class="result-card">
                        <div class="result-header">
                            <div class="result-title">LE-1234 (Sedan)</div>
                            <div class="result-status">Currently Parked</div>
                        </div>
                        <div class="result-details">
                            <div>
                                <div class="detail-label">Color</div>
                                <div class="detail-value">White</div>
                            </div>
                            <div>
                                <div class="detail-label">Owner</div>
                                <div class="detail-value">Dr. Ali Ahmed (Faculty)</div>
                            </div>
                            <div>
                                <div class="detail-label">Parking Lot</div>
                                <div class="detail-value">Faculty Lot A</div>
                            </div>
                            <div>
                                <div class="detail-label">Slot Number</div>
                                <div class="detail-value">F-12</div>
                            </div>
                            <div>
                                <div class="detail-label">Entry Time</div>
                                <div class="detail-value">Today, 8:15 AM</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="result-card">
                        <div class="result-header">
                            <div class="result-title">KHI-5678 (SUV)</div>
                            <div class="result-status">Currently Parked</div>
                        </div>
                        <div class="result-details">
                            <div>
                                <div class="detail-label">Color</div>
                                <div class="detail-value">Black</div>
                            </div>
                            <div>
                                <div class="detail-label">Owner</div>
                                <div class="detail-value">Dr. Sara Khan (Faculty)</div>
                            </div>
                            <div>
                                <div class="detail-label">Parking Lot</div>
                                <div class="detail-value">North Campus Lot</div>
                            </div>
                            <div>
                                <div class="detail-label">Slot Number</div>
                                <div class="detail-value">N-08</div>
                            </div>
                            <div>
                                <div class="detail-label">Entry Time</div>
                                <div class="detail-value">Today, 9:30 AM</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reserve Slot Modal -->
    <div class="modal" id="reserve-modal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Reserve Parking Slot</div>
                <button class="close-modal">&times;</button>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="reserve-slot-number">Slot Number</label>
                <input type="text" class="form-control" id="reserve-slot-number" readonly>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="reserve-lot-name">Parking Lot</label>
                <input type="text" class="form-control" id="reserve-lot-name" readonly>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="reserve-for">Reserve For</label>
                <select class="form-control" id="reserve-for" required>
                    <option value="">Select option</option>
                    <option value="faculty">Faculty Member</option>
                    <option value="visitor">Visitor</option>
                    <option value="event">Special Event</option>
                    <option value="maintenance">Maintenance</option>
                </select>
            </div>
            
            <div class="form-group" id="faculty-select-group">
                <label class="form-label" for="reserve-faculty">Select Faculty</label>
                <select class="form-control" id="reserve-faculty">
                    <option value="">Select faculty</option>
                    <option value="ali">Dr. Ali Ahmed</option>
                    <option value="sara">Dr. Sara Khan</option>
                    <option value="raza">Dr. Raza Hussain</option>
                    <option value="fatima">Dr. Fatima Malik</option>
                </select>
            </div>
            
            <div class="form-group" id="visitor-details-group" style="display: none;">
                <label class="form-label" for="reserve-visitor-name">Visitor Name</label>
                <input type="text" class="form-control" id="reserve-visitor-name" placeholder="Visitor name">
                
                <label class="form-label" for="reserve-visitor-contact">Contact Number</label>
                <input type="text" class="form-control" id="reserve-visitor-contact" placeholder="Contact number">
            </div>
            
            <div class="form-group">
                <label class="form-label" for="reserve-date">Date</label>
                <input type="date" class="form-control" id="reserve-date" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Time Slot</label>
                <div style="display: flex; gap: 10px;">
                    <select class="form-control" id="reserve-start-time" style="flex: 1;" required>
                        <option value="">Start time</option>
                        <option value="08:00">8:00 AM</option>
                        <option value="09:00">9:00 AM</option>
                        <option value="10:00">10:00 AM</option>
                        <option value="11:00">11:00 AM</option>
                        <option value="12:00">12:00 PM</option>
                        <option value="13:00">1:00 PM</option>
                        <option value="14:00">2:00 PM</option>
                        <option value="15:00">3:00 PM</option>
                        <option value="16:00">4:00 PM</option>
                        <option value="17:00">5:00 PM</option>
                    </select>
                    <select class="form-control" id="reserve-end-time" style="flex: 1;" required>
                        <option value="">End time</option>
                        <option value="09:00">9:00 AM</option>
                        <option value="10:00">10:00 AM</option>
                        <option value="11:00">11:00 AM</option>
                        <option value="12:00">12:00 PM</option>
                        <option value="13:00">1:00 PM</option>
                        <option value="14:00">2:00 PM</option>
                        <option value="15:00">3:00 PM</option>
                        <option value="16:00">4:00 PM</option>
                        <option value="17:00">5:00 PM</option>
                        <option value="18:00">6:00 PM</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="reserve-purpose">Purpose</label>
                <textarea class="form-control" id="reserve-purpose" rows="3" placeholder="Purpose of reservation"></textarea>
            </div>
            
            <div class="modal-actions">
                <button class="btn btn-secondary close-modal">Cancel</button>
                <button class="btn btn-primary" id="confirm-reservation">Confirm Reservation</button>
            </div>
        </div>
    </div>

    <!-- Free Slot Modal -->
    <div class="modal" id="free-modal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Free Parking Slot</div>
                <button class="close-modal">&times;</button>
            </div>
            
            <p style="margin-bottom: 20px;">Are you sure you want to free this parking slot?</p>
            
            <div class="form-group">
                <label class="form-label" for="free-slot-number">Slot Number</label>
                <input type="text" class="form-control" id="free-slot-number" readonly>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="free-lot-name">Parking Lot</label>
                <input type="text" class="form-control" id="free-lot-name" readonly>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="free-reason">Reason</label>
                <select class="form-control" id="free-reason" required>
                    <option value="">Select reason</option>
                    <option value="departed">Vehicle Departed</option>
                    <option value="wrong-parking">Wrong Parking</option>
                    <option value="emergency">Emergency</option>
                    <option value="other">Other</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="free-notes">Notes</label>
                <textarea class="form-control" id="free-notes" rows="3" placeholder="Additional notes"></textarea>
            </div>
            
            <div class="modal-actions">
                <button class="btn btn-secondary close-modal">Cancel</button>
                <button class="btn btn-primary" id="confirm-free">Confirm Free Slot</button>
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

        // Manual Entry Form
        document.getElementById('manual-entry-form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Manual entry recorded successfully!');
            this.reset();
        });

        // Vehicle Search Form
        document.getElementById('vehicle-search-form').addEventListener('submit', function(e) {
            e.preventDefault();
            document.getElementById('search-results').style.display = 'block';
            // In a real app, you would make an AJAX call here and populate results dynamically
        });

        // Lot Navigation
        const lotNames = ["Faculty Lot A", "Faculty Lot B", "Student Lot A", "Student Lot B", "North Campus Lot", "Main Campus Lot", "Visitor Area"];
        let currentLotIndex = 0;
        
        document.getElementById('next-lot').addEventListener('click', function() {
            currentLotIndex = (currentLotIndex + 1) % lotNames.length;
            updateLotDisplay();
        });
        
        document.getElementById('prev-lot').addEventListener('click', function() {
            currentLotIndex = (currentLotIndex - 1 + lotNames.length) % lotNames.length;
            updateLotDisplay();
        });
        
        function updateLotDisplay() {
    // This line updates the visible lot title on the dashboard
    // It uses the value from the 'lotNames' array based on the 'currentLotIndex'
    document.querySelector('.lot-title').textContent = lotNames[currentLotIndex];

    const slotsGrid = document.querySelector('#slot-management .slots-grid');
    slotsGrid.innerHTML = '<p>Loading slots...</p>'; // Placeholder

    // The fetch request correctly uses the current lot name
    fetch(`get_slots_for_security.php?lot_name=${encodeURIComponent(lotNames[currentLotIndex])}`)
    .then(response => response.json())
    .then(data => {
        slotsGrid.innerHTML = ''; // Clear loading
        if (data.status === 'success' && data.slots) {
            data.slots.forEach(slot => {
                const slotDiv = document.createElement('div');
                slotDiv.classList.add('slot');
                slotDiv.classList.add(slot.status.toLowerCase().replace(/\s+/g, '-'));
                if (slot.is_occupied) slotDiv.classList.add('occupied-state');

                slotDiv.innerHTML = `
                    <div class="slot-number">${slot.slot_number}</div>
                    <div class="slot-status">${slot.status}</div>
                    <div class="slot-details">${slot.details || ''}</div>
                    <div class="slot-actions">
                        ${slot.status === 'Available' || slot.status === null ? `<button class="slot-action-btn reserve-btn" data-slot-id="${slot.slot_id}">Reserve</button>` : ''}
                        ${slot.status === 'Occupied' || slot.status === 'Reserved' || slot.status === 'Maintenance' ? `<button class="slot-action-btn free-btn" data-slot-id="${slot.slot_id}">Free</button>` : ''}
                    </div>
                `;
                slotsGrid.appendChild(slotDiv);
            });
            attachSlotActionListeners(); // Re-attach event listeners for new buttons
        } else {
            slotsGrid.innerHTML = `<p>${data.message || 'No slots found for this lot.'}</p>`;
        }
    })
    .catch(error => {
        console.error('Error fetching slots:', error);
        slotsGrid.innerHTML = '<p>Error loading slots. Please try again.</p>';
    });
}

        // Reserve Slot Modal
        const reserveModal = document.getElementById('reserve-modal');
        const freeModal = document.getElementById('free-modal');
        
        document.querySelectorAll('.reserve-btn').forEach(button => {
            button.addEventListener('click', function() {
                const slotNumber = this.closest('.slot').querySelector('.slot-number').textContent;
                const lotName = document.querySelector('.lot-title').textContent;
                
                document.getElementById('reserve-slot-number').value = slotNumber;
                document.getElementById('reserve-lot-name').value = lotName;
                
                // Set default date to today
                const today = new Date();
                document.getElementById('reserve-date').value = today.toISOString().split('T')[0];
                
                reserveModal.style.display = 'flex';
            });
        });
        
        // Free Slot Modal
        document.querySelectorAll('.free-btn').forEach(button => {
            button.addEventListener('click', function() {
                const slotNumber = this.closest('.slot').querySelector('.slot-number').textContent;
                const lotName = document.querySelector('.lot-title').textContent;
                
                document.getElementById('free-slot-number').value = slotNumber;
                document.getElementById('free-lot-name').value = lotName;
                
                freeModal.style.display = 'flex';
            });
        });
        
        // Close Modals
        document.querySelectorAll('.close-modal').forEach(button => {
            button.addEventListener('click', function() {
                reserveModal.style.display = 'none';
                freeModal.style.display = 'none';
            });
        });
        
        window.addEventListener('click', function(event) {
            if (event.target === reserveModal || event.target === freeModal) {
                reserveModal.style.display = 'none';
                freeModal.style.display = 'none';
            }
        });
        
        // Confirm Reservation
       // Confirm Reservation (Security)
        document.getElementById('confirm-reservation').addEventListener('click', function() {
            const slotId = document.getElementById('reserve-slot-number').value;
            const reserveForType = document.getElementById('reserve-for').value;
            const facultyUserId = document.getElementById('reserve-faculty').value; // if 'faculty'
            const visitorName = document.getElementById('reserve-visitor-name').value; // if 'visitor'
            const reserveDate = document.getElementById('reserve-date').value;
            const startTime = document.getElementById('reserve-start-time').value;
            const endTime = document.getElementById('reserve-end-time').value;
            const purpose = document.getElementById('reserve-purpose').value;

            // Basic Validation
            if (!slotId || !reserveForType || !reserveDate || !startTime || !endTime) {
                alert('Please fill all required fields for reservation.');
                return;
            }
             if (reserveForType === 'faculty' && !facultyUserId) {
                alert('Please select a faculty member.');
                return;
            }

            const formData = new FormData();
            formData.append('action', 'reserve_slot_security');
            formData.append('slot_id', slotId);
            formData.append('reserve_for_type', reserveForType);
            if (reserveForType === 'faculty') formData.append('faculty_user_id', facultyUserId);
            if (reserveForType === 'visitor') formData.append('visitor_name', visitorName); // Can add more visitor fields
            formData.append('reserve_date', reserveDate);
            formData.append('reserve_start_time', startTime);
            formData.append('reserve_end_time', endTime);
            formData.append('reserve_purpose', purpose);


            fetch('security_actions.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.status === 'success') {
                    reserveModal.style.display = 'none';
                    // Consider reloading slot view or updating UI dynamically
                    updateLotDisplay(); // Refresh the current lot view
                }
            })
            .catch(error => {
                console.error('Error reserving slot:', error);
                alert('An error occurred while reserving the slot.');
            });
        });
        
        // Confirm Free Slot
        // Confirm Free Slot (Security)
        document.getElementById('confirm-free').addEventListener('click', function() {
            const slotId = document.getElementById('free-slot-number').value;
            const reason = document.getElementById('free-reason').value;
            const notes = document.getElementById('free-notes').value;

            if (!slotId || !reason) {
                alert('Slot ID and Reason are required to free a slot.');
                return;
            }

            const formData = new FormData();
            formData.append('action', 'free_slot_security');
            formData.append('slot_id', slotId);
            formData.append('free_reason', reason);
            formData.append('free_notes', notes);

            fetch('security_actions.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.status === 'success') {
                    freeModal.style.display = 'none';
                    // Consider reloading slot view or updating UI dynamically
                     updateLotDisplay(); // Refresh the current lot view
                }
            })
            .catch(error => {
                console.error('Error freeing slot:', error);
                alert('An error occurred while freeing the slot.');
            });
        });
        
        // Update Lot Display to fetch dynamic slots
        // (This is a simplified example, you'd fetch based on currentLotIndex)
        function updateLotDisplay() {
            const lotTitle = document.querySelector('.lot-title').textContent; // e.g., "Faculty Lot A"
            const slotsGrid = document.querySelector('#slot-management .slots-grid');
            slotsGrid.innerHTML = '<p>Loading slots...</p>'; // Placeholder

            fetch(`get_slots_for_security.php?lot_name=${encodeURIComponent(lotNames[currentLotIndex])}`) // You'll need to create this PHP file
            .then(response => response.json())
            .then(data => {
                slotsGrid.innerHTML = ''; // Clear loading
                if (data.status === 'success' && data.slots) {
                    data.slots.forEach(slot => {
                        const slotDiv = document.createElement('div');
                        slotDiv.classList.add('slot');
                        slotDiv.classList.add(slot.status.toLowerCase().replace(/\s+/g, '-')); // e.g., 'available', 'occupied', 'reserved'
                        if (slot.is_occupied) slotDiv.classList.add('occupied-state');


                        slotDiv.innerHTML = `
                            <div class="slot-number">${slot.slot_number}</div>
                            <div class="slot-status">${slot.status}</div>
                            <div class="slot-details">${slot.details || ''}</div>
                            <div class="slot-actions">
                                ${slot.status === 'Available' || slot.status === null ? `<button class="slot-action-btn reserve-btn" data-slot-id="${slot.slot_id}">Reserve</button>` : ''}
                                ${slot.status === 'Occupied' || slot.status === 'Reserved' || slot.status === 'Maintenance' ? `<button class="slot-action-btn free-btn" data-slot-id="${slot.slot_id}">Free</button>` : ''}
                            </div>
                        `;
                        slotsGrid.appendChild(slotDiv);
                    });
                    // Re-attach event listeners for new buttons
                    attachSlotActionListeners();
                } else {
                    slotsGrid.innerHTML = `<p>${data.message || 'No slots found for this lot.'}</p>`;
                }
            })
            .catch(error => {
                console.error('Error fetching slots:', error);
                slotsGrid.innerHTML = '<p>Error loading slots. Please try again.</p>';
            });
        }

        function attachSlotActionListeners() {
            document.querySelectorAll('#slot-management .reserve-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const slotId = this.dataset.slotId; // Assuming you add data-slot-id
                    const slotElement = this.closest('.slot');
                    const slotNumber = slotElement.querySelector('.slot-number').textContent;
                    const lotName = document.querySelector('.lot-title').textContent;

                    document.getElementById('reserve-slot-number').value = slotNumber; // Or slotId if that's what you display
                    document.getElementById('reserve-lot-name').value = lotName;
                    document.getElementById('reserve-date').valueAsDate = new Date(); // Default to today
                    reserveModal.style.display = 'flex';
                });
            });

            document.querySelectorAll('#slot-management .free-btn').forEach(button => {
                button.addEventListener('click', function() {
                     const slotId = this.dataset.slotId; // Assuming you add data-slot-id
                    const slotElement = this.closest('.slot');
                    const slotNumber = slotElement.querySelector('.slot-number').textContent;
                    const lotName = document.querySelector('.lot-title').textContent;

                    document.getElementById('free-slot-number').value = slotNumber; // Or slotId
                    document.getElementById('free-lot-name').value = lotName;
                    freeModal.style.display = 'flex';
                });
            });
        }
        // Initial call to load slots for the default lot and attach listeners
        updateLotDisplay();

        // Show/hide form fields based on reserve type
        document.getElementById('reserve-for').addEventListener('change', function() {
            const value = this.value;
            document.getElementById('faculty-select-group').style.display = 
                value === 'faculty' ? 'block' : 'none';
            document.getElementById('visitor-details-group').style.display = 
                value === 'visitor' ? 'block' : 'none';
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