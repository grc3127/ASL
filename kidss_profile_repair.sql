-- KIDSS role-profile repair for the database exported from main.
-- Run this after importing kidss_db.sql. It preserves all existing profiles and
-- creates only missing profile rows for existing user accounts.
START TRANSACTION;

INSERT INTO admins (user_id, first_name, middle_name, last_name)
SELECT u.user_id,
       CONCAT(UPPER(LEFT(REPLACE(REPLACE(SUBSTRING_INDEX(u.email,'@',1),'.',' '),'_',' '),1)),
              SUBSTRING(REPLACE(REPLACE(SUBSTRING_INDEX(u.email,'@',1),'.',' '),'_',' '),2)),
       NULL, 'User'
FROM users u
JOIN roles r ON r.role_id=u.role_id
WHERE LOWER(r.role_name) IN ('admin','administrator')
  AND NOT EXISTS (SELECT 1 FROM admins a WHERE a.user_id=u.user_id);

INSERT INTO teachers (user_id, employee_number, first_name, middle_name, last_name)
SELECT u.user_id, NULL,
       CONCAT(UPPER(LEFT(REPLACE(REPLACE(SUBSTRING_INDEX(u.email,'@',1),'.',' '),'_',' '),1)),
              SUBSTRING(REPLACE(REPLACE(SUBSTRING_INDEX(u.email,'@',1),'.',' '),'_',' '),2)),
       NULL, 'User'
FROM users u
JOIN roles r ON r.role_id=u.role_id
WHERE LOWER(r.role_name)='teacher'
  AND NOT EXISTS (SELECT 1 FROM teachers t WHERE t.user_id=u.user_id);

INSERT INTO students (user_id, student_number, first_name, middle_name, last_name)
SELECT u.user_id, NULL,
       CONCAT(UPPER(LEFT(REPLACE(REPLACE(SUBSTRING_INDEX(u.email,'@',1),'.',' '),'_',' '),1)),
              SUBSTRING(REPLACE(REPLACE(SUBSTRING_INDEX(u.email,'@',1),'.',' '),'_',' '),2)),
       NULL, 'User'
FROM users u
JOIN roles r ON r.role_id=u.role_id
WHERE LOWER(r.role_name)='student'
  AND NOT EXISTS (SELECT 1 FROM students s WHERE s.user_id=u.user_id);

COMMIT;

-- The generated profile name is intentionally a safe starter value when no
-- name existed anywhere in the database. Users/admins can edit it in the UI.
-- Run this query to verify that every account has its role-specific profile:
SELECT u.user_id,u.username,r.role_name,
       CASE WHEN LOWER(r.role_name) IN ('admin','administrator') THEN a.admin_id
            WHEN LOWER(r.role_name)='teacher' THEN t.teacher_id
            WHEN LOWER(r.role_name)='student' THEN s.student_id END AS profile_id
FROM users u JOIN roles r ON r.role_id=u.role_id
LEFT JOIN admins a ON a.user_id=u.user_id
LEFT JOIN teachers t ON t.user_id=u.user_id
LEFT JOIN students s ON s.user_id=u.user_id
WHERE (LOWER(r.role_name) IN ('admin','administrator') AND a.admin_id IS NULL)
   OR (LOWER(r.role_name)='teacher' AND t.teacher_id IS NULL)
   OR (LOWER(r.role_name)='student' AND s.student_id IS NULL);

-- Keep the settings table available to class management and the settings page.
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
