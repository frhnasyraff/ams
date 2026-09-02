

###################
Steve-UI
###################

*******************
*******************
SQL Alter table - 25-04-2022
*******************
ALTER TABLE `incident_requests` DROP `scn`;
ALTER TABLE `incident_requests` ADD `vessel_visit_id` INT(11) NOT NULL AFTER `incident_type_id`;
ALTER TABLE `workers` ADD `leave_override` INT(50) NOT NULL AFTER `standby_rate_override`;
ALTER TABLE `workers` ADD `medical_leave_override` INT(50) NOT NULL AFTER `leave_override`;



###################
Steve-UI
###################

*******************
*******************
SQL Alter table - 01-04-2022
*******************



ALTER TABLE `worker_availability` CHANGE `work_through_meals_pay` `work_through_meals_pay` DECIMAL(10,5) NULL DEFAULT NULL;
ALTER TABLE `worker_availability` CHANGE `rd_ot` `rd_ot` DECIMAL(10,5) NULL DEFAULT NULL;
ALTER TABLE `worker_availability` CHANGE `rd_pay` `rd_pay` DECIMAL(10,5) NULL DEFAULT NULL;
ALTER TABLE `worker_availability` CHANGE `ph_ot` `ph_ot` DECIMAL(10,5) NULL DEFAULT NULL;
ALTER TABLE `worker_availability` CHANGE `ph_pay` `ph_pay` DECIMAL(10,5) NULL DEFAULT NULL;
ALTER TABLE `worker_availability` CHANGE `ot_rate` `ot_rate` DECIMAL(10,5) NULL DEFAULT NULL;
ALTER TABLE `worker_availability` CHANGE `ot_amount` `ot_amount` DECIMAL(10,5) NULL DEFAULT NULL;
ALTER TABLE `worker_availability` CHANGE `lop_amount` `lop_amount` DECIMAL(10,5) NULL DEFAULT NULL;
ALTER TABLE `worker_availability` CHANGE `pay_amount` `pay_amount` DECIMAL(10,5) NULL DEFAULT NULL;
ALTER TABLE `worker_availability` CHANGE `lop_rate` `lop_rate` DECIMAL(10,5) NULL DEFAULT NULL;
ALTER TABLE `worker_availability` CHANGE `pay_rate` `pay_rate` DECIMAL(10,5) NULL DEFAULT NULL;
ALTER TABLE `workers` CHANGE `work_rate_override` `work_rate_override` DECIMAL(10,5) NULL DEFAULT NULL;
ALTER TABLE `workers` CHANGE `standby_rate_override` `standby_rate_override` DECIMAL(10,5) NULL DEFAULT NULL;

###################
Steve-UI
###################

*******************
*******************
SQL Alter table - 22-03-2022
*******************
ALTER TABLE `worker_availability` ADD `vessel_name` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `work_standby`;


ALTER TABLE `worker_availability` ADD `ph_pay` DECIMAL(6,2) NULL DEFAULT NULL AFTER `ot_hours`;
ALTER TABLE `worker_availability` ADD `ph_ot` DECIMAL(6,2) NULL DEFAULT NULL AFTER `ph_pay`, ADD `rd_pay` DECIMAL(6,2) NULL DEFAULT NULL AFTER `ph_ot`, ADD `rd_ot` DECIMAL(6,2) NULL DEFAULT NULL AFTER `rd_pay`;
ALTER TABLE `worker_availability` ADD `work_through_meals_pay` DECIMAL(6,2) NULL DEFAULT NULL AFTER `work_through_meals`;





###################
Steve-UI
###################

*******************
style.css
*******************

.acc-panel{
  margin-bottom: 10px;
  border: 1px solid #ccc;
  padding: 0px 10px;
}

.acc-panel .card-body{
  margin-bottom: 0px;
  padding: 0px;
}

.acc-panel a.acr {
  width: 100%;
  color: #787a8b;
  font-size: 1rem;
  font-weight: 600;
  padding: 10px;
}

.acc-panel a:hover{
  text-decoration: none;
  color: #787a8b;
}





###################
Steve-UI
###################

*******************
SQL Alter table - 24-01-2022
*******************

ALTER TABLE `vessel_visit_workers`  ADD `vessel_visit_workers_id` INT NOT NULL AUTO_INCREMENT  FIRST,  ADD   PRIMARY KEY  (`vessel_visit_workers_id`);
ALTER TABLE `public_holidays`  ADD `public_holiday_id` INT NOT NULL AUTO_INCREMENT  FIRST,  ADD   PRIMARY KEY  (`public_holiday_id`);

ALTER TABLE `workers` CHANGE `standby_rate_to_delete` `standby_rate_override` DECIMAL(6,2) NULL DEFAULT NULL;

*******************
SQL Alter table - 13-01-2022
*******************

 ALTER TABLE worker_availability ADD worker_availability_id int NOT NULL AUTO_INCREMENT primary key First


*******************
SQL new table - 25-12-2021
*******************
-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 26, 2021 at 02:39 AM
-- Server version: 10.4.17-MariaDB
-- PHP Version: 7.4.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `steve_gss`
--

-- --------------------------------------------------------

--
-- Table structure for table `incident_requests`
--

CREATE TABLE `incident_requests` (
  `incident_request_id` int(11) NOT NULL,
  `incident_datetime` datetime NOT NULL,
  `incident_type_id` int(11) NOT NULL,
  `scn` varchar(200) NOT NULL,
  `location_id` int(11) NOT NULL,
  `location_details` text DEFAULT NULL,
  `risk_rating` int(11) NOT NULL,
  `weather` varchar(300) NOT NULL,
  `asset_person` varchar(50) NOT NULL,
  `event_before` text DEFAULT NULL,
  `event_during` text DEFAULT NULL,
  `event_after` text DEFAULT NULL,
  `initial_finding` text DEFAULT NULL,
  `intermediate_action` text DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `incident_request_status` enum('new','approved','planned','in_progress','completed','cancelled','draft') NOT NULL DEFAULT 'new',
  `deleted` int(1) NOT NULL DEFAULT 0,
  `added_by` int(11) NOT NULL,
  `t_added` datetime NOT NULL DEFAULT current_timestamp(),
  `t_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `incident_requests_attachments`
--

CREATE TABLE `incident_requests_attachments` (
  `incident_request_attachment_id` int(11) NOT NULL,
  `incident_request_id` int(11) NOT NULL,
  `filename` text NOT NULL,
  `file_order` int(11) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT NULL,
  `timestamp` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `incident_requests_remarks`
--

CREATE TABLE `incident_requests_remarks` (
  `incident_request_remarks_id` int(11) NOT NULL,
  `incident_request_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `remark` text NOT NULL,
  `t_added` datetime NOT NULL DEFAULT current_timestamp(),
  `t_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `incident_request_asset_details`
--

CREATE TABLE `incident_request_asset_details` (
  `incident_request_asset_details_id` int(11) NOT NULL,
  `incident_request_id` int(11) NOT NULL,
  `asset_type_id` int(11) DEFAULT NULL,
  `damage_part` varchar(300) DEFAULT NULL,
  `type_of_damage` varchar(300) DEFAULT NULL,
  `technical_status` varchar(300) DEFAULT NULL,
  `owner` varchar(300) DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT 0,
  `added_by` int(11) NOT NULL,
  `t_added` datetime NOT NULL DEFAULT current_timestamp(),
  `t_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `incident_request_person_details`
--

CREATE TABLE `incident_request_person_details` (
  `incident_request_person_details_id` int(11) NOT NULL,
  `incident_request_id` int(11) NOT NULL,
  `ic_passport` varchar(100) DEFAULT NULL,
  `name` varchar(300) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `postion_id` int(11) DEFAULT NULL,
  `injured` varchar(50) DEFAULT NULL,
  `injured_part` varchar(100) DEFAULT NULL,
  `type_of_injury` text DEFAULT NULL,
  `cause` text DEFAULT NULL,
  `object_cause_injury` text DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT 0,
  `added_by` int(11) NOT NULL,
  `t_added` datetime NOT NULL DEFAULT current_timestamp(),
  `t_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `incident_requests`
--
ALTER TABLE `incident_requests`
  ADD PRIMARY KEY (`incident_request_id`);

--
-- Indexes for table `incident_requests_attachments`
--
ALTER TABLE `incident_requests_attachments`
  ADD PRIMARY KEY (`incident_request_attachment_id`);

--
-- Indexes for table `incident_requests_remarks`
--
ALTER TABLE `incident_requests_remarks`
  ADD PRIMARY KEY (`incident_request_remarks_id`);

--
-- Indexes for table `incident_request_asset_details`
--
ALTER TABLE `incident_request_asset_details`
  ADD PRIMARY KEY (`incident_request_asset_details_id`);

--
-- Indexes for table `incident_request_person_details`
--
ALTER TABLE `incident_request_person_details`
  ADD PRIMARY KEY (`incident_request_person_details_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `incident_requests`
--
ALTER TABLE `incident_requests`
  MODIFY `incident_request_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `incident_requests_attachments`
--
ALTER TABLE `incident_requests_attachments`
  MODIFY `incident_request_attachment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `incident_requests_remarks`
--
ALTER TABLE `incident_requests_remarks`
  MODIFY `incident_request_remarks_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `incident_request_asset_details`
--
ALTER TABLE `incident_request_asset_details`
  MODIFY `incident_request_asset_details_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `incident_request_person_details`
--
ALTER TABLE `incident_request_person_details`
  MODIFY `incident_request_person_details_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;




###################
Steve-UI
###################

*******************
SQL new table - 07-12-2021
*******************


-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 06, 2021 at 01:47 PM
-- Server version: 10.4.17-MariaDB
-- PHP Version: 7.4.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `steve_gss`
--

-- --------------------------------------------------------

--

--
-- Table structure for table `incident_types`
--

CREATE TABLE `incident_types` (
  `incident_type_id` int(11) NOT NULL,
  `incident_type` varchar(300) NOT NULL,
  `Description` varchar(300) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `masters_companies`
--

CREATE TABLE `masters_companies` (
  `company_id` int(11) NOT NULL,
  `registration_id` varchar(100) DEFAULT NULL,
  `company_name` varchar(300) NOT NULL,
  `contact_person` varchar(300) DEFAULT NULL,
  `contact_email` varchar(100) DEFAULT NULL,
  `business_type` varchar(300) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--

--
-- Indexes for table `incident_types`
--
ALTER TABLE `incident_types`
  ADD PRIMARY KEY (`incident_type_id`),
  ADD UNIQUE KEY `incident_type` (`incident_type`);

--
-- Indexes for table `masters_companies`
--
ALTER TABLE `masters_companies`
  ADD PRIMARY KEY (`company_id`),
  ADD UNIQUE KEY `company_name` (`company_name`,`registration_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--

--
-- AUTO_INCREMENT for table `masters_companies`
--
ALTER TABLE `masters_companies`
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;





INSERT INTO `permissions` (`perm_name`, `perm_cat_id`, `system`) VALUES
('add_masters_companies', 1, 0),
('delete_masters_companies', 1, 0),
('edit_masters_companies', 1, 0),
('list_masters_companies', 1, 0),
('edit_approve_incident_request', 1, 0),
('delete_incident_requests', 1, 0),
('cancel_incident_requests', 1, 0),
('approve_incident_requests', 1, 0),
('add_incident_documents', 1, 0),
('list_incidents_request', 1, 0),
('edit_incidents_request', 1, 0),
('add_incidents_request', 1, 0),
('add_incident_types', 1, 0),
('edit_incident_types', 1, 0),
('list_incident_types', 1, 0);

###################
Steve-UI
###################

*******************
SQL new table - 02-10-2021
*******************




###################
Steve-UI
###################

*******************
SQL new table - 02-10-2021
*******************

ALTER TABLE `worker_availability` ADD `worker_availability_id` INT NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`worker_availability_id`);

CREATE TABLE `steve_gss`.`worker_types` ( `worker_type_id` INT NOT NULL AUTO_INCREMENT , `worker_type` VARCHAR(300) NOT NULL , `max_ot_hours` INT NOT NULL , PRIMARY KEY (`worker_type_id`), UNIQUE (`worker_type`)) ENGINE = InnoDB;

INSERT INTO `worker_types` (`worker_type_id`, `worker_type`, `max_ot_hours`) VALUES (NULL, 'casual-daily', '0'), (NULL, 'contract-daily', '3'),(NULL, 'contract-monthly', '3'),(NULL, 'permanent-office', '3'),(NULL, 'permanent-ops', '3'),(NULL, 'van-driver', '5');

ALTER TABLE `workers` ADD `max_ot_hours` INT(2) NOT NULL AFTER `shift_2`;
ALTER TABLE `worker_availability` ADD `work_through_meals` INT(10) NOT NULL AFTER `ot_hours`;

UPDATE `workers` SET `max_ot_hours` =  
CASE
  WHEN `worker_type` = 'casual-daily' THEN 0
  WHEN `worker_type` = 'contract-daily' THEN 3
  WHEN `worker_type` = 'contract-monthly' THEN 3
  WHEN `worker_type` = 'permanent-office' THEN 3
  WHEN `worker_type` = 'permanent-ops' THEN 3
  WHEN `worker_type` = 'van-driver' THEN 5
END

UPDATE `worker_availability` wa JOIN `workers` t ON (wa.worker_id = t.worker_id)
SET wa.work_through_meals =  
CASE
  WHEN t.worker_type = 'casual-daily' AND wa.work_standby = 0 THEN 1
  WHEN t.worker_type = 'contract-daily' AND wa.work_standby = 0 THEN 1
  WHEN t.worker_type = 'contract-monthly' AND wa.work_standby = 0 THEN 1
  WHEN t.worker_type = 'permanent-office' AND wa.work_standby = 0 THEN 1
  WHEN t.worker_type = 'permanent-ops' AND wa.work_standby = 0 THEN 1
  WHEN t.worker_type = 'van-driver' THEN 0
END

******************
SQL updates - 17/09/2021
******************

ALTER TABLE `service_request_operation_tally` ADD `delay_start` DATETIME NOT NULL AFTER `rebundling_colour`;


*******************
SQL updates - 12/09/2021
*******************

ALTER TABLE `vessel_visit_workers` ADD `vessel_visit_worker_id` INT(10) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`vessel_visit_worker_id`);
ALTER TABLE `vessel_visits` CHANGE `planning_status` `planning_status` ENUM('new','approved','rejected','started','ended','billed') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `service_request_operation_tally` ADD `tally_price` INT(10) NULL DEFAULT NULL AFTER `remarks`;
ALTER TABLE `service_request_operation_tally` ADD `tally_invoice_id` INT(10) NULL DEFAULT NULL AFTER `remarks`;
ALTER TABLE `vessel_visit_equipments` ADD `price` DECIMAL(10,2) NULL AFTER `cost`;
ALTER TABLE `vessel_visit_workers` ADD `price` DECIMAL(10,2) NULL AFTER `cost`;
ALTER TABLE `vessel_visit_gears` ADD `price` DECIMAL(10,2) NULL AFTER `cost`;
ALTER TABLE `vessel_visit_gears` ADD `invoice_id` INT(10) NULL DEFAULT NULL AFTER `cost`;
ALTER TABLE `vessel_visit_equipments` ADD `invoice_id` INT(10) NULL DEFAULT NULL AFTER `cost`;
ALTER TABLE `vessel_visit_workers` ADD `invoice_id` INT(10) NULL DEFAULT NULL AFTER `cost`;
ALTER TABLE `service_request_disposals` ADD `price` DECIMAL(10,2) NULL AFTER `t_end`, ADD `invoice_id` INT(10) NULL AFTER `price`;
ALTER TABLE `service_request_operations` ADD `price` DECIMAL(10,2) NULL AFTER `stowage_row`;
ALTER TABLE `service_requests` ADD `work_meal_price` DECIMAL(10,2) NULL AFTER `deleted`, ADD `work_meal_invoice_id` INT(10) NULL AFTER `work_meal_price`;


*******************
SQL updates - 16/10/2021
*******************

ALTER TABLE `operation_types` ADD `no_stowage` INT(1) NOT NULL DEFAULT '0' AFTER `no_commodity`;
ALTER TABLE `service_request_operations` ADD `deleted` INT(1) NOT NULL DEFAULT '0' AFTER `stowage_row`;
ALTER TABLE `service_request_disposals` ADD `deleted` INT(1) NOT NULL DEFAULT '0' AFTER `location_to`;
ALTER TABLE `service_requests` CHANGE `service_request_status` `service_request_status` ENUM('new','approved','planned','in_progress','completed','cancelled','draft') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'new';
ALTER TABLE `worker_availability` CHANGE `work_through_meals` `work_through_meals` INT(10) NULL;
ALTER TABLE `vessel_visit_workers` CHANGE `worker_resource_type` `worker_resource_type_override` INT(10) NULL DEFAULT NULL;
ALTER TABLE `workers` DROP `worker_shift`;
ALTER TABLE `service_request_operation_tally` CHANGE `delay_start` `delay_start` DATETIME NULL;


*******************
SQL updates - 20/12/2021
*******************

CREATE TABLE `worker_allowances` (
  `worker_allowance_id` int(10) NOT NULL,
  `worker_id` int(10) DEFAULT NULL,
  `month` VARCHAR(7) DEFAULT NULL,
  `allowance_amount` decimal(10,2) DEFAULT NULL,
  `t_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `worker_allowances`
  ADD PRIMARY KEY (`worker_allowance_id`);
ALTER TABLE `worker_allowances`
  MODIFY `worker_allowance_id` int(10) NOT NULL AUTO_INCREMENT;
ALTER TABLE `workers` ADD `monthly_allowance` DECIMAL(10,2) NULL AFTER `payment_effective`;

