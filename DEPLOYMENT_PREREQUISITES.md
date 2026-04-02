# Deployment Prerequisites

## Server Requirements

- PHP 8.2 or newer with `bcmath`, `ctype`, `curl`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo`, `pdo_mysql`, `tokenizer`, `xml`, and `zip`
- MySQL 8+ or MariaDB 10.5+
- Composer 2+
- Node.js 20+ and npm for frontend builds
- Web server: Nginx or Apache pointed to the `public/` directory
- Process manager for queues and scheduled tasks if reminders or async notifications are enabled

## Laravel Setup

1. Copy `.env.example` to `.env` and configure:
   - `APP_NAME`
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - `APP_URL`
   - database credentials
   - mail credentials
   - SMS gateway credentials
2. Install backend dependencies with `composer install --no-dev --optimize-autoloader`
3. Install frontend dependencies with `npm ci`
4. Generate app key with `php artisan key:generate`
5. Run migrations and seeders with `php artisan migrate --seed`
6. Create storage symlink with `php artisan storage:link`
7. Build frontend assets with `npm run build`
8. Cache production config with:
   - `php artisan config:cache`
   - `php artisan route:cache`
   - `php artisan view:cache`

## Runtime Services

- Queue worker if notifications or jobs are used:
  - `php artisan queue:work --tries=3`
- Scheduler cron entry:
  - `* * * * * php /path-to-project/artisan schedule:run >> /dev/null 2>&1`

## File Permissions

- Web server user must have write access to:
  - `storage/`
  - `bootstrap/cache/`

## DNS / SSL / Web Server

- Point the domain to the server
- Install SSL and force HTTPS
- Configure the web root to the Laravel `public/` directory
- Add redirect rules from HTTP to HTTPS

## Admin Checklist Before Go-Live

- Update website settings in `admin-panel/settings`
- Upload production logo
- Set GST rate
- Verify active pricing rules
- Verify live slots and tables
- Publish blogs, happiness cards, and deals
- Test booking, OTP, email, SMS, and catering enquiry submissions
