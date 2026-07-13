<?php

namespace App\Services\Inventory;

use App\Models\Catalog\BodyStyle;
use App\Models\Catalog\BodyType;
use App\Models\Catalog\Color;
use App\Models\Catalog\DrivetrainType;
use App\Models\Catalog\FuelType;
use App\Models\Catalog\Make;
use App\Models\Catalog\MakeModel;
use App\Models\Catalog\TransmissionType;
use App\Models\Vin\VehicleVinData;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VinDecodeServiceV2
{
    private const BASE_URL = 'https://api.vehicledatabases.com/advanced-vin-decode';

    private const TIMEOUT = 12;

    private const VIN_CACHE_TTL = 86400;

    private const CATALOG_CACHE_TTL = 21600;

    private const ENGINE_CONFIG_MAP = [
        'in-line' => 'I',
        'inline' => 'I',
        'v-shape' => 'V',
        'v' => 'V',
        'w-shape' => 'W',
        'w' => 'W',
        'h-shape' => 'H',
        'h' => 'H',
        'opposed' => 'H',
        'rotary' => 'Rotary',
        'single' => 'Single',
    ];

    public function decode(string $vin, ?int $modelYear = null): array
    {
        $vin = strtoupper(trim($vin));
        $cacheKey = 'vin_decode_v2:'.$vin.($modelYear ? ':'.$modelYear : '');

        Log::channel('vin-decode')->info('[VINDECODE] decode() called', [
            'vin' => $vin,
            'model_year' => $modelYear,
            'cache_key' => $cacheKey,
        ]);

        if (Cache::has($cacheKey)) {
            $cached = Cache::get($cacheKey);
            if (is_array($cached) && ($cached['success'] ?? false)) {
                Log::channel('vin-decode')->info('[VINDECODE] cache HIT', ['cache_key' => $cacheKey]);

                return $cached;
            }
            Cache::forget($cacheKey);
        }

        Log::channel('vin-decode')->info('[VINDECODE] cache MISS — fetching from API', ['vin' => $vin]);

        $result = $this->fetchAndNormalize($vin, $modelYear);

        if (is_array($result) && ($result['success'] ?? false)) {
            Cache::put($cacheKey, $result, self::VIN_CACHE_TTL);
        }

        return $result;
    }

    private function getDefaultHeaders(): array
    {
        $headers = [
            'Accept' => 'application/json',
        ];

        $apiKey = config('services.vehicle_databases.api_key') ?? env('VEHICLE_DATABASES_API_KEY');

        if ($apiKey) {
            $headers['x-Authkey'] = $apiKey;

            Log::channel('vin-decode')->debug('[VINDECODE] x-Authkey header set', [
                'key_prefix' => substr($apiKey, 0, 8).'****',
            ]);
        } else {
            Log::channel('vin-decode')->error('[VINDECODE] No API key found! Check VEHICLE_DATABASES_API_KEY env var or config/services.php');
        }

        return $headers;
    }

    private function fetchAndNormalize(string $vin, ?int $modelYear): array
    {
        $url = self::BASE_URL.'/'.$vin;
        $apiKey = config('services.vehicle_databases.api_key') ?? env('VEHICLE_DATABASES_API_KEY');
        $apiKeyPresent = ! empty($apiKey);

        Log::channel('vin-decode')->info('[VINDECODE] fetchAndNormalize START', [
            'vin' => $vin,
            'model_year' => $modelYear,
            'url' => $url,
            'api_key_present' => $apiKeyPresent,
            'api_key_prefix' => $apiKeyPresent ? substr($apiKey, 0, 8).'****' : null,
            'timeout' => self::TIMEOUT,
            'php_sapi' => php_sapi_name(),
        ]);

        $startTime = microtime(true);

        try {
            $headers = $this->getDefaultHeaders();

            Log::channel('vin-decode')->info('[VINDECODE] sending HTTP GET', [
                'url' => $url,
                'headers' => $headers,
            ]);

            $response = Http::timeout(self::TIMEOUT)
                ->withHeaders($headers)
                ->get($url);

            $elapsedMs = round((microtime(true) - $startTime) * 1000);

            Log::channel('vin-decode')->info('[VINDECODE] HTTP response received', [
                'status' => $response->status(),
                'elapsed_ms' => $elapsedMs,
                'body_length' => strlen($response->body()),
                'content_type' => $response->header('Content-Type'),
                'headers' => $response->headers(),
            ]);

            if (! $response->successful()) {
                $bodyPreview = substr($response->body(), 0, 1000);
                Log::channel('vin-decode')->error('[VINDECODE] API returned non-success HTTP status', [
                    'status' => $response->status(),
                    'elapsed_ms' => $elapsedMs,
                    'body_preview' => $bodyPreview,
                    'body_length' => strlen($response->body()),
                ]);

                return $this->errorResponse(
                    'Vehicle Databases API is currently unavailable (HTTP '.$response->status().'). Please try again shortly.'
                );
            }

            $body = $response->json();

            if (is_null($body)) {
                Log::channel('vin-decode')->error('[VINDECODE] response->json() returned null — body may not be valid JSON', [
                    'body_preview' => substr($response->body(), 0, 500),
                ]);

                return $this->errorResponse('Invalid response from Vehicle Databases API. Please try again.');
            }

            if (($body['status'] ?? '') !== 'success') {
                Log::channel('vin-decode')->error('[VINDECODE] API body status not success', [
                    'api_status' => $body['status'] ?? '(missing)',
                    'message' => $body['message'] ?? '(none)',
                    'elapsed_ms' => $elapsedMs,
                ]);

                return $this->errorResponse(
                    $body['message'] ?? 'Vehicle Databases API could not decode this VIN. Please verify the VIN and try again.'
                );
            }

            $rawData = $body['data'] ?? [];
            $trimKey = array_key_first($rawData);
            $records = $trimKey ? ($rawData[$trimKey] ?? []) : [];

            Log::channel('vin-decode')->info('[VINDECODE] data parsed', [
                'trim_count' => count($rawData),
                'trim_key' => $trimKey,
                'records_populated' => ! empty($records),
                'records_keys' => is_array($records) ? array_keys($records) : 'not_array',
            ]);

            if (empty($records)) {
                Log::channel('vin-decode')->warning('[VINDECODE] empty records from API', [
                    'vin' => $vin,
                    'raw_data_keys' => array_keys($rawData),
                ]);

                return $this->errorResponse('No data returned from Vehicle Databases. Please verify the VIN and try again.');
            }

            VehicleVinData::updateOrCreate(
                ['vin' => $vin],
                ['vehicle_databases' => $body]
            );

            Log::channel('vin-decode')->info('[VINDECODE] raw payload persisted to vehicle_vin_data');

            // ── V2 API: Premium / enriched data ────────────────────────────────
            try {
                $v2Url = self::BASE_URL.'/v2/'.$vin;
                Log::channel('vin-decode')->info('[VINDECODE] fetching V2 premium data', ['url' => $v2Url]);

                $v2StartTime = microtime(true);
                $v2Response = Http::timeout(self::TIMEOUT)
                    ->withHeaders($this->getDefaultHeaders())
                    ->get($v2Url);
                $v2ElapsedMs = round((microtime(true) - $v2StartTime) * 1000);

                Log::channel('vin-decode')->info('[VINDECODE] V2 response received', [
                    'status' => $v2Response->status(),
                    'elapsed_ms' => $v2ElapsedMs,
                    'body_length' => strlen($v2Response->body()),
                ]);

                if ($v2Response->successful()) {
                    $v2Json = $v2Response->json();
                    VehicleVinData::where('vin', $vin)->update([
                        'premium_vehicle_databases' => $v2Json,
                    ]);
                    Log::channel('vin-decode')->info('[VINDECODE] V2 premium data persisted');
                } else {
                    Log::channel('vin-decode')->warning('[VINDECODE] V2 API returned non-success', [
                        'status' => $v2Response->status(),
                        'body_preview' => substr($v2Response->body(), 0, 500),
                    ]);
                }
            } catch (Exception $e) {
                Log::channel('vin-decode')->warning('[VINDECODE] V2 API call failed (non-blocking)', [
                    'message' => $e->getMessage(),
                    'class' => get_class($e),
                    'file' => $e->getFile().':'.$e->getLine(),
                ]);
            }

            Log::channel('vin-decode')->info('[VINDECODE] calling normalizeResponse()', [
                'records_keys' => array_keys($records),
            ]);

            $result = $this->normalizeResponse($records);

            $totalElapsedMs = round((microtime(true) - $startTime) * 1000);
            Log::channel('vin-decode')->info('[VINDECODE] fetchAndNormalize COMPLETE', [
                'success' => $result['success'] ?? false,
                'partial' => $result['partial'] ?? false,
                'total_elapsed_ms' => $totalElapsedMs,
                'warnings_count' => count($result['warnings'] ?? []),
                'data_keys' => array_keys($result['data'] ?? []),
            ]);

            return $result;

        } catch (ConnectionException $e) {
            $elapsedMs = round((microtime(true) - $startTime) * 1000);
            Log::channel('vin-decode')->error('[VINDECODE] ConnectionException', [
                'message' => $e->getMessage(),
                'file' => $e->getFile().':'.$e->getLine(),
                'elapsed_ms' => $elapsedMs,
                'url' => $url,
            ]);

            return $this->errorResponse(
                'Could not connect to Vehicle Databases API. Check your internet connection and try again.'
            );
        } catch (Exception $e) {
            $elapsedMs = round((microtime(true) - $startTime) * 1000);
            Log::channel('vin-decode')->error('[VINDECODE] unexpected Exception', [
                'message' => $e->getMessage(),
                'class' => get_class($e),
                'file' => $e->getFile().':'.$e->getLine(),
                'trace_first' => $e->getTraceAsString(),
                'elapsed_ms' => $elapsedMs,
            ]);

            return $this->errorResponse(
                'An unexpected error occurred while decoding the VIN. Please try again.'
            );
        }
    }

    private function normalizeResponse(array $r): array
    {
        $basic = $r['basic'] ?? [];
        $engine = $r['engine'] ?? [];
        $manu = $r['manufacturer'] ?? [];
        $trans = $r['transmission'] ?? [];
        $drive = $r['drivetrain'] ?? [];
        $fuel = $r['fuel'] ?? [];
        $weight = $r['weight'] ?? [];
        $dims = $r['dimensions'] ?? [];
        $seat = $r['seating'] ?? [];
        $colors = $r['colors'] ?? [];
        $wheels = $r['wheels_and_tires'] ?? [];
        $market = $r['market_value'] ?? [];
        $feat = $r['feature'] ?? [];

        $makeId = $this->resolveMakeId($basic['make'] ?? '');
        $makeModelId = $this->resolveMakeModelId($basic['model'] ?? '', $makeId);
        $bodyTypeId = $this->resolveBodyTypeId($basic['body_type'] ?? '');
        $bodyStyleId = $this->resolveBodyStyleId($basic['body_type'] ?? '');
        $drivetrainId = $this->resolveDrivetrainTypeId($drive['drive_type'] ?? '');
        $fuelTypeId = $this->resolveFuelTypeId($fuel['fuel_type'] ?? '');
        $transmissionId = $this->resolveTransmissionTypeId($trans['transmission_style'] ?? '');

        [$hpValue, $hpRpm] = $this->parseHorsepower($engine['horsepower'] ?? '');
        [$torque, $torqueRpm] = $this->parseTorque($engine['net_torque'] ?? '');
        [$blockType, $cyl] = $this->parseCylinderConfig($engine['engine_number_of_cylinders'] ?? '');
        $displacementL = $this->parseDisplacement($engine);
        $engineConfig = $this->mapBlockTypeToConfig($blockType);
        $engineString = $this->buildEngineString(
            $blockType, $cyl, $displacementL, $fuel['fuel_type'] ?? ''
        );
        $transmissionStd = $this->parseTransmissionStandard($trans['transmission_style'] ?? '');
        $drivetrainStd = $this->parseDrivetrainStandard($drive['drive_type'] ?? '');

        $trim = trim($basic['trim']['Trim'] ?? '');
        $bodyClass = $basic['body_type'] ?? '';
        $exteriorCol = $this->resolveFirstColor($colors['exterior'] ?? []);
        $interiorCol = $this->resolveFirstColor($colors['interior'] ?? []);
        $mergedFeat = $this->mergeFeatures($feat);

        $warnings = $this->buildWarnings($basic, $makeId, $makeModelId, $bodyTypeId);

        Log::channel('vin-decode')->info('[VINDECODE] normalizeResponse resolutions', [
            'make' => $basic['make'] ?? '(none)',
            'make_id' => $makeId,
            'model' => $basic['model'] ?? '(none)',
            'make_model_id' => $makeModelId,
            'body_type' => $basic['body_type'] ?? '(none)',
            'body_type_id' => $bodyTypeId,
            'body_style_id' => $bodyStyleId,
            'drivetrain_type_id' => $drivetrainId,
            'fuel_type_id' => $fuelTypeId,
            'transmission_id' => $transmissionId,
            'exterior_color_id' => $exteriorCol,
            'interior_color_id' => $interiorCol,
            'warnings_count' => count($warnings),
        ]);

        return [
            'success' => true,
            'partial' => false,
            'message' => null,
            'warnings' => $warnings,
            'data' => [
                // ── Core identity ──────────────────────────────────────────────
                'year' => $basic['year'] ?: null,
                'make' => $basic['make'] ?: null,
                'make_id' => $makeId,
                'model' => $basic['model'] ?: null,
                'make_model_id' => $makeModelId,
                'trim' => $trim ?: null,

                // ── Body ──────────────────────────────────────────────────────
                'body_class' => $bodyClass ?: null,
                'body_type_id' => $bodyTypeId,
                'body_style_id' => $bodyStyleId,
                'doors' => $basic['doors'] ?: null,
                'body_style' => $basic['body_type'] ?: null,

                // ── Drivetrain ─────────────────────────────────────────────────
                'drive_type' => $drive['drive_type'] ?: null,
                'drivetrain_type_id' => $drivetrainId,
                'drivetrain_standard' => $drivetrainStd,

                // ── Fuel ───────────────────────────────────────────────────────
                'fuel_type_primary' => $fuel['fuel_type'] ?: null,
                'fuel_type_id' => $fuelTypeId,

                // ── Transmission ───────────────────────────────────────────────
                'transmission_style' => $trans['transmission_style'] ?: null,
                'transmission_type_id' => $transmissionId,
                'transmission_standard' => $transmissionStd,

                // ── Engine string ──────────────────────────────────────────────
                'engine_string' => $engineString,

                // ── Engine specs (→ vehicles + vehicle_specs) ──────────────────
                'engine_hp' => $hpValue,
                'engine_hp_rpm' => $hpRpm,
                'engine_cylinders' => $cyl,
                'engine_displacement_l' => $displacementL,
                'engine_config' => $engineConfig,
                'block_type' => $blockType,
                'torque' => $torque,
                'torque_rpm' => $torqueRpm,
                'engine_valves' => $engine['engine_valves'] ?: null,
                'compression' => $engine['compression'] ?: null,
                'engine_model' => $engine['engine_model'] ?: null,

                // ── Weight / GVWR ──────────────────────────────────────────────
                'gvwr' => null,
                'empty_weight' => $this->extractInt($weight['curb_weight'] ?? ''),

                // ── Fuel economy (→ vehicle_specs) ─────────────────────────────
                'fuel_tank' => $this->extractDecimal($fuel['fuel_capacity'] ?? ''),
                'mpg_city' => $this->extractDecimal($fuel['city_mileage'] ?? ''),
                'mpg_highway' => $this->extractDecimal($fuel['highway_mileage'] ?? ''),
                'mpg_combined' => $this->extractDecimal($fuel['fuel_economy_est_combined'] ?? ''),
                'fuel_injection_type' => $fuel['fuel_injection_type'] ?: null,

                // ── Dimensions (→ vehicle_specs) ───────────────────────────────
                'dimension_width' => $this->extractDecimal($dims['width'] ?? ''),
                'dimension_length' => $this->extractDecimal($dims['length'] ?? ''),
                'dimension_height' => $this->extractDecimal($dims['height'] ?? ''),
                'wheelbase' => $this->extractDecimal($dims['wheelbase'] ?? ''),

                // ── Drivetrain specs (→ vehicle_specs) ─────────────────────────
                'axle_ratio' => $this->extractDecimal($drive['final_drive_axle_ratio'] ?? ''),

                // ── Wheels & tires (→ vehicle_specs) ───────────────────────────
                'front_tire' => $wheels['front_tire_size'] ?: null,
                'rear_tire' => $wheels['rear_tire_size'] ?: null,
                'front_wheel' => $wheels['front_wheel_material'] ?: null,
                'rear_wheel' => $wheels['rear_wheel_material'] ?: null,

                // ── Seating (→ vehicles) ───────────────────────────────────────
                'seating_capacity' => $this->extractInt($seat['standard_seating'] ?? ''),

                // ── Pricing (→ vehicle_prices) ─────────────────────────────────
                'msrp' => $this->extractDecimal($market['msrp'] ?? ''),
                'dealer_cost' => $this->extractDecimal($market['invoice_price'] ?? ''),
                'destination_charge' => $this->extractDecimal($market['destination_charge'] ?? ''),

                // ── Colors ─────────────────────────────────────────────────────
                'exterior_color_id' => $exteriorCol,
                'interior_color_id' => $interiorCol,
                'exterior_colors' => $colors['exterior'] ?? [],
                'interior_colors' => $colors['interior'] ?? [],

                // ── Features (→ vehicle_notes.key_highlights + premium options) ─
                'features' => $mergedFeat,
                'features_categorized' => $feat,

                // ── Metadata ───────────────────────────────────────────────────
                'manufacturer' => $manu['manufacturer'] ?: null,
                'plant_city' => null,
                'plant_country' => $manu['country'] ?: null,
                'vehicle_type' => $basic['vehicle_type'] ?: null,
                'vehicle_size' => $basic['vehicle_size'] ?: null,
            ],
        ];
    }

    private function parseHorsepower(string $raw): array
    {
        if (empty($raw)) {
            return [null, null];
        }

        if (preg_match('/^(\d+)\s*@\s*(\d+)/', $raw, $m)) {
            return [(int) $m[1], (int) $m[2]];
        }

        if (preg_match('/^(\d+)/', $raw, $m)) {
            return [(int) $m[1], null];
        }

        return [null, null];
    }

    private function parseCylinderConfig(string $raw): array
    {
        if (empty($raw)) {
            return [null, null];
        }

        $normalized = strtolower(trim($raw));

        if ($normalized === 'rotary') {
            return ['Rotary', null];
        }

        // "V-6", "V6", "V 6", "I-4", "I4" ...
        if (preg_match('/^([a-z])\s*-?\s*(\d+)$/', $normalized, $m)) {
            $shortCode = strtoupper($m[1]);
            $shortCode = isset(array_flip(self::ENGINE_CONFIG_MAP)[$shortCode])
                ? $shortCode
                : null;

            return [$shortCode, (int) $m[2]];
        }

        // "6" — plain number, cylinders only
        if (preg_match('/^(\d+)$/', $normalized, $m)) {
            return [null, (int) $m[1]];
        }

        return [null, null];
    }

    private function parseDisplacement(array $engine): ?float
    {
        if (! empty($engine['engine_displacement_units'])) {
            $val = (float) $engine['engine_displacement_units'];

            if ($val > 0) {
                return $val;
            }
        }

        if (! empty($engine['displacement_(l_ci)'])) {
            $val = (int) preg_replace('/[^0-9]/', '', $engine['displacement_(l_ci)']);

            if ($val > 0) {
                return round($val / 1000, 1);
            }
        }

        return null;
    }

    private function parseTorque(string $raw): array
    {
        if (empty($raw)) {
            return [null, null];
        }

        if (preg_match('/^(\d+)\s*@\s*(\d+)/', $raw, $m)) {
            return [(int) $m[1], (int) $m[2]];
        }

        if (preg_match('/^(\d+)/', $raw, $m)) {
            return [(int) $m[1], null];
        }

        return [null, null];
    }

    private function extractInt(string $raw): ?int
    {
        $cleaned = preg_replace('/[^0-9-]/', '', $raw);

        return $cleaned !== '' ? (int) $cleaned : null;
    }

    private function extractDecimal(string $raw): ?float
    {
        if (empty($raw)) {
            return null;
        }

        // Remove $ and commas, keep digits, decimal point, minus
        $cleaned = preg_replace('/[^0-9.\-]/', '', $raw);

        if ($cleaned === '' || $cleaned === '-' || $cleaned === '.') {
            return null;
        }

        return (float) $cleaned;
    }

    private function mapBlockTypeToConfig(?string $blockType): ?string
    {
        return match ($blockType) {
            'I' => 'In-Line',
            'V' => 'V-Shape',
            'W' => 'W-Shape',
            'H' => 'H-Shape',
            default => null,
        };
    }

    private function buildEngineString(?string $blockType, ?int $cyl, ?float $displacementL, string $fuelType): ?string
    {
        if (stripos($fuelType, 'electric') !== false) {
            return 'Electric';
        }

        $parts = [];

        if ($displacementL) {
            $parts[] = round($displacementL, 1).'L';
        }

        if ($blockType && $cyl) {
            $parts[] = $blockType.$cyl;
        } elseif ($cyl) {
            $parts[] = $cyl.'-Cylinder';
        }

        return $parts ? implode(' ', $parts) : null;
    }

    private function resolveMakeId(string $make): ?int
    {
        if (empty($make)) {
            return null;
        }

        return Cache::remember(
            'vin_v2_make_id:'.strtolower($make),
            self::CATALOG_CACHE_TTL,
            fn (): ?int => Make::whereRaw('LOWER(name) = ?', [strtolower($make)])->value('id')
        );
    }

    private function resolveMakeModelId(string $model, ?int $makeId): ?int
    {
        if (empty($model) || ! $makeId) {
            return null;
        }

        return Cache::remember(
            'vin_v2_model_id:'.$makeId.':'.strtolower($model),
            self::CATALOG_CACHE_TTL,
            fn (): ?int => MakeModel::where('make_id', $makeId)
                ->whereRaw('LOWER(name) = ?', [strtolower($model)])
                ->value('id')
        );
    }

    private function resolveBodyTypeId(string $bodyClass): ?int
    {
        if (empty($bodyClass)) {
            return null;
        }

        $typeMap = $this->getBodyTypeMap();
        $normalized = strtolower(trim($bodyClass));

        if (isset($typeMap[$normalized])) {
            return $typeMap[$normalized];
        }

        if (preg_match('/\(([^)]+)\)/', $bodyClass, $m)) {
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

        $firstKeyword = strtolower(strtok($normalized, '/ ') ?: $normalized);
        foreach ($typeMap as $dbName => $id) {
            if (str_contains($dbName, $firstKeyword) || str_contains($firstKeyword, $dbName)) {
                return $id;
            }
        }

        foreach ($typeMap as $dbName => $id) {
            if (str_contains($normalized, $dbName) || str_contains($dbName, $normalized)) {
                return $id;
            }
        }

        return null;
    }

    private function resolveBodyStyleId(string $bodyClass): ?int
    {
        if (empty($bodyClass)) {
            return null;
        }

        $styleMap = $this->getBodyStyleMap();
        $normalized = strtolower(trim($bodyClass));

        if (isset($styleMap[$normalized])) {
            return $styleMap[$normalized];
        }

        if (preg_match('/\(([^)]+)\)/', $bodyClass, $m)) {
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

        $firstKeyword = strtolower(strtok($normalized, '/ ') ?: $normalized);
        foreach ($styleMap as $dbName => $id) {
            if (str_contains($dbName, $firstKeyword) || str_contains($firstKeyword, $dbName)) {
                return $id;
            }
        }

        foreach ($styleMap as $dbName => $id) {
            if (str_contains($normalized, $dbName) || str_contains($dbName, $normalized)) {
                return $id;
            }
        }

        return null;
    }

    private function resolveDrivetrainTypeId(string $driveType): ?int
    {
        if (empty($driveType)) {
            return null;
        }

        $acronym = strtolower(explode('/', $driveType)[0]);
        $key = 'vin_v2_drivetrain:'.$acronym;

        return Cache::remember($key, self::CATALOG_CACHE_TTL, function () use ($acronym, $driveType): ?int {
            $id = DrivetrainType::whereRaw('LOWER(name) LIKE ?', ['%'.$acronym.'%'])->value('id');

            if ($id) {
                return $id;
            }

            $description = strtolower(trim(explode('/', $driveType)[1] ?? ''));

            return $description
                ? DrivetrainType::whereRaw('LOWER(name) LIKE ?', ['%'.$description.'%'])->value('id')
                : null;
        });
    }

    private function resolveFuelTypeId(string $fuel): ?int
    {
        if (empty($fuel)) {
            return null;
        }

        $baseKeyword = strtolower(trim(explode('(', $fuel)[0]));
        $key = 'vin_v2_fuel:'.$baseKeyword;

        return Cache::remember(
            $key,
            self::CATALOG_CACHE_TTL,
            fn (): ?int => FuelType::whereRaw('LOWER(name) LIKE ?', ['%'.$baseKeyword.'%'])->value('id')
        );
    }

    private function resolveTransmissionTypeId(string $transmission): ?int
    {
        if (empty($transmission)) {
            return null;
        }

        $normalized = strtolower(trim($transmission));
        $key = 'vin_v2_transmission:'.$normalized;

        return Cache::remember($key, self::CATALOG_CACHE_TTL, function () use ($normalized): ?int {
            return TransmissionType::whereRaw('LOWER(standard) = ?', [$normalized])->value('id')
                ?? TransmissionType::whereRaw('LOWER(name) LIKE ?', ['%'.$normalized.'%'])->value('id');
        });
    }

    private function getBodyTypeMap(): array
    {
        return Cache::remember('vin_v2_body_type_map', self::CATALOG_CACHE_TTL, function (): array {
            return BodyType::all(['id', 'name'])
                ->mapWithKeys(fn ($bt) => [strtolower($bt->name) => $bt->id])
                ->all();
        });
    }

    private function getBodyStyleMap(): array
    {
        return Cache::remember('vin_v2_body_style_map', self::CATALOG_CACHE_TTL, function (): array {
            return BodyStyle::all(['id', 'name'])
                ->mapWithKeys(fn ($bs) => [strtolower($bs->name) => $bs->id])
                ->all();
        });
    }

    private function parseTransmissionStandard(string $raw): ?string
    {
        if (empty($raw)) {
            return null;
        }

        $n = strtolower(trim($raw));

        return match (true) {
            str_contains($n, 'cvt') => 'CVT',
            str_contains($n, 'dual') => 'Dual Clutch',
            str_contains($n, 'automatic') => 'Automatic',
            str_contains($n, 'manual') => 'Manual',
            default => ucwords($raw),
        };
    }

    private function parseDrivetrainStandard(string $driveType): ?string
    {
        if (empty($driveType)) {
            return null;
        }

        return strtoupper(trim(explode('/', $driveType)[0]));
    }

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

    private function resolveFirstColor(array $apiColors): ?int
    {
        if (empty($apiColors)) {
            return null;
        }

        $first = $apiColors[0] ?? [];

        $candidates = array_filter([
            $first['Generic Name'] ?? null,
            $first['Color'] ?? null,
        ]);

        foreach ($candidates as $name) {
            $name = trim($name);

            if (empty($name)) {
                continue;
            }

            $id = Color::whereRaw('LOWER(name) = ?', [strtolower($name)])
                ->orWhereRaw('LOWER(standard_name) = ?', [strtolower($name)])
                ->value('id');

            if ($id) {
                return $id;
            }
        }

        return null;
    }

    private function mergeFeatures(array $feature): array
    {
        $sections = [
            'mechanical_and_powertrain',
            'safety',
            'interior',
            'exterior',
        ];

        $merged = [];

        foreach ($sections as $section) {
            $items = $feature[$section] ?? [];

            if (is_array($items)) {
                array_push($merged, ...$items);
            }
        }

        return $merged;
    }

    private function buildWarnings(
        array $basic,
        ?int $makeId,
        ?int $makeModelId,
        ?int $bodyTypeId,
    ): array {
        $warnings = [];

        if (! empty($basic['make']) && ! $makeId) {
            $warnings[] = "Make \"{$basic['make']}\" was not found in your catalog — please select it manually.";
        }

        if (! empty($basic['model']) && ! $makeModelId) {
            $warnings[] = "Model \"{$basic['model']}\" was not found in your catalog — please select it manually.";
        }

        if (! empty($basic['body_type']) && ! $bodyTypeId) {
            $warnings[] = "Body type \"{$basic['body_type']}\" could not be matched — please select it manually.";
        }

        return $warnings;
    }

    private function errorResponse(string $message): array
    {
        return [
            'success' => false,
            'partial' => false,
            'message' => $message,
            'warnings' => [],
            'data' => [],
        ];
    }
}
