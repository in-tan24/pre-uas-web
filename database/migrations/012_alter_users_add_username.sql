ALTER TABLE `users`
  ADD COLUMN `username` VARCHAR(60) NOT NULL AFTER `full_name`,
  MODIFY COLUMN `email` VARCHAR(191) NULL;

-- Best-effort populate for existing rows (only if username empty / not exists before)
UPDATE `users`
SET `username` = LEFT(SUBSTRING_INDEX(COALESCE(`email`, CONCAT('user', `id`)), '@', 1), 60)
WHERE `username` = '' OR `username` IS NULL;

ALTER TABLE `users`
  ADD UNIQUE KEY `uq_users_username` (`username`);

