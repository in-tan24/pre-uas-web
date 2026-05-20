CREATE DATABASE IF NOT EXISTS pmb_campusflow
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE pmb_campusflow;

CREATE TABLE candidates (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(180) NOT NULL,
  email VARCHAR(191) NOT NULL UNIQUE,
  phone VARCHAR(30) NOT NULL,
  origin_school VARCHAR(180) NOT NULL,
  program_name VARCHAR(120) NOT NULL,
  status ENUM('submitted','doc_review','exam_scheduled','accepted','rejected','enrolled') NOT NULL DEFAULT 'submitted',
  exam_score DECIMAL(5,2) NULL,
  payment_status ENUM('pending','completed') NOT NULL DEFAULT 'pending',
  ospek_status ENUM('not_started','present') NOT NULL DEFAULT 'not_started',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE candidate_documents (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  candidate_id INT UNSIGNED NOT NULL,
  document_name VARCHAR(120) NOT NULL,
  status ENUM('pending','verified','rejected') NOT NULL DEFAULT 'pending',
  FOREIGN KEY (candidate_id) REFERENCES candidates(id) ON DELETE CASCADE
);
