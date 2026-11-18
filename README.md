# AR Dental Care — Blade + Livewire

Session-based dental clinic app for a single doctor: appointments, availability, clinic profile, and pad-style prescriptions. This app is a Laravel Blade and Livewire twin of the React + API product. It uses its own SQLite database.

## Requirements

- PHP 8.3+
- Composer
- Node.js 20+
- SQLite

## Setup

```bash
cd /Users/mizan/Documents/drapplaravel
cp .env.example .env
composer install
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan storage:link
npm ci
npm run build
php artisan serve --host=127.0.0.1 --port=8001
```

Open [http://127.0.0.1:8001/login](http://127.0.0.1:8001/login).

`APP_URL` should stay `http://127.0.0.1:8001` so appointment links, prescription share links, and email verification links point at this app. Timezone is `Asia/Dhaka`. SMS uses the `log` driver (`SMS_DRIVER=log`). Mail uses the `log` mailer unless you change `MAIL_MAILER`.

## Demo login

| | |
|---|---|
| Email | `doctor@ardental.local` |
| Password | `password` |
| Public booking | [http://127.0.0.1:8001/appointment/ar-dental-care](http://127.0.0.1:8001/appointment/ar-dental-care) |

Register with an email or a phone number (+880 prefix; enter 1XXXXXXXXX). Email verification links look like `/verify?channel=email&token=...`. With the log SMS driver, the OTP is written to the Laravel log and shown on the verify screen in local development.

## Doctor screens

- `/` dashboard, weekly chart, and appointment QR
- `/appointments` list and status updates
- `/appointment-config` weekly hours and closed days
- `/profile` clinic profile, avatar, and logo
- `/prescriptions` list, pad editor, medicine search, print, email, and WhatsApp share
- `/rx/{token}` public prescription
- `/appointment/{slug}` and `/book/{slug}` public booking

Every doctor-owned record is checked with `user_id` before it can be changed.

## Medicines

A starter catalog is seeded. When `OUSHODH_KEY` is set, refresh it with:

```bash
php artisan medicines:sync
```

## Tests

```bash
php artisan test
```
