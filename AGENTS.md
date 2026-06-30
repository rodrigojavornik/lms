# Repository Guidelines

## Project Structure & Module Organization

This is a Laravel 10 LMS application. Core PHP code lives in `app/`, with models in `app/Models`, controllers in `app/Http/Controllers`, middleware in `app/Http/Middleware`, and form requests in `app/Http/Requests`. Routes are split across `routes/web.php`, `routes/api.php`, `routes/auth.php`, `routes/console.php`, and `routes/channels.php`.

Blade views are in `resources/views`, grouped by role or feature (`admin`, `manager`, `instructor`, `student`, `auth`, `components`, `layouts`). Frontend entry points are `resources/js/app.js` and `resources/css/app.css`, built by Vite and Tailwind. Database files are under `database/`; tests are under `tests/Feature` and `tests/Unit`. Deployment samples are in `deploy/`.

## Build, Test, and Development Commands

- `composer install` installs PHP dependencies.
- `npm install` installs frontend tooling.
- `Copy-Item .env.example .env; php artisan key:generate` creates local configuration in PowerShell.
- `php artisan migrate --seed` applies schema changes and seeds development data.
- `php artisan serve` starts the Laravel development server.
- `npm run dev` starts Vite for local assets.
- `npm run build` builds production frontend assets.
- `php artisan test` runs the PHPUnit test suite.
- `./vendor/bin/pint` formats PHP using Laravel Pint.

## Coding Style & Naming Conventions

Follow `.editorconfig`: UTF-8, LF endings, four-space indentation, final newline, and trimmed trailing whitespace except in Markdown. YAML uses two spaces.

Use Laravel and PSR-4 conventions. Classes are PascalCase (`StudentController`, `QuizAttempt`), methods and variables are camelCase, database tables and migrations are snake_case, and multiword Blade files use kebab-case such as `course-player.blade.php`. Keep shared UI in `resources/views/components`.

## Testing Guidelines

PHPUnit is configured in `phpunit.xml`. Feature tests belong in `tests/Feature`; isolated logic tests belong in `tests/Unit`. Name tests with the `*Test.php` suffix and use descriptive methods that state expected behavior. The test environment uses array cache/session drivers, sync queues, and array mail. No coverage threshold is enforced, but coverage includes `app/`.

## Commit & Pull Request Guidelines

Git history currently only shows `Initial commit`, so no strict convention is established. Use short, imperative subjects, for example `Add course sharing validation`. Keep unrelated changes separate.

Pull requests should include a concise description, testing performed (`php artisan test`, `npm run build`, or targeted tests), linked issues when available, and screenshots for Blade/UI changes. Note migrations, seed data, or configuration requirements.

## Security & Configuration Tips

Do not commit secrets from `.env`; update `.env.example` when adding required configuration. Keep generated files in `storage/`, `bootstrap/cache/`, `vendor/`, and `node_modules/` out of source control. Review `deploy/` before changing production PHP or Nginx settings.
