-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 10, 2024 at 03:29 PM
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
-- Database: `mikrotik_cloud`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_tables`
--

CREATE TABLE `admin_tables` (
  `admin_id` int(11) NOT NULL,
  `admin_fullname` varchar(200) DEFAULT NULL,
  `admin_username` varchar(200) DEFAULT NULL,
  `admin_password` varchar(200) DEFAULT NULL,
  `last_time_login` varchar(200) DEFAULT NULL,
  `organization_id` varchar(200) DEFAULT NULL,
  `contacts` varchar(200) DEFAULT NULL,
  `user_status` int(11) DEFAULT 1 COMMENT '0 will be blocked and 1 will be active to login',
  `email` varchar(500) DEFAULT NULL,
  `country` varchar(500) DEFAULT NULL,
  `CompanyName` varchar(500) DEFAULT NULL,
  `priviledges` longtext NOT NULL DEFAULT '\'[{"option":"My Clients","view":true,"readonly":false},{"option":"Transactions","view":true,"readonly":false},{"option":"Expenses","view":true,"readonly":false},{"option":"My Routers","view":true,"readonly":false},{"option":"SMS","view":true,"readonly":false},{"option":"Account and Profile","view":true,"readonly":true}]\'',
  `activated` int(1) NOT NULL DEFAULT 0,
  `dp_locale` varchar(500) DEFAULT NULL,
  `date_changed` varchar(200) NOT NULL DEFAULT '20230320161856',
  `deleted` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `client_tables`
--

CREATE TABLE `client_tables` (
  `client_id` int(11) NOT NULL,
  `client_name` varchar(200) DEFAULT NULL,
  `client_address` varchar(200) DEFAULT NULL,
  `location_coordinates` mediumtext DEFAULT NULL,
  `client_network` varchar(200) DEFAULT NULL,
  `client_default_gw` varchar(200) DEFAULT NULL,
  `next_expiration_date` varchar(200) DEFAULT NULL,
  `clients_reg_date` varchar(20) DEFAULT NULL,
  `max_upload_download` varchar(200) DEFAULT NULL,
  `monthly_payment` int(11) DEFAULT NULL,
  `router_name` varchar(200) DEFAULT NULL,
  `client_interface` varchar(200) DEFAULT NULL,
  `comment` mediumtext DEFAULT NULL,
  `clients_contacts` varchar(200) DEFAULT NULL,
  `client_account` varchar(200) NOT NULL,
  `client_status` int(11) DEFAULT 1 COMMENT '1 client active, 2 client inactive',
  `payments_status` int(11) NOT NULL DEFAULT 1 COMMENT '1 the user is to be charged, 0 the user is not to be charged',
  `wallet_amount` int(11) NOT NULL DEFAULT 0,
  `min_amount` int(5) NOT NULL DEFAULT 100,
  `client_username` varchar(200) DEFAULT NULL,
  `client_password` varchar(200) DEFAULT NULL,
  `freeze_date` varchar(200) DEFAULT NULL,
  `client_freeze_status` int(11) DEFAULT 0,
  `client_freeze_untill` varchar(250) DEFAULT NULL,
  `reffered_by` longtext DEFAULT NULL,
  `assignment` varchar(255) NOT NULL DEFAULT 'static',
  `client_secret` varchar(2000) DEFAULT NULL,
  `client_secret_password` varchar(2000) DEFAULT NULL,
  `client_profile` varchar(500) DEFAULT NULL,
  `last_changed` varchar(500) DEFAULT '20220801185959',
  `date_changed` varchar(200) DEFAULT '20230320161856',
  `deleted` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `Expenses`
--

CREATE TABLE `Expenses` (
  `id` int(11) NOT NULL,
  `name` mediumtext DEFAULT NULL,
  `category` mediumtext DEFAULT NULL,
  `unit_of_measure` varchar(3000) DEFAULT NULL,
  `unit_price` float DEFAULT NULL,
  `unit_amount` float DEFAULT NULL,
  `total_price` float DEFAULT NULL,
  `date_recorded` varchar(200) DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `date_changed` varchar(200) DEFAULT NULL,
  `deleted` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `remote_routers`
--

CREATE TABLE `remote_routers` (
  `router_id` int(11) NOT NULL,
  `router_name` varchar(500) DEFAULT NULL,
  `sstp_username` varchar(500) DEFAULT NULL,
  `sstp_password` varchar(500) DEFAULT NULL,
  `router_location` varchar(500) DEFAULT NULL,
  `router_coordinates` varchar(500) DEFAULT NULL,
  `winbox_port` varchar(200) DEFAULT NULL,
  `api_port` varchar(200) DEFAULT NULL,
  `date_changed` varchar(500) DEFAULT NULL,
  `activated` int(1) NOT NULL DEFAULT 0,
  `deleted` int(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `router_tables`
--

CREATE TABLE `router_tables` (
  `router_id` int(11) NOT NULL,
  `router_name` varchar(200) DEFAULT NULL,
  `router_ipaddr` varchar(200) DEFAULT NULL,
  `router_api_username` varchar(200) DEFAULT NULL,
  `router_api_password` varchar(200) DEFAULT NULL,
  `router_api_port` int(11) NOT NULL,
  `router_status` int(11) DEFAULT NULL COMMENT '1 = Router is active, 0 means its inactive and no user will be moniored from that router',
  `date_changed` varchar(200) DEFAULT '20230320161856',
  `deleted` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `keyword` varchar(500) NOT NULL,
  `value` longtext NOT NULL,
  `status` int(11) NOT NULL,
  `date_changed` varchar(200) DEFAULT '20230320161856',
  `deleted` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `sms_clients`
--

CREATE TABLE `sms_clients` (
  `client_id` int(11) NOT NULL,
  `client_name` varchar(1000) DEFAULT NULL,
  `client_location` longtext DEFAULT NULL,
  `phone_number` varchar(200) DEFAULT NULL,
  `email` mediumtext DEFAULT NULL,
  `sms_rate` varchar(100) DEFAULT '1',
  `sms_balance` int(11) DEFAULT 0,
  `account_number` varchar(300) DEFAULT NULL,
  `status` int(11) NOT NULL,
  `comments` mediumtext DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `licence_acc_number` varchar(20) DEFAULT NULL,
  `licence_number` varchar(100) DEFAULT NULL,
  `licence_expiry` varchar(100) DEFAULT NULL,
  `packages` varchar(200) DEFAULT NULL,
  `date_joined` varchar(500) DEFAULT '20220803181818',
  `last_changed` varchar(500) NOT NULL DEFAULT '20220803181818',
  `date_changed` varchar(200) DEFAULT '20230320161856',
  `deleted` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `sms_clients_packages`
--

CREATE TABLE `sms_clients_packages` (
  `package_id` int(11) NOT NULL,
  `package_name` varchar(500) DEFAULT NULL,
  `free_trial_period` varchar(500) DEFAULT NULL,
  `payment_intervals` varchar(500) DEFAULT NULL,
  `amount_to_pay` varchar(500) DEFAULT NULL,
  `status` int(11) DEFAULT 1,
  `date_changed` varchar(200) DEFAULT '20230320161856',
  `deleted` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `sms_tables`
--

CREATE TABLE `sms_tables` (
  `sms_id` int(11) NOT NULL,
  `sms_content` mediumtext DEFAULT NULL,
  `date_sent` varchar(20) NOT NULL,
  `recipient_phone` varchar(50) DEFAULT NULL,
  `sms_status` int(11) DEFAULT NULL,
  `account_id` int(11) DEFAULT NULL,
  `sms_type` int(11) NOT NULL DEFAULT 1 COMMENT '1 is transaction 2 is Notification',
  `date_changed` varchar(200) DEFAULT '20230320161856',
  `deleted` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `transaction_sms_tables`
--

CREATE TABLE `transaction_sms_tables` (
  `transaction_id` int(11) NOT NULL,
  `transaction_mpesa_id` varchar(200) DEFAULT NULL,
  `transaction_date` varchar(200) DEFAULT NULL,
  `transacion_amount` int(11) DEFAULT NULL,
  `phone_transacting` varchar(200) DEFAULT NULL,
  `transaction_account` varchar(200) DEFAULT NULL,
  `transaction_acc_id` int(11) DEFAULT NULL,
  `transaction_status` int(11) DEFAULT NULL,
  `transaction_short_code` varchar(200) DEFAULT NULL,
  `fullnames` varchar(200) NOT NULL,
  `date_changed` varchar(200) DEFAULT '20230320161856',
  `deleted` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `transaction_tables`
--

CREATE TABLE `transaction_tables` (
  `transaction_id` int(11) NOT NULL,
  `transaction_mpesa_id` varchar(200) DEFAULT NULL,
  `transaction_date` varchar(200) DEFAULT NULL,
  `transacion_amount` int(11) DEFAULT NULL,
  `phone_transacting` varchar(200) DEFAULT NULL,
  `transaction_account` varchar(200) DEFAULT NULL,
  `transaction_acc_id` int(11) DEFAULT NULL,
  `transaction_status` int(11) DEFAULT NULL,
  `transaction_short_code` varchar(200) DEFAULT NULL,
  `fullnames` varchar(200) NOT NULL,
  `date_changed` varchar(200) DEFAULT '20230320161856',
  `deleted` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `verification_codes`
--

CREATE TABLE `verification_codes` (
  `id` int(11) NOT NULL,
  `code` int(11) NOT NULL,
  `phone_sent` varchar(200) NOT NULL,
  `date_generated` varchar(200) NOT NULL,
  `status` int(11) NOT NULL COMMENT '0 = not used, 1 = already used',
  `date_changed` varchar(200) DEFAULT '20230320161856',
  `deleted` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_tables`
--
ALTER TABLE `admin_tables`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `client_tables`
--
ALTER TABLE `client_tables`
  ADD PRIMARY KEY (`client_id`);

--
-- Indexes for table `Expenses`
--
ALTER TABLE `Expenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `remote_routers`
--
ALTER TABLE `remote_routers`
  ADD PRIMARY KEY (`router_id`);

--
-- Indexes for table `router_tables`
--
ALTER TABLE `router_tables`
  ADD PRIMARY KEY (`router_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sms_clients`
--
ALTER TABLE `sms_clients`
  ADD PRIMARY KEY (`client_id`);

--
-- Indexes for table `sms_clients_packages`
--
ALTER TABLE `sms_clients_packages`
  ADD PRIMARY KEY (`package_id`);

--
-- Indexes for table `sms_tables`
--
ALTER TABLE `sms_tables`
  ADD PRIMARY KEY (`sms_id`);

--
-- Indexes for table `transaction_sms_tables`
--
ALTER TABLE `transaction_sms_tables`
  ADD PRIMARY KEY (`transaction_id`);

--
-- Indexes for table `transaction_tables`
--
ALTER TABLE `transaction_tables`
  ADD PRIMARY KEY (`transaction_id`);

--
-- Indexes for table `verification_codes`
--
ALTER TABLE `verification_codes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_tables`
--
ALTER TABLE `admin_tables`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `client_tables`
--
ALTER TABLE `client_tables`
  MODIFY `client_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `Expenses`
--
ALTER TABLE `Expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `remote_routers`
--
ALTER TABLE `remote_routers`
  MODIFY `router_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `router_tables`
--
ALTER TABLE `router_tables`
  MODIFY `router_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `sms_clients`
--
ALTER TABLE `sms_clients`
  MODIFY `client_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `sms_clients_packages`
--
ALTER TABLE `sms_clients_packages`
  MODIFY `package_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `sms_tables`
--
ALTER TABLE `sms_tables`
  MODIFY `sms_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `transaction_tables`
--
ALTER TABLE `transaction_tables`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `verification_codes`
--
ALTER TABLE `verification_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;