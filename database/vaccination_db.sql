-- Vaccination Management System Database
-- Database: vaccination_db
-- Compatible with MySQL 5.7+ / MariaDB / XAMPP

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `vaccination_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `vaccination_db`;

-- --------------------------------------------------------
-- Table structure for table `admins`
-- --------------------------------------------------------
CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `parents`
-- --------------------------------------------------------
CREATE TABLE `parents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `cnic` varchar(15) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `reset_token` varchar(100) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `cnic` (`cnic`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `hospitals`
-- --------------------------------------------------------
CREATE TABLE `hospitals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hospital_name` varchar(150) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('active','inactive','pending') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `children`
-- --------------------------------------------------------
CREATE TABLE `children` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `date_of_birth` date NOT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `blood_group` varchar(5) DEFAULT NULL,
  `cnic` varchar(15) DEFAULT NULL,
  `birth_certificate_no` varchar(50) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `children_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `parents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `vaccines`
-- --------------------------------------------------------
CREATE TABLE `vaccines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `recommended_age` varchar(50) DEFAULT NULL,
  `doses_required` int(11) NOT NULL DEFAULT 1,
  `interval_days` int(11) DEFAULT 0,
  `availability` enum('available','unavailable') NOT NULL DEFAULT 'available',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `appointments`
-- --------------------------------------------------------
CREATE TABLE `appointments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `child_id` int(11) NOT NULL,
  `hospital_id` int(11) NOT NULL,
  `vaccine_id` int(11) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `status` enum('pending','approved','rejected','completed','cancelled') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `child_id` (`child_id`),
  KEY `hospital_id` (`hospital_id`),
  KEY `vaccine_id` (`vaccine_id`),
  KEY `idx_appointment_date` (`appointment_date`),
  KEY `idx_status` (`status`),
  CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `parents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`child_id`) REFERENCES `children` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `appointments_ibfk_3` FOREIGN KEY (`hospital_id`) REFERENCES `hospitals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `appointments_ibfk_4` FOREIGN KEY (`vaccine_id`) REFERENCES `vaccines` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `vaccination_requests`
-- --------------------------------------------------------
CREATE TABLE `vaccination_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `child_id` int(11) NOT NULL,
  `vaccine_id` int(11) NOT NULL,
  `hospital_id` int(11) DEFAULT NULL,
  `preferred_date` date DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `admin_response` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `child_id` (`child_id`),
  KEY `vaccine_id` (`vaccine_id`),
  KEY `hospital_id` (`hospital_id`),
  CONSTRAINT `vaccination_requests_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `parents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `vaccination_requests_ibfk_2` FOREIGN KEY (`child_id`) REFERENCES `children` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `vaccination_requests_ibfk_3` FOREIGN KEY (`vaccine_id`) REFERENCES `vaccines` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `vaccination_requests_ibfk_4` FOREIGN KEY (`hospital_id`) REFERENCES `hospitals` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `vaccination_records`
-- --------------------------------------------------------
CREATE TABLE `vaccination_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `child_id` int(11) NOT NULL,
  `vaccine_id` int(11) NOT NULL,
  `hospital_id` int(11) NOT NULL,
  `appointment_id` int(11) DEFAULT NULL,
  `dose_number` int(11) NOT NULL DEFAULT 1,
  `vaccination_date` date NOT NULL,
  `administered_by` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('completed','partial') NOT NULL DEFAULT 'completed',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `child_id` (`child_id`),
  KEY `vaccine_id` (`vaccine_id`),
  KEY `hospital_id` (`hospital_id`),
  KEY `appointment_id` (`appointment_id`),
  CONSTRAINT `vaccination_records_ibfk_1` FOREIGN KEY (`child_id`) REFERENCES `children` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `vaccination_records_ibfk_2` FOREIGN KEY (`vaccine_id`) REFERENCES `vaccines` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `vaccination_records_ibfk_3` FOREIGN KEY (`hospital_id`) REFERENCES `hospitals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `vaccination_records_ibfk_4` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Seed Data
-- --------------------------------------------------------

-- Admin (password: Admin@123)
INSERT INTO `admins` (`full_name`, `email`, `password`, `phone`, `status`) VALUES
('System Administrator', 'admin@vaccination.com', '$2y$10$djmQXh3911gwKMh9aBqNMedR/okBNrFUVyMikZhneqmWB0KPuWlUC', '03001234567', 'active');

-- Vaccines
INSERT INTO `vaccines` (`name`, `description`, `recommended_age`, `doses_required`, `interval_days`, `availability`, `status`) VALUES
('BCG', 'Bacillus Calmette-Guérin vaccine protects against tuberculosis.', 'At birth', 1, 0, 'available', 'active'),
('Hepatitis B', 'Protects against Hepatitis B virus infection.', 'At birth, 6 weeks, 14 weeks', 3, 28, 'available', 'active'),
('OPV', 'Oral Polio Vaccine protects against poliomyelitis.', 'At birth, 6, 10, 14 weeks', 4, 28, 'available', 'active'),
('Pentavalent', 'Protects against Diphtheria, Pertussis, Tetanus, Hib and Hepatitis B.', '6, 10, 14 weeks', 3, 28, 'available', 'active'),
('PCV', 'Pneumococcal Conjugate Vaccine protects against pneumococcal disease.', '6, 10, 14 weeks', 3, 28, 'available', 'active'),
('Rotavirus', 'Protects against rotavirus gastroenteritis.', '6, 10 weeks', 2, 28, 'available', 'active'),
('IPV', 'Inactivated Polio Vaccine.', '14 weeks', 1, 0, 'available', 'active'),
('Measles', 'Protects against measles.', '9 months, 15 months', 2, 180, 'available', 'active'),
('TT', 'Tetanus Toxoid for pregnant women and others.', 'As required', 2, 28, 'available', 'active'),
('COVID-19', 'COVID-19 vaccination as per guidelines.', 'As per age guidelines', 2, 28, 'available', 'active');

-- Hospitals (password: Hospital@123 for all)
INSERT INTO `hospitals` (`hospital_name`, `email`, `phone`, `address`, `city`, `password`, `status`) VALUES
('City General Hospital', 'citygeneral@hospital.com', '02134567890', '123 Main Boulevard, Gulberg', 'Lahore', '$2y$10$xvTLsbokNxEpyhjKceBK.OUXRRq1puzYwCus3mbjeAFD4T3bJo7mS', 'active'),
('National Children Hospital', 'nch@hospital.com', '05123456789', '45 Health Avenue, Blue Area', 'Islamabad', '$2y$10$xvTLsbokNxEpyhjKceBK.OUXRRq1puzYwCus3mbjeAFD4T3bJo7mS', 'active'),
('Karachi Medical Complex', 'kmc@hospital.com', '02198765432', '78 Clifton Road', 'Karachi', '$2y$10$xvTLsbokNxEpyhjKceBK.OUXRRq1puzYwCus3mbjeAFD4T3bJo7mS', 'active'),
('Peshawar Child Care', 'pcc@hospital.com', '09112345678', '12 University Road', 'Peshawar', '$2y$10$xvTLsbokNxEpyhjKceBK.OUXRRq1puzYwCus3mbjeAFD4T3bJo7mS', 'active'),
('Quetta Health Center', 'qhc@hospital.com', '08123456789', '5 Jinnah Road', 'Quetta', '$2y$10$xvTLsbokNxEpyhjKceBK.OUXRRq1puzYwCus3mbjeAFD4T3bJo7mS', 'pending');

-- Sample Parent (password: Parent@123)
INSERT INTO `parents` (`full_name`, `email`, `phone`, `cnic`, `password`, `address`, `city`, `status`) VALUES
('Ahmed Khan', 'ahmed@parent.com', '03001234567', '35202-1234567-1', '$2y$10$57NRXinN45Wu/rAANH44Mu5XS1ii29yKGOd9vqUR6PIhR0xq5eWgi', 'House 12, Street 5, Model Town', 'Lahore', 'active'),
('Fatima Ali', 'fatima@parent.com', '03009876543', '42101-7654321-2', '$2y$10$57NRXinN45Wu/rAANH44Mu5XS1ii29yKGOd9vqUR6PIhR0xq5eWgi', 'Flat 3B, Gulshan-e-Iqbal', 'Karachi', 'active');

-- Sample Children
INSERT INTO `children` (`parent_id`, `full_name`, `date_of_birth`, `gender`, `blood_group`, `status`) VALUES
(1, 'Ali Ahmed', '2023-05-15', 'male', 'B+', 'active'),
(1, 'Sara Ahmed', '2021-11-20', 'female', 'A+', 'active'),
(2, 'Hassan Ali', '2024-01-10', 'male', 'O+', 'active');

-- Sample Appointments
INSERT INTO `appointments` (`parent_id`, `child_id`, `hospital_id`, `vaccine_id`, `appointment_date`, `appointment_time`, `status`, `notes`) VALUES
(1, 1, 1, 1, CURDATE(), '10:00:00', 'approved', 'First BCG dose'),
(1, 1, 1, 2, DATE_ADD(CURDATE(), INTERVAL 7 DAY), '11:00:00', 'pending', 'Hepatitis B second dose'),
(1, 2, 2, 8, DATE_ADD(CURDATE(), INTERVAL 14 DAY), '09:30:00', 'pending', 'Measles vaccination'),
(2, 3, 3, 1, CURDATE(), '14:00:00', 'approved', 'Newborn BCG');

-- Sample Vaccination Records
INSERT INTO `vaccination_records` (`child_id`, `vaccine_id`, `hospital_id`, `appointment_id`, `dose_number`, `vaccination_date`, `administered_by`, `status`) VALUES
(1, 1, 1, 1, 1, CURDATE(), 'Dr. Ayesha', 'completed'),
(2, 1, 2, NULL, 1, DATE_SUB(CURDATE(), INTERVAL 30 DAY), 'Dr. Imran', 'completed');

-- Sample Vaccination Requests
INSERT INTO `vaccination_requests` (`parent_id`, `child_id`, `vaccine_id`, `hospital_id`, `preferred_date`, `message`, `status`) VALUES
(1, 1, 4, 1, DATE_ADD(CURDATE(), INTERVAL 10 DAY), 'Please schedule Pentavalent for my child.', 'pending'),
(2, 3, 3, 3, DATE_ADD(CURDATE(), INTERVAL 5 DAY), 'Need OPV dose.', 'pending');

COMMIT;
