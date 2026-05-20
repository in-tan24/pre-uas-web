USE `pre_uas_admission`;

INSERT INTO `faculties` (`faculty_name`, `description`) VALUES
('Fakultas Ilmu Komputer', 'Program berbasis teknologi dan komputasi'),
('Fakultas Ekonomi', 'Program ekonomi dan bisnis');

INSERT INTO `programs` (`faculty_id`, `program_name`, `capacity`, `requirements`, `description`) VALUES
(1, 'Teknik Informatika', 120, 'Ijazah SMA/SMK sederajat', 'S1 Teknik Informatika'),
(1, 'Sistem Informasi', 100, 'Ijazah SMA/SMK sederajat', 'S1 Sistem Informasi'),
(2, 'Manajemen', 150, 'Ijazah SMA/SMK sederajat', 'S1 Manajemen');

INSERT INTO `roles` (`role_key`, `role_name`) VALUES
('superadmin', 'Superadmin'),
('admin', 'Admin/Reviewer'),
('finance', 'Finance Officer');

-- Default password: admin123
INSERT INTO `users` (`role_id`, `full_name`, `username`, `email`, `password_hash`)
SELECT r.id, 'Super Admin', 'superadmin', 'superadmin@local.test', '$2y$10$bDYkBZtw/5RhfgncyigUkuxDy0pzbux6hxWf9dV3OoITe8BaOKiyK'
FROM roles r WHERE r.role_key = 'superadmin';

INSERT INTO `users` (`role_id`, `full_name`, `username`, `email`, `password_hash`)
SELECT r.id, 'Admin Reviewer', 'admin', 'admin@local.test', '$2y$10$bDYkBZtw/5RhfgncyigUkuxDy0pzbux6hxWf9dV3OoITe8BaOKiyK'
FROM roles r WHERE r.role_key = 'admin';

INSERT INTO `users` (`role_id`, `full_name`, `username`, `email`, `password_hash`)
SELECT r.id, 'Finance Officer', 'finance', 'finance@local.test', '$2y$10$bDYkBZtw/5RhfgncyigUkuxDy0pzbux6hxWf9dV3OoITe8BaOKiyK'
FROM roles r WHERE r.role_key = 'finance';
