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

## Vehicle Photos

- **Primary photo storage**: `storage/app/public/dealers/{dealer_id}/media/primary/{vehicle_slug}/{uuid}.{ext}`. Path built in `ApplyPhotoOverlay` job; old primary file destroyed in `SetPrimaryPhotoAction`.
- **URL domain**: Photo URLs are stored with the dealer's domain (`https://{dealer.domain}/storage/{path}`) instead of `APP_URL`. Set in `UploadPhotosAction` and `ApplyPhotoOverlay`. Existing APP_URL-based URLs can be fixed with `php artisan photos:fix-urls`.
- **Gallery modal flow**: Two separate Bootstrap modals — grid (`#modalGallery`) and carousel (`#modalCarousel`). Grid opens via `data-bs-toggle` on main photo. Clicking a grid image (identified by `[data-gallery-idx]`) opens the carousel at that index via JS. Photos JSON is stored in `#modalGallery`'s `data-photos` attribute, parsed at runtime by the carousel IIFE in `vehicle-detail.js:1234+`.
- **VDP thumbnail sync**: `vehicle-detail.js:1209+` IIFE handles `.vdp-thumb` click → swap `#vdpMainPhoto` src.
- **View URL usage**: All views use `$photo->url` (stored DB value) — no `Storage::url()` or `asset('storage/...')` in dealer inventory views.

## Known JS quirks

- `vehicle-detail.js` is a large monolithic file (1336+ lines) combining code for schedule-test, trade-in, get-approved, payment-calc, dual-range slider, star rating, and photo gallery. Many blocks target offcanvas elements that may not be present on the page — **all top-level DOM queries and `.addEventListener()` calls must be null-guarded** to prevent the module from crashing before reaching the carousel code.
- **Fixed (2026-07-02)**: Two crashes that silently swallowed the carousel IIFE: (1) `#increase`/`#decrease`/`#unitPrice` block called `.addEventListener()` without null-checking those elements; (2) `handleMin`/`handleMax`/`track` slider code ran at top level outside the `if (loanInput || handleMin)` guard. Both are now null-checked. The slider code was also renamed to `handleMin2`/`handleMax2`/`SMIN`/`SMAX` to avoid redeclaration conflicts.
- Schedule-test IDs in Blade use `std-` prefix (`std-select-date`, `std-step-1`, `std-wizard-step` etc.). The JS originally used bare names — external code may need similar prefix alignment.
- When adding new top-level JS code, wrap it in an IIFE with null-checks to avoid being blocked by pre-existing errors.

## VIN Decode (V2)

- **Service**: `app/Services/Inventory/VinDecodeServiceV2.php` — uses `vehicle-databases.com` API (`https://api.vehicledatabases.com/advanced-vin-decode/{vin}`) via `x-Authkey` header. Falls back to `VEHICLE_DATABASES_API_KEY` env / `config('services.vehicle_databases.api_key')`. `normalizeResponse()` returns `features_categorized` (4 sections) alongside merged `features`.
- **Log channel**: `vin-decode` — writes to `storage/logs/vin-decode.log` (daily, 7-day retention). Always logs requests/responses/errors at `debug` level.
- **DevLog helper** (`App\Helpers\DevLog`):
  - `DevLog::debug()` / `DevLog::info()` — only writes when `APP_DEBUG=true`, suppressed in production automatically
  - `DevLog::error()` / `DevLog::warning()` — always writes regardless of environment
  - `DevLog::channel('vin-decode', ...)` — writes to the vin-decode channel with the same debug guard
- **Actions V2**: `CreateVehicleActionV2`, `UpdateDetailsActionV2` — persist enriched spec/pricing/notes data from the decode payload. `UpdateDetailsActionV2` also handles both V1 (`cylinders`, `max_horsepower`) and V2 (`engine_cylinders`, `engine_hp`) naming conventions via `resolveSpecField()`.
- **Premium options auto-extraction**: `ExtractPremiumOptionsAction` reads `VehicleVinData.vehicle_databases` (persisted raw API response) after vehicle create/update, extracts `feature` sections (`mechanical_and_powertrain`, `safety`, `interior`, `exterior`), and creates `VehiclePremiumOption` records. Skips if vehicle already has premium options (preserves manual curation). `factory_code` is a slug of the feature text (or md5 fallback). Called from `CreateVehicleActionV2` and `UpdateDetailsActionV2` after their DB transactions.
- **Requests V2**: `StoreVehicleRequestV2`, `UpdateDetailsRequestV2` — validate both V1 and V2 field naming conventions (e.g. both `cylinders` and `engine_cylinders`).

## VIN Inspector

- **Route**: `GET|POST /dealer/inventory/vdp/{vehicle}/vin-inspector` named `dealer.inventory.vdp.vin-inspector` / `.vin-inspector.save`. Controller: `InventoryController::vinInspector()` / `vinInspectorSave()`.
- **View**: `resources/views/dealer/pages/inventory/vin-inspector.blade.php` — left panel has editable vehicle/price/spec fields, right panel has tabbed JSON viewer with syntax highlighting.
- **JSON rendering**: Server-rendered in `<pre>` tags via Blade `json_encode()` (no JS dependency for basic display). JS layers syntax highlighting, tab switching, search, click-to-fill on top.
- **`rawCache`**: On first call, `getRawJson(col)` reads `pre.textContent` and caches it. Subsequent re-renders (search typing, tab switch) use the cached original to avoid line-number accumulation from reading highlighted DOM textContent.
- **Regex note**: JSON string matching uses `"(?:[^"\\]|\\.)*"` instead of `"[^"]*"` to handle escaped quotes like `\"`. `syntaxLine` null-guards `JSON.parse` so a bad match doesn't crash the whole render.
- **Click-to-fill**: Clicking a JSON value runs `fillBestMatch(val)` which fuzzy-matches field names (e.g. `engine_hp` → `max_horsepower`). Matches with score ≥ 0.5 fill the input and scroll to it.

## Style

- EditorConfig: 4-space indent, LF, UTF-8, final newline, trim trailing whitespace (except `.md`).
- Laravel Pint for PHP CS.
- Vite SCSS via `modern-compiler` API (suppressed deprecations: `legacy-js-api`, `import`, `global-builtin`, `color-functions`, `if-function`).
