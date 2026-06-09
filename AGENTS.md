# AGENTS.md — Angel Motors Inc.

## Project

Multi-tenant dealership management platform. Laravel 12, three panels: **Admin** (`/admin`), **Dealer** (`/dealer`), **Frontend** (domain-based public site).

## Commands

| Command | What it does |
|---|---|
| `composer setup` | Full project setup (composer install, .env, key:generate, migrate, npm install, vite build) |
| `composer dev` | Starts `artisan serve`, `queue:listen`, and `npm run dev` concurrently |
| `composer test` | Runs `artisan config:clear` then `artisan test` |
| `npm run build` | Vite production build |
| `npm run dev` | Vite dev server |
| `php artisan roles:cleanup-expired` | Scheduled daily — run manually if testing role expiry |

## Architecture

- **Tenant resolution**: `TenantServiceProvider` resolves dealer by domain at boot → stored as `app('currentDealer')`. Frontend also uses `DealerResolverService` (cache-backed, 1h TTL).
- **Location context**: Per-dealer locations stored in session via `LocationContext`. `0` = "All Locations".
- **Spatie Permission** with teams enabled (`team_foreign_key = dealer_id`). Custom `Permission`/`Role` models extend Spatie's. `super_admin` implicitly gates all abilities; `dealer_owner` gates `dealer.*` abilities.
- **Middleware order** (appended to `web`): `TeamsPermission`, `SetLocationContext`, then `SubstituteBindings`. Group `all.active` = `EnsureUserIsActive` + `EnsureDealerIsActive` + `EnsureRoleIsActive`.
- **System dealer slug**: `system` (see `config/systemuser.php`). System users use this dealer context.

## Testing

- Pest PHP 4.x, Feature tests under `tests/Feature/`, Unit under `tests/Unit/`.
- **DB**: SQLite `:memory:` in tests (phpunit.xml). `RefreshDatabase` is **commented out** in `tests/Pest.php` — tests must manage DB state explicitly or uncomment if needed.
- Write Feature tests using `$this->get/post/...` (Pest with `TestCase` extension).
- Run single test: `php artisan test --filter=TestName`

## Key config files (not standard Laravel)

- `config/vehicle_types.php` — Maps URL slugs to DB `body_types.name` values (case-sensitive). Powers frontend type-based routes.
- `config/integrations.php` — Provider catalog for dealer third-party integrations (GA4, GTM, Carfax, Stripe, etc.).
- `config/systemuser.php` — System dealer slug (`'system'`).
- `config/seo.php` — Default SEO meta values.

## Deployment

- **`vps` branch**: Rsync to production, then `artisan optimize:clear`, `chmod -R 775 storage bootstrap/cache`. See `.github/workflows/vps.yml`.
- **`main` branch**: FTP deploy (legacy, `.github/workflows/main.yml`).
- Excluded from deploy: `.git/`, `node_modules/`, `vendor/`, `.env`, `storage/*.key`, `public/uploads/` (see `.deployignore`).

## Style

- EditorConfig: 4-space indent, LF, UTF-8, final newline, trim trailing whitespace (except `.md`).
- Laravel Pint for PHP CS.
- Vite builds SCSS with `modern-compiler` API (suppressed deprecations: `legacy-js-api`, `import`, `global-builtin`, `color-functions`, `if-function`).
