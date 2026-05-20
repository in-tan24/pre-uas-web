-- Database: pre_uas_admission
-- Target: MySQL 5.7+ (XAMPP)

CREATE DATABASE IF NOT EXISTS `pre_uas_admission`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `pre_uas_admission`;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `audit_logs`;
DROP TABLE IF EXISTS `ospek_attendance`;
DROP TABLE IF EXISTS `ospek_schedules`;
DROP TABLE IF EXISTS `payments`;
DROP TABLE IF EXISTS `enrollment_records`;
DROP TABLE IF EXISTS `entrance_exams`;
DROP TABLE IF EXISTS `documents`;
DROP TABLE IF EXISTS `applications`;
DROP TABLE IF EXISTS `candidates`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `roles`;
DROP TABLE IF EXISTS `programs`;
DROP TABLE IF EXISTS `faculties`;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE `faculties` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `faculty_name` VARCHAR(150) NOT NULL,
  `description` TEXT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_faculties_name` (`faculty_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `programs` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `faculty_id` INT UNSIGNED NOT NULL,
  `program_name` VARCHAR(200) NOT NULL,
  `capacity` INT UNSIGNED NOT NULL DEFAULT 0,
  `requirements` TEXT NULL,
  `description` TEXT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_programs_faculty` (`faculty_id`),
  CONSTRAINT `fk_programs_faculties`
    FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `roles` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_key` VARCHAR(50) NOT NULL,
  `role_name` VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_roles_key` (`role_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id` INT UNSIGNED NOT NULL,
  `full_name` VARCHAR(200) NOT NULL,
  `username` VARCHAR(60) NOT NULL,
  `email` VARCHAR(191) NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `last_login_at` DATETIME NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_username` (`username`),
  UNIQUE KEY `uq_users_email` (`email`),
  KEY `idx_users_role` (`role_id`),
  CONSTRAINT `fk_users_roles`
    FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `candidates` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `first_name` VARCHAR(100) NOT NULL,
  `last_name` VARCHAR(100) NULL,
  `email` VARCHAR(191) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(30) NULL,
  `date_of_birth` DATE NULL,
  `address` TEXT NULL,
  `status` ENUM(
    'draft',
    'submitted',
    'doc_review',
    'exam_scheduled',
    'accepted',
    'rejected',
    'enrolled'
  ) NOT NULL DEFAULT 'draft',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_candidates_email` (`email`),
  KEY `idx_candidates_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `applications` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `candidate_id` INT UNSIGNED NOT NULL,
  `program_id` INT UNSIGNED NOT NULL,
  `application_date` DATE NOT NULL,
  `submission_date` DATETIME NULL,
  `status` ENUM('pending','submitted','reviewed','approved','rejected','revise') NOT NULL DEFAULT 'pending',
  `review_notes` TEXT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_applications_candidate` (`candidate_id`),
  KEY `idx_applications_program` (`program_id`),
  KEY `idx_applications_status` (`status`),
  CONSTRAINT `fk_applications_candidates`
    FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_applications_programs`
    FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `documents` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `application_id` INT UNSIGNED NOT NULL,
  `document_type` VARCHAR(80) NOT NULL,
  `file_path` VARCHAR(500) NOT NULL,
  `upload_date` DATETIME NOT NULL,
  `status` ENUM('pending','verified','rejected','revise') NOT NULL DEFAULT 'pending',
  `verified_by` INT UNSIGNED NULL,
  `verified_at` DATETIME NULL,
  `notes` TEXT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_documents_app` (`application_id`),
  KEY `idx_documents_type` (`document_type`),
  KEY `idx_documents_status` (`status`),
  CONSTRAINT `fk_documents_applications`
    FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_documents_users`
    FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `entrance_exams` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `application_id` INT UNSIGNED NOT NULL,
  `exam_date` DATETIME NOT NULL,
  `exam_location` VARCHAR(200) NULL,
  `exam_type` ENUM('Written','Online') NOT NULL DEFAULT 'Written',
  `score` DECIMAL(5,2) NULL,
  `status` ENUM('scheduled','completed','passed','failed','absent') NOT NULL DEFAULT 'scheduled',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_exams_app` (`application_id`),
  KEY `idx_exams_status` (`status`),
  CONSTRAINT `fk_exams_applications`
    FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `enrollment_records` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `candidate_id` INT UNSIGNED NOT NULL,
  `program_id` INT UNSIGNED NOT NULL,
  `enrollment_date` DATETIME NOT NULL,
  `status` ENUM('active','inactive','graduated') NOT NULL DEFAULT 'active',
  `student_id` VARCHAR(30) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_enrollments_student_id` (`student_id`),
  KEY `idx_enrollments_candidate` (`candidate_id`),
  KEY `idx_enrollments_program` (`program_id`),
  CONSTRAINT `fk_enrollments_candidates`
    FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_enrollments_programs`
    FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `payments` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `enrollment_id` INT UNSIGNED NOT NULL,
  `amount` DECIMAL(12,2) NOT NULL,
  `payment_date` DATETIME NOT NULL,
  `payment_method` ENUM('cash','transfer','va','card','ewallet') NOT NULL DEFAULT 'transfer',
  `status` ENUM('pending','verified','completed','rejected') NOT NULL DEFAULT 'pending',
  `receipt_file` VARCHAR(500) NULL,
  `verified_by` INT UNSIGNED NULL,
  `verified_at` DATETIME NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_payments_enrollment` (`enrollment_id`),
  KEY `idx_payments_status` (`status`),
  CONSTRAINT `fk_payments_enrollments`
    FOREIGN KEY (`enrollment_id`) REFERENCES `enrollment_records` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_payments_users`
    FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ospek_schedules` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `program_id` INT UNSIGNED NULL,
  `title` VARCHAR(200) NOT NULL,
  `description` TEXT NULL,
  `start_at` DATETIME NOT NULL,
  `end_at` DATETIME NOT NULL,
  `location` VARCHAR(200) NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ospek_program` (`program_id`),
  CONSTRAINT `fk_ospek_programs`
    FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ospek_attendance` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ospek_schedule_id` INT UNSIGNED NOT NULL,
  `candidate_id` INT UNSIGNED NOT NULL,
  `status` ENUM('present','absent','excused') NOT NULL DEFAULT 'present',
  `checked_in_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ospek_att_unique` (`ospek_schedule_id`, `candidate_id`),
  KEY `idx_ospek_att_candidate` (`candidate_id`),
  CONSTRAINT `fk_ospek_att_schedule`
    FOREIGN KEY (`ospek_schedule_id`) REFERENCES `ospek_schedules` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_ospek_att_candidate`
    FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `audit_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `actor_user_id` INT UNSIGNED NULL,
  `actor_candidate_id` INT UNSIGNED NULL,
  `action` VARCHAR(100) NOT NULL,
  `entity` VARCHAR(100) NOT NULL,
  `entity_id` VARCHAR(64) NULL,
  `ip_address` VARCHAR(45) NULL,
  `user_agent` VARCHAR(255) NULL,
  `meta_json` JSON NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_audit_actor_user` (`actor_user_id`),
  KEY `idx_audit_actor_candidate` (`actor_candidate_id`),
  KEY `idx_audit_entity` (`entity`, `entity_id`),
  CONSTRAINT `fk_audit_users`
    FOREIGN KEY (`actor_user_id`) REFERENCES `users` (`id`)
    ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT `fk_audit_candidates`
    FOREIGN KEY (`actor_candidate_id`) REFERENCES `candidates` (`id`)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
