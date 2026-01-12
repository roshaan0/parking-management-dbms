-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 26, 2025 at 06:52 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bu_cpms`
--

-- --------------------------------------------------------

--
-- Table structure for table `parkingrecord`
--

CREATE TABLE `parkingrecord` (
  `RecordID` int(11) NOT NULL,
  `VehicleID` int(11) NOT NULL,
  `SlotID` int(11) NOT NULL,
  `EntryTime` datetime NOT NULL,
  `ExitTime` datetime DEFAULT NULL,
  `EntryBy` int(11) NOT NULL,
  `ExitBy` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parkingrequest`
--

CREATE TABLE `parkingrequest` (
  `RequestID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `SlotID` int(11) DEFAULT NULL,
  `RequestDate` date NOT NULL,
  `RequestTime` time NOT NULL,
  `Status` enum('Pending','Approved','Rejected','Cancelled') DEFAULT 'Pending',
  `RequestType` varchar(50) DEFAULT NULL,
  `Remarks` text DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parkingslot`
--

CREATE TABLE `parkingslot` (
  `SlotID` int(11) NOT NULL,
  `Zone` varchar(50) NOT NULL,
  `SlotNumber` varchar(50) NOT NULL,
  `SlotType` enum('Faculty','Student','Visitor','Disabled') NOT NULL,
  `IsOccupied` tinyint(1) DEFAULT 0,
  `AssignedTo` int(11) DEFAULT NULL,
  `Status` varchar(50) DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parkingslot`
--

INSERT INTO `parkingslot` (`SlotID`, `Zone`, `SlotNumber`, `SlotType`, `IsOccupied`, `AssignedTo`, `Status`) VALUES
(1, 'A', 'A01', 'Faculty', 0, NULL, 'Available'),
(2, 'A', 'A02', 'Faculty', 0, NULL, 'Available'),
(3, 'A', 'A03', 'Faculty', 0, NULL, 'Available'),
(4, 'B', 'B01', 'Student', 0, NULL, 'Available'),
(5, 'B', 'B02', 'Student', 0, NULL, 'Available'),
(6, 'B', 'B03', 'Student', 0, NULL, 'Available'),
(7, 'C', 'C01', 'Visitor', 0, NULL, 'Available'),
(8, 'C', 'C02', 'Visitor', 0, NULL, 'Available'),
(9, 'D', 'D01', 'Disabled', 0, NULL, 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `passwordresets`
--

CREATE TABLE `passwordresets` (
  `ID` int(11) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Token` varchar(64) NOT NULL,
  `ExpiresAt` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

CREATE TABLE `report` (
  `ReportID` int(11) NOT NULL,
  `GeneratedBy` int(11) NOT NULL,
  `ReportType` enum('Daily','Weekly','Monthly','Custom') NOT NULL,
  `DateGenerated` date NOT NULL,
  `Content` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reservation`
--

CREATE TABLE `reservation` (
  `ReservationID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `SlotID` int(11) NOT NULL,
  `VehicleID` int(11) NOT NULL,
  `ReservationStartTime` datetime NOT NULL,
  `ReservationEndTime` datetime DEFAULT NULL,
  `Status` varchar(50) NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `slotchangerequest`
--

CREATE TABLE `slotchangerequest` (
  `RequestID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `CurrentSlotID` int(11) NOT NULL,
  `RequestedSlotID` int(11) NOT NULL,
  `Reason` text DEFAULT NULL,
  `Status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `ReviewedBy` int(11) DEFAULT NULL,
  `RequestedAt` datetime DEFAULT current_timestamp(),
  `ReviewedAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `UserID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Role` enum('Student','Faculty','Admin','Security') NOT NULL,
  `EnrollmentNumber` varchar(50) DEFAULT NULL,
  `EmployeeID` varchar(50) DEFAULT NULL,
  `Phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`UserID`, `Name`, `Email`, `Password`, `Role`, `EnrollmentNumber`, `EmployeeID`, `Phone`) VALUES
(1, 'Mehar Ali Musa', 'fcvenom195@gmail.com', '$2y$10$cd56sscKv2Aq3DNmowI.bealdBivc1wI08AoA/Dm7mNOVqO4SPV1m', 'Student', NULL, NULL, '03008588152'),
(4, 'Ezan Aslam', 'ezan1312@gmail.com', '$2y$10$BGf.1ne1DLMdix8DWupUfuWa9tPf3TwA35I8wGr6rTlATJDGRXTmi', 'Student', NULL, NULL, '3318588152'),
(5, 'Taha Adeel ', 'taha@gmail.com', '$2y$10$XXGlLtYBSTLiY2T1mKvjqOiWOfULv35Gj1Dy63Hdsb1PkHlmb/laq', 'Student', NULL, NULL, '2210910'),
(14, 'security', 'security1212@gmail.com', '$2y$10$QpYus4a.HXUDkyNvVc26wOJYMlYYyw/UaIOf0nMpn5v9UK9fSZzVC', 'Security', NULL, NULL, NULL),
(19, 'Lamine Yamal', 'lamineyamal@gmail.com', '$2y$10$6UszGPIyRENJKV4AgzL0OeTpNB8bahRy6oHMxWopz37xTV4ZxBLEm', 'Admin', NULL, '01-134232-097', '03018588157');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','faculty','security','admin') DEFAULT 'student',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `password`, `role`, `created_at`) VALUES
(1, 'Mehar Ali Musa', 'fcvenom195@gmail.com', '3008588152', '$2y$10$O8.by1NDw..WSMIobKRBAO5hnfQbV/XPO7xYbb4tbDkIn952uyTq.', 'student', '2025-05-25 07:53:31');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle`
--

CREATE TABLE `vehicle` (
  `VehicleID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `LicensePlate` varchar(50) NOT NULL,
  `VehicleType` enum('Car','Bike') NOT NULL,
  `StickerNumber` varchar(50) DEFAULT NULL,
  `IsDefault` tinyint(1) DEFAULT 0,
  `RegistrationDate` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `visitorticket`
--

CREATE TABLE `visitorticket` (
  `TicketID` int(11) NOT NULL,
  `LicensePlate` varchar(15) NOT NULL,
  `EntryTime` datetime NOT NULL,
  `ExitTime` datetime DEFAULT NULL,
  `IssuedBy` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `parkingrecord`
--
ALTER TABLE `parkingrecord`
  ADD PRIMARY KEY (`RecordID`),
  ADD KEY `VehicleID` (`VehicleID`),
  ADD KEY `SlotID` (`SlotID`),
  ADD KEY `EntryBy` (`EntryBy`),
  ADD KEY `ExitBy` (`ExitBy`);

--
-- Indexes for table `parkingrequest`
--
ALTER TABLE `parkingrequest`
  ADD PRIMARY KEY (`RequestID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `SlotID` (`SlotID`);

--
-- Indexes for table `parkingslot`
--
ALTER TABLE `parkingslot`
  ADD PRIMARY KEY (`SlotID`),
  ADD UNIQUE KEY `Zone` (`Zone`,`SlotNumber`),
  ADD KEY `AssignedTo` (`AssignedTo`);

--
-- Indexes for table `passwordresets`
--
ALTER TABLE `passwordresets`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Token` (`Token`);

--
-- Indexes for table `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`ReportID`),
  ADD KEY `GeneratedBy` (`GeneratedBy`);

--
-- Indexes for table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`ReservationID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `SlotID` (`SlotID`),
  ADD KEY `VehicleID` (`VehicleID`);

--
-- Indexes for table `slotchangerequest`
--
ALTER TABLE `slotchangerequest`
  ADD PRIMARY KEY (`RequestID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `CurrentSlotID` (`CurrentSlotID`),
  ADD KEY `RequestedSlotID` (`RequestedSlotID`),
  ADD KEY `ReviewedBy` (`ReviewedBy`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `vehicle`
--
ALTER TABLE `vehicle`
  ADD PRIMARY KEY (`VehicleID`),
  ADD UNIQUE KEY `LicensePlate` (`LicensePlate`),
  ADD UNIQUE KEY `StickerNumber` (`StickerNumber`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `visitorticket`
--
ALTER TABLE `visitorticket`
  ADD PRIMARY KEY (`TicketID`),
  ADD KEY `IssuedBy` (`IssuedBy`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `parkingrecord`
--
ALTER TABLE `parkingrecord`
  MODIFY `RecordID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `parkingrequest`
--
ALTER TABLE `parkingrequest`
  MODIFY `RequestID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `parkingslot`
--
ALTER TABLE `parkingslot`
  MODIFY `SlotID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `passwordresets`
--
ALTER TABLE `passwordresets`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `report`
--
ALTER TABLE `report`
  MODIFY `ReportID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `ReservationID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `slotchangerequest`
--
ALTER TABLE `slotchangerequest`
  MODIFY `RequestID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `vehicle`
--
ALTER TABLE `vehicle`
  MODIFY `VehicleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `visitorticket`
--
ALTER TABLE `visitorticket`
  MODIFY `TicketID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `parkingrecord`
--
ALTER TABLE `parkingrecord`
  ADD CONSTRAINT `parkingrecord_ibfk_1` FOREIGN KEY (`VehicleID`) REFERENCES `vehicle` (`VehicleID`) ON DELETE CASCADE,
  ADD CONSTRAINT `parkingrecord_ibfk_2` FOREIGN KEY (`SlotID`) REFERENCES `parkingslot` (`SlotID`) ON DELETE CASCADE,
  ADD CONSTRAINT `parkingrecord_ibfk_3` FOREIGN KEY (`EntryBy`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `parkingrecord_ibfk_4` FOREIGN KEY (`ExitBy`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `parkingrequest`
--
ALTER TABLE `parkingrequest`
  ADD CONSTRAINT `parkingrequest_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `parkingrequest_ibfk_2` FOREIGN KEY (`SlotID`) REFERENCES `parkingslot` (`SlotID`);

--
-- Constraints for table `parkingslot`
--
ALTER TABLE `parkingslot`
  ADD CONSTRAINT `parkingslot_ibfk_1` FOREIGN KEY (`AssignedTo`) REFERENCES `user` (`UserID`) ON DELETE SET NULL;

--
-- Constraints for table `report`
--
ALTER TABLE `report`
  ADD CONSTRAINT `report_ibfk_1` FOREIGN KEY (`GeneratedBy`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `reservation_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `reservation_ibfk_2` FOREIGN KEY (`SlotID`) REFERENCES `parkingslot` (`SlotID`),
  ADD CONSTRAINT `reservation_ibfk_3` FOREIGN KEY (`VehicleID`) REFERENCES `vehicle` (`VehicleID`);

--
-- Constraints for table `slotchangerequest`
--
ALTER TABLE `slotchangerequest`
  ADD CONSTRAINT `slotchangerequest_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE,
  ADD CONSTRAINT `slotchangerequest_ibfk_2` FOREIGN KEY (`CurrentSlotID`) REFERENCES `parkingslot` (`SlotID`),
  ADD CONSTRAINT `slotchangerequest_ibfk_3` FOREIGN KEY (`RequestedSlotID`) REFERENCES `parkingslot` (`SlotID`),
  ADD CONSTRAINT `slotchangerequest_ibfk_4` FOREIGN KEY (`ReviewedBy`) REFERENCES `user` (`UserID`) ON DELETE SET NULL;

--
-- Constraints for table `vehicle`
--
ALTER TABLE `vehicle`
  ADD CONSTRAINT `vehicle_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `visitorticket`
--
ALTER TABLE `visitorticket`
  ADD CONSTRAINT `visitorticket_ibfk_1` FOREIGN KEY (`IssuedBy`) REFERENCES `user` (`UserID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
