# KIDSS Role-Based Authentication and Routing

## Authentication flow

```text
index.php
  -> login.php
  -> auth/login_process.php
  -> config/database.php
  -> users + roles + account_status + profile table
  -> auth/role_redirect.php
       -> admin_dashboard.php
       -> teacher_dashboard.php
       -> student_dashboard.php
```

## Authentication files

- `config/database.php` — PDO connection to `kidss_db`.
- `auth/login_process.php` — validates credentials, role, and account status.
- `auth/guard.php` — protects pages and checks roles.
- `auth/role_redirect.php` — routes authenticated users to the correct dashboard.
- `logout.php` — destroys the active PHP session.

## Database requirements

The authentication code uses the finalized KIDSS schema:

- `users`
- `roles`
- `account_status`
- `admins`
- `teachers`
- `students`

Passwords must be stored in `users.password_hash` using PHP's `password_hash()` function. Login verification uses `password_verify()`.

Only accounts with `account_status.status_id = 1` (`Active`) are allowed to log in.

## Database connection defaults

For a normal XAMPP local installation, the defaults are:

```text
Host:     127.0.0.1
Port:     3306
Database: kidss_db
Username: root
Password: [empty]
```

If your MySQL credentials are different, set these server environment variables:

```text
KIDSS_DB_HOST
KIDSS_DB_PORT
KIDSS_DB_NAME
KIDSS_DB_USER
KIDSS_DB_PASS
```

## Creating passwords

Do not insert plaintext passwords into `users.password_hash`.

Generate a hash with PHP:

```php
password_hash('YourPasswordHere', PASSWORD_DEFAULT);
```

Then insert the resulting hash into `users.password_hash`.

## Session data

After successful login, the application stores the authenticated user's:

- `user_id`
- `username`
- `email`
- `role_id`
- `role`
- `role_name`
- `status_id`
- `status_name`
- `name`
- role-specific profile ID
- student number or teacher employee number where applicable

The session ID is regenerated after successful authentication.
