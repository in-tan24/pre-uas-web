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

