-- Child Health Monitoring System (CHMS) Database Schema
-- Created: December 1, 2025
-- Database: chms_db

DROP DATABASE IF EXISTS chms_db;
CREATE DATABASE chms_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE chms_db;

-- Table: users
-- Stores information about mothers and doctors using the system
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    role ENUM('mother', 'doctor') NOT NULL DEFAULT 'mother',
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: children
-- Stores child profile information
CREATE TABLE children (
    child_id INT AUTO_INCREMENT PRIMARY KEY,
    mother_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    dob DATE NOT NULL,
    gender ENUM('male', 'female') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mother_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_mother (mother_id),
    INDEX idx_dob (dob)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: health_records
-- Stores monthly health check-up data for children
CREATE TABLE health_records (
    record_id INT AUTO_INCREMENT PRIMARY KEY,
    child_id INT NOT NULL,
    weight DECIMAL(5,2) NOT NULL COMMENT 'Weight in kilograms',
    height DECIMAL(5,2) NOT NULL COMMENT 'Height in meters',
    nutrition_status VARCHAR(50) DEFAULT 'Normal',
    vaccinations TEXT COMMENT 'JSON or comma-separated list of vaccines',
    doctor_notes TEXT,
    record_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (child_id) REFERENCES children(child_id) ON DELETE CASCADE,
    INDEX idx_child (child_id),
    INDEX idx_date (record_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: alerts
-- Stores health alerts for children based on growth deviations
CREATE TABLE alerts (
    alert_id INT AUTO_INCREMENT PRIMARY KEY,
    child_id INT NOT NULL,
    alert_type VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'resolved') DEFAULT 'pending',
    FOREIGN KEY (child_id) REFERENCES children(child_id) ON DELETE CASCADE,
    INDEX idx_child (child_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: immunizations
-- Tracks vaccination schedules for children
CREATE TABLE immunizations (
    immunization_id INT AUTO_INCREMENT PRIMARY KEY,
    child_id INT NOT NULL,
    vaccine_name VARCHAR(100) NOT NULL,
    date_given DATE NOT NULL,
    next_due_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (child_id) REFERENCES children(child_id) ON DELETE CASCADE,
    INDEX idx_child (child_id),
    INDEX idx_next_due (next_due_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: mother_health_records
-- Stores health records for mothers (prenatal, postnatal, and general health)
CREATE TABLE mother_health_records (
    record_id INT AUTO_INCREMENT PRIMARY KEY,
    mother_id INT NOT NULL,
    record_type ENUM('prenatal', 'postnatal', 'general') NOT NULL DEFAULT 'general',
    record_date DATE NOT NULL,
    weight DECIMAL(5,2) COMMENT 'Weight in kilograms',
    blood_pressure VARCHAR(20) COMMENT 'e.g., 120/80',
    hemoglobin DECIMAL(4,2) COMMENT 'Hemoglobin level in g/dL',
    blood_sugar DECIMAL(5,2) COMMENT 'Blood sugar level in mg/dL',
    pregnancy_week INT COMMENT 'Week of pregnancy (for prenatal records)',
    delivery_date DATE COMMENT 'Date of delivery (for postnatal records)',
    delivery_type ENUM('normal', 'cesarean', 'assisted') COMMENT 'Type of delivery',
    complications TEXT COMMENT 'Any complications or concerns',
    medications TEXT COMMENT 'Current medications',
    doctor_notes TEXT,
    next_checkup_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mother_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_mother (mother_id),
    INDEX idx_date (record_date),
    INDEX idx_type (record_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample users (passwords are hashed using PHP password_hash)
-- Password for all users: "password123"
INSERT INTO users (name, email, phone, role, password) VALUES
('Dr. Sarah Johnson', 'sarah.johnson@chms.com', '+1-555-0101', 'doctor', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Dr. Michael Chen', 'michael.chen@chms.com', '+1-555-0102', 'doctor', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Mary Williams', 'mary.williams@email.com', '+1-555-0201', 'mother', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Jennifer Davis', 'jennifer.davis@email.com', '+1-555-0202', 'mother', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Patricia Martinez', 'patricia.martinez@email.com', '+1-555-0203', 'mother', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Linda Anderson', 'linda.anderson@email.com', '+1-555-0204', 'mother', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insert sample children
INSERT INTO children (mother_id, name, dob, gender) VALUES
(3, 'Emma Williams', '2024-06-15', 'female'),
(3, 'Noah Williams', '2022-03-22', 'male'),
(4, 'Olivia Davis', '2024-08-10', 'female'),
(5, 'Liam Martinez', '2023-12-05', 'male'),
(5, 'Sophia Martinez', '2024-02-18', 'female'),
(6, 'Ava Anderson', '2024-05-30', 'female');

-- Insert sample health records
INSERT INTO health_records (child_id, weight, height, nutrition_status, vaccinations, doctor_notes, record_date) VALUES
-- Emma Williams (6 months old)
(1, 7.2, 0.65, 'Normal', 'BCG, Hepatitis B, DPT-1, Polio-1', 'Healthy growth. Continue breastfeeding.', '2024-07-15'),
(1, 7.8, 0.67, 'Normal', 'DPT-2, Polio-2', 'Good weight gain. Development on track.', '2024-08-15'),
(1, 8.3, 0.69, 'Normal', 'DPT-3, Polio-3', 'Excellent progress. Start introducing solid foods.', '2024-09-15'),
(1, 8.9, 0.71, 'Normal', 'Measles-1', 'Growing well. Motor skills developing normally.', '2024-10-15'),
(1, 9.4, 0.73, 'Normal', '', 'Healthy baby. Continue current feeding schedule.', '2024-11-15'),

-- Noah Williams (2 years old)
(2, 12.5, 0.86, 'Normal', 'MMR, Varicella', 'Active and healthy. Language development appropriate.', '2024-03-22'),
(2, 13.2, 0.89, 'Normal', '', 'Good growth. Encourage physical activity.', '2024-06-22'),
(2, 13.8, 0.91, 'Normal', '', 'Developing well. No concerns.', '2024-09-22'),

-- Olivia Davis (4 months old)
(3, 6.5, 0.61, 'Normal', 'BCG, Hepatitis B, DPT-1', 'Good health. Breastfeeding well.', '2024-09-10'),
(3, 7.1, 0.63, 'Normal', 'DPT-2, Polio-2', 'Gaining weight appropriately.', '2024-10-10'),
(3, 7.6, 0.65, 'Normal', 'DPT-3, Polio-3', 'Healthy progress. All milestones met.', '2024-11-10'),

-- Liam Martinez (1 year old)
(4, 9.8, 0.75, 'Normal', 'Measles, MMR', 'Walking independently. Excellent development.', '2024-01-05'),
(4, 10.2, 0.77, 'Normal', '', 'Good appetite. Growing steadily.', '2024-04-05'),
(4, 10.8, 0.80, 'Normal', '', 'Very active. Speech developing well.', '2024-07-05'),
(4, 11.3, 0.82, 'Normal', '', 'Healthy and happy. Continue current care.', '2024-10-05'),

-- Sophia Martinez (10 months old)
(5, 8.5, 0.72, 'Normal', 'BCG, DPT series complete', 'Thriving. Starting to crawl.', '2024-03-18'),
(5, 9.1, 0.74, 'Normal', 'Measles-1', 'Good weight gain. Eating solids well.', '2024-06-18'),
(5, 9.6, 0.76, 'Normal', '', 'Active baby. Development normal.', '2024-09-18'),

-- Ava Anderson (6 months old)
(6, 7.0, 0.64, 'Normal', 'BCG, Hepatitis B, DPT-1, Polio-1', 'Healthy baby. Good feeding.', '2024-06-30'),
(6, 7.5, 0.66, 'Normal', 'DPT-2, Polio-2', 'Appropriate growth. No concerns.', '2024-07-30'),
(6, 8.0, 0.68, 'Normal', 'DPT-3, Polio-3', 'Excellent progress. Ready for solid foods.', '2024-08-30'),
(6, 8.5, 0.70, 'Normal', '', 'Growing well. All milestones achieved.', '2024-09-30'),
(6, 9.0, 0.72, 'Normal', 'Measles-1', 'Healthy development. Keep up good care.', '2024-10-30');

-- Insert sample alerts
INSERT INTO alerts (child_id, alert_type, message, status) VALUES
(1, 'vaccination_due', 'DPT-4 booster vaccination due next month', 'pending'),
(2, 'checkup_reminder', 'Annual health checkup recommended', 'pending'),
(3, 'vaccination_due', 'Measles vaccination due in 2 weeks', 'pending'),
(4, 'checkup_reminder', 'Monthly growth monitoring due', 'resolved'),
(5, 'vaccination_due', 'MMR vaccine due next month', 'pending');

-- Insert sample immunization schedules
INSERT INTO immunizations (child_id, vaccine_name, date_given, next_due_date) VALUES
-- Emma Williams
(1, 'BCG', '2024-06-15', NULL),
(1, 'Hepatitis B (Birth dose)', '2024-06-15', '2024-07-15'),
(1, 'DPT-1', '2024-07-15', '2024-08-15'),
(1, 'Polio-1', '2024-07-15', '2024-08-15'),
(1, 'DPT-2', '2024-08-15', '2024-09-15'),
(1, 'Polio-2', '2024-08-15', '2024-09-15'),
(1, 'DPT-3', '2024-09-15', '2025-09-15'),
(1, 'Polio-3', '2024-09-15', NULL),
(1, 'Measles-1', '2024-10-15', '2025-06-15'),

-- Noah Williams
(2, 'BCG', '2022-03-22', NULL),
(2, 'DPT Series', '2022-07-22', NULL),
(2, 'Polio Series', '2022-07-22', NULL),
(2, 'Measles', '2023-03-22', NULL),
(2, 'MMR', '2024-03-22', '2026-03-22'),
(2, 'Varicella', '2024-03-22', NULL),

-- Olivia Davis
(3, 'BCG', '2024-08-10', NULL),
(3, 'Hepatitis B', '2024-08-10', '2024-09-10'),
(3, 'DPT-1', '2024-09-10', '2024-10-10'),
(3, 'Polio-1', '2024-09-10', '2024-10-10'),
(3, 'DPT-2', '2024-10-10', '2024-11-10'),
(3, 'Polio-2', '2024-10-10', '2024-11-10'),
(3, 'DPT-3', '2024-11-10', '2025-11-10'),
(3, 'Polio-3', '2024-11-10', NULL),

-- Liam Martinez
(4, 'BCG', '2023-12-05', NULL),
(4, 'DPT Series', '2024-02-05', NULL),
(4, 'Polio Series', '2024-02-05', NULL),
(4, 'Measles', '2024-06-05', NULL),
(4, 'MMR', '2024-12-05', '2026-12-05'),

-- Sophia Martinez
(5, 'BCG', '2024-02-18', NULL),
(5, 'DPT Series', '2024-04-18', NULL),
(5, 'Polio Series', '2024-04-18', NULL),
(5, 'Measles-1', '2024-08-18', '2025-02-18'),

-- Ava Anderson
(6, 'BCG', '2024-05-30', NULL),
(6, 'Hepatitis B', '2024-05-30', '2024-06-30'),
(6, 'DPT-1', '2024-06-30', '2024-07-30'),
(6, 'Polio-1', '2024-06-30', '2024-07-30'),
(6, 'DPT-2', '2024-07-30', '2024-08-30'),
(6, 'Polio-2', '2024-07-30', '2024-08-30'),
(6, 'DPT-3', '2024-08-30', '2025-08-30'),
(6, 'Polio-3', '2024-08-30', NULL),
(6, 'Measles-1', '2024-10-30', '2025-05-30');

-- Create views for easier data access
CREATE VIEW v_children_with_mothers AS
SELECT 
    c.child_id,
    c.name AS child_name,
    c.dob,
    c.gender,
    TIMESTAMPDIFF(MONTH, c.dob, CURDATE()) AS age_months,
    u.user_id AS mother_id,
    u.name AS mother_name,
    u.email AS mother_email,
    u.phone AS mother_phone
FROM children c
JOIN users u ON c.mother_id = u.user_id
WHERE u.role = 'mother';

CREATE VIEW v_latest_health_records AS
SELECT 
    hr.*,
    c.name AS child_name,
    c.dob,
    c.gender,
    u.name AS mother_name
FROM health_records hr
JOIN children c ON hr.child_id = c.child_id
JOIN users u ON c.mother_id = u.user_id
WHERE hr.record_date = (
    SELECT MAX(record_date) 
    FROM health_records 
    WHERE child_id = hr.child_id
);

CREATE VIEW v_pending_alerts AS
SELECT 
    a.*,
    c.name AS child_name,
    c.dob,
    u.name AS mother_name,
    u.email AS mother_email
FROM alerts a
JOIN children c ON a.child_id = c.child_id
JOIN users u ON c.mother_id = u.user_id
WHERE a.status = 'pending'
ORDER BY a.created_at DESC;

CREATE VIEW v_upcoming_immunizations AS
SELECT 
    i.*,
    c.name AS child_name,
    c.dob,
    u.name AS mother_name,
    u.email AS mother_email
FROM immunizations i
JOIN children c ON i.child_id = c.child_id
JOIN users u ON c.mother_id = u.user_id
WHERE i.next_due_date IS NOT NULL 
    AND i.next_due_date >= CURDATE()
ORDER BY i.next_due_date ASC;

CREATE VIEW v_latest_mother_health_records AS
SELECT 
    mhr.*,
    u.name AS mother_name,
    u.email AS mother_email,
    u.phone AS mother_phone
FROM mother_health_records mhr
JOIN users u ON mhr.mother_id = u.user_id
WHERE u.role = 'mother'
    AND mhr.record_date = (
        SELECT MAX(record_date) 
        FROM mother_health_records 
        WHERE mother_id = mhr.mother_id
    );

-- Insert sample mother health records
INSERT INTO mother_health_records (mother_id, record_type, record_date, weight, blood_pressure, hemoglobin, blood_sugar, complications, medications, doctor_notes, next_checkup_date) VALUES
-- Mary Williams (postnatal - 6 months after delivery)
(3, 'postnatal', '2024-06-15', 68.5, '118/75', 12.5, 95.0, NULL, 'Multivitamin', 'Recovery excellent. Continue breastfeeding. Iron levels good.', '2024-09-15'),
(3, 'postnatal', '2024-09-15', 66.0, '120/78', 13.0, 92.0, NULL, 'Multivitamin', 'Healthy. Regular exercise recommended.', '2024-12-15'),

-- Jennifer Davis (postnatal - 4 months after delivery)
(4, 'postnatal', '2024-08-10', 72.0, '125/82', 12.0, 98.0, 'Mild anemia', 'Iron supplement, Multivitamin', 'Anemia improving. Continue iron supplementation.', '2024-11-10'),
(4, 'postnatal', '2024-11-10', 70.5, '122/80', 12.8, 94.0, NULL, 'Multivitamin', 'Anemia resolved. Good overall health.', '2025-02-10'),

-- Patricia Martinez (general health)
(5, 'general', '2024-03-10', 65.0, '115/72', 13.5, 88.0, NULL, NULL, 'Excellent health. Continue healthy lifestyle.', '2024-09-10'),
(5, 'general', '2024-09-10', 64.5, '118/74', 13.2, 90.0, NULL, NULL, 'All parameters normal. Annual checkup recommended.', '2025-03-10'),

-- Linda Anderson (postnatal - 6 months after delivery)
(6, 'postnatal', '2024-05-30', 70.0, '120/78', 12.3, 96.0, NULL, 'Multivitamin, Calcium', 'Recovery good. Breastfeeding well established.', '2024-08-30'),
(6, 'postnatal', '2024-08-30', 68.5, '118/76', 12.9, 93.0, NULL, 'Multivitamin', 'Healthy. All vitals normal.', '2024-11-30'),
(6, 'postnatal', '2024-11-30', 67.0, '119/77', 13.1, 91.0, NULL, 'Multivitamin', 'Excellent progress. Continue routine care.', '2025-02-28');

-- Success message
SELECT 'Database created successfully!' AS Status;
