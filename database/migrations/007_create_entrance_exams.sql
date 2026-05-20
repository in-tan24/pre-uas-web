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

