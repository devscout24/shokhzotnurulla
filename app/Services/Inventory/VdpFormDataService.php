<?php

namespace App\Services\Inventory;

use App\Models\Catalog\BodyStyle;
use App\Models\Catalog\BodyStyleGroup;
use App\Models\Catalog\BodyType;
use App\Models\Catalog\BodyTypeGroup;
use App\Models\Catalog\Color;
use App\Models\Catalog\DrivetrainType;
use App\Models\Catalog\FactoryOptionCategory;
use App\Models\Catalog\FuelType;
use App\Models\Catalog\Make;
use App\Models\Catalog\TransmissionType;
use App\Models\Inventory\Vehicle;

class VdpFormDataService
{
    public function getDropdowns(): array
    {
        return [
            'makes'                   => Make::orderBy('name')->get(['id', 'name']),
            'colors'                  => Color::orderBy('name')->get(['id', 'name', 'hex']),
            'bodyTypeGroups'          => BodyTypeGroup::with([
                                          'bodyTypes' => fn ($q) => $q->orderBy('name'),
                                        ])->orderBy('name')->get(['id', 'name']),
            'bodyStyleGroups'         => BodyStyleGroup::with([
                                          'bodyStyles' => fn ($q) => $q->orderBy('name'),
                                        ])->orderBy('name')->get(['id', 'name']),
            'bodyTypes'               => BodyType::orderBy('name')->get(['id', 'name']),
            'bodyStyles'              => BodyStyle::orderBy('name')->get(['id', 'name']),
            'fuelTypes'               => FuelType::orderBy('name')->get(['id', 'name']),
            'transmissionTypes'       => TransmissionType::orderBy('name')->get(['id', 'name']),
            'drivetrainTypes'         => DrivetrainType::orderBy('name')->get(['id', 'name']),
            'factoryOptionCategories' => FactoryOptionCategory::with([
                                          'groups.options',
                                        ])->get(),
        ];
    }

    public function getFactoryOptionState(Vehicle $vehicle): array
    {
        $options = $vehicle->factoryOptions;

        return [
            'selectedOptionIds' => $options->pluck('id')->toArray(),
            'starredOptionIds'  => $options
                                    ->filter(fn ($o) => $o->pivot->is_starred)
                                    ->pluck('id')
                                    ->toArray(),
        ];
    }
}