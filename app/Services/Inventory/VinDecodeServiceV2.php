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

class VinDecodeServiceV2
{
    private const BASE_URL          = 'https://api.vehicledatabases.com/advanced-vin-decode';
    private const TIMEOUT           = 12;
    private const VIN_CACHE_TTL     = 86400;
    private const CATALOG_CACHE_TTL = 21600;

    private const ENGINE_CONFIG_MAP = [
        'in-line' => 'I',
        'inline'  => 'I',
        'v-shape' => 'V',
        'v'       => 'V',
        'w-shape' => 'W',
        'w'       => 'W',
        'h-shape' => 'H',
        'h'       => 'H',
        'opposed' => 'H',
        'rotary'  => 'Rotary',
        'single'  => 'Single',
    ];

    public function decode(string $vin, ?int $modelYear = null): array
    {
        $vin      = strtoupper(trim($vin));
        $cacheKey = 'vin_decode_v2:' . $vin . ($modelYear ? ':' . $modelYear : '');

        return Cache::remember($cacheKey, self::VIN_CACHE_TTL, function () use ($vin, $modelYear) {
            return $this->fetchAndNormalize($vin, $modelYear);
        });
    }

    private function getDefaultHeaders(): array
    {
        $headers = [
            'Accept' => 'application/json',
        ];

        $apiKey = config('services.vehicle_databases.api_key') ?? env('VEHICLE_DATABASES_API_KEY');

        if ($apiKey) {
            $headers['x-Authkey'] = $apiKey;
        }

        return $headers;
    }

    private function fetchAndNormalize(string $vin, ?int $modelYear): array
    {
        $url = self::BASE_URL . '/' . $vin;

        try {
            $response = Http::timeout(self::TIMEOUT)
                ->withHeaders($this->getDefaultHeaders())
                ->get($url);

            if (! $response->successful()) {
                return $this->errorResponse(
                    'Vehicle Databases API is currently unavailable (HTTP ' . $response->status() . '). Please try again shortly.'
                );
            }

            $body = $response->json();

            if (($body['status'] ?? '') !== 'success') {
                return $this->errorResponse(
                    $body['message'] ?? 'Vehicle Databases API could not decode this VIN. Please verify the VIN and try again.'
                );
            }

            $rawData = $body['data'] ?? [];
            $trimKey = array_key_first($rawData);
            $records = $trimKey ? ($rawData[$trimKey] ?? []) : [];

            if (empty($records)) {
                return $this->errorResponse('No data returned from Vehicle Databases. Please verify the VIN and try again.');
            }

            return $this->normalizeResponse($records);

        } catch (ConnectionException) {
            return $this->errorResponse(
                'Could not connect to Vehicle Databases API. Check your internet connection and try again.'
            );
        } catch (Exception) {
            return $this->errorResponse(
                'An unexpected error occurred while decoding the VIN. Please try again.'
            );
        }
    }

    private function normalizeResponse(array $r): array
    {
        $basic   = $r['basic']        ?? [];
        $engine  = $r['engine']       ?? [];
        $manu    = $r['manufacturer'] ?? [];
        $trans   = $r['transmission'] ?? [];
        $drive   = $r['drivetrain']   ?? [];
        $fuel    = $r['fuel']         ?? [];
        $weight  = $r['weight']       ?? [];

        $makeId         = $this->resolveMakeId($basic['make'] ?? '');
        $makeModelId    = $this->resolveMakeModelId($basic['model'] ?? '', $makeId);
        $bodyTypeId     = $this->resolveBodyTypeId($basic['body_type'] ?? '');
        $bodyStyleId    = $this->resolveBodyStyleId($basic['body_type'] ?? '');
        $drivetrainId   = $this->resolveDrivetrainTypeId($drive['drive_type'] ?? '');
        $fuelTypeId     = $this->resolveFuelTypeId($fuel['fuel_type'] ?? '');
        $transmissionId = $this->resolveTransmissionTypeId($trans['transmission_style'] ?? '');

        [$hpValue, $hpRpm]    = $this->parseHorsepower($engine['horsepower'] ?? '');
        [$blockType, $cyl]    = $this->parseCylinderConfig($engine['engine_number_of_cylinders'] ?? '');
        $displacementL        = $this->parseDisplacement($engine);
        $engineConfig         = $this->mapBlockTypeToConfig($blockType);
        $engineString         = $this->buildEngineString(
            $blockType, $cyl, $displacementL, $fuel['fuel_type'] ?? ''
        );
        $transmissionStd      = $this->parseTransmissionStandard($trans['transmission_style'] ?? '');
        $drivetrainStd        = $this->parseDrivetrainStandard($drive['drive_type'] ?? '');
        $gvwrParsed           = $this->parseGvwr($weight['curb_weight'] ?? '');

        $trim      = trim($basic['trim']['Trim'] ?? '');
        $bodyClass = $basic['body_type'] ?? '';

        $warnings = $this->buildWarnings($basic, $makeId, $makeModelId, $bodyTypeId);

        return [
            'success'  => true,
            'partial'  => false,
            'message'  => null,
            'warnings' => $warnings,
            'data'     => [
                'year'                   => $basic['year']              ?: null,
                'make'                   => $basic['make']              ?: null,
                'make_id'                => $makeId,
                'model'                  => $basic['model']             ?: null,
                'make_model_id'          => $makeModelId,
                'trim'                   => $trim                       ?: null,

                'body_class'             => $bodyClass                  ?: null,
                'body_type_id'           => $bodyTypeId,
                'body_style_id'          => $bodyStyleId,
                'doors'                  => $basic['doors']             ?: null,

                'drive_type'             => $drive['drive_type']        ?: null,
                'drivetrain_type_id'     => $drivetrainId,
                'drivetrain_standard'    => $drivetrainStd,

                'fuel_type_primary'      => $fuel['fuel_type']          ?: null,
                'fuel_type_id'           => $fuelTypeId,

                'transmission_style'     => $trans['transmission_style'] ?: null,
                'transmission_type_id'   => $transmissionId,
                'transmission_standard'  => $transmissionStd,

                'engine_string'          => $engineString,

                'engine_hp'              => $hpValue,
                'engine_cylinders'       => $cyl,
                'engine_displacement_l'  => $displacementL,
                'engine_config'          => $engineConfig,
                'block_type'             => $blockType,
                'gvwr'                   => $gvwrParsed,

                'manufacturer'           => $manu['manufacturer']       ?: null,
                'plant_city'             => null,
                'plant_country'          => $manu['country']            ?: null,
                'vehicle_type'           => $basic['vehicle_type']      ?: null,
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

        if (preg_match('/^([A-Za-z])-(\d+)$/', $raw, $m)) {
            $shortCode = strtoupper($m[1]);
            $shortCode = isset(array_flip(self::ENGINE_CONFIG_MAP)[$shortCode])
                ? $shortCode
                : null;

            return [$shortCode, (int) $m[2]];
        }

        if (preg_match('/^(\d+)$/', $raw, $m)) {
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
            $parts[] = round($displacementL, 1) . 'L';
        }

        if ($blockType && $cyl) {
            $parts[] = $blockType . $cyl;
        } elseif ($cyl) {
            $parts[] = $cyl . '-Cylinder';
        }

        return $parts ? implode(' ', $parts) : null;
    }

    private function resolveMakeId(string $make): ?int
    {
        if (empty($make)) {
            return null;
        }

        return Cache::remember(
            'vin_v2_make_id:' . strtolower($make),
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
            'vin_v2_model_id:' . $makeId . ':' . strtolower($model),
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

        $typeMap    = $this->getBodyTypeMap();
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

        $styleMap   = $this->getBodyStyleMap();
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
        $key     = 'vin_v2_drivetrain:' . $acronym;

        return Cache::remember($key, self::CATALOG_CACHE_TTL, function () use ($acronym, $driveType): ?int {
            $id = DrivetrainType::whereRaw('LOWER(name) LIKE ?', ['%' . $acronym . '%'])->value('id');

            if ($id) {
                return $id;
            }

            $description = strtolower(trim(explode('/', $driveType)[1] ?? ''));

            return $description
                ? DrivetrainType::whereRaw('LOWER(name) LIKE ?', ['%' . $description . '%'])->value('id')
                : null;
        });
    }

    private function resolveFuelTypeId(string $fuel): ?int
    {
        if (empty($fuel)) {
            return null;
        }

        $baseKeyword = strtolower(trim(explode('(', $fuel)[0]));
        $key         = 'vin_v2_fuel:' . $baseKeyword;

        return Cache::remember(
            $key,
            self::CATALOG_CACHE_TTL,
            fn (): ?int => FuelType::whereRaw('LOWER(name) LIKE ?', ['%' . $baseKeyword . '%'])->value('id')
        );
    }

    private function resolveTransmissionTypeId(string $transmission): ?int
    {
        if (empty($transmission)) {
            return null;
        }

        $normalized = strtolower(trim($transmission));
        $key        = 'vin_v2_transmission:' . $normalized;

        return Cache::remember($key, self::CATALOG_CACHE_TTL, function () use ($normalized): ?int {
            return TransmissionType::whereRaw('LOWER(standard) = ?', [$normalized])->value('id')
                ?? TransmissionType::whereRaw('LOWER(name) LIKE ?', ['%' . $normalized . '%'])->value('id');
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
            str_contains($n, 'cvt')       => 'CVT',
            str_contains($n, 'dual')      => 'Dual Clutch',
            str_contains($n, 'automatic') => 'Automatic',
            str_contains($n, 'manual')    => 'Manual',
            default                       => ucwords($raw),
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
            'success'  => false,
            'partial'  => false,
            'message'  => $message,
            'warnings' => [],
            'data'     => [],
        ];
    }
}
