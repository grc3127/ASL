https://docs.google.com/spreadsheets/d/1tGgXoFbHq1CAuQ9vMf2qdTkgW9G8tR5gsPTttuam0UM/edit?gid=1739620952#gid=1739620952

**Main Page**: index.php
Note: only load from index.php

**User Credentials**:
admin:
user: a
pass: a

teacher:
user: b
pass: b

student:
user: c
pass: c

## System completion integration

The integration work is being developed on the `integration/system-completion` branch. The existing `kidss_db.sql` schema remains the base database because it already contains users, students, teachers, classes, lessons, progress, achievements, quizzes, and quiz attempts.

### Database setup

1. Back up your current database first.
2. Import `kidss_db.sql` into MariaDB/MySQL if you are rebuilding the database from the exported snapshot.
3. Import the latest `kidss_system_upgrade.sql` into the same database. It adds `student_quiz_state` and `system_settings`, removes the unused `notifications` table, and creates missing `admins`, `teachers`, and `students` profile rows for existing users without overwriting profiles that already exist.
4. Confirm `config/database.php` points to your database. You can use the `KIDSS_DB_HOST`, `KIDSS_DB_PORT`, `KIDSS_DB_NAME`, `KIDSS_DB_USER`, and `KIDSS_DB_PASS` environment variables.
5. Open the site through `index.php`, not by opening nested PHP pages directly.

### Integration notes

- Student and teacher account lists are queried from `users`, `students`, `teachers`, `roles`, and `account_status`.
- Classes and sections use `classes` and `class_students`. Administrators assign teachers and manage sections; teachers can enroll or transfer students only within their own assigned classes.
- Student lesson progress is stored in `student_progress`; quiz attempts are stored in `quiz_attempts`; achievement awards are stored in `student_achievements`.
- Student lessons and picture-quiz questions are derived from published database lessons and media, rather than a fixed A–Z list.
- The notification page and navigation have been removed. The upgrade script removes the old notifications table.
- Keep the existing database backup until local login, account CRUD, class enrollment/transfer, lesson completion, quiz resume/completion, achievements, and teacher progress logs have been tested.

- The administrator's reports, feedback inbox, activity logs, profile, and basic system settings are database-backed. Student feedback submissions are saved in `feedback`; successful sign-ins and management changes are written to `activity_logs`.
- Quiz scores are recalculated by the server from submitted answers before an attempt is recorded; the browser's displayed score is not trusted as the source of truth.

### Latest main-branch fixes

- Lesson Management now provides file-picker controls for the thumbnail, image, audio, and video. Selected files upload to `assets/lesson-media/` and automatically fill the corresponding relative path. Ensure that folder is writable by the PHP/web-server account and that PHP upload limits allow the intended file size (maximum supported by the endpoint: 100 MB).
- The login flow repairs a missing role-specific profile row when an existing user signs in. The profile page and account editor also repair or upsert missing profile rows instead of failing silently.
- If you already imported an older version of `kidss_system_upgrade.sql`, import the updated file again; it uses `CREATE TABLE IF NOT EXISTS` and only inserts profile rows that are missing.
