# Homepage Dynamic Page Section

## What changed

Implemented dynamic content for the homepage "About Us / card-section / gradient CTA" block using the existing `pages` table and `App\Models\Website\Page` model.

## Files edited

### `app/Models/Website/Page.php`

- Added `HOME_ABOUT_CARD_CTA_SLUG` with the identifier:
  - `home-about-card-cta`
- Added `scopeForAllLocations()` to query global pages where `location_id` is `null`.
- Added `firstOrCreateGlobalSectionContent(...)`.
- The helper:
  - looks for the page by dealer and slug with `location_id = null`;
  - falls back to any existing page with the same dealer and slug;
  - auto-creates the page using `Page::create([...])` if it does not exist;
  - stores the default homepage HTML as the page `content`;
  - sets `location_id` to `null` so the page is visible for all locations;
  - returns `null` if the page is inactive, scheduled for the future, empty, or if a database error occurs.

### `app/Http/Controllers/Frontend/FrontendController.php`

- Imported `App\Models\Website\Page`.
- Rendered the default homepage section partial once.
- Called `Page::firstOrCreateGlobalSectionContent(...)` from the homepage action.
- Passed these variables to `frontend.pages.home`:
  - `$homeAboutCardCtaContent`
  - `$homeAboutCardCtaFallback`

### `resources/views/frontend/pages/home.blade.php`

- Replaced the hardcoded "About Us / card-section / gradient CTA" block with dynamic rendering.
- The homepage now renders stored Page content when present.
- If the Page content is missing, inactive, empty, or cannot be loaded, it renders the fallback HTML.

### `resources/views/frontend/partials/home-about-card-cta.blade.php`

- Added a new partial containing the original hardcoded homepage HTML block.
- Preserved the original markup and CSS classes.
- This partial is used both as:
  - the fallback rendered on the homepage;
  - the default content stored when the Page record is auto-created.

## Runtime behavior

On homepage load:

1. The controller renders the default section HTML from `frontend.partials.home-about-card-cta`.
2. The controller tries to load a global Page with slug `home-about-card-cta`.
3. If the Page does not exist, it creates one using `Page::create([...])`.
4. The created Page has `location_id = null`, making it available across all locations.
5. The homepage renders the Page content if available.
6. If Page content is unavailable, the homepage renders the default fallback HTML.

## Verification performed

- `php -l app\Models\Website\Page.php` passed.
- `php -l app\Http\Controllers\Frontend\FrontendController.php` passed.

`php artisan view:clear` could not be run because the local CLI PHP binary is `8.2.12`, while Composer requires PHP `>= 8.4.0`.
