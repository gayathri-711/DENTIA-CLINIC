-- ========================================================
-- Doctor Dental Clinic Dentia - Complete Database Schema
-- MySQL 5.7+ / 8.0+ / 9.0+ Compatible
-- ========================================================

CREATE DATABASE IF NOT EXISTS `dentia_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `dentia_db`;

-- --------------------------------------------------------
-- Table: clinic_settings
-- --------------------------------------------------------
DROP TABLE IF EXISTS `clinic_settings`;
CREATE TABLE `clinic_settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(100) NOT NULL UNIQUE,
  `setting_value` TEXT NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `clinic_settings` (`setting_key`, `setting_value`) VALUES
('clinic_name', 'Doctor Dental Clinic Dentia'),
('tagline', 'Modern Dental Care & Compassionate Smiles'),
('email', 'contact@dentiacare.com'),
('phone', '+1 (800) 234-5678'),
('emergency_phone', '+1 (800) 911-DENT'),
('address', '100 S Main St, Suite 400, New York, NY 10001'),
('opening_hours_weekday', 'Monday - Friday: 08:00 AM - 08:00 PM'),
('opening_hours_weekend', 'Saturday: 09:00 AM - 05:00 PM (Emergency On-Call Sunday)'),
('google_map_embed', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d193595.15830869428!2d-74.119763973046!3d40.69766374874431!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c24fa5d33f083b%3A0xc80b8f06e177fe62!2sNew%20York%2C%20NY!5e0!3m2!1sen!2sus!4v1689247190821!5m2!1sen!2sus');

-- --------------------------------------------------------
-- Table: admins
-- --------------------------------------------------------
DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(120) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` VARCHAR(50) DEFAULT 'admin',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default password is 'admin123'
INSERT INTO `admins` (`name`, `email`, `password`, `role`) VALUES
('Chief Medical Admin', 'admin@dentia.com', '$2y$10$eE0m9L1Z5Qf1m4WbT2K3teK1pMhVj7V9c9zD1bC7/XG3j8Z1F1Mxe', 'superadmin');

-- --------------------------------------------------------
-- Table: patients
-- --------------------------------------------------------
DROP TABLE IF EXISTS `patients`;
CREATE TABLE `patients` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(120) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `phone` VARCHAR(30) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `dob` DATE NULL,
  `gender` ENUM('Male', 'Female', 'Other') DEFAULT 'Male',
  `address` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default sample patient password is 'patient123'
INSERT INTO `patients` (`name`, `email`, `phone`, `password`, `dob`, `gender`, `address`) VALUES
('John Doe', 'john.doe@example.com', '+1 (555) 234-5678', '$2y$10$eE0m9L1Z5Qf1m4WbT2K3teK1pMhVj7V9c9zD1bC7/XG3j8Z1F1Mxe', '1990-05-15', 'Male', '742 Evergreen Terrace, New York, NY'),
('Emily Watson', 'emily.watson@example.com', '+1 (555) 987-6543', '$2y$10$eE0m9L1Z5Qf1m4WbT2K3teK1pMhVj7V9c9zD1bC7/XG3j8Z1F1Mxe', '1995-10-22', 'Female', '452 Broadway Ave, Brooklyn, NY');

-- --------------------------------------------------------
-- Table: dentists
-- --------------------------------------------------------
DROP TABLE IF EXISTS `dentists`;
CREATE TABLE `dentists` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(120) NOT NULL,
  `qualification` VARCHAR(255) NOT NULL,
  `specialization` VARCHAR(150) NOT NULL,
  `experience` VARCHAR(50) NOT NULL,
  `email` VARCHAR(150) NULL,
  `login_email` VARCHAR(150) NULL,
  `password` VARCHAR(255) NULL,
  `has_login` TINYINT(1) DEFAULT 0,
  `phone` VARCHAR(50) NULL,
  `description` TEXT NOT NULL,
  `image` VARCHAR(255) DEFAULT 'images/team/1.webp',
  `availability_days` VARCHAR(100) DEFAULT 'Mon, Tue, Wed, Thu, Fri',
  `availability_hours` VARCHAR(100) DEFAULT '09:00 - 17:00',
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `dentists` (`id`, `name`, `qualification`, `specialization`, `experience`, `email`, `login_email`, `password`, `has_login`, `phone`, `description`, `image`, `availability_days`, `availability_hours`, `is_active`) VALUES
(1, 'Dr. Sarah Bennett', 'Doctor of Dental Surgery (DDS), NYU College of Dentistry', 'Lead Dentist & General Care', '12+ Years Experience', 'sarah.bennett@dentiacare.com', 'sarah.bennett@dentiacare.com', '$2y$10$eE0m9L1Z5Qf1m4WbT2K3teK1pMhVj7V9c9zD1bC7/XG3j8Z1F1Mxe', 1, '+1 (800) 234-5671', 'Dr. Sarah Bennett leads our clinical practice with a focus on comprehensive preventive dentistry, painless procedures, and restorative prosthetics.', 'images/team/1.webp', 'Monday - Friday', '09:00 - 17:00', 1),
(2, 'Dr. Maya Lin', 'Doctor of Medicine in Dentistry (DMD), Univ. of Pennsylvania', 'Cosmetic & Aesthetic Dentistry', '9+ Years Experience', 'maya.lin@dentiacare.com', 'maya.lin@dentiacare.com', '$2y$10$eE0m9L1Z5Qf1m4WbT2K3teK1pMhVj7V9c9zD1bC7/XG3j8Z1F1Mxe', 1, '+1 (800) 234-5672', 'Specializing in smile redesigns, porcelain veneers, and professional laser whitening treatments with artistic precision.', 'images/team/2.webp', 'Monday - Thursday', '10:00 - 18:00', 1),
(3, 'Dr. James Rodriguez', 'Doctor of Dental Surgery (DDS), Columbia University', 'Orthodontics & Implants Specialist', '14+ Years Experience', 'james.rodriguez@dentiacare.com', 'james.rodriguez@dentiacare.com', '$2y$10$eE0m9L1Z5Qf1m4WbT2K3teK1pMhVj7V9c9zD1bC7/XG3j8Z1F1Mxe', 1, '+1 (800) 234-5673', 'Board-certified Orthodontist and Implantologist dedicated to invisible aligners, complex bite corrections, and titanium dental implants.', 'images/team/3.webp', 'Tuesday - Saturday', '09:00 - 16:00', 1),
(4, 'Dr. Emily Chen', 'Doctor of Dental Surgery (DDS), UCLA School of Dentistry', 'Pediatric Dentistry & Gum Therapy', '8+ Years Experience', 'emily.chen@dentiacare.com', 'emily.chen@dentiacare.com', '$2y$10$eE0m9L1Z5Qf1m4WbT2K3teK1pMhVj7V9c9zD1bC7/XG3j8Z1F1Mxe', 1, '+1 (800) 234-5674', 'Renowned for gentle and engaging pediatric care, root canal treatments, and preventative periodontal therapy for the whole family.', 'images/team/4.webp', 'Monday - Friday', '08:30 - 16:30', 1),
(5, 'Dr. Alexander Vance', 'DDS, Harvard School of Dental Medicine', 'Periodontics & Advanced Implantology', '15+ Years Experience', 'alexander.vance@dentiacare.com', 'alexander.vance@dentiacare.com', NULL, 0, '+1 (800) 234-5678', 'Experienced dental professional dedicated to comprehensive oral care and patient comfort.', 'images/team/1.webp', 'Monday - Friday', '09:00 - 17:00', 1);

-- --------------------------------------------------------
-- Table: notifications
-- --------------------------------------------------------
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `recipient_email` VARCHAR(150) NOT NULL,
  `recipient_phone` VARCHAR(50) NULL,
  `recipient_name` VARCHAR(120) NOT NULL,
  `appointment_ref` VARCHAR(50) NOT NULL,
  `channel` ENUM('email', 'sms') DEFAULT 'email',
  `type` VARCHAR(50) NOT NULL,
  `subject` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `preview_url` VARCHAR(255) NULL,
  `html` MEDIUMTEXT NULL,
  `status` VARCHAR(100) DEFAULT 'Delivered',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: services
-- --------------------------------------------------------
DROP TABLE IF EXISTS `services`;
CREATE TABLE `services` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `slug` VARCHAR(150) NOT NULL UNIQUE,
  `icon` VARCHAR(100) DEFAULT 'images/icons/tooth-1.png',
  `image` VARCHAR(255) DEFAULT 'images/misc/l1.webp',
  `short_description` VARCHAR(255) NOT NULL,
  `full_description` TEXT NOT NULL,
  `benefits` TEXT NOT NULL,
  `price` DECIMAL(10,2) DEFAULT 0.00,
  `duration` VARCHAR(50) DEFAULT '45 mins',
  `is_featured` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `services` (`id`, `name`, `slug`, `icon`, `image`, `short_description`, `full_description`, `benefits`, `price`, `duration`, `is_featured`) VALUES
(1, 'General Dentistry', 'general-dentistry', 'images/icons/tooth-1.png', 'images/misc/l1.webp', 
 'Complete oral care for every smile with cleanings, exams, and diagnostics.', 
 'Our general dentistry services focus on proactive prevention, accurate digital diagnostics, and effective treatment for patients of all ages. Regular examinations ensure optimal oral health and prevent dental disease before it starts.',
 'Complete Oral Health Examinations\nDigital Low-Radiation X-Rays\nTooth-Colored Biomimetic Fillings\nFluoride & Sealant Treatments\nPersonalized Oral Hygiene Care Plans', 
 85.00, '45-60 mins', 1),

(2, 'Teeth Whitening', 'teeth-whitening', 'images/icons/tooth-2.png', 'images/misc/l2.webp', 
 'Achieve a radiant, luminous smile up to 8 shades brighter in a single session.', 
 'Our in-office laser teeth whitening safely dissolves deep stains caused by coffee, tea, wine, and aging. Formulated with enamel-safe protective gels that deliver instant, dazzling, and long-lasting brightness.',
 'Immediate Results in Just 60 Minutes\nSafe for Sensitive Teeth & Enamel\nRemoves Deep Intrinsic & Extrinsic Stains\nCustom Take-Home Maintenance Trays\nUp to 8 Shades Brighter in One Visit', 
 199.00, '60 mins', 1),

(3, 'Dental Implants', 'dental-implants', 'images/icons/tooth-3.png', 'images/misc/l3.webp', 
 'Permanent, natural-looking replacement for missing teeth with medical titanium.', 
 'Dental implants provide a sturdy, lifelong foundation for replacement teeth that look, feel, and function just like natural teeth. Prevent jawbone loss and restore your full chewing power with 3D-guided implant placement.',
 'Permanent, Lifetime Solution for Missing Teeth\nPreserves Facial Structure & Bone Density\nFully Restores Chewing & Speaking Ability\nMatches Your Natural Tooth Color & Contour\nHigh 98% Documented Clinical Success Rate', 
 850.00, '90 mins', 1),

(4, 'Root Canal Treatment', 'root-canal-treatment', 'images/icons/tooth-4.png', 'images/misc/l4.webp', 
 'Gentle, pain-free therapy designed to save severely infected or damaged teeth.', 
 'Modern root canal therapy is quick, comfortable, and painless. Using advanced rotary micro-endodontics and precision digital imaging, we clear out infection, disinfect the root canals, and seal the tooth to preserve its natural structure.',
 'Instant Relief from Acute Tooth Pain\nSaves Natural Tooth from Extraction\nMicroscope-Assisted Precision Care\nPainless with Modern Local Anesthesia\nRestored with a Strong Custom Crown', 
 320.00, '60-75 mins', 1),

(5, 'Orthodontics and Braces', 'orthodontics-and-braces', 'images/icons/tooth-5.png', 'images/misc/l5.webp', 
 'Clear aligners and modern braces for beautifully aligned teeth and balanced bites.', 
 'Straighten misaligned teeth and fix bite issues comfortably. We provide state-of-the-art clear aligners (Invisalign certified), ceramic discreet brackets, and traditional orthodontics for teenagers and adults.',
 'Discreet Clear Aligners & Aesthetic Brackets\nCorrects Overbite, Underbite, & Crowding\nImproves Chewing Function & Jaw Comfort\nDigital 3D Smile Simulation Preview\nTailored Payment & Flexible Financing Plans', 
 1200.00, '45 mins', 1),

(6, 'Cosmetic Dentistry', 'cosmetic-dentistry', 'images/icons/tooth-2.png', 'images/misc/l6.webp', 
 'Transform your smile with handcrafted porcelain veneers, contouring, and bonding.', 
 'Cosmetic dentistry blends art and science to craft your dream smile. From porcelain veneers that conceal chips and gaps to composite bonding and gum reshaping, we give you confident, camera-ready smiles.',
 'Custom Porcelain Veneers & Lumineers\nComposite Bonding for Gaps & Chipped Teeth\nAesthetic Gum Line Recontouring\nDigital Smile Design Custom Mockups\nNatural Translucency and Long Durability', 
 450.00, '60 mins', 1),

(7, 'Pediatric Dentistry', 'pediatric-dentistry', 'images/icons/tooth-6.png', 'images/misc/s1.webp', 
 'Child-friendly dental care in a warm, welcoming, and anxiety-free environment.', 
 'We specialize in making dental visits fun, positive, and stress-free for kids. From toddler checkups to preventive sealants and habit counseling, we help your child cultivate lifelong healthy brushing habits.',
 'Gentle, Anxiety-Free Pediatric Dental Team\nProtective Dental Sealants & Fluoride\nHabit Guidance (Thumb Sucking & Teething)\nEarly Orthodontic Growth Monitoring\nFun Prizes and Educational Smile Games', 
 95.00, '30-45 mins', 1),

(8, 'Tooth Extraction', 'tooth-extraction', 'images/icons/tooth-7.png', 'images/misc/s2.webp', 
 'Safe, painless extraction of wisdom teeth, impacted teeth, or severe damage.', 
 'When a tooth is broken beyond repair or impacted (like problematic wisdom teeth), our oral surgeons carry out gentle, swift extractions with modern sedation options to keep you completely relaxed throughout.',
 'Minimally Invasive Atraumatic Extraction\nWisdom Tooth Removal with Sedation Options\nFast Healing with Platelet-Rich Fibrin Care\nComprehensive Aftercare Support & Guidance\nImmediate Bone Grafting for Future Implants', 
 150.00, '45-60 mins', 1),

(9, 'Dental Cleaning', 'dental-cleaning', 'images/icons/tooth-1.png', 'images/misc/s3.webp', 
 'Ultrasonic plaque, tartar, and stain removal for fresh breath and radiant enamel.', 
 'Professional dental prophylaxis cleans deep where regular toothbrushes cannot reach. Ultrasonic scalers remove calcified tartar, eliminate odor-causing bacteria, and polish enamel to a smooth finish.',
 'Ultrasonic Scaling for Deep Tartar Removal\nAir-Flow Stain Polishing Technology\nEliminates Bad Breath (Halitosis)\nPrevents Gingivitis & Cavity Formation\nLeaves Teeth Feeling Incredibly Smooth & Clean', 
 75.00, '40 mins', 1),

(10, 'Gum Treatment', 'gum-treatment', 'images/icons/tooth-4.png', 'images/misc/s4.webp', 
 'Comprehensive periodontal therapy to stop bleeding gums and treat gum disease.', 
 'Healthy gums are the foundation of healthy teeth. Our periodontal treatments treat gingivitis and periodontitis through deep cleaning (scaling and root planing), laser pocket therapy, and antimicrobial rinses.',
 'Halts Gum Bleeding, Swelling, and Soreness\nDeep Scaling & Root Planing (SRP)\nLaser-Assisted Gum Sterilization\nPrevents Bone Loss and Tooth Looseness\nLong-Term Periodontal Maintenance Plans', 
 210.00, '60 mins', 1);

-- --------------------------------------------------------
-- Table: appointments
-- --------------------------------------------------------
DROP TABLE IF EXISTS `appointments`;
CREATE TABLE `appointments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `appointment_ref` VARCHAR(30) NOT NULL UNIQUE,
  `patient_id` INT NULL,
  `patient_name` VARCHAR(120) NOT NULL,
  `patient_email` VARCHAR(150) NOT NULL,
  `patient_phone` VARCHAR(30) NOT NULL,
  `service_id` INT NOT NULL,
  `dentist_id` INT NOT NULL,
  `appointment_date` DATE NOT NULL,
  `appointment_time` VARCHAR(20) NOT NULL,
  `message` TEXT NULL,
  `status` ENUM('Pending', 'Confirmed', 'Completed', 'Cancelled') DEFAULT 'Pending',
  `cancellation_reason` VARCHAR(255) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_dentist_date_time` (`dentist_id`, `appointment_date`, `appointment_time`),
  INDEX `idx_patient_email` (`patient_email`),
  CONSTRAINT `fk_app_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_app_service` FOREIGN KEY (`service_id`) REFERENCES `services`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_app_dentist` FOREIGN KEY (`dentist_id`) REFERENCES `dentists`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Sample Appointments
INSERT INTO `appointments` (`appointment_ref`, `patient_id`, `patient_name`, `patient_email`, `patient_phone`, `service_id`, `dentist_id`, `appointment_date`, `appointment_time`, `message`, `status`) VALUES
('DEN-2026-1001', 1, 'John Doe', 'john.doe@example.com', '+1 (555) 234-5678', 1, 1, DATE_ADD(CURRENT_DATE, INTERVAL 2 DAY), '10:00 AM', 'Routine six-month dental checkup and cleaning.', 'Confirmed'),
('DEN-2026-1002', 2, 'Emily Watson', 'emily.watson@example.com', '+1 (555) 987-6543', 2, 2, DATE_ADD(CURRENT_DATE, INTERVAL 3 DAY), '11:00 AM', 'Interested in in-office laser teeth whitening.', 'Pending'),
('DEN-2026-1003', NULL, 'Robert Taylor', 'robert.taylor@example.com', '+1 (555) 345-6789', 3, 3, DATE_ADD(CURRENT_DATE, INTERVAL 4 DAY), '02:00 PM', 'Consultation for dental implant on lower molar.', 'Confirmed'),
('DEN-2026-1004', NULL, 'Jessica Alba', 'jessica.alba@example.com', '+1 (555) 678-1234', 5, 3, DATE_SUB(CURRENT_DATE, INTERVAL 5 DAY), '03:00 PM', 'Braces adjustment and wire tightening.', 'Completed');

-- --------------------------------------------------------
-- Table: patient_medical_records
-- --------------------------------------------------------
DROP TABLE IF EXISTS `patient_medical_records`;
CREATE TABLE `patient_medical_records` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `patient_id` INT NOT NULL,
  `dentist_id` INT NOT NULL,
  `appointment_id` INT NULL,
  `appointment_ref` VARCHAR(30) NULL,
  `treatment_date` DATE NOT NULL,
  `diagnosis` TEXT NOT NULL,
  `treatment_provided` TEXT NOT NULL,
  `prescription` TEXT NULL,
  `doctor_notes` TEXT NULL,
  `followup_date` DATE NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_med_patient` (`patient_id`),
  INDEX `idx_med_dentist` (`dentist_id`),
  CONSTRAINT `fk_med_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_med_dentist` FOREIGN KEY (`dentist_id`) REFERENCES `dentists`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `patient_medical_records` (`patient_id`, `dentist_id`, `appointment_ref`, `treatment_date`, `diagnosis`, `treatment_provided`, `prescription`, `doctor_notes`, `followup_date`) VALUES
(1, 1, 'DEN-2026-1001', CURRENT_DATE, 'Mild plaque accumulation and localized gingivitis on lower incisors.', 'Ultrasonic scaling and polishing with fluoride varnish application.', 'Chlorhexidine Gluconate 0.12% oral rinse - 15ml twice daily for 7 days.', 'Advised patient to practice daily flossing. Recommended soft-bristled brush.', DATE_ADD(CURRENT_DATE, INTERVAL 6 MONTH));

-- --------------------------------------------------------
-- Table: testimonials
-- --------------------------------------------------------
DROP TABLE IF EXISTS `testimonials`;
CREATE TABLE `testimonials` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `patient_name` VARCHAR(120) NOT NULL,
  `patient_role` VARCHAR(100) DEFAULT 'Patient',
  `rating` INT DEFAULT 5,
  `comment` TEXT NOT NULL,
  `image` VARCHAR(255) DEFAULT 'images/testimonial/1.webp',
  `is_approved` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `testimonials` (`patient_name`, `patient_role`, `rating`, `comment`, `image`, `is_approved`) VALUES
('Michael Scott', 'Business Executive', 5, 'Dr. Sarah Bennett and the Dentia team are phenomenal. The clinic is spotless, high-tech, and the entire dental implant procedure was completely painless. Highly recommended!', 'images/testimonial/1.webp', 1),
('Sophie Martinez', 'Graphic Designer', 5, 'I underwent laser teeth whitening before my wedding day. My teeth are noticeably whiter and there was zero sensitivity. The staff is so gentle and supportive!', 'images/testimonial/2.webp', 1),
('David Harrison', 'Software Engineer', 5, 'Getting Invisalign here with Dr. Rodriguez changed my smile entirely. The digital scanning was so cool, no messy putty molds. Clean, professional, and on schedule.', 'images/testimonial/3.webp', 1),
('Amanda Clark', 'School Teacher', 5, 'My 7-year-old daughter used to be terrified of the dentist. Dr. Emily Chen made the visit fun and enjoyable for her. We wouldn’t go anywhere else!', 'images/testimonial/4.webp', 1);

-- --------------------------------------------------------
-- Table: contact_messages
-- --------------------------------------------------------
DROP TABLE IF EXISTS `contact_messages`;
CREATE TABLE `contact_messages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(120) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(30) NULL,
  `subject` VARCHAR(200) DEFAULT 'General Inquiry',
  `message` TEXT NOT NULL,
  `is_read` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `contact_messages` (`name`, `email`, `phone`, `subject`, `message`, `is_read`) VALUES
('Alexander Wright', 'alex.wright@example.com', '+1 (555) 777-8899', 'Dental Insurance Query', 'Hello, do you accept MetLife and Delta Dental PPO insurance plans for root canal treatments?', 0),
('Rachel Green', 'rachel.g@example.com', '+1 (555) 444-3322', 'Weekend Appointment Inquiry', 'Is Dr. Sarah Bennett available for emergency Saturday appointments?', 1);

-- --------------------------------------------------------
-- Table: faqs
-- --------------------------------------------------------
DROP TABLE IF EXISTS `faqs`;
CREATE TABLE `faqs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `question` VARCHAR(255) NOT NULL,
  `answer` TEXT NOT NULL,
  `category` VARCHAR(50) DEFAULT 'General',
  `display_order` INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `faqs` (`question`, `answer`, `category`, `display_order`) VALUES
('How often should I visit the dentist for a checkup?', 'We recommend visiting Dentia every six months for a comprehensive checkup and professional cleaning. Regular checkups allow us to detect potential issues early, preventing costly and invasive treatments later.', 'General', 1),
('Are dental treatments at Dentia painless?', 'Yes! We prioritize patient comfort with state-of-the-art painless anesthesia techniques, micro-invasive rotary tools, and compassionate sedation options to make your visit stress-free.', 'General', 2),
('How long does laser teeth whitening last?', 'Our in-office whitening results typically last between 1 to 3 years, depending on your dietary habits (such as coffee, tea, or red wine consumption) and daily oral hygiene routine.', 'Cosmetic', 3),
('What should I do in a dental emergency?', 'Call our 24/7 Dental Emergency Hotline immediately at +1 (800) 911-DENT. For a knocked-out tooth, place it in milk or saliva and come directly to our clinic within 60 minutes for the highest re-implantation success.', 'Emergency', 4),
('How can I prevent duplicate bookings?', 'Our live booking system checks real-time slot availability for each dentist. Once a time slot is reserved, it is locked immediately to prevent any double-booking.', 'Booking', 5);

-- --------------------------------------------------------
-- Table: blogs
-- --------------------------------------------------------
DROP TABLE IF EXISTS `blogs`;
CREATE TABLE `blogs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `excerpt` TEXT NOT NULL,
  `content` TEXT NOT NULL,
  `image` VARCHAR(255) DEFAULT 'images/blog/l1.webp',
  `author` VARCHAR(100) DEFAULT 'Dr. Sarah Bennett',
  `category` VARCHAR(100) DEFAULT 'Oral Health',
  `published_at` DATE DEFAULT (CURRENT_DATE)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `blogs` (`title`, `slug`, `excerpt`, `content`, `image`, `author`, `category`, `published_at`) VALUES
('5 Essential Tips for Healthy Teeth and Gums', '5-essential-tips-healthy-teeth', 
 'Discover daily dental habits that keep your teeth strong, plaque-free, and your breath fresh throughout the day.', 
 '<p>Good oral hygiene is the foundation of overall wellness. Here are five simple yet crucial habits to adopt: brush twice daily with fluoride toothpaste, floss before bedtime to remove interdental food debris, rinse with an antimicrobial mouthwash, drink plenty of water, and never skip your bi-annual dental checkup.</p>', 
 'images/blog/l1.webp', 'Dr. Sarah Bennett', 'Dental Care', '2026-10-15'),

('Everything You Need to Know About Dental Implants', 'everything-about-dental-implants', 
 'Missing teeth? Learn why modern titanium dental implants are the gold standard for restoring chewing power and aesthetics.', 
 '<p>A dental implant is a titanium post surgically positioned into the jawbone beneath the gum line. It provides permanent support for prosthetic crowns or bridges. Unlike dentures, implants do not slip or click, and they actively prevent facial bone resorption.</p>', 
 'images/blog/l2.webp', 'Dr. James Rodriguez', 'Implantology', '2026-10-28'),

('The Truth About Teeth Whitening: Myths vs Facts', 'teeth-whitening-myths-vs-facts', 
 'Separating commercial myths from clinically proven whitening methods that protect your enamel while delivering bright smiles.', 
 '<p>Many over-the-counter DIY kits can damage tooth enamel or cause severe gum irritation. Professional in-office laser treatments utilize pH-balanced hydrogen peroxide gels activated by specialized light frequencies for fast, safe, and even shade enhancement.</p>', 
 'images/blog/l3.webp', 'Dr. Maya Lin', 'Aesthetic Care', '2026-11-02');

