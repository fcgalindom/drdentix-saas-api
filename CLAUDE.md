# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# Initial setup (installs deps, configures .env, runs migrations, installs npm)
composer setup

# Run development servers (PHP + queue + logs + Vite concurrently)
composer dev

# Run full test suite
composer test

# Run a single test file
php artisan test tests/Feature/ExampleTest.php

# Run tests matching a name
php artisan test --filter=ExampleTest

# Format PHP code (Laravel Pint)
./vendor/bin/pint

# Build frontend assets
npm run build

# Artisan shortcuts
php artisan migrate
php artisan migrate:fresh --seed
php artisan tinker
```

## Stack

- **PHP 8.3**, **Laravel 13.8**
- **Vite 8** + **Tailwind CSS 4** for frontend assets
- **SQLite** in development/testing (in-memory for tests), MySQL-compatible for production
- **Database-backed** sessions, cache, and queue (no Redis required by default)

## Architecture

This is an early-stage Laravel API skeleton for a dental SaaS product. There is minimal business logic yet — the foundation is ready but routes, controllers, and models beyond `User` still need to be built.

**Request lifecycle:**
- `bootstrap/app.php` — application bootstrap, middleware registration, exception handling
- `routes/web.php` — currently only the welcome view (`GET /`); API routes (`routes/api.php`) have not been created yet
- `app/Http/Controllers/Controller.php` — empty abstract base controller

**Database:**
- Migrations cover users, sessions, cache, jobs/batches/failed_jobs tables
- `database/seeders/DatabaseSeeder.php` seeds one test user (`test@example.com`)

**Testing:**
- PHPUnit 12, configured in `phpunit.xml`
- Tests use SQLite in-memory DB, array cache/queue/mail — no external services needed
- `tests/Unit/` and `tests/Feature/` directories
