<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\Website\StaffMember;
use App\Models\Website\StaffMemberCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WebsiteStaffMemberController extends Controller
{
    public function index(): View
    {
        $categories = StaffMemberCategory::orderBy('sort_order')->withCount('staffMembers')->get();
        $members = StaffMember::with('category')->orderBy('sort_order', 'desc')->get();

        return view('dealer.pages.website.staff-members.index', compact('members', 'categories'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'full_name'                => 'required|string|max:255',
            'job_title'                => 'required|string|max:255',
            'staff_member_category_id' => 'required|integer|exists:staff_member_categories,id',
            'photo_url'                => 'nullable|string|max:255',
            'email_address'            => 'nullable|string|max:255',
            'phone_number'             => 'nullable|string|max:255',
            'short_bio'                => 'nullable|string',
            'status'                   => 'required|string',
        ]);

        $validated['author'] = Auth::user()->name;
        $validated['sort_order'] = StaffMember::max('sort_order') + 1;

        $member = StaffMember::create($validated);
        $member->load('category');

        return response()->json($member);
    }

    public function update(Request $request, StaffMember $staffMember): JsonResponse
    {
        $validated = $request->validate([
            'full_name'                => 'required|string|max:255',
            'job_title'                => 'required|string|max:255',
            'staff_member_category_id' => 'required|integer|exists:staff_member_categories,id',
            'photo_url'                => 'nullable|string|max:255',
            'email_address'            => 'nullable|string|max:255',
            'phone_number'             => 'nullable|string|max:255',
            'short_bio'                => 'nullable|string',
            'status'                   => 'required|string',
        ]);

        $staffMember->update($validated);
        $staffMember->load('category');

        return response()->json($staffMember);
    }

    public function destroy(StaffMember $staffMember): JsonResponse
    {
        $staffMember->delete();
        return response()->json(['success' => true]);
    }

    public function bulkUpdate(Request $request): JsonResponse
    {
        $data = $request->input('members', []);
        $author = Auth::user()->name;

        foreach ($data as $index => $item) {
            $id = $item['id'] ?? null;

            if (!empty($item['is_deleted']) && $id) {
                StaffMember::where('id', $id)->delete();
                continue;
            }

            $payload = [
                'full_name'                => $item['full_name'] ?? '',
                'job_title'                => $item['job_title'] ?? '',
                'staff_member_category_id' => $item['staff_member_category_id'] ?? null,
                'photo_url'                => $item['photo_url'] ?? null,
                'email_address'            => $item['email_address'] ?? null,
                'phone_number'             => $item['phone_number'] ?? null,
                'short_bio'                => $item['short_bio'] ?? null,
                'status'                   => $item['status'] ?? 'Published',
                'sort_order'               => $index,
            ];

            if ($id) {
                StaffMember::where('id', $id)->update($payload);
            } else {
                $payload['author'] = $author;
                StaffMember::create($payload);
            }
        }

        $all = StaffMember::with('category')->orderBy('sort_order')->get();
        return response()->json($all);
    }

    public function storeCategory(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = StaffMemberCategory::create([
            'name'       => $validated['name'],
            'sort_order' => StaffMemberCategory::max('sort_order') + 1,
        ]);

        $category->loadCount('staffMembers');
        return response()->json($category);
    }

    public function updateCategory(Request $request, StaffMemberCategory $staffMemberCategory): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $staffMemberCategory->update($validated);
        $staffMemberCategory->loadCount('staffMembers');
        return response()->json($staffMemberCategory);
    }

    public function destroyCategory(StaffMemberCategory $staffMemberCategory): JsonResponse
    {
        StaffMember::where('staff_member_category_id', $staffMemberCategory->id)
            ->update(['staff_member_category_id' => null]);

        $staffMemberCategory->delete();
        return response()->json(['success' => true]);
    }
}
