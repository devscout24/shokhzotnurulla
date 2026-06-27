# AGENTS.md — Angel Motors Inc

## Project

Multi-tenant dealership management platform. Laravel 12 (`^8.2` / Node 26 in `.nvmrc`), three panels:
- **Admin** (`/admin`) — platform-level super admins
- **Dealer** (`/dealer`) — per-dealer managers  
- **Frontend** — domain-based public site (tenant resolves via domain → dealer)

## Commands

| Command | What it does |
|---|---|
| `composer setup` | Full setup: `composer install`, copy `.env`, `key:generate`, `migrate --force`, `npm install`, `npm run build` |
| `composer dev` | Runs `artisan serve`, `queue:listen --tries=1`, and `npm run dev` in parallel via `npx concurrently` |
| `composer test` | `artisan config:clear --ansi` then `artisan test` |
| `php artisan roles:cleanup-expired` | Scheduled daily — run manually to test role expiry |
| `php artisan test --filter=TestName` | Run a single test |

## Architecture

- **Tenant resolution**: `TenantServiceProvider` resolves dealer by domain at boot → stored as `app('currentDealer')`. Falls back to `Dealer::domain` / `staging_domain`. Frontend also uses `DealerResolverService` (cache-backed, 1h TTL). `app.config.fallback_allowed` env toggle.
- **Location context**: `LocationContext` service stores per-dealer active location in session. `0` = "All Locations".
- **Spatie Permission** with teams (`team_foreign_key = dealer_id`). Custom `App\Models\Permission`/`Role` extend Spatie's. `super_admin` gates all; `dealer_owner` gates `dealer.*`.
- **Middleware order** (appended to `web`): `TeamsPermission`, `SetLocationContext`, then `SubstituteBindings` (priority). Group `all.active`: `EnsureUserIsActive` + `EnsureDealerIsActive` + `EnsureRoleIsActive`.
- **System dealer**: slug `system` (see `config/systemuser.php`). System users use this dealer context.

## Testing

- Pest PHP 4.x, Feature tests `tests/Feature/`, Unit `tests/Unit/`.
- **DB**: SQLite `:memory:` (phpunit.xml). `RefreshDatabase` is **commented out** in `tests/Pest.php` — tests manage state explicitly or uncomment if needed.
- **Before role/permission assignment in tests** — call `setPermissionsTeamId($dealerId)`. Spatie teams require this.
- Tests use `DatabaseTransactions` per-class (see `AdminRestrictedSitesTest.php` for patterns).

## Key config files (non-standard Laravel)

- `config/vehicle_types.php` — Maps URL slugs to `body_types.name` values (case-sensitive). Powers frontend type-based routes.
- `config/integrations.php` — Provider catalog for dealer third-party integrations (GA4, GTM, Carfax, Stripe, etc.).
- `config/permission.php` — Spatie teams setup (`team_foreign_key = dealer_id`), custom model classes.
- `config/systemuser.php` — System dealer slug (`'system'`).
- `config/seo.php` — Default SEO meta values and tracking cache TTL.

## Deployment

- **`vps` branch**: Rsync via GitHub Actions (PHP 8.4, Node 20 on CI), then `artisan optimize:clear`, `chmod -R 775 storage bootstrap/cache`. See `.github/workflows/vps.yml`.
- **`main` branch**: FTP deploy (legacy, `.github/workflows/main.yml`).
- Excluded from deploy per `.deployignore`: `.git/`, `.github/`, `.env`, `node_modules/`, `vendor/`, `storage/*.key`, `public/uploads/`.

## Style

- EditorConfig: 4-space indent, LF, UTF-8, final newline, trim trailing whitespace (except `.md`).
- Laravel Pint for PHP CS.
- Vite SCSS via `modern-compiler` API (suppressed deprecations: `legacy-js-api`, `import`, `global-builtin`, `color-functions`, `if-function`).
