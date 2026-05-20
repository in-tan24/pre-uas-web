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

