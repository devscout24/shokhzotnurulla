<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dealership\Dealer;
use App\Enums\DealerStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DealerController extends Controller
{
    public function index()
    {
        $dealers = Dealer::latest()->paginate(15);
        return view('admin.pages.dealers.index', compact('dealers'));
    }

    public function create()
    {
        return view('admin.pages.dealers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:50',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:16',
            'domains' => 'required|array|min:1',
            'domains.*' => 'required|string|unique:domains,domain',
        ]);

        $dealerData = [
            'name' => $validated['company_name'],
            'company_name' => $validated['company_name'],
            'slug' => Str::slug($validated['company_name']),
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'status' => DealerStatus::ACTIVE,
            'is_active' => true,
            'domain' => $validated['domains'][0], // Set the first one as the primary on dealer table too
        ];

        $dealer = Dealer::create($dealerData);

        foreach ($validated['domains'] as $index => $domainName) {
            $dealer->domains()->create([
                'domain' => $domainName,
                'is_primary' => $index === 0,
            ]);
        }

        return redirect()->route('admin.dealers.index')->with('success', 'Dealer created successfully.');
    }

    public function edit(Dealer $dealer)
    {
        return view('admin.pages.dealers.edit', compact('dealer'));
    }

    public function update(Request $request, Dealer $dealer)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:50',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:16',
            'status' => 'required|string',
            'domains' => 'required|array|min:1',
            'domains.*' => 'required|string|unique:domains,domain,' . $dealer->id . ',dealer_id',
        ]);

        $dealer->update([
            'name' => $validated['company_name'],
            'company_name' => $validated['company_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'status' => $validated['status'],
            'is_active' => $validated['status'] === DealerStatus::ACTIVE->value,
            'domain' => $validated['domains'][0],
        ]);

        // Sync domains
        $dealer->domains()->delete();
        foreach ($validated['domains'] as $index => $domainName) {
            $dealer->domains()->create([
                'domain' => $domainName,
                'is_primary' => $index === 0,
            ]);
        }

        return redirect()->route('admin.dealers.index')->with('success', 'Dealer updated successfully.');
    }

    public function toggleStatus(Dealer $dealer)
    {
        $dealer->status = $dealer->status === DealerStatus::ACTIVE ? DealerStatus::INACTIVE : DealerStatus::ACTIVE;
        $dealer->is_active = $dealer->status === DealerStatus::ACTIVE;
        $dealer->save();

        return back()->with('success', 'Dealer status updated.');
    }

    public function destroy(Dealer $dealer)
    {
        $dealer->delete();
        return redirect()->route('admin.dealers.index')->with('success', 'Dealer deleted.');
    }
}
