<?php

use App\Models\AdminRestrictedSite;
use App\Models\AdminSetting;
use App\Models\User;
use App\Models\Dealership\Dealer;
use App\Models\Role;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

beforeEach(function () {
    // Safely delete any records created by other tests or previous runs within transaction
    AdminRestrictedSite::query()->delete();
    AdminSetting::where('key', 'restricted_login_enabled')->delete();

    // 1. Create or fetch system dealer and an admin user
    $this->systemDealer = Dealer::updateOrCreate(
        ['slug' => 'system'],
        [
            'company_name' => 'System Management',
            'name' => 'System Dealer',
            'email' => 'superadmin@gmail.com',
            'phone' => '+1234567890',
            'is_active' => true,
        ]
    );

    $this->adminUser = User::where('email', 'superadmin@gmail.com')->first();
    if (!$this->adminUser) {
        $this->adminUser = User::create([
            'first_name' => 'System',
            'last_name' => 'Admin',
            'email' => 'superadmin@gmail.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password123'),
            'is_active' => true,
            'is_system_user' => true,
            'current_dealer_id' => $this->systemDealer->id,
        ]);
    } else {
        $this->adminUser->update([
            'password' => bcrypt('password123'),
            'is_active' => true,
            'is_system_user' => true,
            'current_dealer_id' => $this->systemDealer->id,
        ]);
    }

    // Attach to system dealer pivot
    $this->adminUser->dealers()->sync([
        $this->systemDealer->id => ['is_owner' => true],
    ]);

    // Create or fetch super_admin role and assign it to the admin user
    $this->adminRole = Role::updateOrCreate(
        [
            'name' => 'super_admin',
            'guard_name' => 'web',
            'dealer_id' => $this->systemDealer->id,
        ],
        [
            'is_active' => true,
        ]
    );
    
    // Set permissions team before assigning role
    setPermissionsTeamId($this->systemDealer->id);

    if (!$this->adminUser->hasRole($this->adminRole)) {
        $this->adminUser->assignRole($this->adminRole);
    }

    // 2. Create or fetch dealer and a dealer user
    $this->dealer = Dealer::updateOrCreate(
        ['slug' => 'dealer-one'],
        [
            'company_name' => 'Dealer One LLC',
            'name' => 'Dealer One',
            'email' => 'dealer@gmail.com',
            'phone' => '+1987654321',
            'is_active' => true,
        ]
    );

    $this->dealerUser = User::where('email', 'dealer@gmail.com')->first();
    if (!$this->dealerUser) {
        $this->dealerUser = User::create([
            'first_name' => 'Dealer',
            'last_name' => 'Manager',
            'email' => 'dealer@gmail.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password123'),
            'is_active' => true,
            'is_system_user' => false,
            'current_dealer_id' => $this->dealer->id,
        ]);
    } else {
        $this->dealerUser->update([
            'password' => bcrypt('password123'),
            'is_active' => true,
            'is_system_user' => false,
            'current_dealer_id' => $this->dealer->id,
        ]);
    }

    $this->dealerUser->dealers()->sync([
        $this->dealer->id => ['is_owner' => true],
    ]);

    $this->dealerRole = Role::updateOrCreate(
        [
            'name' => 'dealer_owner',
            'guard_name' => 'web',
            'dealer_id' => $this->dealer->id,
        ],
        [
            'is_active' => true,
        ]
    );
    
    // Set permissions team before assigning role
    setPermissionsTeamId($this->dealer->id);

    if (!$this->dealerUser->hasRole($this->dealerRole)) {
        $this->dealerUser->assignRole($this->dealerRole);
    }

    // Set default setting for the tests back to system dealer context
    setPermissionsTeamId($this->systemDealer->id);

    // Set up default settings row
    $this->setting = AdminSetting::create([
        'key' => 'restricted_login_enabled',
        'value' => '0',
    ]);
});

test('admin can log in from any domain when restriction is disabled', function () {
    $this->setting->update(['value' => '0']);

    $response = $this->post('/login', [
        'email' => 'superadmin@gmail.com',
        'password' => 'password123',
    ]);

    $response->assertRedirect('/admin/dealers'); // redirectToDashboard redirects system user here
    $this->assertAuthenticatedAs($this->adminUser);
});

test('admin login fails from unauthorized domain when restriction is enabled', function () {
    $this->setting->update(['value' => '1']);

    // Attempt login from untrusted domain
    $response = $this->post('http://untrusted.com/login', [
        'email' => 'superadmin@gmail.com',
        'password' => 'password123',
    ]);

    $response->assertRedirect();
    $response->assertSessionHasErrors(['email' => 'Login not allowed from this domain.']);
    $this->assertGuest();
});

test('admin login succeeds from authorized domain when restriction is enabled', function () {
    $this->setting->update(['value' => '1']);
    AdminRestrictedSite::create(['domain' => 'allowed.com']);

    // Attempt login from allowed domain
    $response = $this->post('http://allowed.com/login', [
        'email' => 'superadmin@gmail.com',
        'password' => 'password123',
    ]);

    $response->assertRedirect('http://allowed.com/admin/dealers');
    $this->assertAuthenticatedAs($this->adminUser);
});

test('dealer user is not affected by domain restriction setting', function () {
    $this->setting->update(['value' => '1']);
    // No domains added in allowed domains

    // Attempt login from untrusted domain
    $response = $this->post('http://untrusted.com/login', [
        'email' => 'dealer@gmail.com',
        'password' => 'password123',
    ]);

    $response->assertRedirect('http://untrusted.com/dealer/website/dashboard');
    $this->assertAuthenticatedAs($this->dealerUser);
});

test('super admin settings page requires authentication', function () {
    $response = $this->get('/admin/restricted-sites');
    $response->assertRedirect('/login');
});

test('super admin settings page can be accessed by authenticated admin', function () {
    $response = $this->actingAs($this->adminUser)->get('/admin/restricted-sites');
    $response->assertStatus(200);
    $response->assertSee('Admin Restricted Sites');
    $response->assertSee('Login Domain Restriction Toggle');
});

test('super admin can toggle the restriction setting', function () {
    $response = $this->actingAs($this->adminUser)->post('/admin/restricted-sites/setting', [
        'restricted_login_enabled' => '1',
    ]);

    $response->assertRedirect();
    $this->assertEquals('1', AdminSetting::where('key', 'restricted_login_enabled')->value('value'));
});

test('super admin can add an authorized domain with validation', function () {
    // 1. Valid domain
    $response = $this->actingAs($this->adminUser)->post('/admin/restricted-sites', [
        'domain' => 'my-admin-domain.com',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('admin_restricted_sites', ['domain' => 'my-admin-domain.com']);

    // 2. Duplicate domain validation
    $response = $this->actingAs($this->adminUser)->post('/admin/restricted-sites', [
        'domain' => 'my-admin-domain.com',
    ]);
    $response->assertSessionHasErrors(['domain']);

    // 3. Invalid hostname format validation
    $response = $this->actingAs($this->adminUser)->post('/admin/restricted-sites', [
        'domain' => 'invalid_domain!',
    ]);
    $response->assertSessionHasErrors(['domain']);
});

test('super admin can delete an authorized domain', function () {
    $site = AdminRestrictedSite::create(['domain' => 'delete-me.com']);

    $response = $this->actingAs($this->adminUser)->delete("/admin/restricted-sites/{$site->id}");

    $response->assertRedirect();
    $this->assertDatabaseMissing('admin_restricted_sites', ['id' => $site->id]);
});
