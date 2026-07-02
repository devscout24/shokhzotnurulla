<?php

use App\Models\User;
use App\Models\Dealership\Dealer;
use App\Models\Website\Location;
use App\Models\Role;
use App\Models\Catalog\Make;
use App\Models\Catalog\MakeModel;
use App\Models\Catalog\BodyType;
use App\Models\Catalog\BodyTypeGroup;
use App\Models\Catalog\FuelType;
use App\Models\Catalog\Color;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function () {
    // ── Dealer ──────────────────────────────────────────────
    $this->dealer = Dealer::updateOrCreate(
        ['slug' => 'test-dealer'],
        [
            'company_name' => 'Test Dealer LLC',
            'name'         => 'Test Dealer',
            'email'        => 'test@dealer.com',
            'phone'        => '+1234567890',
            'is_active'    => true,
        ]
    );

    // ── Dealer user ─────────────────────────────────────────
    $this->user = User::updateOrCreate(
        ['email' => 'dealer@test.com'],
        [
            'first_name'       => 'Dealer',
            'last_name'        => 'User',
            'email_verified_at' => now(),
            'password'          => bcrypt('password123'),
            'is_active'         => true,
            'is_system_user'    => false,
            'current_dealer_id' => $this->dealer->id,
        ]
    );

    $this->user->dealers()->sync([
        $this->dealer->id => ['is_owner' => true],
    ]);

    // email_verified_at not in $fillable, set directly
    $this->user->email_verified_at = now();
    $this->user->save();

    // ── Role ─────────────────────────────────────────────────
    $role = Role::updateOrCreate(
        [
            'name'      => 'dealer_owner',
            'guard_name' => 'web',
            'dealer_id' => $this->dealer->id,
        ],
        ['is_active' => true]
    );

    setPermissionsTeamId($this->dealer->id);
    if (!$this->user->hasRole($role)) {
        $this->user->assignRole($role);
    }
    setPermissionsTeamId($this->dealer->id);

    // ── Location ─────────────────────────────────────────────
    $this->location = Location::create([
        'dealer_id' => $this->dealer->id,
        'name'      => 'Main Showroom',
        'street1'   => '123 Main St',
        'city'      => 'Los Angeles',
        'state'     => 'CA',
        'postalcode' => '90001',
        'country'   => 'US',
    ]);

    // ── Catalog data ─────────────────────────────────────────

    // Make
    $this->make = Make::where('slug', 'honda')->first();
    if (!$this->make) {
        $this->make = Make::create([
            'name' => 'Honda',
            'slug' => 'honda',
        ]);
    }

    // Existing model
    $this->existingModel = MakeModel::where('name', 'Civic')
        ->where('make_id', $this->make->id)
        ->first();
    if (!$this->existingModel) {
        $this->existingModel = MakeModel::create([
            'make_id' => $this->make->id,
            'name'    => 'Civic',
            'slug'    => 'honda-civic',
        ]);
    }

    // Body type — ensure body_type_groups exist first
    $group = BodyTypeGroup::where('slug', 'passenger-vehicles')->first();
    if (!$group) {
        $group = BodyTypeGroup::create([
            'name' => 'Passenger Vehicles',
            'slug' => 'passenger-vehicles',
        ]);
    }

    $this->bodyType = BodyType::where('slug', 'sedan')->first();
    if (!$this->bodyType) {
        $this->bodyType = BodyType::create([
            'body_type_group_id' => $group->id,
            'name'               => 'Sedan',
            'slug'               => 'sedan',
        ]);
    }
});

// ── Tests ─────────────────────────────────────────────────────

test('store creates vehicle with existing make_model_id', function () {
    $response = $this->actingAs($this->user)->post(route('dealer.inventory.store'), [
        'location_id'       => $this->location->id,
        'stock_number'      => 'STOCK001',
        'vin'               => '1HGCM82633A123456',
        'mileage'           => 15000,
        'year'              => 2023,
        'make_id'           => $this->make->id,
        'make_model_id'     => $this->existingModel->id,
        'trim'              => 'LX',
        'body_type_id'      => $this->bodyType->id,
        'vehicle_condition' => 'Used',
        'list_price'        => 25000,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('vehicles', [
        'stock_number'  => 'STOCK001',
        'make_id'       => $this->make->id,
        'make_model_id' => $this->existingModel->id,
    ]);
});



test('store creates vehicle with new make_model_name', function () {
    $newModelName = 'TestModel-' . Str::random(6);

    $response = $this->actingAs($this->user)->post(route('dealer.inventory.store'), [
        'location_id'       => $this->location->id,
        'stock_number'      => 'STOCK002',
        'vin'               => '2HGCM82633A654321',
        'mileage'           => 20000,
        'year'              => 2024,
        'make_id'           => $this->make->id,
        'make_model_name'   => $newModelName,
        'trim'              => 'EX',
        'body_type_id'      => $this->bodyType->id,
        'vehicle_condition' => 'New',
        'list_price'        => 30000,
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('make_models', [
        'make_id' => $this->make->id,
        'name'    => $newModelName,
    ]);

    $createdModel = MakeModel::where('name', $newModelName)->first();
    $this->assertNotNull($createdModel);

    $this->assertDatabaseHas('vehicles', [
        'stock_number'  => 'STOCK002',
        'make_id'       => $this->make->id,
        'make_model_id' => $createdModel->id,
    ]);
});

test('store creates vehicle with existing make_model_name', function () {
    $existingName = $this->existingModel->name;

    $response = $this->actingAs($this->user)->post(route('dealer.inventory.store'), [
        'location_id'       => $this->location->id,
        'stock_number'      => 'STOCK003',
        'vin'               => '3HGCM82633A789012',
        'mileage'           => 10000,
        'year'              => 2025,
        'make_id'           => $this->make->id,
        'make_model_name'   => $existingName,
        'trim'              => 'Sport',
        'body_type_id'      => $this->bodyType->id,
        'vehicle_condition' => 'Certified Pre-Owned',
        'list_price'        => 35000,
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('vehicles', [
        'stock_number'  => 'STOCK003',
        'make_id'       => $this->make->id,
        'make_model_id' => $this->existingModel->id,
    ]);
});
