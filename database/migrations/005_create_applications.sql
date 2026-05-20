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

