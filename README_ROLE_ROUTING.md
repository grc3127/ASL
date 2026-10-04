# KIDSS Role-Based Routing Update

## New authentication flow

index.php
  -> login.php
  -> auth/login_process.php
  -> auth/role_redirect.php
       -> admin_dashboard.php
       -> teacher_dashboard.php
       -> student_dashboard.php

## Security structure

- `auth/guard.php` contains reusable role guards.
- `student_dashboard.php` requires the `student` role.
- `teacher_dashboard.php` requires the `teacher` role.
- `admin_dashboard.php` requires `admin` or `administrator`.
- `logout.php` destroys the PHP session.
- `login_process.php` is the only database-authentication integration point.

## IMPORTANT: database authentication is not enabled yet

The repository currently does not expose a finalized account/database schema.
Therefore `authenticateUser()` intentionally returns `false` instead of inventing
table or column names.

When you provide the SQL schema, replace only that function with a prepared query
and `password_verify()`.

Recommended account result:
- user_id
- role
- full_name/name
- email

Recommended password storage:
- `password_hash($password, PASSWORD_DEFAULT)`
- `password_verify($password, $hash)`

Do NOT store plaintext passwords.

## Existing student UI

The existing student sidebar navigation is preserved. A Logout item has been
added, and `student_dashboard.php` now displays the logged-in user's session name.

## Admin UI

The previously created administrator pages are included under `admin_pages/`.
The admin shell is now protected by the administrator role.
