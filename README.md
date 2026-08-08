# Event Management System (EMS)

A production-ready, multi-event registration platform built with **PHP 8+, MySQL, Bootstrap 5** — no framework, clean MVC architecture.

---

## Features

| Module | Details |
|---|---|
| **Authentication** | Secure admin login, BCrypt hashing, session management, CSRF protection, role-based access (superadmin / admin / moderator / viewer) |
| **Dashboard** | Live stats: total events, active events, registrations, check-ins; recent registrations table; quick action shortcuts |
| **Event Management** | Create/Edit/Delete events; publish/unpublish toggle; open/close registration toggle; banner image upload; capacity limits; registration deadlines; speakers; schedule/programme builder |
| **Registration System** | Public event listing & detail pages; full registration form (name, email, phone, gender, DOB, church, state, city, address, emergency contact); duplicate prevention by email per event |
| **Registration Codes** | Auto-generated sequential codes in format `EMS-2026-000001` (prefix and year configurable) |
| **QR Codes** | PNG QR codes generated per registration encoding a secure check-in URL; downloadable; shown on success page and confirmation email |
| **Confirmation Email** | HTML email with registration details and embedded QR code via PHPMailer + SMTP |
| **Admin Registrations** | Search, filter by event / date / gender / church / check-in status; view detail; edit; delete; export CSV; print attendee pass |
| **QR Check-In** | Camera scanner (html5-qrcode); manual code lookup; real-time attendee display; one-click check-in; duplicate check-in detection |
| **Reports** | Daily registrations chart; gender doughnut; top churches; top states; attendance %, no-shows; CSV export |
| **Settings** | Church name, logo, primary/secondary color, SMTP config, timezone, registration code prefix — all editable via UI |
| **Security** | PDO prepared statements, CSRF tokens, XSS output escaping, session hardening, file upload MIME validation, path traversal prevention |

---

## Project Structure

```
ems/
├── app/
│   ├── Controllers/        AuthController, DashboardController,
│   │                       EventController, PublicController,
│   │                       RegistrationController, ReportController,
│   │                       SettingsController
│   ├── Models/             Admin, Event, Registration, Setting
│   ├── Helpers/            Database, Helper, Mailer, QRCode, Session
│   └── Middleware/         AuthMiddleware (auth + CSRF guard)
├── config/
│   └── config.php          All constants, autoloader, error handling
├── database/
│   └── schema.sql          Normalized MySQL schema
├── public/                 ← Web root (point server here)
│   ├── index.php           Front controller
│   ├── .htaccess           Apache rewrite rules
│   ├── assets/
│   │   ├── css/            admin.css, public.css
│   │   └── js/             admin.js
│   └── uploads/
│       ├── banners/        Event banner images
│       └── qrcodes/        Generated QR PNGs
├── routes/
│   ├── Router.php          Regex-based URL router
│   └── web.php             All route definitions
├── storage/
│   └── logs/               PHP errors, DB errors, mailer errors
├── vendor/
│   ├── phpmailer/          PHPMailer (download separately)
│   └── phpqrcode/          phpqrcode library (download separately)
├── views/
│   ├── admin/              All admin views
│   ├── emails/             confirmation.php
│   ├── layouts/            admin.php, auth.php, public.php
│   └── public/             home, event, register, success, checkin, 404
├── install.php             One-time CLI installer
├── nginx.conf.example      Nginx server block
└── .env.example            Environment variable reference
```

---

## Installation

### Requirements

- PHP 8.0+ with extensions: `pdo`, `pdo_mysql`, `mbstring`, `json`, `gd` (for QR), `fileinfo`
- MySQL 8.0+ or MariaDB 10.6+
- Apache with `mod_rewrite` **or** Nginx
- Web server document root → `public/`

### Step 1: Clone / Upload Files

```bash
git clone https://github.com/yourrepo/ems.git /var/www/ems
cd /var/www/ems
```

### Step 2: Install PHP Dependencies

```bash
# PHPMailer
mkdir -p vendor/phpmailer/phpmailer/src
curl -o vendor/phpmailer/phpmailer/src/PHPMailer.php \
  https://raw.githubusercontent.com/PHPMailer/PHPMailer/master/src/PHPMailer.php
curl -o vendor/phpmailer/phpmailer/src/SMTP.php \
  https://raw.githubusercontent.com/PHPMailer/PHPMailer/master/src/SMTP.php
curl -o vendor/phpmailer/phpmailer/src/Exception.php \
  https://raw.githubusercontent.com/PHPMailer/PHPMailer/master/src/Exception.php

# phpqrcode
mkdir -p vendor/phpqrcode
for f in qrlib.php qrconst.php qrcodetools.php qrrscode.php qrmatrix.php qrencode.php \
          qrimage.php qrbitstream.php qrinput.php qrspec.php qrsplit.php qrmask.php; do
  curl -o "vendor/phpqrcode/$f" \
    "https://raw.githubusercontent.com/t0k4rt/phpqrcode/master/$f"
done
```

Alternatively, use **Composer** (recommended):

```bash
composer require phpmailer/phpmailer
# For QR, use endroid/qr-code and update QRCode.php accordingly
```

### Step 3: Configure the Database

Edit `config/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'ems_db');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');
```

Or set environment variables `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` and the config reads them automatically.

### Step 4: Run the Installer

```bash
php install.php
```

This will:
- Create the `ems_db` database
- Import the full schema
- Create upload and log directories
- Prompt for superadmin credentials
- Set the site URL

### Step 5: Web Server Setup

**Apache** — The included `.htaccess` handles rewriting. Enable `mod_rewrite`:

```bash
a2enmod rewrite
systemctl restart apache2
```

Set `AllowOverride All` on the document root in your VirtualHost.

**Nginx** — Copy `nginx.conf.example` to `/etc/nginx/sites-available/ems`, update `server_name` and `root`, then:

```bash
ln -s /etc/nginx/sites-available/ems /etc/nginx/sites-enabled/
nginx -t && systemctl reload nginx
```

### Step 6: File Permissions

```bash
chown -R www-data:www-data public/uploads storage
chmod -R 755 public/uploads storage
```

### Step 7: Delete the Installer

```bash
rm install.php
```

---

## First Login

Visit `http://yourdomain.com/admin/login`

Use the credentials you set during installation.

**Default (if you skipped the installer):**
- Email: `admin@ems.local`
- Password: `password` ← **Change immediately**

---

## Admin Quick Start

1. **Settings** → Set church name, site URL, SMTP credentials
2. **New Event** → Fill in title, dates, venue, upload a banner → Publish
3. Share `http://yourdomain.com/events/your-event-slug` with attendees
4. **QR Check-In** → Use any device camera to scan attendee passes on the day
5. **Reports** → Monitor attendance in real time

---

## Security Notes

- All database queries use PDO prepared statements — no SQL injection risk
- CSRF tokens protect every state-changing form and AJAX request
- All output is escaped with `htmlspecialchars()` before rendering
- File uploads are validated by MIME type (not just extension) using `finfo`
- Sessions use `httponly`, `samesite=Lax`, and regenerate on login
- Passwords hashed with BCrypt cost 12
- Admin routes are protected by `AuthMiddleware::requireAuth()` and `requireRole()`
- Error details are hidden in production (`APP_ENV=production`)

---

## Database Schema (summary)

| Table | Purpose |
|---|---|
| `admins` | Admin users with roles |
| `events` | Event definitions with full metadata |
| `event_speakers` | Speaker records linked to events |
| `event_schedule` | Programme items per event |
| `attendees` | Master attendee records (reusable across events) |
| `registrations` | Links attendees to events; holds the registration code |
| `checkins` | Records check-in timestamp and method |
| `settings` | Key-value system configuration |
| `activity_logs` | Admin audit trail |
| `password_resets` | Token-based password reset (ready for implementation) |

---

## Future Roadmap (scaffolded in DB / architecture)

- [ ] Password reset flow
- [ ] Multiple organisations / multi-tenancy
- [ ] Online payment integration (Paystack, Flutterwave)
- [ ] Badge PDF generation (TCPDF/DomPDF)
- [ ] Certificate generation
- [ ] SMS notifications (Termii, Twilio)
- [ ] Excel export (PhpSpreadsheet)
- [ ] Volunteer management
- [ ] Public API with JWT
- [ ] Mobile check-in app

---

## License

MIT License — free for personal and commercial use.
