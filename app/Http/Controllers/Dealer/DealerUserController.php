<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\Dealer\StaffInvitationMail;
use Illuminate\Support\Str;

class DealerUserController extends Controller
{
    public function index()
    {
        $dealer = auth()->user()->currentDealer;
        
        // Ensure requested roles exist for this dealer
        $this->ensureRolesExist($dealer);

        // Scope users to current dealer only and exclude system users
        $users = $dealer->users()
            ->where('is_system_user', false)
            ->with(['roles' => function($query) use ($dealer) {
                $query->where('roles.dealer_id', $dealer->id);
            }])
            ->get();
        
        // Load all available timezones
        $timezones = \DateTimeZone::listIdentifiers();

        return view('dealer.pages.users.index', compact('users', 'timezones'));
    }

    public function update(Request $request, User $user)
    {
        $dealer = auth()->user()->currentDealer;
        
        // Security check: user must belong to this dealer
        if ($user->current_dealer_id !== $dealer->id) {
            abort(403);
        }

        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'timezone' => 'required|string',
            'roles' => 'required|array|min:1',
        ]);

        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'timezone' => $request->timezone,
        ]);

        // Sync roles
        setPermissionsTeamId($dealer->id);
        $user->syncRoles($request->roles);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully.'
        ]);
    }

    private function ensureRolesExist($dealer)
    {
        $roles = ['View only', 'Content editor', 'Billing', 'Administrator'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate([
                'name' => $roleName,
                'dealer_id' => $dealer->id,
                'guard_name' => 'web'
            ], [
                'is_active' => true
            ]);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'timezone' => 'required|string',
            'roles' => 'required|array|min:1',
            'password' => 'required|string|min:8',
        ]);

        $dealer = auth()->user()->currentDealer;

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'timezone' => $request->timezone,
            'password' => Hash::make($request->password),
            'current_dealer_id' => $dealer->id,
            'is_active' => true,
            'email_verified_at' => now(), // Auto-verify staff users
        ]);

        // Attach to dealer
        $dealer->users()->attach($user->id, ['is_owner' => false]);

        // Assign roles
        setPermissionsTeamId($dealer->id);
        $user->assignRole($request->roles);

        // Send Email (Queued)
        Mail::to($user->email)->queue(new StaffInvitationMail($user, $request->password, $dealer->name));

        return response()->json([
            'success' => true,
            'message' => 'User added successfully and invitation sent.'
        ]);
    }
    
    public function destroy(User $user)
    {
        // Safety check
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }

        $dealer = auth()->user()->currentDealer;
        
        // Check if user belongs to this dealer
        if ($user->current_dealer_id !== $dealer->id) {
            abort(403);
        }

        $user->delete();

        return back()->with('success', 'User removed successfully.');
    }
}
