# MYTH — More Than Habits

A personal habit tracking app built with Laravel 13.

## Setup

```bash
composer setup
```

## Development

```bash
composer dev   # server + queue + logs + Vite, all concurrently
```

## Tests

```bash
composer test

# Single test
php artisan test --filter=TestNameOrMethod
```

## Stack

- **Backend:** Laravel 13, PHP 8.3+, SQLite
- **Frontend:** Blade, Alpine.js, Tailwind CSS v4, Vite
- **Queue / cache / sessions:** database driver (SQLite locally)
