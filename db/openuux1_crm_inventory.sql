-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 17, 2023 at 01:57 PM
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
-- Database: `openuux1_crm_inventory`
--

-- --------------------------------------------------------

--
-- Table structure for table `inward_outward_reports`
--

CREATE TABLE `inward_outward_reports` (
  `InwardOutwardReportID` int(11) NOT NULL,
  `CompID` int(11) NOT NULL,
  `Item` varchar(200) NOT NULL,
  `HSN` varchar(10) DEFAULT NULL,
  `OpeningStockQty` int(11) NOT NULL DEFAULT 0,
  `InwardStockQty` int(11) NOT NULL DEFAULT 0,
  `OutwardStockQty` int(11) NOT NULL DEFAULT 0,
  `ClosingStockQty` int(11) NOT NULL DEFAULT 0,
  `ReportDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `ItemID` int(11) NOT NULL,
  `CompID` int(11) NOT NULL,
  `Item` text NOT NULL,
  `ItemCategoryMasterID` int(11) DEFAULT NULL,
  `ItemType` enum('Good','Service') NOT NULL,
  `BarcodeNo` varchar(20) DEFAULT NULL,
  `BuyingPrice` decimal(10,2) DEFAULT NULL,
  `Price` decimal(10,2) NOT NULL,
  `HSN` varchar(20) NOT NULL,
  `Qty` int(11) NOT NULL DEFAULT 0,
  `AddedBy` int(11) DEFAULT NULL,
  `AddedDate` datetime NOT NULL,
  `UpdatedBy` int(11) DEFAULT NULL,
  `UpdatedDate` datetime DEFAULT NULL,
  `IsDeleted` tinyint(1) NOT NULL DEFAULT 0,
  `DeletedBy` int(11) DEFAULT NULL,
  `DeletedDate` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `item_category_master`
--

CREATE TABLE `item_category_master` (
  `ItemCategoryMasterID` int(11) NOT NULL,
  `ItemCategory` varchar(200) NOT NULL,
  `CompID` int(11) DEFAULT NULL,
  `AddedBy` int(11) DEFAULT NULL,
  `AddedDate` datetime DEFAULT NULL,
  `UpdatedBy` int(11) DEFAULT NULL,
  `UpdatedDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `item_category_master`
--

INSERT INTO `item_category_master` (`ItemCategoryMasterID`, `ItemCategory`, `CompID`, `AddedBy`, `AddedDate`, `UpdatedBy`, `UpdatedDate`) VALUES
(1, 'ABC', 1, 1, '2023-02-02 23:49:58', 1, '2023-05-03 09:05:05'),
(2, 'Electronics', 1, 1, '2023-03-04 19:56:38', NULL, NULL),
(4, 'HGT', 1, 1, '2023-05-07 18:35:49', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `item_taxes`
--

CREATE TABLE `item_taxes` (
  `ItemTaxID` int(11) NOT NULL,
  `ItemID` int(11) NOT NULL,
  `Tax` varchar(25) NOT NULL,
  `TaxPercentage` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `returned_expiring_stocks`
--

CREATE TABLE `returned_expiring_stocks` (
  `ReturnedExpiringStockID` int(11) NOT NULL,
  `CompID` int(11) NOT NULL,
  `Item` text NOT NULL,
  `Vendor` varchar(250) DEFAULT NULL,
  `BatchNo` varchar(10) DEFAULT NULL,
  `UnitsReturned` int(11) NOT NULL,
  `ReturnDate` date NOT NULL,
  `VendorRepresentativeName` varchar(100) NOT NULL,
  `VendorRepresentativeEmail` varchar(254) NOT NULL,
  `AddedBy` int(11) DEFAULT NULL,
  `AddedDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_inward_logs`
--

CREATE TABLE `stock_inward_logs` (
  `StockInwardHistoryID` int(11) NOT NULL,
  `CompID` int(11) NOT NULL,
  `VendorID` int(11) DEFAULT NULL,
  `Item` varchar(250) NOT NULL,
  `HSN` varchar(10) DEFAULT NULL,
  `BatchNo` varchar(10) DEFAULT NULL,
  `BuyingPricePerUnit` decimal(10,2) NOT NULL,
  `Qty` int(11) NOT NULL,
  `RemainingQty` int(11) DEFAULT NULL,
  `InwardDate` date NOT NULL,
  `InvoiceNo` varchar(200) DEFAULT NULL,
  `ManufacturingDate` date DEFAULT NULL,
  `ExpiryDate` date DEFAULT NULL,
  `ExpiryReminderDate` date DEFAULT NULL,
  `NextReminderDate` date DEFAULT NULL,
  `AddedBy` int(11) DEFAULT NULL,
  `AddedDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_inward_taxes`
--

CREATE TABLE `stock_inward_taxes` (
  `StockInwardTaxID` int(11) NOT NULL,
  `StockInwardHistoryID` int(11) NOT NULL,
  `Tax` varchar(100) NOT NULL,
  `TaxPercentage` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `inward_outward_reports`
--
ALTER TABLE `inward_outward_reports`
  ADD PRIMARY KEY (`InwardOutwardReportID`),
  ADD KEY `CompID` (`CompID`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`ItemID`),
  ADD UNIQUE KEY `CompBarcode` (`CompID`,`BarcodeNo`) USING BTREE,
  ADD KEY `CompID` (`CompID`),
  ADD KEY `UpdatedBy` (`UpdatedBy`),
  ADD KEY `DeletedBy` (`DeletedBy`),
  ADD KEY `AddedBy` (`AddedBy`),
  ADD KEY `ItemCategoryMasterID` (`ItemCategoryMasterID`);

--
-- Indexes for table `item_category_master`
--
ALTER TABLE `item_category_master`
  ADD PRIMARY KEY (`ItemCategoryMasterID`),
  ADD KEY `CompID` (`CompID`),
  ADD KEY `AddedBy` (`AddedBy`),
  ADD KEY `UpdatedBy` (`UpdatedBy`);

--
-- Indexes for table `item_taxes`
--
ALTER TABLE `item_taxes`
  ADD PRIMARY KEY (`ItemTaxID`),
  ADD KEY `ItemID` (`ItemID`);

--
-- Indexes for table `returned_expiring_stocks`
--
ALTER TABLE `returned_expiring_stocks`
  ADD PRIMARY KEY (`ReturnedExpiringStockID`),
  ADD KEY `AddedBy` (`AddedBy`),
  ADD KEY `CompID` (`CompID`);

--
-- Indexes for table `stock_inward_logs`
--
ALTER TABLE `stock_inward_logs`
  ADD PRIMARY KEY (`StockInwardHistoryID`),
  ADD UNIQUE KEY `BatchNo_Composite` (`CompID`,`VendorID`,`Item`,`BatchNo`) USING BTREE,
  ADD KEY `AddedBy` (`AddedBy`),
  ADD KEY `CompID` (`CompID`),
  ADD KEY `VendorID` (`VendorID`);

--
-- Indexes for table `stock_inward_taxes`
--
ALTER TABLE `stock_inward_taxes`
  ADD PRIMARY KEY (`StockInwardTaxID`),
  ADD KEY `StockInwardHistoryID` (`StockInwardHistoryID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `inward_outward_reports`
--
ALTER TABLE `inward_outward_reports`
  MODIFY `InwardOutwardReportID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `ItemID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `item_category_master`
--
ALTER TABLE `item_category_master`
  MODIFY `ItemCategoryMasterID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `item_taxes`
--
ALTER TABLE `item_taxes`
  MODIFY `ItemTaxID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `returned_expiring_stocks`
--
ALTER TABLE `returned_expiring_stocks`
  MODIFY `ReturnedExpiringStockID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_inward_logs`
--
ALTER TABLE `stock_inward_logs`
  MODIFY `StockInwardHistoryID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_inward_taxes`
--
ALTER TABLE `stock_inward_taxes`
  MODIFY `StockInwardTaxID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `inward_outward_reports`
--
ALTER TABLE `inward_outward_reports`
  ADD CONSTRAINT `inward_outward_reports_ibfk_1` FOREIGN KEY (`CompID`) REFERENCES `openuux1_crm_core`.`company_master` (`CompID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `items_ibfk_1` FOREIGN KEY (`CompID`) REFERENCES `openuux1_crm_core`.`company_master` (`CompID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `items_ibfk_2` FOREIGN KEY (`AddedBy`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `items_ibfk_3` FOREIGN KEY (`UpdatedBy`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `items_ibfk_4` FOREIGN KEY (`DeletedBy`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `items_ibfk_5` FOREIGN KEY (`ItemCategoryMasterID`) REFERENCES `item_category_master` (`ItemCategoryMasterID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `item_category_master`
--
ALTER TABLE `item_category_master`
  ADD CONSTRAINT `item_category_master_ibfk_1` FOREIGN KEY (`CompID`) REFERENCES `openuux1_crm_core`.`company_master` (`CompID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `item_category_master_ibfk_2` FOREIGN KEY (`AddedBy`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `item_category_master_ibfk_3` FOREIGN KEY (`UpdatedBy`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `item_taxes`
--
ALTER TABLE `item_taxes`
  ADD CONSTRAINT `item_taxes_ibfk_1` FOREIGN KEY (`ItemID`) REFERENCES `items` (`ItemID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `returned_expiring_stocks`
--
ALTER TABLE `returned_expiring_stocks`
  ADD CONSTRAINT `returned_expiring_stocks_ibfk_1` FOREIGN KEY (`AddedBy`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `returned_expiring_stocks_ibfk_2` FOREIGN KEY (`CompID`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `stock_inward_logs`
--
ALTER TABLE `stock_inward_logs`
  ADD CONSTRAINT `stock_inward_logs_ibfk_1` FOREIGN KEY (`VendorID`) REFERENCES `openuux1_crm_core`.`vendors` (`VendorID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `stock_inward_logs_ibfk_2` FOREIGN KEY (`CompID`) REFERENCES `openuux1_crm_core`.`company_master` (`CompID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `stock_inward_logs_ibfk_3` FOREIGN KEY (`AddedBy`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `stock_inward_taxes`
--
ALTER TABLE `stock_inward_taxes`
  ADD CONSTRAINT `stock_inward_taxes_ibfk_1` FOREIGN KEY (`StockInwardHistoryID`) REFERENCES `stock_inward_logs` (`StockInwardHistoryID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
