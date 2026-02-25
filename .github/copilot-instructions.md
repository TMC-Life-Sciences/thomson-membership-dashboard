# Copilot Instructions – Thomson Membership Dashboard

## Project Overview

PHP 8.0 web dashboard for **Thomson Hospital Kota Damansara (THKD)** managing two membership programmes:

- **TGP** – Thomson Golden Years Privilege → tables/files suffixed `_tgp`
- **TKC** – Thomson Kids Club → tables/files suffixed `_tkc` (TKC members can have child records in the `child` table via FK)

Stack: PHP 8.0 on XAMPP, MariaDB/MySQL, PHPMailer, Bootstrap 4, DataTables. No MVC framework — all logic, queries, and HTML live in single `.php` files.

---

## Architecture & Page Lifecycle

Every authenticated page follows this pattern:

```php
include 'header.php';   // handles DB connection, session validation, idle timeout
// ... page-specific queries and HTML ...
include 'footer.php';
```

- `config/config.inc.php` — DB credentials, SMTP config, and the `encryptor()` helper (AES-256-CBC). This file opens `$conn` and is the source of truth for all globals.
- `config/session.php` — `secure_session_start()` / `validate_session()`. Session name is `membership_session`. Detects concurrent logins via `session_id` column in `user` table.
- `login.php` / `initial.php` — the only pages that **do not** include `header.php`; they manage their own bootstrap.

---

## Critical Conventions

### URL Parameter Encryption

All record IDs passed via `$_GET` are AES-256-CBC encrypted. Always decrypt before use:

```php
$pat_id = encryptor('decrypt', $_GET['id']);
```

And encrypt before building links:

```php
$enc = encryptor('encrypt', $row['pat_id']);
echo "<a href='pat_details_tgp.php?id=$enc'>View</a>";
```

### Password Hashing

Passwords are hashed with `sha256`, **not** bcrypt:

```php
$password = hash('sha256', $password);
```

### DB Query Style — Mixed

Older pages use `mysqli_query($conn, $sql)` directly; newer/updated pages use prepared statements `$conn->prepare(...)`. Prefer prepared statements for any new or edited queries.

### User Feedback Pattern

All success/error messages use inline JS — no server-side redirects with flash messages:

```php
echo "<script>window.alert('Success message')</script>";
echo "<script>window.location.replace('pm_tgp.php')</script>";
```

### Email (PHPMailer)

Pages that send email (`update_pat_tgp.php`, `update_pat_tkc.php`) manually require PHPMailer **and** the composer autoload:

```php
require 'vendor/autoload.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
```

SMTP credentials come from `$smtpHost`, `$smtpUsername`, etc., set in `config/config.inc.php`.

---

## Roles & Access

| Role     | Value in DB | Access                                |
| -------- | ----------- | ------------------------------------- |
| Admin/IT | `IT`        | Full access including user management |
| Staff    | `User`      | Membership data read/write only       |

`initial = 1` forces first-login password change via `initial.php`. `$_SESSION['role']` and `$_SESSION['department']` gate UI elements.

---

## Membership Status Flow

`Pending Registration` → `Successfully Registered In SAP`

Status is set on record creation (`pm_tgp.php` / `pm_tkc.php`) and updated to "Successfully Registered In SAP" when an MRN is assigned in `update_pat_tgp.php` / `update_pat_tkc.php`. The update also triggers a PHPMailer confirmation email.

---

## Database & Migration

```bash
# Run from project root (CLI only)
php database/migrate.php
# Options: 1=Fresh Install, 2=Alter Table Only, 3=Seed Only
```

- `database/migrate.php` — schema + interactive runner; auto-detects missing database and prompts to create it.
- `database/seeder.php` — idempotent `INSERT … ON DUPLICATE KEY UPDATE` for the `user` table; can be run standalone.

### Environment

Credentials are read from `.env` (gitignored). Copy `.env.example` → `.env` and set `DB_SERVER`, `DB_USERNAME`, `DB_PASSWORD`, `DB_DATABASE`. `config/config.inc.php` is the live connection; `migrate.php` parses the config file directly via regex to avoid `die()` on missing DB.

---

## Key File Map

| File                                              | Purpose                                      |
| ------------------------------------------------- | -------------------------------------------- |
| `config/config.inc.php`                           | DB conn, SMTP, `encryptor()`, `$conn` global |
| `config/session.php`                              | Session management & concurrent-login guard  |
| `header.php`                                      | Universal page bootstrap + sidebar HTML      |
| `pm_tgp.php` / `pm_tkc.php`                       | Membership list + new registration form      |
| `pat_details_tgp.php` / `pat_details_tkc.php`     | Read-only patient detail view                |
| `update_pat_tgp.php` / `update_pat_tkc.php`       | Edit patient + assign MRN + send email       |
| `parent.php`                                      | TKC child records view                       |
| `manageuser.php` / `adduser.php` / `edituser.php` | User CRUD (IT role only)                     |
| `database/migrate.php`                            | CLI schema migration runner                  |
| `database/seeder.php`                             | User table seeder                            |
| `email_template/`                                 | HTML email templates loaded by PHPMailer     |
