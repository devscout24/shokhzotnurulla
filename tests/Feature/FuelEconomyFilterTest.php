<?php

use App\Models\Dealership\Dealer;
use App\Models\Website\Domain;
use App\Models\Inventory\Vehicle;
use App\Models\Inventory\VehicleSpec;
use App\Models\Catalog\Make;
use App\Models\Catalog\MakeModel;
use App\Models\Catalog\BodyType;
use App\Models\Catalog\BodyTypeGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function () {
    // ── Dealer ──────────────────────────────────────────────
    $this->dealer = Dealer::create([
        'slug'         => 'test-dealer',
        'company_name' => 'Test Dealer LLC',
        'name'         => 'Test Dealer',
        'email'        => 'test@dealer.com',
        'phone'        => '+1234567890',
        'is_active'    => true,
        'domain'       => 'localhost',
    ]);

    // ── Domain ──────────────────────────────────────────────
    Domain::create([
        'dealer_id'  => $this->dealer->id,
        'domain'     => 'localhost',
        'is_primary' => true,
    ]);

    // ── Catalog details ─────────────────────────────────────
    $make = Make::create([
        'name' => 'Honda',
        'slug' => 'honda',
    ]);

    $model = MakeModel::create([
        'make_id' => $make->id,
        'name'    => 'Civic',
        'slug'    => 'honda-civic',
    ]);

    $group = BodyTypeGroup::create([
        'name' => 'Passenger Vehicles',
        'slug' => 'passenger-vehicles',
    ]);

    $bodyType = BodyType::create([
        'body_type_group_id' => $group->id,
        'name'               => 'Sedan',
        'slug'               => 'sedan',
    ]);

    // ── Create Vehicles with MPG specs ─────────────────────
    // Vehicle 1: Low MPG (15 city, 22 hwy)
    $this->vehicle1 = Vehicle::create([
        'dealer_id'         => $this->dealer->id,
        'ulid'              => (string) Str::ulid(),
        'stock_number'      => 'STOCK1',
        'vin'               => '1HGCM82633A111111',
        'mileage'           => 10000,
        'year'              => 2020,
        'make_id'           => $make->id,
        'make_model_id'     => $model->id,
        'body_type_id'      => $bodyType->id,
        'vehicle_condition' => 'Used',
        'list_price'        => 20000,
        'status'            => 'active',
    ]);
    VehicleSpec::create([
        'vehicle_id'  => $this->vehicle1->id,
        'mpg_city'    => 15.0,
        'mpg_highway' => 22.0,
    ]);

    // Vehicle 2: Mid MPG (25 city, 32 hwy)
    $this->vehicle2 = Vehicle::create([
        'dealer_id'         => $this->dealer->id,
        'ulid'              => (string) Str::ulid(),
        'stock_number'      => 'STOCK2',
        'vin'               => '1HGCM82633A222222',
        'mileage'           => 12000,
        'year'              => 2021,
        'make_id'           => $make->id,
        'make_model_id'     => $model->id,
        'body_type_id'      => $bodyType->id,
        'vehicle_condition' => 'Used',
        'list_price'        => 22000,
        'status'            => 'active',
    ]);
    VehicleSpec::create([
        'vehicle_id'  => $this->vehicle2->id,
        'mpg_city'    => 25.0,
        'mpg_highway' => 32.0,
    ]);

    // Vehicle 3: High MPG (35 city, 42 hwy)
    $this->vehicle3 = Vehicle::create([
        'dealer_id'         => $this->dealer->id,
        'ulid'              => (string) Str::ulid(),
        'stock_number'      => 'STOCK3',
        'vin'               => '1HGCM82633A333333',
        'mileage'           => 8000,
        'year'              => 2022,
        'make_id'           => $make->id,
        'make_model_id'     => $model->id,
        'body_type_id'      => $bodyType->id,
        'vehicle_condition' => 'Used',
        'list_price'        => 25000,
        'status'            => 'active',
    ]);
    VehicleSpec::create([
        'vehicle_id'  => $this->vehicle3->id,
        'mpg_city'    => 35.0,
        'mpg_highway' => 42.0,
    ]);
});

test('inventory filter by highway mpg returns correct vehicles', function () {
    // Highway MPG >= 30: Should return vehicle 2 (32 hwy) and vehicle 3 (42 hwy)
    $response = $this->get(route('frontend.inventory', [
        'mpghwy' => ['gt' => '30'],
    ]));

    $response->assertStatus(200);
    $vehicles = $response->viewData('vehicles');
    
    expect($vehicles->count())->toBe(2);
    expect($vehicles->pluck('id')->toArray())->toContain($this->vehicle2->id, $this->vehicle3->id);
    expect($vehicles->pluck('id')->toArray())->not->toContain($this->vehicle1->id);
});

test('inventory filter by city mpg returns correct vehicles', function () {
    // City MPG >= 30: Should only return vehicle 3 (35 city)
    $response = $this->get(route('frontend.inventory', [
        'mpgcity' => ['gt' => '30'],
    ]));

    $response->assertStatus(200);
    $vehicles = $response->viewData('vehicles');

    expect($vehicles->count())->toBe(1);
    expect($vehicles->pluck('id')->toArray())->toContain($this->vehicle3->id);
    expect($vehicles->pluck('id')->toArray())->not->toContain($this->vehicle1->id, $this->vehicle2->id);
});

test('inventory filter sidebar contains correct dynamic counts', function () {
    $response = $this->get(route('frontend.inventory'));

    $response->assertStatus(200);
    $filterData = $response->viewData('filterData');

    expect($filterData)->toHaveKey('mpgHighwayCounts');
    expect($filterData)->toHaveKey('mpgCityCounts');

    // Highway counts validation:
    // 20+ hwy (vehicle 1 [22], 2 [32], 3 [42]): 3
    // 25+ hwy (vehicle 2 [32], 3 [42]): 2
    // 30+ hwy (vehicle 2 [32], 3 [42]): 2
    // 35+ hwy (vehicle 3 [42]): 1
    // 40+ hwy (vehicle 3 [42]): 1
    expect($filterData['mpgHighwayCounts'][20])->toBe(3);
    expect($filterData['mpgHighwayCounts'][25])->toBe(2);
    expect($filterData['mpgHighwayCounts'][30])->toBe(2);
    expect($filterData['mpgHighwayCounts'][35])->toBe(1);
    expect($filterData['mpgHighwayCounts'][40])->toBe(1);

    // City counts validation:
    // 20+ city (vehicle 2 [25], 3 [35]): 2
    // 25+ city (vehicle 2 [25], 3 [35]): 2
    // 30+ city (vehicle 3 [35]): 1
    // 35+ city (vehicle 3 [35]): 1
    // 40+ city: 0
    expect($filterData['mpgCityCounts'][20])->toBe(2);
    expect($filterData['mpgCityCounts'][25])->toBe(2);
    expect($filterData['mpgCityCounts'][30])->toBe(1);
    expect($filterData['mpgCityCounts'][35])->toBe(1);
    expect($filterData['mpgCityCounts'][40])->toBe(0);
});
