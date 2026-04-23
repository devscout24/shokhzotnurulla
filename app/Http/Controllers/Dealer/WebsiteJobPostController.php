<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\Website\JobPost;
use App\Models\Website\JobPostCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WebsiteJobPostController extends Controller
{
    public function index(): View
    {
        $categories = JobPostCategory::orderBy('sort_order')->withCount('jobPosts')->get();
        $jobs = JobPost::with('category')->orderBy('sort_order', 'desc')->get();

        return view('dealer.pages.website.job-posts.index', compact('jobs', 'categories'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'job_title'            => 'required|string|max:255',
            'job_post_category_id' => 'required|integer|exists:job_post_categories,id',
            'job_description'      => 'required|string',
            'status'               => 'required|string',
        ]);

        $validated['author'] = Auth::user()->name;
        $validated['sort_order'] = JobPost::max('sort_order') + 1;

        $job = JobPost::create($validated);
        $job->load('category');

        return response()->json($job);
    }

    public function update(Request $request, JobPost $jobPost): JsonResponse
    {
        $validated = $request->validate([
            'job_title'            => 'required|string|max:255',
            'job_post_category_id' => 'required|integer|exists:job_post_categories,id',
            'job_description'      => 'required|string',
            'status'               => 'required|string',
        ]);

        $jobPost->update($validated);
        $jobPost->load('category');

        return response()->json($jobPost);
    }

    public function destroy(JobPost $jobPost): JsonResponse
    {
        $jobPost->delete();
        return response()->json(['success' => true]);
    }

    public function bulkUpdate(Request $request): JsonResponse
    {
        $data = $request->input('jobs', []);
        $author = Auth::user()->name;

        foreach ($data as $index => $item) {
            $id = $item['id'] ?? null;

            if (!empty($item['is_deleted']) && $id) {
                JobPost::where('id', $id)->delete();
                continue;
            }

            $payload = [
                'job_title'            => $item['job_title'] ?? '',
                'job_post_category_id' => $item['job_post_category_id'] ?? null,
                'job_description'      => $item['job_description'] ?? '',
                'status'               => $item['status'] ?? 'Published',
                'sort_order'           => $index,
            ];

            if ($id) {
                JobPost::where('id', $id)->update($payload);
            } else {
                $payload['author'] = $author;
                JobPost::create($payload);
            }
        }

        $all = JobPost::with('category')->orderBy('sort_order')->get();
        return response()->json($all);
    }

    public function storeCategory(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = JobPostCategory::create([
            'name'       => $validated['name'],
            'sort_order' => JobPostCategory::max('sort_order') + 1,
        ]);

        $category->loadCount('jobPosts');
        return response()->json($category);
    }

    public function updateCategory(Request $request, JobPostCategory $jobPostCategory): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $jobPostCategory->update($validated);
        $jobPostCategory->loadCount('jobPosts');
        return response()->json($jobPostCategory);
    }

    public function destroyCategory(JobPostCategory $jobPostCategory): JsonResponse
    {
        JobPost::where('job_post_category_id', $jobPostCategory->id)
            ->update(['job_post_category_id' => null]);

        $jobPostCategory->delete();
        return response()->json(['success' => true]);
    }
}
