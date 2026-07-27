# Shared Hosting & cPanel Deployment Guide (PHP 8.4)

This guide provides step-by-step instructions for deploying **https://obituaries.co.ke** on shared hosting environments (cPanel, DirectAdmin, Plesk) running **PHP 8.4**.

---

## 1. Prerequisites & Server Requirements

- **PHP Version**: 8.3 or **8.4**
- **PHP Extensions**: `bcmath`, `ctype`, `curl`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`, `zip`.
- **Database**: MySQL 8.0+ or MariaDB 10.5+

---

## 2. Shared Hosting File Structure Setup

On shared hosting (cPanel), place the project files either in `public_html` or in a folder above `public_html`.

### Option A: Recommended Root `.htaccess` Setup (Easiest)
Upload all project files directly into `public_html` (or your domain directory).
The root `.htaccess` included in the project will automatically route all incoming web requests into `public/index.php` seamlessly.

### Option B: Split Folder Setup (For enhanced security)
1. Upload all project files **outside** `public_html` (e.g. `/home/username/obituaries_core`).
2. Move the contents of `public/` into `public_html`.
3. Edit `public_html/index.php` to point to the core directory:
   ```php
   // Change from:
   require __DIR__.'/../vendor/autoload.php';
   $app = require_once __DIR__.'/../bootstrap/app.php';

   // To:
   require __DIR__.'/../obituaries_core/vendor/autoload.php';
   $app = require_once __DIR__.'/../obituaries_core/bootstrap/app.php';
   ```

---

## 3. Database & Production Environment Configuration

1. In cPanel, navigate to **MySQL Database Wizard**:
   - Create a database (e.g., `user_obituaries`).
   - Create a user (e.g., `user_obituser`) and assign a strong password.
   - Grant **ALL PRIVILEGES** to the user.

2. Create/edit the `.env` file on your server:
   ```ini
   APP_NAME="Obituaries.co.ke"
   APP_ENV=production
   APP_KEY=base64:YOUR_GENERATED_APP_KEY
   APP_DEBUG=false
   APP_URL=https://obituaries.co.ke

   LOG_CHANNEL=daily
   LOG_LEVEL=error

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=user_obituaries
   DB_USERNAME=user_obituser
   DB_PASSWORD=YOUR_STRONG_PASSWORD

   FILESYSTEM_DISK=public

   # M-Pesa Live Credentials
   MPESA_ENV=live
   MPESA_CONSUMER_KEY=your_live_consumer_key
   MPESA_CONSUMER_SECRET=your_live_consumer_secret
   MPESA_SHORTCODE=174379
   MPESA_PASSKEY=your_live_passkey
   MPESA_CALLBACK_URL="https://obituaries.co.ke/api/v1/mpesa/callback"
   MPESA_MOCK_MODE=false
   ```

---

## 4. Database Migrations & Initial Admin Seeding

Run the following commands via SSH (or via cPanel Terminal / Cron Job):

```bash
# 1. Run migrations in production mode
php artisan migrate --force

# 2. Seed initial admin user
php artisan db:seed --class=AdminSeeder --force
```

> **Default Admin Login Credentials:**
> - Email: `admin@obituaries.co.ke`
> - Password: `password123` *(Change immediately after first login)*

---

## 5. File Permissions & Storage Fallback

1. Set write permissions for `storage` and `bootstrap/cache`:
   ```bash
   chmod -R 775 storage bootstrap/cache
   ```

2. Create storage symlink:
   ```bash
   php artisan storage:link
   ```
   *Note: If your host disables `symlink()`, the included fallback route in `routes/web.php` will automatically serve uploaded photos and PDFs from `/storage/{path}` safely.*

---

## 6. Optimization Commands for Fast Loading

Run these Laravel caching commands after deployment to maximize speed:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

To clear cache after making updates:
```bash
php artisan optimize:clear
```
