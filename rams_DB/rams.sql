-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 14, 2022 at 09:14 AM
-- Server version: 5.7.38
-- PHP Version: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rams`
--

-- --------------------------------------------------------

--
-- Table structure for table `alerts`
--

CREATE TABLE `alerts` (
  `alert_id` int(10) UNSIGNED NOT NULL,
  `recipients` varchar(255) DEFAULT NULL,
  `alert_booking_id` int(10) UNSIGNED DEFAULT NULL,
  `alert_quotation_id` int(10) UNSIGNED DEFAULT NULL,
  `alert_bills_of_lading_id` int(10) UNSIGNED DEFAULT NULL,
  `alert_notices_of_arrival_id` int(10) UNSIGNED DEFAULT NULL,
  `alert_container_release_order_id` int(10) UNSIGNED DEFAULT NULL,
  `alert_debit_note_id` int(10) UNSIGNED DEFAULT NULL,
  `alert_credit_note_id` int(10) UNSIGNED DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `branch_id` int(10) UNSIGNED DEFAULT NULL,
  `record_status` varchar(200) DEFAULT NULL,
  `active` int(1) NOT NULL DEFAULT '0',
  `alert_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `billing_resources`
--

CREATE TABLE `billing_resources` (
  `billing_resources_id` int(11) NOT NULL,
  `billing_resources_name` varchar(45) NOT NULL,
  `billing_resources_shortcode` varchar(45) DEFAULT NULL,
  `active` tinyint(4) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `cargo_packagings`
--

CREATE TABLE `cargo_packagings` (
  `cargo_packaging_id` int(11) NOT NULL,
  `cargo_packaging_name` varchar(100) DEFAULT NULL,
  `description` text,
  `active` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cargo_types`
--

CREATE TABLE `cargo_types` (
  `cargo_type_id` int(11) NOT NULL,
  `cargo_type_name` varchar(100) DEFAULT NULL,
  `description` text,
  `active` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `charges`
--

CREATE TABLE `charges` (
  `charge_id` int(5) UNSIGNED NOT NULL,
  `charge_code` varchar(30) DEFAULT NULL,
  `charge_name` varchar(200) DEFAULT NULL,
  `description` text,
  `movement` enum('export','import') DEFAULT NULL,
  `charge_group` enum('local','port','surcharge','admin','commission','others') DEFAULT NULL,
  `active` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `commodities`
--

CREATE TABLE `commodities` (
  `commodity_id` int(10) UNSIGNED NOT NULL,
  `commodity_code` varchar(50) NOT NULL,
  `description` mediumtext NOT NULL,
  `cargo_packagings` varchar(100) DEFAULT NULL,
  `cargo_types` varchar(100) DEFAULT NULL,
  `active` int(1) NOT NULL DEFAULT '1',
  `t_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `company_id` int(10) UNSIGNED NOT NULL,
  `company_code` varchar(10) DEFAULT NULL,
  `company_name` varchar(200) NOT NULL,
  `vessel_billing_type` enum('commodity','resources') NOT NULL DEFAULT 'resources',
  `warehouse_billing_type` enum('commodity','resources') DEFAULT NULL,
  `notes` text,
  `t_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`company_id`, `company_code`, `company_name`, `vessel_billing_type`, `warehouse_billing_type`, `notes`, `t_updated`) VALUES
(1, 'KG', 'KELLOGG\'S MALAYSIA', 'resources', 'commodity', '', '2022-08-04 22:32:02'),
(2, 'CC', 'COCA-COLA MALAYSIA', 'resources', NULL, '', '2022-08-04 22:32:27'),
(3, 'AJ', 'AJINOMOTO MALAYSIA', 'resources', NULL, '', '2022-08-04 22:32:54'),
(4, 'NS', 'NESTLE MALAYSIA', 'resources', NULL, '', '2022-08-04 22:33:14'),
(5, 'DL', 'DUTCH LADY MALAYSIA', 'resources', NULL, '', '2022-08-04 22:33:34'),
(6, 'PF', 'PETROFAC MALAYSIA', 'resources', NULL, '', '2022-08-04 22:34:09'),
(7, 'MD', 'MONDELEZ MALAYSIA', 'resources', NULL, '', '2022-08-04 22:34:37');

-- --------------------------------------------------------

--
-- Table structure for table `company_addresses`
--

CREATE TABLE `company_addresses` (
  `company_address_id` int(10) UNSIGNED NOT NULL,
  `company_id` int(10) UNSIGNED NOT NULL,
  `location_id` varchar(255) NOT NULL,
  `telephone` varchar(255) NOT NULL,
  `mobile` varchar(255) NOT NULL,
  `person_contact` varchar(255) NOT NULL,
  `designation` int(10) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `address_line_1` varchar(255) NOT NULL,
  `address_line_2` varchar(255) NOT NULL,
  `address_zip` varchar(10) NOT NULL,
  `address_city` varchar(100) NOT NULL,
  `address_state` varchar(100) NOT NULL,
  `address_country` char(2) NOT NULL,
  `finance` int(1) NOT NULL DEFAULT '0',
  `active` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `company_prices`
--

CREATE TABLE `company_prices` (
  `company_price_id` int(10) NOT NULL,
  `company_id` int(10) DEFAULT NULL,
  `equipment_type_id` int(10) DEFAULT NULL,
  `resource_type_id` int(10) DEFAULT NULL,
  `gear_type_id` int(10) DEFAULT NULL,
  `commodity_id` int(10) DEFAULT NULL,
  `selling_price` decimal(10,2) DEFAULT NULL,
  `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `configs`
--

CREATE TABLE `configs` (
  `config_id` int(10) NOT NULL,
  `config_name` varchar(100) DEFAULT NULL,
  `config_value` varchar(200) NOT NULL,
  `t_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `consumables`
--

CREATE TABLE `consumables` (
  `consumable_id` int(10) UNSIGNED NOT NULL,
  `consumable_name` varchar(200) DEFAULT NULL,
  `consumable_notes` text,
  `consumable_stock` decimal(10,2) DEFAULT NULL,
  `consumable_replenishment` decimal(10,2) DEFAULT NULL,
  `active` int(1) NOT NULL DEFAULT '1',
  `t_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `consumables`
--

INSERT INTO `consumables` (`consumable_id`, `consumable_name`, `consumable_notes`, `consumable_stock`, `consumable_replenishment`, `active`, `t_updated`) VALUES
(1, 'dfd', 'vcbvcb', '5.00', '0.00', 1, '2022-08-08 18:56:43');

-- --------------------------------------------------------

--
-- Table structure for table `consumable_purchases`
--

CREATE TABLE `consumable_purchases` (
  `consumable_purchase_id` int(10) NOT NULL,
  `consumable_id` int(10) NOT NULL,
  `purchase_quantity` decimal(10,2) NOT NULL,
  `purchase_order_number` varchar(255) NOT NULL,
  `consumable_purchase_notes` text,
  `consumable_purchase_datetime` datetime NOT NULL,
  `t_inserted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `consumable_purchases`
--

INSERT INTO `consumable_purchases` (`consumable_purchase_id`, `consumable_id`, `purchase_quantity`, `purchase_order_number`, `consumable_purchase_notes`, `consumable_purchase_datetime`, `t_inserted`) VALUES
(1, 1, '5.00', '12', 'aa', '2022-08-04 23:56:00', '2022-08-08 18:56:43');

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `country_id` int(5) UNSIGNED NOT NULL,
  `countrycode` char(3) NOT NULL,
  `countryname` varchar(200) NOT NULL,
  `code` char(2) DEFAULT NULL,
  `active` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`country_id`, `countrycode`, `countryname`, `code`, `active`) VALUES
(1, 'ABW', 'Aruba', 'AW', 0),
(2, 'AFG', 'Afghanistan', 'AF', 0),
(3, 'AGO', 'Angola', 'AO', 0),
(4, 'AIA', 'Anguilla', 'AI', 0),
(5, 'ALA', 'Åland', 'AX', 0),
(6, 'ALB', 'Albania', 'AL', 0),
(7, 'AND', 'Andorra', 'AD', 0),
(8, 'ARE', 'United Arab Emirates', 'AE', 0),
(9, 'ARG', 'Argentina', 'AR', 0),
(10, 'ARM', 'Armenia', 'AM', 0),
(11, 'ASM', 'American Samoa', 'AS', 0),
(12, 'ATA', 'Antarctica', 'AQ', 0),
(13, 'ATF', 'French Southern Territories', 'TF', 0),
(14, 'ATG', 'Antigua and Barbuda', 'AG', 0),
(15, 'AUS', 'Australia', 'AU', 0),
(16, 'AUT', 'Austria', 'AT', 0),
(17, 'AZE', 'Azerbaijan', 'AZ', 0),
(18, 'BDI', 'Burundi', 'BI', 0),
(19, 'BEL', 'Belgium', 'BE', 0),
(20, 'BEN', 'Benin', 'BJ', 0),
(21, 'BES', 'Bonaire', 'BQ', 0),
(22, 'BFA', 'Burkina Faso', 'BF', 0),
(23, 'BGD', 'Bangladesh', 'BD', 0),
(24, 'BGR', 'Bulgaria', 'BG', 0),
(25, 'BHR', 'Bahrain', 'BH', 0),
(26, 'BHS', 'Bahamas', 'BS', 0),
(27, 'BIH', 'Bosnia and Herzegovina', 'BA', 0),
(28, 'BLM', 'Saint Barthélemy', 'BL', 0),
(29, 'BLR', 'Belarus', 'BY', 0),
(30, 'BLZ', 'Belize', 'BZ', 0),
(31, 'BMU', 'Bermuda', 'BM', 0),
(32, 'BOL', 'Bolivia', 'BO', 0),
(33, 'BRA', 'Brazil', 'BR', 0),
(34, 'BRB', 'Barbados', 'BB', 0),
(35, 'BRN', 'Brunei', 'BN', 0),
(36, 'BTN', 'Bhutan', 'BT', 0),
(37, 'BVT', 'Bouvet Island', 'BV', 0),
(38, 'BWA', 'Botswana', 'BW', 0),
(39, 'CAF', 'Central African Republic', 'CF', 0),
(40, 'CAN', 'Canada', 'CA', 0),
(41, 'CCK', 'Cocos [Keeling] Islands', 'CC', 0),
(42, 'CHE', 'Switzerland', 'CH', 0),
(43, 'CHL', 'Chile', 'CL', 0),
(44, 'CHN', 'China', 'CN', 0),
(45, 'CIV', 'Ivory Coast', 'CI', 0),
(46, 'CMR', 'Cameroon', 'CM', 0),
(47, 'COD', 'Democratic Republic of the Congo', 'CD', 0),
(48, 'COG', 'Republic of the Congo', 'CG', 0),
(49, 'COK', 'Cook Islands', 'CK', 0),
(50, 'COL', 'Colombia', 'CO', 0),
(51, 'COM', 'Comoros', 'KM', 0),
(52, 'CPV', 'Cape Verde', 'CV', 0),
(53, 'CRI', 'Costa Rica', 'CR', 0),
(54, 'CUB', 'Cuba', 'CU', 0),
(55, 'CUW', 'Curacao', 'CW', 0),
(56, 'CXR', 'Christmas Island', 'CX', 0),
(57, 'CYM', 'Cayman Islands', 'KY', 0),
(58, 'CYP', 'Cyprus', 'CY', 0),
(59, 'CZE', 'Czech Republic', 'CZ', 0),
(60, 'DEU', 'Germany', 'DE', 0),
(61, 'DJI', 'Djibouti', 'DJ', 0),
(62, 'DMA', 'Dominica', 'DM', 0),
(63, 'DNK', 'Denmark', 'DK', 0),
(64, 'DOM', 'Dominican Republic', 'DO', 0),
(65, 'DZA', 'Algeria', 'DZ', 0),
(66, 'ECU', 'Ecuador', 'EC', 0),
(67, 'EGY', 'Egypt', 'EG', 0),
(68, 'ERI', 'Eritrea', 'ER', 0),
(69, 'ESH', 'Western Sahara', 'EH', 0),
(70, 'ESP', 'Spain', 'ES', 0),
(71, 'EST', 'Estonia', 'EE', 0),
(72, 'ETH', 'Ethiopia', 'ET', 0),
(73, 'FIN', 'Finland', 'FI', 0),
(74, 'FJI', 'Fiji', 'FJ', 0),
(75, 'FLK', 'Falkland Islands', 'FK', 0),
(76, 'FRA', 'France', 'FR', 0),
(77, 'FRO', 'Faroe Islands', 'FO', 0),
(78, 'FSM', 'Micronesia', 'FM', 0),
(79, 'GAB', 'Gabon', 'GA', 0),
(80, 'GBR', 'United Kingdom', 'GB', 0),
(81, 'GEO', 'Georgia', 'GE', 0),
(82, 'GGY', 'Guernsey', 'GG', 0),
(83, 'GHA', 'Ghana', 'GH', 0),
(84, 'GIB', 'Gibraltar', 'GI', 0),
(85, 'GIN', 'Guinea', 'GN', 0),
(86, 'GLP', 'Guadeloupe', 'GP', 0),
(87, 'GMB', 'Gambia', 'GM', 0),
(88, 'GNB', 'Guinea-Bissau', 'GW', 0),
(89, 'GNQ', 'Equatorial Guinea', 'GQ', 0),
(90, 'GRC', 'Greece', 'GR', 0),
(91, 'GRD', 'Grenada', 'GD', 0),
(92, 'GRL', 'Greenland', 'GL', 0),
(93, 'GTM', 'Guatemala', 'GT', 0),
(94, 'GUF', 'French Guiana', 'GF', 0),
(95, 'GUM', 'Guam', 'GU', 0),
(96, 'GUY', 'Guyana', 'GY', 0),
(97, 'HKG', 'Hong Kong', 'HK', 0),
(98, 'HMD', 'Heard Island and McDonald Islands', 'HM', 0),
(99, 'HND', 'Honduras', 'HN', 0),
(100, 'HRV', 'Croatia', 'HR', 0),
(101, 'HTI', 'Haiti', 'HT', 0),
(102, 'HUN', 'Hungary', 'HU', 0),
(103, 'IDN', 'Indonesia', 'ID', 1),
(104, 'IMN', 'Isle of Man', 'IM', 0),
(105, 'IND', 'India', 'IN', 1),
(106, 'IOT', 'British Indian Ocean Territory', 'IO', 0),
(107, 'IRL', 'Ireland', 'IE', 0),
(108, 'IRN', 'Iran', 'IR', 0),
(109, 'IRQ', 'Iraq', 'IQ', 0),
(110, 'ISL', 'Iceland', 'IS', 0),
(111, 'ISR', 'Israel', 'IL', 0),
(112, 'ITA', 'Italy', 'IT', 0),
(113, 'JAM', 'Jamaica', 'JM', 0),
(114, 'JEY', 'Jersey', 'JE', 0),
(115, 'JOR', 'Jordan', 'JO', 0),
(116, 'JPN', 'Japan', 'JP', 0),
(117, 'KAZ', 'Kazakhstan', 'KZ', 0),
(118, 'KEN', 'Kenya', 'KE', 0),
(119, 'KGZ', 'Kyrgyzstan', 'KG', 0),
(120, 'KHM', 'Cambodia', 'KH', 0),
(121, 'KIR', 'Kiribati', 'KI', 0),
(122, 'KNA', 'Saint Kitts and Nevis', 'KN', 0),
(123, 'KOR', 'South Korea', 'KR', 0),
(124, 'KWT', 'Kuwait', 'KW', 0),
(125, 'LAO', 'Laos', 'LA', 0),
(126, 'LBN', 'Lebanon', 'LB', 0),
(127, 'LBR', 'Liberia', 'LR', 0),
(128, 'LBY', 'Libya', 'LY', 0),
(129, 'LCA', 'Saint Lucia', 'LC', 0),
(130, 'LIE', 'Liechtenstein', 'LI', 0),
(131, 'LKA', 'Sri Lanka', 'LK', 0),
(132, 'LSO', 'Lesotho', 'LS', 0),
(133, 'LTU', 'Lithuania', 'LT', 0),
(134, 'LUX', 'Luxembourg', 'LU', 0),
(135, 'LVA', 'Latvia', 'LV', 0),
(136, 'MAC', 'Macao', 'MO', 0),
(137, 'MAF', 'Saint Martin', 'MF', 0),
(138, 'MAR', 'Morocco', 'MA', 0),
(139, 'MCO', 'Monaco', 'MC', 0),
(140, 'MDA', 'Moldova', 'MD', 0),
(141, 'MDG', 'Madagascar', 'MG', 0),
(142, 'MDV', 'Maldives', 'MV', 0),
(143, 'MEX', 'Mexico', 'MX', 0),
(144, 'MHL', 'Marshall Islands', 'MH', 0),
(145, 'MKD', 'Macedonia', 'MK', 0),
(146, 'MLI', 'Mali', 'ML', 0),
(147, 'MLT', 'Malta', 'MT', 0),
(148, 'MMR', 'Myanmar [Burma]', 'MM', 0),
(149, 'MNE', 'Montenegro', 'ME', 0),
(150, 'MNG', 'Mongolia', 'MN', 0),
(151, 'MNP', 'Northern Mariana Islands', 'MP', 0),
(152, 'MOZ', 'Mozambique', 'MZ', 0),
(153, 'MRT', 'Mauritania', 'MR', 0),
(154, 'MSR', 'Montserrat', 'MS', 0),
(155, 'MTQ', 'Martinique', 'MQ', 0),
(156, 'MUS', 'Mauritius', 'MU', 0),
(157, 'MWI', 'Malawi', 'MW', 0),
(158, 'MYS', 'Malaysia', 'MY', 1),
(159, 'MYT', 'Mayotte', 'YT', 0),
(160, 'NAM', 'Namibia', 'NA', 0),
(161, 'NCL', 'New Caledonia', 'NC', 0),
(162, 'NER', 'Niger', 'NE', 0),
(163, 'NFK', 'Norfolk Island', 'NF', 0),
(164, 'NGA', 'Nigeria', 'NG', 0),
(165, 'NIC', 'Nicaragua', 'NI', 0),
(166, 'NIU', 'Niue', 'NU', 0),
(167, 'NLD', 'Netherlands', 'NL', 0),
(168, 'NOR', 'Norway', 'NO', 0),
(169, 'NPL', 'Nepal', 'NP', 0),
(170, 'NRU', 'Nauru', 'NR', 0),
(171, 'NZL', 'New Zealand', 'NZ', 0),
(172, 'OMN', 'Oman', 'OM', 0),
(173, 'PAK', 'Pakistan', 'PK', 0),
(174, 'PAN', 'Panama', 'PA', 0),
(175, 'PCN', 'Pitcairn Islands', 'PN', 0),
(176, 'PER', 'Peru', 'PE', 0),
(177, 'PHL', 'Philippines', 'PH', 0),
(178, 'PLW', 'Palau', 'PW', 0),
(179, 'PNG', 'Papua New Guinea', 'PG', 0),
(180, 'POL', 'Poland', 'PL', 0),
(181, 'PRI', 'Puerto Rico', 'PR', 0),
(182, 'PRK', 'North Korea', 'KP', 0),
(183, 'PRT', 'Malaysia', 'PT', 0),
(184, 'PRY', 'Paraguay', 'PY', 0),
(185, 'PSE', 'Palestine', 'PS', 0),
(186, 'PYF', 'French Polynesia', 'PF', 0),
(187, 'QAT', 'Qatar', 'QA', 0),
(188, 'REU', 'Réunion', 'RE', 0),
(189, 'ROU', 'Romania', 'RO', 0),
(190, 'RUS', 'Russia', 'RU', 0),
(191, 'RWA', 'Rwanda', 'RW', 0),
(192, 'SAU', 'Saudi Arabia', 'SA', 0),
(193, 'SDN', 'Sudan', 'SD', 0),
(194, 'SEN', 'Senegal', 'SN', 0),
(195, 'SGP', 'Singapore', 'SG', 0),
(196, 'SGS', 'South Georgia and the South Sandwich Islands', 'GS', 0),
(197, 'SHN', 'Saint Helena', 'SH', 0),
(198, 'SJM', 'Svalbard and Jan Mayen', 'SJ', 0),
(199, 'SLB', 'Solomon Islands', 'SB', 0),
(200, 'SLE', 'Sierra Leone', 'SL', 0),
(201, 'SLV', 'El Salvador', 'SV', 0),
(202, 'SMR', 'San Marino', 'SM', 0),
(203, 'SOM', 'Somalia', 'SO', 0),
(204, 'SPM', 'Saint Pierre and Miquelon', 'PM', 0),
(205, 'SRB', 'Serbia', 'RS', 0),
(206, 'SSD', 'South Sudan', 'SS', 0),
(207, 'STP', 'São Tomé and Príncipe', 'ST', 0),
(208, 'SUR', 'Suriname', 'SR', 0),
(209, 'SVK', 'Slovakia', 'SK', 0),
(210, 'SVN', 'Slovenia', 'SI', 0),
(211, 'SWE', 'Sweden', 'SE', 0),
(212, 'SWZ', 'Swaziland', 'SZ', 0),
(213, 'SXM', 'Sint Maarten', 'SX', 0),
(214, 'SYC', 'Seychelles', 'SC', 0),
(215, 'SYR', 'Syria', 'SY', 0),
(216, 'TCA', 'Turks and Caicos Islands', 'TC', 0),
(217, 'TCD', 'Chad', 'TD', 0),
(218, 'TGO', 'Togo', 'TG', 0),
(219, 'THA', 'Thailand', 'TH', 0),
(220, 'TJK', 'Tajikistan', 'TJ', 0),
(221, 'TKL', 'Tokelau', 'TK', 0),
(222, 'TKM', 'Turkmenistan', 'TM', 0),
(223, 'TLS', 'East Timor', 'TL', 0),
(224, 'TON', 'Tonga', 'TO', 0),
(225, 'TTO', 'Trinidad and Tobago', 'TT', 0),
(226, 'TUN', 'Tunisia', 'TN', 0),
(227, 'TUR', 'Turkey', 'TR', 0),
(228, 'TUV', 'Tuvalu', 'TV', 0),
(229, 'TWN', 'Taiwan', 'TW', 0),
(230, 'TZA', 'Tanzania', 'TZ', 0),
(231, 'UGA', 'Uganda', 'UG', 0),
(232, 'UKR', 'Ukraine', 'UA', 0),
(233, 'UMI', 'U.S. Minor Outlying Islands', 'UM', 0),
(234, 'URY', 'Uruguay', 'UY', 0),
(235, 'USA', 'United States', 'US', 0),
(236, 'UZB', 'Uzbekistan', 'UZ', 0),
(237, 'VAT', 'Vatican City', 'VA', 0),
(238, 'VCT', 'Saint Vincent and the Grenadines', 'VC', 0),
(239, 'VEN', 'Venezuela', 'VE', 0),
(240, 'VGB', 'British Virgin Islands', 'VG', 0),
(241, 'VIR', 'U.S. Virgin Islands', 'VI', 0),
(242, 'VNM', 'Vietnam', 'VN', 0),
(243, 'VUT', 'Vanuatu', 'VU', 0),
(244, 'WLF', 'Wallis and Futuna', 'WF', 0),
(245, 'WSM', 'Samoa', 'WS', 0),
(246, 'XKX', 'Kosovo', 'XK', 0),
(247, 'YEM', 'Yemen', 'YE', 0),
(248, 'ZAF', 'South Africa', 'ZA', 0),
(249, 'ZMB', 'Zambia', 'ZM', 0),
(250, 'ZWE', 'Zimbabwe', 'ZW', 0);

-- --------------------------------------------------------

--
-- Table structure for table `delay_reasons`
--

CREATE TABLE `delay_reasons` (
  `delay_reason_id` int(11) NOT NULL,
  `delay_reason_name` varchar(100) DEFAULT NULL,
  `description` text,
  `delay_charge` decimal(10,2) NOT NULL DEFAULT '0.00',
  `delay_min_hours` int(2) DEFAULT NULL,
  `active` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `designations`
--

CREATE TABLE `designations` (
  `designation_id` int(10) UNSIGNED NOT NULL,
  `designation_name` varchar(255) NOT NULL,
  `description` mediumtext NOT NULL,
  `active` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `designations`
--

INSERT INTO `designations` (`designation_id`, `designation_name`, `description`, `active`) VALUES
(1, 'Class D', '', 1),
(2, 'Class DA', '', 1),
(3, 'Class E1', '', 1),
(4, 'Class E2', '', 1),
(5, 'Class F', '', 1),
(6, 'Class G', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `equipments`
--

CREATE TABLE `equipments` (
  `equipment_id` int(10) UNSIGNED NOT NULL,
  `equipment_registration` varchar(30) DEFAULT NULL,
  `equipment_name` varchar(200) DEFAULT NULL,
  `equipment_picture` varchar(150) DEFAULT NULL,
  `equipment_manufacturer` int(10) UNSIGNED DEFAULT NULL,
  `equipment_type` int(10) UNSIGNED DEFAULT NULL,
  `equipment_status` enum('In use','Maintenance','Standby') DEFAULT NULL,
  `current_mileage` decimal(12,2) DEFAULT NULL,
  `service_every_mileage` decimal(12,2) DEFAULT NULL,
  `next_service_mileage` decimal(12,2) DEFAULT NULL,
  `last_service_date` date DEFAULT NULL,
  `service_interval_weeks` int(10) DEFAULT NULL,
  `next_service_date` date DEFAULT NULL,
  `worked_days` int(3) NOT NULL DEFAULT '0',
  `equipment_notes` text,
  `equipment_safe_load` varchar(255) DEFAULT NULL,
  `active` int(1) NOT NULL DEFAULT '1',
  `purchase_date` date DEFAULT NULL,
  `t_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `qr_code` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `equipments`
--

INSERT INTO `equipments` (`equipment_id`, `equipment_registration`, `equipment_name`, `equipment_picture`, `equipment_manufacturer`, `equipment_type`, `equipment_status`, `current_mileage`, `service_every_mileage`, `next_service_mileage`, `last_service_date`, `service_interval_weeks`, `next_service_date`, `worked_days`, `equipment_notes`, `equipment_safe_load`, `active`, `purchase_date`, `t_updated`, `qr_code`) VALUES
(1, 'CBA123', 'COMPACT_1', '1660046982-Screenshot 2022-08-09 at 8.09.22 PM.png', 3, 6, 'In use', NULL, NULL, '44.00', NULL, 0, '2022-08-04', 0, '', '100MT', 1, '2022-08-01', '2022-08-09 12:09:42', 1),
(2, 'WTW123', 'MINI_1', '1660046902-Screenshot 2022-08-09 at 8.08.34 PM.png', 2, 1, 'In use', '554.00', NULL, NULL, NULL, 0, NULL, 0, '', '', 1, '2022-08-01', '2022-08-09 12:08:22', 1),
(3, 'ABD123', 'RORO_2', '1660046760-Screenshot 2022-08-09 at 8.05.33 PM.png', 1, 11, 'In use', '12.00', NULL, NULL, NULL, 0, NULL, 0, '', '80MT', 1, '2022-08-01', '2022-08-09 12:06:00', 0),
(4, 'ABC123', 'RORO_4', '1660044994-Screenshot 2022-08-09 at 7.36.46 PM.png', 1, 11, 'In use', NULL, NULL, NULL, NULL, 0, NULL, 0, '', '80MT', 1, '2022-08-01', '2022-08-09 11:36:34', 0),
(5, 'VBY9112', 'RORO_1', '1660044841-Screenshot 2022-08-09 at 7.34.12 PM.png', 2, 11, 'In use', '800.00', NULL, '25000.00', NULL, 0, '2022-08-31', 0, '', '5MT', 1, '2022-08-01', '2022-08-09 11:35:48', 0),
(6, 'CBB123', 'COMPACT_2', '1660046956-Screenshot 2022-08-09 at 8.09.26 PM.png', 3, 6, 'In use', NULL, NULL, NULL, NULL, 0, NULL, 0, '', '100MT', 1, '2022-08-01', '2022-08-09 12:09:16', 0),
(7, 'AAY993', 'UW_1', '1660047148-Screenshot 2022-08-09 at 8.12.30 PM.png', 5, 7, 'In use', '2.00', '5.00', '11.00', '2022-08-14', 2, '2022-08-08', 0, 'aa', 'aa', 1, '2022-08-02', '2022-08-09 14:27:20', 1),
(8, 'JDT112', 'UW_2', '1660047306-Screenshot 2022-08-09 at 8.12.00 PM.png', 5, 7, 'In use', NULL, NULL, NULL, NULL, 0, NULL, 0, '', '', 1, '2022-08-01', '2022-08-10 03:14:08', 1);

-- --------------------------------------------------------

--
-- Table structure for table `equipments_asset`
--

CREATE TABLE `equipments_asset` (
  `equipment_id` int(10) UNSIGNED NOT NULL,
  `equipment_registration` varchar(30) DEFAULT NULL,
  `equipment_name` varchar(200) DEFAULT NULL,
  `equipment_picture` varchar(150) DEFAULT NULL,
  `equipment_manufacturer` int(10) UNSIGNED DEFAULT NULL,
  `equipment_type` int(10) UNSIGNED DEFAULT NULL,
  `equipment_status` enum('In use','Maintenance','Standby') DEFAULT NULL,
  `current_mileage` decimal(12,2) DEFAULT NULL,
  `service_every_mileage` decimal(12,2) DEFAULT NULL,
  `next_service_mileage` decimal(12,2) DEFAULT NULL,
  `last_service_date` date DEFAULT NULL,
  `service_interval_weeks` int(10) DEFAULT NULL,
  `next_service_date` date DEFAULT NULL,
  `worked_days` int(3) NOT NULL DEFAULT '0',
  `equipment_notes` text,
  `equipment_safe_load` varchar(255) DEFAULT NULL,
  `active` int(1) NOT NULL DEFAULT '1',
  `purchase_date` date DEFAULT NULL,
  `t_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `qr_code` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `equipments_asset`
--

INSERT INTO `equipments_asset` (`equipment_id`, `equipment_registration`, `equipment_name`, `equipment_picture`, `equipment_manufacturer`, `equipment_type`, `equipment_status`, `current_mileage`, `service_every_mileage`, `next_service_mileage`, `last_service_date`, `service_interval_weeks`, `next_service_date`, `worked_days`, `equipment_notes`, `equipment_safe_load`, `active`, `purchase_date`, `t_updated`, `qr_code`) VALUES
(1, 'SW22919991', 'RRB_2', '1660047515-Screenshot 2022-08-09 at 8.18.22 PM.png', 8, 12, 'In use', NULL, NULL, '44.00', NULL, 0, '2022-08-04', 0, '', '10MT', 1, '2022-08-01', '2022-08-09 14:24:21', 1),
(6, 'SW1129991', 'RRB_1', '1660031267-Screenshot 2022-08-09 at 2.32.23 PM.png', 8, 12, 'In use', NULL, NULL, NULL, NULL, 0, NULL, 0, '', '10MT', 1, '2022-08-01', '2022-08-09 12:17:03', 1),
(8, 'SW18299292', 'RRB_3', '1660054927-Screenshot 2022-08-09 at 8.18.15 PM.png', 8, 12, 'In use', NULL, NULL, NULL, NULL, 0, NULL, 0, '', '10MT', 1, '2022-08-01', '2022-08-09 14:22:13', 1);

-- --------------------------------------------------------

--
-- Table structure for table `equipment_consumables`
--

CREATE TABLE `equipment_consumables` (
  `equipment_consumable_id` int(12) UNSIGNED NOT NULL,
  `equipment_id` int(10) UNSIGNED DEFAULT NULL,
  `consumable_id` int(10) UNSIGNED DEFAULT NULL,
  `quantity` decimal(12,2) DEFAULT NULL,
  `date_recorded` date DEFAULT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `equipment_consumables`
--

INSERT INTO `equipment_consumables` (`equipment_consumable_id`, `equipment_id`, `consumable_id`, `quantity`, `date_recorded`, `date_updated`) VALUES
(1, 7, 1, '5.00', '2022-08-04', '2022-08-08 18:41:55');

-- --------------------------------------------------------

--
-- Table structure for table `equipment_consumables_asset`
--

CREATE TABLE `equipment_consumables_asset` (
  `equipment_consumable_id` int(12) UNSIGNED NOT NULL,
  `equipment_id` int(10) UNSIGNED DEFAULT NULL,
  `consumable_id` int(10) UNSIGNED DEFAULT NULL,
  `quantity` decimal(12,2) DEFAULT NULL,
  `date_recorded` date DEFAULT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `equipment_consumables_asset`
--

INSERT INTO `equipment_consumables_asset` (`equipment_consumable_id`, `equipment_id`, `consumable_id`, `quantity`, `date_recorded`, `date_updated`) VALUES
(1, 5, 1, '2.00', '2022-08-04', '2022-08-05 03:28:17');

-- --------------------------------------------------------

--
-- Table structure for table `equipment_group`
--

CREATE TABLE `equipment_group` (
  `equipment_id` int(10) UNSIGNED NOT NULL,
  `equipment_group_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `equipment_group`
--

INSERT INTO `equipment_group` (`equipment_id`, `equipment_group_id`) VALUES
(22, 2),
(10, 2),
(11, 2),
(24, 2),
(23, 2),
(21, 2),
(19, 2),
(25, 2),
(18, 2),
(20, 2),
(16, 2),
(17, 2),
(13, 2),
(12, 2),
(9, 2),
(15, 2),
(14, 2),
(26, 3),
(27, 3),
(50, 4),
(49, 4),
(52, 4),
(53, 4),
(51, 4),
(36, 5),
(35, 5),
(33, 5),
(32, 5),
(31, 5),
(30, 5),
(47, 5),
(29, 5),
(46, 5),
(45, 5),
(44, 5),
(43, 5),
(42, 5),
(41, 5),
(40, 5),
(39, 5),
(38, 5),
(37, 5),
(28, 5),
(6, 5),
(5, 3),
(2, 5),
(4, 5),
(4, 6),
(3, 1),
(3, 3),
(3, 5),
(1, 5),
(7, 1),
(7, 6),
(8, 6);

-- --------------------------------------------------------

--
-- Table structure for table `equipment_groups`
--

CREATE TABLE `equipment_groups` (
  `equipment_group_id` int(10) UNSIGNED NOT NULL,
  `equipment_group_name` varchar(200) DEFAULT NULL,
  `equipment_group_code` varchar(20) DEFAULT NULL,
  `equipment_group_notes` text,
  `active` int(1) NOT NULL DEFAULT '1',
  `t_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `equipment_groups`
--

INSERT INTO `equipment_groups` (`equipment_group_id`, `equipment_group_name`, `equipment_group_code`, `equipment_group_notes`, `active`, `t_updated`) VALUES
(1, 'General', '', '', 1, '2022-08-04 22:14:57'),
(2, 'Grass Cutting', 'GC', '', 1, '2022-08-04 22:19:15'),
(3, 'Recycle Waste', 'RW', '', 1, '2022-08-04 22:17:18'),
(4, 'Motorcycle', 'MC', '', 1, '2022-08-04 22:17:37'),
(5, 'Waste Management', 'WM', '', 1, '2022-08-04 22:15:41'),
(6, 'General Cleaning', 'GC', '', 1, '2022-08-04 22:19:37');

-- --------------------------------------------------------

--
-- Table structure for table `equipment_groups_asset`
--

CREATE TABLE `equipment_groups_asset` (
  `equipment_group_id` int(10) UNSIGNED NOT NULL,
  `equipment_group_name` varchar(200) DEFAULT NULL,
  `equipment_group_code` varchar(20) DEFAULT NULL,
  `equipment_group_notes` text,
  `active` int(1) NOT NULL DEFAULT '1',
  `t_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `equipment_groups_asset`
--

INSERT INTO `equipment_groups_asset` (`equipment_group_id`, `equipment_group_name`, `equipment_group_code`, `equipment_group_notes`, `active`, `t_updated`) VALUES
(1, 'General', '', '', 1, '2022-08-04 22:14:57'),
(2, 'Grass Cutting', 'GC', '', 1, '2022-08-04 22:19:15'),
(3, 'Recycle Waste', 'RW', '', 1, '2022-08-04 22:17:18'),
(4, 'Motorcycle', 'MC', '', 1, '2022-08-04 22:17:37'),
(5, 'Waste Management', 'WM', '', 1, '2022-08-04 22:15:41'),
(6, 'General Cleaning', 'GC', '', 1, '2022-08-04 22:19:37'),
(7, 'aa', 'BB', 'cc', 1, '2022-08-05 03:32:26'),
(8, 'aa', 'BB', 'cc', 1, '2022-08-08 18:58:33');

-- --------------------------------------------------------

--
-- Table structure for table `equipment_group_asset`
--

CREATE TABLE `equipment_group_asset` (
  `equipment_id` int(10) UNSIGNED NOT NULL,
  `equipment_group_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `equipment_group_asset`
--

INSERT INTO `equipment_group_asset` (`equipment_id`, `equipment_group_id`) VALUES
(22, 2),
(10, 2),
(11, 2),
(24, 2),
(23, 2),
(21, 2),
(19, 2),
(25, 2),
(18, 2),
(20, 2),
(16, 2),
(17, 2),
(13, 2),
(12, 2),
(9, 2),
(3, 2),
(15, 2),
(8, 2),
(14, 2),
(26, 3),
(27, 3),
(50, 4),
(49, 4),
(52, 4),
(53, 4),
(51, 4),
(36, 5),
(35, 5),
(33, 5),
(32, 5),
(31, 5),
(30, 5),
(47, 5),
(29, 5),
(46, 5),
(45, 5),
(44, 5),
(43, 5),
(42, 5),
(41, 5),
(40, 5),
(39, 5),
(38, 5),
(37, 5),
(28, 5),
(1, 2),
(4, 5),
(6, 5),
(5, 3),
(2, 5);

-- --------------------------------------------------------

--
-- Table structure for table `equipment_maintenance`
--

CREATE TABLE `equipment_maintenance` (
  `equipment_maintenance_id` int(12) UNSIGNED NOT NULL,
  `equipment_id` int(10) UNSIGNED DEFAULT NULL,
  `maintenance_date` date DEFAULT NULL,
  `in_out` enum('In maintenance','Out of maintenance') DEFAULT NULL,
  `maintenance_mileage` decimal(10,2) DEFAULT NULL,
  `maintenance_notes` text,
  `maintenance_files` text,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `equipment_maintenance`
--

INSERT INTO `equipment_maintenance` (`equipment_maintenance_id`, `equipment_id`, `maintenance_date`, `in_out`, `maintenance_mileage`, `maintenance_notes`, `maintenance_files`, `date_updated`) VALUES
(1, 7, '2022-08-01', 'In maintenance', '5.00', '', '{\"files\":[\"1659977759-aaaaaaa.png\"]}', '2022-08-08 16:56:22');

-- --------------------------------------------------------

--
-- Table structure for table `equipment_maintenance_asset`
--

CREATE TABLE `equipment_maintenance_asset` (
  `equipment_maintenance_id` int(12) UNSIGNED NOT NULL,
  `equipment_id` int(10) UNSIGNED DEFAULT NULL,
  `maintenance_date` date DEFAULT NULL,
  `in_out` enum('In maintenance','Out of maintenance') DEFAULT NULL,
  `maintenance_mileage` decimal(10,2) DEFAULT NULL,
  `maintenance_notes` text,
  `maintenance_files` text,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `equipment_maintenance_asset`
--

INSERT INTO `equipment_maintenance_asset` (`equipment_maintenance_id`, `equipment_id`, `maintenance_date`, `in_out`, `maintenance_mileage`, `maintenance_notes`, `maintenance_files`, `date_updated`) VALUES
(1, 5, '2022-08-03', 'In maintenance', NULL, 'ssss', '', '2022-08-05 03:27:30');

-- --------------------------------------------------------

--
-- Table structure for table `equipment_mileage`
--

CREATE TABLE `equipment_mileage` (
  `equipment_mileage_id` int(12) UNSIGNED NOT NULL,
  `equipment_id` int(10) UNSIGNED DEFAULT NULL,
  `mileage` decimal(12,2) DEFAULT NULL,
  `date_recorded` date DEFAULT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `equipment_mileage`
--

INSERT INTO `equipment_mileage` (`equipment_mileage_id`, `equipment_id`, `mileage`, `date_recorded`, `date_updated`) VALUES
(1, 5, '81722.00', '2022-08-05', '2022-08-04 22:58:58'),
(2, 3, '12.00', '2022-08-08', '2022-08-08 16:27:14'),
(3, 5, '800.00', '2022-08-09', '2022-08-08 23:21:59');

-- --------------------------------------------------------

--
-- Table structure for table `equipment_mileage_asset`
--

CREATE TABLE `equipment_mileage_asset` (
  `equipment_mileage_id` int(12) UNSIGNED NOT NULL,
  `equipment_id` int(10) UNSIGNED DEFAULT NULL,
  `mileage` decimal(12,2) DEFAULT NULL,
  `date_recorded` date DEFAULT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `equipment_mileage_asset`
--

INSERT INTO `equipment_mileage_asset` (`equipment_mileage_id`, `equipment_id`, `mileage`, `date_recorded`, `date_updated`) VALUES
(1, 5, '81722.00', '2022-08-05', '2022-08-04 22:58:58');

-- --------------------------------------------------------

--
-- Table structure for table `equipment_types`
--

CREATE TABLE `equipment_types` (
  `equipment_type_id` int(11) NOT NULL,
  `equipment_type_name` varchar(100) DEFAULT NULL,
  `equipment_type_short_code` varchar(6) DEFAULT NULL,
  `equipment_type_colour` varchar(7) DEFAULT NULL,
  `equipment_type_cost` decimal(10,2) DEFAULT NULL,
  `equipment_type_fuel_cost` decimal(5,2) DEFAULT NULL,
  `operator_id` int(10) DEFAULT NULL,
  `description` text,
  `active` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `equipment_types`
--

INSERT INTO `equipment_types` (`equipment_type_id`, `equipment_type_name`, `equipment_type_short_code`, `equipment_type_colour`, `equipment_type_cost`, `equipment_type_fuel_cost`, `operator_id`, `description`, `active`) VALUES
(1, 'Mini Compactor', 'MC', '#193fd8', NULL, NULL, 2, '', 1),
(2, 'Bulk Waste Tipper', 'BWT', '#42f8ff', NULL, NULL, 3, '', 1),
(3, 'Box Van', 'BV', '#dde500', NULL, NULL, 3, '', 1),
(4, 'Cleaning Machinery', 'CM', '#f90acb', NULL, NULL, 4, '', 1),
(5, 'Side Car', 'SC', '#ff6b01', NULL, NULL, 3, '', 1),
(6, 'Compactor', 'CPT', '#c047e8', NULL, NULL, 2, '', 1),
(7, 'Ultra Whack', 'UW', '#35a723', NULL, NULL, 4, 'BIN FOR RORO TRUCK', 1),
(8, 'Water Jetter', 'WJ', '#f60b30', NULL, NULL, 4, '', 1),
(9, 'Beach Comber', 'BC', '#6fdddd', NULL, NULL, 4, '', 1),
(10, 'Mini Ride On Movers', 'MR', '#ef240a', NULL, NULL, 4, '', 1),
(11, 'Arm Roll RORO', 'RORO', '#06a379', NULL, NULL, 1, '', 1),
(12, 'RORO Bin', 'RRB', '#89f4bd', NULL, NULL, 1, '', 1),
(13, 'Waste Bin', 'WB', '#fc6577', NULL, NULL, 3, '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `equipment_types_asset`
--

CREATE TABLE `equipment_types_asset` (
  `equipment_type_id` int(11) NOT NULL,
  `equipment_type_name` varchar(100) DEFAULT NULL,
  `equipment_type_short_code` varchar(6) DEFAULT NULL,
  `equipment_type_colour` varchar(7) DEFAULT NULL,
  `equipment_type_cost` decimal(10,2) DEFAULT NULL,
  `equipment_type_fuel_cost` decimal(5,2) DEFAULT NULL,
  `operator_id` int(10) DEFAULT NULL,
  `description` text,
  `active` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `equipment_types_asset`
--

INSERT INTO `equipment_types_asset` (`equipment_type_id`, `equipment_type_name`, `equipment_type_short_code`, `equipment_type_colour`, `equipment_type_cost`, `equipment_type_fuel_cost`, `operator_id`, `description`, `active`) VALUES
(1, 'Mini Compactor', 'MC', '#193fd8', NULL, NULL, 2, '', 1),
(2, 'Bulk Waste Tipper', 'BWT', '#42f8ff', NULL, NULL, 3, '', 1),
(3, 'Box Van', 'BV', '#dde500', NULL, NULL, 3, '', 1),
(4, 'Cleaning Machinery', 'CM', '#f90acb', NULL, NULL, 4, '', 1),
(5, 'Side Car', 'SC', '#ff6b01', NULL, NULL, 3, '', 1),
(6, 'Compactor', 'CPT', '#c047e8', NULL, NULL, 2, '', 1),
(7, 'Ultra Whack', 'UW', '#35a723', NULL, NULL, 4, 'BIN FOR RORO TRUCK', 1),
(8, 'Water Jetter', 'WJ', '#f60b30', NULL, NULL, 4, '', 1),
(9, 'Beach Comber', 'BC', '#6fdddd', NULL, NULL, 4, '', 1),
(10, 'Mini Ride On Movers', 'MR', '#ef240a', NULL, NULL, 4, '', 1),
(11, 'Arm Roll RORO', 'RORO', '#06a379', NULL, NULL, 1, '', 1),
(12, 'RORO Bin', 'RRB', '#89f4bd', NULL, NULL, 1, '', 1),
(13, 'Waste Bin', 'WB', '#fc6577', NULL, NULL, 3, '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `exchange_rates`
--

CREATE TABLE `exchange_rates` (
  `exchange_rate_id` int(10) NOT NULL,
  `date` date NOT NULL,
  `USD` double NOT NULL,
  `MYR` double NOT NULL,
  `INR` double NOT NULL,
  `EUR` double NOT NULL,
  `THB` double NOT NULL,
  `IDR` double NOT NULL,
  `PHP` double NOT NULL,
  `SGD` double NOT NULL,
  `BDT` double DEFAULT NULL,
  `VND` double DEFAULT NULL,
  `CNY` double DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `exchange_rates`
--

INSERT INTO `exchange_rates` (`exchange_rate_id`, `date`, `USD`, `MYR`, `INR`, `EUR`, `THB`, `IDR`, `PHP`, `SGD`, `BDT`, `VND`, `CNY`, `timestamp`) VALUES
(1, '2019-08-02', 1, 4.1574824419, 69.6380334954, 0.9004141905, 30.7554475059, 14201.737799388, 51.6000360166, 1.3767332973, NULL, NULL, NULL, '2019-08-03 05:19:15'),
(3, '2019-08-02', 1, 4.1574824419, 69.6380334954, 0.9004141905, 30.7554475059, 14201.737799388, 51.6000360166, 1.3767332973, NULL, NULL, NULL, '2019-08-04 11:09:15'),
(4, '2019-08-02', 1, 4.1574824419, 69.6380334954, 0.9004141905, 30.7554475059, 14201.737799388, 51.6000360166, 1.3767332973, NULL, NULL, NULL, '2019-08-04 11:09:32'),
(5, '2019-08-02', 1, 4.1574824419, 69.6380334954, 0.9004141905, 30.7554475059, 14201.737799388, 51.6000360166, 1.3767332973, NULL, NULL, NULL, '2019-08-05 01:30:48'),
(6, '2019-08-02', 1, 4.1574824419, 69.6380334954, 0.9004141905, 30.7554475059, 14201.737799388, 51.6000360166, 1.3767332973, NULL, NULL, NULL, '2019-08-05 02:24:59'),
(7, '2019-08-02', 1, 4.1574824419, 69.6380334954, 0.9004141905, 30.7554475059, 14201.737799388, 51.6000360166, 1.3767332973, NULL, NULL, NULL, '2019-08-05 15:38:09'),
(8, '2019-08-26', 1, 4.2054695934, 72.025008996, 0.8996041742, 30.5901403383, 14244.746311623, 52.4712126664, 1.3882691616, NULL, NULL, NULL, '2019-08-26 17:44:18'),
(9, '2019-10-02', 1, 4.1945080092, 71.23798627, 0.9153318078, 30.6251716247, 14203.00228833, 51.9963386728, 1.3855377574, NULL, NULL, 7.148375286, '2019-10-03 15:32:13'),
(10, '2019-10-02', 1, 4.1945080092, 71.23798627, 0.9153318078, 30.6251716247, 14203.00228833, 51.9963386728, 1.3855377574, NULL, NULL, 7.148375286, '2019-10-03 15:32:15');

-- --------------------------------------------------------

--
-- Table structure for table `gears`
--

CREATE TABLE `gears` (
  `gear_id` int(10) UNSIGNED NOT NULL,
  `gear_name` varchar(200) DEFAULT NULL,
  `gear_type` int(10) UNSIGNED DEFAULT NULL,
  `current_quantity` int(10) DEFAULT NULL,
  `gear_notes` text,
  `purchase_date` date DEFAULT NULL,
  `active` int(1) NOT NULL DEFAULT '1',
  `damaged` int(1) NOT NULL DEFAULT '0',
  `t_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `gears`
--

INSERT INTO `gears` (`gear_id`, `gear_name`, `gear_type`, `current_quantity`, `gear_notes`, `purchase_date`, `active`, `damaged`, `t_updated`) VALUES
(1, '32', 1, NULL, 'ss', '2022-08-02', 1, 0, '2022-08-04 20:44:39');

-- --------------------------------------------------------

--
-- Table structure for table `gear_purchases`
--

CREATE TABLE `gear_purchases` (
  `gear_purchase_id` int(10) UNSIGNED NOT NULL,
  `gear_id` int(10) NOT NULL,
  `quantity` int(10) NOT NULL DEFAULT '0',
  `purchase_date` date DEFAULT NULL,
  `gear_purchase_notes` text NOT NULL,
  `active` int(1) NOT NULL DEFAULT '1',
  `t_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `gear_types`
--

CREATE TABLE `gear_types` (
  `gear_type_id` int(11) NOT NULL,
  `gear_type_name` varchar(100) DEFAULT NULL,
  `gear_type_short_code` varchar(8) DEFAULT NULL,
  `gear_type_colour` varchar(7) DEFAULT NULL,
  `gear_type_cost` decimal(10,2) DEFAULT NULL,
  `gear_type_seq` int(10) NOT NULL DEFAULT '1',
  `description` text,
  `active` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `gear_types`
--

INSERT INTO `gear_types` (`gear_type_id`, `gear_type_name`, `gear_type_short_code`, `gear_type_colour`, `gear_type_cost`, `gear_type_seq`, `description`, `active`) VALUES
(1, '10m Shackle', '10SH', '#ff1515', NULL, 1, '', 1),
(2, '10m Wire Sling', '10WS', '#d9e44e', NULL, 1, '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `incident_requests`
--

CREATE TABLE `incident_requests` (
  `incident_request_id` int(11) NOT NULL,
  `incident_datetime` datetime NOT NULL,
  `incident_type_id` int(11) NOT NULL,
  `vessel_visit_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `location_details` text,
  `risk_rating` int(11) NOT NULL,
  `weather` varchar(300) NOT NULL,
  `asset_person` varchar(50) NOT NULL,
  `event_before` text,
  `event_during` text,
  `event_after` text,
  `initial_finding` text,
  `intermediate_action` text,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `incident_request_status` enum('new','approved','planned','in_progress','completed','cancelled','draft') NOT NULL DEFAULT 'new',
  `deleted` int(1) NOT NULL DEFAULT '0',
  `added_by` int(11) NOT NULL,
  `t_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `t_updated` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `incident_requests`
--

INSERT INTO `incident_requests` (`incident_request_id`, `incident_datetime`, `incident_type_id`, `vessel_visit_id`, `location_id`, `location_details`, `risk_rating`, `weather`, `asset_person`, `event_before`, `event_during`, `event_after`, `initial_finding`, `intermediate_action`, `active`, `incident_request_status`, `deleted`, `added_by`, `t_added`, `t_updated`) VALUES
(1, '2021-12-25 12:49:00', 5, 0, 10, 'Test data', 8, 'rainy', 'both', 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA', 'tAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA', 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA', 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA', 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA', 1, 'approved', 0, 2, '2021-12-26 01:52:32', '2021-12-27 08:25:05'),
(2, '2021-12-26 10:19:00', 4, 0, 1, 'In front of Car Park', 5, '', 'both', '', '', '', '', '', 1, 'approved', 0, 2, '2021-12-27 02:23:07', '2021-12-27 02:24:36'),
(3, '2021-12-27 01:00:00', 2, 0, 10, 'Crane 22 and Terminal Tractor 17', 6, 'Sunny', 'both', 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA', 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA', 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA', 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA', 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA', 1, 'approved', 0, 2, '2021-12-27 08:23:25', '2021-12-27 08:24:32'),
(4, '2022-01-06 14:25:00', 2, 0, 10, 'Crane Breakdown', 6, 'sunny', 'both', 'test', 'test', 'test', 'test', 'test', 1, 'approved', 0, 50, '2022-01-06 06:55:19', '2022-01-06 06:56:11'),
(5, '2022-01-06 15:00:00', 2, 0, 10, 'Slipped', 5, 'Rainy', 'person', 'test', 'test', 'test', 'test', 'test', 1, 'approved', 0, 50, '2022-01-06 07:23:18', '2022-01-11 02:40:42');

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
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `incident_requests_attachments`
--

INSERT INTO `incident_requests_attachments` (`incident_request_attachment_id`, `incident_request_id`, `filename`, `file_order`, `deleted`, `timestamp`) VALUES
(1, 1, '1640483695-cargo.jpg', 0, 1, '2021-12-26 01:54:55'),
(2, 2, '1640571866-Screenshot 2021-12-27 at 10.24.11 AM.png', 0, 0, '2021-12-27 02:24:26'),
(3, 3, '1640593583-Screenshot 2021-12-27 at 4.26.10 PM.png', 0, 0, '2021-12-27 08:26:23'),
(4, 1, '1641383721-WhatsApp Image 2021-12-28 at 10.26.27 AM.jpeg', 0, 0, '2022-01-05 11:55:21');

-- --------------------------------------------------------

--
-- Table structure for table `incident_requests_remarks`
--

CREATE TABLE `incident_requests_remarks` (
  `incident_request_remarks_id` int(11) NOT NULL,
  `incident_request_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `remark` text NOT NULL,
  `t_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `t_updated` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `incident_requests_remarks`
--

INSERT INTO `incident_requests_remarks` (`incident_request_remarks_id`, `incident_request_id`, `user_id`, `remark`, `t_added`, `t_updated`) VALUES
(1, 1, 2, 'test dummy', '2021-12-26 01:52:32', NULL),
(2, 2, 2, '', '2021-12-27 02:23:07', NULL),
(3, 2, 2, '', '2021-12-27 02:24:36', NULL),
(4, 3, 2, '', '2021-12-27 08:23:25', NULL),
(5, 3, 2, '', '2021-12-27 08:23:42', NULL),
(6, 1, 2, '', '2021-12-27 08:24:41', NULL),
(7, 4, 50, 'All under control', '2022-01-06 06:55:19', NULL),
(8, 4, 51, '', '2022-01-06 06:56:11', NULL),
(9, 5, 50, 'All under control', '2022-01-06 07:23:18', NULL),
(10, 5, 50, '', '2022-01-11 02:40:42', NULL);

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
  `deleted` int(1) NOT NULL DEFAULT '0',
  `added_by` int(11) NOT NULL,
  `t_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `t_updated` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `incident_request_asset_details`
--

INSERT INTO `incident_request_asset_details` (`incident_request_asset_details_id`, `incident_request_id`, `asset_type_id`, `damage_part`, `type_of_damage`, `technical_status`, `owner`, `deleted`, `added_by`, `t_added`, `t_updated`) VALUES
(1, 1, 9, 'test1', 'test1', 'test1', 'test1', 0, 2, '2021-12-26 01:52:32', NULL),
(2, 1, 3, 'test2', 'test2', 'test2', 'test2', 0, 2, '2021-12-26 01:52:32', NULL),
(3, 2, 4, 'Lights', 'Lights Damaged', 'Still In Use', 'PMB', 0, 2, '2021-12-27 02:23:07', NULL),
(4, 2, 0, 'Lights', 'Lights Damaged', 'Still In Use', 'PMB', 0, 2, '2021-12-27 02:23:07', NULL),
(5, 3, 6, 'FRONT', 'ENGINE AND BODY', 'NOT IN USE', 'KP', 0, 2, '2021-12-27 08:23:25', NULL),
(6, 3, 1, 'FRONT ', 'BROKEN WINDSCREEN', 'IN USE', 'KP', 0, 2, '2021-12-27 08:23:25', NULL),
(7, 4, 2, 'test', 'test', 'test', 'test', 0, 50, '2022-01-06 06:55:19', NULL);

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
  `type_of_injury` text,
  `cause` text,
  `object_cause_injury` text,
  `deleted` int(1) NOT NULL DEFAULT '0',
  `added_by` int(11) NOT NULL,
  `t_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `t_updated` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `incident_request_person_details`
--

INSERT INTO `incident_request_person_details` (`incident_request_person_details_id`, `incident_request_id`, `ic_passport`, `name`, `age`, `company_id`, `postion_id`, `injured`, `injured_part`, `type_of_injury`, `cause`, `object_cause_injury`, `deleted`, `added_by`, `t_added`, `t_updated`) VALUES
(1, 1, 'test1', 'test1', 33, 1, 11, 'Yes', 'test1', 'test1', 'test1', 'test1', 0, 2, '2021-12-26 01:52:32', NULL),
(2, 1, 'test2', 'test2', 31, 1, 4, 'Yes', 'test2', 'test2', 'test2', 'test2', 0, 2, '2021-12-26 01:52:32', NULL),
(3, 2, '771027106038', 'james', 37, 1, 13, 'Yes', 'Toe', 'Broken Toe', 'Accident', 'Impact', 0, 2, '2021-12-27 02:23:07', NULL),
(4, 3, '671027106011', 'MAHRIM', 23, 1, 1, 'Yes', 'TOE', 'BROKEN TOE', 'ACCIDENT', 'METAL', 0, 2, '2021-12-27 08:23:25', NULL),
(5, 3, '681019104711', 'JOHAN', 19, 1, 1, 'Yes', 'TEAR AND CUT', 'FINGER', 'ACCIDENT', 'GLASS', 0, 2, '2021-12-27 08:23:25', NULL),
(6, 4, '001232109888', 'ray', 26, 1, 11, 'Yes', 'ankle', 'ankle sprained', 'fell down', 'No', 0, 50, '2022-01-06 06:55:19', NULL),
(7, 5, '908877109890', 'Luis', 32, 1, 12, 'Yes', 'Hand', 'Broken Hand', 'slipped', 'No', 0, 50, '2022-01-06 07:23:18', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `incident_types`
--

CREATE TABLE `incident_types` (
  `incident_type_id` int(11) NOT NULL,
  `incident_type` varchar(300) NOT NULL,
  `Description` varchar(300) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `incident_types`
--

INSERT INTO `incident_types` (`incident_type_id`, `incident_type`, `Description`, `active`) VALUES
(1, 'Safety', 'Test', 1),
(2, 'Vehicle Accident', '', 1),
(3, 'Asset Damage', '', 1),
(4, 'Theft', '', 1),
(5, 'Sabotage', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `invoice_id` int(10) NOT NULL,
  `vessel_visit_id` int(10) DEFAULT NULL,
  `company_id` int(10) DEFAULT NULL,
  `filename` text,
  `invoice_number` varchar(30) DEFAULT NULL,
  `value` decimal(10,2) DEFAULT NULL,
  `t_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `log_id` int(15) UNSIGNED NOT NULL,
  `log_user_id` int(10) UNSIGNED NOT NULL,
  `log_item_table` enum('cargo_types','cargo_packagings','charges','commodities','companies','service_requests','countries','country_template','designations','merchants','merchant_addresses','merchant_branches','permissions','permission_categories','ports','quotations','quotation_remarks','roles','role_permissions','tasks','templates','timezones','users','user_branch','user_groups','user_role','vessels','vessel_visits','depots','bills_of_lading','container_release_orders','notices_of_arrival','debit_notes','gears','equipments','consumables','equipment_groups','gear_groups','resource_groups','workers') NOT NULL,
  `log_item_id` int(15) NOT NULL,
  `log_code` varchar(50) NOT NULL,
  `log_description` mediumtext NOT NULL,
  `log_ip` varchar(15) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`log_id`, `log_user_id`, `log_item_table`, `log_item_id`, `log_code`, `log_description`, `log_ip`, `timestamp`) VALUES
(1, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '::1', '2022-08-02 19:53:05'),
(2, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '::1', '2022-08-02 19:53:10'),
(3, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '::1', '2022-08-02 19:53:10'),
(4, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '::1', '2022-08-02 21:05:06'),
(5, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '::1', '2022-08-02 21:05:15'),
(6, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '::1', '2022-08-02 21:05:15'),
(7, 2, 'gears', 1, 'GEAR_ADDED', '{\"gear_units\":\"1\",\"purchase_date\":\"02\\/08\\/2022\",\"names\":\"32\",\"notes\":\"ss\"}', '::1', '2022-08-02 21:15:57'),
(8, 2, 'equipments', 1, 'WORKER_ADDED', '{\"name\":\"ggff\",\"code\":\"454545\",\"purchase_date\":\"02\\/08\\/2022\",\"equipment_status\":\"\",\"current_mileage\":\"\",\"service_every_mileage\":\"\",\"next_service_mileage\":\"\",\"last_service_date\":\"\",\"service_interval_weeks\":\"\",\"next_service_date\":\"\",\"notes\":\"\",\"safe_load\":\"\"}', '::1', '2022-08-02 21:43:56'),
(9, 2, 'equipments', 1, 'ITEM_DISABLED', '', '::1', '2022-08-02 22:13:02'),
(10, 2, 'users', 2, 'LOGOUT', 'Logged out manually.', '::1', '2022-08-02 22:14:41'),
(11, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '::1', '2022-08-02 22:14:50'),
(12, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '::1', '2022-08-02 22:14:50'),
(13, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '::1', '2022-08-02 22:17:05'),
(14, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '::1', '2022-08-02 22:17:13'),
(15, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '::1', '2022-08-02 22:17:13'),
(16, 2, 'permissions', 261, 'PERMISSION_CREATED', '{\"name\":\"add_qrcode\",\"category\":\"1\"}', '::1', '2022-08-02 22:19:05'),
(17, 2, 'permissions', 262, 'PERMISSION_CREATED', '{\"name\":\"qr_generator\",\"category\":\"1\"}', '::1', '2022-08-02 22:20:11'),
(18, 2, 'permissions', 263, 'PERMISSION_CREATED', '{\"name\":\"add_history\",\"category\":\"1\"}', '::1', '2022-08-02 22:20:59'),
(19, 2, 'users', 2, 'PERMISSION_OVERRIDE_UPDATED', '{\"permissions\":[\"262\",\"263\"],\"id\":\"2\"}', '::1', '2022-08-02 22:22:22'),
(20, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '::1', '2022-08-03 05:25:11'),
(21, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '::1', '2022-08-03 05:25:11'),
(22, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '::1', '2022-08-03 06:06:11'),
(23, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '::1', '2022-08-03 06:06:11'),
(24, 2, 'users', 2, 'LOGOUT', 'Logged out manually.', '::1', '2022-08-03 16:32:43'),
(25, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '::1', '2022-08-03 16:32:53'),
(26, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '::1', '2022-08-03 16:32:54'),
(27, 2, 'workers', 1, 'WORKER_ADDED', '{\"name\":\"fdfdfdd\",\"type\":\"casual-daily\",\"bank_account\":\"5454\",\"ic_number\":\"54545\",\"contact_number\":\"45\",\"id_lcm\":\"5454\",\"id_samalaju\":\"4545\",\"payment_effective\":\"03\\/09\\/2022\",\"shift_1\":\"07:30 - 16:30\",\"shift_2\":\"\",\"notes\":\"efdfd\"}', '::1', '2022-08-03 16:33:18'),
(28, 2, 'workers', 1, 'PHOTO_UPLOADED', 'A new photo was uploaded.', '::1', '2022-08-03 16:34:34'),
(29, 2, 'equipments', 2, 'WORKER_ADDED', '{\"name\":\"3ffsdf\",\"code\":\"54545\",\"purchase_date\":\"02\\/08\\/2022\",\"equipment_status\":\"In use\",\"equipment_type\":\"1\",\"current_mileage\":\"554\",\"service_every_mileage\":\"\",\"next_service_mileage\":\"\",\"last_service_date\":\"\",\"service_interval_weeks\":\"\",\"next_service_date\":\"\",\"notes\":\"fgfgfg\",\"safe_load\":\"\"}', '::1', '2022-08-03 16:37:01'),
(30, 2, 'permissions', 264, 'PERMISSION_CREATED', '{\"name\":\"list_drivers\",\"category\":\"1\"}', '::1', '2022-08-03 18:29:32'),
(31, 2, 'users', 2, 'PERMISSION_OVERRIDE_UPDATED', '{\"permissions\":[\"262\",\"263\",\"264\"],\"id\":\"2\"}', '::1', '2022-08-03 18:30:21'),
(32, 2, 'permissions', 265, 'PERMISSION_CREATED', '{\"name\":\"list_truck\",\"category\":\"1\"}', '::1', '2022-08-03 18:34:53'),
(33, 2, 'permissions', 266, 'PERMISSION_CREATED', '{\"name\":\"list_asset\",\"category\":\"1\"}', '::1', '2022-08-03 18:35:07'),
(34, 2, 'permissions', 267, 'PERMISSION_CREATED', '{\"name\":\"list_truck_groups\",\"category\":\"1\"}', '::1', '2022-08-03 18:37:24'),
(35, 2, 'permissions', 268, 'PERMISSION_CREATED', '{\"name\":\"truck_groups\",\"category\":\"1\"}', '::1', '2022-08-03 18:38:02'),
(36, 2, 'users', 2, 'PERMISSION_OVERRIDE_UPDATED', '{\"permissions\":[\"262\",\"263\",\"264\",\"265\",\"266\",\"267\",\"268\"],\"id\":\"2\"}', '::1', '2022-08-03 18:38:51'),
(37, 2, 'equipments', 1, 'ITEM_ACTIVE', '', '::1', '2022-08-03 18:45:04'),
(38, 2, 'equipments', 1, 'ITEM_DISABLED', '', '::1', '2022-08-03 18:45:05'),
(39, 2, 'workers', 1, 'GROUPS_UPDATED', '{\"id\":\"1\"}', '::1', '2022-08-03 18:54:10'),
(40, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '::1', '2022-08-04 11:05:14'),
(41, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '::1', '2022-08-04 11:05:14'),
(42, 2, 'workers', 2, 'DRIVER_ADDED', '{\"name\":\"abc\",\"type\":\"contract-daily\",\"bank_account\":\"1234567890\",\"ic_number\":\"0987654321\",\"contact_number\":\"567891234\",\"age\":\"12\",\"ext_work_hours\":\"14\",\"ext_address\":\"51214\"}', '::1', '2022-08-04 13:36:22'),
(43, 2, 'workers', 2, 'ITEM_DISABLED', '', '::1', '2022-08-04 13:46:16'),
(44, 2, 'workers', 2, 'ITEM_ACTIVE', '', '::1', '2022-08-04 13:46:18'),
(45, 2, 'workers', 1, 'GROUPS_UPDATED', '{\"groups\":[\"2\"],\"id\":\"1\"}', '::1', '2022-08-04 14:53:08'),
(46, 2, 'workers', 1, 'GROUPS_UPDATED', '{\"groups\":[\"2\"],\"id\":\"1\"}', '::1', '2022-08-04 14:53:08'),
(47, 2, 'workers', 1, 'GROUPS_UPDATED', '{\"id\":\"1\"}', '::1', '2022-08-04 14:53:12'),
(48, 2, 'workers', 1, 'GROUPS_UPDATED', '{\"groups\":[\"2\"],\"id\":\"1\"}', '::1', '2022-08-04 14:53:27'),
(49, 2, 'workers', 1, 'GROUPS_UPDATED', '{\"id\":\"1\"}', '::1', '2022-08-04 14:53:31'),
(50, 2, 'workers', 1, 'GROUPS_UPDATED', '{\"groups\":[\"2\"],\"id\":\"1\"}', '::1', '2022-08-04 14:53:47'),
(51, 2, 'workers', 1, 'GROUPS_UPDATED', '{\"id\":\"1\"}', '::1', '2022-08-04 14:53:51'),
(52, 2, 'workers', 1, 'WORKER_UPDATED', '{\"name\":\"fdfdfdd\",\"type\":\"casual-daily\",\"contact_number\":\"45\",\"standby_rate\":\"\",\"max_overtime_hours\":\"\",\"bank_account\":\"5454\",\"ic_number\":\"54545\",\"monthly_allowance\":\"\",\"id_lcm\":\"5454\",\"id_samalaju\":\"454511\",\"notes\":\"efdfd\",\"leave_override\":\"0\",\"medical_leave_override\":\"0\",\"payment_effective\":\"2022-08-04\",\"basic_pay\":\"\",\"standby_pay\":\"\",\"id\":\"1\"}', '::1', '2022-08-04 14:55:45'),
(53, 2, 'workers', 1, 'WORKER_UPDATED', '{\"name\":\"fdfdfdd\",\"type\":\"casual-daily\",\"contact_number\":\"45\",\"age\":\"\",\"ext_work_hours\":\"\",\"bank_account\":\"5454\",\"ic_number\":\"54545\",\"ext_address\":\"gfdgfd\",\"id\":\"1\"}', '::1', '2022-08-04 15:02:29'),
(54, 2, 'workers', 1, 'WORKER_UPDATED', '{\"name\":\"fdfdfdd\",\"type\":\"casual-daily\",\"contact_number\":\"45\",\"age\":\"\",\"ext_work_hours\":\"\",\"bank_account\":\"5454fdf\",\"ic_number\":\"54545\",\"ext_address\":\"fdfdfdfd\",\"id\":\"1\"}', '::1', '2022-08-04 15:05:44'),
(55, 2, 'workers', 1, 'DRIVER_UPDATED', '{\"name\":\"fdfdfdd1\",\"type\":\"contract-daily\",\"contact_number\":\"452\",\"age\":\"3\",\"ext_work_hours\":\"4\",\"bank_account\":\"5\",\"ic_number\":\"6\",\"ext_address\":\"7\",\"id\":\"1\"}', '::1', '2022-08-04 15:07:21'),
(56, 2, 'workers', 2, 'ITEM_DISABLED', '', '::1', '2022-08-04 15:19:48'),
(57, 2, 'workers', 2, 'ITEM_ACTIVE', '', '::1', '2022-08-04 15:19:49'),
(58, 2, 'workers', 1, 'ITEM_DISABLED', '', '::1', '2022-08-04 15:19:50'),
(59, 2, 'workers', 1, 'ITEM_ACTIVE', '', '::1', '2022-08-04 15:19:50'),
(60, 2, '', 1, 'WORKER_GROUP_ADDED', '{\"name\":\"bgfggf\",\"code\":\"FGFGF\",\"notes\":\"grgrg\"}', '::1', '2022-08-04 17:44:03'),
(61, 2, 'equipments', 1, 'ITEM_ACTIVE', '', '::1', '2022-08-04 18:25:52'),
(62, 2, 'equipments', 1, 'ITEM_DISABLED', '', '::1', '2022-08-04 18:25:53'),
(63, 2, 'workers', 1, 'ITEM_DISABLED', '', '::1', '2022-08-04 18:26:04'),
(64, 2, 'workers', 1, 'ITEM_ACTIVE', '', '::1', '2022-08-04 18:26:05'),
(65, 2, 'equipments', 2, 'ITEM_DISABLED', '', '::1', '2022-08-04 18:28:25'),
(66, 2, 'equipments', 2, 'ITEM_ACTIVE', '', '::1', '2022-08-04 18:28:31'),
(67, 2, 'equipments', 3, 'WORKER_ADDED', '{\"name\":\"rfdgdfg\",\"code\":\"334\",\"purchase_date\":\"01\\/08\\/2022\",\"equipment_status\":\"In use\",\"equipment_manufacturer\":\"\",\"equipment_type\":\"3\",\"notes\":\"dgdfgfdgfd\",\"safe_load\":\"gfdgdfg\"}', '::1', '2022-08-04 18:44:47'),
(68, 2, 'equipments', 3, 'ITEM_DISABLED', '', '::1', '2022-08-04 18:44:56'),
(69, 2, 'equipments', 4, 'WORKER_ADDED', '{\"name\":\"rtyreye\",\"code\":\"YERYERY\",\"purchase_date\":\"01\\/08\\/2022\",\"equipment_status\":\"In use\",\"equipment_type\":\"4\",\"notes\":\"uuku\",\"safe_load\":\"kuku\"}', '::1', '2022-08-04 18:47:28'),
(70, 2, 'equipments', 2, 'ITEM_DISABLED', '', '::1', '2022-08-04 18:48:28'),
(71, 2, 'equipments', 2, 'ITEM_ACTIVE', '', '::1', '2022-08-04 18:48:29'),
(72, 2, 'equipments', 1, 'SCHEDULED_MAINTENANCE_ADDED', '{\"next_maintenance_date\":\"04\\/08\\/2022\",\"next_maintenance_mileage\":\"44\",\"id\":\"1\"}', '::1', '2022-08-04 18:50:12'),
(73, 2, 'equipments', 1, 'EQUIPMENT_UPDATED', '{\"name\":\"ggff1\",\"code\":\"4545452\",\"purchase_date\":\"03\\/08\\/2022\",\"equipment_status\":\"In use\",\"equipment_type\":\"2\",\"notes\":\"5\",\"safe_load\":\"6\",\"id\":\"1\"}', '::1', '2022-08-04 18:59:37'),
(74, 2, 'equipments', 1, 'GROUPS_UPDATED', '{\"groups\":[\"2\",\"3\",\"5\"],\"id\":\"1\"}', '::1', '2022-08-04 19:00:39'),
(75, 2, 'equipments', 1, 'GROUPS_UPDATED', '{\"groups\":[\"2\"],\"id\":\"1\"}', '::1', '2022-08-04 19:00:45'),
(76, 2, 'equipment_groups', 6, 'TRUCK_GROUP_ADDED', '{\"name\":\"gfg\",\"code\":\"FGFG\",\"notes\":\"fgfgf\"}', '::1', '2022-08-04 19:05:39'),
(77, 2, 'equipment_groups', 6, 'TRUCK_GROUP_UPDATED', '{\"name\":\"gfg1\",\"code\":\"FGFG2\",\"notes\":\"fgfgf3\",\"id\":\"6\"}', '::1', '2022-08-04 19:06:33'),
(78, 2, 'consumables', 1, 'CONSUMABLE_ADDED', '{\"name\":\"dfd\",\"opening_stock\":\"3\",\"replenishment_level\":\"45\",\"notes\":\"vcbvcb\"}', '::1', '2022-08-04 19:07:15'),
(79, 2, 'permissions', 269, 'PERMISSION_CREATED', '{\"name\":\"list_assets\",\"category\":\"1\"}', '::1', '2022-08-04 19:10:10'),
(80, 2, 'users', 2, 'PERMISSION_OVERRIDE_UPDATED', '{\"permissions\":[\"262\",\"263\",\"264\",\"265\",\"266\",\"267\",\"268\",\"269\"],\"id\":\"2\"}', '::1', '2022-08-04 19:10:24'),
(81, 2, 'permissions', 270, 'PERMISSION_CREATED', '{\"name\":\"list_asset_groups\",\"category\":\"1\"}', '::1', '2022-08-04 19:11:48'),
(82, 2, 'users', 2, 'PERMISSION_OVERRIDE_UPDATED', '{\"permissions\":[\"262\",\"263\",\"264\",\"265\",\"266\",\"267\",\"268\",\"269\",\"270\"],\"id\":\"2\"}', '::1', '2022-08-04 19:12:11'),
(83, 2, 'equipments', 4, 'ITEM_DISABLED', '', '::1', '2022-08-04 19:15:06'),
(84, 2, 'equipments', 4, 'ITEM_ACTIVE', '', '::1', '2022-08-04 19:15:08'),
(85, 2, 'equipments', 1, 'EQUIPMENT_UPDATED', '{\"name\":\"ggff1\",\"code\":\"4545452\",\"purchase_date\":\"03\\/08\\/2022\",\"equipment_status\":\"Maintenance\",\"equipment_type\":\"3\",\"notes\":\"5\",\"safe_load\":\"6\",\"id\":\"1\"}', '::1', '2022-08-04 19:34:25'),
(86, 2, 'equipments', 1, 'EQUIPMENT_UPDATED', '{\"name\":\"ggff1\",\"code\":\"4545452\",\"purchase_date\":\"03\\/08\\/2022\",\"equipment_status\":\"Maintenance\",\"equipment_type\":\"3\",\"notes\":\"5\",\"safe_load\":\"6\",\"id\":\"1\"}', '::1', '2022-08-04 19:35:57'),
(87, 2, 'equipments', 1, 'EQUIPMENT_UPDATED', '{\"name\":\"ggff1\",\"code\":\"4545452\",\"purchase_date\":\"03\\/08\\/2022\",\"equipment_status\":\"Maintenance\",\"equipment_type\":\"3\",\"notes\":\"51\",\"safe_load\":\"6\",\"id\":\"1\"}', '::1', '2022-08-04 19:36:09'),
(88, 2, 'equipments', 1, 'ASSET_UPDATED', '{\"name\":\"ggff1\",\"code\":\"4545452\",\"purchase_date\":\"03\\/08\\/2022\",\"equipment_status\":\"Maintenance\",\"equipment_type\":\"3\",\"notes\":\"51\",\"safe_load\":\"6\",\"id\":\"1\"}', '::1', '2022-08-04 19:36:55'),
(89, 2, 'equipments', 1, 'ASSET_UPDATED', '{\"name\":\"ggff1\",\"code\":\"4545452\",\"purchase_date\":\"03\\/08\\/2022\",\"equipment_status\":\"Maintenance\",\"equipment_type\":\"3\",\"notes\":\"51\",\"safe_load\":\"6\",\"id\":\"1\"}', '::1', '2022-08-04 19:37:50'),
(90, 2, 'equipments', 1, 'ASSET_UPDATED', '{\"name\":\"ggff1\",\"code\":\"4545452\",\"purchase_date\":\"03\\/08\\/2022\",\"equipment_status\":\"Maintenance\",\"equipment_type\":\"3\",\"notes\":\"51\",\"safe_load\":\"6\",\"id\":\"1\"}', '::1', '2022-08-04 19:39:11'),
(91, 2, 'equipments', 4, 'ASSET_UPDATED', '{\"name\":\"rtyreye\",\"code\":\"YERYERY\",\"purchase_date\":\"01\\/08\\/2022\",\"equipment_status\":\"In use\",\"equipment_type\":\"4\",\"notes\":\"uuku1\",\"safe_load\":\"kuku1\",\"id\":\"4\"}', '::1', '2022-08-04 19:39:42'),
(92, 2, 'equipments', 5, 'TRUCK_ADDED', '{\"name\":\"dfs\",\"code\":\"FDFSDF\",\"purchase_date\":\"01\\/08\\/2022\",\"equipment_status\":\"In use\",\"equipment_type\":\"9\",\"notes\":\"dfdfds\",\"safe_load\":\"fdfdsf\"}', '::1', '2022-08-04 19:39:57'),
(93, 2, 'equipments', 6, 'ASSET_ADDED', '{\"name\":\"ert\",\"code\":\"RTRT\",\"purchase_date\":\"01\\/08\\/2022\",\"equipment_status\":\"Maintenance\",\"equipment_type\":\"5\",\"notes\":\"rtret\",\"safe_load\":\"rtretret\"}', '::1', '2022-08-04 19:41:22'),
(94, 2, 'equipments', 6, 'GROUPS_UPDATED', '{\"groups\":[\"2\",\"5\"],\"id\":\"6\"}', '::1', '2022-08-04 19:41:37'),
(95, 2, 'equipments', 6, 'ASSET_UPDATED', '{\"name\":\"ert\",\"code\":\"RTRT\",\"purchase_date\":\"01\\/08\\/2022\",\"equipment_status\":\"Maintenance\",\"equipment_type\":\"5\",\"notes\":\"rtret1\",\"safe_load\":\"rtretret2\",\"id\":\"6\"}', '::1', '2022-08-04 19:41:45'),
(96, 2, 'equipment_groups', 5, 'ASSET_GROUP_UPDATED', '{\"name\":\"GENERAL1\",\"code\":\"GENERAL2\",\"notes\":\"3\",\"id\":\"5\"}', '::1', '2022-08-04 19:55:00'),
(97, 2, 'gears', 1, 'ITEM_DISABLED', '', '::1', '2022-08-04 20:38:39'),
(98, 2, 'gears', 1, 'ITEM_ACTIVE', '', '::1', '2022-08-04 20:38:41'),
(99, 2, 'gears', 1, 'ITEM_DISABLED', '', '::1', '2022-08-04 20:38:44'),
(100, 2, '', 1, 'GEAR_TYPE_CREATED', '{\"name\":\"aa\",\"short_code\":\"3A\",\"description\":\"aaaa\",\"colour\":\"\"}', '::1', '2022-08-04 20:44:08'),
(101, 2, '', 1, 'ITEM_DISABLED', '', '::1', '2022-08-04 20:44:13'),
(102, 2, '', 1, 'ITEM_ACTIVE', '', '::1', '2022-08-04 20:44:15'),
(103, 2, 'gears', 1, 'GEAR_UPDATED', '{\"name\":\"32\",\"gear_type\":\"1\",\"purchase_date\":\"02\\/08\\/2022\",\"damaged\":\"0\",\"notes\":\"ss\",\"id\":\"1\"}', '::1', '2022-08-04 20:44:34'),
(104, 2, 'gears', 1, 'ITEM_DISABLED', '', '::1', '2022-08-04 20:44:37'),
(105, 2, 'gears', 1, 'ITEM_ACTIVE', '', '::1', '2022-08-04 20:44:39'),
(106, 2, '', 8, 'ITEM_DISABLED', '', '::1', '2022-08-04 20:53:59'),
(107, 2, '', 8, 'ITEM_ACTIVE', '', '::1', '2022-08-04 20:54:02'),
(108, 2, '', 8, 'ITEM_DISABLED', '', '::1', '2022-08-04 20:58:23'),
(109, 2, '', 8, 'ITEM_ACTIVE', '', '::1', '2022-08-04 20:58:25'),
(110, 2, '', 1, 'ITEM_DISABLED', '', '::1', '2022-08-04 20:59:23'),
(111, 2, '', 1, 'ITEM_ACTIVE', '', '::1', '2022-08-04 20:59:25'),
(112, 2, '', 1, 'MANUFACTURER_CREATED', '{\"name\":\"d\",\"description\":\"dfd\"}', '::1', '2022-08-04 20:59:34'),
(113, 2, '', 1, 'OPERATION_TYPE_CREATED', '{\"name\":\"fddsgg\",\"description\":\"gdg\"}', '::1', '2022-08-04 21:01:03'),
(114, 2, '', 1, 'ITEM_DISABLED', '', '::1', '2022-08-04 21:02:26'),
(115, 2, '', 1, 'ITEM_ACTIVE', '', '::1', '2022-08-04 21:02:27'),
(116, 2, '', 2, 'OPERATION_TYPE_CREATED', '{\"name\":\"ss\",\"description\":\"ss\"}', '::1', '2022-08-04 21:03:11'),
(117, 2, '', 2, 'ITEM_DISABLED', '', '::1', '2022-08-04 21:03:14'),
(118, 2, '', 2, 'ITEM_ACTIVE', '', '::1', '2022-08-04 21:03:16'),
(119, 2, '', 1, 'RESOURCE_TYPE_CREATED', '{\"name\":\"ff\",\"short_code\":\"5F\",\"description\":\"f\",\"colour\":\"#386fbc\",\"shift_1_start\":\"07:00\",\"shift_1_end\":\"1\",\"shift_2_start\":\"19:00\",\"shift_2_end\":\"1\"}', '::1', '2022-08-04 21:03:42'),
(120, 2, '', 1, 'ITEM_DISABLED', '', '::1', '2022-08-04 21:05:17'),
(121, 2, '', 1, 'ITEM_ACTIVE', '', '::1', '2022-08-04 21:05:18'),
(122, 2, '', 1, 'ITEM_DISABLED', '', '::1', '2022-08-04 21:05:28'),
(123, 2, '', 1, 'ITEM_ACTIVE', '', '::1', '2022-08-04 21:05:29'),
(124, 2, 'user_groups', 1, 'ITEM_DISABLED', '', '::1', '2022-08-04 21:06:43'),
(125, 2, 'user_groups', 1, 'ITEM_ACTIVE', '', '::1', '2022-08-04 21:06:45'),
(126, 2, 'user_groups', 7, 'USER_GROUP_CREATED', '{\"name\":\"f\",\"description\":\"ff\"}', '::1', '2022-08-04 21:06:49'),
(127, 2, 'user_groups', 8, 'USER_GROUP_CREATED', '{\"name\":\"fdfd\",\"description\":\"dfd\"}', '::1', '2022-08-04 21:08:56'),
(128, 2, 'user_groups', 7, 'USER_GROUP_UPDATED', '{\"name\":\"fa\",\"description\":\"ff\",\"id\":\"7\"}', '::1', '2022-08-04 21:09:05'),
(129, 2, 'users', 2, 'ITEM_DISABLED', '', '::1', '2022-08-04 21:09:23'),
(130, 2, 'users', 2, 'LOGOUT', 'Logged out manually.', '::1', '2022-08-04 21:09:24'),
(131, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '::1', '2022-08-04 21:11:23'),
(132, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '::1', '2022-08-04 21:11:23'),
(133, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '::1', '2022-08-04 21:15:24'),
(134, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '::1', '2022-08-04 21:15:24'),
(135, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '::1', '2022-08-04 21:15:52'),
(136, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '::1', '2022-08-04 21:15:52'),
(137, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '::1', '2022-08-04 21:15:57'),
(138, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '::1', '2022-08-04 21:15:57'),
(139, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '::1', '2022-08-04 21:16:21'),
(140, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '::1', '2022-08-04 21:16:21'),
(141, 2, 'users', 2, 'LOGOUT', 'Logged out manually.', '::1', '2022-08-04 21:17:08'),
(142, 2, 'users', 2, 'LOGOUT', 'Logged out manually.', '::1', '2022-08-04 21:19:33'),
(143, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '::1', '2022-08-04 21:20:21'),
(144, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '::1', '2022-08-04 21:20:22'),
(145, 2, 'designations', 1, 'DESIGNATION_CREATED', '{\"name\":\"caa\",\"description\":\"aaa\"}', '::1', '2022-08-04 21:21:16'),
(146, 2, 'designations', 1, 'ITEM_DISABLED', '', '::1', '2022-08-04 21:21:28'),
(147, 2, 'designations', 1, 'ITEM_ACTIVE', '', '::1', '2022-08-04 21:21:30'),
(148, 2, '', 1, 'MASTERS_COMPANIES_CREATED', '{\"registration_id\":\"1\",\"company_name\":\"aa\",\"contact_person\":\"aaa\",\"contact_email\":\"aa@aa.bb\",\"business_type\":\"abc\"}', '::1', '2022-08-04 21:26:15'),
(149, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '111.88.121.82', '2022-08-04 21:38:34'),
(150, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '111.88.121.82', '2022-08-04 21:38:34'),
(151, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '113.211.209.108', '2022-08-04 22:06:19'),
(152, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '113.211.209.108', '2022-08-04 22:06:19'),
(153, 2, 'equipment_groups', 5, 'ITEM_DISABLED', '', '113.211.209.108', '2022-08-04 22:14:51'),
(154, 2, 'equipment_groups', 6, 'ITEM_DISABLED', '', '113.211.209.108', '2022-08-04 22:14:52'),
(155, 2, 'equipment_groups', 2, 'ITEM_DISABLED', '', '113.211.209.108', '2022-08-04 22:14:53'),
(156, 2, 'equipment_groups', 2, 'ITEM_ACTIVE', '', '113.211.209.108', '2022-08-04 22:14:54'),
(157, 2, 'equipment_groups', 6, 'ITEM_ACTIVE', '', '113.211.209.108', '2022-08-04 22:14:54'),
(158, 2, 'equipment_groups', 5, 'ITEM_ACTIVE', '', '113.211.209.108', '2022-08-04 22:14:55'),
(159, 2, 'equipment_groups', 1, 'ITEM_ACTIVE', '', '113.211.209.108', '2022-08-04 22:14:57'),
(160, 2, 'equipment_groups', 5, 'TRUCK_GROUP_UPDATED', '{\"name\":\"Waste Management\",\"code\":\"WM\",\"notes\":\"\",\"id\":\"5\"}', '113.211.209.108', '2022-08-04 22:15:41'),
(161, 2, 'equipment_groups', 6, 'TRUCK_GROUP_UPDATED', '{\"name\":\"Road Cleaning\",\"code\":\"RC\",\"notes\":\"\",\"id\":\"6\"}', '113.211.209.108', '2022-08-04 22:16:04'),
(162, 2, 'equipment_groups', 3, 'TRUCK_GROUP_UPDATED', '{\"name\":\"Recycle Waste\",\"code\":\"RW\",\"notes\":\"\",\"id\":\"3\"}', '113.211.209.108', '2022-08-04 22:17:18'),
(163, 2, 'equipment_groups', 4, 'TRUCK_GROUP_UPDATED', '{\"name\":\"Motorcycle\",\"code\":\"MC\",\"notes\":\"\",\"id\":\"4\"}', '113.211.209.108', '2022-08-04 22:17:37'),
(164, 2, 'equipment_groups', 2, 'TRUCK_GROUP_UPDATED', '{\"name\":\"Grass Cutting\",\"code\":\"GC\",\"notes\":\"\",\"id\":\"2\"}', '113.211.209.108', '2022-08-04 22:19:15'),
(165, 2, 'equipment_groups', 6, 'TRUCK_GROUP_UPDATED', '{\"name\":\"General Cleaning\",\"code\":\"GC\",\"notes\":\"\",\"id\":\"6\"}', '113.211.209.108', '2022-08-04 22:19:37'),
(166, 2, 'equipments', 4, 'GROUPS_UPDATED', '{\"groups\":[\"5\"],\"id\":\"4\"}', '113.211.209.108', '2022-08-04 22:20:45'),
(167, 2, 'user_groups', 8, 'USER_GROUP_UPDATED', '{\"name\":\"Driver\",\"description\":\"\",\"id\":\"8\"}', '113.211.209.108', '2022-08-04 22:25:42'),
(168, 2, 'user_groups', 7, 'USER_GROUP_UPDATED', '{\"name\":\"Engineering\",\"description\":\"\",\"id\":\"7\"}', '113.211.209.108', '2022-08-04 22:26:03'),
(169, 2, 'user_groups', 6, 'USER_GROUP_UPDATED', '{\"name\":\"Customer Service\",\"description\":\"\",\"id\":\"6\"}', '113.211.209.108', '2022-08-04 22:26:17'),
(170, 2, 'user_groups', 9, 'USER_GROUP_CREATED', '{\"name\":\"Clients\",\"description\":\"\"}', '113.211.209.108', '2022-08-04 22:26:28'),
(171, 2, 'designations', 1, 'DESIGNATION_UPDATED', '{\"name\":\"Class D\",\"description\":\"\",\"id\":\"1\"}', '113.211.209.108', '2022-08-04 22:26:52'),
(172, 2, 'designations', 2, 'DESIGNATION_CREATED', '{\"name\":\"Class DA\",\"description\":\"\"}', '113.211.209.108', '2022-08-04 22:27:02'),
(173, 2, 'designations', 3, 'DESIGNATION_CREATED', '{\"name\":\"Class E1\",\"description\":\"\"}', '113.211.209.108', '2022-08-04 22:27:13'),
(174, 2, 'designations', 4, 'DESIGNATION_CREATED', '{\"name\":\"Class E2\",\"description\":\"\"}', '113.211.209.108', '2022-08-04 22:27:20'),
(175, 2, 'designations', 5, 'DESIGNATION_CREATED', '{\"name\":\"Class F\",\"description\":\"\"}', '113.211.209.108', '2022-08-04 22:27:28'),
(176, 2, 'designations', 6, 'DESIGNATION_CREATED', '{\"name\":\"Class G\",\"description\":\"\"}', '113.211.209.108', '2022-08-04 22:27:38'),
(177, 2, 'companies', 1, 'COMPANY_CREATED', '{\"code\":\"KG\",\"name\":\"KELLOGG\'S MALAYSIA\",\"notes\":\"\"}', '113.211.209.108', '2022-08-04 22:31:55'),
(178, 2, 'companies', 1, 'COMPANY_UPDATED', '{\"code\":\"KG\",\"name\":\"KELLOGG\'S MALAYSIA\",\"vessel_billing_type\":\"resources\",\"warehouse_billing_type\":\"commodity\",\"id\":\"1\"}', '113.211.209.108', '2022-08-04 22:32:02'),
(179, 2, 'companies', 2, 'COMPANY_CREATED', '{\"code\":\"CC\",\"name\":\"COCA-COLA MALAYSIA\",\"notes\":\"\"}', '113.211.209.108', '2022-08-04 22:32:27'),
(180, 2, 'companies', 3, 'COMPANY_CREATED', '{\"code\":\"AJ\",\"name\":\"AJINOMOTO MALAYSIA\",\"notes\":\"\"}', '113.211.209.108', '2022-08-04 22:32:54'),
(181, 2, 'companies', 4, 'COMPANY_CREATED', '{\"code\":\"NS\",\"name\":\"NESTLE MALAYSIA\",\"notes\":\"\"}', '113.211.209.108', '2022-08-04 22:33:14'),
(182, 2, 'companies', 5, 'COMPANY_CREATED', '{\"code\":\"DL\",\"name\":\"DUTCH LADY MALAYSIA\",\"notes\":\"\"}', '113.211.209.108', '2022-08-04 22:33:34'),
(183, 2, 'companies', 6, 'COMPANY_CREATED', '{\"code\":\"PF\",\"name\":\"PETROFAC MALAYSIA\",\"notes\":\"\"}', '113.211.209.108', '2022-08-04 22:34:09'),
(184, 2, 'companies', 7, 'COMPANY_CREATED', '{\"code\":\"MD\",\"name\":\"MONDELEZ MALAYSIA\",\"notes\":\"\"}', '113.211.209.108', '2022-08-04 22:34:38'),
(185, 2, 'workers', 3, 'DRIVER_ADDED', '{\"name\":\"Ali bin Ahmad\",\"type\":\"contract-monthly\",\"resource_type\":[\"1\"],\"bank_account\":\"\",\"ic_number\":\"771029108112\",\"contact_number\":\"0197757522\",\"age\":\"35\",\"ext_work_hours\":\"8\",\"ext_address\":\"\"}', '113.211.209.108', '2022-08-04 22:36:53'),
(186, 2, 'workers', 2, 'DRIVER_UPDATED', '{\"name\":\"Johan bin Setia \",\"type\":\"contract-monthly\",\"contact_number\":\"0197665552\",\"age\":\"31\",\"ext_work_hours\":\"8\",\"bank_account\":\"\",\"resource_type\":[\"3\"],\"ic_number\":\"781012108119\",\"ext_address\":\"\",\"id\":\"2\"}', '113.211.209.108', '2022-08-04 22:37:54'),
(187, 2, 'workers', 1, 'DRIVER_UPDATED', '{\"name\":\"Kassim bin Selamat\",\"type\":\"contract-monthly\",\"contact_number\":\"0172557676\",\"age\":\"39\",\"ext_work_hours\":\"8\",\"bank_account\":\"\",\"resource_type\":[\"2\"],\"ic_number\":\"791012107112\",\"ext_address\":\"\",\"id\":\"1\"}', '113.211.209.108', '2022-08-04 22:39:00'),
(188, 2, 'workers', 1, 'DRIVER_UPDATED', '{\"name\":\"Kassim bin Selamat\",\"type\":\"contract-monthly\",\"contact_number\":\"0172557676\",\"age\":\"39\",\"ext_work_hours\":\"8.00\",\"bank_account\":\"\",\"resource_type\":[\"2\"],\"ic_number\":\"791012107112\",\"ext_address\":\"\",\"id\":\"1\"}', '113.211.209.108', '2022-08-04 22:39:05'),
(189, 2, 'workers', 4, 'DRIVER_ADDED', '{\"name\":\"Denno bin Nazmi\",\"type\":\"contract-monthly\",\"resource_type\":[\"4\"],\"bank_account\":\"\",\"ic_number\":\"811012107600\",\"contact_number\":\"0128119898\",\"age\":\"41\",\"ext_work_hours\":\"8\",\"ext_address\":\"\"}', '113.211.209.108', '2022-08-04 22:41:07'),
(190, 2, 'workers', 1, 'DRIVER_UPDATED', '{\"name\":\"Kassim bin Selamat\",\"type\":\"contract-monthly\",\"contact_number\":\"0172557676\",\"age\":\"43\",\"ext_work_hours\":\"8.00\",\"bank_account\":\"\",\"resource_type\":[\"2\"],\"ic_number\":\"791012107112\",\"ext_address\":\"\",\"id\":\"1\"}', '113.211.209.108', '2022-08-04 22:41:21'),
(191, 2, 'workers', 2, 'DRIVER_UPDATED', '{\"name\":\"Johan bin Setia \",\"type\":\"contract-monthly\",\"contact_number\":\"0197665552\",\"age\":\"44\",\"ext_work_hours\":\"8.00\",\"bank_account\":\"\",\"resource_type\":[\"3\"],\"ic_number\":\"781012108119\",\"ext_address\":\"\",\"id\":\"2\"}', '113.211.209.108', '2022-08-04 22:41:49'),
(192, 2, 'workers', 3, 'DRIVER_UPDATED', '{\"name\":\"Ali bin Ahmad\",\"type\":\"contract-monthly\",\"contact_number\":\"0197757522\",\"age\":\"45\",\"ext_work_hours\":\"8.00\",\"bank_account\":\"\",\"resource_type\":[\"1\"],\"ic_number\":\"771029108112\",\"ext_address\":\"\",\"id\":\"3\"}', '113.211.209.108', '2022-08-04 22:42:20'),
(193, 2, '', 1, 'EQUIPMENT_TYPE_UPDATED', '{\"name\":\"Mini Compactor\",\"short_code\":\"MC\",\"resource_type\":\"2\",\"description\":\"\",\"colour\":\"#193fd8\",\"cost\":\"\",\"fuel_cost\":\"\",\"id\":\"1\"}', '113.211.209.108', '2022-08-04 22:47:04'),
(194, 2, '', 2, 'EQUIPMENT_TYPE_UPDATED', '{\"name\":\"Bulk Waste Tipper\",\"short_code\":\"BWT\",\"resource_type\":\"3\",\"description\":\"\",\"colour\":\"#42f8ff\",\"cost\":\"\",\"fuel_cost\":\"\",\"id\":\"2\"}', '113.211.209.108', '2022-08-04 22:47:42'),
(195, 2, 'equipments', 4, 'EQUIPMENT_UPDATED', '{\"name\":\"RORO_1\",\"code\":\"ABC123\",\"purchase_date\":\"01\\/08\\/2022\",\"equipment_status\":\"In use\",\"equipment_manufacturer\":\"1\",\"equipment_type\":\"11\",\"notes\":\"\",\"safe_load\":\"80MT\",\"id\":\"4\"}', '113.211.209.108', '2022-08-04 22:53:05'),
(196, 2, 'equipments', 3, 'EQUIPMENT_UPDATED', '{\"name\":\"RORO_2\",\"code\":\"ABD123\",\"purchase_date\":\"01\\/08\\/2022\",\"equipment_status\":\"In use\",\"equipment_manufacturer\":\"1\",\"equipment_type\":\"11\",\"notes\":\"\",\"safe_load\":\"80MT\",\"id\":\"3\"}', '113.211.209.108', '2022-08-04 22:53:38'),
(197, 2, 'equipments', 3, 'ITEM_ACTIVE', '', '113.211.209.108', '2022-08-04 22:53:44'),
(198, 2, 'equipments', 1, 'EQUIPMENT_UPDATED', '{\"name\":\"COMPACT_1\",\"code\":\"CBA123\",\"purchase_date\":\"01\\/08\\/2022\",\"equipment_status\":\"In use\",\"equipment_manufacturer\":\"3\",\"equipment_type\":\"6\",\"notes\":\"\",\"safe_load\":\"100MT\",\"id\":\"1\"}', '113.211.209.108', '2022-08-04 22:54:39'),
(199, 2, 'equipments', 1, 'ITEM_ACTIVE', '', '113.211.209.108', '2022-08-04 22:54:45'),
(200, 2, 'equipments', 6, 'GROUPS_UPDATED', '{\"groups\":[\"5\"],\"id\":\"6\"}', '113.211.209.108', '2022-08-04 22:55:18'),
(201, 2, 'equipments', 6, 'EQUIPMENT_UPDATED', '{\"name\":\"COMPACT_2\",\"code\":\"CBB123\",\"purchase_date\":\"01\\/08\\/2022\",\"equipment_status\":\"In use\",\"equipment_manufacturer\":\"3\",\"equipment_type\":\"6\",\"notes\":\"\",\"safe_load\":\"100MT\",\"id\":\"6\"}', '113.211.209.108', '2022-08-04 22:55:20'),
(202, 2, 'equipments', 5, 'GROUPS_UPDATED', '{\"groups\":[\"3\"],\"id\":\"5\"}', '113.211.209.108', '2022-08-04 22:56:13'),
(203, 2, 'equipments', 5, 'GROUPS_UPDATED', '{\"groups\":[\"3\"],\"id\":\"5\"}', '113.211.209.108', '2022-08-04 22:56:21'),
(204, 2, 'equipments', 5, 'EQUIPMENT_UPDATED', '{\"name\":\"TIPPER_1\",\"code\":\"XYZ123\",\"purchase_date\":\"01\\/08\\/2022\",\"equipment_status\":\"In use\",\"equipment_manufacturer\":\"2\",\"equipment_type\":\"2\",\"notes\":\"\",\"safe_load\":\"50MT\",\"id\":\"5\"}', '113.211.209.108', '2022-08-04 22:57:15'),
(205, 2, 'equipments', 2, 'EQUIPMENT_UPDATED', '{\"name\":\"MINI_1\",\"code\":\"WTW123\",\"purchase_date\":\"01\\/08\\/2022\",\"equipment_status\":\"In use\",\"equipment_manufacturer\":\"2\",\"equipment_type\":\"1\",\"notes\":\"\",\"safe_load\":\"\",\"id\":\"2\"}', '113.211.209.108', '2022-08-04 22:58:11'),
(206, 2, 'equipments', 2, 'GROUPS_UPDATED', '{\"groups\":[\"5\"],\"id\":\"2\"}', '113.211.209.108', '2022-08-04 22:58:21'),
(207, 2, 'equipments', 5, 'MILEAGE_ADDED', '{\"mileage\":\"81722\",\"record_date\":\"05\\/08\\/2022\",\"id\":\"5\"}', '113.211.209.108', '2022-08-04 22:58:58'),
(208, 2, 'equipments', 5, 'SCHEDULED_MAINTENANCE_ADDED', '{\"next_maintenance_date\":\"31\\/08\\/2022\",\"next_maintenance_mileage\":\"900000\",\"id\":\"5\"}', '113.211.209.108', '2022-08-04 22:59:11'),
(209, 2, '', 9, 'MANUFACTURER_CREATED', '{\"name\":\"Perstorp\",\"description\":\"\"}', '113.211.209.108', '2022-08-04 23:04:39'),
(210, 2, 'equipments', 5, 'ASSET_UPDATED', '{\"name\":\"RRB_1\",\"code\":\"UER111112\",\"purchase_date\":\"01\\/08\\/2022\",\"equipment_status\":\"In use\",\"equipment_manufacturer\":\"8\",\"equipment_type\":\"13\",\"notes\":\"\",\"safe_load\":\"5MT\",\"id\":\"5\"}', '113.211.209.108', '2022-08-04 23:05:36'),
(211, 2, 'users', 2, 'LOGOUT', 'Logged out manually.', '113.211.209.108', '2022-08-04 23:24:17'),
(212, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '111.88.100.171', '2022-08-05 02:23:53'),
(213, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '111.88.100.171', '2022-08-05 02:23:53'),
(214, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '113.211.208.82', '2022-08-05 02:27:22'),
(215, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '113.211.208.82', '2022-08-05 02:27:22'),
(216, 2, 'users', 2, 'LOGOUT', 'Logged out manually.', '111.88.100.171', '2022-08-05 02:30:39'),
(217, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '111.88.100.171', '2022-08-05 02:30:52'),
(218, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '111.88.100.171', '2022-08-05 02:30:53'),
(219, 2, 'users', 2, 'LOGOUT', 'Logged out manually.', '113.211.208.82', '2022-08-05 02:35:58'),
(220, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '113.211.208.82', '2022-08-05 02:36:08'),
(221, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '113.211.208.82', '2022-08-05 02:36:08'),
(222, 2, 'workers', 1, 'DRIVER_UPDATED', '{\"name\":\"Kassim bin Selamat\",\"type\":\"contract-monthly\",\"contact_number\":\"0172557676\",\"age\":\"43\",\"ext_work_hours\":\"8.00\",\"bank_account\":\"\",\"resource_type\":[\"2\"],\"ic_number\":\"791012107112\",\"ext_address\":\"\",\"id\":\"1\"}', '113.211.208.82', '2022-08-05 02:37:16'),
(223, 2, 'users', 2, 'LOGOUT', 'Logged out manually.', '111.88.100.171', '2022-08-05 02:37:28'),
(224, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '111.88.100.171', '2022-08-05 02:37:41'),
(225, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '111.88.100.171', '2022-08-05 02:37:41'),
(226, 2, 'users', 2, 'LOGOUT', 'Logged out manually.', '113.211.208.82', '2022-08-05 02:41:25'),
(227, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '113.211.208.82', '2022-08-05 02:49:29'),
(228, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '113.211.208.82', '2022-08-05 02:49:29'),
(229, 2, 'users', 2, 'LOGOUT', 'Logged out manually.', '111.88.100.171', '2022-08-05 02:50:58'),
(230, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '111.88.100.171', '2022-08-05 02:51:23'),
(231, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '111.88.100.171', '2022-08-05 02:51:34'),
(232, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '111.88.100.171', '2022-08-05 02:51:34'),
(233, 2, 'users', 2, 'LOGOUT', 'Logged out manually.', '113.211.208.82', '2022-08-05 02:52:10'),
(234, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '113.211.208.82', '2022-08-05 02:52:37'),
(235, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '113.211.208.82', '2022-08-05 02:52:37'),
(236, 2, 'users', 2, 'LOGOUT', 'Logged out manually.', '113.211.208.82', '2022-08-05 02:55:25'),
(237, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '113.211.208.82', '2022-08-05 02:55:57'),
(238, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '113.211.208.82', '2022-08-05 02:55:58'),
(239, 2, 'workers', 1, 'GROUPS_UPDATED', '{\"groups\":[\"1\",\"6\",\"2\",\"4\"],\"id\":\"1\"}', '113.211.208.82', '2022-08-05 02:56:19'),
(240, 2, 'users', 2, 'LOGOUT', 'Logged out manually.', '113.211.208.82', '2022-08-05 02:56:56'),
(241, 2, 'users', 2, 'LOGOUT', 'Logged out manually.', '111.88.100.171', '2022-08-05 02:58:26'),
(242, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '111.88.100.171', '2022-08-05 02:58:35'),
(243, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '111.88.100.171', '2022-08-05 02:58:36'),
(244, 2, 'users', 2, 'LOGOUT', 'Logged out manually.', '::1', '2022-08-05 03:25:44'),
(245, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '::1', '2022-08-05 03:25:55'),
(246, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '::1', '2022-08-05 03:26:02'),
(247, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '::1', '2022-08-05 03:26:02'),
(248, 2, '', 7, 'ASSET_ADDED', '{\"name\":\"gfgf\",\"code\":\"G54\",\"purchase_date\":\"01\\/08\\/2022\",\"equipment_status\":\"Maintenance\",\"equipment_manufacturer\":\"2\",\"equipment_type\":\"1\",\"notes\":\"ggg\",\"safe_load\":\"g\"}', '::1', '2022-08-05 03:26:23'),
(249, 2, '', 7, 'ASSET_UPDATED', '{\"name\":\"gfgf\",\"code\":\"G5411\",\"purchase_date\":\"01\\/08\\/2022\",\"equipment_status\":\"Maintenance\",\"equipment_manufacturer\":\"2\",\"equipment_type\":\"1\",\"notes\":\"ggg\",\"safe_load\":\"g\",\"id\":\"7\"}', '::1', '2022-08-05 03:26:37'),
(250, 2, 'equipments', 5, 'MAINTENANCE_ADDED', '{\"in_out\":\"In maintenance\",\"maintenance_date\":\"03\\/08\\/2022\",\"maintenance_mileage\":\"\",\"maintenance_files\":\"\",\"maintenance_notes\":\"ssss\",\"id\":\"5\"}', '::1', '2022-08-05 03:27:30'),
(251, 2, '', 5, 'SCHEDULED_MAINTENANCE_ADDED', '{\"next_maintenance_date\":\"03\\/08\\/2022\",\"next_maintenance_mileage\":\"5\",\"id\":\"5\"}', '::1', '2022-08-05 03:27:41'),
(252, 2, '', 1, 'CONSUMABLE_ADDED', '{\"consumable_id\":\"1\",\"consumable_quantity\":\"2\",\"consumable_date\":\"04\\/08\\/2022\",\"id\":\"5\"}', '::1', '2022-08-05 03:28:17'),
(253, 2, '', 7, 'ASSET_GROUP_ADDED', '{\"name\":\"aa\",\"code\":\"BB\",\"notes\":\"cc\"}', '::1', '2022-08-05 03:32:26'),
(254, 2, 'users', 2, 'LOGOUT', 'Logged out manually.', '::1', '2022-08-08 08:57:25'),
(255, 2, 'users', 2, 'LOGOUT', 'Logged out manually.', '::1', '2022-08-08 08:57:25'),
(256, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '::1', '2022-08-08 08:57:32'),
(257, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '::1', '2022-08-08 08:57:33'),
(258, 2, 'workers', 1, 'PHOTO_UPLOADED', 'A new photo was uploaded.', '::1', '2022-08-08 08:57:55'),
(259, 2, '', 2, 'WORKER_GROUP_ADDED', '{\"name\":\"my grp1\",\"code\":\"MGP1\",\"notes\":\"aa\"}', '::1', '2022-08-08 08:58:22'),
(260, 2, 'workers', 0, 'PUBLIC_HOLIDAY_ADDED', 'Added 2022-08-08', '::1', '2022-08-08 08:58:48'),
(261, 2, 'workers', 0, 'PUBLIC_HOLIDAY_ADDED', 'Added 2022-08-09', '::1', '2022-08-08 08:59:00'),
(262, 2, 'workers', 1, 'ATTENDANCE_ADDED', '{\"work_start_end\":\"08-Aug 07:30 AM - 04:30 PM\",\"selectedDate\":\"2022-08-08\",\"work_start\":\"2022-08-08 07:30:00\",\"work_end\":\"2022-08-08 16:30:00\",\"worker_attendance\":\"P\",\"remarks\":\"ma1\",\"id\":\"1\"}', '::1', '2022-08-08 08:59:29'),
(263, 2, 'workers', 1, 'ATTENDANCE_ADDED', '{\"work_start_end\":\"\",\"selectedDate\":\"\",\"work_start\":\"2022-08-09 00:00:00\",\"work_end\":\"\",\"worker_attendance\":\"PL\",\"remarks\":\"aa\",\"id\":\"1\"}', '::1', '2022-08-08 08:59:49'),
(264, 2, 'workers', 1, 'ATTENDANCE_UPDATED', '{\"work_start_end\":\"\",\"selectedDate\":\"\",\"work_start\":\"\",\"work_end\":\"\",\"worker_attendance\":\"PL\",\"remarks\":\"aa\",\"id\":\"1\"}', '::1', '2022-08-08 09:00:15'),
(265, 2, 'workers', 1, 'ATTENDANCE_UPDATED', '{\"work_start_end\":\"\",\"selectedDate\":\"\",\"work_start\":\"2022-08-09 00:00:00\",\"work_end\":\"\",\"worker_attendance\":\"ML\",\"remarks\":\"\",\"id\":\"1\"}', '::1', '2022-08-08 13:37:05'),
(266, 2, 'workers', 5, 'DRIVER_ADDED', '{\"name\":\"aaa\",\"type\":\"casual-daily\",\"resource_type\":[\"1\"],\"bank_account\":\"33\",\"ic_number\":\"33\",\"contact_number\":\"33\",\"age\":\"4\",\"ext_work_hours\":\"5\",\"ext_address\":\"a\"}', '::1', '2022-08-08 14:34:25'),
(267, 2, 'users', 2, 'COLOR_CHANGED', '[]', '::1', '2022-08-08 14:35:20'),
(268, 2, 'users', 2, 'PICTURE_UPLOADED', 'A new profile photo was uploaded. attachment 1659969320-aaaaaaa.png was added.', '::1', '2022-08-08 14:35:20'),
(269, 2, 'workers', 1, 'DRIVER_UPDATED', '{\"name\":\"Kassim bin Selamat\",\"type\":\"contract-monthly\",\"contact_number\":\"0172557676\",\"age\":\"43\",\"ext_work_hours\":\"8.00\",\"bank_account\":\"\",\"resource_type\":[\"3\"],\"ic_number\":\"791012107112\",\"ext_address\":\"\",\"id\":\"1\"}', '::1', '2022-08-08 14:40:42'),
(270, 2, 'workers', 1, 'DRIVER_UPDATED', '{\"name\":\"Kassim bin Selamat\",\"type\":\"contract-monthly\",\"contact_number\":\"0172557676\",\"age\":\"43\",\"ext_work_hours\":\"8.00\",\"bank_account\":\"\",\"resource_type\":[\"2\"],\"ic_number\":\"791012107112\",\"ext_address\":\"\",\"id\":\"1\"}', '::1', '2022-08-08 14:42:44'),
(271, 2, 'workers', 1, 'DRIVER_UPDATED', '{\"name\":\"Kassim bin Selamat\",\"type\":\"contract-monthly\",\"contact_number\":\"0172557676\",\"age\":\"43\",\"ext_work_hours\":\"8.00\",\"bank_account\":\"\",\"resource_type\":[\"3\"],\"ic_number\":\"791012107112\",\"ext_address\":\"\",\"id\":\"1\"}', '::1', '2022-08-08 14:42:59'),
(272, 2, 'workers', 1, 'ATTENDANCE_UPDATED', '{\"work_start_end\":\"08-Aug 07:30 AM - 04:50 PM\",\"selectedDate\":\"2022-08-08\",\"work_start\":\"2022-08-08 07:30:00\",\"work_end\":\"2022-08-08 16:30:00\",\"remarks\":\"\",\"id\":\"1\"}', '::1', '2022-08-08 14:57:37'),
(273, 2, 'workers', 1, 'ATTENDANCE_UPDATED', '{\"work_start_end\":\"\",\"selectedDate\":\"2022-08-08\",\"work_start\":\"\",\"work_end\":\"\",\"worker_attendance\":\"P\",\"remarks\":\"aaa\",\"id\":\"1\"}', '::1', '2022-08-08 14:57:49'),
(274, 2, 'workers', 1, 'ATTENDANCE_ADDED', '{\"work_start_end\":\"07-Aug 08:30 AM - 02:30 PM\",\"selectedDate\":\"2022-08-07\",\"work_start\":\"2022-08-07 08:30:00\",\"work_end\":\"2022-08-07 14:30:00\",\"worker_attendance\":\"P\",\"remarks\":\"bb\",\"id\":\"1\"}', '::1', '2022-08-08 14:58:30'),
(275, 2, 'workers', 1, 'ATTENDANCE_DELETED', '{\"work_start_end\":\"06-Aug 07:30 AM - 04:30 PM\",\"selectedDate\":\"2022-08-06\",\"work_start\":\"2022-08-06 07:30:00\",\"work_end\":\"2022-08-06 16:30:00\",\"worker_attendance\":\"P\",\"remarks\":\"\",\"delete_attendance\":\"1\",\"id\":\"1\"}', '::1', '2022-08-08 14:59:01'),
(276, 2, 'workers', 1, 'ATTENDANCE_ADDED', '{\"work_start_end\":\"\",\"selectedDate\":\"2022-08-06\",\"work_start\":\"\",\"work_end\":\"\",\"worker_attendance\":\"XL\",\"remarks\":\"nn\",\"id\":\"1\"}', '::1', '2022-08-08 14:59:30'),
(277, 2, 'workers', 1, 'GROUPS_UPDATED', '{\"id\":\"1\"}', '::1', '2022-08-08 15:03:18'),
(278, 2, 'workers', 1, 'GROUPS_UPDATED', '{\"groups\":[\"2\"],\"id\":\"1\"}', '::1', '2022-08-08 15:03:31'),
(279, 2, 'workers', 1, 'DRIVER_UPDATED', '{\"name\":\"Kassim bin Selamat\",\"ic_number\":\"791012107112\",\"type\":\"contract-monthly\",\"resource_type\":[\"3\"],\"contact_number\":\"0172557676\",\"ext_work_hours\":\"8.00\",\"age\":\"43\",\"ext_address\":\"aaaaaa\",\"id\":\"1\"}', '::1', '2022-08-08 15:03:52'),
(280, 2, 'workers', 1, 'DRIVER_UPDATED', '{\"name\":\"Kassim bin Selamat 1\",\"ic_number\":\"791012107112 1\",\"type\":\"permanent-office\",\"resource_type\":[\"2\"],\"contact_number\":\"0172557676 1\",\"ext_work_hours\":\"8.001\",\"age\":\"431\",\"ext_address\":\"aaaaaa1\",\"id\":\"1\"}', '::1', '2022-08-08 15:04:09'),
(281, 2, 'workers', 1, 'DRIVER_UPDATED', '{\"name\":\"Kassim bin Selamat\",\"ic_number\":\"791012107112\",\"type\":\"contract-monthly\",\"resource_type\":[\"3\"],\"contact_number\":\"0172557676\",\"ext_work_hours\":\"8.1\",\"age\":\"43\",\"ext_address\":\"aaaaaa\",\"id\":\"1\"}', '::1', '2022-08-08 15:06:28'),
(282, 2, 'workers', 1, 'DRIVER_UPDATED', '{\"name\":\"Kassim bin Selamat\",\"ic_number\":\"791012107112\",\"type\":\"contract-monthly\",\"resource_type\":[\"3\"],\"contact_number\":\"0172557676\",\"ext_work_hours\":\"8.00\",\"age\":\"43\",\"ext_address\":\"aaaaaa\",\"id\":\"1\"}', '::1', '2022-08-08 15:06:43'),
(283, 2, 'workers', 1, 'PHOTO_UPLOADED', 'A new photo was uploaded.', '::1', '2022-08-08 15:07:50'),
(284, 2, 'workers', 5, 'DRIVER_UPDATED', '{\"name\":\"aaa\",\"ic_number\":\"33\",\"type\":\"casual-daily\",\"resource_type\":[\"\"],\"contact_number\":\"33\",\"ext_work_hours\":\"5.00\",\"age\":\"4\",\"ext_address\":\"a\",\"id\":\"5\"}', '::1', '2022-08-08 15:09:29'),
(285, 2, 'workers', 5, 'DRIVER_UPDATED', '{\"name\":\"aaa\",\"ic_number\":\"33\",\"type\":\"casual-daily\",\"resource_type\":[\"3\"],\"contact_number\":\"33\",\"ext_work_hours\":\"5.00\",\"age\":\"4\",\"ext_address\":\"aa\",\"id\":\"5\"}', '::1', '2022-08-08 15:09:59'),
(286, 2, 'workers', 5, 'DRIVER_UPDATED', '{\"name\":\"aaa\",\"ic_number\":\"33\",\"type\":\"casual-daily\",\"resource_type\":[\"\"],\"contact_number\":\"33\",\"ext_work_hours\":\"5.00\",\"age\":\"4\",\"ext_address\":\"aa\",\"id\":\"5\"}', '::1', '2022-08-08 15:10:33'),
(287, 2, 'workers', 5, 'DRIVER_UPDATED', '{\"name\":\"aaa\",\"ic_number\":\"33\",\"type\":\"casual-daily\",\"resource_type\":[\"2\"],\"contact_number\":\"33\",\"ext_work_hours\":\"5.00\",\"age\":\"4\",\"ext_address\":\"aa\",\"id\":\"5\"}', '::1', '2022-08-08 15:10:48'),
(288, 2, '', 2, 'DRIVER_GROUP_UPDATED', '{\"name\":\"my grp1\",\"code\":\"MGP1\",\"payroll_start\":\"\",\"notes\":\"aa\",\"id\":\"2\"}', '::1', '2022-08-08 15:20:12'),
(289, 2, '', 3, 'DRIVER_GROUP_ADDED', '{\"name\":\"bb\",\"code\":\"C2\",\"notes\":\"aabb\"}', '::1', '2022-08-08 15:29:24'),
(290, 2, '', 3, 'DRIVER_GROUP_UPDATED', '{\"name\":\"bb2\",\"code\":\"C2\",\"payroll_start\":\"\",\"notes\":\"aabb\",\"id\":\"3\"}', '::1', '2022-08-08 15:29:38'),
(291, 2, '', 3, 'DRIVER_GROUP_UPDATED', '{\"name\":\"bb2\",\"code\":\"C2B\",\"payroll_start\":\"1\",\"notes\":\"aabba\",\"id\":\"3\"}', '::1', '2022-08-08 15:29:54'),
(292, 2, '', 3, 'DRIVER_GROUP_UPDATED', '{\"name\":\"bb\",\"code\":\"C2\",\"payroll_start\":\"\",\"notes\":\"aabba\",\"id\":\"3\"}', '::1', '2022-08-08 15:30:07'),
(293, 2, '', 2, 'DRIVER_GROUP_UPDATED', '{\"name\":\"my grp1\",\"code\":\"MGP1\",\"payroll_start\":\"\",\"notes\":\"aa\",\"id\":\"2\"}', '::1', '2022-08-08 15:30:46'),
(294, 2, 'equipments', 3, 'MILEAGE_ADDED', '{\"mileage\":\"12\",\"record_date\":\"08\\/08\\/2022\",\"id\":\"3\"}', '::1', '2022-08-08 16:27:14'),
(295, 2, 'equipments', 3, 'EQUIPMENT_UPDATED', '{\"name\":\"RORO_2\",\"code\":\"ABD123\",\"purchase_date\":\"01\\/08\\/2022\",\"equipment_status\":\"In use\",\"equipment_manufacturer\":\"2\",\"equipment_type\":\"9\",\"notes\":\"\",\"safe_load\":\"80MT\",\"id\":\"3\"}', '::1', '2022-08-08 16:27:45'),
(296, 2, 'equipments', 3, 'EQUIPMENT_UPDATED', '{\"name\":\"RORO_2\",\"code\":\"ABD123\",\"purchase_date\":\"01\\/08\\/2022\",\"equipment_status\":\"In use\",\"equipment_manufacturer\":\"1\",\"equipment_type\":\"11\",\"notes\":\"\",\"safe_load\":\"80MT\",\"id\":\"3\"}', '::1', '2022-08-08 16:27:56'),
(297, 2, 'equipments', 7, 'TRUCK_ADDED', '{\"name\":\"aa\",\"code\":\"1131313\",\"purchase_date\":\"02\\/08\\/2022\",\"equipment_status\":\"Maintenance\",\"equipment_manufacturer\":\"5\",\"equipment_type\":\"1\",\"current_mileage\":\"2\",\"service_every_mileage\":\"5\",\"next_service_mileage\":\"12\",\"last_service_date\":\"14\\/08\\/2022\",\"service_interval_weeks\":\"2\",\"next_service_date\":\"03\\/08\\/2022\",\"notes\":\"aa\",\"safe_load\":\"aa\"}', '::1', '2022-08-08 16:32:45'),
(298, 2, 'equipments', 7, 'SCHEDULED_MAINTENANCE_ADDED', '{\"next_maintenance_date\":\"09\\/08\\/2022\",\"next_maintenance_mileage\":\"55\",\"id\":\"7\"}', '::1', '2022-08-08 16:33:24'),
(299, 2, 'equipments', 7, 'SCHEDULED_MAINTENANCE_ADDED', '{\"next_maintenance_date\":\"08\\/08\\/2022\",\"next_maintenance_mileage\":\"11\",\"id\":\"7\"}', '::1', '2022-08-08 16:50:12'),
(300, 2, 'equipments', 7, 'MAINTENANCE_ADDED', '{\"in_out\":\"In maintenance\",\"maintenance_date\":\"01\\/08\\/2022\",\"maintenance_mileage\":\"5\",\"maintenance_files\":\"{\\\"files\\\":[\\\"1659977759-aaaaaaa.png\\\"]}\",\"maintenance_notes\":\"\",\"id\":\"7\"}', '::1', '2022-08-08 16:56:22'),
(301, 2, 'equipments', 1, 'EQUIPMENT_UPDATED', '{\"name\":\"COMPACT_1\",\"code\":\"CBA123\",\"purchase_date\":\"01\\/08\\/2022\",\"equipment_status\":\"In use\",\"equipment_manufacturer\":\"3\",\"equipment_type\":\"6\",\"notes\":\"\",\"safe_load\":\"100MT\",\"id\":\"1\"}', '::1', '2022-08-08 17:03:42'),
(302, 2, 'equipments', 5, 'ITEM_DISABLED', '', '::1', '2022-08-08 17:09:05'),
(303, 2, 'equipments', 5, 'ITEM_ACTIVE', '', '::1', '2022-08-08 17:09:06'),
(304, 2, 'equipments', 1, 'CONSUMABLE_ADDED', '{\"consumable_id\":\"1\",\"consumable_quantity\":\"5\",\"consumable_date\":\"04\\/08\\/2022\",\"id\":\"7\"}', '::1', '2022-08-08 18:41:55'),
(305, 2, 'consumables', 1, 'CONSUMABLE_UPDATED', '{\"name\":\"dfd\",\"opening_stock\":\"-4.00\",\"replenishment_level\":\"45.00\",\"notes\":\"vcbvcb\",\"id\":\"1\"}', '::1', '2022-08-08 18:56:22'),
(306, 2, 'consumables', 1, 'CONSUMABLE_PURCHASE_ADDED', '{\"purchase_datetime\":\"04\\/08\\/2022 23:56\",\"quantity\":\"5\",\"purchase_order\":\"12\",\"purchase_notes\":\"aa\",\"id\":\"1\"}', '::1', '2022-08-08 18:56:43'),
(307, 2, '', 8, 'ASSET_GROUP_ADDED', '{\"name\":\"aa\",\"code\":\"BB\",\"notes\":\"cc\"}', '::1', '2022-08-08 18:58:33'),
(308, 2, 'equipments', 0, 'ASSET_USAGE_ADDED', '{\"vh_date\":\"2022-08-09\",\"vh_time_start\":\"01:42\",\"vh_time_end\":\"00:45\",\"vh_location_start\":\"aa\",\"vh_location_end\":\"cc\",\"driver_id\":\"2|781012108119\",\"id\":\"7\"}', '::1', '2022-08-08 19:42:25'),
(309, 2, '', 7, 'GROUPS_UPDATED', '{\"groups\":[\"2\",\"6\"],\"id\":\"7\"}', '::1', '2022-08-08 19:53:53'),
(310, 2, '', 7, 'GROUPS_UPDATED', '{\"groups\":[\"4\",\"5\",\"6\"],\"id\":\"7\"}', '::1', '2022-08-08 19:54:07'),
(311, 2, '', 7, 'GROUPS_UPDATED', '{\"id\":\"7\"}', '::1', '2022-08-08 19:54:32'),
(312, 2, 'equipments', 8, 'TRUCK_ADDED', '{\"name\":\"aa\",\"code\":\"3\",\"purchase_date\":\"\",\"equipment_status\":\"\",\"equipment_manufacturer\":\"\",\"equipment_type\":\"1\",\"current_mileage\":\"\",\"service_every_mileage\":\"\",\"next_service_mileage\":\"\",\"last_service_date\":\"\",\"service_interval_weeks\":\"\",\"next_service_date\":\"\",\"notes\":\"\",\"safe_load\":\"\"}', '::1', '2022-08-08 19:58:33'),
(313, 2, '', 6, 'ASSET_PHOTO_UPLOADED', 'A new photo was uploaded.', '::1', '2022-08-08 20:16:08'),
(314, 2, '', 8, 'ASSET_PHOTO_UPLOADED', 'A new photo was uploaded.', '::1', '2022-08-08 20:29:16'),
(315, 2, '', 8, 'ASSET_PHOTO_UPLOADED', 'A new photo was uploaded.', '::1', '2022-08-08 20:31:41'),
(316, 2, '', 8, 'TRUCK_PHOTO_UPLOADED', 'A new photo was uploaded.', '::1', '2022-08-08 20:32:30'),
(317, 2, '', 8, 'TRUCK_PHOTO_UPLOADED', 'A new photo was uploaded.', '::1', '2022-08-08 20:32:59'),
(318, 2, '', 8, 'TRUCK_PHOTO_UPLOADED', 'A new photo was uploaded.', '::1', '2022-08-08 20:38:33'),
(319, 2, '', 3, 'TRUCK_PHOTO_UPLOADED', 'A new photo was uploaded.', '::1', '2022-08-08 20:52:23'),
(320, 2, '', 3, 'TRUCK_PHOTO_UPLOADED', 'A new photo was uploaded.', '::1', '2022-08-08 20:52:31'),
(321, 2, '', 3, 'TRUCK_PHOTO_UPLOADED', 'A new photo was uploaded.', '::1', '2022-08-08 21:19:58'),
(322, 2, '', 5, 'TRUCK_PHOTO_UPLOADED', 'A new photo was uploaded.', '::1', '2022-08-08 21:27:32'),
(323, 2, '', 5, 'TRUCK_PHOTO_UPLOADED', 'A new photo was uploaded.', '::1', '2022-08-08 21:30:58'),
(324, 2, '', 5, 'TRUCK_PHOTO_UPLOADED', 'A new photo was uploaded.', '::1', '2022-08-08 21:31:35'),
(325, 2, '', 5, 'ASSET_PHOTO_UPLOADED', 'A new photo was uploaded.', '::1', '2022-08-08 21:33:11'),
(326, 2, '', 7, 'ASSET_PHOTO_UPLOADED', 'A new photo was uploaded.', '::1', '2022-08-08 22:41:33'),
(327, 2, '', 2, 'ASSET_PHOTO_UPLOADED', 'A new photo was uploaded.', '::1', '2022-08-08 22:44:34'),
(328, 2, 'equipments', 5, 'MILEAGE_ADDED', '{\"mileage\":\"800\",\"record_date\":\"09\\/08\\/2022\",\"id\":\"5\"}', '::1', '2022-08-08 23:21:59'),
(329, 2, 'equipments', 5, 'SCHEDULED_MAINTENANCE_ADDED', '{\"next_maintenance_date\":\"12\\/08\\/2022\",\"next_maintenance_mileage\":\"150\",\"id\":\"5\"}', '::1', '2022-08-08 23:26:20'),
(330, 2, 'equipments', 5, 'SCHEDULED_MAINTENANCE_ADDED', '{\"next_maintenance_date\":\"10\\/08\\/2022\",\"next_maintenance_mileage\":\"150\",\"id\":\"5\"}', '::1', '2022-08-08 23:30:34'),
(331, 2, 'equipments', 5, 'SCHEDULED_MAINTENANCE_ADDED', '{\"next_maintenance_date\":\"10\\/08\\/2022\",\"next_maintenance_mileage\":\"112\",\"id\":\"5\"}', '::1', '2022-08-08 23:34:04'),
(332, 2, '', 2, 'TRUCK_PHOTO_UPLOADED', 'A new photo was uploaded.', '::1', '2022-08-08 23:39:18'),
(333, 2, 'workers', 6, 'DRIVER_ADDED', '{\"name\":\"aa\",\"type\":\"permanent-ops\",\"resource_type\":[\"1\"],\"bank_account\":\"\",\"ic_number\":\"5\",\"contact_number\":\"\",\"age\":\"4\",\"ext_work_hours\":\"4\",\"ext_address\":\"\"}', '::1', '2022-08-08 23:41:28');
INSERT INTO `logs` (`log_id`, `log_user_id`, `log_item_table`, `log_item_id`, `log_code`, `log_description`, `log_ip`, `timestamp`) VALUES
(334, 2, 'workers', 6, 'PHOTO_UPLOADED', 'A new photo was uploaded.', '::1', '2022-08-08 23:41:41'),
(335, 2, 'equipments', 0, 'ASSET_USAGE_ADDED', '{\"vh_date\":\"2022-08-09\",\"vh_time_start\":\"06:42\",\"vh_time_end\":\"08:42\",\"vh_location_start\":\"aa\",\"vh_location_end\":\"bb\",\"driver_id\":\"2|781012108119\",\"id\":\"6\"}', '::1', '2022-08-08 23:42:27'),
(336, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '39.34.131.11', '2022-08-08 23:54:30'),
(337, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '39.34.131.11', '2022-08-08 23:54:38'),
(338, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '39.34.131.11', '2022-08-08 23:54:39'),
(339, 2, 'users', 2, 'COLOR_CHANGED', '[]', '39.34.131.11', '2022-08-08 23:55:59'),
(340, 2, 'users', 2, 'PICTURE_UPLOADED', 'A new profile photo was uploaded. attachment 1660002962-istockphoto-1223671392-612x612.jpg was added.', '39.34.131.11', '2022-08-08 23:56:02'),
(341, 2, 'users', 2, 'COLOR_CHANGED', '[]', '39.34.131.11', '2022-08-08 23:56:06'),
(342, 2, 'workers', 1, 'PHOTO_UPLOADED', 'A new photo was uploaded.', '39.34.131.11', '2022-08-08 23:56:18'),
(343, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '113.211.209.224', '2022-08-09 03:10:28'),
(344, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '113.211.209.224', '2022-08-09 03:10:28'),
(345, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '202.186.228.42', '2022-08-09 06:01:18'),
(346, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '202.186.228.42', '2022-08-09 06:01:18'),
(347, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '39.34.131.11', '2022-08-09 06:15:57'),
(348, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '39.34.131.11', '2022-08-09 06:15:57'),
(349, 2, 'users', 2, 'LOGOUT', 'Logged out manually.', '39.34.131.11', '2022-08-09 06:17:58'),
(350, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '113.211.209.224', '2022-08-09 06:30:07'),
(351, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '113.211.209.224', '2022-08-09 06:30:07'),
(352, 2, 'equipments', 5, 'EQUIPMENT_UPDATED', '{\"name\":\"RRB_1\",\"code\":\"UER111112\",\"purchase_date\":\"01\\/08\\/2022\",\"equipment_status\":\"In use\",\"equipment_manufacturer\":\"8\",\"equipment_type\":\"13\",\"notes\":\"\",\"safe_load\":\"5MT\",\"id\":\"5\"}', '113.211.209.224', '2022-08-09 06:31:38'),
(353, 2, 'workers', 1, 'PHOTO_UPLOADED', 'A new photo was uploaded.', '113.211.209.224', '2022-08-09 06:32:46'),
(354, 2, 'workers', 2, 'PHOTO_UPLOADED', 'A new photo was uploaded.', '113.211.209.224', '2022-08-09 06:33:07'),
(355, 2, 'workers', 3, 'PHOTO_UPLOADED', 'A new photo was uploaded.', '113.211.209.224', '2022-08-09 06:33:27'),
(356, 2, 'workers', 4, 'PHOTO_UPLOADED', 'A new photo was uploaded.', '113.211.209.224', '2022-08-09 06:33:44'),
(357, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '39.34.131.11', '2022-08-09 06:42:24'),
(358, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '39.34.131.11', '2022-08-09 06:42:24'),
(359, 2, 'users', 2, 'LOGOUT', 'Logged out manually.', '113.211.209.224', '2022-08-09 06:42:27'),
(360, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '39.34.131.11', '2022-08-09 06:51:16'),
(361, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '39.34.131.11', '2022-08-09 06:51:16'),
(362, 2, 'workers', 1, 'PHOTO_UPLOADED', 'A new photo was uploaded.', '39.34.131.11', '2022-08-09 07:44:26'),
(363, 2, 'users', 2, 'COLOR_CHANGED', '[]', '39.34.131.11', '2022-08-09 07:44:35'),
(364, 2, 'users', 2, 'PICTURE_UPLOADED', 'A new profile photo was uploaded. attachment 1660031078-aaaaa.PNG was added.', '39.34.131.11', '2022-08-09 07:44:38'),
(365, 2, 'users', 2, 'COLOR_CHANGED', '[]', '39.34.131.11', '2022-08-09 07:44:42'),
(366, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '113.211.209.224', '2022-08-09 07:47:25'),
(367, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '113.211.209.224', '2022-08-09 07:47:25'),
(368, 2, '', 6, 'ASSET_PHOTO_UPLOADED', 'A new photo was uploaded.', '113.211.209.224', '2022-08-09 07:47:47'),
(369, 2, 'users', 2, 'LOGOUT', 'Logged out manually.', '39.34.131.11', '2022-08-09 07:48:09'),
(370, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '39.34.131.11', '2022-08-09 07:48:14'),
(371, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '39.34.131.11', '2022-08-09 07:48:14'),
(372, 2, 'users', 2, 'LOGOUT', 'Logged out manually.', '113.211.209.224', '2022-08-09 07:48:58'),
(373, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '113.211.209.224', '2022-08-09 07:49:07'),
(374, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '113.211.209.224', '2022-08-09 07:49:07'),
(375, 2, 'users', 2, 'LOGOUT', 'Logged out manually.', '39.34.129.240', '2022-08-09 08:56:54'),
(376, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '39.34.129.240', '2022-08-09 08:56:59'),
(377, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '39.34.129.240', '2022-08-09 08:56:59'),
(378, 2, 'users', 58, 'USER_CREATED', '{\"username\":\"gfgdfgd\",\"user_code\":\"FGFDG\",\"full_name\":\"dgfdg\",\"email\":\"fgdgfd@ss.vv\",\"designation\":\"1\",\"user_group\":\"1\",\"address_line_1\":\"\",\"address_line_2\":\"\",\"address_zip\":\"\",\"address_city\":\"\",\"address_state\":\"\",\"address_country\":\"MY\",\"phone\":\"\",\"company_name\":\"\",\"company_id\":\"0\"}', '39.34.129.240', '2022-08-09 09:25:02'),
(379, 2, 'users', 59, 'USER_CREATED', '{\"username\":\"fgfdgd\",\"user_code\":\"GFDGF\",\"full_name\":\"gfgfdgfd\",\"email\":\"gfggd@dd.vc\",\"designation\":\"1\",\"user_group\":\"1\",\"address_line_1\":\"\",\"address_line_2\":\"\",\"address_zip\":\"\",\"address_city\":\"\",\"address_state\":\"\",\"address_country\":\"MY\",\"phone\":\"\",\"company_name\":\"\",\"company_id\":\"\"}', '39.34.129.240', '2022-08-09 09:27:04'),
(380, 2, 'users', 59, 'ITEM_DISABLED', '', '39.34.129.240', '2022-08-09 09:27:10'),
(381, 2, 'users', 59, 'ITEM_ACTIVE', '', '39.34.129.240', '2022-08-09 09:27:12'),
(382, 2, 'users', 2, 'LOGOUT', 'Logged out manually.', '39.34.129.240', '2022-08-09 09:30:19'),
(383, 60, 'users', 60, 'LOGIN_ATTEMPT', 'Login attempted', '39.34.129.240', '2022-08-09 09:30:24'),
(384, 60, 'users', 60, 'LOGIN_SUCCESS', 'Login successful', '39.34.129.240', '2022-08-09 09:30:24'),
(385, 60, 'users', 60, 'LOGOUT', 'Logged out manually.', '39.34.129.240', '2022-08-09 09:30:24'),
(386, 60, 'users', 60, 'LOGIN_ATTEMPT', 'Login attempted', '39.34.129.240', '2022-08-09 09:30:32'),
(387, 60, 'users', 60, 'LOGIN_SUCCESS', 'Login successful', '39.34.129.240', '2022-08-09 09:30:32'),
(388, 60, 'users', 60, 'LOGOUT', 'Logged out manually.', '39.34.129.240', '2022-08-09 09:30:33'),
(389, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '39.34.129.240', '2022-08-09 09:31:23'),
(390, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '39.34.129.240', '2022-08-09 09:31:23'),
(391, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '39.34.129.240', '2022-08-09 09:34:05'),
(392, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '39.34.129.240', '2022-08-09 09:34:06'),
(393, 2, 'users', 2, 'LOGOUT', 'Logged out manually.', '39.34.129.240', '2022-08-09 09:34:12'),
(394, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '113.211.209.224', '2022-08-09 11:25:43'),
(395, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '113.211.209.224', '2022-08-09 11:25:43'),
(396, 2, 'workers', 1, 'PHOTO_UPLOADED', 'A new photo was uploaded.', '113.211.209.224', '2022-08-09 11:26:33'),
(397, 2, 'workers', 2, 'PHOTO_UPLOADED', 'A new photo was uploaded.', '113.211.209.224', '2022-08-09 11:26:51'),
(398, 2, 'workers', 3, 'PHOTO_UPLOADED', 'A new photo was uploaded.', '113.211.209.224', '2022-08-09 11:27:06'),
(399, 2, 'workers', 4, 'PHOTO_UPLOADED', 'A new photo was uploaded.', '113.211.209.224', '2022-08-09 11:27:18'),
(400, 2, 'workers', 4, 'GROUPS_UPDATED', '{\"groups\":[\"1\",\"6\",\"2\",\"4\",\"3\",\"5\"],\"id\":\"4\"}', '113.211.209.224', '2022-08-09 11:27:29'),
(401, 2, 'workers', 4, 'DRIVER_UPDATED', '{\"name\":\"Denno bin Nazmi\",\"ic_number\":\"811012107600\",\"type\":\"contract-monthly\",\"resource_type\":[\"3\"],\"contact_number\":\"0128119898\",\"ext_work_hours\":\"8.00\",\"age\":\"41\",\"ext_address\":\"\",\"id\":\"4\"}', '113.211.209.224', '2022-08-09 11:27:42'),
(402, 2, 'workers', 4, 'DRIVER_UPDATED', '{\"name\":\"JOAN\",\"ic_number\":\"811012107600\",\"type\":\"contract-monthly\",\"resource_type\":[\"3\"],\"contact_number\":\"01141083844\",\"ext_work_hours\":\"8.00\",\"age\":\"41\",\"ext_address\":\"11 LORONG MERLIMAU\\r\\nTELUK PULAI, KLANG , SELANGOR, MALAYSIA\",\"id\":\"4\"}', '113.211.209.224', '2022-08-09 11:28:13'),
(403, 2, 'workers', 4, 'DRIVER_UPDATED', '{\"name\":\"SALIM BIN MAHMUD\",\"ic_number\":\"811012107600\",\"type\":\"contract-monthly\",\"resource_type\":[\"3\"],\"contact_number\":\"01141083844\",\"ext_work_hours\":\"8.00\",\"age\":\"41\",\"ext_address\":\"11 LORONG MERLIMAU\\r\\nTELUK PULAI, KLANG , SELANGOR, MALAYSIA\",\"id\":\"4\"}', '113.211.209.224', '2022-08-09 11:28:24'),
(404, 2, 'workers', 4, 'ATTENDANCE_ADDED', '{\"work_start_end\":\"09-Aug 07:30 AM - 04:30 PM\",\"selectedDate\":\"2022-08-09\",\"work_start\":\"2022-08-09 07:30:00\",\"work_end\":\"2022-08-09 16:30:00\",\"worker_attendance\":\"P\",\"remarks\":\"\",\"id\":\"4\"}', '113.211.209.224', '2022-08-09 11:28:36'),
(405, 2, 'workers', 4, 'ATTENDANCE_ADDED', '{\"work_start_end\":\"08-Aug 07:30 AM - 04:30 PM\",\"selectedDate\":\"2022-08-08\",\"work_start\":\"2022-08-08 07:30:00\",\"work_end\":\"2022-08-08 16:30:00\",\"worker_attendance\":\"P\",\"remarks\":\"\",\"id\":\"4\"}', '113.211.209.224', '2022-08-09 11:28:47'),
(406, 2, 'workers', 4, 'ATTENDANCE_ADDED', '{\"work_start_end\":\"07-Aug 07:30 AM - 04:30 PM\",\"selectedDate\":\"2022-08-07\",\"work_start\":\"2022-08-07 07:30:00\",\"work_end\":\"2022-08-07 16:30:00\",\"worker_attendance\":\"P\",\"remarks\":\"\",\"id\":\"4\"}', '113.211.209.224', '2022-08-09 11:28:50'),
(407, 2, 'workers', 4, 'ATTENDANCE_DELETED', '{\"work_start_end\":\"07-Aug\",\"selectedDate\":\"2022-08-07\",\"work_start\":\"\",\"work_end\":\"\",\"worker_attendance\":\"RD\",\"remarks\":\"\",\"delete_attendance\":\"1\",\"id\":\"4\"}', '113.211.209.224', '2022-08-09 11:29:09'),
(408, 2, 'workers', 4, 'DRIVER_UPDATED', '{\"name\":\"SALIM BIN MAHMUD\",\"ic_number\":\"811012107600\",\"type\":\"contract-monthly\",\"resource_type\":[\"3\"],\"contact_number\":\"01141083844\",\"ext_work_hours\":\"8.00\",\"age\":\"41\",\"ext_address\":\"11 LORONG MERLIMAU\\r\\nTELUK PULAI, KLANG , SELANGOR, MALAYSIA\",\"id\":\"4\"}', '113.211.209.224', '2022-08-09 11:29:12'),
(409, 2, 'workers', 1, 'GROUPS_UPDATED', '{\"groups\":[\"3\"],\"id\":\"1\"}', '113.211.209.224', '2022-08-09 11:31:34'),
(410, 2, 'workers', 1, 'DRIVER_UPDATED', '{\"name\":\"Kassim bin Selamat\",\"ic_number\":\"791012107112\",\"type\":\"contract-monthly\",\"resource_type\":[\"3\"],\"contact_number\":\"0172557676\",\"ext_work_hours\":\"8.00\",\"age\":\"43\",\"ext_address\":\"aaaaaa\",\"id\":\"1\"}', '113.211.209.224', '2022-08-09 11:31:36'),
(411, 2, 'workers', 2, 'GROUPS_UPDATED', '{\"groups\":[\"4\"],\"id\":\"2\"}', '113.211.209.224', '2022-08-09 11:31:46'),
(412, 2, 'workers', 2, 'DRIVER_UPDATED', '{\"name\":\"Johan bin Setia \",\"ic_number\":\"781012108119\",\"type\":\"contract-monthly\",\"resource_type\":[\"\"],\"contact_number\":\"0197665552\",\"ext_work_hours\":\"8.00\",\"age\":\"44\",\"ext_address\":\"\",\"id\":\"2\"}', '113.211.209.224', '2022-08-09 11:31:48'),
(413, 2, 'workers', 3, 'GROUPS_UPDATED', '{\"groups\":[\"4\"],\"id\":\"3\"}', '113.211.209.224', '2022-08-09 11:32:01'),
(414, 2, 'workers', 4, 'DRIVER_UPDATED', '{\"name\":\"Salim bin Mahmud\",\"ic_number\":\"811012107600\",\"type\":\"contract-monthly\",\"resource_type\":[\"3\"],\"contact_number\":\"01141083844\",\"ext_work_hours\":\"8.00\",\"age\":\"41\",\"ext_address\":\"11 LORONG MERLIMAU\\r\\nTELUK PULAI, KLANG , SELANGOR, MALAYSIA\",\"id\":\"4\"}', '113.211.209.224', '2022-08-09 11:32:27'),
(415, 2, 'workers', 4, 'GROUPS_UPDATED', '{\"groups\":[\"3\"],\"id\":\"4\"}', '113.211.209.224', '2022-08-09 11:32:35'),
(416, 2, 'equipments', 5, 'EQUIPMENT_UPDATED', '{\"name\":\"RORO_1\",\"code\":\"UER111112\",\"purchase_date\":\"01\\/08\\/2022\",\"equipment_status\":\"In use\",\"equipment_manufacturer\":\"2\",\"equipment_type\":\"11\",\"notes\":\"\",\"safe_load\":\"5MT\",\"id\":\"5\"}', '113.211.209.224', '2022-08-09 11:34:31'),
(417, 2, 'equipments', 4, 'GROUPS_UPDATED', '{\"groups\":[\"5\",\"6\"],\"id\":\"4\"}', '113.211.209.224', '2022-08-09 11:34:47'),
(418, 2, 'equipments', 4, 'EQUIPMENT_UPDATED', '{\"name\":\"RORO_4\",\"code\":\"ABC123\",\"purchase_date\":\"01\\/08\\/2022\",\"equipment_status\":\"In use\",\"equipment_manufacturer\":\"1\",\"equipment_type\":\"11\",\"notes\":\"\",\"safe_load\":\"80MT\",\"id\":\"4\"}', '113.211.209.224', '2022-08-09 11:34:51'),
(419, 2, 'equipments', 5, 'EQUIPMENT_UPDATED', '{\"name\":\"RORO_1\",\"code\":\"VBY9112\",\"purchase_date\":\"01\\/08\\/2022\",\"equipment_status\":\"In use\",\"equipment_manufacturer\":\"2\",\"equipment_type\":\"11\",\"notes\":\"\",\"safe_load\":\"5MT\",\"id\":\"5\"}', '113.211.209.224', '2022-08-09 11:35:15'),
(420, 2, 'equipments', 5, 'SCHEDULED_MAINTENANCE_ADDED', '{\"next_maintenance_date\":\"31\\/08\\/2022\",\"next_maintenance_mileage\":\"25000\",\"id\":\"5\"}', '113.211.209.224', '2022-08-09 11:35:48'),
(421, 2, 'equipments', 4, 'EQUIPMENT_UPDATED', '{\"name\":\"RORO_4\",\"code\":\"ABC123\",\"purchase_date\":\"01\\/08\\/2022\",\"equipment_status\":\"In use\",\"equipment_manufacturer\":\"1\",\"equipment_type\":\"11\",\"notes\":\"\",\"safe_load\":\"80MT\",\"id\":\"4\"}', '113.211.209.224', '2022-08-09 11:36:41'),
(422, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '39.34.135.178', '2022-08-09 12:01:24'),
(423, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '39.34.135.178', '2022-08-09 12:01:24'),
(424, 2, 'users', 2, 'LOGOUT', 'Logged out manually.', '113.211.209.224', '2022-08-09 12:05:22'),
(425, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '113.211.209.224', '2022-08-09 12:05:31'),
(426, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '113.211.209.224', '2022-08-09 12:05:31'),
(427, 2, 'equipments', 3, 'GROUPS_UPDATED', '{\"groups\":[\"1\",\"3\",\"5\"],\"id\":\"3\"}', '113.211.209.224', '2022-08-09 12:06:08'),
(428, 2, 'users', 61, 'USER_CREATED', '{\"username\":\"Suresh\",\"user_code\":\"SC\",\"full_name\":\"Suresh Chidambareswaran\",\"email\":\"suresh@bytespace.asia\",\"designation\":\"1\",\"user_group\":\"1\",\"address_line_1\":\"11 Jalan Setia Indah u13\\/12G\",\"address_line_2\":\"Setia Indah 12\",\"address_zip\":\"40170\",\"address_city\":\"Shah Alam\",\"address_state\":\"Selangor\",\"address_country\":\"MY\",\"phone\":\"0122127352\",\"company_name\":\"\",\"company_id\":\"\"}', '113.211.209.224', '2022-08-09 12:06:40'),
(429, 2, 'users', 61, 'ROLES_UPDATED', '{\"roles\":[\"1\"],\"id\":\"61\"}', '113.211.209.224', '2022-08-09 12:06:47'),
(430, 2, 'users', 61, 'PICTURE_UPLOADED', 'A new profile photo was uploaded. attachment 1660046821-Suresh.png was added.', '113.211.209.224', '2022-08-09 12:07:01'),
(431, 2, 'users', 61, 'USER_UPDATED', '{\"username\":\"Suresh\",\"user_code\":\"SC\",\"full_name\":\"Suresh Chidambareswaran\",\"email\":\"suresh@bytespace.asia\",\"designation\":\"1\",\"user_group\":\"1\",\"address_line_1\":\"11 Jalan Setia Indah u13\\/12G\",\"address_line_2\":\"Setia Indah 12\",\"address_zip\":\"40170\",\"address_city\":\"Shah Alam\",\"address_state\":\"Selangor\",\"address_country\":\"MY\",\"phone\":\"0122127352\",\"company_name\":\"\",\"company_id\":\"0\",\"id\":\"61\"}', '113.211.209.224', '2022-08-09 12:07:07'),
(432, 2, 'users', 2, 'USER_UPDATED', '{\"username\":\"admin\",\"user_code\":\"ADMIN\",\"full_name\":\"Admin\",\"email\":\"admin@bytespace.asia\",\"designation\":\"1\",\"user_group\":\"1\",\"address_line_1\":\"\",\"address_line_2\":\"\",\"address_zip\":\"\",\"address_city\":\"\",\"address_state\":\"\",\"address_country\":\"MY\",\"phone\":\"123\",\"company_name\":\"\",\"company_id\":\"\",\"mobile\":\"1\",\"id\":\"2\"}', '113.211.209.224', '2022-08-09 12:07:22'),
(433, 2, 'equipments', 2, 'EQUIPMENT_UPDATED', '{\"name\":\"MINI_1\",\"code\":\"WTW123\",\"purchase_date\":\"01\\/08\\/2022\",\"equipment_status\":\"In use\",\"equipment_manufacturer\":\"2\",\"equipment_type\":\"1\",\"notes\":\"\",\"safe_load\":\"\",\"id\":\"2\"}', '113.211.209.224', '2022-08-09 12:08:33'),
(434, 2, 'equipments', 6, 'EQUIPMENT_UPDATED', '{\"name\":\"COMPACT_2\",\"code\":\"CBB123\",\"purchase_date\":\"01\\/08\\/2022\",\"equipment_status\":\"In use\",\"equipment_manufacturer\":\"3\",\"equipment_type\":\"6\",\"notes\":\"\",\"safe_load\":\"100MT\",\"id\":\"6\"}', '113.211.209.224', '2022-08-09 12:09:27'),
(435, 2, 'equipments', 1, 'GROUPS_UPDATED', '{\"groups\":[\"5\"],\"id\":\"1\"}', '113.211.209.224', '2022-08-09 12:09:49'),
(436, 2, 'equipments', 7, 'GROUPS_UPDATED', '{\"groups\":[\"1\",\"6\"],\"id\":\"7\"}', '113.211.209.224', '2022-08-09 12:12:41'),
(437, 2, 'equipments', 7, 'EQUIPMENT_UPDATED', '{\"name\":\"UW_1\",\"code\":\"AAY993\",\"purchase_date\":\"02\\/08\\/2022\",\"equipment_status\":\"Maintenance\",\"equipment_manufacturer\":\"5\",\"equipment_type\":\"7\",\"notes\":\"aa\",\"safe_load\":\"aa\",\"id\":\"7\"}', '113.211.209.224', '2022-08-09 12:13:04'),
(438, 2, 'users', 2, 'LOGOUT', 'Logged out manually.', '39.34.135.178', '2022-08-09 12:13:17'),
(439, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '39.34.135.178', '2022-08-09 12:13:19'),
(440, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '39.34.135.178', '2022-08-09 12:13:19'),
(441, 2, 'users', 2, 'LOGOUT', 'Logged out manually.', '113.211.209.224', '2022-08-09 12:13:42'),
(442, 61, 'users', 61, 'LOGIN_ATTEMPT', 'Login attempted', '113.211.209.224', '2022-08-09 12:13:52'),
(443, 61, 'users', 61, 'LOGIN_SUCCESS', 'Login successful', '113.211.209.224', '2022-08-09 12:13:52'),
(444, 61, 'users', 61, 'COLOR_CHANGED', '[]', '113.211.209.224', '2022-08-09 12:14:01'),
(445, 61, 'users', 61, 'FONT_CHANGED', '{\"font\":\"Amaranth\"}', '113.211.209.224', '2022-08-09 12:14:05'),
(446, 61, 'equipments', 8, 'EQUIPMENT_UPDATED', '{\"name\":\"UW_2\",\"code\":\"JDT112\",\"purchase_date\":\"01\\/08\\/2022\",\"equipment_status\":\"In use\",\"equipment_manufacturer\":\"5\",\"equipment_type\":\"7\",\"notes\":\"\",\"safe_load\":\"\",\"id\":\"8\"}', '113.211.209.224', '2022-08-09 12:14:50'),
(447, 61, 'equipments', 8, 'GROUPS_UPDATED', '{\"groups\":[\"6\"],\"id\":\"8\"}', '113.211.209.224', '2022-08-09 12:14:57'),
(448, 61, '', 8, 'TRUCK_PHOTO_UPLOADED', 'A new photo was uploaded.', '113.211.209.224', '2022-08-09 12:15:06'),
(449, 61, 'roles', 1, 'PERMISSIONS_UPDATED', '{\"permissions\":[\"9\",\"10\",\"13\",\"14\",\"16\",\"42\",\"43\",\"44\",\"51\",\"52\",\"53\",\"58\",\"78\",\"108\",\"137\",\"138\",\"156\",\"220\",\"221\",\"234\",\"235\",\"236\",\"237\",\"238\",\"239\",\"240\",\"241\",\"242\",\"243\",\"244\",\"245\",\"246\",\"247\",\"248\",\"249\",\"251\",\"252\",\"253\",\"254\",\"255\",\"256\",\"257\",\"258\",\"262\",\"263\",\"264\",\"265\",\"266\",\"267\",\"268\",\"269\",\"270\",\"27\",\"37\",\"38\",\"61\",\"62\",\"63\",\"82\",\"142\",\"146\",\"150\",\"166\",\"170\",\"176\",\"179\",\"1\",\"3\",\"5\",\"7\",\"8\",\"11\",\"12\",\"15\",\"17\",\"36\",\"45\",\"46\",\"136\",\"2\",\"4\",\"6\",\"33\",\"55\",\"56\",\"57\",\"133\",\"134\",\"135\",\"181\",\"182\",\"183\",\"186\",\"187\",\"188\",\"189\",\"190\",\"191\",\"215\",\"216\",\"217\",\"19\",\"20\",\"25\",\"26\",\"35\",\"39\",\"40\",\"69\",\"70\",\"71\",\"81\",\"109\",\"132\",\"147\",\"169\",\"21\",\"23\",\"24\",\"116\",\"117\",\"50\",\"60\",\"141\",\"144\",\"145\",\"167\",\"192\",\"193\",\"194\",\"195\",\"196\",\"207\",\"208\",\"209\",\"210\",\"212\",\"213\",\"214\",\"222\",\"30\",\"31\",\"32\",\"47\",\"48\",\"49\",\"59\",\"74\",\"85\",\"34\",\"139\",\"143\",\"149\",\"151\",\"152\",\"153\",\"157\",\"165\",\"177\",\"184\",\"185\",\"223\",\"224\",\"225\",\"22\",\"54\",\"79\",\"80\",\"101\",\"110\",\"111\",\"112\",\"119\",\"124\",\"154\",\"155\",\"158\",\"159\",\"161\",\"162\",\"164\",\"171\",\"172\",\"173\",\"174\",\"175\",\"206\",\"180\",\"163\",\"178\",\"199\",\"200\",\"201\",\"202\",\"230\",\"233\",\"203\",\"204\",\"205\",\"211\",\"218\",\"219\",\"227\",\"228\",\"229\",\"231\"],\"id\":\"1\"}', '113.211.209.224', '2022-08-09 12:15:51'),
(450, 61, '', 1, 'ASSET_PHOTO_UPLOADED', 'A new photo was uploaded.', '113.211.209.224', '2022-08-09 12:18:35'),
(451, 61, 'equipments', 0, 'ASSET_USAGE_ADDED', '{\"vh_date\":\"2022-08-02\",\"vh_time_start\":\"09:00\",\"vh_time_end\":\"19:00\",\"vh_location_start\":\"Melaka Operation Center\",\"vh_location_end\":\"Kellogs (M)\",\"driver_id\":\"3|771029108112\",\"id\":\"8\"}', '113.211.209.224', '2022-08-09 14:23:02'),
(452, 61, 'equipments', 0, 'ASSET_USAGE_ADDED', '{\"vh_date\":\"2022-08-03\",\"vh_time_start\":\"09:00\",\"vh_time_end\":\"21:00\",\"vh_location_start\":\"North Johor Operation Office\",\"vh_location_end\":\"Bintang 3 Sdn Bhd\",\"driver_id\":\"2|781012108119\",\"id\":\"1\"}', '113.211.209.224', '2022-08-09 14:25:14'),
(453, 61, 'equipments', 7, 'EQUIPMENT_UPDATED', '{\"name\":\"UW_1\",\"code\":\"AAY993\",\"purchase_date\":\"02\\/08\\/2022\",\"equipment_status\":\"In use\",\"equipment_manufacturer\":\"5\",\"equipment_type\":\"7\",\"notes\":\"aa\",\"safe_load\":\"aa\",\"id\":\"7\"}', '113.211.209.224', '2022-08-09 14:27:13'),
(454, 61, 'equipments', 7, 'ITEM_ACTIVE', '', '113.211.209.224', '2022-08-09 14:27:20'),
(455, 61, 'workers', 2, 'ATTENDANCE_ADDED', '{\"work_start_end\":\"08-Aug 07:30 AM - 04:30 PM\",\"selectedDate\":\"2022-08-08\",\"work_start\":\"2022-08-08 07:30:00\",\"work_end\":\"2022-08-08 16:30:00\",\"worker_attendance\":\"P\",\"remarks\":\"\",\"id\":\"2\"}', '113.211.209.224', '2022-08-09 14:29:53'),
(456, 61, 'workers', 2, 'ATTENDANCE_ADDED', '{\"work_start_end\":\"09-Aug 07:30 AM - 04:30 PM\",\"selectedDate\":\"2022-08-09\",\"work_start\":\"2022-08-09 07:30:00\",\"work_end\":\"2022-08-09 16:30:00\",\"worker_attendance\":\"P\",\"remarks\":\"\",\"id\":\"2\"}', '113.211.209.224', '2022-08-09 14:29:56'),
(457, 61, 'workers', 3, 'ATTENDANCE_ADDED', '{\"work_start_end\":\"08-Aug 07:30 AM - 04:30 PM\",\"selectedDate\":\"2022-08-08\",\"work_start\":\"2022-08-08 07:30:00\",\"work_end\":\"2022-08-08 16:30:00\",\"worker_attendance\":\"P\",\"remarks\":\"\",\"id\":\"3\"}', '113.211.209.224', '2022-08-09 14:30:20'),
(458, 61, 'workers', 3, 'ATTENDANCE_ADDED', '{\"work_start_end\":\"09-Aug 07:30 AM - 04:30 PM\",\"selectedDate\":\"2022-08-09\",\"work_start\":\"2022-08-09 07:30:00\",\"work_end\":\"2022-08-09 16:30:00\",\"worker_attendance\":\"P\",\"remarks\":\"\",\"id\":\"3\"}', '113.211.209.224', '2022-08-09 14:30:22'),
(459, 61, 'workers', 3, 'DRIVER_UPDATED', '{\"name\":\"Ali bin Ahmad\",\"ic_number\":\"771029108112\",\"type\":\"contract-monthly\",\"resource_type\":[\"3\"],\"contact_number\":\"0197757522\",\"ext_work_hours\":\"8.00\",\"age\":\"45\",\"ext_address\":\"\",\"id\":\"3\"}', '113.211.209.224', '2022-08-09 14:30:35'),
(460, 61, 'workers', 2, 'DRIVER_UPDATED', '{\"name\":\"Johan bin Setia \",\"ic_number\":\"781012108119\",\"type\":\"contract-monthly\",\"resource_type\":[\"1\"],\"contact_number\":\"0197665552\",\"ext_work_hours\":\"8.00\",\"age\":\"44\",\"ext_address\":\"\",\"id\":\"2\"}', '113.211.209.224', '2022-08-09 14:32:59'),
(461, 61, 'workers', 2, 'DRIVER_UPDATED', '{\"name\":\"Johan bin Setia \",\"ic_number\":\"781012108119\",\"type\":\"contract-monthly\",\"resource_type\":[\"1\"],\"contact_number\":\"0197665552\",\"ext_work_hours\":\"8.00\",\"age\":\"44\",\"ext_address\":\"\",\"id\":\"2\"}', '113.211.209.224', '2022-08-09 14:33:02'),
(462, 61, 'workers', 1, 'GROUPS_UPDATED', '{\"groups\":[\"6\",\"3\",\"4\"],\"id\":\"1\"}', '113.211.209.224', '2022-08-09 14:34:22'),
(463, 61, 'users', 61, 'LOGOUT', 'Logged out manually.', '113.211.209.224', '2022-08-09 14:35:22'),
(464, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '39.34.138.134', '2022-08-09 19:03:19'),
(465, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '39.34.138.134', '2022-08-09 19:03:19'),
(466, 2, 'equipments', 0, 'ASSET_USAGE_ADDED', '{\"vh_date\":\"2022-08-12\",\"vh_date_end\":\"2022-08-16\",\"vh_time_start\":\"04:06\",\"vh_time_end\":\"05:10\",\"vh_location_start\":\"htrhtrh\",\"vh_location_end\":\"htrhtrh\",\"driver_id\":\"1|791012107112\",\"id\":\"8\"}', '39.34.138.134', '2022-08-09 19:06:16'),
(467, 61, 'users', 61, 'LOGIN_ATTEMPT', 'Login attempted', '113.211.208.220', '2022-08-09 23:16:00'),
(468, 61, 'users', 61, 'LOGIN_SUCCESS', 'Login successful', '113.211.208.220', '2022-08-09 23:16:01'),
(469, 61, 'workers', 1, 'DRIVER_UPDATED', '{\"name\":\"Kassim bin Selamat\",\"ic_number\":\"791012107112\",\"resource_type_primary\":[\"3\"],\"resource_type\":[\"4\"],\"type\":\"contract-monthly\",\"contact_number\":\"0172557676\",\"ext_work_hours\":\"8.00\",\"age\":\"43\",\"ext_address\":\"aaaaaa\",\"id\":\"1\"}', '113.211.208.220', '2022-08-09 23:16:39'),
(470, 61, 'workers', 1, 'ATTENDANCE_ADDED', '{\"work_start_end\":\"09-Aug 07:30 AM - 04:30 PM\",\"selectedDate\":\"2022-08-09\",\"work_start\":\"2022-08-09 07:30:00\",\"work_end\":\"2022-08-09 16:30:00\",\"worker_attendance\":\"P\",\"remarks\":\"\",\"id\":\"1\"}', '113.211.208.220', '2022-08-09 23:16:43'),
(471, 61, 'workers', 2, 'DRIVER_UPDATED', '{\"name\":\"Johan bin Setia \",\"ic_number\":\"781012108119\",\"resource_type_primary\":[\"1\"],\"resource_type\":[\"3\"],\"type\":\"contract-monthly\",\"contact_number\":\"0197665552\",\"ext_work_hours\":\"8.00\",\"age\":\"44\",\"ext_address\":\"\",\"id\":\"2\"}', '113.211.208.220', '2022-08-09 23:17:26'),
(472, 61, 'workers', 2, 'DRIVER_UPDATED', '{\"name\":\"Johan bin Setia \",\"ic_number\":\"781012108119\",\"resource_type_primary\":[\"1\"],\"resource_type\":[\"3\"],\"type\":\"contract-monthly\",\"contact_number\":\"0197665552\",\"ext_work_hours\":\"8.00\",\"age\":\"44\",\"ext_address\":\"\",\"id\":\"2\"}', '113.211.208.220', '2022-08-09 23:17:27'),
(473, 61, 'workers', 4, 'GROUPS_UPDATED', '{\"groups\":[\"5\",\"3\",\"7\"],\"id\":\"4\"}', '113.211.208.220', '2022-08-09 23:18:00'),
(474, 61, 'equipments', 0, 'ASSET_USAGE_ADDED', '{\"vh_date\":\"2022-08-05\",\"vh_date_end\":\"2022-08-09\",\"vh_time_start\":\"09:00\",\"vh_time_end\":\"16:00\",\"vh_location_start\":\"Negeri Sembilan Operation Office\",\"vh_location_end\":\"Dutch Lady (M)\",\"driver_id\":\"2|781012108119\",\"id\":\"8\"}', '113.211.208.220', '2022-08-09 23:21:17'),
(475, 61, 'users', 61, 'LOGOUT', 'Logged out manually.', '113.211.208.220', '2022-08-09 23:22:01'),
(476, 2, 'users', 2, 'LOGIN_ATTEMPT', 'Login attempted', '113.211.118.232', '2022-08-10 03:11:40'),
(477, 2, 'users', 2, 'LOGIN_SUCCESS', 'Login successful', '113.211.118.232', '2022-08-10 03:11:40');

-- --------------------------------------------------------

--
-- Table structure for table `manufacturers`
--

CREATE TABLE `manufacturers` (
  `manufacturer_id` int(10) UNSIGNED NOT NULL,
  `manufacturer_name` varchar(200) DEFAULT NULL,
  `manufacturer_notes` text,
  `active` int(1) NOT NULL DEFAULT '1',
  `t_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `manufacturers`
--

INSERT INTO `manufacturers` (`manufacturer_id`, `manufacturer_name`, `manufacturer_notes`, `active`, `t_updated`) VALUES
(1, 'Hino', '', 1, '2022-08-04 22:21:09'),
(2, 'Toyota', '', 1, '2022-08-04 22:21:18'),
(3, 'Nissan', '', 1, '2022-08-04 22:21:26'),
(4, 'Kia', '', 1, '2022-08-04 22:21:36'),
(5, 'Mercedes', '', 1, '2022-08-04 22:21:48'),
(6, 'Caterpillar', '', 1, '2022-08-04 22:22:31'),
(7, 'Volvo', '', 1, '2022-08-04 22:22:43'),
(8, 'MineBiz', '', 1, '2022-08-04 23:04:10'),
(9, 'PerStorp', '', 1, '2022-08-04 23:04:50');

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
  `active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `masters_companies`
--

INSERT INTO `masters_companies` (`company_id`, `registration_id`, `company_name`, `contact_person`, `contact_email`, `business_type`, `active`) VALUES
(1, '1', 'aa', 'aaa', 'aa@aa.bb', 'abc', 1);

-- --------------------------------------------------------

--
-- Table structure for table `message_views`
--

CREATE TABLE `message_views` (
  `table_name` varchar(50) NOT NULL,
  `record_id` int(10) NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `message_timestamp` datetime NOT NULL,
  `t_viewed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `notices_of_arrival`
--

CREATE TABLE `notices_of_arrival` (
  `notices_of_arrival_id` int(10) UNSIGNED NOT NULL,
  `booking_id` int(10) UNSIGNED DEFAULT NULL,
  `noa_web_pdf` varchar(200) DEFAULT NULL,
  `noa_print_pdf` varchar(200) DEFAULT NULL,
  `noa_status` varchar(29) DEFAULT NULL,
  `display_dd` int(1) NOT NULL DEFAULT '0',
  `display_charges` int(1) NOT NULL DEFAULT '0',
  `noa_d_created` date DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `notices_of_arrival_remarks`
--

CREATE TABLE `notices_of_arrival_remarks` (
  `notices_of_arrival_remark_id` int(10) NOT NULL,
  `notices_of_arrival_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `remark` text NOT NULL,
  `t_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `operation_types`
--

CREATE TABLE `operation_types` (
  `operation_type_id` int(11) NOT NULL,
  `operation_type_name` varchar(100) DEFAULT NULL,
  `no_cargo` int(1) NOT NULL DEFAULT '0',
  `no_commodity` int(1) NOT NULL DEFAULT '0',
  `no_stowage` int(1) NOT NULL DEFAULT '0',
  `description` text,
  `active` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `operators`
--

CREATE TABLE `operators` (
  `operator_id` int(10) NOT NULL,
  `operator_code` varchar(10) NOT NULL,
  `operator_name` varchar(100) DEFAULT NULL,
  `active` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `perm_id` int(10) UNSIGNED NOT NULL,
  `perm_name` varchar(50) NOT NULL,
  `perm_cat_id` int(5) UNSIGNED NOT NULL,
  `system` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`perm_id`, `perm_name`, `perm_cat_id`, `system`) VALUES
(1, 'list_users', 3, 1),
(2, 'list_permissions', 4, 1),
(3, 'list_user_groups', 3, 1),
(4, 'list_masters', 4, 1),
(5, 'list_user_roles', 3, 1),
(6, 'list_designations', 4, 1),
(7, 'assign_user_roles', 3, 1),
(8, 'edit_users', 3, 1),
(9, 'list_admin', 1, 1),
(10, 'edit_designations', 1, 1),
(11, 'edit_user_roles', 3, 1),
(12, 'assign_users', 3, 1),
(13, 'edit_permissions', 1, 1),
(14, 'assign_permissions', 1, 1),
(15, 'edit_user_groups', 3, 1),
(16, 'add_designations', 1, 1),
(17, 'add_permissions', 3, 1),
(18, 'add_ports', 5, 1),
(19, 'list_ports', 5, 1),
(20, 'edit_ports', 5, 1),
(21, 'list_companies', 6, 1),
(22, 'list_equipments', 10, 1),
(23, 'edit_companies', 6, 1),
(24, 'add_companies', 6, 1),
(25, 'delete_vessel_cranes', 5, 1),
(26, 'add_vessel_cranes', 5, 1),
(27, 'warehouse_resources_planning', 2, 1),
(30, 'add_commodities', 8, 1),
(31, 'edit_commodities', 8, 1),
(32, 'list_commodities', 8, 1),
(33, 'add_dg_classes', 4, 1),
(34, 'add_worker_tenure', 9, 1),
(35, 'add_vessel_hatches', 5, 1),
(36, 'add_user_groups', 3, 1),
(37, 'edit_approved_resource_planner', 2, 1),
(38, 'approve_resource_planner', 2, 1),
(39, 'edit_vessels', 5, 1),
(40, 'list_vessels', 5, 1),
(41, 'add_vessels', 5, 1),
(42, 'add_scheduled_tasks', 1, 1),
(43, 'list_scheduled_tasks', 1, 1),
(44, 'edit_scheduled_tasks', 1, 1),
(45, 'add_users', 3, 1),
(46, 'add_user_roles', 3, 1),
(47, 'list_cargo_types', 8, 1),
(48, 'add_cargo_types', 8, 1),
(49, 'edit_cargo_types', 8, 1),
(50, 'list_operation_types', 7, 1),
(51, 'add_countries', 1, 1),
(52, 'edit_countries', 1, 1),
(53, 'list_countries', 1, 1),
(54, 'add_equipment_groups', 10, 1),
(55, 'list_merchants', 4, 1),
(56, 'add_merchants', 4, 1),
(57, 'edit_merchants', 4, 1),
(58, 'edit_all_merchants', 1, 1),
(59, 'edit_cargo_packagings', 8, 1),
(60, 'add_operation_types', 7, 1),
(61, 'add_vessel_service_requests', 2, 1),
(62, 'list_service_requests', 2, 1),
(63, 'edit_warehouse_service_requests', 2, 1),
(69, 'edit_vessel_visits', 5, 1),
(70, 'add_vessel_visits', 5, 1),
(71, 'list_vessel_visits', 5, 1),
(74, 'add_cargo_packagings', 8, 1),
(78, 'view_logs', 1, 1),
(79, 'edit_equipments', 10, 1),
(80, 'list_equipment_groups', 10, 1),
(81, 'add_depots', 5, 1),
(82, 'add_warehouse_service_requests', 2, 1),
(85, 'list_cargo_packagings', 8, 1),
(101, 'add_equipments', 10, 1),
(108, 'add_service_request_remarks', 1, 1),
(109, 'delete_vessel_hatches', 5, 1),
(110, 'add_equipment_consumption', 10, 1),
(111, 'edit_consumables', 10, 1),
(112, 'add_consumables', 10, 1),
(116, 'update_company_addresses', 6, 1),
(117, 'add_company_addresses', 6, 1),
(119, 'edit_equipment_groups', 10, 1),
(124, 'list_consumables', 10, 1),
(132, 'vessel_resources_planning', 5, 1),
(133, 'add_manufacturers', 4, 1),
(134, 'edit_manufacturers', 4, 1),
(135, 'list_manufacturers', 4, 1),
(136, 'user_permissions_override', 3, 1),
(137, 'import_payment_update', 1, 1),
(138, 'export_payment_update', 1, 1),
(139, 'assign_worker_groups', 9, 1),
(141, 'edit_operation_types', 7, 1),
(142, 'display_export_costs', 2, 1),
(143, 'edit_workers', 9, 1),
(144, 'edit_resource_types', 7, 1),
(145, 'add_resource_types', 7, 1),
(146, 'edit_vessel_service_requests', 2, 1),
(147, 'add_port_wharf', 5, 1),
(149, 'edit_worker_groups', 9, 1),
(150, 'approve_service_requests', 2, 1),
(151, 'list_worker_group_allocation', 9, 1),
(152, 'list_worker_groups', 9, 1),
(153, 'add_worker_groups', 9, 1),
(154, 'view_equipment_groups', 10, 1),
(155, 'edit_gear_types', 10, 1),
(156, 'edit_merchant_names', 1, 1),
(157, 'add_workers', 9, 1),
(158, 'list_gear_types', 10, 1),
(159, 'add_equipment_types', 10, 1),
(161, 'add_gear_types', 10, 1),
(162, 'list_equipment_types', 10, 1),
(163, 'list_worker_attendance', 12, 1),
(164, 'edit_equipment_types', 10, 1),
(165, 'list_workers', 9, 1),
(166, 'cancel_service_requests', 2, 1),
(167, 'list_resource_types', 7, 1),
(169, 'edit_port_wharfs', 5, 1),
(170, 'view_service_requests', 2, 1),
(171, 'assign_equipment_groups', 10, 1),
(172, 'add_gears', 10, 1),
(173, 'list_gears', 10, 1),
(174, 'edit_gears', 10, 1),
(175, 'add_gear_purchase', 10, 1),
(176, 'add_ssr_documents', 2, 1),
(177, 'edit_worker_group_allocation', 9, 1),
(178, 'update_worker_attendance', 12, 1),
(179, 'edit_approved_service_requests', 2, 1),
(180, 'add_mobile_biometrics', 11, 1),
(181, 'add_delay_reasons', 4, 1),
(182, 'edit_delay_reasons', 4, 1),
(183, 'list_delay_reasons', 4, 1),
(184, 'edit_worker_biometrics', 9, 0),
(185, 'delete_worker_tenure', 9, 0),
(186, 'add_rebundling_colours', 4, 0),
(187, 'edit_rebundling_colours', 4, 0),
(188, 'list_rebundling_colours', 4, 0),
(189, 'list_wastage_types', 4, 0),
(190, 'edit_wastage_types', 4, 0),
(191, 'add_wastage_types', 4, 0),
(192, 'view_vessel_performance', 7, 0),
(193, 'add_operation_tally', 7, 0),
(194, 'add_operation_rebundling', 7, 0),
(195, 'add_operation_dropping', 7, 0),
(196, 'add_operation_delay', 7, 0),
(199, 'download_payroll', 12, 0),
(200, 'pre_approve_overtime', 12, 0),
(201, 'approve_overtime', 12, 0),
(202, 'delete_overtime', 12, 0),
(203, 'list_finance_documents', 13, 0),
(204, 'list_service_vouchers', 13, 0),
(205, 'list_payroll_list', 13, 0),
(206, 'add_consumable_purchase', 10, 0),
(207, 'delete_tally_entry', 7, 0),
(208, 'delete_delay_entry', 7, 0),
(209, 'modify_stowage_plan', 7, 0),
(210, 'view_stowage_plan', 7, 0),
(211, 'set_company_prices', 13, 0),
(212, 'view_cost_estimation', 7, 0),
(213, 'manual_resource_plan_worker_group', 7, 0),
(214, 'add_disposal_activity', 7, 0),
(215, 'add_tally_remarks', 4, 0),
(216, 'list_tally_remarks', 4, 0),
(217, 'edit_tally_remarks', 4, 0),
(218, 'list_invoices', 13, 0),
(219, 'generate_invoices', 13, 0),
(220, 'delete_service_requests', 1, 0),
(221, 'delete_vessel_visits', 1, 0),
(222, 'delete_disposal_activity', 7, 0),
(223, 'list_worker_locations', 9, 0),
(224, 'add_worker_locations', 9, 0),
(225, 'edit_worker_locations', 9, 0),
(226, 'attendance_issues_automated_email', 12, 0),
(227, 'set_worker_rate_override', 13, 0),
(228, 'set_resource_type_rate', 13, 0),
(229, 'generate_service_vouchers', 13, 0),
(230, 'edit_public_holidays', 12, 0),
(231, 'delete_service_vouchers', 13, 0),
(233, 'delete_public_holidays', 12, 0),
(234, 'delete_service_request_operation', 1, 0),
(235, 'add_masters_companies', 1, 0),
(236, 'delete_masters_companies', 1, 0),
(237, 'edit_masters_companies', 1, 0),
(238, 'list_masters_companies', 1, 0),
(239, 'edit_approve_incident_request', 1, 0),
(240, 'delete_incident_requests', 1, 0),
(241, 'cancel_incident_requests', 1, 0),
(242, 'approve_incident_requests', 1, 0),
(243, 'add_incident_documents', 1, 0),
(244, 'list_incidents_request', 1, 0),
(245, 'edit_incidents_request', 1, 0),
(246, 'add_incidents_request', 1, 0),
(247, 'add_incident_types', 1, 0),
(248, 'edit_incident_types', 1, 0),
(249, 'list_incident_types', 1, 0),
(251, 'edit_billing_resource', 1, 0),
(252, 'add_billing_resource', 1, 0),
(253, 'list_billing_resource', 1, 0),
(254, 'delete_billing_resource', 1, 0),
(255, 'submit_billdetails', 1, 0),
(256, 'manager_approve', 1, 0),
(257, 'finance_approve', 1, 0),
(258, 'delete_operation_bill', 1, 0),
(262, 'qr_generator', 1, 0),
(263, 'add_history', 1, 0),
(264, 'list_drivers', 1, 0),
(265, 'list_truck', 1, 0),
(266, 'list_asset', 1, 0),
(267, 'list_truck_groups', 1, 0),
(268, 'truck_groups', 1, 0),
(269, 'list_assets', 1, 0),
(270, 'list_asset_groups', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `permission_categories`
--

CREATE TABLE `permission_categories` (
  `perm_cat_id` int(5) UNSIGNED NOT NULL,
  `perm_cat_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `permission_categories`
--

INSERT INTO `permission_categories` (`perm_cat_id`, `perm_cat_name`) VALUES
(1, 'Admin'),
(2, 'Service requests'),
(3, 'Users'),
(4, 'Masters'),
(5, 'Ports & vessels'),
(6, 'Companies'),
(7, 'Operations'),
(8, 'Cargo & commodities'),
(9, 'Workers'),
(10, 'Equipments, consumables & gears'),
(11, 'Mobile'),
(12, 'Attendance / payroll'),
(13, 'Finance');

-- --------------------------------------------------------

--
-- Table structure for table `ports`
--

CREATE TABLE `ports` (
  `port_id` int(10) UNSIGNED NOT NULL,
  `country_code` char(2) DEFAULT NULL,
  `port_code` varchar(3) NOT NULL,
  `port_name` varchar(255) NOT NULL,
  `function` varchar(10) DEFAULT NULL,
  `sea` int(1) NOT NULL DEFAULT '0',
  `rail` int(1) NOT NULL DEFAULT '0',
  `road` int(1) NOT NULL DEFAULT '0',
  `air` int(1) NOT NULL DEFAULT '0',
  `coordinates` varchar(15) DEFAULT NULL,
  `ref_international` varchar(60) NOT NULL,
  `ref_domestic` varchar(60) NOT NULL,
  `port_timezone` varchar(100) DEFAULT NULL,
  `detention` int(1) NOT NULL DEFAULT '0',
  `demurrage` int(1) NOT NULL DEFAULT '0',
  `port_currency` varchar(3) DEFAULT NULL,
  `yard_close_hrs` int(3) DEFAULT NULL,
  `yard_open_hrs` int(3) DEFAULT NULL,
  `active` int(1) NOT NULL DEFAULT '1',
  `starred` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `port_wharfs`
--

CREATE TABLE `port_wharfs` (
  `port_wharf_id` int(5) UNSIGNED NOT NULL,
  `port_id` int(10) UNSIGNED DEFAULT NULL,
  `wharf_id` varchar(10) DEFAULT NULL,
  `wharf_name` varchar(200) DEFAULT NULL,
  `active` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `public_holidays`
--

CREATE TABLE `public_holidays` (
  `public_holiday_id` int(11) NOT NULL,
  `public_holiday_name` varchar(200) NOT NULL,
  `public_holiday_date` date NOT NULL,
  `active` int(1) NOT NULL DEFAULT '1',
  `t_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `public_holidays`
--

INSERT INTO `public_holidays` (`public_holiday_id`, `public_holiday_name`, `public_holiday_date`, `active`, `t_updated`) VALUES
(1, 'aaa', '2022-08-08', 1, '2022-08-08 08:58:48'),
(2, 'baaaas', '2022-08-09', 1, '2022-08-08 08:59:00');

-- --------------------------------------------------------

--
-- Table structure for table `rebundling_colours`
--

CREATE TABLE `rebundling_colours` (
  `rebundling_colour_id` int(11) NOT NULL,
  `rebundling_colour_name` varchar(100) DEFAULT NULL,
  `description` text,
  `active` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `resource_types`
--

CREATE TABLE `resource_types` (
  `resource_type_id` int(11) NOT NULL,
  `resource_type_name` varchar(100) DEFAULT NULL,
  `resource_type_short_code` varchar(6) DEFAULT NULL,
  `resource_type_colour` varchar(7) DEFAULT NULL,
  `shift_1_start` time DEFAULT NULL,
  `shift_1_end` time DEFAULT NULL,
  `shift_2_start` time DEFAULT NULL,
  `shift_2_end` time DEFAULT NULL,
  `description` text,
  `supervising` int(1) NOT NULL DEFAULT '0',
  `active` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `resource_types`
--

INSERT INTO `resource_types` (`resource_type_id`, `resource_type_name`, `resource_type_short_code`, `resource_type_colour`, `shift_1_start`, `shift_1_end`, `shift_2_start`, `shift_2_end`, `description`, `supervising`, `active`) VALUES
(1, 'Arm-Roll RORO Driver', 'ARD', '#76bc40', '07:00:00', '19:00:00', '19:00:00', '07:00:00', '', 0, 1),
(2, 'Compactor Driver', 'CD', '#ce2421', NULL, '19:00:00', '19:00:00', '07:00:00', '', 0, 1),
(3, 'Bulk Waste Driver', 'BWD', '#a510a0', '00:00:00', '19:00:00', '19:00:00', '07:00:00', '', 0, 1),
(4, 'Road Sweeper Driver', 'RSD', '#969426', '00:00:00', '19:00:00', '19:00:00', '07:00:00', '', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `resource_type_rates`
--

CREATE TABLE `resource_type_rates` (
  `resource_type_id` int(10) NOT NULL,
  `employment_type` varchar(40) DEFAULT NULL,
  `work_rate` decimal(10,2) DEFAULT NULL,
  `standby_rate` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(10) UNSIGNED NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `description` mediumtext NOT NULL,
  `active` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`, `description`, `active`) VALUES
(1, 'Administrator', '', 1),
(2, 'Operations - Management', '', 1),
(3, 'General Manager', '', 1),
(4, 'Customer', '', 1),
(5, 'Human Resource - Management', '', 1),
(6, 'Finance - Management', '', 1),
(7, 'Operations - Admin', '', 1),
(8, 'Operations - Supervisor', '', 1),
(9, 'Operations - Team Leader', '', 1),
(10, 'Human Resource - Senior Admin', '', 1),
(11, 'Human Resource - Admin', '', 1),
(12, 'Finance - Senior Admin', '', 1),
(13, 'Finance - Admin', '', 1),
(14, 'Admin with Vessel Creation', '', 1),
(15, 'Equipment & Asset Admin', '', 1),
(16, 'Operations Junior Manager', '', 1),
(17, 'Mobile User', '', 1),
(18, 'sdsds', 'dssd', 1);

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role_id` int(10) UNSIGNED NOT NULL,
  `perm_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`role_id`, `perm_id`) VALUES
(10, 9),
(10, 10),
(10, 16),
(10, 42),
(10, 43),
(10, 44),
(10, 51),
(10, 52),
(10, 53),
(10, 58),
(10, 78),
(10, 108),
(10, 138),
(10, 156),
(10, 27),
(10, 37),
(10, 61),
(10, 62),
(10, 63),
(10, 82),
(10, 146),
(10, 166),
(10, 170),
(10, 176),
(10, 179),
(10, 1),
(10, 3),
(10, 5),
(10, 7),
(10, 8),
(10, 11),
(10, 12),
(10, 15),
(10, 36),
(10, 45),
(10, 46),
(10, 2),
(10, 4),
(10, 6),
(10, 33),
(10, 55),
(10, 56),
(10, 57),
(10, 133),
(10, 134),
(10, 135),
(10, 181),
(10, 182),
(10, 183),
(10, 186),
(10, 187),
(10, 188),
(10, 189),
(10, 190),
(10, 191),
(10, 19),
(10, 40),
(10, 69),
(10, 70),
(10, 21),
(10, 23),
(10, 24),
(10, 116),
(10, 117),
(10, 50),
(10, 60),
(10, 141),
(10, 144),
(10, 145),
(10, 167),
(10, 192),
(10, 193),
(10, 194),
(10, 195),
(10, 196),
(10, 207),
(10, 208),
(10, 210),
(10, 30),
(10, 31),
(10, 32),
(10, 47),
(10, 48),
(10, 49),
(10, 59),
(10, 74),
(10, 85),
(10, 34),
(10, 139),
(10, 143),
(10, 149),
(10, 151),
(10, 152),
(10, 153),
(10, 157),
(10, 165),
(10, 177),
(10, 185),
(10, 22),
(10, 54),
(10, 79),
(10, 80),
(10, 101),
(10, 110),
(10, 111),
(10, 112),
(10, 119),
(10, 124),
(10, 154),
(10, 155),
(10, 158),
(10, 159),
(10, 161),
(10, 162),
(10, 164),
(10, 171),
(10, 172),
(10, 173),
(10, 174),
(10, 175),
(10, 206),
(10, 163),
(10, 178),
(10, 200),
(10, 204),
(13, 51),
(13, 52),
(13, 53),
(13, 58),
(13, 137),
(13, 138),
(13, 156),
(13, 142),
(13, 170),
(13, 1),
(13, 3),
(13, 55),
(13, 56),
(13, 57),
(13, 133),
(13, 134),
(13, 135),
(13, 19),
(13, 40),
(13, 71),
(13, 21),
(13, 23),
(13, 24),
(13, 116),
(13, 117),
(13, 50),
(13, 192),
(13, 212),
(13, 30),
(13, 31),
(13, 32),
(13, 47),
(13, 48),
(13, 49),
(13, 59),
(13, 74),
(13, 85),
(13, 151),
(13, 152),
(13, 165),
(13, 22),
(13, 54),
(13, 79),
(13, 80),
(13, 101),
(13, 110),
(13, 111),
(13, 112),
(13, 119),
(13, 124),
(13, 154),
(13, 155),
(13, 158),
(13, 159),
(13, 161),
(13, 162),
(13, 164),
(13, 171),
(13, 172),
(13, 173),
(13, 174),
(13, 175),
(13, 206),
(13, 163),
(13, 199),
(13, 203),
(13, 204),
(13, 205),
(13, 211),
(9, 108),
(9, 27),
(9, 37),
(9, 61),
(9, 62),
(9, 63),
(9, 82),
(9, 146),
(9, 166),
(9, 170),
(9, 176),
(9, 179),
(9, 1),
(9, 3),
(9, 50),
(9, 60),
(9, 141),
(9, 144),
(9, 145),
(9, 167),
(9, 192),
(9, 193),
(9, 194),
(9, 195),
(9, 196),
(9, 207),
(9, 208),
(9, 209),
(9, 210),
(9, 30),
(9, 31),
(9, 32),
(9, 47),
(9, 48),
(9, 49),
(9, 59),
(9, 74),
(9, 85),
(9, 22),
(9, 54),
(9, 79),
(9, 80),
(9, 101),
(9, 110),
(9, 111),
(9, 112),
(9, 119),
(9, 124),
(9, 154),
(9, 155),
(9, 158),
(9, 159),
(9, 161),
(9, 162),
(9, 164),
(9, 171),
(9, 172),
(9, 173),
(9, 174),
(9, 175),
(9, 206),
(9, 163),
(7, 9),
(7, 43),
(7, 44),
(7, 51),
(7, 52),
(7, 53),
(7, 108),
(7, 156),
(7, 27),
(7, 37),
(7, 61),
(7, 62),
(7, 63),
(7, 82),
(7, 146),
(7, 166),
(7, 170),
(7, 176),
(7, 179),
(7, 4),
(7, 6),
(7, 33),
(7, 55),
(7, 56),
(7, 57),
(7, 133),
(7, 134),
(7, 135),
(7, 181),
(7, 182),
(7, 183),
(7, 186),
(7, 187),
(7, 188),
(7, 189),
(7, 190),
(7, 191),
(7, 18),
(7, 19),
(7, 20),
(7, 25),
(7, 26),
(7, 35),
(7, 39),
(7, 40),
(7, 41),
(7, 69),
(7, 70),
(7, 71),
(7, 81),
(7, 109),
(7, 132),
(7, 147),
(7, 169),
(7, 21),
(7, 23),
(7, 24),
(7, 116),
(7, 117),
(7, 50),
(7, 60),
(7, 141),
(7, 192),
(7, 193),
(7, 194),
(7, 195),
(7, 196),
(7, 207),
(7, 208),
(7, 209),
(7, 210),
(7, 30),
(7, 31),
(7, 32),
(7, 47),
(7, 48),
(7, 49),
(7, 59),
(7, 74),
(7, 85),
(7, 163),
(7, 203),
(7, 204),
(15, 9),
(15, 51),
(15, 52),
(15, 53),
(15, 58),
(15, 156),
(15, 55),
(15, 56),
(15, 57),
(15, 133),
(15, 134),
(15, 135),
(15, 21),
(15, 23),
(15, 24),
(15, 116),
(15, 117),
(15, 30),
(15, 31),
(15, 32),
(15, 47),
(15, 48),
(15, 49),
(15, 59),
(15, 74),
(15, 85),
(15, 22),
(15, 54),
(15, 79),
(15, 80),
(15, 101),
(15, 110),
(15, 111),
(15, 112),
(15, 119),
(15, 124),
(15, 154),
(15, 155),
(15, 158),
(15, 159),
(15, 161),
(15, 162),
(15, 164),
(15, 171),
(15, 172),
(15, 173),
(15, 174),
(15, 175),
(15, 206),
(16, 9),
(16, 14),
(16, 16),
(16, 42),
(16, 43),
(16, 44),
(16, 51),
(16, 52),
(16, 53),
(16, 78),
(16, 108),
(16, 27),
(16, 37),
(16, 38),
(16, 61),
(16, 62),
(16, 63),
(16, 82),
(16, 142),
(16, 146),
(16, 150),
(16, 166),
(16, 170),
(16, 176),
(16, 179),
(16, 1),
(16, 3),
(16, 2),
(16, 4),
(16, 6),
(16, 33),
(16, 55),
(16, 56),
(16, 57),
(16, 133),
(16, 134),
(16, 135),
(16, 181),
(16, 182),
(16, 183),
(16, 186),
(16, 187),
(16, 188),
(16, 189),
(16, 190),
(16, 191),
(16, 18),
(16, 19),
(16, 20),
(16, 25),
(16, 26),
(16, 35),
(16, 39),
(16, 40),
(16, 41),
(16, 69),
(16, 70),
(16, 71),
(16, 81),
(16, 109),
(16, 132),
(16, 147),
(16, 169),
(16, 21),
(16, 23),
(16, 24),
(16, 116),
(16, 117),
(16, 50),
(16, 60),
(16, 141),
(16, 144),
(16, 145),
(16, 167),
(16, 192),
(16, 193),
(16, 194),
(16, 195),
(16, 196),
(16, 207),
(16, 208),
(16, 209),
(16, 210),
(16, 152),
(16, 165),
(16, 22),
(16, 80),
(16, 124),
(16, 158),
(16, 162),
(16, 173),
(16, 163),
(2, 9),
(2, 16),
(2, 42),
(2, 43),
(2, 44),
(2, 51),
(2, 52),
(2, 53),
(2, 58),
(2, 78),
(2, 108),
(2, 137),
(2, 138),
(2, 156),
(2, 27),
(2, 37),
(2, 38),
(2, 61),
(2, 62),
(2, 63),
(2, 82),
(2, 142),
(2, 146),
(2, 150),
(2, 166),
(2, 170),
(2, 176),
(2, 179),
(2, 4),
(2, 6),
(2, 33),
(2, 55),
(2, 56),
(2, 57),
(2, 133),
(2, 134),
(2, 135),
(2, 181),
(2, 182),
(2, 183),
(2, 186),
(2, 187),
(2, 188),
(2, 189),
(2, 190),
(2, 191),
(2, 18),
(2, 19),
(2, 20),
(2, 25),
(2, 26),
(2, 35),
(2, 39),
(2, 40),
(2, 41),
(2, 69),
(2, 70),
(2, 71),
(2, 81),
(2, 109),
(2, 132),
(2, 147),
(2, 169),
(2, 21),
(2, 23),
(2, 24),
(2, 116),
(2, 117),
(2, 50),
(2, 60),
(2, 141),
(2, 144),
(2, 145),
(2, 167),
(2, 192),
(2, 193),
(2, 194),
(2, 195),
(2, 196),
(2, 207),
(2, 208),
(2, 209),
(2, 210),
(2, 212),
(2, 30),
(2, 31),
(2, 32),
(2, 47),
(2, 48),
(2, 49),
(2, 59),
(2, 74),
(2, 85),
(2, 22),
(2, 54),
(2, 79),
(2, 80),
(2, 101),
(2, 110),
(2, 111),
(2, 112),
(2, 119),
(2, 124),
(2, 154),
(2, 155),
(2, 158),
(2, 159),
(2, 161),
(2, 162),
(2, 164),
(2, 171),
(2, 172),
(2, 173),
(2, 174),
(2, 175),
(2, 206),
(2, 163),
(2, 203),
(2, 204),
(11, 10),
(11, 14),
(11, 16),
(11, 42),
(11, 43),
(11, 44),
(11, 51),
(11, 52),
(11, 53),
(11, 78),
(11, 108),
(11, 1),
(11, 3),
(11, 5),
(11, 8),
(11, 12),
(11, 15),
(11, 36),
(11, 45),
(11, 46),
(11, 6),
(11, 33),
(11, 56),
(11, 133),
(11, 135),
(11, 181),
(11, 183),
(11, 186),
(11, 188),
(11, 191),
(11, 19),
(11, 26),
(11, 35),
(11, 40),
(11, 41),
(11, 70),
(11, 71),
(11, 81),
(11, 147),
(11, 21),
(11, 24),
(11, 116),
(11, 117),
(11, 50),
(11, 60),
(11, 145),
(11, 167),
(11, 192),
(11, 193),
(11, 194),
(11, 195),
(11, 196),
(11, 207),
(11, 210),
(11, 30),
(11, 32),
(11, 47),
(11, 48),
(11, 74),
(11, 85),
(11, 34),
(11, 139),
(11, 143),
(11, 149),
(11, 151),
(11, 152),
(11, 153),
(11, 157),
(11, 165),
(11, 177),
(11, 184),
(11, 185),
(11, 22),
(11, 54),
(11, 80),
(11, 101),
(11, 110),
(11, 112),
(11, 124),
(11, 154),
(11, 158),
(11, 161),
(11, 162),
(11, 171),
(11, 172),
(11, 173),
(11, 175),
(11, 206),
(11, 163),
(11, 178),
(11, 199),
(11, 204),
(11, 205),
(8, 62),
(8, 170),
(8, 40),
(8, 69),
(8, 70),
(8, 71),
(8, 50),
(8, 60),
(8, 141),
(8, 144),
(8, 145),
(8, 167),
(8, 193),
(8, 194),
(8, 195),
(8, 196),
(8, 207),
(8, 208),
(8, 209),
(8, 210),
(8, 213),
(8, 214),
(8, 222),
(8, 32),
(8, 47),
(8, 85),
(8, 22),
(8, 80),
(8, 124),
(8, 158),
(8, 162),
(8, 173),
(4, 61),
(4, 62),
(4, 63),
(4, 82),
(4, 146),
(4, 170),
(4, 176),
(4, 19),
(4, 40),
(4, 70),
(4, 71),
(4, 192),
(4, 210),
(4, 203),
(4, 204),
(4, 218),
(14, 9),
(14, 10),
(14, 13),
(14, 14),
(14, 16),
(14, 42),
(14, 43),
(14, 44),
(14, 51),
(14, 52),
(14, 53),
(14, 58),
(14, 78),
(14, 108),
(14, 137),
(14, 138),
(14, 156),
(14, 220),
(14, 221),
(14, 259),
(14, 27),
(14, 37),
(14, 38),
(14, 61),
(14, 62),
(14, 63),
(14, 82),
(14, 142),
(14, 146),
(14, 150),
(14, 166),
(14, 170),
(14, 176),
(14, 179),
(14, 1),
(14, 3),
(14, 5),
(14, 7),
(14, 8),
(14, 11),
(14, 12),
(14, 15),
(14, 17),
(14, 36),
(14, 45),
(14, 46),
(14, 136),
(14, 2),
(14, 4),
(14, 6),
(14, 33),
(14, 55),
(14, 56),
(14, 57),
(14, 133),
(14, 134),
(14, 135),
(14, 181),
(14, 182),
(14, 183),
(14, 186),
(14, 187),
(14, 188),
(14, 189),
(14, 190),
(14, 191),
(14, 215),
(14, 216),
(14, 217),
(14, 18),
(14, 19),
(14, 20),
(14, 25),
(14, 26),
(14, 35),
(14, 39),
(14, 40),
(14, 41),
(14, 69),
(14, 70),
(14, 71),
(14, 81),
(14, 109),
(14, 132),
(14, 147),
(14, 169),
(14, 21),
(14, 23),
(14, 24),
(14, 116),
(14, 117),
(14, 50),
(14, 60),
(14, 141),
(14, 144),
(14, 145),
(14, 167),
(14, 192),
(14, 193),
(14, 194),
(14, 195),
(14, 196),
(14, 207),
(14, 208),
(14, 209),
(14, 210),
(14, 212),
(14, 213),
(14, 214),
(14, 222),
(14, 30),
(14, 31),
(14, 32),
(14, 47),
(14, 48),
(14, 49),
(14, 59),
(14, 74),
(14, 85),
(14, 34),
(14, 139),
(14, 143),
(14, 149),
(14, 151),
(14, 152),
(14, 153),
(14, 157),
(14, 165),
(14, 177),
(14, 184),
(14, 185),
(14, 22),
(14, 54),
(14, 79),
(14, 80),
(14, 101),
(14, 110),
(14, 111),
(14, 112),
(14, 119),
(14, 124),
(14, 154),
(14, 155),
(14, 158),
(14, 159),
(14, 161),
(14, 162),
(14, 164),
(14, 171),
(14, 172),
(14, 173),
(14, 174),
(14, 175),
(14, 206),
(14, 180),
(14, 163),
(14, 178),
(14, 199),
(14, 200),
(14, 201),
(14, 202),
(14, 203),
(14, 204),
(14, 205),
(14, 211),
(14, 218),
(14, 219),
(14, 260),
(1, 9),
(1, 10),
(1, 13),
(1, 14),
(1, 16),
(1, 42),
(1, 43),
(1, 44),
(1, 51),
(1, 52),
(1, 53),
(1, 58),
(1, 78),
(1, 108),
(1, 137),
(1, 138),
(1, 156),
(1, 220),
(1, 221),
(1, 234),
(1, 235),
(1, 236),
(1, 237),
(1, 238),
(1, 239),
(1, 240),
(1, 241),
(1, 242),
(1, 243),
(1, 244),
(1, 245),
(1, 246),
(1, 247),
(1, 248),
(1, 249),
(1, 251),
(1, 252),
(1, 253),
(1, 254),
(1, 255),
(1, 256),
(1, 257),
(1, 258),
(1, 262),
(1, 263),
(1, 264),
(1, 265),
(1, 266),
(1, 267),
(1, 268),
(1, 269),
(1, 270),
(1, 27),
(1, 37),
(1, 38),
(1, 61),
(1, 62),
(1, 63),
(1, 82),
(1, 142),
(1, 146),
(1, 150),
(1, 166),
(1, 170),
(1, 176),
(1, 179),
(1, 1),
(1, 3),
(1, 5),
(1, 7),
(1, 8),
(1, 11),
(1, 12),
(1, 15),
(1, 17),
(1, 36),
(1, 45),
(1, 46),
(1, 136),
(1, 2),
(1, 4),
(1, 6),
(1, 33),
(1, 55),
(1, 56),
(1, 57),
(1, 133),
(1, 134),
(1, 135),
(1, 181),
(1, 182),
(1, 183),
(1, 186),
(1, 187),
(1, 188),
(1, 189),
(1, 190),
(1, 191),
(1, 215),
(1, 216),
(1, 217),
(1, 19),
(1, 20),
(1, 25),
(1, 26),
(1, 35),
(1, 39),
(1, 40),
(1, 69),
(1, 70),
(1, 71),
(1, 81),
(1, 109),
(1, 132),
(1, 147),
(1, 169),
(1, 21),
(1, 23),
(1, 24),
(1, 116),
(1, 117),
(1, 50),
(1, 60),
(1, 141),
(1, 144),
(1, 145),
(1, 167),
(1, 192),
(1, 193),
(1, 194),
(1, 195),
(1, 196),
(1, 207),
(1, 208),
(1, 209),
(1, 210),
(1, 212),
(1, 213),
(1, 214),
(1, 222),
(1, 30),
(1, 31),
(1, 32),
(1, 47),
(1, 48),
(1, 49),
(1, 59),
(1, 74),
(1, 85),
(1, 34),
(1, 139),
(1, 143),
(1, 149),
(1, 151),
(1, 152),
(1, 153),
(1, 157),
(1, 165),
(1, 177),
(1, 184),
(1, 185),
(1, 223),
(1, 224),
(1, 225),
(1, 22),
(1, 54),
(1, 79),
(1, 80),
(1, 101),
(1, 110),
(1, 111),
(1, 112),
(1, 119),
(1, 124),
(1, 154),
(1, 155),
(1, 158),
(1, 159),
(1, 161),
(1, 162),
(1, 164),
(1, 171),
(1, 172),
(1, 173),
(1, 174),
(1, 175),
(1, 206),
(1, 180),
(1, 163),
(1, 178),
(1, 199),
(1, 200),
(1, 201),
(1, 202),
(1, 230),
(1, 233),
(1, 203),
(1, 204),
(1, 205),
(1, 211),
(1, 218),
(1, 219),
(1, 227),
(1, 228),
(1, 229),
(1, 231);

-- --------------------------------------------------------

--
-- Table structure for table `service_requests`
--

CREATE TABLE `service_requests` (
  `service_request_id` int(10) UNSIGNED NOT NULL,
  `service_request_number` varchar(20) DEFAULT NULL,
  `service_request_type` enum('vessel','warehouse') DEFAULT NULL,
  `vessel_visit_id` int(10) UNSIGNED DEFAULT NULL,
  `company_address_id` int(10) DEFAULT NULL,
  `company_id` int(10) UNSIGNED DEFAULT NULL,
  `cargo_type` int(10) UNSIGNED DEFAULT NULL,
  `number_gangs` int(4) NOT NULL DEFAULT '0',
  `bl_number` varchar(255) DEFAULT NULL,
  `work_meals` int(1) DEFAULT '0',
  `not_chargeable` int(1) NOT NULL DEFAULT '0',
  `active` int(1) NOT NULL DEFAULT '1',
  `service_request_status` enum('new','approved','planned','in_progress','completed','cancelled','draft') NOT NULL DEFAULT 'new',
  `added_by` int(10) UNSIGNED NOT NULL,
  `service_voucher_1` text,
  `service_voucher_2` text,
  `deleted` int(1) NOT NULL DEFAULT '0',
  `t_added` datetime DEFAULT NULL,
  `t_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `service_request_attachments`
--

CREATE TABLE `service_request_attachments` (
  `service_request_attachment_id` int(10) NOT NULL,
  `service_request_id` int(10) UNSIGNED NOT NULL,
  `filename` varchar(200) NOT NULL,
  `file_order` int(2) NOT NULL,
  `prefix` varchar(30) NOT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `service_request_billing`
--

CREATE TABLE `service_request_billing` (
  `service_request_billing_id` int(11) NOT NULL,
  `service_request_id` int(11) NOT NULL,
  `operation_date` date NOT NULL,
  `shift` int(11) NOT NULL,
  `billing_resources` varchar(45) DEFAULT NULL,
  `is_manager_approved` tinyint(4) DEFAULT '0',
  `is_finance_approved` tinyint(4) DEFAULT '0',
  `deleted` tinyint(4) DEFAULT '0',
  `added_by` int(11) DEFAULT NULL,
  `t_added` datetime DEFAULT NULL,
  `t_updated` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `service_request_disposals`
--

CREATE TABLE `service_request_disposals` (
  `service_request_disposal_id` int(10) NOT NULL,
  `service_request_id` int(10) NOT NULL,
  `location_from` varchar(255) DEFAULT NULL,
  `location_to` varchar(255) DEFAULT NULL,
  `disposal_invoice_id` int(10) DEFAULT NULL,
  `disposal_price` decimal(10,2) DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  `t_start` date NOT NULL,
  `t_end` date NOT NULL,
  `t_inserted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `service_request_disposal_tally`
--

CREATE TABLE `service_request_disposal_tally` (
  `service_request_disposal_tally_id` int(10) NOT NULL,
  `service_request_disposal_id` int(10) UNSIGNED NOT NULL,
  `delivery_order` varchar(64) DEFAULT NULL,
  `driver_name` varchar(128) DEFAULT NULL,
  `vehicle` varchar(24) DEFAULT NULL,
  `wastage_type_id` int(10) UNSIGNED NOT NULL,
  `quantity` int(8) NOT NULL,
  `weight` decimal(10,2) DEFAULT NULL,
  `delay_start` datetime DEFAULT NULL,
  `datetime` datetime NOT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `service_request_equipment_types`
--

CREATE TABLE `service_request_equipment_types` (
  `service_request_equipment_id` int(10) UNSIGNED NOT NULL,
  `service_request_id` int(10) UNSIGNED NOT NULL,
  `equipment_type_id` int(10) UNSIGNED NOT NULL,
  `quantity` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `service_request_gear_types`
--

CREATE TABLE `service_request_gear_types` (
  `service_request_gear_id` int(10) UNSIGNED NOT NULL,
  `service_request_id` int(10) UNSIGNED NOT NULL,
  `gear_type_id` int(10) UNSIGNED NOT NULL,
  `quantity` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `service_request_operations`
--

CREATE TABLE `service_request_operations` (
  `service_request_operation_id` int(10) NOT NULL,
  `service_request_id` int(10) UNSIGNED NOT NULL,
  `vessel_hatch_id` int(10) DEFAULT NULL,
  `cargo_type_id` int(10) DEFAULT NULL,
  `commodity_id` int(10) UNSIGNED DEFAULT NULL,
  `cargo_packaging_id` int(10) UNSIGNED DEFAULT NULL,
  `operation_type` int(10) UNSIGNED DEFAULT NULL,
  `tonnage` decimal(10,4) DEFAULT NULL,
  `quantity` int(7) DEFAULT NULL,
  `bl_number` varchar(100) DEFAULT NULL,
  `operation_delayed` int(1) NOT NULL DEFAULT '0',
  `t_start` date NOT NULL,
  `t_end` date NOT NULL,
  `stowage_sizex` int(2) DEFAULT NULL,
  `stowage_sizey` int(2) DEFAULT NULL,
  `stowage_col` int(2) DEFAULT NULL,
  `stowage_row` int(2) DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  `t_inserted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `service_request_operation_tally`
--

CREATE TABLE `service_request_operation_tally` (
  `service_request_operation_tally_id` int(10) NOT NULL,
  `service_request_operation_id` int(10) DEFAULT NULL,
  `delay_reason_id` int(10) DEFAULT NULL,
  `vessel_visit_id` int(10) DEFAULT NULL,
  `vessel_hatch_id` int(10) DEFAULT NULL,
  `dropping` int(1) NOT NULL DEFAULT '0',
  `rebundling` int(1) NOT NULL DEFAULT '0',
  `tally_quantity` decimal(10,2) DEFAULT NULL,
  `tally_remark` int(10) DEFAULT NULL,
  `delay_minutes` int(4) DEFAULT NULL,
  `rebundling_code` varchar(32) DEFAULT NULL,
  `rebundling_serial` varchar(128) DEFAULT NULL,
  `rebundling_colour` int(10) DEFAULT NULL,
  `delay_start` datetime DEFAULT NULL,
  `datetime` datetime NOT NULL,
  `processed` int(1) NOT NULL DEFAULT '0',
  `manual` int(1) NOT NULL DEFAULT '0',
  `deleted` int(1) NOT NULL DEFAULT '0',
  `remarks` text,
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `service_request_remarks`
--

CREATE TABLE `service_request_remarks` (
  `service_request_remark_id` int(10) NOT NULL,
  `service_request_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `remark` text,
  `t_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `service_request_resource_types`
--

CREATE TABLE `service_request_resource_types` (
  `service_request_resource_id` int(10) UNSIGNED NOT NULL,
  `service_request_id` int(10) UNSIGNED NOT NULL,
  `resource_type_id` int(10) UNSIGNED NOT NULL,
  `quantity` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `service_vouchers`
--

CREATE TABLE `service_vouchers` (
  `service_voucher_id` int(10) NOT NULL,
  `vessel_visit_id` int(10) DEFAULT NULL,
  `company_id` int(10) DEFAULT NULL,
  `shift` int(1) DEFAULT NULL,
  `operation_date` date DEFAULT NULL,
  `filename` text,
  `invoice_id` int(10) DEFAULT NULL,
  `remarks` text,
  `t_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `shipment_terms`
--

CREATE TABLE `shipment_terms` (
  `shipment_term_id` int(5) UNSIGNED NOT NULL,
  `shipment_term_name` varchar(30) DEFAULT NULL,
  `description` text,
  `active` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tally_remarks`
--

CREATE TABLE `tally_remarks` (
  `tally_remark_id` int(11) NOT NULL,
  `tally_remark_name` varchar(100) DEFAULT NULL,
  `description` text,
  `active` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `task_runs`
--

CREATE TABLE `task_runs` (
  `task_run_id` int(11) NOT NULL,
  `task_id` smallint(6) NOT NULL,
  `status` enum('started','completed','error') DEFAULT NULL,
  `execution_time` decimal(6,2) NOT NULL DEFAULT '0.00',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `output` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `templates`
--

CREATE TABLE `templates` (
  `template_id` int(5) UNSIGNED NOT NULL,
  `template_name` varchar(100) NOT NULL,
  `template_group` varchar(10) NOT NULL,
  `template_html` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `timezones`
--

CREATE TABLE `timezones` (
  `timezone_id` int(5) UNSIGNED NOT NULL,
  `country_code` varchar(2) NOT NULL,
  `timezone` varchar(100) NOT NULL,
  `active` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) UNSIGNED NOT NULL,
  `username` varchar(255) NOT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `user_code` varchar(200) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `active_branch` int(5) DEFAULT NULL,
  `company_id` int(10) DEFAULT NULL,
  `designation` int(10) DEFAULT NULL,
  `user_group` int(10) DEFAULT NULL,
  `address_line_1` varchar(255) DEFAULT NULL,
  `address_line_2` varchar(255) DEFAULT NULL,
  `address_zip` varchar(10) DEFAULT NULL,
  `address_city` varchar(100) DEFAULT NULL,
  `address_state` varchar(100) DEFAULT NULL,
  `address_country` char(2) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `timezone` varchar(30) DEFAULT NULL,
  `session` varchar(255) DEFAULT NULL,
  `profile_picture` varchar(200) DEFAULT NULL,
  `remember` int(1) DEFAULT '0',
  `mobile` int(1) DEFAULT '0',
  `default_color` varchar(30) DEFAULT NULL,
  `default_font` varchar(255) DEFAULT NULL,
  `active` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `full_name`, `user_code`, `email`, `password`, `active_branch`, `company_id`, `designation`, `user_group`, `address_line_1`, `address_line_2`, `address_zip`, `address_city`, `address_state`, `address_country`, `phone`, `timezone`, `session`, `profile_picture`, `remember`, `mobile`, `default_color`, `default_font`, `active`) VALUES
(2, 'admin', 'Admin', 'ADMIN', 'admin@bytespace.asia', '$2y$10$N.7jwz9kWfcCnmOKYESSuOm4CJkJ0/JVavSpkB7fahhQv3kFIgCiq', 1, NULL, 1, 1, '', '', '', '', '', 'MY', '123', '', '$2y$10$Vj5UQEPm8UtPX2hR9MNzDuqeQDLZxug7NWlEsHbylMpN8y/ZdCd.C', '1660031078-aaaaa.PNG', 1, 1, '', 'Amaranth', 1),
(61, 'Suresh', 'Suresh Chidambareswaran', 'SC', 'suresh@bytespace.asia', '$2y$10$lW4HTooYH6YzVWls1OakLe1IuUbQk9e6L2fT0HsQnSBRiB9qAy6VS', NULL, NULL, 1, 1, '11 Jalan Setia Indah u13/12G', 'Setia Indah 12', '40170', 'Shah Alam', 'Selangor', 'MY', '0122127352', NULL, '$2y$10$tS1pfemNvrdUTebRcJuQUO58NNTl74fuwld6fSehE9l189.pM27.y', '1660046821-Suresh.png', 1, NULL, '', 'Amaranth', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_branch`
--

CREATE TABLE `user_branch` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `branch_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_branch`
--

INSERT INTO `user_branch` (`user_id`, `branch_id`) VALUES
(2, 1),
(6, 1),
(9, 1),
(2, 2),
(9, 2),
(10, 1),
(10, 2),
(2, 4),
(9, 4),
(2, 3),
(9, 3),
(10, 3);

-- --------------------------------------------------------

--
-- Table structure for table `user_groups`
--

CREATE TABLE `user_groups` (
  `user_group_id` int(10) UNSIGNED NOT NULL,
  `user_group_name` varchar(50) NOT NULL,
  `description` mediumtext NOT NULL,
  `active` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_groups`
--

INSERT INTO `user_groups` (`user_group_id`, `user_group_name`, `description`, `active`) VALUES
(1, 'Operations', '', 1),
(3, 'Senior Management', '', 1),
(4, 'Finance', '', 1),
(5, 'Human Resource', '', 1),
(6, 'Customer Service', '', 1),
(7, 'Engineering', '', 1),
(8, 'Driver', '', 1),
(9, 'Clients', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_permissions`
--

CREATE TABLE `user_permissions` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `perm_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_permissions`
--

INSERT INTO `user_permissions` (`user_id`, `perm_id`) VALUES
(18, 9),
(18, 10),
(18, 13),
(18, 14),
(18, 16),
(18, 42),
(18, 43),
(18, 44),
(18, 51),
(18, 52),
(18, 53),
(18, 58),
(18, 78),
(18, 108),
(18, 137),
(18, 138),
(18, 156),
(18, 61),
(18, 62),
(18, 63),
(18, 82),
(18, 142),
(18, 146),
(18, 150),
(18, 166),
(18, 170),
(18, 176),
(18, 1),
(18, 3),
(18, 5),
(18, 7),
(18, 8),
(18, 11),
(18, 12),
(18, 15),
(18, 17),
(18, 36),
(18, 45),
(18, 46),
(18, 136),
(18, 2),
(18, 4),
(18, 6),
(18, 33),
(18, 34),
(18, 55),
(18, 56),
(18, 57),
(18, 133),
(18, 134),
(18, 135),
(18, 18),
(18, 19),
(18, 20),
(18, 35),
(18, 39),
(18, 40),
(18, 41),
(18, 69),
(18, 70),
(18, 71),
(18, 81),
(18, 109),
(18, 132),
(18, 147),
(18, 169),
(18, 21),
(18, 23),
(18, 24),
(18, 25),
(18, 26),
(18, 27),
(18, 37),
(18, 38),
(18, 116),
(18, 117),
(18, 50),
(18, 60),
(18, 141),
(18, 144),
(18, 145),
(18, 155),
(18, 158),
(18, 161),
(18, 167),
(18, 30),
(18, 31),
(18, 32),
(18, 47),
(18, 48),
(18, 49),
(18, 59),
(18, 74),
(18, 85),
(18, 107),
(18, 128),
(18, 129),
(18, 139),
(18, 143),
(18, 149),
(18, 151),
(18, 152),
(18, 153),
(18, 157),
(18, 163),
(18, 165),
(18, 22),
(18, 54),
(18, 79),
(18, 80),
(18, 101),
(18, 110),
(18, 111),
(18, 112),
(18, 119),
(18, 124),
(18, 154),
(18, 159),
(18, 162),
(18, 164),
(18, 171),
(18, 172),
(18, 173),
(18, 174),
(18, 175),
(24, 180),
(27, 9),
(27, 10),
(27, 13),
(27, 14),
(27, 16),
(27, 42),
(27, 43),
(27, 44),
(27, 51),
(27, 52),
(27, 53),
(27, 58),
(27, 78),
(27, 108),
(27, 137),
(27, 138),
(27, 156),
(27, 27),
(27, 37),
(27, 38),
(27, 61),
(27, 62),
(27, 63),
(27, 82),
(27, 142),
(27, 146),
(27, 150),
(27, 166),
(27, 170),
(27, 176),
(27, 179),
(27, 1),
(27, 3),
(27, 5),
(27, 7),
(27, 8),
(27, 11),
(27, 12),
(27, 15),
(27, 17),
(27, 36),
(27, 45),
(27, 46),
(27, 136),
(27, 2),
(27, 4),
(27, 6),
(27, 33),
(27, 55),
(27, 56),
(27, 57),
(27, 133),
(27, 134),
(27, 135),
(27, 181),
(27, 182),
(27, 183),
(27, 186),
(27, 187),
(27, 188),
(27, 189),
(27, 190),
(27, 191),
(27, 18),
(27, 19),
(27, 20),
(27, 25),
(27, 26),
(27, 35),
(27, 39),
(27, 40),
(27, 41),
(27, 69),
(27, 70),
(27, 71),
(27, 81),
(27, 109),
(27, 132),
(27, 147),
(27, 169),
(27, 21),
(27, 23),
(27, 24),
(27, 116),
(27, 117),
(27, 50),
(27, 60),
(27, 141),
(27, 144),
(27, 145),
(27, 167),
(27, 192),
(27, 193),
(27, 194),
(27, 195),
(27, 196),
(27, 197),
(27, 30),
(27, 31),
(27, 32),
(27, 47),
(27, 48),
(27, 49),
(27, 59),
(27, 74),
(27, 85),
(27, 34),
(27, 139),
(27, 143),
(27, 149),
(27, 151),
(27, 152),
(27, 153),
(27, 157),
(27, 163),
(27, 165),
(27, 177),
(27, 178),
(27, 184),
(27, 185),
(27, 22),
(27, 54),
(27, 79),
(27, 80),
(27, 101),
(27, 110),
(27, 111),
(27, 112),
(27, 119),
(27, 124),
(27, 154),
(27, 155),
(27, 158),
(27, 159),
(27, 161),
(27, 162),
(27, 164),
(27, 171),
(27, 172),
(27, 173),
(27, 174),
(27, 175),
(27, 180),
(14, 139),
(14, 177),
(11, 226),
(42, 226),
(40, 9),
(40, 10),
(40, 13),
(40, 14),
(40, 16),
(40, 42),
(40, 43),
(40, 44),
(40, 51),
(40, 52),
(40, 53),
(40, 58),
(40, 78),
(40, 108),
(40, 137),
(40, 138),
(40, 156),
(40, 27),
(40, 37),
(40, 38),
(40, 61),
(40, 62),
(40, 63),
(40, 82),
(40, 142),
(40, 146),
(40, 150),
(40, 166),
(40, 170),
(40, 176),
(40, 179),
(40, 2),
(40, 4),
(40, 6),
(40, 33),
(40, 55),
(40, 56),
(40, 57),
(40, 133),
(40, 134),
(40, 135),
(40, 181),
(40, 182),
(40, 183),
(40, 186),
(40, 187),
(40, 188),
(40, 189),
(40, 190),
(40, 191),
(40, 216),
(40, 18),
(40, 19),
(40, 20),
(40, 25),
(40, 26),
(40, 35),
(40, 39),
(40, 40),
(40, 41),
(40, 69),
(40, 70),
(40, 71),
(40, 81),
(40, 109),
(40, 132),
(40, 147),
(40, 169),
(40, 21),
(40, 23),
(40, 24),
(40, 116),
(40, 117),
(40, 50),
(40, 60),
(40, 141),
(40, 144),
(40, 145),
(40, 167),
(40, 192),
(40, 193),
(40, 194),
(40, 195),
(40, 196),
(40, 207),
(40, 208),
(40, 209),
(40, 210),
(40, 212),
(40, 30),
(40, 31),
(40, 32),
(40, 47),
(40, 48),
(40, 49),
(40, 59),
(40, 74),
(40, 85),
(40, 22),
(40, 54),
(40, 79),
(40, 80),
(40, 101),
(40, 110),
(40, 111),
(40, 112),
(40, 119),
(40, 124),
(40, 154),
(40, 155),
(40, 158),
(40, 159),
(40, 161),
(40, 162),
(40, 164),
(40, 171),
(40, 172),
(40, 173),
(40, 174),
(40, 175),
(40, 206),
(40, 203),
(40, 204),
(40, 205),
(40, 211),
(40, 218),
(40, 219),
(40, 227),
(40, 228),
(39, 10),
(39, 16),
(39, 42),
(39, 58),
(39, 220),
(39, 221),
(39, 38),
(39, 150),
(39, 215),
(39, 216),
(39, 217),
(39, 144),
(39, 145),
(39, 167),
(39, 213),
(39, 214),
(39, 222),
(39, 139),
(39, 143),
(39, 149),
(39, 151),
(39, 152),
(39, 153),
(39, 157),
(39, 165),
(39, 177),
(39, 224),
(39, 22),
(39, 54),
(39, 79),
(39, 80),
(39, 101),
(39, 110),
(39, 111),
(39, 112),
(39, 119),
(39, 124),
(39, 154),
(39, 155),
(39, 158),
(39, 159),
(39, 161),
(39, 162),
(39, 164),
(39, 171),
(39, 172),
(39, 173),
(39, 174),
(39, 175),
(39, 206),
(43, 215),
(43, 216),
(43, 217),
(43, 229),
(43, 231),
(26, 223),
(26, 224),
(26, 225),
(26, 233),
(23, 9),
(23, 10),
(23, 13),
(23, 14),
(23, 16),
(23, 42),
(23, 43),
(23, 44),
(23, 51),
(23, 52),
(23, 53),
(23, 58),
(23, 78),
(23, 108),
(23, 137),
(23, 138),
(23, 156),
(23, 220),
(23, 221),
(23, 27),
(23, 37),
(23, 38),
(23, 61),
(23, 62),
(23, 63),
(23, 82),
(23, 142),
(23, 146),
(23, 150),
(23, 166),
(23, 170),
(23, 176),
(23, 179),
(23, 1),
(23, 3),
(23, 5),
(23, 7),
(23, 8),
(23, 11),
(23, 12),
(23, 15),
(23, 17),
(23, 36),
(23, 45),
(23, 46),
(23, 136),
(23, 2),
(23, 4),
(23, 6),
(23, 33),
(23, 55),
(23, 56),
(23, 57),
(23, 133),
(23, 134),
(23, 135),
(23, 181),
(23, 182),
(23, 183),
(23, 186),
(23, 187),
(23, 188),
(23, 189),
(23, 190),
(23, 191),
(23, 215),
(23, 216),
(23, 217),
(23, 18),
(23, 19),
(23, 20),
(23, 25),
(23, 26),
(23, 35),
(23, 39),
(23, 40),
(23, 41),
(23, 69),
(23, 70),
(23, 71),
(23, 81),
(23, 109),
(23, 132),
(23, 147),
(23, 169),
(23, 21),
(23, 23),
(23, 24),
(23, 116),
(23, 117),
(23, 50),
(23, 60),
(23, 141),
(23, 144),
(23, 145),
(23, 167),
(23, 192),
(23, 193),
(23, 194),
(23, 195),
(23, 196),
(23, 207),
(23, 208),
(23, 209),
(23, 210),
(23, 212),
(23, 213),
(23, 214),
(23, 222),
(23, 30),
(23, 31),
(23, 32),
(23, 47),
(23, 48),
(23, 49),
(23, 59),
(23, 74),
(23, 85),
(23, 34),
(23, 139),
(23, 143),
(23, 149),
(23, 151),
(23, 152),
(23, 153),
(23, 157),
(23, 165),
(23, 177),
(23, 184),
(23, 185),
(23, 223),
(23, 224),
(23, 225),
(23, 22),
(23, 54),
(23, 79),
(23, 80),
(23, 101),
(23, 110),
(23, 111),
(23, 112),
(23, 119),
(23, 124),
(23, 154),
(23, 155),
(23, 158),
(23, 159),
(23, 161),
(23, 162),
(23, 164),
(23, 171),
(23, 172),
(23, 173),
(23, 174),
(23, 175),
(23, 206),
(23, 180),
(23, 163),
(23, 178),
(23, 199),
(23, 200),
(23, 201),
(23, 202),
(23, 230),
(23, 233),
(23, 203),
(23, 204),
(23, 205),
(23, 211),
(23, 218),
(23, 219),
(23, 227),
(23, 228),
(23, 229),
(23, 231),
(9, 9),
(9, 10),
(9, 13),
(9, 14),
(9, 16),
(9, 42),
(9, 43),
(9, 44),
(9, 51),
(9, 52),
(9, 53),
(9, 58),
(9, 78),
(9, 108),
(9, 137),
(9, 138),
(9, 156),
(9, 220),
(9, 221),
(9, 27),
(9, 37),
(9, 38),
(9, 142),
(9, 150),
(9, 166),
(9, 179),
(9, 1),
(9, 3),
(9, 5),
(9, 7),
(9, 8),
(9, 11),
(9, 12),
(9, 15),
(9, 17),
(9, 36),
(9, 45),
(9, 46),
(9, 136),
(9, 2),
(9, 4),
(9, 6),
(9, 33),
(9, 55),
(9, 56),
(9, 57),
(9, 133),
(9, 134),
(9, 135),
(9, 181),
(9, 182),
(9, 183),
(9, 186),
(9, 187),
(9, 188),
(9, 189),
(9, 190),
(9, 191),
(9, 215),
(9, 216),
(9, 217),
(9, 18),
(9, 20),
(9, 25),
(9, 26),
(9, 35),
(9, 39),
(9, 41),
(9, 69),
(9, 81),
(9, 109),
(9, 132),
(9, 147),
(9, 169),
(9, 21),
(9, 23),
(9, 24),
(9, 116),
(9, 117),
(9, 50),
(9, 60),
(9, 141),
(9, 144),
(9, 145),
(9, 167),
(9, 193),
(9, 194),
(9, 195),
(9, 196),
(9, 207),
(9, 208),
(9, 209),
(9, 212),
(9, 213),
(9, 214),
(9, 222),
(9, 30),
(9, 31),
(9, 32),
(9, 47),
(9, 48),
(9, 49),
(9, 59),
(9, 74),
(9, 85),
(9, 34),
(9, 139),
(9, 143),
(9, 149),
(9, 151),
(9, 152),
(9, 153),
(9, 157),
(9, 165),
(9, 177),
(9, 184),
(9, 185),
(9, 223),
(9, 224),
(9, 225),
(9, 22),
(9, 54),
(9, 79),
(9, 80),
(9, 101),
(9, 110),
(9, 111),
(9, 112),
(9, 119),
(9, 124),
(9, 154),
(9, 155),
(9, 158),
(9, 159),
(9, 161),
(9, 162),
(9, 164),
(9, 171),
(9, 172),
(9, 173),
(9, 174),
(9, 175),
(9, 206),
(9, 180),
(9, 163),
(9, 178),
(9, 199),
(9, 200),
(9, 201),
(9, 202),
(9, 230),
(9, 233),
(9, 205),
(9, 211),
(9, 219),
(9, 227),
(9, 228),
(9, 229),
(9, 231),
(50, 9),
(50, 10),
(50, 13),
(50, 14),
(50, 16),
(50, 42),
(50, 43),
(50, 44),
(50, 51),
(50, 52),
(50, 53),
(50, 58),
(50, 78),
(50, 108),
(50, 137),
(50, 138),
(50, 156),
(50, 220),
(50, 221),
(50, 234),
(50, 27),
(50, 37),
(50, 38),
(50, 61),
(50, 62),
(50, 63),
(50, 82),
(50, 142),
(50, 146),
(50, 150),
(50, 166),
(50, 170),
(50, 176),
(50, 179),
(50, 1),
(50, 3),
(50, 5),
(50, 7),
(50, 8),
(50, 11),
(50, 12),
(50, 15),
(50, 17),
(50, 36),
(50, 45),
(50, 46),
(50, 136),
(50, 2),
(50, 4),
(50, 6),
(50, 33),
(50, 55),
(50, 56),
(50, 57),
(50, 133),
(50, 134),
(50, 135),
(50, 181),
(50, 182),
(50, 183),
(50, 186),
(50, 187),
(50, 188),
(50, 189),
(50, 190),
(50, 191),
(50, 215),
(50, 216),
(50, 217),
(50, 18),
(50, 19),
(50, 20),
(50, 25),
(50, 26),
(50, 35),
(50, 39),
(50, 40),
(50, 41),
(50, 69),
(50, 70),
(50, 71),
(50, 81),
(50, 109),
(50, 132),
(50, 147),
(50, 169),
(50, 21),
(50, 23),
(50, 24),
(50, 116),
(50, 117),
(50, 50),
(50, 60),
(50, 141),
(50, 144),
(50, 145),
(50, 167),
(50, 192),
(50, 193),
(50, 194),
(50, 195),
(50, 196),
(50, 207),
(50, 208),
(50, 209),
(50, 210),
(50, 212),
(50, 213),
(50, 214),
(50, 222),
(50, 30),
(50, 31),
(50, 32),
(50, 47),
(50, 48),
(50, 49),
(50, 59),
(50, 74),
(50, 85),
(50, 34),
(50, 139),
(50, 143),
(50, 149),
(50, 151),
(50, 152),
(50, 153),
(50, 157),
(50, 165),
(50, 177),
(50, 184),
(50, 185),
(50, 223),
(50, 224),
(50, 225),
(50, 22),
(50, 54),
(50, 79),
(50, 80),
(50, 101),
(50, 110),
(50, 111),
(50, 112),
(50, 119),
(50, 124),
(50, 154),
(50, 155),
(50, 158),
(50, 159),
(50, 161),
(50, 162),
(50, 164),
(50, 171),
(50, 172),
(50, 173),
(50, 174),
(50, 175),
(50, 206),
(50, 180),
(50, 163),
(50, 178),
(50, 199),
(50, 200),
(50, 201),
(50, 202),
(50, 226),
(50, 230),
(50, 233),
(50, 203),
(50, 204),
(50, 205),
(50, 211),
(50, 218),
(50, 219),
(50, 227),
(50, 228),
(50, 229),
(50, 231),
(51, 9),
(51, 10),
(51, 13),
(51, 14),
(51, 16),
(51, 42),
(51, 43),
(51, 44),
(51, 51),
(51, 52),
(51, 53),
(51, 58),
(51, 78),
(51, 108),
(51, 137),
(51, 138),
(51, 156),
(51, 220),
(51, 221),
(51, 234),
(51, 27),
(51, 37),
(51, 38),
(51, 61),
(51, 62),
(51, 63),
(51, 82),
(51, 142),
(51, 146),
(51, 150),
(51, 166),
(51, 170),
(51, 176),
(51, 179),
(51, 1),
(51, 3),
(51, 5),
(51, 7),
(51, 8),
(51, 11),
(51, 12),
(51, 15),
(51, 17),
(51, 36),
(51, 45),
(51, 46),
(51, 136),
(51, 2),
(51, 4),
(51, 6),
(51, 33),
(51, 55),
(51, 56),
(51, 57),
(51, 133),
(51, 134),
(51, 135),
(51, 181),
(51, 182),
(51, 183),
(51, 186),
(51, 187),
(51, 188),
(51, 189),
(51, 190),
(51, 191),
(51, 215),
(51, 216),
(51, 217),
(51, 18),
(51, 19),
(51, 20),
(51, 25),
(51, 26),
(51, 35),
(51, 39),
(51, 40),
(51, 41),
(51, 69),
(51, 70),
(51, 71),
(51, 81),
(51, 109),
(51, 132),
(51, 147),
(51, 169),
(51, 21),
(51, 23),
(51, 24),
(51, 116),
(51, 117),
(51, 50),
(51, 60),
(51, 141),
(51, 144),
(51, 145),
(51, 167),
(51, 192),
(51, 193),
(51, 194),
(51, 195),
(51, 196),
(51, 207),
(51, 208),
(51, 209),
(51, 210),
(51, 212),
(51, 213),
(51, 214),
(51, 222),
(51, 30),
(51, 31),
(51, 32),
(51, 47),
(51, 48),
(51, 49),
(51, 59),
(51, 74),
(51, 85),
(51, 34),
(51, 139),
(51, 143),
(51, 149),
(51, 151),
(51, 152),
(51, 153),
(51, 157),
(51, 165),
(51, 177),
(51, 184),
(51, 185),
(51, 223),
(51, 224),
(51, 225),
(51, 22),
(51, 54),
(51, 79),
(51, 80),
(51, 101),
(51, 110),
(51, 111),
(51, 112),
(51, 119),
(51, 124),
(51, 154),
(51, 155),
(51, 158),
(51, 159),
(51, 161),
(51, 162),
(51, 164),
(51, 171),
(51, 172),
(51, 173),
(51, 174),
(51, 175),
(51, 206),
(51, 180),
(51, 163),
(51, 178),
(51, 199),
(51, 200),
(51, 201),
(51, 202),
(51, 226),
(51, 230),
(51, 233),
(51, 203),
(51, 204),
(51, 205),
(51, 211),
(51, 218),
(51, 219),
(51, 227),
(51, 228),
(51, 229),
(51, 231),
(52, 9),
(52, 10),
(52, 13),
(52, 14),
(52, 16),
(52, 42),
(52, 43),
(52, 44),
(52, 51),
(52, 52),
(52, 53),
(52, 58),
(52, 78),
(52, 108),
(52, 137),
(52, 138),
(52, 156),
(52, 220),
(52, 221),
(52, 234),
(52, 27),
(52, 37),
(52, 38),
(52, 61),
(52, 62),
(52, 63),
(52, 82),
(52, 142),
(52, 146),
(52, 150),
(52, 166),
(52, 170),
(52, 176),
(52, 179),
(52, 1),
(52, 3),
(52, 5),
(52, 7),
(52, 8),
(52, 11),
(52, 12),
(52, 15),
(52, 17),
(52, 36),
(52, 45),
(52, 46),
(52, 136),
(52, 2),
(52, 4),
(52, 6),
(52, 33),
(52, 55),
(52, 56),
(52, 57),
(52, 133),
(52, 134),
(52, 135),
(52, 181),
(52, 182),
(52, 183),
(52, 186),
(52, 187),
(52, 188),
(52, 189),
(52, 190),
(52, 191),
(52, 215),
(52, 216),
(52, 217),
(52, 18),
(52, 19),
(52, 20),
(52, 25),
(52, 26),
(52, 35),
(52, 39),
(52, 40),
(52, 41),
(52, 69),
(52, 70),
(52, 71),
(52, 81),
(52, 109),
(52, 132),
(52, 147),
(52, 169),
(52, 21),
(52, 23),
(52, 24),
(52, 116),
(52, 117),
(52, 50),
(52, 60),
(52, 141),
(52, 144),
(52, 145),
(52, 167),
(52, 192),
(52, 193),
(52, 194),
(52, 195),
(52, 196),
(52, 207),
(52, 208),
(52, 209),
(52, 210),
(52, 212),
(52, 213),
(52, 214),
(52, 222),
(52, 30),
(52, 31),
(52, 32),
(52, 47),
(52, 48),
(52, 49),
(52, 59),
(52, 74),
(52, 85),
(52, 34),
(52, 139),
(52, 143),
(52, 149),
(52, 151),
(52, 152),
(52, 153),
(52, 157),
(52, 165),
(52, 177),
(52, 184),
(52, 185),
(52, 223),
(52, 224),
(52, 225),
(52, 22),
(52, 54),
(52, 79),
(52, 80),
(52, 101),
(52, 110),
(52, 111),
(52, 112),
(52, 119),
(52, 124),
(52, 154),
(52, 155),
(52, 158),
(52, 159),
(52, 161),
(52, 162),
(52, 164),
(52, 171),
(52, 172),
(52, 173),
(52, 174),
(52, 175),
(52, 206),
(52, 163),
(52, 178),
(52, 199),
(52, 200),
(52, 201),
(52, 202),
(52, 226),
(52, 230),
(52, 233),
(52, 203),
(52, 204),
(52, 205),
(52, 211),
(52, 218),
(52, 219),
(52, 227),
(52, 228),
(52, 229),
(52, 231),
(45, 17),
(45, 136),
(45, 215),
(45, 216),
(45, 217),
(45, 209),
(45, 213),
(45, 214),
(45, 222),
(45, 184),
(45, 223),
(45, 224),
(45, 225),
(45, 199),
(45, 201),
(45, 202),
(45, 226),
(45, 230),
(45, 233),
(45, 203),
(45, 205),
(45, 211),
(45, 218),
(45, 219),
(56, 9),
(56, 10),
(56, 13),
(56, 14),
(56, 16),
(56, 42),
(56, 43),
(56, 44),
(56, 51),
(56, 52),
(56, 53),
(56, 58),
(56, 78),
(56, 108),
(56, 137),
(56, 138),
(56, 156),
(56, 220),
(56, 221),
(56, 234),
(56, 235),
(56, 236),
(56, 237),
(56, 238),
(56, 239),
(56, 240),
(56, 241),
(56, 242),
(56, 243),
(56, 244),
(56, 245),
(56, 246),
(56, 247),
(56, 248),
(56, 249),
(56, 27),
(56, 37),
(56, 38),
(56, 61),
(56, 62),
(56, 63),
(56, 82),
(56, 142),
(56, 146),
(56, 150),
(56, 166),
(56, 170),
(56, 176),
(56, 179),
(56, 1),
(56, 3),
(56, 5),
(56, 7),
(56, 8),
(56, 11),
(56, 12),
(56, 15),
(56, 17),
(56, 36),
(56, 45),
(56, 46),
(56, 136),
(56, 2),
(56, 4),
(56, 6),
(56, 33),
(56, 55),
(56, 56),
(56, 57),
(56, 133),
(56, 134),
(56, 135),
(56, 181),
(56, 182),
(56, 183),
(56, 186),
(56, 187),
(56, 188),
(56, 189),
(56, 190),
(56, 191),
(56, 215),
(56, 216),
(56, 217),
(56, 18),
(56, 19),
(56, 20),
(56, 25),
(56, 26),
(56, 35),
(56, 39),
(56, 40),
(56, 41),
(56, 69),
(56, 70),
(56, 71),
(56, 81),
(56, 109),
(56, 132),
(56, 147),
(56, 169),
(56, 21),
(56, 23),
(56, 24),
(56, 116),
(56, 117),
(56, 50),
(56, 60),
(56, 141),
(56, 144),
(56, 145),
(56, 167),
(56, 192),
(56, 193),
(56, 194),
(56, 195),
(56, 196),
(56, 207),
(56, 208),
(56, 209),
(56, 210),
(56, 212),
(56, 213),
(56, 214),
(56, 222),
(56, 30),
(56, 31),
(56, 32),
(56, 47),
(56, 48),
(56, 49),
(56, 59),
(56, 74),
(56, 85),
(56, 34),
(56, 139),
(56, 143),
(56, 149),
(56, 151),
(56, 152),
(56, 153),
(56, 157),
(56, 165),
(56, 177),
(56, 184),
(56, 185),
(56, 223),
(56, 224),
(56, 225),
(56, 22),
(56, 54),
(56, 79),
(56, 80),
(56, 101),
(56, 110),
(56, 111),
(56, 112),
(56, 119),
(56, 124),
(56, 154),
(56, 155),
(56, 158),
(56, 159),
(56, 161),
(56, 162),
(56, 164),
(56, 171),
(56, 172),
(56, 173),
(56, 174),
(56, 175),
(56, 206),
(56, 180),
(56, 163),
(56, 178),
(56, 199),
(56, 200),
(56, 201),
(56, 202),
(56, 226),
(56, 230),
(56, 233),
(56, 203),
(56, 204),
(56, 205),
(56, 211),
(56, 218),
(56, 219),
(56, 227),
(56, 228),
(56, 229),
(56, 231),
(57, 9),
(57, 10),
(57, 13),
(57, 14),
(57, 16),
(57, 42),
(57, 43),
(57, 44),
(57, 51),
(57, 52),
(57, 53),
(57, 58),
(57, 78),
(57, 156),
(57, 235),
(57, 236),
(57, 238),
(57, 1),
(57, 3),
(57, 5),
(57, 7),
(57, 8),
(57, 11),
(57, 12),
(57, 15),
(57, 17),
(57, 36),
(57, 45),
(57, 46),
(57, 136),
(57, 2),
(57, 4),
(57, 6),
(57, 55),
(57, 56),
(57, 57),
(57, 133),
(57, 134),
(57, 135),
(57, 21),
(57, 23),
(57, 24),
(57, 116),
(57, 117),
(57, 144),
(57, 145),
(57, 167),
(57, 34),
(57, 139),
(57, 143),
(57, 149),
(57, 151),
(57, 152),
(57, 153),
(57, 157),
(57, 165),
(57, 177),
(57, 184),
(57, 185),
(57, 223),
(57, 224),
(57, 225),
(57, 180),
(57, 163),
(57, 178),
(57, 199),
(57, 200),
(57, 201),
(57, 202),
(57, 226),
(57, 230),
(57, 233),
(57, 203),
(57, 205),
(57, 211),
(57, 227),
(57, 228),
(2, 262),
(2, 263),
(2, 264),
(2, 265),
(2, 266),
(2, 267),
(2, 268),
(2, 269),
(2, 270);

-- --------------------------------------------------------

--
-- Table structure for table `user_role`
--

CREATE TABLE `user_role` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_role`
--

INSERT INTO `user_role` (`user_id`, `role_id`) VALUES
(35, 8),
(36, 8),
(28, 9),
(29, 9),
(30, 9),
(31, 9),
(32, 9),
(33, 9),
(34, 9),
(38, 9),
(15, 7),
(16, 15),
(13, 2),
(11, 5),
(14, 2),
(2, 14),
(26, 14),
(39, 7),
(9, 4),
(43, 7),
(44, 4),
(17, 11),
(42, 11),
(40, 12),
(45, 10),
(46, 4),
(46, 6),
(46, 12),
(46, 13),
(46, 15),
(6, 17),
(37, 8),
(47, 4),
(47, 6),
(47, 12),
(47, 13),
(47, 15),
(48, 4),
(2, 1),
(11, 1),
(26, 1),
(27, 1),
(46, 1),
(47, 1),
(50, 1),
(52, 1),
(53, 1),
(51, 1),
(51, 17),
(54, 1),
(54, 17),
(56, 1),
(56, 17),
(57, 5),
(55, 13),
(61, 1);

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_history`
--

CREATE TABLE `vehicle_history` (
  `vh_id` int(12) NOT NULL,
  `vh_date` varchar(35) DEFAULT NULL,
  `vh_time_start` varchar(30) DEFAULT NULL,
  `vh_time_end` varchar(30) DEFAULT NULL,
  `vh_location_start` varchar(255) DEFAULT NULL,
  `vh_location_end` varchar(255) DEFAULT NULL,
  `equipment_id` int(11) DEFAULT NULL,
  `vh_driver_name_ic_number` varchar(200) DEFAULT NULL,
  `driver_id` int(12) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `vehicle_history`
--

INSERT INTO `vehicle_history` (`vh_id`, `vh_date`, `vh_time_start`, `vh_time_end`, `vh_location_start`, `vh_location_end`, `equipment_id`, `vh_driver_name_ic_number`, `driver_id`) VALUES
(1, '2022-08-08', '11:14pm', '11:05pm', 'aa', 'bb', 7, '771029108112', 3),
(2, '2022-08-09', '02:15am', '12:15am', 'abc', 'xyz', 7, '811012107600', 4),
(3, '2022-08-08', '08:30am', '04:15pm', 'Negeri Sembilan Operation Office', 'Negeri Sembilan Operation Office', 4, '771029108112', 3),
(5, '2022-08-04', '08:00am', '04:30pm', 'Negeri Sembilan Operation Office', 'Melaka Operation Office', 8, '791012107112', 1),
(6, '2022-08-09', '08:00am', '04:30pm', 'Melaka Operation Office', 'Negeri Sembilan Operation Office', 8, '791012107112', 1);

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_history_asset`
--

CREATE TABLE `vehicle_history_asset` (
  `vh_id` int(12) NOT NULL,
  `vh_date` varchar(35) DEFAULT NULL,
  `vh_date_end` varchar(35) NOT NULL,
  `vh_time_start` varchar(30) DEFAULT NULL,
  `vh_time_end` varchar(30) DEFAULT NULL,
  `vh_location_start` varchar(255) DEFAULT NULL,
  `vh_location_end` varchar(255) DEFAULT NULL,
  `equipment_id` int(11) DEFAULT NULL,
  `vh_driver_name_ic_number` varchar(200) DEFAULT NULL,
  `driver_id` int(12) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `vehicle_history_asset`
--

INSERT INTO `vehicle_history_asset` (`vh_id`, `vh_date`, `vh_date_end`, `vh_time_start`, `vh_time_end`, `vh_location_start`, `vh_location_end`, `equipment_id`, `vh_driver_name_ic_number`, `driver_id`) VALUES
(1, '2022-08-09', '2022-08-09', '01:42am', '12:45am', 'North-South Expy, 34850 Changkat Jering, Perak, Malaysia', 'Lot Pt 679, Ulu Kangsar, 33700 Padang Rengas, Perak, Malaysia', 7, '781012108119', 2),
(2, '2022-08-09', '2022-08-09', '06:42am', '08:42am', 'Lot Pt 679, Ulu Kangsar, 33700 Padang Rengas, Perak, Malaysia', 'WILDROCK CAMPSITE PADANG RENGAS, 33700 Kuala Kangsar, Perak, Malaysia', 6, '781012108119', 2),
(3, '2022-08-02', '2022-08-02', '09:00am', '07:00pm', 'Melaka Operation Center', 'Kellogs (M)', 8, '771029108112', 3),
(4, '2022-08-03', '2022-08-03', '09:00am', '09:00pm', 'North Johor Operation Office', 'Bintang 3 Sdn Bhd', 1, '781012108119', 2),
(6, '2022-08-05', '2022-08-09', '09:00am', '04:00pm', 'Negeri Sembilan Operation Office', 'Dutch Lady (M)', 8, '781012108119', 2);

-- --------------------------------------------------------

--
-- Table structure for table `vessels`
--

CREATE TABLE `vessels` (
  `vessel_id` int(8) UNSIGNED NOT NULL,
  `vessel_name` varchar(200) NOT NULL,
  `vessel_imo` varchar(9) DEFAULT NULL,
  `vessel_call_sign` varchar(12) DEFAULT NULL,
  `vessel_type` enum('CONTAINER','GENERAL CARGO','BULK','PASSENGER','CAR CARRIER','ORE','OTHER','RORO/REEFER CARRIER','CHEMICAL','LIQUIFIED PETROLEUM GAS TANKER','MULTI-PURPOSE(SEMI CONT.)','TANKER (CRUDE,FUEL,DIESEL,LUB)','TUG','BULKER','LIQUIFIED NATURAL GAS TANKER','LIQUIFIED PETROLEUM  GAS TANKER','GENERAL CAGRO','TANKER (EDIBLE OIL, SEWAGE)') DEFAULT NULL,
  `vessel_tonnage` decimal(10,0) DEFAULT NULL,
  `vessel_flag` int(5) UNSIGNED DEFAULT NULL,
  `active` int(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `vessel_cranes`
--

CREATE TABLE `vessel_cranes` (
  `vessel_crane_id` int(10) NOT NULL,
  `vessel_id` int(10) UNSIGNED DEFAULT NULL,
  `crane_name` varchar(40) DEFAULT NULL,
  `crane_safeload` text,
  `deleted` int(1) NOT NULL DEFAULT '0',
  `t_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `vessel_hatches`
--

CREATE TABLE `vessel_hatches` (
  `vessel_hatch_id` int(10) NOT NULL,
  `vessel_id` int(10) UNSIGNED DEFAULT NULL,
  `hatch_name` varchar(40) DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  `t_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `vessel_visits`
--

CREATE TABLE `vessel_visits` (
  `vessel_visit_id` int(10) UNSIGNED NOT NULL,
  `vessel_id` int(8) UNSIGNED NOT NULL,
  `visit_scn` varchar(10) DEFAULT NULL,
  `visit_voyage` varchar(20) DEFAULT NULL,
  `vessel_carrier` varchar(200) DEFAULT NULL,
  `port_wharf_id` int(10) UNSIGNED DEFAULT NULL,
  `port_next` int(10) UNSIGNED DEFAULT NULL,
  `visit_eta` datetime DEFAULT NULL,
  `visit_etd` datetime DEFAULT NULL,
  `visit_ata` datetime DEFAULT NULL,
  `visit_atd` datetime DEFAULT NULL,
  `operation_started` datetime DEFAULT NULL,
  `operation_ended` datetime DEFAULT NULL,
  `planning_status` enum('new','approved','rejected','started','ended') DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  `t_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `active` int(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `vessel_visit_equipments`
--

CREATE TABLE `vessel_visit_equipments` (
  `vessel_visit_equipment_id` int(10) NOT NULL,
  `vessel_visit_id` int(10) DEFAULT NULL,
  `equipment_id` int(10) DEFAULT NULL,
  `operation_date` date DEFAULT NULL,
  `gang` int(1) DEFAULT NULL,
  `shift` int(1) DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `vessel_visit_equipment_confirmation`
--

CREATE TABLE `vessel_visit_equipment_confirmation` (
  `vessel_visit_equipment_confirmation_id` int(10) NOT NULL,
  `vessel_visit_equipment_id` int(10) NOT NULL,
  `vessel_visit_equipment_confirmation_date` date DEFAULT NULL,
  `vessel_visit_equipment_confirmation_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `vessel_visit_gears`
--

CREATE TABLE `vessel_visit_gears` (
  `vessel_visit_gear_id` int(10) NOT NULL,
  `vessel_visit_id` int(10) DEFAULT NULL,
  `gear_id` int(10) DEFAULT NULL,
  `operation_date` date DEFAULT NULL,
  `gang` int(1) DEFAULT NULL,
  `shift` int(1) DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `vessel_visit_gear_confirmation`
--

CREATE TABLE `vessel_visit_gear_confirmation` (
  `vessel_visit_gear_confirmation_id` int(10) NOT NULL,
  `vessel_visit_gear_id` int(10) NOT NULL,
  `vessel_visit_gear_confirmation_date` date DEFAULT NULL,
  `vessel_visit_gear_confirmation_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `vessel_visit_workers`
--

CREATE TABLE `vessel_visit_workers` (
  `vessel_visit_workers_id` int(11) NOT NULL,
  `vessel_visit_id` int(10) DEFAULT NULL,
  `worker_id` int(10) DEFAULT NULL,
  `worker_resource_type_override` int(10) DEFAULT NULL,
  `operation_date` date DEFAULT NULL,
  `gang` int(1) DEFAULT NULL,
  `shift` int(1) DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `worker_confirmed` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `warehouse_equipments`
--

CREATE TABLE `warehouse_equipments` (
  `company_address` int(10) DEFAULT NULL,
  `service_request_id` int(10) DEFAULT NULL,
  `equipment_id` int(10) DEFAULT NULL,
  `operation_date` date DEFAULT NULL,
  `gang` int(1) DEFAULT NULL,
  `shift` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `warehouse_gears`
--

CREATE TABLE `warehouse_gears` (
  `company_address` int(10) DEFAULT NULL,
  `service_request_id` int(10) DEFAULT NULL,
  `gear_id` int(10) DEFAULT NULL,
  `operation_date` date DEFAULT NULL,
  `gang` int(1) DEFAULT NULL,
  `shift` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `warehouse_workers`
--

CREATE TABLE `warehouse_workers` (
  `company_address` int(10) DEFAULT NULL,
  `service_request_id` int(10) DEFAULT NULL,
  `worker_id` int(10) DEFAULT NULL,
  `operation_date` date DEFAULT NULL,
  `gang` int(1) DEFAULT NULL,
  `shift` int(1) DEFAULT NULL,
  `worker_confirmed` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `wastage_types`
--

CREATE TABLE `wastage_types` (
  `wastage_type_id` int(11) NOT NULL,
  `wastage_type_name` varchar(100) DEFAULT NULL,
  `description` text,
  `active` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `wastage_types`
--

INSERT INTO `wastage_types` (`wastage_type_id`, `wastage_type_name`, `description`, `active`) VALUES
(1, 'General Waste', '', 1),
(2, 'Dangerous Waste', '', 1),
(3, 'Recycle Waste', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `workers`
--

CREATE TABLE `workers` (
  `worker_id` int(10) UNSIGNED NOT NULL,
  `worker_name` varchar(200) DEFAULT NULL,
  `worker_order` int(6) DEFAULT NULL,
  `worker_type` enum('casual-daily','contract-daily','contract-monthly','permanent-office','permanent-ops','van-driver') DEFAULT NULL,
  `work_rate_override` decimal(10,5) DEFAULT NULL,
  `standby_rate_override` decimal(10,5) DEFAULT NULL,
  `leave_override` int(50) DEFAULT NULL,
  `medical_leave_override` int(50) DEFAULT NULL,
  `bank_account` varchar(40) DEFAULT NULL,
  `ic_number` varchar(20) DEFAULT NULL,
  `address` text,
  `worker_resource_type` varchar(40) DEFAULT NULL,
  `worker_secondary_resource_type` varchar(60) DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  `contact_number` varchar(30) DEFAULT NULL,
  `id_lcm` varchar(6) DEFAULT NULL,
  `id_samalaju` varchar(6) DEFAULT NULL,
  `shift_1` varchar(14) DEFAULT NULL,
  `shift_2` varchar(14) DEFAULT NULL,
  `max_ot_hours` int(2) DEFAULT NULL,
  `worker_photo` varchar(255) DEFAULT NULL,
  `worker_notes` text,
  `payment_effective` date DEFAULT NULL,
  `monthly_allowance` decimal(10,2) DEFAULT NULL,
  `last_worked` date DEFAULT NULL,
  `active` int(1) NOT NULL DEFAULT '1',
  `t_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ext_age` varchar(100) DEFAULT NULL,
  `ext_address` text,
  `ext_work_hours` double(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `workers`
--

INSERT INTO `workers` (`worker_id`, `worker_name`, `worker_order`, `worker_type`, `work_rate_override`, `standby_rate_override`, `leave_override`, `medical_leave_override`, `bank_account`, `ic_number`, `address`, `worker_resource_type`, `worker_secondary_resource_type`, `joining_date`, `contact_number`, `id_lcm`, `id_samalaju`, `shift_1`, `shift_2`, `max_ot_hours`, `worker_photo`, `worker_notes`, `payment_effective`, `monthly_allowance`, `last_worked`, `active`, `t_updated`, `ext_age`, `ext_address`, `ext_work_hours`) VALUES
(1, 'Kassim bin Selamat', NULL, 'contract-monthly', NULL, NULL, NULL, NULL, NULL, '791012107112', NULL, '3', '4', NULL, '0172557676', NULL, NULL, NULL, NULL, 0, '1660044393-Screenshot 2022-08-06 at 11.10.38 PM.png', NULL, NULL, NULL, NULL, 1, '2022-08-09 23:16:39', '43', 'aaaaaa', 8.00),
(2, 'Johan bin Setia ', NULL, 'contract-monthly', NULL, NULL, NULL, NULL, NULL, '781012108119', NULL, '1', '3', NULL, '0197665552', NULL, NULL, NULL, NULL, NULL, '1660044411-Screenshot 2022-08-06 at 11.10.20 PM.png', NULL, NULL, NULL, NULL, 1, '2022-08-09 23:17:26', '44', '', 8.00),
(3, 'Ali bin Ahmad', NULL, 'contract-monthly', NULL, NULL, NULL, NULL, NULL, '771029108112', NULL, '1', '3', NULL, '0197757522', NULL, NULL, NULL, NULL, NULL, '1660044426-Screenshot 2022-08-06 at 11.10.15 PM.png', NULL, NULL, NULL, NULL, 1, '2022-08-09 14:30:35', '45', '', 8.00),
(4, 'Salim bin Mahmud', NULL, 'contract-monthly', NULL, NULL, NULL, NULL, NULL, '811012107600', NULL, '4', '3', NULL, '01141083844', NULL, NULL, NULL, NULL, NULL, '1660044438-Screenshot 2022-08-06 at 11.10.02 PM.png', NULL, NULL, NULL, NULL, 1, '2022-08-09 11:32:27', '41', '11 LORONG MERLIMAU\r\nTELUK PULAI, KLANG , SELANGOR, MALAYSIA', 8.00);

-- --------------------------------------------------------

--
-- Table structure for table `worker_allowances`
--

CREATE TABLE `worker_allowances` (
  `worker_allowance_id` int(10) NOT NULL,
  `worker_id` int(10) DEFAULT NULL,
  `month` varchar(7) DEFAULT NULL,
  `allowance_amount` decimal(10,2) DEFAULT NULL,
  `t_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `worker_availability`
--

CREATE TABLE `worker_availability` (
  `worker_availability_id` int(11) NOT NULL,
  `worker_id` int(10) UNSIGNED NOT NULL,
  `worker_availability` int(1) DEFAULT '1',
  `worker_attendance` varchar(2) DEFAULT NULL,
  `availability_date` date NOT NULL,
  `worker_shift` int(1) DEFAULT NULL,
  `worker_group` int(10) DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  `work_start` datetime DEFAULT NULL,
  `work_end` datetime DEFAULT NULL,
  `onsite_start` datetime DEFAULT NULL,
  `onsite_end` datetime DEFAULT NULL,
  `worker_location` int(10) DEFAULT NULL,
  `work_lop` int(2) DEFAULT NULL,
  `work_standby` int(1) NOT NULL DEFAULT '1',
  `vessel_name` varchar(200) DEFAULT NULL,
  `work_overtime_before` int(2) DEFAULT NULL,
  `work_overtime_after` int(2) DEFAULT NULL,
  `pay_hours` int(2) DEFAULT NULL,
  `pay_rate` decimal(10,5) DEFAULT NULL,
  `lop_hours` int(2) DEFAULT NULL,
  `lop_rate` decimal(10,5) DEFAULT NULL,
  `pay_amount` decimal(10,5) DEFAULT NULL,
  `lop_amount` decimal(10,5) DEFAULT NULL,
  `ot_amount` decimal(10,5) DEFAULT NULL,
  `ot_rate` decimal(10,5) DEFAULT NULL,
  `ot_hours` int(2) DEFAULT NULL,
  `ph_pay` decimal(10,5) DEFAULT NULL,
  `ph_ot` decimal(10,5) DEFAULT NULL,
  `rd_pay` decimal(10,5) DEFAULT NULL,
  `rd_ot` decimal(10,5) DEFAULT NULL,
  `work_through_meals` int(10) DEFAULT NULL,
  `work_through_meals_pay` decimal(10,5) DEFAULT NULL,
  `overtime_pending` int(1) NOT NULL DEFAULT '0',
  `overtime_preapproved` int(2) DEFAULT NULL,
  `attendance_processed` int(1) NOT NULL DEFAULT '0',
  `remarks` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `worker_availability`
--

INSERT INTO `worker_availability` (`worker_availability_id`, `worker_id`, `worker_availability`, `worker_attendance`, `availability_date`, `worker_shift`, `worker_group`, `deleted`, `work_start`, `work_end`, `onsite_start`, `onsite_end`, `worker_location`, `work_lop`, `work_standby`, `vessel_name`, `work_overtime_before`, `work_overtime_after`, `pay_hours`, `pay_rate`, `lop_hours`, `lop_rate`, `pay_amount`, `lop_amount`, `ot_amount`, `ot_rate`, `ot_hours`, `ph_pay`, `ph_ot`, `rd_pay`, `rd_ot`, `work_through_meals`, `work_through_meals_pay`, `overtime_pending`, `overtime_preapproved`, `attendance_processed`, `remarks`) VALUES
(1, 1, 1, 'P', '2022-08-08', 2, 2, 0, '2022-08-08 07:30:00', '2022-08-08 16:30:00', NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'aaa'),
(2, 1, 1, 'ML', '1970-01-01', 2, 2, 0, '2022-08-09 00:00:00', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, ''),
(3, 1, 1, 'P', '2022-08-07', 1, 2, 0, '2022-08-07 08:30:00', '2022-08-07 14:30:00', NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'bb'),
(4, 1, 1, 'XL', '2022-08-06', 2, 2, 0, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'nn'),
(5, 4, 1, 'P', '2022-08-09', 1, 3, 0, '2022-08-09 07:30:00', '2022-08-09 16:30:00', NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, ''),
(6, 4, 1, 'P', '2022-08-08', 1, 3, 0, '2022-08-08 07:30:00', '2022-08-08 16:30:00', NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, ''),
(7, 4, 1, 'P', '2022-08-07', 1, 3, 1, '2022-08-07 07:30:00', '2022-08-07 16:30:00', NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, ''),
(8, 2, 1, 'P', '2022-08-08', 1, 4, 0, '2022-08-08 07:30:00', '2022-08-08 16:30:00', NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, ''),
(9, 2, 1, 'P', '2022-08-09', 1, 4, 0, '2022-08-09 07:30:00', '2022-08-09 16:30:00', NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, ''),
(10, 3, 1, 'P', '2022-08-08', 1, 4, 0, '2022-08-08 07:30:00', '2022-08-08 16:30:00', NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, ''),
(11, 3, 1, 'P', '2022-08-09', 1, 4, 0, '2022-08-09 07:30:00', '2022-08-09 16:30:00', NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, ''),
(12, 1, 1, 'P', '2022-08-09', 1, 4, 0, '2022-08-09 07:30:00', '2022-08-09 16:30:00', NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `worker_biometrics`
--

CREATE TABLE `worker_biometrics` (
  `worker_id` int(10) UNSIGNED NOT NULL,
  `biometric_1` text,
  `biometric_2` text,
  `t_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `worker_group`
--

CREATE TABLE `worker_group` (
  `worker_id` int(10) UNSIGNED NOT NULL,
  `worker_group_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `worker_group`
--

INSERT INTO `worker_group` (`worker_id`, `worker_group_id`) VALUES
(2, 4),
(3, 4),
(1, 6),
(1, 3),
(1, 4),
(4, 5),
(4, 3),
(4, 7);

-- --------------------------------------------------------

--
-- Table structure for table `worker_groups`
--

CREATE TABLE `worker_groups` (
  `worker_group_id` int(10) UNSIGNED NOT NULL,
  `worker_group_name` varchar(200) DEFAULT NULL,
  `worker_group_code` varchar(20) DEFAULT NULL,
  `payroll_start` int(2) DEFAULT NULL,
  `worker_group_notes` text,
  `active` int(1) NOT NULL DEFAULT '1',
  `t_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `worker_groups`
--

INSERT INTO `worker_groups` (`worker_group_id`, `worker_group_name`, `worker_group_code`, `payroll_start`, `worker_group_notes`, `active`, `t_updated`) VALUES
(2, 'TEAM B', 'TB', NULL, 'Drivers under supervision Gopal Manickam', 1, '2022-08-09 11:31:20'),
(3, 'TEAM A', 'TA', NULL, 'Drivers under supervision on Tan Lee Weng', 1, '2022-08-09 11:30:02'),
(4, 'TEAM C', 'TC', NULL, 'Drivers under supervision of Alex Lim', 1, '2022-08-09 11:31:07');

-- --------------------------------------------------------

--
-- Table structure for table `worker_group_allocation`
--

CREATE TABLE `worker_group_allocation` (
  `worker_group_id` int(10) UNSIGNED NOT NULL,
  `availability_date` date NOT NULL,
  `worker_group_shift` int(1) DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  `public_holiday` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `worker_group_allocation`
--

INSERT INTO `worker_group_allocation` (`worker_group_id`, `availability_date`, `worker_group_shift`, `deleted`, `public_holiday`) VALUES
(2, '2022-08-08', 9, 0, 1),
(2, '2022-08-09', 9, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `worker_locations`
--

CREATE TABLE `worker_locations` (
  `worker_location_id` int(10) UNSIGNED NOT NULL,
  `worker_location_name` varchar(50) NOT NULL,
  `description` mediumtext NOT NULL,
  `active` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `worker_locations`
--

INSERT INTO `worker_locations` (`worker_location_id`, `worker_location_name`, `description`, `active`) VALUES
(1, 'Negeri Sembilan Operation Office', '', 1),
(2, 'Melaka State Operation Office', '', 1),
(3, 'North Johor Operation Office', '', 1),
(4, 'South Johor Operation Office', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `worker_tenure`
--

CREATE TABLE `worker_tenure` (
  `worker_id` int(10) NOT NULL,
  `tenure_action` enum('joined','quit') NOT NULL,
  `tenure_date` date NOT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  `t_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alerts`
--
ALTER TABLE `alerts`
  ADD PRIMARY KEY (`alert_id`),
  ADD KEY `bills_of_lading_id` (`alert_bills_of_lading_id`),
  ADD KEY `booking_id` (`alert_booking_id`),
  ADD KEY `container_release_orders_id` (`alert_container_release_order_id`),
  ADD KEY `notices_of_arrival_id` (`alert_notices_of_arrival_id`),
  ADD KEY `quotation_id` (`alert_quotation_id`),
  ADD KEY `branch_id` (`branch_id`);

--
-- Indexes for table `billing_resources`
--
ALTER TABLE `billing_resources`
  ADD PRIMARY KEY (`billing_resources_id`);

--
-- Indexes for table `cargo_packagings`
--
ALTER TABLE `cargo_packagings`
  ADD PRIMARY KEY (`cargo_packaging_id`);

--
-- Indexes for table `cargo_types`
--
ALTER TABLE `cargo_types`
  ADD PRIMARY KEY (`cargo_type_id`);

--
-- Indexes for table `charges`
--
ALTER TABLE `charges`
  ADD PRIMARY KEY (`charge_id`),
  ADD UNIQUE KEY `charge_code` (`charge_code`);

--
-- Indexes for table `commodities`
--
ALTER TABLE `commodities`
  ADD PRIMARY KEY (`commodity_id`),
  ADD UNIQUE KEY `commodity_code` (`commodity_code`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`company_id`),
  ADD UNIQUE KEY `merchant_name` (`company_name`);

--
-- Indexes for table `company_addresses`
--
ALTER TABLE `company_addresses`
  ADD PRIMARY KEY (`company_address_id`),
  ADD KEY `designation` (`designation`),
  ADD KEY `address_country` (`address_country`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `company_prices`
--
ALTER TABLE `company_prices`
  ADD PRIMARY KEY (`company_price_id`);

--
-- Indexes for table `configs`
--
ALTER TABLE `configs`
  ADD PRIMARY KEY (`config_id`),
  ADD UNIQUE KEY `config_name` (`config_name`);

--
-- Indexes for table `consumables`
--
ALTER TABLE `consumables`
  ADD PRIMARY KEY (`consumable_id`);

--
-- Indexes for table `consumable_purchases`
--
ALTER TABLE `consumable_purchases`
  ADD PRIMARY KEY (`consumable_purchase_id`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`country_id`),
  ADD KEY `code` (`code`),
  ADD KEY `countrycode` (`countrycode`);

--
-- Indexes for table `delay_reasons`
--
ALTER TABLE `delay_reasons`
  ADD PRIMARY KEY (`delay_reason_id`);

--
-- Indexes for table `designations`
--
ALTER TABLE `designations`
  ADD PRIMARY KEY (`designation_id`);

--
-- Indexes for table `equipments`
--
ALTER TABLE `equipments`
  ADD PRIMARY KEY (`equipment_id`);

--
-- Indexes for table `equipments_asset`
--
ALTER TABLE `equipments_asset`
  ADD PRIMARY KEY (`equipment_id`);

--
-- Indexes for table `equipment_consumables`
--
ALTER TABLE `equipment_consumables`
  ADD PRIMARY KEY (`equipment_consumable_id`);

--
-- Indexes for table `equipment_consumables_asset`
--
ALTER TABLE `equipment_consumables_asset`
  ADD PRIMARY KEY (`equipment_consumable_id`);

--
-- Indexes for table `equipment_groups`
--
ALTER TABLE `equipment_groups`
  ADD PRIMARY KEY (`equipment_group_id`);

--
-- Indexes for table `equipment_groups_asset`
--
ALTER TABLE `equipment_groups_asset`
  ADD PRIMARY KEY (`equipment_group_id`);

--
-- Indexes for table `equipment_maintenance`
--
ALTER TABLE `equipment_maintenance`
  ADD PRIMARY KEY (`equipment_maintenance_id`);

--
-- Indexes for table `equipment_maintenance_asset`
--
ALTER TABLE `equipment_maintenance_asset`
  ADD PRIMARY KEY (`equipment_maintenance_id`);

--
-- Indexes for table `equipment_mileage`
--
ALTER TABLE `equipment_mileage`
  ADD PRIMARY KEY (`equipment_mileage_id`);

--
-- Indexes for table `equipment_mileage_asset`
--
ALTER TABLE `equipment_mileage_asset`
  ADD PRIMARY KEY (`equipment_mileage_id`);

--
-- Indexes for table `equipment_types`
--
ALTER TABLE `equipment_types`
  ADD PRIMARY KEY (`equipment_type_id`);

--
-- Indexes for table `equipment_types_asset`
--
ALTER TABLE `equipment_types_asset`
  ADD PRIMARY KEY (`equipment_type_id`);

--
-- Indexes for table `exchange_rates`
--
ALTER TABLE `exchange_rates`
  ADD PRIMARY KEY (`exchange_rate_id`),
  ADD KEY `date_2` (`date`);

--
-- Indexes for table `gears`
--
ALTER TABLE `gears`
  ADD PRIMARY KEY (`gear_id`);

--
-- Indexes for table `gear_purchases`
--
ALTER TABLE `gear_purchases`
  ADD PRIMARY KEY (`gear_purchase_id`);

--
-- Indexes for table `gear_types`
--
ALTER TABLE `gear_types`
  ADD PRIMARY KEY (`gear_type_id`);

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
-- Indexes for table `incident_types`
--
ALTER TABLE `incident_types`
  ADD PRIMARY KEY (`incident_type_id`),
  ADD UNIQUE KEY `incident_type` (`incident_type`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`invoice_id`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`log_user_id`),
  ADD KEY `log_code` (`log_code`),
  ADD KEY `timestamp` (`timestamp`),
  ADD KEY `log_item_table` (`log_item_table`,`log_item_id`);

--
-- Indexes for table `manufacturers`
--
ALTER TABLE `manufacturers`
  ADD PRIMARY KEY (`manufacturer_id`);

--
-- Indexes for table `masters_companies`
--
ALTER TABLE `masters_companies`
  ADD PRIMARY KEY (`company_id`),
  ADD UNIQUE KEY `company_name` (`company_name`,`registration_id`);

--
-- Indexes for table `message_views`
--
ALTER TABLE `message_views`
  ADD KEY `user_id` (`user_id`),
  ADD KEY `record_id` (`record_id`),
  ADD KEY `table_name` (`table_name`);

--
-- Indexes for table `notices_of_arrival`
--
ALTER TABLE `notices_of_arrival`
  ADD PRIMARY KEY (`notices_of_arrival_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `notices_of_arrival_remarks`
--
ALTER TABLE `notices_of_arrival_remarks`
  ADD PRIMARY KEY (`notices_of_arrival_remark_id`),
  ADD KEY `notices_of_arrival_id` (`notices_of_arrival_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `operation_types`
--
ALTER TABLE `operation_types`
  ADD PRIMARY KEY (`operation_type_id`);

--
-- Indexes for table `operators`
--
ALTER TABLE `operators`
  ADD PRIMARY KEY (`operator_id`),
  ADD UNIQUE KEY `operator_code` (`operator_code`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`perm_id`),
  ADD UNIQUE KEY `perm_name` (`perm_name`),
  ADD KEY `perm_cat` (`perm_cat_id`);

--
-- Indexes for table `permission_categories`
--
ALTER TABLE `permission_categories`
  ADD PRIMARY KEY (`perm_cat_id`);

--
-- Indexes for table `ports`
--
ALTER TABLE `ports`
  ADD PRIMARY KEY (`port_id`),
  ADD KEY `port_code` (`port_code`);

--
-- Indexes for table `port_wharfs`
--
ALTER TABLE `port_wharfs`
  ADD PRIMARY KEY (`port_wharf_id`),
  ADD KEY `branch_id` (`wharf_id`),
  ADD KEY `port_id` (`port_id`);

--
-- Indexes for table `public_holidays`
--
ALTER TABLE `public_holidays`
  ADD PRIMARY KEY (`public_holiday_id`);

--
-- Indexes for table `rebundling_colours`
--
ALTER TABLE `rebundling_colours`
  ADD PRIMARY KEY (`rebundling_colour_id`);

--
-- Indexes for table `resource_types`
--
ALTER TABLE `resource_types`
  ADD PRIMARY KEY (`resource_type_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD KEY `role_id` (`role_id`),
  ADD KEY `perm_id` (`perm_id`);

--
-- Indexes for table `service_requests`
--
ALTER TABLE `service_requests`
  ADD PRIMARY KEY (`service_request_id`),
  ADD UNIQUE KEY `container_number` (`service_request_number`),
  ADD KEY `container_number_2` (`service_request_number`),
  ADD KEY `type_size` (`service_request_type`);

--
-- Indexes for table `service_request_attachments`
--
ALTER TABLE `service_request_attachments`
  ADD PRIMARY KEY (`service_request_attachment_id`),
  ADD KEY `service_request_id` (`service_request_id`);

--
-- Indexes for table `service_request_billing`
--
ALTER TABLE `service_request_billing`
  ADD PRIMARY KEY (`service_request_billing_id`);

--
-- Indexes for table `service_request_disposals`
--
ALTER TABLE `service_request_disposals`
  ADD PRIMARY KEY (`service_request_disposal_id`),
  ADD KEY `service_request_id` (`service_request_id`);

--
-- Indexes for table `service_request_disposal_tally`
--
ALTER TABLE `service_request_disposal_tally`
  ADD PRIMARY KEY (`service_request_disposal_tally_id`);

--
-- Indexes for table `service_request_equipment_types`
--
ALTER TABLE `service_request_equipment_types`
  ADD PRIMARY KEY (`service_request_equipment_id`);

--
-- Indexes for table `service_request_gear_types`
--
ALTER TABLE `service_request_gear_types`
  ADD PRIMARY KEY (`service_request_gear_id`);

--
-- Indexes for table `service_request_operations`
--
ALTER TABLE `service_request_operations`
  ADD PRIMARY KEY (`service_request_operation_id`),
  ADD KEY `service_request_id` (`service_request_id`);

--
-- Indexes for table `service_request_operation_tally`
--
ALTER TABLE `service_request_operation_tally`
  ADD PRIMARY KEY (`service_request_operation_tally_id`);

--
-- Indexes for table `service_request_remarks`
--
ALTER TABLE `service_request_remarks`
  ADD PRIMARY KEY (`service_request_remark_id`),
  ADD KEY `quotation_id` (`service_request_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `service_request_resource_types`
--
ALTER TABLE `service_request_resource_types`
  ADD PRIMARY KEY (`service_request_resource_id`);

--
-- Indexes for table `service_vouchers`
--
ALTER TABLE `service_vouchers`
  ADD PRIMARY KEY (`service_voucher_id`);

--
-- Indexes for table `shipment_terms`
--
ALTER TABLE `shipment_terms`
  ADD PRIMARY KEY (`shipment_term_id`),
  ADD UNIQUE KEY `shipment_term_name` (`shipment_term_name`);

--
-- Indexes for table `tally_remarks`
--
ALTER TABLE `tally_remarks`
  ADD PRIMARY KEY (`tally_remark_id`);

--
-- Indexes for table `task_runs`
--
ALTER TABLE `task_runs`
  ADD PRIMARY KEY (`task_run_id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `ts` (`ts`);

--
-- Indexes for table `templates`
--
ALTER TABLE `templates`
  ADD PRIMARY KEY (`template_id`),
  ADD KEY `template_name` (`template_name`);

--
-- Indexes for table `timezones`
--
ALTER TABLE `timezones`
  ADD PRIMARY KEY (`timezone_id`),
  ADD KEY `country_code` (`country_code`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `designation` (`designation`),
  ADD KEY `user_group` (`user_group`),
  ADD KEY `country` (`address_country`),
  ADD KEY `session` (`session`),
  ADD KEY `active_branch` (`active_branch`);

--
-- Indexes for table `user_branch`
--
ALTER TABLE `user_branch`
  ADD KEY `user_id` (`user_id`),
  ADD KEY `branch_id` (`branch_id`);

--
-- Indexes for table `user_groups`
--
ALTER TABLE `user_groups`
  ADD PRIMARY KEY (`user_group_id`);

--
-- Indexes for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD KEY `perm_id` (`perm_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_role`
--
ALTER TABLE `user_role`
  ADD KEY `user_id` (`user_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `vehicle_history`
--
ALTER TABLE `vehicle_history`
  ADD PRIMARY KEY (`vh_id`);

--
-- Indexes for table `vehicle_history_asset`
--
ALTER TABLE `vehicle_history_asset`
  ADD PRIMARY KEY (`vh_id`);

--
-- Indexes for table `vessels`
--
ALTER TABLE `vessels`
  ADD PRIMARY KEY (`vessel_id`),
  ADD KEY `vessel_flag` (`vessel_flag`);

--
-- Indexes for table `vessel_cranes`
--
ALTER TABLE `vessel_cranes`
  ADD PRIMARY KEY (`vessel_crane_id`),
  ADD KEY `vessel_id` (`vessel_id`);

--
-- Indexes for table `vessel_hatches`
--
ALTER TABLE `vessel_hatches`
  ADD PRIMARY KEY (`vessel_hatch_id`),
  ADD KEY `vessel_id` (`vessel_id`);

--
-- Indexes for table `vessel_visits`
--
ALTER TABLE `vessel_visits`
  ADD PRIMARY KEY (`vessel_visit_id`),
  ADD KEY `vessel_id` (`vessel_id`),
  ADD KEY `port_from` (`port_wharf_id`),
  ADD KEY `port_next` (`port_next`),
  ADD KEY `visit_eta` (`visit_eta`,`visit_etd`,`visit_ata`,`visit_atd`);

--
-- Indexes for table `vessel_visit_equipments`
--
ALTER TABLE `vessel_visit_equipments`
  ADD PRIMARY KEY (`vessel_visit_equipment_id`),
  ADD KEY `operation_date` (`operation_date`,`equipment_id`);

--
-- Indexes for table `vessel_visit_equipment_confirmation`
--
ALTER TABLE `vessel_visit_equipment_confirmation`
  ADD PRIMARY KEY (`vessel_visit_equipment_confirmation_id`);

--
-- Indexes for table `vessel_visit_gears`
--
ALTER TABLE `vessel_visit_gears`
  ADD PRIMARY KEY (`vessel_visit_gear_id`),
  ADD KEY `operation_date` (`operation_date`,`gear_id`);

--
-- Indexes for table `vessel_visit_gear_confirmation`
--
ALTER TABLE `vessel_visit_gear_confirmation`
  ADD PRIMARY KEY (`vessel_visit_gear_confirmation_id`);

--
-- Indexes for table `vessel_visit_workers`
--
ALTER TABLE `vessel_visit_workers`
  ADD PRIMARY KEY (`vessel_visit_workers_id`),
  ADD KEY `operation_date` (`operation_date`,`worker_id`);

--
-- Indexes for table `warehouse_equipments`
--
ALTER TABLE `warehouse_equipments`
  ADD KEY `operation_date` (`operation_date`,`equipment_id`);

--
-- Indexes for table `warehouse_gears`
--
ALTER TABLE `warehouse_gears`
  ADD KEY `operation_date` (`operation_date`,`gear_id`);

--
-- Indexes for table `warehouse_workers`
--
ALTER TABLE `warehouse_workers`
  ADD KEY `operation_date` (`operation_date`,`worker_id`);

--
-- Indexes for table `wastage_types`
--
ALTER TABLE `wastage_types`
  ADD PRIMARY KEY (`wastage_type_id`);

--
-- Indexes for table `workers`
--
ALTER TABLE `workers`
  ADD PRIMARY KEY (`worker_id`);

--
-- Indexes for table `worker_allowances`
--
ALTER TABLE `worker_allowances`
  ADD PRIMARY KEY (`worker_allowance_id`);

--
-- Indexes for table `worker_availability`
--
ALTER TABLE `worker_availability`
  ADD PRIMARY KEY (`worker_availability_id`),
  ADD KEY `user_id` (`worker_id`),
  ADD KEY `role_id` (`worker_availability`),
  ADD KEY `availability_date` (`availability_date`);

--
-- Indexes for table `worker_biometrics`
--
ALTER TABLE `worker_biometrics`
  ADD KEY `worker_id` (`worker_id`);

--
-- Indexes for table `worker_group`
--
ALTER TABLE `worker_group`
  ADD KEY `user_id` (`worker_id`),
  ADD KEY `role_id` (`worker_group_id`);

--
-- Indexes for table `worker_groups`
--
ALTER TABLE `worker_groups`
  ADD PRIMARY KEY (`worker_group_id`);

--
-- Indexes for table `worker_group_allocation`
--
ALTER TABLE `worker_group_allocation`
  ADD KEY `worker_group_id` (`worker_group_id`,`availability_date`,`worker_group_shift`);

--
-- Indexes for table `worker_locations`
--
ALTER TABLE `worker_locations`
  ADD PRIMARY KEY (`worker_location_id`);

--
-- Indexes for table `worker_tenure`
--
ALTER TABLE `worker_tenure`
  ADD KEY `worker_id` (`worker_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alerts`
--
ALTER TABLE `alerts`
  MODIFY `alert_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `billing_resources`
--
ALTER TABLE `billing_resources`
  MODIFY `billing_resources_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cargo_packagings`
--
ALTER TABLE `cargo_packagings`
  MODIFY `cargo_packaging_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cargo_types`
--
ALTER TABLE `cargo_types`
  MODIFY `cargo_type_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `charges`
--
ALTER TABLE `charges`
  MODIFY `charge_id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `commodities`
--
ALTER TABLE `commodities`
  MODIFY `commodity_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `company_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `company_addresses`
--
ALTER TABLE `company_addresses`
  MODIFY `company_address_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company_prices`
--
ALTER TABLE `company_prices`
  MODIFY `company_price_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `configs`
--
ALTER TABLE `configs`
  MODIFY `config_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `consumables`
--
ALTER TABLE `consumables`
  MODIFY `consumable_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `consumable_purchases`
--
ALTER TABLE `consumable_purchases`
  MODIFY `consumable_purchase_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `country_id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=251;

--
-- AUTO_INCREMENT for table `delay_reasons`
--
ALTER TABLE `delay_reasons`
  MODIFY `delay_reason_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `designations`
--
ALTER TABLE `designations`
  MODIFY `designation_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `equipments`
--
ALTER TABLE `equipments`
  MODIFY `equipment_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `equipments_asset`
--
ALTER TABLE `equipments_asset`
  MODIFY `equipment_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `equipment_consumables`
--
ALTER TABLE `equipment_consumables`
  MODIFY `equipment_consumable_id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `equipment_consumables_asset`
--
ALTER TABLE `equipment_consumables_asset`
  MODIFY `equipment_consumable_id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `equipment_groups`
--
ALTER TABLE `equipment_groups`
  MODIFY `equipment_group_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `equipment_groups_asset`
--
ALTER TABLE `equipment_groups_asset`
  MODIFY `equipment_group_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `equipment_maintenance`
--
ALTER TABLE `equipment_maintenance`
  MODIFY `equipment_maintenance_id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `equipment_maintenance_asset`
--
ALTER TABLE `equipment_maintenance_asset`
  MODIFY `equipment_maintenance_id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `equipment_mileage`
--
ALTER TABLE `equipment_mileage`
  MODIFY `equipment_mileage_id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `equipment_mileage_asset`
--
ALTER TABLE `equipment_mileage_asset`
  MODIFY `equipment_mileage_id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `equipment_types`
--
ALTER TABLE `equipment_types`
  MODIFY `equipment_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `equipment_types_asset`
--
ALTER TABLE `equipment_types_asset`
  MODIFY `equipment_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `exchange_rates`
--
ALTER TABLE `exchange_rates`
  MODIFY `exchange_rate_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `gears`
--
ALTER TABLE `gears`
  MODIFY `gear_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `gear_purchases`
--
ALTER TABLE `gear_purchases`
  MODIFY `gear_purchase_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gear_types`
--
ALTER TABLE `gear_types`
  MODIFY `gear_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `incident_requests`
--
ALTER TABLE `incident_requests`
  MODIFY `incident_request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `incident_requests_attachments`
--
ALTER TABLE `incident_requests_attachments`
  MODIFY `incident_request_attachment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `incident_requests_remarks`
--
ALTER TABLE `incident_requests_remarks`
  MODIFY `incident_request_remarks_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `incident_request_asset_details`
--
ALTER TABLE `incident_request_asset_details`
  MODIFY `incident_request_asset_details_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `incident_request_person_details`
--
ALTER TABLE `incident_request_person_details`
  MODIFY `incident_request_person_details_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `incident_types`
--
ALTER TABLE `incident_types`
  MODIFY `incident_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `invoice_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `log_id` int(15) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=478;

--
-- AUTO_INCREMENT for table `manufacturers`
--
ALTER TABLE `manufacturers`
  MODIFY `manufacturer_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `masters_companies`
--
ALTER TABLE `masters_companies`
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `notices_of_arrival`
--
ALTER TABLE `notices_of_arrival`
  MODIFY `notices_of_arrival_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notices_of_arrival_remarks`
--
ALTER TABLE `notices_of_arrival_remarks`
  MODIFY `notices_of_arrival_remark_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `operation_types`
--
ALTER TABLE `operation_types`
  MODIFY `operation_type_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `operators`
--
ALTER TABLE `operators`
  MODIFY `operator_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `perm_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=271;

--
-- AUTO_INCREMENT for table `permission_categories`
--
ALTER TABLE `permission_categories`
  MODIFY `perm_cat_id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `ports`
--
ALTER TABLE `ports`
  MODIFY `port_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `port_wharfs`
--
ALTER TABLE `port_wharfs`
  MODIFY `port_wharf_id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `public_holidays`
--
ALTER TABLE `public_holidays`
  MODIFY `public_holiday_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rebundling_colours`
--
ALTER TABLE `rebundling_colours`
  MODIFY `rebundling_colour_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `resource_types`
--
ALTER TABLE `resource_types`
  MODIFY `resource_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `service_requests`
--
ALTER TABLE `service_requests`
  MODIFY `service_request_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_request_attachments`
--
ALTER TABLE `service_request_attachments`
  MODIFY `service_request_attachment_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_request_billing`
--
ALTER TABLE `service_request_billing`
  MODIFY `service_request_billing_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_request_disposals`
--
ALTER TABLE `service_request_disposals`
  MODIFY `service_request_disposal_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_request_disposal_tally`
--
ALTER TABLE `service_request_disposal_tally`
  MODIFY `service_request_disposal_tally_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_request_equipment_types`
--
ALTER TABLE `service_request_equipment_types`
  MODIFY `service_request_equipment_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_request_gear_types`
--
ALTER TABLE `service_request_gear_types`
  MODIFY `service_request_gear_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_request_operations`
--
ALTER TABLE `service_request_operations`
  MODIFY `service_request_operation_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_request_operation_tally`
--
ALTER TABLE `service_request_operation_tally`
  MODIFY `service_request_operation_tally_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_request_remarks`
--
ALTER TABLE `service_request_remarks`
  MODIFY `service_request_remark_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_request_resource_types`
--
ALTER TABLE `service_request_resource_types`
  MODIFY `service_request_resource_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_vouchers`
--
ALTER TABLE `service_vouchers`
  MODIFY `service_voucher_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shipment_terms`
--
ALTER TABLE `shipment_terms`
  MODIFY `shipment_term_id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tally_remarks`
--
ALTER TABLE `tally_remarks`
  MODIFY `tally_remark_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `task_runs`
--
ALTER TABLE `task_runs`
  MODIFY `task_run_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `templates`
--
ALTER TABLE `templates`
  MODIFY `template_id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `timezones`
--
ALTER TABLE `timezones`
  MODIFY `timezone_id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `user_groups`
--
ALTER TABLE `user_groups`
  MODIFY `user_group_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `vehicle_history`
--
ALTER TABLE `vehicle_history`
  MODIFY `vh_id` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `vehicle_history_asset`
--
ALTER TABLE `vehicle_history_asset`
  MODIFY `vh_id` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `vessels`
--
ALTER TABLE `vessels`
  MODIFY `vessel_id` int(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vessel_cranes`
--
ALTER TABLE `vessel_cranes`
  MODIFY `vessel_crane_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vessel_hatches`
--
ALTER TABLE `vessel_hatches`
  MODIFY `vessel_hatch_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vessel_visits`
--
ALTER TABLE `vessel_visits`
  MODIFY `vessel_visit_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vessel_visit_equipments`
--
ALTER TABLE `vessel_visit_equipments`
  MODIFY `vessel_visit_equipment_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vessel_visit_equipment_confirmation`
--
ALTER TABLE `vessel_visit_equipment_confirmation`
  MODIFY `vessel_visit_equipment_confirmation_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vessel_visit_gears`
--
ALTER TABLE `vessel_visit_gears`
  MODIFY `vessel_visit_gear_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vessel_visit_gear_confirmation`
--
ALTER TABLE `vessel_visit_gear_confirmation`
  MODIFY `vessel_visit_gear_confirmation_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vessel_visit_workers`
--
ALTER TABLE `vessel_visit_workers`
  MODIFY `vessel_visit_workers_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wastage_types`
--
ALTER TABLE `wastage_types`
  MODIFY `wastage_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `workers`
--
ALTER TABLE `workers`
  MODIFY `worker_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `worker_allowances`
--
ALTER TABLE `worker_allowances`
  MODIFY `worker_allowance_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `worker_availability`
--
ALTER TABLE `worker_availability`
  MODIFY `worker_availability_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `worker_groups`
--
ALTER TABLE `worker_groups`
  MODIFY `worker_group_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `worker_locations`
--
ALTER TABLE `worker_locations`
  MODIFY `worker_location_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `company_addresses`
--
ALTER TABLE `company_addresses`
  ADD CONSTRAINT `company_addresses_ibfk_2` FOREIGN KEY (`designation`) REFERENCES `designations` (`designation_id`),
  ADD CONSTRAINT `company_addresses_ibfk_3` FOREIGN KEY (`address_country`) REFERENCES `countries` (`code`),
  ADD CONSTRAINT `company_addresses_ibfk_4` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
