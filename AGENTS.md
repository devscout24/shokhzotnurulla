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
- **Vehicle card photo overlay**: `resources/views/frontend/partials/vehicle-card.blade.php` — `#has-more-photos` overlay has "View all N photos" and "Apply online" links. Both are `<a>` tags pointing to `route('frontend.inventory.show', $vehicle->slug)`. Do **not** use `onclick="event.stopPropagation()"` on the overlay — it blocks child clicks.

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
- **Premium options auto-extraction**: `ExtractPremiumOptionsAction` reads `VehicleVinData.vehicle_databases` (persisted raw API response) after vehicle create/update, extracts `feature` sections (`mechanical_and_powertrain`, `safety`, `interior`, `exterior`), and creates `VehiclePremiumOption` records. Skips if vehicle already has premium options (preserves manual curation). `factory_code` is a slug of the feature text (or md5 fallback). Called from `CreateVehicleActionV2` and `UpdateDetailsActionV2` after their DB transactions. `name` column is `text` (not `string(150)`) — API feature names can exceed 255 chars.
- **Requests V2**: `StoreVehicleRequestV2`, `UpdateDetailsRequestV2` — validate both V1 and V2 field naming conventions (e.g. both `cylinders` and `engine_cylinders`).

## VIN Inspector

- **Route**: `GET|POST /dealer/inventory/vdp/{vehicle}/vin-inspector` named `dealer.inventory.vdp.vin-inspector` / `.vin-inspector.save`. Controller: `InventoryController::vinInspector()` / `vinInspectorSave()`.
- **View**: `resources/views/dealer/pages/inventory/vin-inspector.blade.php` — left panel has editable vehicle/price/spec fields, right panel has tabbed JSON viewer with syntax highlighting.
- **JSON rendering**: Server-rendered in `<pre>` tags via Blade `json_encode()` (no JS dependency for basic display). JS layers syntax highlighting, tab switching, search, click-to-fill on top.
- **`rawCache`**: On first call, `getRawJson(col)` reads `pre.textContent` and caches it. Subsequent re-renders (search typing, tab switch) use the cached original to avoid line-number accumulation from reading highlighted DOM textContent.
- **Regex note**: JSON string matching uses `"(?:[^"\\]|\\.)*"` instead of `"[^"]*"` to handle escaped quotes like `\"`. `syntaxLine` null-guards `JSON.parse` so a bad match doesn't crash the whole render.
- **Click-to-fill**: Clicking a JSON value runs `fillBestMatch(val)` which fuzzy-matches field names (e.g. `engine_hp` → `max_horsepower`). Matches with score ≥ 0.5 fill the input and scroll to it.
- **Loader overlay**: `#viLoader` shows a spinning loader during expensive operations (tab switch, search, initial highlight). Uses `requestAnimationFrame` + `setTimeout(fn, 0)` to let the browser paint the spinner before blocking syntax-highlighting work. `showLoader(label)` / `hideLoader()` helpers toggle the overlay and accept custom labels ("Switching tab…", "Searching…", "Loading…").

## Admin Restricted Credits

- **Route**: `GET admin/restricted-credits` → `AdminEnvController@index`. `PATCH admin/restricted-credits` → `AdminEnvController@update`.
- **View**: `resources/views/admin/pages/restriced-credits.blade.php` (note: filename has typo `restriced` matching the existing convention).
- **Purpose**: Platform-level env key management. Currently manages `VEHICLE_DATABASES_API_KEY` for the VIN Decode V2 service.
- **Env write logic**: `AdminEnvController::updateEnvValue()` reads `.env`, regex-replaces the existing `KEY=value` line, or appends if missing. Also sets `$_ENV`, `putenv()`, and `config()` at runtime so the change takes effect without a server restart.
- **View features**: Password input with eye-toggle visibility button, status badge (Configured / Not Configured), scoped page styles (all CSS inline via `@push('page-styles')`).

## Frontend Inventory Listing

- **Route**: `GET /inventory` → `FrontendController::inventory()`. Filter AJAX: `GET /inventory/filter` → `inventoryFilter()`. Type variants: `/{type}` / `/{type}/filter`.
- **View**: `resources/views/frontend/pages/inventory-listing.blade.php` — extends `layouts.frontend.app`. Includes `frontend.partials.filter-sidebar` (left) and `frontend.partials.inventory-grid` (right).
- **Sort dropdown**: Custom toggle (not Bootstrap JS) — `.show` class on `.dropdown` parent, not the button. `initSortDropdown()` in `resources/js/frontend/pages/inventory-listing.js:347+` handles open/close, checkmark, and label update. On item click → `window.location.href` with `?sort=` (full page reload, not AJAX). Sort values: `best_match`, `price_asc`, `price_desc`, `newest`, `mileage_asc`, `mileage_desc`, `year_desc`, `year_asc`, `make_asc`.
- **Filter sidebar**: `resources/views/frontend/partials/filter-sidebar.blade.php` — card with collapsible `.filter-dropdown` sections. Each section has a `.filter-search` input that filters `.make-item` elements within its `.dropdown-content` by label text match (`bindFilterSearch()` in `inventory-listing.js`).
- **Filter chips**: Server-rendered `.filter-chip` elements in `#filter-badges`. Click removal handled by `bindBadgeRemoval()` targeting `.badge-default, .filter-chip`. Unchecks the corresponding checkbox and re-fetches via AJAX.
- **Clear All Filters**: `btn-outline-danger` button in card header, links to `request()->url()` (base URL without query params). Only shown when active filters exist.
- **Price slider**: Dual native `<input type="range">` stacked in `.dual-range-wrapper` (same styling as `.gep-range` in get-estimate). Two inputs (`#price-range-min`, `#price-range-max`) with a `.dual-range-track` behind them. `input` events → live track fill + histogram + display inputs. `change` events → AJAX fetch. `clampPair()` prevents thumbs from crossing. `fillTrack()` helper sets the gradient background on any track.
- **Histogram**: 10 bars (`#histogram-bars .histogram-bar[data-bar-idx]`) that toggle `.in-range` class based on which 10% price slices overlap the slider range. Updated in `updateTrackFill()` alongside the track gradient.
- **Price / Payment toggle**: Radio buttons (`#shop_price`, `#shop_payment`) toggle between `#tab-price` and `#tab-payment` panels via `initPricePaymentToggle()`. Price tab has the price range slider. Payment tab has its own dual range slider (`#payment-range-min`, `#payment-range-max`) showing monthly payment amounts.
- **Payment tab**: Calculates min/max monthly from price range using the sidebar APR rate and standard amortization formula (60mo). Slider values are reverse-converted to price via `paymentToPrice()` and written into the same `#minprice`/`#maxprice` hidden inputs that drive AJAX filtering. Display inputs show `$X/mo` format.
- **GVWR (Weight) filter**: Hardcoded range 800–2000 lbs. Dual range slider (`#gvwr-range-min`, `#gvwr-range-max`) with hidden inputs `gvwr[gt]`/`gvwr[lt]`. **Hidden inputs start empty** — only populated when user narrows the range from full. When at full range (800–2000), hidden inputs are cleared to `""` so `collectParams()` skips them. This prevents `whereHas('specs', ...)` from excluding vehicles without GVWR data.
- **GVWR server-side filter**: `InventoryListingService::buildQuery()` — `gvwr.gt`/`gvwr.lt` → `whereHas('specs', fn => where('gvwr', ...))`. Only runs when params are present.
- **Interest rate display**: Sidebar price section shows dynamic APR from `$interestRates` using same matching logic as `get-estimate.blade.php` (60mo term, 740 credit score). "Adjust Terms" link opens `#getEstimate` offcanvas with `data-vehicle-*` attributes (price = `activeMax`, rate = matched rate, term = 60). Fallback: `6.79%`. `initSidebarGetEstimateLink()` updates `data-vehicle-price` based on active tab (price vs payment).
- **AJAX filtering**: `fetchResults()` in `inventory-listing.js` sends GET to `/inventory/filter`, updates grid + pagination + heading, pushes browser history. Aborts previous in-flight request via `AbortController`. Filter form submit intercepted → always AJAX.
- **`InventoryListingService`** (`app/Services/Inventory/InventoryListingService.php`): Invokable service. `buildQuery()` handles all filter params, `buildFilterData()` caches filter sidebar data per dealer + location + version. Sort handled via `match` on `$request->input('sort', 'best_match')`. Filter data cache includes: `makes`, `models`, `bodyStyles`, `colors`, `interiorColors`, `transmissions`, `drivetrains`, `fuelTypes`, `engines`, `seating`, `features`, `priceRange`, `yearRange`.

## Vehicle Detail Page (VDP)

- **Route**: `GET /inventory/{slug}` → `FrontendController::vehicleDetail()`.
- **View**: `resources/views/frontend/pages/vehicle-detail.blade.php`.
- **Key Features section**: Starred factory options (`$vehicle->factoryOptions` where `pivot->is_starred === true`) — max 12, rendered as pill-style cards in a 3-column grid with an SVG icon per option. Icons live in `public/assets/frontend/img/features/{option_key}.svg`; missing icons hide via `onerror`. The `features` relationship is unused on the VDP view. The `factoryOptions` eager load in `VehicleDetailService::loadVehicle()` includes `option_key` and `withPivot('is_starred')`.
- **Premium Options section**: `$vehicle->premiumOptions` grouped by `category` → rendered as collapsible accordion rows per category. Each row has a table with Name and Description columns. Uses same `.collapse-header` / `.collapse-content` pattern as factory options.
- **Factory Options section**: `$groupedOptions` (from `VehicleDetailService::groupedFactoryOptions()`) rendered as collapsible accordion rows per category, showing `label` in a 2-column grid. Excludes starred options (they appear in Key Features instead).
- **FAQs section**: Dynamically generated by `VehicleDetailService::buildFaqs()` (`app/Services/Inventory/VehicleDetailService.php:136`). Not from a database — builds Q&A pairs from vehicle specs: fuel type, fuel capacity, horsepower, torque, city MPG. Each FAQ only included if the corresponding spec value exists.
- **Collapsible accordions**: JS in `vehicle-detail.js:108-120` — `.collapse-header` click toggles `.open` on next `.collapse-content` sibling (walks from `.card-footer` parent).

## Style

- EditorConfig: 4-space indent, LF, UTF-8, final newline, trim trailing whitespace (except `.md`).
- Laravel Pint for PHP CS.
- Vite SCSS via `modern-compiler` API (suppressed deprecations: `legacy-js-api`, `import`, `global-builtin`, `color-functions`, `if-function`).

## Detailing Page & Schedule Service Form

- **Detailing Route**: `GET /detailing` named `frontend.detailing` -> `FrontendController@detailing`.
- **Detailing View**: `resources/views/frontend/pages/detailing.blade.php`.
- **Detailing Submit Route**: `POST /forms/detailing` named `frontend.forms.detailing` -> `FormEntryController@detailing`.
- **Schedule Service Route**: `POST /forms/schedule-service` named `frontend.forms.schedule-service` -> `FormEntryController@scheduleService`.
- **Schedule Service Request**: `StoreScheduleServiceRequest` extends `StoreSimpleFormRequest` to support additional vehicle specifications (`year`, `make`, `model`, `mileage`, `vin`, `warranty`, `services`, `preferreddate`, `vehicle`).
- **Form type & ENUM**: Database migrations `add_schedule_service_to_form_entries_form_type` and `add_detailing_to_form_entries_form_type` alter the `form_type` column on the `form_entries` table to add the `schedule_service` and `detailing` enum values.
- **Form Submission Logic**: Both forms have been converted to AJAX forms using `FormData`. They post to their respective endpoints, display a custom success confirmation section on successful response, and render error lists on validation failures.

## Dealer Media Library

- **Route**: `GET /dealer/website/media` → `WebsiteMediaController@index`. AJAX list: `GET /dealer/website/media/list` → `list()`.
- **View**: `resources/views/dealer/pages/website/media.blade.php` — toolbar with tabs (All/Images/Video/File), search, upload drop zone, grid with display modes (compact/comfortable/expanded), pagination.
- **Location scope**: Media list is **not** filtered by `forActiveLocation()` — media is a shared dealer resource. Uploading while "All Locations" is active stores `location_id = NULL`, and those records must remain visible from any location context.


