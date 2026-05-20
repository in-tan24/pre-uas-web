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

