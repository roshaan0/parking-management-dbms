<?php
session_start();

// Ensure only Admin users can access this script
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    http_response_code(403); // Forbidden
    echo "Unauthorized access.";
    exit;
}

require __DIR__ . '/vendor/autoload.php'; // Composer autoloader
require_once 'db.php'; // Your database connection

use Dompdf\Dompdf;
use Dompdf\Options;

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['report_type']) && isset($_GET['format'])) {
    $reportType = $_GET['report_type'];
    $format = $_GET['format']; // Will be 'pdf'

    if ($format !== 'pdf') {
        http_response_code(400);
        echo "Invalid format specified.";
        exit;
    }

    $title = '';
    $html = '';

    switch ($reportType) {
        case 'all_users':
            $title = 'All Users Report';
            $sql = "SELECT UserID, Name, Email, Role, EnrollmentNumber, EmployeeID, Phone FROM User";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();

            $html .= '<h1>' . $title . '</h1>';
            $html .= '<table border="1" cellpadding="5" cellspacing="0" style="width:100%; border-collapse: collapse;">';
            $html .= '<thead><tr><th>User ID</th><th>Name</th><th>Email</th><th>Role</th><th>Enrollment No.</th><th>Employee ID</th><th>Phone</th></tr></thead>';
            $html .= '<tbody>';
            while ($row = $result->fetch_assoc()) {
                $html .= '<tr>';
                $html .= '<td>' . htmlspecialchars($row['UserID']) . '</td>';
                $html .= '<td>' . htmlspecialchars($row['Name']) . '</td>';
                $html .= '<td>' . htmlspecialchars($row['Email']) . '</td>';
                $html .= '<td>' . htmlspecialchars($row['Role']) . '</td>';
                $html .= '<td>' . htmlspecialchars($row['EnrollmentNumber'] ?? '-') . '</td>';
                $html .= '<td>' . htmlspecialchars($row['EmployeeID'] ?? '-') . '</td>';
                $html .= '<td>' . htmlspecialchars($row['Phone']) . '</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody></table>';
            $stmt->close();
            break;

        case 'all_slots':
            $title = 'All Parking Slots Report';
            $sql = "SELECT SlotID, SlotNumber, SlotType, Status FROM ParkingSlot";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();

            $html .= '<h1>' . $title . '</h1>';
            $html .= '<table border="1" cellpadding="5" cellspacing="0" style="width:100%; border-collapse: collapse;">';
            $html .= '<thead><tr><th>Slot ID</th><th>Slot Number</th><th>Slot Type</th><th>Status</th></tr></thead>';
            $html .= '<tbody>';
            while ($row = $result->fetch_assoc()) {
                $html .= '<tr>';
                $html .= '<td>' . htmlspecialchars($row['SlotID']) . '</td>';
                $html .= '<td>' . htmlspecialchars($row['SlotNumber']) . '</td>';
                $html .= '<td>' . htmlspecialchars($row['SlotType']) . '</td>';
                $html .= '<td>' . htmlspecialchars($row['Status']) . '</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody></table>';
            $stmt->close();
            break;

        case 'occupied_slots':
            $title = 'Occupied Parking Slots Report';
            $sql = "SELECT ps.SlotID, ps.SlotNumber, ps.SlotType, ps.Status, u.Name AS UserName, v.LicensePlate
                    FROM ParkingSlot ps
                    LEFT JOIN Reservation r ON ps.SlotID = r.SlotID AND r.Status = 'Active'
                    LEFT JOIN User u ON r.UserID = u.UserID
                    LEFT JOIN Vehicle v ON r.VehicleID = v.VehicleID
                    WHERE ps.Status = 'Occupied'";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();

            $html .= '<h1>' . $title . '</h1>';
            $html .= '<table border="1" cellpadding="5" cellspacing="0" style="width:100%; border-collapse: collapse;">';
            $html .= '<thead><tr><th>Slot ID</th><th>Slot Number</th><th>Slot Type</th><th>Status</th><th>Occupied By</th><th>Vehicle License Plate</th></tr></thead>';
            $html .= '<tbody>';
            while ($row = $result->fetch_assoc()) {
                $html .= '<tr>';
                $html .= '<td>' . htmlspecialchars($row['SlotID']) . '</td>';
                $html .= '<td>' . htmlspecialchars($row['SlotNumber']) . '</td>';
                $html .= '<td>' . htmlspecialchars($row['SlotType']) . '</td>';
                $html .= '<td>' . htmlspecialchars($row['Status']) . '</td>';
                $html .= '<td>' . htmlspecialchars($row['UserName'] ?? 'N/A') . '</td>'; // Show N/A if no user
                $html .= '<td>' . htmlspecialchars($row['LicensePlate'] ?? 'N/A') . '</td>'; // Show N/A if no vehicle
                $html .= '</tr>';
            }
            $html .= '</tbody></table>';
            $stmt->close();
            break;

        default:
            http_response_code(400);
            echo "Invalid report type specified.";
            exit;
    }

    $conn->close();

    // Setup Dompdf
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true); // Enable if you use external assets (like images from URLs)
    $dompdf = new Dompdf($options);

    // Load HTML to Dompdf
    $dompdf->loadHtml($html);

    // (Optional) Set paper size and orientation
    $dompdf->setPaper('A4', 'portrait');

    // Render the HTML as PDF
    $dompdf->render();

    // Output the generated PDF to Browser
    $filename = str_replace(' ', '_', strtolower($title)) . '_' . date('Ymd_His') . '.pdf';
    $dompdf->stream($filename, ["Attachment" => true]); // true = download, false = open in browser

} else {
    http_response_code(400);
    echo "Invalid request.";
}
?>