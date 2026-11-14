-- ========================================================
-- MEDINOVA - MULTI-SPECIALITY HOSPITAL MANAGEMENT SYSTEM
-- Database: medinova_hospital
-- Character Set: utf8mb4 / Engine: InnoDB
-- Fully Normalized 3NF Schema with Foreign Keys & Indexes
-- ========================================================

CREATE DATABASE IF NOT EXISTS `medinova_hospital` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `medinova_hospital`;

SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- 1. Table: roles
-- --------------------------------------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `role_name` VARCHAR(50) NOT NULL UNIQUE,
  `display_name` VARCHAR(100) NOT NULL,
  `description` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `roles` (`id`, `role_name`, `display_name`, `description`) VALUES
(1, 'superadmin', 'Super Administrator', 'Full hospital system oversight and configuration'),
(2, 'admin', 'Hospital Administrator', 'Operational management across departments and staff'),
(3, 'doctor', 'Attending Physician / Specialist', 'Clinical consultations, diagnoses, and prescriptions'),
(4, 'patient', 'Registered Patient', 'Access to own medical records, visits, and bills'),
(5, 'receptionist', 'Front Desk & Reception', 'Patient intake, appointment booking, and bed admissions'),
(6, 'nurse', 'Inpatient Registered Nurse', 'Vitals recording and inpatient care monitoring'),
(7, 'lab_technician', 'Pathology & Lab Technician', 'Sample collection and diagnostic test result reporting'),
(8, 'pharmacist', 'Dispensary Pharmacist', 'Medication inventory management and dispensing'),
(9, 'billing', 'Billing & Cashier', 'Hospital invoicing, claims, and payment reconciliation');

-- --------------------------------------------------------
-- 2. Table: users (Central Authentication)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `role_id` INT NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(120) NOT NULL,
  `phone` VARCHAR(30) NOT NULL,
  `avatar` VARCHAR(255) DEFAULT 'images/team/1.webp',
  `is_active` TINYINT(1) DEFAULT 1,
  `last_login` DATETIME NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_user_role` FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default password for all seed accounts is 'medinova123'
INSERT INTO `users` (`id`, `role_id`, `email`, `password_hash`, `full_name`, `phone`, `avatar`) VALUES
(1, 1, 'admin@medinova.com', '$2y$10$eE0m9L1Z5Qf1m4WbT2K3teK1pMhVj7V9c9zD1bC7/XG3j8Z1F1Mxe', 'Dr. Arthur Sterling', '+1 (800) 555-0100', 'images/team/1.webp'),
(2, 3, 'cardiology@medinova.com', '$2y$10$eE0m9L1Z5Qf1m4WbT2K3teK1pMhVj7V9c9zD1bC7/XG3j8Z1F1Mxe', 'Dr. Marcus Vance', '+1 (800) 555-0101', 'images/team/2.webp'),
(3, 3, 'neurology@medinova.com', '$2y$10$eE0m9L1Z5Qf1m4WbT2K3teK1pMhVj7V9c9zD1bC7/XG3j8Z1F1Mxe', 'Dr. Elena Rostova', '+1 (800) 555-0102', 'images/team/3.webp'),
(4, 3, 'orthopedics@medinova.com', '$2y$10$eE0m9L1Z5Qf1m4WbT2K3teK1pMhVj7V9c9zD1bC7/XG3j8Z1F1Mxe', 'Dr. Julian Thorne', '+1 (800) 555-0103', 'images/team/4.webp'),
(5, 3, 'pediatrics@medinova.com', '$2y$10$eE0m9L1Z5Qf1m4WbT2K3teK1pMhVj7V9c9zD1bC7/XG3j8Z1F1Mxe', 'Dr. Sophia Martinez', '+1 (800) 555-0104', 'images/team/1.webp'),
(6, 4, 'patient@medinova.com', '$2y$10$eE0m9L1Z5Qf1m4WbT2K3teK1pMhVj7V9c9zD1bC7/XG3j8Z1F1Mxe', 'John Doe', '+1 (555) 234-5678', 'images/team/2.webp'),
(7, 5, 'reception@medinova.com', '$2y$10$eE0m9L1Z5Qf1m4WbT2K3teK1pMhVj7V9c9zD1bC7/XG3j8Z1F1Mxe', 'Sarah Jenkins', '+1 (800) 555-0110', 'images/team/3.webp'),
(8, 6, 'nurse@medinova.com', '$2y$10$eE0m9L1Z5Qf1m4WbT2K3teK1pMhVj7V9c9zD1bC7/XG3j8Z1F1Mxe', 'Nurse Emily Watson', '+1 (800) 555-0120', 'images/team/4.webp'),
(9, 7, 'lab@medinova.com', '$2y$10$eE0m9L1Z5Qf1m4WbT2K3teK1pMhVj7V9c9zD1bC7/XG3j8Z1F1Mxe', 'David Miller', '+1 (800) 555-0130', 'images/team/1.webp'),
(10, 8, 'pharmacy@medinova.com', '$2y$10$eE0m9L1Z5Qf1m4WbT2K3teK1pMhVj7V9c9zD1bC7/XG3j8Z1F1Mxe', 'Rachel Green', '+1 (800) 555-0140', 'images/team/2.webp'),
(11, 9, 'billing@medinova.com', '$2y$10$eE0m9L1Z5Qf1m4WbT2K3teK1pMhVj7V9c9zD1bC7/XG3j8Z1F1Mxe', 'Robert Taylor', '+1 (800) 555-0150', 'images/team/3.webp');

-- --------------------------------------------------------
-- 3. Table: departments (12 Specialized Divisions)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `departments`;
CREATE TABLE `departments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `slug` VARCHAR(150) NOT NULL UNIQUE,
  `code` VARCHAR(20) NOT NULL UNIQUE,
  `icon` VARCHAR(100) DEFAULT 'fa-heart-pulse',
  `image` VARCHAR(255) DEFAULT 'images/misc/l1.webp',
  `short_desc` VARCHAR(255) NOT NULL,
  `full_desc` TEXT NOT NULL,
  `emergency_phone` VARCHAR(50) DEFAULT '+1 (800) 555-9111',
  `display_order` INT DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `departments` (`id`, `name`, `slug`, `code`, `icon`, `image`, `short_desc`, `full_desc`, `display_order`) VALUES
(1, 'Cardiology', 'cardiology', 'CARD', 'fa-heart-pulse', 'images/misc/l1.webp', 
 'Comprehensive cardiac diagnostics, interventional cath labs, coronary angioplasty, and advanced heart failure care.',
 'The MEDINOVA Department of Cardiology provides cutting-edge medical and surgical cardiovascular services. Utilizing 3D echocardiography, digital biplane cardiac catheterization, coronary stenting, and pacemaker implantation, our board-certified cardiologists ensure rapid, lifesaving heart treatments around the clock.', 1),

(2, 'Neurology', 'neurology', 'NEUR', 'fa-brain', 'images/misc/l2.webp',
 'Advanced neurological disorder management, acute stroke intervention, epilepsy monitoring, and neurosurgery.',
 'Our Department of Neurology & Neurosurgery treats acute stroke, neurodegenerative disorders, chronic migraines, epilepsy, and brain tumors. Supported by high-resolution 3T MRI, neuro-navigation surgical guidance, and a specialized Neuro-ICU.', 2),

(3, 'Orthopedics', 'orthopedics', 'ORTH', 'fa-bone', 'images/misc/l3.webp',
 'Total joint replacements, minimally invasive arthroscopic surgery, trauma reconstruction, and sports medicine.',
 'The Orthopedic Institute at MEDINOVA delivers computerized robotic-assisted knee and hip replacements, spinal disc repairs, arthroscopic ligament reconstructions, and trauma care to restore mobility swiftly and comfortably.', 3),

(4, 'Pediatrics', 'pediatrics', 'PEDI', 'fa-baby', 'images/misc/l4.webp',
 'Compassionate infant, child, and adolescent healthcare, Neonatal Intensive Care (NICU), and pediatric surgery.',
 'Dedicated to young lives, our Pediatric Department combines developmental checkups, comprehensive immunizations, child-friendly surgery, and Level-III NICU facilities within an anxiety-free, nurturing environment.', 4),

(5, 'Gynecology & Obstetrics', 'gynecology', 'GYNE', 'fa-venus', 'images/misc/l5.webp',
 'Complete maternity care, fetal medicine, painless labor suites, reproductive health, and laparoscopic gynecology.',
 'We provide comprehensive women’s health programs spanning prenatal care, high-risk pregnancy management, 4D ultrasound screening, fertility evaluation, and gentle minimally invasive gynecologic surgery.', 5),

(6, 'Dermatology', 'dermatology', 'DERM', 'fa-hand-dots', 'images/misc/l6.webp',
 'Clinical skin pathology, psoriasis therapy, skin cancer screening, allergy patch testing, and laser dermatology.',
 'Specializing in diagnosis and treatment of complex skin, hair, and nail disorders, aesthetic laser treatments, phototherapy, and mole mapping for early melanoma detection.', 6),

(7, 'Ophthalmology', 'ophthalmology', 'OPHT', 'fa-eye', 'images/misc/s1.webp',
 'Micro-incision cataract surgery, LASIK laser vision correction, glaucoma management, and vitreoretinal care.',
 'Our eye institute is equipped with femtosecond laser cataract systems, retinal angiography, optical coherence tomography (OCT), and pediatric squint correction.', 7),

(8, 'Dental Care & Maxillofacial Surgery', 'dental-care', 'DENT', 'fa-tooth', 'images/misc/s2.webp',
 'Advanced restorative dentistry, computer-guided titanium implants, orthodontic aligners, and facial trauma surgery.',
 'Preserving our foundational dental excellence, MEDINOVA Dental delivers pain-free ultrasonic prophylaxis, micro-endodontics, aesthetic smile redesigns, and full-mouth surgical rehabilitations.', 8),

(9, 'Oncology', 'oncology', 'ONCO', 'fa-dna', 'images/misc/s3.webp',
 'Medical oncology, targeted immunotherapy, surgical oncology, stereotactic radiotherapy, and tumor boards.',
 'Multidisciplinary cancer care combining molecular genetic profiling, modern chemotherapy infusion suites, and compassionate palliative support aimed at achieving high clinical remission rates.', 9),

(10, 'General & Internal Medicine', 'general-medicine', 'GENM', 'fa-stethoscope', 'images/misc/s4.webp',
 'Primary adult healthcare, management of chronic lifestyle diseases, diabetes control, and acute fever clinics.',
 'Our internists manage multisystem illnesses, hypertension, metabolic syndromes, lipid abnormalities, and adult immunizations through comprehensive evidence-based protocols.', 10),

(11, 'Radiology & Imaging', 'radiology', 'RADI', 'fa-x-ray', 'images/misc/l1.webp',
 'High-speed 128-slice CT, 3.0 Tesla MRI, digital mammography, Doppler ultrasound, and interventional radiology.',
 'Operating 24/7 with PACS digital cloud archiving, enabling rapid cross-speciality diagnostic accuracy and image-guided biopsy procedures.', 11),

(12, 'Emergency & Trauma Medicine', 'emergency-medicine', 'EMER', 'fa-truck-medical', 'images/misc/l2.webp',
 'Level-I Trauma resuscitation bays, 24/7 cardiac emergency unit, stroke thrombolysis, and mobile ICU ambulances.',
 'Equipped with rapid triage, bedside point-of-care ultrasound, advanced airway management, and immediate surgical theatre access for critical medical emergencies.', 12);

-- --------------------------------------------------------
-- 4. Table: patients (Patient Registry)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `patients`;
CREATE TABLE `patients` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL UNIQUE,
  `patient_code` VARCHAR(30) NOT NULL UNIQUE,
  `dob` DATE NOT NULL,
  `gender` ENUM('Male', 'Female', 'Other') DEFAULT 'Male',
  `blood_group` VARCHAR(10) DEFAULT 'O+',
  `marital_status` ENUM('Single', 'Married', 'Divorced', 'Widowed') DEFAULT 'Single',
  `address` TEXT NOT NULL,
  `emergency_contact_name` VARCHAR(100) NOT NULL,
  `emergency_contact_phone` VARCHAR(30) NOT NULL,
  `insurance_provider` VARCHAR(100) DEFAULT 'BlueCross BlueShield',
  `insurance_policy_no` VARCHAR(50) DEFAULT 'MED-POL-88219',
  `medical_allergies` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_patient_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `patients` (`id`, `user_id`, `patient_code`, `dob`, `gender`, `blood_group`, `marital_status`, `address`, `emergency_contact_name`, `emergency_contact_phone`, `insurance_provider`, `insurance_policy_no`, `medical_allergies`) VALUES
(1, 6, 'MED-PAT-1001', '1988-06-14', 'Male', 'O+', 'Married', '742 Evergreen Terrace, New York, NY 10001', 'Jane Doe', '+1 (555) 999-4321', 'BlueCross Medical', 'BCBS-NY-9921', 'Penicillin, Shellfish');

-- --------------------------------------------------------
-- 5. Table: doctors (Specialist Roster)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `doctors`;
CREATE TABLE `doctors` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL UNIQUE,
  `department_id` INT NOT NULL,
  `doctor_code` VARCHAR(30) NOT NULL UNIQUE,
  `specialization` VARCHAR(150) NOT NULL,
  `qualification` VARCHAR(255) NOT NULL,
  `experience_years` INT DEFAULT 10,
  `consultation_fee` DECIMAL(10,2) DEFAULT 120.00,
  `room_no` VARCHAR(30) DEFAULT 'Wing A - 302',
  `bio` TEXT NOT NULL,
  `availability_days` VARCHAR(100) DEFAULT 'Mon, Tue, Wed, Thu, Fri',
  `availability_hours` VARCHAR(100) DEFAULT '09:00 - 17:00',
  `is_featured` TINYINT(1) DEFAULT 1,
  `is_available` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_doctor_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_doctor_dept` FOREIGN KEY (`department_id`) REFERENCES `departments`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `doctors` (`id`, `user_id`, `department_id`, `doctor_code`, `specialization`, `qualification`, `experience_years`, `consultation_fee`, `room_no`, `bio`, `availability_days`, `availability_hours`) VALUES
(1, 2, 1, 'MED-DOC-001', 'Senior Interventional Cardiologist', 'MD, FACC, FSCAI (Harvard Medical School)', 16, 150.00, 'Wing A - Room 101', 'Specialist in complex coronary interventions, structural heart valve repairs, and preventive cardiology programs.', 'Monday - Friday', '09:00 - 15:00'),
(2, 3, 2, 'MED-DOC-002', 'Chief of Neurology & Neuro-Critical Care', 'MD, PhD (Johns Hopkins University)', 14, 160.00, 'Wing B - Room 204', 'Renowned expert in acute stroke thrombolysis, epilepsy mapping, and neuro-rehabilitation therapy.', 'Monday - Thursday', '10:00 - 16:00'),
(3, 4, 3, 'MED-DOC-003', 'Consultant Orthopedic & Joint Surgeon', 'MS (Ortho), FRCS (Royal College of Surgeons)', 18, 140.00, 'Wing C - Room 305', 'Pioneering robotic total knee and hip replacement specialist with over 3,000 successful joint procedures.', 'Tuesday - Saturday', '09:00 - 17:00'),
(4, 5, 4, 'MED-DOC-004', 'Head Pediatrician & Neonatologist', 'MD (Pediatrics), FAAP (Columbia University)', 12, 110.00, 'Wing D - Room 402', 'Devoted to developmental pediatrics, pediatric intensive care, childhood allergies, and adolescent wellness.', 'Monday - Friday', '08:30 - 16:30');

-- --------------------------------------------------------
-- 6. Table: doctor_schedules
-- --------------------------------------------------------
DROP TABLE IF EXISTS `doctor_schedules`;
CREATE TABLE `doctor_schedules` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `doctor_id` INT NOT NULL,
  `day_of_week` ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday') NOT NULL,
  `start_time` TIME NOT NULL,
  `end_time` TIME NOT NULL,
  `slot_duration` INT DEFAULT 30,
  `max_patients` INT DEFAULT 15,
  CONSTRAINT `fk_sched_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `doctor_schedules` (`doctor_id`, `day_of_week`, `start_time`, `end_time`, `slot_duration`, `max_patients`) VALUES
(1, 'Monday', '09:00:00', '15:00:00', 30, 12),
(1, 'Wednesday', '09:00:00', '15:00:00', 30, 12),
(1, 'Friday', '09:00:00', '15:00:00', 30, 12),
(2, 'Tuesday', '10:00:00', '16:00:00', 30, 12),
(2, 'Thursday', '10:00:00', '16:00:00', 30, 12),
(3, 'Tuesday', '09:00:00', '17:00:00', 30, 16),
(3, 'Saturday', '09:00:00', '14:00:00', 30, 10),
(4, 'Monday', '08:30:00', '16:30:00', 30, 16);

-- --------------------------------------------------------
-- 7. Table: staff (Hospital Personnel)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `staff`;
CREATE TABLE `staff` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL UNIQUE,
  `department_id` INT NULL,
  `staff_code` VARCHAR(30) NOT NULL UNIQUE,
  `designation` VARCHAR(100) NOT NULL,
  `qualification` VARCHAR(150) NOT NULL,
  `shift` ENUM('Morning', 'Evening', 'Night', 'Rotational') DEFAULT 'Morning',
  `joining_date` DATE NOT NULL,
  CONSTRAINT `fk_staff_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_staff_dept` FOREIGN KEY (`department_id`) REFERENCES `departments`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `staff` (`id`, `user_id`, `department_id`, `staff_code`, `designation`, `qualification`, `shift`, `joining_date`) VALUES
(1, 7, 10, 'MED-STF-001', 'Chief Reception Officer', 'BSc Healthcare Administration', 'Morning', '2023-01-15'),
(2, 8, 1, 'MED-STF-002', 'Senior Inpatient Nurse (RN)', 'BSN, Registered Nurse (NY Board)', 'Rotational', '2022-04-10'),
(3, 9, 11, 'MED-STF-003', 'Senior Pathology Lab Technologist', 'MSc Clinical Biochemistry', 'Morning', '2021-09-01'),
(4, 10, NULL, 'MED-STF-004', 'Lead Hospital Pharmacist', 'PharmD, Licensed Pharmacist', 'Morning', '2020-03-20'),
(5, 11, NULL, 'MED-STF-005', 'Financial Controller & Billing Officer', 'BBA in Healthcare Finance', 'Morning', '2019-11-05');

-- --------------------------------------------------------
-- 8. Table: appointments (Hospital Booking System)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `appointments`;
CREATE TABLE `appointments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `appointment_ref` VARCHAR(30) NOT NULL UNIQUE,
  `patient_id` INT NULL,
  `patient_name` VARCHAR(120) NOT NULL,
  `patient_email` VARCHAR(150) NOT NULL,
  `patient_phone` VARCHAR(30) NOT NULL,
  `department_id` INT NOT NULL,
  `doctor_id` INT NOT NULL,
  `appointment_date` DATE NOT NULL,
  `appointment_time` VARCHAR(20) NOT NULL,
  `appointment_type` ENUM('General Consultation', 'Follow-up', 'Emergency Check', 'Diagnostic Review') DEFAULT 'General Consultation',
  `symptoms` TEXT NOT NULL,
  `status` ENUM('Pending', 'Approved', 'In-Consultation', 'Completed', 'Cancelled', 'Rejected') DEFAULT 'Pending',
  `cancellation_reason` VARCHAR(255) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_doc_date_time` (`doctor_id`, `appointment_date`, `appointment_time`),
  CONSTRAINT `fk_app_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_app_dept` FOREIGN KEY (`department_id`) REFERENCES `departments`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_app_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `appointments` (`id`, `appointment_ref`, `patient_id`, `patient_name`, `patient_email`, `patient_phone`, `department_id`, `doctor_id`, `appointment_date`, `appointment_time`, `appointment_type`, `symptoms`, `status`) VALUES
(1, 'MED-2026-1001', 1, 'John Doe', 'patient@medinova.com', '+1 (555) 234-5678', 1, 1, DATE_ADD(CURRENT_DATE, INTERVAL 1 DAY), '10:00 AM', 'General Consultation', 'Occasional chest tightness and shortness of breath during exertion.', 'Approved'),
(2, 'MED-2026-1002', 1, 'John Doe', 'patient@medinova.com', '+1 (555) 234-5678', 2, 2, DATE_ADD(CURRENT_DATE, INTERVAL 3 DAY), '11:00 AM', 'Diagnostic Review', 'Recurrent unilateral tension headaches and visual aura.', 'Pending'),
(3, 'MED-2026-1003', NULL, 'Robert Taylor', 'robert.taylor@example.com', '+1 (555) 345-6789', 3, 3, DATE_ADD(CURRENT_DATE, INTERVAL 4 DAY), '02:00 PM', 'General Consultation', 'Knee joint stiffness and pain after running marathons.', 'Approved'),
(4, 'MED-2026-1004', NULL, 'Jessica Alba', 'jessica.alba@example.com', '+1 (555) 678-1234', 4, 4, DATE_SUB(CURRENT_DATE, INTERVAL 3 DAY), '03:00 PM', 'Follow-up', 'Child wellness immunization and seasonal allergy review.', 'Completed');

-- --------------------------------------------------------
-- 9. Table: medical_records (EMR & Clinical Notes)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `medical_records`;
CREATE TABLE `medical_records` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `patient_id` INT NOT NULL,
  `doctor_id` INT NOT NULL,
  `appointment_id` INT NULL,
  `visit_date` DATE NOT NULL,
  `vitals_bp` VARCHAR(20) DEFAULT '120/80 mmHg',
  `vitals_pulse` VARCHAR(20) DEFAULT '72 bpm',
  `vitals_temp` VARCHAR(20) DEFAULT '98.6 °F',
  `vitals_spo2` VARCHAR(20) DEFAULT '99%',
  `symptoms` TEXT NOT NULL,
  `diagnosis` TEXT NOT NULL,
  `clinical_notes` TEXT NOT NULL,
  `follow_up_date` DATE NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_mr_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_mr_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_mr_appointment` FOREIGN KEY (`appointment_id`) REFERENCES `appointments`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `medical_records` (`id`, `patient_id`, `doctor_id`, `appointment_id`, `visit_date`, `vitals_bp`, `vitals_pulse`, `vitals_temp`, `vitals_spo2`, `symptoms`, `diagnosis`, `clinical_notes`, `follow_up_date`) VALUES
(1, 1, 1, 1, DATE_SUB(CURRENT_DATE, INTERVAL 10 DAY), '128/84 mmHg', '76 bpm', '98.4 °F', '98%', 
 'Mild exertional angina and high cholesterol history.', 
 'Stage 1 Essential Hypertension; Hyperlipidemia; Stable Coronary Angina.', 
 'Patient presented alert and oriented. Heart sounds S1, S2 regular without murmurs. Resting ECG shows normal sinus rhythm. Advised low-sodium Mediterranean diet, lifestyle moderation, and prescribed lipid-lowering medication. Ordered follow-up Lipid Profile and Treadmill Stress Test.', 
 DATE_ADD(CURRENT_DATE, INTERVAL 20 DAY));

-- --------------------------------------------------------
-- 10. Table: medicines (Drug Catalogue)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `medicines`;
CREATE TABLE `medicines` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `generic_name` VARCHAR(150) NOT NULL,
  `category` VARCHAR(100) NOT NULL,
  `form` ENUM('Tablet', 'Capsule', 'Syrup', 'Injection', 'Ointment', 'Inhaler') DEFAULT 'Tablet',
  `manufacturer` VARCHAR(150) NOT NULL,
  `unit_price` DECIMAL(10,2) NOT NULL,
  `reorder_level` INT DEFAULT 50,
  `is_active` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `medicines` (`id`, `name`, `generic_name`, `category`, `form`, `manufacturer`, `unit_price`, `reorder_level`) VALUES
(1, 'Atorvastatin 20mg', 'Atorvastatin Calcium', 'Cardiovascular', 'Tablet', 'Pfizer Global Health', 18.50, 60),
(2, 'Amlodipine 5mg', 'Amlodipine Besylate', 'Antihypertensive', 'Tablet', 'Novartis Pharma', 12.00, 50),
(3, 'Amoxicillin & Clavulanate 625mg', 'Amoxicillin / Clavulanic Acid', 'Antibiotic', 'Tablet', 'GlaxoSmithKline', 24.00, 40),
(4, 'Paracetamol 500mg IV', 'Acetaminophen Injection', 'Analgesic / Antipyretic', 'Injection', 'Baxter Healthcare', 15.00, 30),
(5, 'Salbutamol Inhaler 100mcg', 'Albuterol Sulfate', 'Respiratory', 'Inhaler', 'Cipla Respiratory', 32.00, 25),
(6, 'Omeprazole 20mg DR', 'Omeprazole Magnesium', 'Gastrointestinal', 'Capsule', 'AstraZeneca', 14.50, 45);

-- --------------------------------------------------------
-- 11. Table: medicine_stock (Pharmacy Inventory)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `medicine_stock`;
CREATE TABLE `medicine_stock` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `medicine_id` INT NOT NULL,
  `batch_number` VARCHAR(50) NOT NULL UNIQUE,
  `expiry_date` DATE NOT NULL,
  `quantity_available` INT NOT NULL DEFAULT 100,
  `purchase_price` DECIMAL(10,2) NOT NULL,
  `selling_price` DECIMAL(10,2) NOT NULL,
  `supplier_name` VARCHAR(150) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_stock_med` FOREIGN KEY (`medicine_id`) REFERENCES `medicines`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `medicine_stock` (`medicine_id`, `batch_number`, `expiry_date`, `quantity_available`, `purchase_price`, `selling_price`, `supplier_name`) VALUES
(1, 'BAT-ATOR-2027A', '2027-12-31', 250, 10.00, 18.50, 'McKesson Medical Supplies'),
(2, 'BAT-AMLO-2028B', '2028-06-30', 320, 6.50, 12.00, 'Cardinal Health Corp'),
(3, 'BAT-AMOX-2027C', '2027-09-15', 180, 14.00, 24.00, 'AmerisourceBergen'),
(4, 'BAT-PARA-2026D', '2026-11-30', 85, 8.00, 15.00, 'Medline Industries'),
(5, 'BAT-SALB-2028E', '2028-04-10', 95, 18.00, 32.00, 'Henry Schein Medical');

-- --------------------------------------------------------
-- 12. Table: prescriptions & prescription_items
-- --------------------------------------------------------
DROP TABLE IF EXISTS `prescriptions`;
CREATE TABLE `prescriptions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `prescription_code` VARCHAR(30) NOT NULL UNIQUE,
  `medical_record_id` INT NULL,
  `patient_id` INT NOT NULL,
  `doctor_id` INT NOT NULL,
  `prescription_date` DATE NOT NULL,
  `clinical_instructions` TEXT NOT NULL,
  `status` ENUM('Prescribed', 'Partially Dispensed', 'Dispensed', 'Cancelled') DEFAULT 'Prescribed',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_presc_mr` FOREIGN KEY (`medical_record_id`) REFERENCES `medical_records`(`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_presc_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_presc_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `prescriptions` (`id`, `prescription_code`, `medical_record_id`, `patient_id`, `doctor_id`, `prescription_date`, `clinical_instructions`, `status`) VALUES
(1, 'RX-2026-8810', 1, 1, 1, DATE_SUB(CURRENT_DATE, INTERVAL 10 DAY), 'Take medications with water after breakfast and before bed as instructed. Avoid grapefruits.', 'Dispensed');

DROP TABLE IF EXISTS `prescription_items`;
CREATE TABLE `prescription_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `prescription_id` INT NOT NULL,
  `medicine_id` INT NOT NULL,
  `dosage` VARCHAR(50) NOT NULL,
  `frequency` VARCHAR(50) NOT NULL,
  `duration_days` INT NOT NULL,
  `instructions` VARCHAR(255) NOT NULL,
  `quantity` INT NOT NULL,
  `is_dispensed` TINYINT(1) DEFAULT 0,
  CONSTRAINT `fk_pi_presc` FOREIGN KEY (`prescription_id`) REFERENCES `prescriptions`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pi_med` FOREIGN KEY (`medicine_id`) REFERENCES `medicines`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `prescription_items` (`prescription_id`, `medicine_id`, `dosage`, `frequency`, `duration_days`, `instructions`, `quantity`, `is_dispensed`) VALUES
(1, 1, '20mg', 'Once Daily (Night)', 30, 'Take orally after dinner', 30, 1),
(1, 2, '5mg', 'Once Daily (Morning)', 30, 'Take orally with morning water', 30, 1);

-- --------------------------------------------------------
-- 13. Table: lab_tests (Pathology Directory)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `lab_tests`;
CREATE TABLE `lab_tests` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `test_code` VARCHAR(30) NOT NULL UNIQUE,
  `test_name` VARCHAR(150) NOT NULL,
  `department_id` INT NOT NULL,
  `category` VARCHAR(100) NOT NULL,
  `standard_fee` DECIMAL(10,2) NOT NULL,
  `normal_range` VARCHAR(150) NOT NULL,
  `unit_of_measure` VARCHAR(50) NOT NULL,
  `turnaround_hours` INT DEFAULT 24,
  CONSTRAINT `fk_test_dept` FOREIGN KEY (`department_id`) REFERENCES `departments`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `lab_tests` (`id`, `test_code`, `test_name`, `department_id`, `category`, `standard_fee`, `normal_range`, `unit_of_measure`, `turnaround_hours`) VALUES
(1, 'LAB-CBC-01', 'Complete Blood Count (CBC) with Differential', 10, 'Hematology', 45.00, 'WBC: 4.5-11.0; RBC: 4.3-5.9; Hb: 13.5-17.5', '10^3/uL, g/dL', 12),
(2, 'LAB-LIP-02', 'Comprehensive Lipid Panel (Cholesterol/Triglycerides)', 1, 'Biochemistry', 65.00, 'Total Chol: <200; LDL: <100; HDL: >40', 'mg/dL', 24),
(3, 'LAB-TROP-03', 'Troponin-I High-Sensitivity Cardiac Marker', 1, 'Immunoassay', 95.00, '< 0.04 (Negative for myocardial infarction)', 'ng/mL', 2),
(4, 'LAB-GLUC-04', 'Fasting Blood Glucose & HbA1c', 10, 'Endocrinology', 50.00, 'FBG: 70-99 mg/dL; HbA1c: < 5.7%', '% and mg/dL', 24),
(5, 'LAB-THYR-05', 'Thyroid Function Profile (FT3, FT4, TSH)', 10, 'Immunoassay', 75.00, 'TSH: 0.4-4.0; FT4: 0.8-1.8', 'uIU/mL, ng/dL', 24);

-- --------------------------------------------------------
-- 14. Table: lab_orders & lab_results
-- --------------------------------------------------------
DROP TABLE IF EXISTS `lab_orders`;
CREATE TABLE `lab_orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_ref` VARCHAR(30) NOT NULL UNIQUE,
  `patient_id` INT NOT NULL,
  `doctor_id` INT NOT NULL,
  `appointment_id` INT NULL,
  `test_id` INT NOT NULL,
  `order_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `clinical_notes` TEXT NULL,
  `status` ENUM('Requested', 'Sample Collected', 'Processing', 'Completed', 'Cancelled') DEFAULT 'Requested',
  CONSTRAINT `fk_lo_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_lo_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_lo_test` FOREIGN KEY (`test_id`) REFERENCES `lab_tests`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `lab_orders` (`id`, `order_ref`, `patient_id`, `doctor_id`, `test_id`, `status`) VALUES
(1, 'LAB-ORD-9011', 1, 1, 2, 'Completed'),
(2, 'LAB-ORD-9012', 1, 1, 1, 'Processing');

DROP TABLE IF EXISTS `lab_results`;
CREATE TABLE `lab_results` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `lab_order_id` INT NOT NULL,
  `test_id` INT NOT NULL,
  `technician_id` INT NULL,
  `parameter_name` VARCHAR(150) NOT NULL,
  `measured_value` VARCHAR(100) NOT NULL,
  `reference_range` VARCHAR(100) NOT NULL,
  `flag` ENUM('Normal', 'Low', 'High', 'Critical') DEFAULT 'Normal',
  `technician_notes` TEXT NULL,
  `verified_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_lr_order` FOREIGN KEY (`lab_order_id`) REFERENCES `lab_orders`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_lr_test` FOREIGN KEY (`test_id`) REFERENCES `lab_tests`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `lab_results` (`lab_order_id`, `test_id`, `parameter_name`, `measured_value`, `reference_range`, `flag`, `technician_notes`) VALUES
(1, 2, 'Total Cholesterol', '215 mg/dL', '< 200 mg/dL', 'High', 'Borderline elevated total cholesterol; dietary modification indicated.'),
(1, 2, 'LDL Direct', '135 mg/dL', '< 100 mg/dL', 'High', 'Elevated atherogenic lipoprotein.'),
(1, 2, 'HDL Good Cholesterol', '52 mg/dL', '> 40 mg/dL', 'Normal', 'Satisfactory protective HDL level.'),
(1, 2, 'Triglycerides', '140 mg/dL', '< 150 mg/dL', 'Normal', 'Within optimal lipid spectrum.');

-- --------------------------------------------------------
-- 15. Table: room_categories, rooms & beds (Inpatient Ward)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `room_categories`;
CREATE TABLE `room_categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `code` VARCHAR(30) NOT NULL UNIQUE,
  `daily_rate` DECIMAL(10,2) NOT NULL,
  `description` TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `room_categories` (`id`, `name`, `code`, `daily_rate`, `description`) VALUES
(1, 'Intensive Care Unit (ICU)', 'ICU', 750.00, 'Continuous multi-parameter hemodynamic monitoring, mechanical ventilators, 1:1 nursing care.'),
(2, 'Coronary Care Unit (CCU)', 'CCU', 650.00, 'Specialized telemetry cardiac monitoring and post-cath recovery suites.'),
(3, 'Deluxe Private Suite', 'DELUXE', 350.00, 'Private executive room with en-suite bath, attendant couch, television, and Wi-Fi.'),
(4, 'Semi-Private Ward', 'SEMI-PVT', 200.00, 'Two-patient climate-controlled room with privacy curtains and shared nursing post.'),
(5, 'General Medical Ward', 'GEN-WARD', 100.00, 'Clean multi-bed ward with 24/7 centralized oxygen supply and nursing station.');

DROP TABLE IF EXISTS `rooms`;
CREATE TABLE `rooms` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `room_number` VARCHAR(30) NOT NULL UNIQUE,
  `category_id` INT NOT NULL,
  `floor_number` INT NOT NULL,
  `wing` VARCHAR(50) NOT NULL DEFAULT 'North Wing',
  CONSTRAINT `fk_room_cat` FOREIGN KEY (`category_id`) REFERENCES `room_categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `rooms` (`id`, `room_number`, `category_id`, `floor_number`, `wing`) VALUES
(1, 'ICU-101', 1, 1, 'Critical Care Block'),
(2, 'CCU-201', 2, 2, 'Cardiac Care Wing'),
(3, 'PVT-301', 3, 3, 'Executive Pavilion'),
(4, 'SEMI-401', 4, 4, 'West General Wing'),
(5, 'WARD-501', 5, 5, 'East Medical Wing');

DROP TABLE IF EXISTS `beds`;
CREATE TABLE `beds` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `room_id` INT NOT NULL,
  `bed_number` VARCHAR(30) NOT NULL UNIQUE,
  `status` ENUM('Available', 'Occupied', 'Maintenance', 'Reserved') DEFAULT 'Available',
  CONSTRAINT `fk_bed_room` FOREIGN KEY (`room_id`) REFERENCES `rooms`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `beds` (`id`, `room_id`, `bed_number`, `status`) VALUES
(1, 1, 'BED-ICU-01', 'Available'),
(2, 1, 'BED-ICU-02', 'Occupied'),
(3, 2, 'BED-CCU-01', 'Available'),
(4, 3, 'BED-PVT-01', 'Occupied'),
(5, 4, 'BED-SEMI-01', 'Available'),
(6, 4, 'BED-SEMI-02', 'Available'),
(7, 5, 'BED-GEN-01', 'Available'),
(8, 5, 'BED-GEN-02', 'Available');

-- --------------------------------------------------------
-- 16. Table: admissions (Inpatient Care)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `admissions`;
CREATE TABLE `admissions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `admission_ref` VARCHAR(30) NOT NULL UNIQUE,
  `patient_id` INT NOT NULL,
  `doctor_id` INT NOT NULL,
  `bed_id` INT NOT NULL,
  `admission_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `discharge_date` DATETIME NULL,
  `reason` TEXT NOT NULL,
  `status` ENUM('Admitted', 'Transferred', 'Discharged') DEFAULT 'Admitted',
  `discharge_summary` TEXT NULL,
  CONSTRAINT `fk_adm_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_adm_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors`(`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_adm_bed` FOREIGN KEY (`bed_id`) REFERENCES `beds`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `admissions` (`id`, `admission_ref`, `patient_id`, `doctor_id`, `bed_id`, `admission_date`, `reason`, `status`) VALUES
(1, 'ADM-2026-0041', 1, 1, 4, DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 2 DAY), 'Observation for post-cardiac catheterization stabilization.', 'Admitted');

-- --------------------------------------------------------
-- 17. Table: invoices & invoice_items (Billing Module)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `invoices`;
CREATE TABLE `invoices` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `invoice_number` VARCHAR(30) NOT NULL UNIQUE,
  `patient_id` INT NOT NULL,
  `admission_id` INT NULL,
  `total_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `discount_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `tax_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `net_payable` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `paid_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `payment_status` ENUM('Pending', 'Partially Paid', 'Paid', 'Refunded') DEFAULT 'Pending',
  `invoice_date` DATE NOT NULL DEFAULT (CURRENT_DATE),
  `due_date` DATE NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_inv_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_inv_adm` FOREIGN KEY (`admission_id`) REFERENCES `admissions`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `invoices` (`id`, `invoice_number`, `patient_id`, `admission_id`, `total_amount`, `discount_amount`, `tax_amount`, `net_payable`, `paid_amount`, `payment_status`, `invoice_date`, `due_date`) VALUES
(1, 'MED-INV-2026-0891', 1, NULL, 280.00, 20.00, 13.00, 273.00, 273.00, 'Paid', DATE_SUB(CURRENT_DATE, INTERVAL 10 DAY), CURRENT_DATE),
(2, 'MED-INV-2026-0892', 1, 1, 850.00, 50.00, 40.00, 840.00, 400.00, 'Partially Paid', CURRENT_DATE, DATE_ADD(CURRENT_DATE, INTERVAL 7 DAY));

DROP TABLE IF EXISTS `invoice_items`;
CREATE TABLE `invoice_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `invoice_id` INT NOT NULL,
  `item_type` ENUM('Consultation', 'Lab Test', 'Pharmacy', 'Bed Stay', 'Nursing', 'Procedure') NOT NULL,
  `description` VARCHAR(255) NOT NULL,
  `unit_price` DECIMAL(10,2) NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `subtotal` DECIMAL(10,2) NOT NULL,
  CONSTRAINT `fk_ii_inv` FOREIGN KEY (`invoice_id`) REFERENCES `invoices`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `invoice_items` (`invoice_id`, `item_type`, `description`, `unit_price`, `quantity`, `subtotal`) VALUES
(1, 'Consultation', 'Dr. Marcus Vance - Cardiology Specialist Consultation', 150.00, 1, 150.00),
(1, 'Lab Test', 'Comprehensive Lipid Panel (LAB-LIP-02)', 65.00, 1, 65.00),
(1, 'Pharmacy', 'Atorvastatin 20mg (30 Tablets) & Amlodipine 5mg (30 Tablets)', 65.00, 1, 65.00),
(2, 'Bed Stay', 'Deluxe Private Suite (PVT-301) - 2 Nights Stay', 350.00, 2, 700.00),
(2, 'Nursing', 'Continuous Hemodynamic Inpatient Nursing Care', 150.00, 1, 150.00);

-- --------------------------------------------------------
-- 18. Table: payments
-- --------------------------------------------------------
DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `payment_ref` VARCHAR(30) NOT NULL UNIQUE,
  `invoice_id` INT NOT NULL,
  `amount_paid` DECIMAL(10,2) NOT NULL,
  `payment_method` ENUM('Credit Card', 'Debit Card', 'Cash', 'Online Banking', 'Insurance Claim') DEFAULT 'Credit Card',
  `transaction_reference` VARCHAR(100) NOT NULL,
  `payment_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `received_by` INT NULL,
  CONSTRAINT `fk_pay_inv` FOREIGN KEY (`invoice_id`) REFERENCES `invoices`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `payments` (`payment_ref`, `invoice_id`, `amount_paid`, `payment_method`, `transaction_reference`, `received_by`) VALUES
('PAY-2026-9901', 1, 273.00, 'Credit Card', 'TXN-VISA-9918231', 11),
('PAY-2026-9902', 2, 400.00, 'Insurance Claim', 'CLAIM-BCBS-771891', 11);

-- --------------------------------------------------------
-- 19. Table: hospital_settings
-- --------------------------------------------------------
DROP TABLE IF EXISTS `hospital_settings`;
CREATE TABLE `hospital_settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(100) NOT NULL UNIQUE,
  `setting_value` TEXT NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `hospital_settings` (`setting_key`, `setting_value`) VALUES
('hospital_name', 'MEDINOVA Multi-Speciality Hospital'),
('tagline', 'Advanced Healthcare, Better Tomorrow.'),
('email', 'care@medinova.com'),
('phone', '+1 (800) 555-MEDS (6337)'),
('emergency_phone', '+1 (800) 555-9111'),
('ambulance_phone', '+1 (800) 555-AMBU'),
('address', '750 Health Sciences Blvd, Medical District, New York, NY 10016'),
('operating_hours', 'Outpatient Clinics: Mon - Sat 08:00 AM - 08:00 PM | Emergency & Trauma: 24/7 All Days'),
('google_maps', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d193595.15830869428!2d-74.119763973046!3d40.69766374874431!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c24fa5d33f083b%3A0xc80b8f06e177fe62!2sNew%20York%2C%20NY!5e0!3m2!1sen!2sus!4v1689247190821!5m2!1sen!2sus');

-- --------------------------------------------------------
-- 20. Table: testimonials & contact_messages & blogs
-- --------------------------------------------------------
DROP TABLE IF EXISTS `testimonials`;
CREATE TABLE `testimonials` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `patient_name` VARCHAR(120) NOT NULL,
  `department` VARCHAR(100) NOT NULL,
  `rating` INT DEFAULT 5,
  `comment` TEXT NOT NULL,
  `image` VARCHAR(255) DEFAULT 'images/testimonial/1.webp',
  `is_approved` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `testimonials` (`patient_name`, `department`, `rating`, `comment`, `image`) VALUES
('David Harrison', 'Cardiology', 5, 'Dr. Marcus Vance and the coronary care team at MEDINOVA saved my life during an acute cardiac event. The speed of diagnosis in the emergency cath lab was world-class.', 'images/testimonial/1.webp'),
('Sophie Martinez', 'Neurology', 5, 'After months of unexplained neurological symptoms, Dr. Elena Rostova gave me clear answers, compassionate guidance, and a treatment plan that restored my quality of life.', 'images/testimonial/2.webp'),
('Amanda Clark', 'Pediatrics', 5, 'The pediatric wing at MEDINOVA is incredible. The nurses made my 6-year-old feel comfortable and safe during her appendectomy recovery. Clean, comforting, and professional.', 'images/testimonial/3.webp'),
('Michael Scott', 'Orthopedics', 5, 'Underwent robotic total hip replacement with Dr. Julian Thorne. Was walking with support within 24 hours. Incredible physical therapy and surgical precision.', 'images/testimonial/4.webp');

DROP TABLE IF EXISTS `contact_messages`;
CREATE TABLE `contact_messages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(120) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(30) NULL,
  `department` VARCHAR(100) DEFAULT 'General Inquiry',
  `subject` VARCHAR(200) NOT NULL,
  `message` TEXT NOT NULL,
  `is_read` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `contact_messages` (`name`, `email`, `phone`, `department`, `subject`, `message`) VALUES
('Alexander Wright', 'alex.wright@example.com', '+1 (555) 777-8899', 'Cardiology', 'Insurance Pre-Authorization for Cardiac CT', 'Hello, do you accept BlueCross BlueShield PPO for outpatient cardiac angiography appointments?');

DROP TABLE IF EXISTS `blogs`;
CREATE TABLE `blogs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `author` VARCHAR(100) NOT NULL,
  `department` VARCHAR(100) NOT NULL,
  `excerpt` TEXT NOT NULL,
  `content` TEXT NOT NULL,
  `image` VARCHAR(255) DEFAULT 'images/blog/l1.webp',
  `published_at` DATE DEFAULT (CURRENT_DATE)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `blogs` (`title`, `slug`, `author`, `department`, `excerpt`, `content`, `image`) VALUES
('Early Warning Signs of Heart Disease You Shouldn’t Ignore', 'early-signs-heart-disease', 'Dr. Marcus Vance', 'Cardiology', 
 'Learn the subtle clinical indicators of cardiovascular stress, including chest pressure, exertional fatigue, and irregular rhythms.',
 '<p>Heart disease remains the leading global health challenge, yet early diagnostic intervention saves thousands of lives each year. Warning signs are not always dramatic crushing chest pain; they frequently manifest as sudden shortness of breath upon routine walking, jaw or shoulder tightness, unexplained dizziness, or fluid swelling in the ankles. If you experience these symptoms, prompt consultation with a cardiologist and a resting ECG are vital first steps.</p>', 
 'images/blog/l1.webp'),

('Understanding Acute Stroke: Act FAST to Protect Brain Function', 'understanding-acute-stroke-fast', 'Dr. Elena Rostova', 'Neurology', 
 'Every second counts during a cerebrovascular event. Recognize Facial drooping, Arm weakness, and Speech slurring.',
 '<p>Ischemic stroke occurs when an artery supplying oxygenated blood to brain parenchyma is occluded. The medical rule of thumb is "Time is Brain." Administering thrombolytic clot-busting medication within the critical 3-to-4.5-hour therapeutic window can reverse neurological deficits completely. Familiarize yourself and your family with the FAST protocol: Face, Arms, Speech, Time to call emergency services.</p>', 
 'images/blog/l2.webp'),

('Modern Advances in Robotic-Assisted Joint Replacement', 'robotic-joint-replacement-advances', 'Dr. Julian Thorne', 'Orthopedics', 
 'How 3D robotic pre-surgical modeling and sub-millimeter precision implants are cutting recovery time in half.',
 '<p>Gone are the days of prolonged hospital stays after knee or hip surgery. With CT-derived 3D anatomical modeling and haptic robotic guidance, surgeons now balance soft tissue envelopes with millimeter precision. This preserves healthy bone, reduces intraoperative blood loss, and enables patients to achieve active joint mobilization on the very day of surgery.</p>', 
 'images/blog/l3.webp');

-- --------------------------------------------------------
-- 21. Table: audit_logs (HIPAA & Security Compliance)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE `audit_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NULL,
  `action` VARCHAR(100) NOT NULL,
  `module` VARCHAR(100) NOT NULL,
  `record_id` VARCHAR(50) NULL,
  `details` TEXT NULL,
  `ip_address` VARCHAR(50) DEFAULT '127.0.0.1',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `audit_logs` (`user_id`, `action`, `module`, `record_id`, `details`) VALUES
(1, 'SYSTEM_INIT', 'Core', '0', 'Medinova Multi-Speciality Hospital Management Platform initialized successfully.'),
(2, 'CONSULTATION_NOTE_CREATED', 'MedicalRecords', '1', 'Dr. Marcus Vance recorded clinical findings for Patient MED-PAT-1001.');

SET FOREIGN_KEY_CHECKS = 1;
