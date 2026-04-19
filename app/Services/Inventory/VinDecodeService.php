<?php

namespace App\Services\Inventory;

use App\Models\Catalog\BodyStyle;
use App\Models\Catalog\BodyType;
use App\Models\Catalog\DrivetrainType;
use App\Models\Catalog\FuelType;
use App\Models\Catalog\Make;
use App\Models\Catalog\MakeModel;
use App\Models\Catalog\TransmissionType;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class VinDecodeService
{
    private const BASE_URL          = 'https://vpic.nhtsa.dot.gov/api/vehicles';
    private const TIMEOUT           = 12;
    private const VIN_CACHE_TTL     = 86400;  // 24h — VIN specs never change
    private const CATALOG_CACHE_TTL = 21600;  // 6h  — catalog data rarely changes

    /**
     * NHTSA EngineConfiguration → our block_type (max 10 chars).
     * Order matters — more specific substrings first.
     */
    private const ENGINE_CONFIG_MAP = [
        'in-line' => 'I',
        'inline'  => 'I',
        'v-shape' => 'V',
        'w-shape' => 'W',
        'h-shape' => 'H',    // Horizontally Opposed / Boxer
        'opposed' => 'H',    // alternate label for boxer
        'rotary'  => 'Rotary',
        'single'  => 'Single',
    ];

    // --------------------------------------------------------------------------
    // Public API
    // --------------------------------------------------------------------------

    /**
     * Decode a VIN via NHTSA vPIC API and map results to our DB schema.
     *
     * @return array{
     *     success: bool,
     *     partial: bool,
     *     message: string|null,
     *     warnings: array<string>,
     *     data: array<string, mixed>
     * }
     */
    public function decode(string $vin, ?int $modelYear = null): array
    {
        $vin      = strtoupper(trim($vin));
        $cacheKey = 'vin_decode:' . $vin . ($modelYear ? ':' . $modelYear : '');

        return Cache::remember($cacheKey, self::VIN_CACHE_TTL, function () use ($vin, $modelYear) {
            return $this->fetchAndNormalize($vin, $modelYear);
        });
    }

    // --------------------------------------------------------------------------
    // NHTSA Fetch
    // --------------------------------------------------------------------------

    private function fetchAndNormalize(string $vin, ?int $modelYear): array
    {
        $url = self::BASE_URL . '/DecodeVinValues/' . $vin . '?format=json'
            . ($modelYear ? '&modelyear=' . $modelYear : '');

        try {
            $response = Http::timeout(self::TIMEOUT)->get($url);

            if (! $response->successful()) {
                return $this->errorResponse(
                    'NHTSA API is currently unavailable (HTTP ' . $response->status() . '). Please try again shortly.'
                );
            }

            $body    = $response->json();
            $results = $body['Results'][0] ?? null;

            if (! $results) {
                return $this->errorResponse('No data returned from NHTSA. Please verify the VIN and try again.');
            }

            // ErrorCode reference:
            // 0     → Clean decode
            // 1–5   → Partial decode (some fields missing — still usable)
            // 6+    → Invalid / undecodeable VIN → reject
            $errorCode = (int) ($results['ErrorCode'] ?? 999);

            if ($errorCode >= 6) {
                return $this->errorResponse(
                    $results['ErrorText'] ?? 'This VIN could not be decoded. Please check the VIN and try again.'
                );
            }

            return $this->normalizeResponse($results, $errorCode);

        } catch (ConnectionException) {
            return $this->errorResponse(
                'Could not connect to NHTSA API. Check your internet connection and try again.'
            );
        } catch (Exception) {
            return $this->errorResponse(
                'An unexpected error occurred while decoding the VIN. Please try again.'
            );
        }
    }

    // --------------------------------------------------------------------------
    // Response Normalization
    // --------------------------------------------------------------------------

    /**
     * Map raw NHTSA flat response to our structured format with DB ID resolution.
     */
    private function normalizeResponse(array $r, int $errorCode): array
    {
        // ── Catalog ID resolution ──────────────────────────────────────────────
        $makeId         = $this->resolveMakeId($r['Make'] ?? '');
        $makeModelId    = $this->resolveMakeModelId($r['Model'] ?? '', $makeId);
        $bodyTypeId     = $this->resolveBodyTypeId($r['BodyClass'] ?? '');
        $bodyStyleId    = $this->resolveBodyStyleId($r['BodyClass'] ?? '');
        $drivetrainId   = $this->resolveDrivetrainTypeId($r['DriveType'] ?? '');
        $fuelTypeId     = $this->resolveFuelTypeId($r['FuelTypePrimary'] ?? '');
        $transmissionId = $this->resolveTransmissionTypeId($r['TransmissionStyle'] ?? '');

        // ── Derived / constructed values ───────────────────────────────────────
        $blockType        = $this->parseBlockType($r['EngineConfiguration'] ?? '');
        $engineString     = $this->buildEngineString($r);
        $transmissionStd  = $this->parseTransmissionStandard($r['TransmissionStyle'] ?? '');
        $drivetrainStd    = $this->parseDrivetrainStandard($r['DriveType'] ?? '');
        $gvwrParsed       = $this->parseGvwr($r['GVWR'] ?? '');

        // NHTSA Trim + Series → our trim field
        $trim = trim(implode(' ', array_filter([
            $r['Trim']   ?? '',
            $r['Series'] ?? '',
        ])));

        $warnings = $this->buildWarnings($r, $makeId, $makeModelId, $bodyTypeId);

        return [
            'success'  => true,
            'partial'  => $errorCode > 0,
            'message'  => $errorCode > 0
                ? 'VIN partially decoded. Some fields may be incomplete — please verify.'
                : null,
            'warnings' => $warnings,
            'data'     => [
                // ── Core identity ──────────────────────────────────────────
                'year'                   => $r['ModelYear']           ?: null,
                'make'                   => $r['Make']                ?: null,
                'make_id'                => $makeId,
                'model'                  => $r['Model']               ?: null,
                'make_model_id'          => $makeModelId,
                'trim'                   => $trim                     ?: null,

                // ── Body ───────────────────────────────────────────────────
                'body_class'             => $r['BodyClass']           ?: null,
                'body_type_id'           => $bodyTypeId,
                'body_style_id'          => $bodyStyleId,
                'doors'                  => $r['Doors']               ?: null,

                // ── Drivetrain ─────────────────────────────────────────────
                'drive_type'             => $r['DriveType']           ?: null,
                'drivetrain_type_id'     => $drivetrainId,
                'drivetrain_standard'    => $drivetrainStd,

                // ── Fuel ───────────────────────────────────────────────────
                'fuel_type_primary'      => $r['FuelTypePrimary']     ?: null,
                'fuel_type_id'           => $fuelTypeId,

                // ── Transmission ───────────────────────────────────────────
                'transmission_style'     => $r['TransmissionStyle']   ?: null,
                'transmission_type_id'   => $transmissionId,
                'transmission_standard'  => $transmissionStd,

                // ── Engine string (→ vehicles.engine column) ───────────────
                'engine_string'          => $engineString,

                // ── Engine specs (→ vehicle_specs table) ───────────────────
                'engine_hp'              => $r['EngineHP']            ?: null,
                'engine_cylinders'       => $r['EngineCylinders']     ?: null,
                'engine_displacement_l'  => $r['DisplacementL']       ?: null,
                'engine_config'          => $r['EngineConfiguration'] ?: null,
                'block_type'             => $blockType,
                'gvwr'                   => $gvwrParsed,

                // ── Metadata (informational only — not saved to DB) ────────
                'manufacturer'           => $r['Manufacturer']        ?: null,
                'plant_city'             => $r['PlantCity']           ?: null,
                'plant_country'          => $r['PlantCountry']        ?: null,
                'vehicle_type'           => $r['VehicleType']         ?: null,
            ],
        ];
    }

    // --------------------------------------------------------------------------
    // DB ID Resolvers
    // --------------------------------------------------------------------------

    /**
     * Resolve Make.id — NHTSA returns uppercase e.g. "FORD", "HONDA".
     */
    private function resolveMakeId(string $nhtsaMake): ?int
    {
        if (empty($nhtsaMake)) {
            return null;
        }

        return Cache::remember(
            'vin_make_id:' . strtolower($nhtsaMake),
            self::CATALOG_CACHE_TTL,
            fn (): ?int => Make::whereRaw('LOWER(name) = ?', [strtolower($nhtsaMake)])->value('id')
        );
    }

    /**
     * Resolve MakeModel.id scoped to the resolved make_id.
     */
    private function resolveMakeModelId(string $nhtsaModel, ?int $makeId): ?int
    {
        if (empty($nhtsaModel) || ! $makeId) {
            return null;
        }

        return Cache::remember(
            'vin_model_id:' . $makeId . ':' . strtolower($nhtsaModel),
            self::CATALOG_CACHE_TTL,
            fn (): ?int => MakeModel::where('make_id', $makeId)
                ->whereRaw('LOWER(name) = ?', [strtolower($nhtsaModel)])
                ->value('id')
        );
    }

    /**
     * Resolve BodyType.id from NHTSA BodyClass.
     * BodyType = broader vehicle category (Car, Truck, SUV, Van...).
     *
     * 4-step matching strategy (same as resolveBodyStyleId):
     *  1. Exact lowercase match
     *  2. Acronym in parentheses — "(SUV)" from "Sport Utility Vehicle (SUV)"
     *  3. First keyword before "/" or space
     *  4. Any partial contains match
     */
    private function resolveBodyTypeId(string $nhtsaBodyClass): ?int
    {
        if (empty($nhtsaBodyClass)) {
            return null;
        }

        $typeMap    = $this->getBodyTypeMap();
        $normalized = strtolower(trim($nhtsaBodyClass));

        // 1. Exact
        if (isset($typeMap[$normalized])) {
            return $typeMap[$normalized];
        }

        // 2. Acronym in parentheses e.g. "(SUV)"
        if (preg_match('/\(([^)]+)\)/', $nhtsaBodyClass, $m)) {
            $acronym = strtolower(trim($m[1]));
            if (isset($typeMap[$acronym])) {
                return $typeMap[$acronym];
            }
            foreach ($typeMap as $dbName => $id) {
                if (str_contains($dbName, $acronym) || str_contains($acronym, $dbName)) {
                    return $id;
                }
            }
        }

        // 3. First keyword
        $firstKeyword = strtolower(strtok($normalized, '/ ') ?: $normalized);
        foreach ($typeMap as $dbName => $id) {
            if (str_contains($dbName, $firstKeyword) || str_contains($firstKeyword, $dbName)) {
                return $id;
            }
        }

        // 4. Any partial contains
        foreach ($typeMap as $dbName => $id) {
            if (str_contains($normalized, $dbName) || str_contains($dbName, $normalized)) {
                return $id;
            }
        }

        return null;
    }

    /**
     * Resolve BodyStyle.id from NHTSA BodyClass.
     * BodyStyle = more specific (Sedan, Coupe, Convertible, Hatchback...).
     * Same 4-step strategy as resolveBodyTypeId.
     */
    private function resolveBodyStyleId(string $nhtsaBodyClass): ?int
    {
        if (empty($nhtsaBodyClass)) {
            return null;
        }

        $styleMap   = $this->getBodyStyleMap();
        $normalized = strtolower(trim($nhtsaBodyClass));

        // 1. Exact
        if (isset($styleMap[$normalized])) {
            return $styleMap[$normalized];
        }

        // 2. Acronym in parentheses
        if (preg_match('/\(([^)]+)\)/', $nhtsaBodyClass, $m)) {
            $acronym = strtolower(trim($m[1]));
            if (isset($styleMap[$acronym])) {
                return $styleMap[$acronym];
            }
            foreach ($styleMap as $dbName => $id) {
                if (str_contains($dbName, $acronym) || str_contains($acronym, $dbName)) {
                    return $id;
                }
            }
        }

        // 3. First keyword
        $firstKeyword = strtolower(strtok($normalized, '/ ') ?: $normalized);
        foreach ($styleMap as $dbName => $id) {
            if (str_contains($dbName, $firstKeyword) || str_contains($firstKeyword, $dbName)) {
                return $id;
            }
        }

        // 4. Any partial contains
        foreach ($styleMap as $dbName => $id) {
            if (str_contains($normalized, $dbName) || str_contains($dbName, $normalized)) {
                return $id;
            }
        }

        return null;
    }

    /**
     * Resolve DrivetrainType.id.
     * NHTSA: "FWD/Front-Wheel Drive", "AWD/All-Wheel Drive", "4WD/4-Wheel Drive"
     * Extract acronym before "/" for primary match.
     */
    private function resolveDrivetrainTypeId(string $nhtsaDriveType): ?int
    {
        if (empty($nhtsaDriveType)) {
            return null;
        }

        $acronym = strtolower(explode('/', $nhtsaDriveType)[0]);
        $key     = 'vin_drivetrain:' . $acronym;

        return Cache::remember($key, self::CATALOG_CACHE_TTL, function () use ($acronym, $nhtsaDriveType): ?int {
            $id = DrivetrainType::whereRaw('LOWER(name) LIKE ?', ['%' . $acronym . '%'])->value('id');

            if ($id) {
                return $id;
            }

            $description = strtolower(trim(explode('/', $nhtsaDriveType)[1] ?? ''));

            return $description
                ? DrivetrainType::whereRaw('LOWER(name) LIKE ?', ['%' . $description . '%'])->value('id')
                : null;
        });
    }

    /**
     * Resolve FuelType.id.
     * NHTSA: "Gasoline", "Diesel", "Electric", "Flex Fuel (FFV)", "Natural Gas (CNG)"
     * Strip parenthetical suffix before matching.
     */
    private function resolveFuelTypeId(string $nhtsaFuel): ?int
    {
        if (empty($nhtsaFuel)) {
            return null;
        }

        $baseKeyword = strtolower(trim(explode('(', $nhtsaFuel)[0]));
        $key         = 'vin_fuel:' . $baseKeyword;

        return Cache::remember(
            $key,
            self::CATALOG_CACHE_TTL,
            fn (): ?int => FuelType::whereRaw('LOWER(name) LIKE ?', ['%' . $baseKeyword . '%'])->value('id')
        );
    }

    /**
     * Resolve TransmissionType.id.
     * NHTSA: "Automatic", "Manual", "CVT"
     * Tries standard column (exact) first, then name LIKE.
     */
    private function resolveTransmissionTypeId(string $nhtsaTransmission): ?int
    {
        if (empty($nhtsaTransmission)) {
            return null;
        }

        $normalized = strtolower(trim($nhtsaTransmission));
        $key        = 'vin_transmission:' . $normalized;

        return Cache::remember($key, self::CATALOG_CACHE_TTL, function () use ($normalized): ?int {
            return TransmissionType::whereRaw('LOWER(standard) = ?', [$normalized])->value('id')
                ?? TransmissionType::whereRaw('LOWER(name) LIKE ?', ['%' . $normalized . '%'])->value('id');
        });
    }

    // --------------------------------------------------------------------------
    // Catalog Map Caches
    // --------------------------------------------------------------------------

    /** @return array<string, int> lowercased name → id */
    private function getBodyTypeMap(): array
    {
        return Cache::remember('vin_body_type_map', self::CATALOG_CACHE_TTL, function (): array {
            return BodyType::all(['id', 'name'])
                ->mapWithKeys(fn ($bt) => [strtolower($bt->name) => $bt->id])
                ->all();
        });
    }

    /** @return array<string, int> lowercased name → id */
    private function getBodyStyleMap(): array
    {
        return Cache::remember('vin_body_style_map', self::CATALOG_CACHE_TTL, function (): array {
            return BodyStyle::all(['id', 'name'])
                ->mapWithKeys(fn ($bs) => [strtolower($bs->name) => $bs->id])
                ->all();
        });
    }

    // --------------------------------------------------------------------------
    // Derived Value Parsers
    // --------------------------------------------------------------------------

    /**
     * Construct human-readable engine string → vehicles.engine column.
     *
     * "3.5L V6" | "2.0L I4" | "Electric"
     */
    private function buildEngineString(array $r): ?string
    {
        $fuel   = $r['FuelTypePrimary']     ?? '';
        $disp   = $r['DisplacementL']       ?? '';
        $config = $r['EngineConfiguration'] ?? '';
        $cyl    = $r['EngineCylinders']     ?? '';

        if (stripos($fuel, 'electric') !== false) {
            return 'Electric';
        }

        $parts      = [];
        $blockShort = $this->parseBlockType($config);

        if ($disp) {
            $parts[] = round((float) $disp, 1) . 'L';
        }

        if ($blockShort && $cyl) {
            $parts[] = $blockShort . $cyl;       // "V6", "I4", "H6"
        } elseif ($cyl) {
            $parts[] = $cyl . '-Cylinder';
        }

        return $parts ? implode(' ', $parts) : null;
    }

    /**
     * Parse NHTSA EngineConfiguration → block_type short code (max 10 chars).
     *
     * "In-Line" → "I" | "V-Shape" → "V" | "H-Shape" → "H" | "Rotary" → "Rotary"
     */
    private function parseBlockType(string $nhtsaConfig): ?string
    {
        if (empty($nhtsaConfig)) {
            return null;
        }

        $normalized = strtolower(trim($nhtsaConfig));

        foreach (self::ENGINE_CONFIG_MAP as $keyword => $code) {
            if (str_contains($normalized, $keyword)) {
                return $code;
            }
        }

        return null;
    }

    /**
     * Standardize transmission → vehicle_specs.transmission_standard.
     * "Automatic" → "Automatic" | "Manual" → "Manual" | "CVT" → "CVT"
     */
    private function parseTransmissionStandard(string $nhtsaTransmission): ?string
    {
        if (empty($nhtsaTransmission)) {
            return null;
        }

        $n = strtolower(trim($nhtsaTransmission));

        return match (true) {
            str_contains($n, 'cvt')       => 'CVT',
            str_contains($n, 'dual')      => 'Dual Clutch',
            str_contains($n, 'automatic') => 'Automatic',
            str_contains($n, 'manual')    => 'Manual',
            default                       => ucwords($nhtsaTransmission),
        };
    }

    /**
     * Extract drivetrain acronym → vehicle_specs.drivetrain_standard.
     * "FWD/Front-Wheel Drive" → "FWD" | "AWD/All-Wheel Drive" → "AWD"
     */
    private function parseDrivetrainStandard(string $nhtsaDriveType): ?string
    {
        if (empty($nhtsaDriveType)) {
            return null;
        }

        return strtoupper(trim(explode('/', $nhtsaDriveType)[0]));
    }

    /**
     * Parse NHTSA GVWR string → integer lbs → vehicle_specs.gvwr.
     *
     * "Class 1C: 4,001 - 5,000 lb (1,814 - 2,268 kg)" → 5000
     * "Class 2A: 6,001 - 8,500 lb (2,722 - 3,856 kg)" → 8500
     * "5000"                                            → 5000
     *
     * Strategy: extract all numbers, filter to plausible GVWR range (1000–80000 lbs),
     * return the highest (= upper bound of the class range).
     */
    private function parseGvwr(string $gvwr): ?int
    {
        if (empty($gvwr)) {
            return null;
        }

        preg_match_all('/[\d,]+/', $gvwr, $matches);

        $reasonable = collect($matches[0])
            ->map(fn ($n) => (int) str_replace(',', '', $n))
            ->filter(fn ($n) => $n >= 1000 && $n <= 80000)
            ->values();

        return $reasonable->isEmpty() ? null : $reasonable->max();
    }

    // --------------------------------------------------------------------------
    // Warning Builder
    // --------------------------------------------------------------------------

    /**
     * Warnings for CRITICAL fields that couldn't be matched.
     * (body_type_id is required — dealer MUST select if not auto-matched)
     *
     * @return array<string>
     */
    private function buildWarnings(
        array $r,
        ?int $makeId,
        ?int $makeModelId,
        ?int $bodyTypeId,
    ): array {
        $warnings = [];

        if (! empty($r['Make']) && ! $makeId) {
            $warnings[] = "Make \"{$r['Make']}\" was not found in your catalog — please select it manually.";
        }

        if (! empty($r['Model']) && ! $makeModelId) {
            $warnings[] = "Model \"{$r['Model']}\" was not found in your catalog — please select it manually.";
        }

        if (! empty($r['BodyClass']) && ! $bodyTypeId) {
            $warnings[] = "Body type \"{$r['BodyClass']}\" could not be matched — please select it manually.";
        }

        return $warnings;
    }

    // --------------------------------------------------------------------------
    // Error Response Helper
    // --------------------------------------------------------------------------

    /** @return array{success: false, partial: false, message: string, warnings: array, data: array} */
    private function errorResponse(string $message): array
    {
        return [
            'success'  => false,
            'partial'  => false,
            'message'  => $message,
            'warnings' => [],
            'data'     => [],
        ];
    }
}
