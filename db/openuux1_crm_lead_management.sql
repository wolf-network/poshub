-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 17, 2023 at 01:50 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `openuux1_crm_lead_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `call_logs`
--

CREATE TABLE `call_logs` (
  `CallLogID` int(11) NOT NULL,
  `LeadID` int(11) NOT NULL,
  `LeadName` varchar(150) NOT NULL,
  `CallDate` datetime NOT NULL,
  `CallType` enum('Incoming','Outgoing') NOT NULL,
  `CallStatusID` int(11) DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `AddedBy` int(11) DEFAULT NULL,
  `AddedDate` datetime DEFAULT NULL,
  `UpdatedBy` int(11) DEFAULT NULL,
  `UpdatedDate` datetime DEFAULT NULL,
  `IsDeleted` tinyint(1) NOT NULL DEFAULT 0,
  `DeletedBy` int(11) DEFAULT NULL,
  `DeletedDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `call_status_master`
--

CREATE TABLE `call_status_master` (
  `CallStatusID` int(11) NOT NULL,
  `CallStatus` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `call_status_master`
--

INSERT INTO `call_status_master` (`CallStatusID`, `CallStatus`) VALUES
(1, 'Under Process'),
(2, 'Call Next Time'),
(3, 'Busy or not answering'),
(4, 'Not Interested'),
(5, 'Success'),
(6, 'Other');

-- --------------------------------------------------------

--
-- Table structure for table `leads`
--

CREATE TABLE `leads` (
  `LeadID` int(11) NOT NULL,
  `ContactName` varchar(100) DEFAULT NULL,
  `BusinessName` varchar(200) DEFAULT NULL,
  `SourceID` int(11) DEFAULT NULL,
  `RoleID` int(11) DEFAULT NULL,
  `ContactNo` varchar(15) DEFAULT NULL,
  `EmailID` varchar(254) DEFAULT NULL,
  `Website` text DEFAULT NULL,
  `CountryID` int(11) DEFAULT NULL,
  `StateID` int(11) DEFAULT NULL,
  `CityID` int(11) DEFAULT NULL,
  `Address` text DEFAULT NULL,
  `BusinessIndustryID` int(11) DEFAULT NULL,
  `LeadStatusID` int(11) DEFAULT NULL,
  `Requirement` text NOT NULL,
  `LeadValue` decimal(10,2) NOT NULL,
  `CompID` int(11) DEFAULT NULL,
  `LeadFormID` int(11) DEFAULT NULL,
  `LeadAssignedBy` int(11) DEFAULT NULL,
  `LeadAssignedTo` int(11) DEFAULT NULL,
  `LeadAssignedDate` datetime NOT NULL,
  `AddedBy` int(11) DEFAULT NULL,
  `AddedDate` datetime NOT NULL,
  `UpdatedBy` int(11) DEFAULT NULL,
  `UpdatedDate` datetime DEFAULT NULL,
  `IsDeleted` tinyint(1) NOT NULL DEFAULT 0,
  `DeletedBy` int(11) DEFAULT NULL,
  `DeletedDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lead_assigned_logs`
--

CREATE TABLE `lead_assigned_logs` (
  `LeadAssignedLogID` int(11) NOT NULL,
  `LeadID` int(11) NOT NULL,
  `LeadAssignedBy` int(11) DEFAULT NULL,
  `LeadAssignedTo` int(11) DEFAULT NULL,
  `LeadAssignedDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lead_form`
--

CREATE TABLE `lead_form` (
  `LeadFormID` int(11) NOT NULL,
  `FormName` varchar(150) NOT NULL,
  `ExpiryDate` date DEFAULT NULL,
  `CompID` int(11) NOT NULL,
  `FormKey` varchar(100) NOT NULL,
  `AddedBy` int(11) DEFAULT NULL,
  `AddedDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lead_form_data`
--

CREATE TABLE `lead_form_data` (
  `LeadFormDataID` int(11) NOT NULL,
  `LeadFormQuestionID` int(11) NOT NULL,
  `LeadID` int(11) NOT NULL,
  `Data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lead_form_questions`
--

CREATE TABLE `lead_form_questions` (
  `LeadFormQuestionID` int(11) NOT NULL,
  `LeadFormID` int(11) NOT NULL,
  `Question` varchar(100) NOT NULL,
  `Mandatory` enum('yes','no') NOT NULL DEFAULT 'no'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lead_logs`
--

CREATE TABLE `lead_logs` (
  `LeadLogID` int(11) NOT NULL,
  `LeadID` int(11) NOT NULL,
  `LeadStatusID` int(11) DEFAULT NULL,
  `Comment` text NOT NULL,
  `AddedBy` int(11) DEFAULT NULL,
  `AddedDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lead_status_master`
--

CREATE TABLE `lead_status_master` (
  `LeadStatusID` int(11) NOT NULL,
  `LeadStatus` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lead_status_master`
--

INSERT INTO `lead_status_master` (`LeadStatusID`, `LeadStatus`) VALUES
(1, 'Cold'),
(2, 'Warm'),
(3, 'Hot'),
(7, 'Not Interested'),
(8, 'Closed');

-- --------------------------------------------------------

--
-- Table structure for table `meeting_location_master`
--

CREATE TABLE `meeting_location_master` (
  `MeetingLocationID` int(11) NOT NULL,
  `MeetingLocation` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `meeting_location_master`
--

INSERT INTO `meeting_location_master` (`MeetingLocationID`, `MeetingLocation`) VALUES
(1, 'Virtual'),
(2, 'Client\'s office'),
(3, 'Our office');

-- --------------------------------------------------------

--
-- Table structure for table `meeting_logs`
--

CREATE TABLE `meeting_logs` (
  `MeetingLogID` int(11) NOT NULL,
  `LeadID` int(11) NOT NULL,
  `LeadName` varchar(150) NOT NULL,
  `MeetingDate` datetime NOT NULL,
  `MeetingLocationID` int(11) DEFAULT NULL,
  `MeetingStatusID` int(11) DEFAULT NULL,
  `Description` text NOT NULL,
  `AddedBy` int(11) DEFAULT NULL,
  `AddedDate` datetime NOT NULL,
  `UpdatedBy` int(11) DEFAULT NULL,
  `UpdatedDate` datetime DEFAULT NULL,
  `IsDeleted` tinyint(1) NOT NULL DEFAULT 0,
  `DeletedBy` int(11) DEFAULT NULL,
  `DeletedDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meeting_status_master`
--

CREATE TABLE `meeting_status_master` (
  `MeetingStatusID` int(11) NOT NULL,
  `MeetingStatus` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `registered_user_notifications`
--

CREATE TABLE `registered_user_notifications` (
  `RegisteredUserNotificationID` int(11) NOT NULL,
  `RegisteredUserID` int(11) NOT NULL,
  `NotifierRegisteredUserID` int(11) NOT NULL,
  `Notification` text NOT NULL,
  `RedirectURL` text DEFAULT NULL,
  `NotificationDate` datetime NOT NULL,
  `NotificationReadDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `source_master`
--

CREATE TABLE `source_master` (
  `SourceID` int(11) NOT NULL,
  `Source` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `source_master`
--

INSERT INTO `source_master` (`SourceID`, `Source`) VALUES
(3, 'Facebook'),
(5, 'Google'),
(4, 'Instagram'),
(11, 'Lead Form'),
(6, 'LinkedIn'),
(9, 'Manual'),
(8, 'Other'),
(2, 'Reference'),
(7, 'Twitter'),
(1, 'Website'),
(10, 'Whatsapp');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `call_logs`
--
ALTER TABLE `call_logs`
  ADD PRIMARY KEY (`CallLogID`),
  ADD KEY `LeadID` (`LeadID`),
  ADD KEY `CallStatus` (`CallStatusID`),
  ADD KEY `AddedBy` (`AddedBy`),
  ADD KEY `UpdatedBy` (`UpdatedBy`),
  ADD KEY `DeletedBy` (`DeletedBy`);

--
-- Indexes for table `call_status_master`
--
ALTER TABLE `call_status_master`
  ADD PRIMARY KEY (`CallStatusID`);

--
-- Indexes for table `leads`
--
ALTER TABLE `leads`
  ADD PRIMARY KEY (`LeadID`),
  ADD KEY `SourceID` (`SourceID`),
  ADD KEY `BusinessIndustryID` (`BusinessIndustryID`),
  ADD KEY `LeadStatusID` (`LeadStatusID`),
  ADD KEY `AddedBy` (`AddedBy`),
  ADD KEY `DeletedBy` (`DeletedBy`),
  ADD KEY `CountryID` (`CountryID`),
  ADD KEY `StateID` (`StateID`),
  ADD KEY `CityID` (`CityID`),
  ADD KEY `LeadAssignedTo` (`LeadAssignedTo`),
  ADD KEY `UpdatedBy` (`UpdatedBy`),
  ADD KEY `LeadAssignedBy` (`LeadAssignedBy`),
  ADD KEY `RoleID` (`RoleID`),
  ADD KEY `LeadFormID` (`LeadFormID`),
  ADD KEY `CompID` (`CompID`);

--
-- Indexes for table `lead_assigned_logs`
--
ALTER TABLE `lead_assigned_logs`
  ADD PRIMARY KEY (`LeadAssignedLogID`),
  ADD KEY `LeadAssignedBy` (`LeadAssignedBy`),
  ADD KEY `LeadAssignedTo` (`LeadAssignedTo`),
  ADD KEY `LeadID` (`LeadID`);

--
-- Indexes for table `lead_form`
--
ALTER TABLE `lead_form`
  ADD PRIMARY KEY (`LeadFormID`),
  ADD KEY `AddedBy` (`AddedBy`),
  ADD KEY `CompID` (`CompID`);

--
-- Indexes for table `lead_form_data`
--
ALTER TABLE `lead_form_data`
  ADD PRIMARY KEY (`LeadFormDataID`),
  ADD KEY `LeadFormQuestionID` (`LeadFormQuestionID`),
  ADD KEY `LeadID` (`LeadID`);

--
-- Indexes for table `lead_form_questions`
--
ALTER TABLE `lead_form_questions`
  ADD PRIMARY KEY (`LeadFormQuestionID`),
  ADD KEY `lead_form_questions_ibfk_1` (`LeadFormID`);

--
-- Indexes for table `lead_logs`
--
ALTER TABLE `lead_logs`
  ADD PRIMARY KEY (`LeadLogID`),
  ADD KEY `LeadStatusID` (`LeadStatusID`),
  ADD KEY `AddedBy` (`AddedBy`);

--
-- Indexes for table `lead_status_master`
--
ALTER TABLE `lead_status_master`
  ADD PRIMARY KEY (`LeadStatusID`);

--
-- Indexes for table `meeting_location_master`
--
ALTER TABLE `meeting_location_master`
  ADD PRIMARY KEY (`MeetingLocationID`);

--
-- Indexes for table `meeting_logs`
--
ALTER TABLE `meeting_logs`
  ADD PRIMARY KEY (`MeetingLogID`),
  ADD KEY `LeadID` (`LeadID`),
  ADD KEY `AddedBy` (`AddedBy`),
  ADD KEY `UpdatedBy` (`UpdatedBy`),
  ADD KEY `DeletedBy` (`DeletedBy`),
  ADD KEY `MeetingStatusID` (`MeetingStatusID`),
  ADD KEY `MeetingTypeID` (`MeetingLocationID`);

--
-- Indexes for table `meeting_status_master`
--
ALTER TABLE `meeting_status_master`
  ADD PRIMARY KEY (`MeetingStatusID`);

--
-- Indexes for table `registered_user_notifications`
--
ALTER TABLE `registered_user_notifications`
  ADD PRIMARY KEY (`RegisteredUserNotificationID`),
  ADD KEY `EmployeeID` (`RegisteredUserID`),
  ADD KEY `NotifierEmployeeID` (`NotifierRegisteredUserID`);

--
-- Indexes for table `source_master`
--
ALTER TABLE `source_master`
  ADD PRIMARY KEY (`SourceID`),
  ADD UNIQUE KEY `Source` (`Source`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `call_logs`
--
ALTER TABLE `call_logs`
  MODIFY `CallLogID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `call_status_master`
--
ALTER TABLE `call_status_master`
  MODIFY `CallStatusID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `leads`
--
ALTER TABLE `leads`
  MODIFY `LeadID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lead_assigned_logs`
--
ALTER TABLE `lead_assigned_logs`
  MODIFY `LeadAssignedLogID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lead_form`
--
ALTER TABLE `lead_form`
  MODIFY `LeadFormID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lead_form_data`
--
ALTER TABLE `lead_form_data`
  MODIFY `LeadFormDataID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lead_form_questions`
--
ALTER TABLE `lead_form_questions`
  MODIFY `LeadFormQuestionID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lead_logs`
--
ALTER TABLE `lead_logs`
  MODIFY `LeadLogID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lead_status_master`
--
ALTER TABLE `lead_status_master`
  MODIFY `LeadStatusID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `meeting_location_master`
--
ALTER TABLE `meeting_location_master`
  MODIFY `MeetingLocationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `meeting_logs`
--
ALTER TABLE `meeting_logs`
  MODIFY `MeetingLogID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `meeting_status_master`
--
ALTER TABLE `meeting_status_master`
  MODIFY `MeetingStatusID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `registered_user_notifications`
--
ALTER TABLE `registered_user_notifications`
  MODIFY `RegisteredUserNotificationID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `source_master`
--
ALTER TABLE `source_master`
  MODIFY `SourceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `call_logs`
--
ALTER TABLE `call_logs`
  ADD CONSTRAINT `call_logs_ibfk_1` FOREIGN KEY (`LeadID`) REFERENCES `leads` (`LeadID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `call_logs_ibfk_2` FOREIGN KEY (`CallStatusID`) REFERENCES `call_status_master` (`CallStatusID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `call_logs_ibfk_3` FOREIGN KEY (`AddedBy`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `call_logs_ibfk_4` FOREIGN KEY (`UpdatedBy`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `call_logs_ibfk_5` FOREIGN KEY (`DeletedBy`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `leads`
--
ALTER TABLE `leads`
  ADD CONSTRAINT `leads_ibfk_1` FOREIGN KEY (`SourceID`) REFERENCES `source_master` (`SourceID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `leads_ibfk_10` FOREIGN KEY (`LeadAssignedTo`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `leads_ibfk_11` FOREIGN KEY (`UpdatedBy`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `leads_ibfk_12` FOREIGN KEY (`LeadAssignedBy`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `leads_ibfk_13` FOREIGN KEY (`RoleID`) REFERENCES `openuux1_crm_core`.`roles_master` (`RoleID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `leads_ibfk_14` FOREIGN KEY (`LeadFormID`) REFERENCES `lead_form` (`LeadFormID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `leads_ibfk_15` FOREIGN KEY (`CompID`) REFERENCES `openuux1_crm_core`.`company_master` (`CompID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `leads_ibfk_2` FOREIGN KEY (`BusinessIndustryID`) REFERENCES `openuux1_crm_core`.`business_industry_master` (`BusinessIndustryID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `leads_ibfk_3` FOREIGN KEY (`LeadStatusID`) REFERENCES `lead_status_master` (`LeadStatusID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `leads_ibfk_4` FOREIGN KEY (`AddedBy`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `leads_ibfk_5` FOREIGN KEY (`DeletedBy`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `leads_ibfk_6` FOREIGN KEY (`CountryID`) REFERENCES `openuux1_crm_core`.`countries_master` (`CountryID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `leads_ibfk_7` FOREIGN KEY (`StateID`) REFERENCES `openuux1_crm_core`.`states_master` (`StateID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `leads_ibfk_8` FOREIGN KEY (`CityID`) REFERENCES `openuux1_crm_core`.`cities_master` (`CityID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `lead_assigned_logs`
--
ALTER TABLE `lead_assigned_logs`
  ADD CONSTRAINT `lead_assigned_logs_ibfk_1` FOREIGN KEY (`LeadAssignedBy`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `lead_assigned_logs_ibfk_2` FOREIGN KEY (`LeadAssignedTo`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `lead_assigned_logs_ibfk_3` FOREIGN KEY (`LeadID`) REFERENCES `leads` (`LeadID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lead_form`
--
ALTER TABLE `lead_form`
  ADD CONSTRAINT `lead_form_ibfk_1` FOREIGN KEY (`AddedBy`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `lead_form_ibfk_2` FOREIGN KEY (`CompID`) REFERENCES `openuux1_crm_core`.`company_master` (`CompID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lead_form_data`
--
ALTER TABLE `lead_form_data`
  ADD CONSTRAINT `lead_form_data_ibfk_1` FOREIGN KEY (`LeadFormQuestionID`) REFERENCES `lead_form_questions` (`LeadFormQuestionID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `lead_form_data_ibfk_2` FOREIGN KEY (`LeadID`) REFERENCES `leads` (`LeadID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lead_form_questions`
--
ALTER TABLE `lead_form_questions`
  ADD CONSTRAINT `lead_form_questions_ibfk_1` FOREIGN KEY (`LeadFormID`) REFERENCES `lead_form` (`LeadFormID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lead_logs`
--
ALTER TABLE `lead_logs`
  ADD CONSTRAINT `lead_logs_ibfk_1` FOREIGN KEY (`LeadStatusID`) REFERENCES `lead_status_master` (`LeadStatusID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `lead_logs_ibfk_2` FOREIGN KEY (`AddedBy`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `meeting_logs`
--
ALTER TABLE `meeting_logs`
  ADD CONSTRAINT `meeting_logs_ibfk_1` FOREIGN KEY (`LeadID`) REFERENCES `leads` (`LeadID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `meeting_logs_ibfk_2` FOREIGN KEY (`AddedBy`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `meeting_logs_ibfk_3` FOREIGN KEY (`UpdatedBy`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `meeting_logs_ibfk_4` FOREIGN KEY (`DeletedBy`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `meeting_logs_ibfk_5` FOREIGN KEY (`MeetingStatusID`) REFERENCES `meeting_status_master` (`MeetingStatusID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `meeting_logs_ibfk_6` FOREIGN KEY (`MeetingLocationID`) REFERENCES `meeting_location_master` (`MeetingLocationID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `registered_user_notifications`
--
ALTER TABLE `registered_user_notifications`
  ADD CONSTRAINT `registered_user_notifications_ibfk_1` FOREIGN KEY (`RegisteredUserID`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `registered_user_notifications_ibfk_2` FOREIGN KEY (`NotifierRegisteredUserID`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
