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

