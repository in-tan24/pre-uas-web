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

