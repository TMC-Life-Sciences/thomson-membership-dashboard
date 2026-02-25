# Thomson Membership Dashboard

A web-based membership management dashboard for **Thomson Hospital Kota Damansara (THKD)**, managing two patient membership programmes:

| Programme                      | Code    | Description                                       |
| ------------------------------ | ------- | ------------------------------------------------- |
| Thomson Golden Years Privilege | **TGP** | Senior membership programme                       |
| Thomson Kids Club              | **TKC** | Children's membership; supports child sub-records |

---

## Tech Stack

| Layer        | Technology                        |
| ------------ | --------------------------------- |
| Server       | PHP 8.0 (XAMPP / cPanel ea-php80) |
| Database     | MariaDB / MySQL                   |
| Frontend     | Bootstrap 4, DataTables, jQuery   |
| Email        | PHPMailer (SMTP / SSL)            |
| Dependencies | Composer (`vlucas/phpdotenv`)     |

---

## Requirements

- PHP 8.0+
- MySQL / MariaDB
- Composer
- XAMPP (local) or cPanel hosting (production)

---

## Local Setup

### 1. Clone & install dependencies

```bash
git clone <repo-url> thomson-membership-dashboard
cd thomson-membership-dashboard
composer install
```

### 2. Configure environment

```bash
cp .env.example .env
```

Edit `.env` with your local credentials:

```dotenv
DB_SERVER=localhost
DB_USERNAME=root
DB_PASSWORD=
DB_DATABASE=thomson1_thkdmembership
```

Update `config/config.inc.php` to match the same values — this file is the live DB connection used by all pages.

### 3. Run the database migration

```bash
php database/migrate.php
```

Select an option when prompted:

| Option | Action                                                                    |
| ------ | ------------------------------------------------------------------------- |
| `1`    | **Fresh Install** — drops and recreates all tables, then seeds user data  |
| `2`    | **Alter Table Only** — safely adds missing columns/keys to an existing DB |
| `3`    | **Seed Only** — upserts user seed data without touching the schema        |

> If the database does not exist, the runner will prompt you to create it automatically.

### 4. Start XAMPP and open in browser

```
http://localhost/thomson-membership-dashboard/
```

---

## Default Login

After a fresh install the seeded admin account is:

| Field    | Value                                       |
| -------- | ------------------------------------------- |
| Username | `admin`                                     |
| Password | _(set in seeder — sha256 hash `c75f28...`)_ |
| Role     | `IT` (full access)                          |

> All users with `initial = 1` are forced to change their password on first login.

---

## Project Structure

```
├── config/
│   ├── config.inc.php      # DB connection, SMTP config, encryptor() helper
│   └── session.php         # Session start, validation, concurrent-login guard
├── database/
│   ├── migrate.php         # CLI migration runner (Fresh / Alter / Seed)
│   └── seeder.php          # Idempotent user table seeder
├── email_template/         # HTML templates used by PHPMailer
├── PHPMailer/              # PHPMailer source (also loaded via composer)
├── vendor/                 # Composer dependencies
├── header.php              # Universal authenticated page bootstrap + sidebar
├── footer.php              # Page footer / closing scripts
├── login.php               # Login page (does not include header.php)
├── initial.php             # Forced first-login password change
├── main.php                # Dashboard home — TGP/TKC summary counts
├── pm_tgp.php              # TGP membership list + registration form
├── pm_tkc.php              # TKC membership list + registration form
├── pat_details_tgp.php     # Read-only TGP patient detail view
├── pat_details_tkc.php     # Read-only TKC patient detail view
├── update_pat_tgp.php      # Edit TGP record + assign MRN + send email
├── update_pat_tkc.php      # Edit TKC record + assign MRN + send email
├── parent.php              # TKC child sub-records view
├── manageuser.php          # User list (IT role only)
├── adduser.php             # Add user (IT role only)
├── edituser.php            # Edit user (IT role only)
├── changepass.php          # Change own password
├── report.php              # Membership report view
└── .env.example            # Environment variable template
```

---

## Database Schema

### Core Tables

| Table         | Purpose                                                              |
| ------------- | -------------------------------------------------------------------- |
| `pat_tgp`     | TGP member records                                                   |
| `pat_tkc`     | TKC member records                                                   |
| `child`       | Child records linked to TKC members (FK → `pat_tkc.pat_id`, CASCADE) |
| `user`        | Staff accounts                                                       |
| `id_generate` | Running number generator for member IDs                              |
| `otp`         | OTP records for password-related flows                               |

### Membership Status Flow

```
Pending Registration  →  Successfully Registered In SAP
```

A record is created with `Pending Registration`. When staff assign an MRN in `update_pat_tgp.php` / `update_pat_tkc.php`, the status changes to `Successfully Registered In SAP` and a confirmation email is sent to the member.

---

## User Roles

| Role       | DB Value | Capabilities                                        |
| ---------- | -------- | --------------------------------------------------- |
| Admin / IT | `IT`     | Full access — user management + all membership data |
| Staff      | `User`   | Membership data read/write; no user management      |

Session variables used for gating: `$_SESSION['role']`, `$_SESSION['department']`.

---

## Security Notes

- All record IDs in URLs are **AES-256-CBC encrypted** via `encryptor()` — never pass raw IDs in `$_GET`.
- Passwords are hashed with **SHA-256** (not bcrypt).
- Accounts are **locked for 15 minutes** after 5 consecutive failed login attempts.
- **Concurrent login detection** — a second login from another device prompts the user to terminate the first session.
- Session cookie is set to `Strict` SameSite; `secure` flag is enabled in non-localhost environments.

---

## Email Configuration

Email is sent via PHPMailer with SMTP/SSL. Configure in `config/config.inc.php`:

```php
$smtpHost     = 'mail.example.com';
$smtpUsername = 'no-reply@example.com';
$smtpPassword = 'yourpassword';
$smtpSecure   = 'ssl';
$smtpPort     = 465;
```

Email templates are stored in `email_template/` as HTML files loaded by `update_pat_tgp.php` and `update_pat_tkc.php`.

---

## Running the Seeder Standalone

```bash
php database/seeder.php
```

Uses `INSERT … ON DUPLICATE KEY UPDATE` — safe to run multiple times without duplicating data.
