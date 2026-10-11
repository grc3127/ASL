-- KIDSS system completion migration
-- Apply this AFTER importing kidss_db.sql.
-- This migration preserves existing users, lessons, quiz attempts, and progress.
-- It adds persistent per-student quiz state so navigation/reopening the quiz resumes
-- the current attempt instead of silently resetting it. Notifications are removed
-- because the application no longer uses a notification feature.

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS notifications;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE IF NOT EXISTS student_quiz_state (
  state_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  student_id INT UNSIGNED NOT NULL,
  category_id INT UNSIGNED NOT NULL,
  quiz_id INT UNSIGNED NOT NULL,
  question_index INT UNSIGNED NOT NULL DEFAULT 0,
  score INT UNSIGNED NOT NULL DEFAULT 0,
  question_order_json LONGTEXT NOT NULL,
  answers_json LONGTEXT NOT NULL,
  completed TINYINT(1) NOT NULL DEFAULT 0,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (state_id),
  UNIQUE KEY uq_student_quiz_category (student_id, category_id),
  KEY idx_quiz_state_student (student_id),
  KEY idx_quiz_state_category (category_id),
  KEY idx_quiz_state_quiz (quiz_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Persisted administrative settings used by the system settings page.
CREATE TABLE IF NOT EXISTS system_settings (
  setting_key VARCHAR(100) NOT NULL,
  setting_value TEXT NOT NULL,
  updated_by INT UNSIGNED DEFAULT NULL,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (setting_key),
  KEY idx_system_settings_updated_by (updated_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO system_settings (setting_key, setting_value) VALUES
  ('system_name', 'KIDSS'),
  ('school_year', '2026-2027');

-- Repair missing role-specific profiles for accounts that already exist.
-- Existing names and identifiers are preserved; only absent profile rows are created.
INSERT INTO admins (user_id, first_name, middle_name, last_name)
SELECT u.user_id,
       CONCAT(UPPER(LEFT(REPLACE(REPLACE(SUBSTRING_INDEX(u.email,'@',1),'.',' '),'_',' '),1)),
              SUBSTRING(REPLACE(REPLACE(SUBSTRING_INDEX(u.email,'@',1),'.',' '),'_',' '),2)),
       NULL, 'User'
FROM users u JOIN roles r ON r.role_id=u.role_id
WHERE LOWER(r.role_name) IN ('admin','administrator')
  AND NOT EXISTS (SELECT 1 FROM admins a WHERE a.user_id=u.user_id);

INSERT INTO teachers (user_id, employee_number, first_name, middle_name, last_name)
SELECT u.user_id, NULL,
       CONCAT(UPPER(LEFT(REPLACE(REPLACE(SUBSTRING_INDEX(u.email,'@',1),'.',' '),'_',' '),1)),
              SUBSTRING(REPLACE(REPLACE(SUBSTRING_INDEX(u.email,'@',1),'.',' '),'_',' '),2)),
       NULL, 'User'
FROM users u JOIN roles r ON r.role_id=u.role_id
WHERE LOWER(r.role_name)='teacher'
  AND NOT EXISTS (SELECT 1 FROM teachers t WHERE t.user_id=u.user_id);

INSERT INTO students (user_id, student_number, first_name, middle_name, last_name)
SELECT u.user_id, NULL,
       CONCAT(UPPER(LEFT(REPLACE(REPLACE(SUBSTRING_INDEX(u.email,'@',1),'.',' '),'_',' '),1)),
              SUBSTRING(REPLACE(REPLACE(SUBSTRING_INDEX(u.email,'@',1),'.',' '),'_',' '),2)),
       NULL, 'User'
FROM users u JOIN roles r ON r.role_id=u.role_id
WHERE LOWER(r.role_name)='student'
  AND NOT EXISTS (SELECT 1 FROM students s WHERE s.user_id=u.user_id);
