<?php

use App\Models\AdminSetting;
use App\Models\User;
use App\Models\Dealership\Dealer;
use App\Models\Role;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Session;

uses(DatabaseTransactions::class);

beforeEach(function () {
    // Create or fetch system dealer and admin user
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
            'email_verified_at' => now(),
            'password' => bcrypt('password123'),
            'is_active' => true,
            'is_system_user' => true,
            'current_dealer_id' => $this->systemDealer->id,
            'google2fa_secret' => null,
            'is_2fa_required' => false,
        ]);
    }

    $this->adminUser->dealers()->sync([
        $this->systemDealer->id => ['is_owner' => true],
    ]);

    $this->adminRole = Role::updateOrCreate(
        [
            'name' => 'super_admin',
            'guard_name' => 'web',
            'dealer_id' => $this->systemDealer->id,
        ],
        ['is_active' => true]
    );

    setPermissionsTeamId($this->systemDealer->id);

    if (!$this->adminUser->hasRole($this->adminRole)) {
        $this->adminUser->assignRole($this->adminRole);
    }
});

test('admin with 2FA enabled is redirected to 2FA verify page', function () {
    $google2fa = app('pragmarx.google2fa');
    $secret = $google2fa->generateSecretKey();

    $this->adminUser->update([
        'google2fa_secret' => $secret,
        'is_2fa_required' => true,
    ]);

    $response = $this->actingAs($this->adminUser)->get('/admin/dashboard');

    $response->assertRedirect(route('admin.2fa.verify'));
});

test('admin with 2FA enabled can access 2FA verify page', function () {
    $google2fa = app('pragmarx.google2fa');
    $secret = $google2fa->generateSecretKey();

    $this->adminUser->update([
        'google2fa_secret' => $secret,
        'is_2fa_required' => true,
    ]);

    $response = $this->actingAs($this->adminUser)->get('/admin/2fa/verify');

    $response->assertStatus(200);
    $response->assertSee('Two-Factor Authentication');
});

test('admin submitting correct 2FA code passes verification', function () {
    $google2fa = app('pragmarx.google2fa');
    $secret = $google2fa->generateSecretKey();

    $this->adminUser->update([
        'google2fa_secret' => $secret,
        'is_2fa_required' => true,
    ]);

    $validOtp = $google2fa->getCurrentOtp($secret);

    $response = $this->actingAs($this->adminUser)->post('/admin/2fa/verify', [
        'one_time_password' => $validOtp,
    ]);

    $response->assertRedirect(route('admin.dealers.index'));
});

test('admin submitting wrong 2FA code fails verification', function () {
    $google2fa = app('pragmarx.google2fa');
    $secret = $google2fa->generateSecretKey();

    $this->adminUser->update([
        'google2fa_secret' => $secret,
        'is_2fa_required' => true,
    ]);

    $response = $this->actingAs($this->adminUser)->post('/admin/2fa/verify', [
        'one_time_password' => '000000',
    ]);

    $response->assertRedirect();
    $response->assertSessionHasErrors(['one_time_password']);
});

test('admin without 2FA secret but with 2fa required is redirected to profile setup', function () {
    $this->adminUser->update([
        'google2fa_secret' => null,
        'is_2fa_required' => true,
    ]);

    $response = $this->actingAs($this->adminUser)->get('/admin/dashboard');

    $response->assertRedirect(route('admin.profile.edit'));
});

test('admin profile page shows 2FA setup section', function () {
    $this->adminUser->update([
        'google2fa_secret' => null,
        'is_2fa_required' => false,
    ]);

    $response = $this->actingAs($this->adminUser)->get('/admin/profile');

    $response->assertStatus(200);
    $response->assertSee('Two-Factor Authentication (2FA)');
    $response->assertSee('Verify &amp; Enable 2FA');
});

test('admin can enable 2FA with a valid code', function () {
    $google2fa = app('pragmarx.google2fa');
    $secret = $google2fa->generateSecretKey();

    // Simulate setup session
    $this->actingAs($this->adminUser)
         ->withSession(['google2fa_secret_setup' => $secret]);

    $validOtp = $google2fa->getCurrentOtp($secret);

    $response = $this->actingAs($this->adminUser)
        ->withSession(['google2fa_secret_setup' => $secret])
        ->post('/admin/2fa/enable', [
            'one_time_password' => $validOtp,
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->adminUser->refresh();
    $this->assertEquals($secret, $this->adminUser->google2fa_secret);
    $this->assertTrue((bool) $this->adminUser->is_2fa_required);
});

test('admin can disable 2FA', function () {
    $google2fa = app('pragmarx.google2fa');
    $secret = $google2fa->generateSecretKey();

    $this->adminUser->update([
        'google2fa_secret' => $secret,
        'is_2fa_required' => true,
    ]);

    $response = $this->actingAs($this->adminUser)
        ->withSession(['2fa_verified' => true])
        ->post('/admin/2fa/disable');

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->adminUser->refresh();
    $this->assertNull($this->adminUser->google2fa_secret);
    $this->assertFalse((bool) $this->adminUser->is_2fa_required);
});

test('admin without 2FA requirement can access admin pages normally', function () {
    $this->adminUser->update([
        'google2fa_secret' => null,
        'is_2fa_required' => false,
    ]);

    $response = $this->actingAs($this->adminUser)->get('/admin/dealers');

    $response->assertStatus(200);
});
