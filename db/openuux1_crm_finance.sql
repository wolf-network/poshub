-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 17, 2023 at 01:54 PM
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
-- Database: `openuux1_crm_finance`
--

-- --------------------------------------------------------

--
-- Table structure for table `client_service_tax_type_mapper`
--

CREATE TABLE `client_service_tax_type_mapper` (
  `ClientServiceTaxID` int(11) NOT NULL,
  `Label` varchar(50) NOT NULL,
  `ClientID` int(11) NOT NULL,
  `ServiceTaxTypeID` int(11) NOT NULL,
  `ServiceTaxNumber` varchar(30) NOT NULL,
  `BillingCountryID` int(11) DEFAULT NULL,
  `BillingStateID` int(11) DEFAULT NULL,
  `BillingAddress` text NOT NULL,
  `AddedBy` int(11) DEFAULT NULL,
  `AddedDate` datetime NOT NULL,
  `UpdatedBy` int(11) DEFAULT NULL,
  `UpdatedDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `company_service_tax_master`
--

CREATE TABLE `company_service_tax_master` (
  `CompanyServiceTaxMasterID` int(11) NOT NULL,
  `CompID` int(11) NOT NULL,
  `BillingCountryID` int(11) DEFAULT NULL,
  `BillingStateID` int(11) DEFAULT NULL,
  `ServiceTaxTypeID` int(11) NOT NULL,
  `ServiceTaxIdentificationNumber` varchar(100) NOT NULL,
  `RegisteredAddress` text NOT NULL,
  `AddedBy` int(11) DEFAULT NULL,
  `AddedDate` datetime NOT NULL,
  `UpdatedBy` int(11) DEFAULT NULL,
  `UpdatedDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_service_tax_master`
--

INSERT INTO `company_service_tax_master` (`CompanyServiceTaxMasterID`, `CompID`, `BillingCountryID`, `BillingStateID`, `ServiceTaxTypeID`, `ServiceTaxIdentificationNumber`, `RegisteredAddress`, `AddedBy`, `AddedDate`, `UpdatedBy`, `UpdatedDate`) VALUES
(1, 1, 101, 22, 2, '27AADCW3339G1Z9', 'Unit No.611, Reliables Pride, Anand Nagar, Opp Heera Panna, Jogeshwari (W), Mumbai, 400102.', 1, '2022-12-18 12:48:26', 1, '2023-05-13 18:20:45'),
(2, 49, 101, 22, 1, 'sasa', 'as', 65, '2023-03-21 23:42:51', NULL, NULL),
(3, 53, 101, 22, 2, '27AAKFC2964P', '102, B WING ROYAL ENCLAVE, ANDHERI EAST,\r\nMUMBAI - 400069', 69, '2023-04-07 16:47:25', 69, '2023-04-07 17:01:47');

-- --------------------------------------------------------

--
-- Table structure for table `credit_notes`
--

CREATE TABLE `credit_notes` (
  `CreditNoteID` int(11) NOT NULL,
  `CompID` int(11) NOT NULL,
  `CreditNoteNo` varchar(20) NOT NULL,
  `CreditNoteDate` date NOT NULL,
  `InvoiceID` int(11) NOT NULL,
  `PayableAmount` decimal(10,2) NOT NULL,
  `PaymentStatus` enum('Paid','Unpaid') NOT NULL DEFAULT 'Unpaid',
  `Reason` text DEFAULT NULL,
  `AddedBy` int(11) DEFAULT NULL,
  `AddedDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `credit_note_details`
--

CREATE TABLE `credit_note_details` (
  `CreditNoteDetailID` int(11) NOT NULL,
  `CreditNoteID` int(11) NOT NULL,
  `Particular` text NOT NULL,
  `HSN` varchar(10) DEFAULT NULL,
  `Qty` decimal(10,2) DEFAULT NULL,
  `PricePerUnit` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `credit_note_details_tax_data`
--

CREATE TABLE `credit_note_details_tax_data` (
  `CreditNoteDetailsTaxDataID` int(11) NOT NULL,
  `CreditNoteDetailID` int(11) NOT NULL,
  `Tax` varchar(100) NOT NULL,
  `TaxPercentage` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `debit_notes`
--

CREATE TABLE `debit_notes` (
  `DebitNoteID` int(11) NOT NULL,
  `CompID` int(11) NOT NULL,
  `DebitNoteNo` varchar(20) NOT NULL,
  `DebitNoteDate` date NOT NULL,
  `VendorID` int(11) DEFAULT NULL,
  `InvoiceNo` varchar(50) DEFAULT NULL,
  `CreditNoteNo` varchar(20) DEFAULT NULL,
  `ReceivableAmount` decimal(10,2) NOT NULL,
  `PaymentStatus` enum('Received','Pending') NOT NULL DEFAULT 'Pending',
  `Remarks` text DEFAULT NULL,
  `AddedBy` int(11) DEFAULT NULL,
  `AddedDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `debit_note_details`
--

CREATE TABLE `debit_note_details` (
  `DebitNoteDetailID` int(11) NOT NULL,
  `DebitNoteID` int(11) NOT NULL,
  `Particular` text NOT NULL,
  `HSN` varchar(10) DEFAULT NULL,
  `Quantity` decimal(10,2) NOT NULL,
  `PricePerUnit` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `debit_note_details_tax_data`
--

CREATE TABLE `debit_note_details_tax_data` (
  `DebitNoteDetailsTaxDataID` int(11) NOT NULL,
  `DebitNoteDetailID` int(11) NOT NULL,
  `Tax` varchar(100) NOT NULL,
  `TaxPercentage` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `ExpenseID` int(11) NOT NULL,
  `ExpenseHeadMasterID` int(11) DEFAULT NULL,
  `CompID` int(11) NOT NULL,
  `VendorID` int(11) DEFAULT NULL,
  `ExpenseDate` date NOT NULL,
  `ExpenseAmount` decimal(10,2) NOT NULL,
  `InvoiceNo` varchar(20) DEFAULT NULL,
  `AttachedDocumentPath` text DEFAULT NULL,
  `Remarks` text DEFAULT NULL,
  `AddedBy` int(11) DEFAULT NULL,
  `AddedDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expense_heads_master`
--

CREATE TABLE `expense_heads_master` (
  `ExpenseHeadMasterID` int(11) NOT NULL,
  `ExpenseHead` varchar(200) NOT NULL,
  `CompID` int(11) DEFAULT NULL,
  `AddedBy` int(11) DEFAULT NULL,
  `AddedDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expense_heads_master`
--

INSERT INTO `expense_heads_master` (`ExpenseHeadMasterID`, `ExpenseHead`, `CompID`, `AddedBy`, `AddedDate`) VALUES
(1, 'Purchase', NULL, NULL, NULL),
(2, 'Interest', NULL, NULL, NULL),
(3, 'Audit Fees', NULL, NULL, NULL),
(4, 'Accounting Fees', NULL, NULL, NULL),
(5, 'Business Promotion Expenses', NULL, NULL, NULL),
(6, 'Electricity Expense', NULL, NULL, NULL),
(7, 'Telephone Expenses', NULL, NULL, NULL),
(8, 'Legal & Professional Fees', NULL, NULL, NULL),
(9, 'Printing & Stationery', NULL, NULL, NULL),
(10, 'Selling & Distribution Expense', NULL, NULL, NULL),
(11, 'Rent', NULL, NULL, NULL),
(12, 'Repair Maintenance', NULL, NULL, NULL),
(13, 'Traveling Expense', NULL, NULL, NULL),
(14, 'Salary', NULL, NULL, NULL),
(15, 'Wages', NULL, NULL, NULL),
(16, 'Train Pass', 1, 1, '2022-12-16'),
(17, 'Laptop Rent', 1, 1, '2022-12-16'),
(18, 'Web Hosting', 1, 1, '2022-12-16'),
(19, 'Banking', 1, 1, '2022-12-25'),
(20, 'Food & Beverages', 1, 1, '2023-01-13'),
(21, 'GST Late fee', 1, 1, '2023-01-21'),
(22, 'Stock Purchase', NULL, NULL, NULL),
(23, 'Marketing Commission', 1, 1, '2023-02-20'),
(24, 'Lead Generation', 1, 1, '2023-04-03');

-- --------------------------------------------------------

--
-- Table structure for table `expense_taxes`
--

CREATE TABLE `expense_taxes` (
  `ExpenseTaxID` int(11) NOT NULL,
  `ExpenseID` int(11) NOT NULL,
  `Tax` varchar(100) NOT NULL,
  `TaxPercentage` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `InvoiceID` int(11) NOT NULL,
  `InvoiceNo` varchar(20) NOT NULL,
  `CompID` int(11) NOT NULL,
  `FirmTypeID` int(11) DEFAULT NULL,
  `CompanyName` varchar(100) NOT NULL,
  `CompanyContactNumber` varchar(15) DEFAULT NULL,
  `CompanyAddress` text DEFAULT NULL,
  `CompanyServiceTaxTypeID` int(11) DEFAULT NULL,
  `CompanyServiceTaxIdentificationNumber` varchar(50) DEFAULT NULL,
  `CompanyTaxIdentificationNumber` varchar(50) DEFAULT NULL,
  `ClientID` int(11) DEFAULT NULL,
  `ServiceTaxTypeID` int(11) DEFAULT NULL,
  `TotalPayableAmount` decimal(10,2) NOT NULL,
  `ClientServiceTaxIdentificationNumber` varchar(20) DEFAULT NULL,
  `ClientContactNo` varchar(15) DEFAULT NULL,
  `ClientInvoiceDate` date NOT NULL,
  `ClientInvoiceDueDate` date NOT NULL,
  `ClientBillingAddress` text DEFAULT NULL,
  `ClientShippingAddress` text DEFAULT NULL,
  `CustomerNotes` text DEFAULT NULL,
  `TermsAndConditions` text DEFAULT NULL,
  `CreatedBy` int(11) DEFAULT NULL,
  `CreatedDate` datetime DEFAULT NULL,
  `IsDeleted` tinyint(1) NOT NULL DEFAULT 0,
  `DeletedBy` int(11) DEFAULT NULL,
  `DeletedDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_additional_charges`
--

CREATE TABLE `invoice_additional_charges` (
  `InvoiceAdditionalChargeID` int(11) NOT NULL,
  `InvoiceID` int(11) NOT NULL,
  `AdditionalChargeType` varchar(200) NOT NULL,
  `Additionalcharge` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_deductibles`
--

CREATE TABLE `invoice_deductibles` (
  `InvoiceDeductibleID` int(11) NOT NULL,
  `InvoiceID` int(11) NOT NULL,
  `DeductibleType` varchar(50) NOT NULL,
  `DeductiblePercentage` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_details`
--

CREATE TABLE `invoice_details` (
  `InvoiceDetailID` int(11) NOT NULL,
  `InvoiceID` int(11) NOT NULL,
  `Particular` text NOT NULL,
  `ParticularType` enum('Good','Service') NOT NULL,
  `ItemCategory` varchar(200) DEFAULT NULL,
  `HSN` varchar(10) NOT NULL,
  `BarcodeNo` varchar(100) DEFAULT NULL,
  `SerialNo` varchar(50) DEFAULT NULL,
  `PricePerUnit` decimal(10,2) NOT NULL,
  `Quantity` decimal(10,2) DEFAULT NULL,
  `TotalTaxPercentage` decimal(10,2) NOT NULL,
  `TotalAmount` decimal(10,2) NOT NULL,
  `Discount` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_details_tax_data`
--

CREATE TABLE `invoice_details_tax_data` (
  `invoiceDetailsTaxDataID` int(11) NOT NULL,
  `InvoiceDetailID` int(11) NOT NULL,
  `Tax` varchar(100) NOT NULL,
  `TaxPercentage` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_settings`
--

CREATE TABLE `invoice_settings` (
  `InvoiceSettingID` int(11) NOT NULL,
  `CompID` int(11) NOT NULL,
  `TermsAndConditions` text DEFAULT NULL,
  `AddedBy` int(11) DEFAULT NULL,
  `AddedDate` datetime NOT NULL,
  `UpdatedBy` int(11) DEFAULT NULL,
  `UpdatedDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lead_quotation`
--

CREATE TABLE `lead_quotation` (
  `LeadQuotationID` int(11) NOT NULL,
  `LeadID` int(11) NOT NULL,
  `CompID` int(11) NOT NULL,
  `CompBillingAddress` text NOT NULL,
  `QuotationNo` varchar(30) NOT NULL,
  `TotalPayableAmount` decimal(10,2) NOT NULL,
  `TermsAndConditions` text DEFAULT NULL,
  `Notes` text DEFAULT NULL,
  `AddedBy` int(11) DEFAULT NULL,
  `AddedDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lead_quotation_additional_charges`
--

CREATE TABLE `lead_quotation_additional_charges` (
  `LeadQuotationAdditionalChargesID` int(11) NOT NULL,
  `LeadQuotationID` int(11) NOT NULL,
  `AdditionalChargeType` varchar(200) DEFAULT NULL,
  `AdditionalCharge` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lead_quotation_details`
--

CREATE TABLE `lead_quotation_details` (
  `LeadQuotationDetailID` int(11) NOT NULL,
  `LeadQuotationID` int(11) NOT NULL,
  `Particular` text NOT NULL,
  `Quantity` int(11) NOT NULL,
  `PricePerUnit` decimal(10,2) NOT NULL,
  `TotalTaxPercentage` decimal(10,2) NOT NULL,
  `TotalAmount` decimal(10,2) NOT NULL,
  `HSN` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lead_quotation_details_tax_data`
--

CREATE TABLE `lead_quotation_details_tax_data` (
  `LeadQuotationDetailsTaxDataID` int(11) NOT NULL,
  `LeadQuotationDetailID` int(11) NOT NULL,
  `Tax` varchar(100) NOT NULL,
  `TaxPercentage` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_mode_master`
--

CREATE TABLE `payment_mode_master` (
  `PaymentModeID` int(11) NOT NULL,
  `PaymentMode` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_mode_master`
--

INSERT INTO `payment_mode_master` (`PaymentModeID`, `PaymentMode`) VALUES
(1, 'Bank remittance'),
(2, 'Bank Transfer'),
(3, 'Cash'),
(4, 'Cheque'),
(5, 'Credit Card');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order`
--

CREATE TABLE `purchase_order` (
  `PurchaseOrderID` int(11) NOT NULL,
  `PurchaseOrderNo` varchar(30) NOT NULL,
  `CompID` int(11) NOT NULL,
  `CompanyName` varchar(200) NOT NULL,
  `CompanyContactNumber` varchar(15) DEFAULT NULL,
  `CompanyAddress` text DEFAULT NULL,
  `FirmTypeID` int(11) DEFAULT NULL,
  `CompanyServiceTaxTypeID` int(11) DEFAULT NULL,
  `CompanyServiceTaxIdentificationNumber` varchar(30) DEFAULT NULL,
  `VendorID` int(11) NOT NULL,
  `ServiceTaxTypeID` int(11) DEFAULT NULL,
  `VendorServiceTaxIdentificationNumber` varchar(30) DEFAULT NULL,
  `VendorContactNo` varchar(15) NOT NULL,
  `VendorBillingAddress` text DEFAULT NULL,
  `DeliveryDate` date NOT NULL,
  `TotalAmount` decimal(10,2) NOT NULL,
  `ShippingAddress` text DEFAULT NULL,
  `ShippingTermsAndConditions` text DEFAULT NULL,
  `PaymentTerms` text NOT NULL,
  `PurchaseOrderStatusID` int(11) DEFAULT NULL,
  `CancelationRemark` text DEFAULT NULL,
  `AddedBy` int(11) DEFAULT NULL,
  `AddedDate` datetime NOT NULL,
  `UpdatedBy` int(11) DEFAULT NULL,
  `UpdatedDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_details`
--

CREATE TABLE `purchase_order_details` (
  `PurchaseOrderDetailID` int(11) NOT NULL,
  `PurchaseOrderID` int(11) NOT NULL,
  `Particular` varchar(200) NOT NULL,
  `HSN` varchar(10) NOT NULL,
  `Quantity` int(11) NOT NULL,
  `PricePerUnit` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_settings`
--

CREATE TABLE `purchase_order_settings` (
  `PurchaseOrderSettingID` int(11) NOT NULL,
  `CompID` int(11) NOT NULL,
  `ShippingTermsAndConditions` text DEFAULT NULL,
  `PaymentTerms` text NOT NULL,
  `Updatedby` int(11) DEFAULT NULL,
  `UpdatedDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_status_master`
--

CREATE TABLE `purchase_order_status_master` (
  `PurchaseOrderStatusID` int(11) NOT NULL,
  `PurchaseOrderStatus` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_order_status_master`
--

INSERT INTO `purchase_order_status_master` (`PurchaseOrderStatusID`, `PurchaseOrderStatus`) VALUES
(1, 'New'),
(2, 'Released'),
(3, 'Received'),
(4, 'Canceled');

-- --------------------------------------------------------

--
-- Table structure for table `receipts`
--

CREATE TABLE `receipts` (
  `ReceiptID` int(11) NOT NULL,
  `ReceiptNo` varchar(20) NOT NULL,
  `InvoiceID` int(11) NOT NULL,
  `PaidAmount` decimal(10,2) NOT NULL,
  `ReceiptDate` datetime DEFAULT NULL,
  `PaymentModeID` int(11) DEFAULT NULL,
  `AddedBy` int(11) DEFAULT NULL,
  `AddedDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `registered_user_bank_account_details`
--

CREATE TABLE `registered_user_bank_account_details` (
  `RegisteredUserBankAccountDetailID` int(11) NOT NULL,
  `RegisteredUserID` int(11) NOT NULL,
  `AccountNumber` int(11) NOT NULL,
  `BankID` int(11) NOT NULL,
  `BankDetailsID` int(11) DEFAULT NULL,
  `AccountType` enum('Savings','Current') NOT NULL DEFAULT 'Savings',
  `AccountHolderName` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_tax_types_master`
--

CREATE TABLE `service_tax_types_master` (
  `ServiceTaxTypeID` int(11) NOT NULL,
  `ServiceTaxType` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_tax_types_master`
--

INSERT INTO `service_tax_types_master` (`ServiceTaxTypeID`, `ServiceTaxType`) VALUES
(1, 'VAT'),
(2, 'GST');

-- --------------------------------------------------------

--
-- Table structure for table `tax_types_master`
--

CREATE TABLE `tax_types_master` (
  `ServiceTaxTypeID` int(11) NOT NULL,
  `ServiceTaxType` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tax_types_master`
--

INSERT INTO `tax_types_master` (`ServiceTaxTypeID`, `ServiceTaxType`) VALUES
(1, 'VAT'),
(2, 'GST');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `TransactionID` int(11) NOT NULL,
  `RegisteredUserID` int(11) DEFAULT NULL,
  `Name` varchar(150) NOT NULL,
  `EmailID` varchar(254) DEFAULT NULL,
  `ContactNo` varchar(15) DEFAULT NULL,
  `CompName` varchar(100) NOT NULL,
  `TaxTypeID` int(11) DEFAULT NULL,
  `TaxIdentificationNumber` varchar(30) DEFAULT NULL,
  `ServiceTaxTypeID` int(11) DEFAULT NULL,
  `ServiceTaxIdentificationNumber` varchar(100) DEFAULT NULL,
  `AppID` int(11) DEFAULT NULL,
  `SubscriptionPlanID` int(11) DEFAULT NULL,
  `InvoiceNo` varchar(20) NOT NULL,
  `ReceiptNo` varchar(20) NOT NULL,
  `PaymentGateway` varchar(100) NOT NULL,
  `OrderID` varchar(200) NOT NULL,
  `PlanAmount` decimal(10,2) NOT NULL,
  `TaxPercentage` decimal(10,2) NOT NULL,
  `AmountPaid` decimal(10,2) NOT NULL,
  `Currency` varchar(50) NOT NULL,
  `Status` varchar(20) NOT NULL,
  `PaymentMadeBy` int(11) DEFAULT NULL,
  `PaymentReceivedDate` datetime NOT NULL,
  `ReferrerCommissionPercentage` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vendor_service_tax_type_mapper`
--

CREATE TABLE `vendor_service_tax_type_mapper` (
  `VendorServiceTaxID` int(11) NOT NULL,
  `Label` varchar(50) NOT NULL,
  `VendorID` int(11) NOT NULL,
  `ServiceTaxTypeID` int(11) NOT NULL,
  `ServiceTaxNumber` varchar(30) NOT NULL,
  `BillingCountryID` int(11) DEFAULT NULL,
  `BillingStateID` int(11) DEFAULT NULL,
  `BillingAddress` text NOT NULL,
  `AddedBy` int(11) DEFAULT NULL,
  `AddedDate` datetime NOT NULL,
  `UpdatedBy` int(11) DEFAULT NULL,
  `UpdatedDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendor_service_tax_type_mapper`
--

INSERT INTO `vendor_service_tax_type_mapper` (`VendorServiceTaxID`, `Label`, `VendorID`, `ServiceTaxTypeID`, `ServiceTaxNumber`, `BillingCountryID`, `BillingStateID`, `BillingAddress`, `AddedBy`, `AddedDate`, `UpdatedBy`, `UpdatedDate`) VALUES
(1, 'Pune GST', 16, 2, '27AAJCC6968R1ZJ', 101, 22, '205, Ketaki Heights, Raskarnagar,\r\nShrirampur, MH-413709', 1, '2022-12-24 14:33:09', 1, '2023-05-14 18:02:45');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `client_service_tax_type_mapper`
--
ALTER TABLE `client_service_tax_type_mapper`
  ADD PRIMARY KEY (`ClientServiceTaxID`),
  ADD KEY `ClientID` (`ClientID`),
  ADD KEY `ServiceTaxTypeID` (`ServiceTaxTypeID`),
  ADD KEY `AddedBy` (`AddedBy`),
  ADD KEY `UpdatedBy` (`UpdatedBy`),
  ADD KEY `BillingCountryID` (`BillingCountryID`),
  ADD KEY `BillingStateID` (`BillingStateID`);

--
-- Indexes for table `company_service_tax_master`
--
ALTER TABLE `company_service_tax_master`
  ADD PRIMARY KEY (`CompanyServiceTaxMasterID`),
  ADD UNIQUE KEY `CompID_2` (`CompID`,`ServiceTaxTypeID`,`ServiceTaxIdentificationNumber`),
  ADD KEY `CompID` (`CompID`),
  ADD KEY `TaxTypeID` (`ServiceTaxTypeID`),
  ADD KEY `AddedBy` (`AddedBy`),
  ADD KEY `UpdatedBy` (`UpdatedBy`),
  ADD KEY `BillingCountryID` (`BillingCountryID`),
  ADD KEY `BillingStateID` (`BillingStateID`);

--
-- Indexes for table `credit_notes`
--
ALTER TABLE `credit_notes`
  ADD PRIMARY KEY (`CreditNoteID`),
  ADD UNIQUE KEY `CompID` (`CompID`,`CreditNoteNo`),
  ADD KEY `AddedBy` (`AddedBy`),
  ADD KEY `InvoiceID` (`InvoiceID`);

--
-- Indexes for table `credit_note_details`
--
ALTER TABLE `credit_note_details`
  ADD PRIMARY KEY (`CreditNoteDetailID`),
  ADD KEY `CreditNoteID` (`CreditNoteID`);

--
-- Indexes for table `credit_note_details_tax_data`
--
ALTER TABLE `credit_note_details_tax_data`
  ADD PRIMARY KEY (`CreditNoteDetailsTaxDataID`),
  ADD KEY `CreditNoteDetailID` (`CreditNoteDetailID`);

--
-- Indexes for table `debit_notes`
--
ALTER TABLE `debit_notes`
  ADD PRIMARY KEY (`DebitNoteID`),
  ADD UNIQUE KEY `CompID` (`CompID`,`DebitNoteNo`),
  ADD KEY `AddedBy` (`AddedBy`),
  ADD KEY `VendorID` (`VendorID`);

--
-- Indexes for table `debit_note_details`
--
ALTER TABLE `debit_note_details`
  ADD PRIMARY KEY (`DebitNoteDetailID`),
  ADD KEY `DebitNoteID` (`DebitNoteID`);

--
-- Indexes for table `debit_note_details_tax_data`
--
ALTER TABLE `debit_note_details_tax_data`
  ADD PRIMARY KEY (`DebitNoteDetailsTaxDataID`),
  ADD KEY `DebitNoteDetailID` (`DebitNoteDetailID`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`ExpenseID`),
  ADD KEY `ExpenseHeadID` (`ExpenseHeadMasterID`),
  ADD KEY `CompID` (`CompID`),
  ADD KEY `AddedBy` (`AddedBy`),
  ADD KEY `VendorID` (`VendorID`);

--
-- Indexes for table `expense_heads_master`
--
ALTER TABLE `expense_heads_master`
  ADD PRIMARY KEY (`ExpenseHeadMasterID`),
  ADD KEY `CompID` (`CompID`),
  ADD KEY `AddedBy` (`AddedBy`);

--
-- Indexes for table `expense_taxes`
--
ALTER TABLE `expense_taxes`
  ADD PRIMARY KEY (`ExpenseTaxID`),
  ADD KEY `ExpenseID` (`ExpenseID`);

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`InvoiceID`),
  ADD KEY `ClientID` (`ClientID`),
  ADD KEY `DeletedBy` (`DeletedBy`),
  ADD KEY `CreatedBy` (`CreatedBy`),
  ADD KEY `CompID` (`CompID`),
  ADD KEY `FirmTypeID` (`FirmTypeID`),
  ADD KEY `CompanyTaxTypeID` (`CompanyServiceTaxTypeID`),
  ADD KEY `TaxTypeID` (`ServiceTaxTypeID`);

--
-- Indexes for table `invoice_additional_charges`
--
ALTER TABLE `invoice_additional_charges`
  ADD PRIMARY KEY (`InvoiceAdditionalChargeID`),
  ADD KEY `InvoiceID` (`InvoiceID`);

--
-- Indexes for table `invoice_deductibles`
--
ALTER TABLE `invoice_deductibles`
  ADD PRIMARY KEY (`InvoiceDeductibleID`),
  ADD KEY `InvoiceID` (`InvoiceID`);

--
-- Indexes for table `invoice_details`
--
ALTER TABLE `invoice_details`
  ADD PRIMARY KEY (`InvoiceDetailID`),
  ADD KEY `InvoiceID` (`InvoiceID`);

--
-- Indexes for table `invoice_details_tax_data`
--
ALTER TABLE `invoice_details_tax_data`
  ADD PRIMARY KEY (`invoiceDetailsTaxDataID`),
  ADD KEY `InvoiceDetailID` (`InvoiceDetailID`);

--
-- Indexes for table `invoice_settings`
--
ALTER TABLE `invoice_settings`
  ADD PRIMARY KEY (`InvoiceSettingID`),
  ADD KEY `CompID` (`CompID`),
  ADD KEY `AddedBy` (`AddedBy`),
  ADD KEY `UpdatedBy` (`UpdatedBy`);

--
-- Indexes for table `lead_quotation`
--
ALTER TABLE `lead_quotation`
  ADD PRIMARY KEY (`LeadQuotationID`),
  ADD KEY `LeadID` (`LeadID`),
  ADD KEY `AddedBy` (`AddedBy`),
  ADD KEY `CompID` (`CompID`);

--
-- Indexes for table `lead_quotation_additional_charges`
--
ALTER TABLE `lead_quotation_additional_charges`
  ADD PRIMARY KEY (`LeadQuotationAdditionalChargesID`),
  ADD KEY `LeadQuotationID` (`LeadQuotationID`);

--
-- Indexes for table `lead_quotation_details`
--
ALTER TABLE `lead_quotation_details`
  ADD PRIMARY KEY (`LeadQuotationDetailID`),
  ADD KEY `LeadQuotationID` (`LeadQuotationID`);

--
-- Indexes for table `lead_quotation_details_tax_data`
--
ALTER TABLE `lead_quotation_details_tax_data`
  ADD PRIMARY KEY (`LeadQuotationDetailsTaxDataID`),
  ADD KEY `LeadQuotationDetailID` (`LeadQuotationDetailID`);

--
-- Indexes for table `payment_mode_master`
--
ALTER TABLE `payment_mode_master`
  ADD PRIMARY KEY (`PaymentModeID`);

--
-- Indexes for table `purchase_order`
--
ALTER TABLE `purchase_order`
  ADD PRIMARY KEY (`PurchaseOrderID`),
  ADD KEY `CompID` (`CompID`),
  ADD KEY `FirmTypeID` (`FirmTypeID`),
  ADD KEY `ServiceTaxTypeID` (`ServiceTaxTypeID`),
  ADD KEY `AddedBy` (`AddedBy`),
  ADD KEY `UpdatedBy` (`UpdatedBy`),
  ADD KEY `VendorID` (`VendorID`),
  ADD KEY `CompanyServiceTaxTypeID` (`CompanyServiceTaxTypeID`),
  ADD KEY `PurchaseOrderStatusID` (`PurchaseOrderStatusID`);

--
-- Indexes for table `purchase_order_details`
--
ALTER TABLE `purchase_order_details`
  ADD PRIMARY KEY (`PurchaseOrderDetailID`),
  ADD KEY `PurchaseOrderID` (`PurchaseOrderID`);

--
-- Indexes for table `purchase_order_settings`
--
ALTER TABLE `purchase_order_settings`
  ADD PRIMARY KEY (`PurchaseOrderSettingID`),
  ADD KEY `CompID` (`CompID`),
  ADD KEY `Updatedby` (`Updatedby`);

--
-- Indexes for table `purchase_order_status_master`
--
ALTER TABLE `purchase_order_status_master`
  ADD PRIMARY KEY (`PurchaseOrderStatusID`);

--
-- Indexes for table `receipts`
--
ALTER TABLE `receipts`
  ADD PRIMARY KEY (`ReceiptID`),
  ADD KEY `InvoiceID` (`InvoiceID`),
  ADD KEY `AddedBy` (`AddedBy`),
  ADD KEY `PaymentModeID` (`PaymentModeID`);

--
-- Indexes for table `registered_user_bank_account_details`
--
ALTER TABLE `registered_user_bank_account_details`
  ADD PRIMARY KEY (`RegisteredUserBankAccountDetailID`),
  ADD UNIQUE KEY `RegisteredUserID_2` (`RegisteredUserID`),
  ADD KEY `BankID` (`BankID`),
  ADD KEY `RegisteredUserID` (`RegisteredUserID`),
  ADD KEY `BankDetailsID` (`BankDetailsID`);

--
-- Indexes for table `service_tax_types_master`
--
ALTER TABLE `service_tax_types_master`
  ADD PRIMARY KEY (`ServiceTaxTypeID`);

--
-- Indexes for table `tax_types_master`
--
ALTER TABLE `tax_types_master`
  ADD PRIMARY KEY (`ServiceTaxTypeID`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`TransactionID`),
  ADD KEY `EmployeeID` (`RegisteredUserID`),
  ADD KEY `AppID` (`AppID`),
  ADD KEY `SubscriptionPlanID` (`SubscriptionPlanID`),
  ADD KEY `PaymentMadeBy` (`PaymentMadeBy`);

--
-- Indexes for table `vendor_service_tax_type_mapper`
--
ALTER TABLE `vendor_service_tax_type_mapper`
  ADD PRIMARY KEY (`VendorServiceTaxID`),
  ADD KEY `VendorID` (`VendorID`),
  ADD KEY `ServiceTaxTypeID` (`ServiceTaxTypeID`),
  ADD KEY `AddedBy` (`AddedBy`),
  ADD KEY `UpdatedBy` (`UpdatedBy`),
  ADD KEY `BillingCountryID` (`BillingCountryID`),
  ADD KEY `BillingStateID` (`BillingStateID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `client_service_tax_type_mapper`
--
ALTER TABLE `client_service_tax_type_mapper`
  MODIFY `ClientServiceTaxID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company_service_tax_master`
--
ALTER TABLE `company_service_tax_master`
  MODIFY `CompanyServiceTaxMasterID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `credit_notes`
--
ALTER TABLE `credit_notes`
  MODIFY `CreditNoteID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `credit_note_details`
--
ALTER TABLE `credit_note_details`
  MODIFY `CreditNoteDetailID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `credit_note_details_tax_data`
--
ALTER TABLE `credit_note_details_tax_data`
  MODIFY `CreditNoteDetailsTaxDataID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `debit_notes`
--
ALTER TABLE `debit_notes`
  MODIFY `DebitNoteID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `debit_note_details`
--
ALTER TABLE `debit_note_details`
  MODIFY `DebitNoteDetailID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `debit_note_details_tax_data`
--
ALTER TABLE `debit_note_details_tax_data`
  MODIFY `DebitNoteDetailsTaxDataID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `ExpenseID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expense_heads_master`
--
ALTER TABLE `expense_heads_master`
  MODIFY `ExpenseHeadMasterID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `expense_taxes`
--
ALTER TABLE `expense_taxes`
  MODIFY `ExpenseTaxID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `InvoiceID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice_additional_charges`
--
ALTER TABLE `invoice_additional_charges`
  MODIFY `InvoiceAdditionalChargeID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice_deductibles`
--
ALTER TABLE `invoice_deductibles`
  MODIFY `InvoiceDeductibleID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice_details`
--
ALTER TABLE `invoice_details`
  MODIFY `InvoiceDetailID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice_details_tax_data`
--
ALTER TABLE `invoice_details_tax_data`
  MODIFY `invoiceDetailsTaxDataID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice_settings`
--
ALTER TABLE `invoice_settings`
  MODIFY `InvoiceSettingID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lead_quotation`
--
ALTER TABLE `lead_quotation`
  MODIFY `LeadQuotationID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lead_quotation_additional_charges`
--
ALTER TABLE `lead_quotation_additional_charges`
  MODIFY `LeadQuotationAdditionalChargesID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lead_quotation_details`
--
ALTER TABLE `lead_quotation_details`
  MODIFY `LeadQuotationDetailID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lead_quotation_details_tax_data`
--
ALTER TABLE `lead_quotation_details_tax_data`
  MODIFY `LeadQuotationDetailsTaxDataID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_mode_master`
--
ALTER TABLE `payment_mode_master`
  MODIFY `PaymentModeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `purchase_order`
--
ALTER TABLE `purchase_order`
  MODIFY `PurchaseOrderID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_order_details`
--
ALTER TABLE `purchase_order_details`
  MODIFY `PurchaseOrderDetailID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_order_settings`
--
ALTER TABLE `purchase_order_settings`
  MODIFY `PurchaseOrderSettingID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_order_status_master`
--
ALTER TABLE `purchase_order_status_master`
  MODIFY `PurchaseOrderStatusID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `receipts`
--
ALTER TABLE `receipts`
  MODIFY `ReceiptID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `registered_user_bank_account_details`
--
ALTER TABLE `registered_user_bank_account_details`
  MODIFY `RegisteredUserBankAccountDetailID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_tax_types_master`
--
ALTER TABLE `service_tax_types_master`
  MODIFY `ServiceTaxTypeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tax_types_master`
--
ALTER TABLE `tax_types_master`
  MODIFY `ServiceTaxTypeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `TransactionID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vendor_service_tax_type_mapper`
--
ALTER TABLE `vendor_service_tax_type_mapper`
  MODIFY `VendorServiceTaxID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `client_service_tax_type_mapper`
--
ALTER TABLE `client_service_tax_type_mapper`
  ADD CONSTRAINT `client_service_tax_type_mapper_ibfk_1` FOREIGN KEY (`ClientID`) REFERENCES `openuux1_crm_core`.`clients` (`ClientID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `client_service_tax_type_mapper_ibfk_2` FOREIGN KEY (`ServiceTaxTypeID`) REFERENCES `tax_types_master` (`ServiceTaxTypeID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `client_service_tax_type_mapper_ibfk_3` FOREIGN KEY (`AddedBy`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `client_service_tax_type_mapper_ibfk_4` FOREIGN KEY (`UpdatedBy`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `client_service_tax_type_mapper_ibfk_5` FOREIGN KEY (`BillingCountryID`) REFERENCES `openuux1_crm_core`.`countries_master` (`CountryID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `client_service_tax_type_mapper_ibfk_6` FOREIGN KEY (`BillingStateID`) REFERENCES `openuux1_crm_core`.`states_master` (`StateID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `company_service_tax_master`
--
ALTER TABLE `company_service_tax_master`
  ADD CONSTRAINT `company_service_tax_master_ibfk_1` FOREIGN KEY (`CompID`) REFERENCES `openuux1_crm_core`.`company_master` (`CompID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `company_service_tax_master_ibfk_2` FOREIGN KEY (`ServiceTaxTypeID`) REFERENCES `tax_types_master` (`ServiceTaxTypeID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `company_service_tax_master_ibfk_3` FOREIGN KEY (`AddedBy`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `company_service_tax_master_ibfk_4` FOREIGN KEY (`UpdatedBy`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `company_service_tax_master_ibfk_5` FOREIGN KEY (`BillingCountryID`) REFERENCES `openuux1_crm_core`.`countries_master` (`CountryID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `company_service_tax_master_ibfk_6` FOREIGN KEY (`BillingStateID`) REFERENCES `openuux1_crm_core`.`states_master` (`StateID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `credit_notes`
--
ALTER TABLE `credit_notes`
  ADD CONSTRAINT `credit_notes_ibfk_1` FOREIGN KEY (`CompID`) REFERENCES `openuux1_crm_core`.`company_master` (`CompID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `credit_notes_ibfk_2` FOREIGN KEY (`AddedBy`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `credit_notes_ibfk_3` FOREIGN KEY (`InvoiceID`) REFERENCES `invoice` (`InvoiceID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `credit_note_details`
--
ALTER TABLE `credit_note_details`
  ADD CONSTRAINT `credit_note_details_ibfk_1` FOREIGN KEY (`CreditNoteID`) REFERENCES `credit_notes` (`CreditNoteID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `credit_note_details_tax_data`
--
ALTER TABLE `credit_note_details_tax_data`
  ADD CONSTRAINT `credit_note_details_tax_data_ibfk_1` FOREIGN KEY (`CreditNoteDetailID`) REFERENCES `credit_note_details` (`CreditNoteDetailID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `debit_notes`
--
ALTER TABLE `debit_notes`
  ADD CONSTRAINT `debit_notes_ibfk_1` FOREIGN KEY (`CompID`) REFERENCES `openuux1_crm_core`.`company_master` (`CompID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `debit_notes_ibfk_2` FOREIGN KEY (`AddedBy`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `debit_notes_ibfk_3` FOREIGN KEY (`VendorID`) REFERENCES `openuux1_crm_core`.`vendors` (`VendorID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `debit_note_details`
--
ALTER TABLE `debit_note_details`
  ADD CONSTRAINT `debit_note_details_ibfk_1` FOREIGN KEY (`DebitNoteID`) REFERENCES `debit_notes` (`DebitNoteID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `debit_note_details_tax_data`
--
ALTER TABLE `debit_note_details_tax_data`
  ADD CONSTRAINT `debit_note_details_tax_data_ibfk_1` FOREIGN KEY (`DebitNoteDetailID`) REFERENCES `debit_note_details` (`DebitNoteDetailID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`ExpenseHeadMasterID`) REFERENCES `expense_heads_master` (`ExpenseHeadMasterID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `expenses_ibfk_2` FOREIGN KEY (`CompID`) REFERENCES `openuux1_crm_core`.`company_master` (`CompID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `expenses_ibfk_3` FOREIGN KEY (`AddedBy`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `expenses_ibfk_4` FOREIGN KEY (`VendorID`) REFERENCES `openuux1_crm_core`.`vendors` (`VendorID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `expense_heads_master`
--
ALTER TABLE `expense_heads_master`
  ADD CONSTRAINT `expense_heads_master_ibfk_1` FOREIGN KEY (`CompID`) REFERENCES `openuux1_crm_core`.`company_master` (`CompID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `expense_heads_master_ibfk_2` FOREIGN KEY (`AddedBy`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `expense_taxes`
--
ALTER TABLE `expense_taxes`
  ADD CONSTRAINT `expense_taxes_ibfk_1` FOREIGN KEY (`ExpenseID`) REFERENCES `expenses` (`ExpenseID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `invoice`
--
ALTER TABLE `invoice`
  ADD CONSTRAINT `invoice_ibfk_1` FOREIGN KEY (`ClientID`) REFERENCES `openuux1_crm_core`.`clients` (`ClientID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `invoice_ibfk_2` FOREIGN KEY (`DeletedBy`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `invoice_ibfk_3` FOREIGN KEY (`CreatedBy`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `invoice_ibfk_4` FOREIGN KEY (`CompID`) REFERENCES `openuux1_crm_core`.`company_master` (`CompID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `invoice_ibfk_5` FOREIGN KEY (`FirmTypeID`) REFERENCES `openuux1_crm_core`.`firm_type_master` (`FirmTypeID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `invoice_ibfk_6` FOREIGN KEY (`CompanyServiceTaxTypeID`) REFERENCES `tax_types_master` (`ServiceTaxTypeID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `invoice_ibfk_7` FOREIGN KEY (`ServiceTaxTypeID`) REFERENCES `tax_types_master` (`ServiceTaxTypeID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `invoice_additional_charges`
--
ALTER TABLE `invoice_additional_charges`
  ADD CONSTRAINT `invoice_additional_charges_ibfk_1` FOREIGN KEY (`InvoiceID`) REFERENCES `invoice` (`InvoiceID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `invoice_deductibles`
--
ALTER TABLE `invoice_deductibles`
  ADD CONSTRAINT `invoice_deductibles_ibfk_1` FOREIGN KEY (`InvoiceID`) REFERENCES `invoice` (`InvoiceID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `invoice_details`
--
ALTER TABLE `invoice_details`
  ADD CONSTRAINT `invoice_details_ibfk_1` FOREIGN KEY (`InvoiceID`) REFERENCES `invoice` (`InvoiceID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `invoice_details_tax_data`
--
ALTER TABLE `invoice_details_tax_data`
  ADD CONSTRAINT `invoice_details_tax_data_ibfk_1` FOREIGN KEY (`InvoiceDetailID`) REFERENCES `invoice_details` (`InvoiceDetailID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `invoice_settings`
--
ALTER TABLE `invoice_settings`
  ADD CONSTRAINT `invoice_settings_ibfk_1` FOREIGN KEY (`CompID`) REFERENCES `openuux1_crm_core`.`company_master` (`CompID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `invoice_settings_ibfk_2` FOREIGN KEY (`AddedBy`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `invoice_settings_ibfk_3` FOREIGN KEY (`UpdatedBy`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `lead_quotation`
--
ALTER TABLE `lead_quotation`
  ADD CONSTRAINT `lead_quotation_ibfk_1` FOREIGN KEY (`LeadID`) REFERENCES `openuux1_crm_lead_management`.`leads` (`LeadID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `lead_quotation_ibfk_2` FOREIGN KEY (`AddedBy`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `lead_quotation_ibfk_3` FOREIGN KEY (`CompID`) REFERENCES `openuux1_crm_core`.`company_master` (`CompID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lead_quotation_additional_charges`
--
ALTER TABLE `lead_quotation_additional_charges`
  ADD CONSTRAINT `lead_quotation_additional_charges_ibfk_1` FOREIGN KEY (`LeadQuotationID`) REFERENCES `lead_quotation` (`LeadQuotationID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lead_quotation_details`
--
ALTER TABLE `lead_quotation_details`
  ADD CONSTRAINT `lead_quotation_details_ibfk_1` FOREIGN KEY (`LeadQuotationID`) REFERENCES `lead_quotation` (`LeadQuotationID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lead_quotation_details_tax_data`
--
ALTER TABLE `lead_quotation_details_tax_data`
  ADD CONSTRAINT `lead_quotation_details_tax_data_ibfk_1` FOREIGN KEY (`LeadQuotationDetailID`) REFERENCES `lead_quotation_details` (`LeadQuotationDetailID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `purchase_order`
--
ALTER TABLE `purchase_order`
  ADD CONSTRAINT `purchase_order_ibfk_1` FOREIGN KEY (`CompID`) REFERENCES `openuux1_crm_core`.`company_master` (`CompID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_order_ibfk_2` FOREIGN KEY (`FirmTypeID`) REFERENCES `openuux1_crm_core`.`firm_type_master` (`FirmTypeID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_order_ibfk_3` FOREIGN KEY (`ServiceTaxTypeID`) REFERENCES `service_tax_types_master` (`ServiceTaxTypeID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_order_ibfk_4` FOREIGN KEY (`AddedBy`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_order_ibfk_5` FOREIGN KEY (`UpdatedBy`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_order_ibfk_6` FOREIGN KEY (`VendorID`) REFERENCES `openuux1_crm_core`.`vendors` (`VendorID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_order_ibfk_7` FOREIGN KEY (`CompanyServiceTaxTypeID`) REFERENCES `service_tax_types_master` (`ServiceTaxTypeID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_order_ibfk_8` FOREIGN KEY (`PurchaseOrderStatusID`) REFERENCES `purchase_order_status_master` (`PurchaseOrderStatusID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `purchase_order_details`
--
ALTER TABLE `purchase_order_details`
  ADD CONSTRAINT `purchase_order_details_ibfk_1` FOREIGN KEY (`PurchaseOrderID`) REFERENCES `purchase_order` (`PurchaseOrderID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `purchase_order_settings`
--
ALTER TABLE `purchase_order_settings`
  ADD CONSTRAINT `purchase_order_settings_ibfk_1` FOREIGN KEY (`CompID`) REFERENCES `openuux1_crm_core`.`company_master` (`CompID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_order_settings_ibfk_2` FOREIGN KEY (`Updatedby`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `receipts`
--
ALTER TABLE `receipts`
  ADD CONSTRAINT `receipts_ibfk_1` FOREIGN KEY (`InvoiceID`) REFERENCES `invoice` (`InvoiceID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `receipts_ibfk_2` FOREIGN KEY (`AddedBy`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `receipts_ibfk_3` FOREIGN KEY (`PaymentModeID`) REFERENCES `payment_mode_master` (`PaymentModeID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `registered_user_bank_account_details`
--
ALTER TABLE `registered_user_bank_account_details`
  ADD CONSTRAINT `registered_user_bank_account_details_ibfk_1` FOREIGN KEY (`BankID`) REFERENCES `openuux1_crm_core`.`banks_master` (`BankID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `registered_user_bank_account_details_ibfk_2` FOREIGN KEY (`RegisteredUserID`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `registered_user_bank_account_details_ibfk_3` FOREIGN KEY (`BankDetailsID`) REFERENCES `openuux1_crm_core`.`bank_details` (`BankDetailsID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`RegisteredUserID`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`AppID`) REFERENCES `openuux1_crm_core`.`apps` (`AppID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_3` FOREIGN KEY (`SubscriptionPlanID`) REFERENCES `openuux1_crm_core`.`subscription_plans` (`SubscriptionPlanID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_4` FOREIGN KEY (`PaymentMadeBy`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `vendor_service_tax_type_mapper`
--
ALTER TABLE `vendor_service_tax_type_mapper`
  ADD CONSTRAINT `vendor_service_tax_type_mapper_ibfk_1` FOREIGN KEY (`VendorID`) REFERENCES `openuux1_crm_core`.`vendors` (`VendorID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `vendor_service_tax_type_mapper_ibfk_2` FOREIGN KEY (`ServiceTaxTypeID`) REFERENCES `service_tax_types_master` (`ServiceTaxTypeID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `vendor_service_tax_type_mapper_ibfk_3` FOREIGN KEY (`AddedBy`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `vendor_service_tax_type_mapper_ibfk_4` FOREIGN KEY (`UpdatedBy`) REFERENCES `openuux1_crm_core`.`registered_users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `vendor_service_tax_type_mapper_ibfk_5` FOREIGN KEY (`BillingCountryID`) REFERENCES `openuux1_crm_core`.`countries_master` (`CountryID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `vendor_service_tax_type_mapper_ibfk_6` FOREIGN KEY (`BillingStateID`) REFERENCES `openuux1_crm_core`.`states_master` (`StateID`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
