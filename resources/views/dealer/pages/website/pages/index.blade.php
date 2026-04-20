@extends('layouts.dealer.app')

@section('title', __('Pages') . ' | ' . __(config('app.name')))



@section('page-content')
<main class="main-content" id="mainContent">
    <div class="page-header" style="display:flex;align-items:center;justify-content:space-between;">
        <h2 class="view-title">{{ __('Pages') }}</h2>
        <a href="{{ $routes['create'] }}" class="btn btn-primary" style="background:#c0392b;color:#fff;border:none;border-radius:6px;padding:9px 20px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
            <i class="fas fa-plus"></i>{{ __('Create Page') }}
        </a>
    </div>

    <div class="view-content">
        @if ($pages->count())
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Title') }}</th>
                        <th>{{ __('Slug') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Tags') }}</th>
                        <th>{{ __('Published') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pages as $page)
                        <tr>
                            <td>
                                <strong>{{ $page->title }}</strong>
                            </td>
                            <td>
                                <code>{{ $page->slug }}</code>
                            </td>
                            <td>
                                <span class="badge" style="background:{{ $page->is_active ? '#27ae60' : '#e74c3c' }};color:#fff;">
                                    {{ $page->getStatusLabel() }}
                                </span>
                            </td>
                            <td>
                                @if ($page->tags && count($page->tags) > 0)
                                    <div>
                                        @foreach ($page->tags as $tag)
                                            <span class="badge" style="background:#3498db;color:#fff;">{{ $tag }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <span style="color:#999;">-</span>
                                @endif
                            </td>
                            <td>
                                {{ $page->published_at ? $page->published_at->format('M d, Y') : '-' }}
                            </td>
                            <td class="text-end">
                                <a href="{{ str_replace('__ID__', $page->id, $routes['edit']) }}" class="btn-icon" title="{{ __('Edit') }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn-icon" onclick="deletePage({{ $page->id }})" title="{{ __('Delete') }}" style="color:#e74c3c;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination">
                {{ $pages->links() }}
            </div>
        @else
            <div class="alert" style="background:#d5dbdb;color:#34495e;padding:15px;border-radius:6px;margin-top:20px;">
                <i class="fas fa-info-circle"></i>
                {{ __('No pages found. Create your first page to get started.') }}
            </div>
        @endif
    </div>
</main>

<!-- Delete Modal -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function deletePage(pageId) {
    if (confirm('{{ __('Are you sure you want to delete this page?') }}')) {
        const form = document.getElementById('deleteForm');
        form.action = '{{ str_replace("__ID__", "", $routes["destroy"]) }}' + pageId;
        form.submit();
    }
}
</script>
@endsection
