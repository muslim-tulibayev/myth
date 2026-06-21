# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

**Initial setup:**
```bash
composer setup
```

**Run full dev environment** (server + queue worker + log tailing + Vite, all concurrently):
```bash
composer dev
```

**Run tests:**
```bash
composer test
```

**Run a single test:**
```bash
php artisan test --filter=TestNameOrMethod
```

**Code formatting (Laravel Pint):**
```bash
./vendor/bin/pint
```

## Architecture

This is a **Laravel 13** application (PHP 8.3+).

**Database:** SQLite (`database/database.sqlite`) in local/development. Tests use in-memory SQLite — no persistent database needed for tests.

**Queue, cache, and sessions** all use the `database` driver by default (backed by SQLite locally).

**Frontend:** Vite with the `laravel-vite-plugin`, Tailwind CSS v4 (`@tailwindcss/vite`), and Bunny Fonts (Instrument Sans). Assets live in `resources/css/app.css` and `resources/js/app.js`.

**Standard Laravel structure:**
- `app/` — application code (Models, Controllers, Providers, etc.)
- `routes/web.php` — web routes (only a welcome route exists so far)
- `resources/views/` — Blade templates
- `database/migrations/` — schema migrations
- `tests/Unit/` and `tests/Feature/` — PHPUnit test suites
