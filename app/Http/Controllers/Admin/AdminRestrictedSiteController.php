<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminRestrictedSite;
use App\Models\AdminSetting;
use Illuminate\Http\Request;

class AdminRestrictedSiteController extends Controller
{
    /**
     * Display a listing of the restricted sites and settings.
     */
    public function index()
    {
        $setting = AdminSetting::firstOrCreate(
            ['key' => 'restricted_login_enabled'],
            ['value' => '0']
        );
        $sites = AdminRestrictedSite::orderBy('domain')->get();

        return view('admin.pages.restricted-sites', compact('setting', 'sites'));
    }

    /**
     * Update the restricted login toggle setting.
     */
    public function updateSetting(Request $request)
    {
        $request->validate([
            'restricted_login_enabled' => 'required|in:0,1',
        ]);

        AdminSetting::updateOrCreate(
            ['key' => 'restricted_login_enabled'],
            ['value' => $request->input('restricted_login_enabled')]
        );

        return redirect()->back()->with('success', 'Restricted login setting updated successfully.');
    }

    /**
     * Store a newly created restricted domain.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'domain' => [
                'required',
                'string',
                'unique:admin_restricted_sites,domain',
                'regex:/^(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z0-9-]{2,}|localhost$/i',
            ],
        ], [
            'domain.regex' => 'The domain must be a valid hostname format.',
        ]);

        AdminRestrictedSite::create([
            'domain' => strtolower($validated['domain']),
        ]);

        return redirect()->back()->with('success', 'Domain added successfully.');
    }

    /**
     * Remove the specified restricted domain.
     */
    public function destroy($id)
    {
        $site = AdminRestrictedSite::findOrFail($id);
        $site->delete();

        return redirect()->back()->with('success', 'Domain deleted successfully.');
    }
}
