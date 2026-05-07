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
            'domain' => 'required|string|unique:dealers,domain',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:16',
        ]);

        $validated['slug'] = Str::slug($validated['company_name']);
        $validated['status'] = DealerStatus::ACTIVE;
        $validated['is_active'] = true;

        Dealer::create($validated);

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
            'domain' => 'required|string|unique:dealers,domain,' . $dealer->id,
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:16',
            'status' => 'required|string',
        ]);

        $dealer->update($validated);
        
        // Sync is_active for backward compatibility if needed
        $dealer->is_active = $dealer->status === DealerStatus::ACTIVE;
        $dealer->save();

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
