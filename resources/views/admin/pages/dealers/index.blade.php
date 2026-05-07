@extends('layouts.admin.app')

@section('title', __('Dealers') . ' | '. __(config('app.name')))

@push('page-styles')
<style>
    .dealer-table {
        width: 100%;
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        border-collapse: separate;
        border-spacing: 0;
        overflow: hidden;
        margin-top: 20px;
    }
    .dealer-table th {
        background: #f8f9fa;
        padding: 12px 16px;
        text-align: left;
        font-size: 13px;
        font-weight: 600;
        color: #666;
        border-bottom: 1px solid #eee;
    }
    .dealer-table td {
        padding: 14px 16px;
        font-size: 13px;
        color: #444;
        border-bottom: 1px solid #eee;
    }
    .dealer-table tr:last-child td {
        border-bottom: none;
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }
    .status-active { background: #e6f4ea; color: #1e8e3e; }
    .status-inactive { background: #fce8e6; color: #d93025; }
    
    .btn-action {
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 12px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid #ddd;
        color: #555;
        background: #fff;
        transition: all 0.2s;
    }
    .btn-action:hover { background: #f8f9fa; border-color: #ccc; }
    
    .btn-primary-scout {
        background: #c0392b;
        color: #fff;
        border: none;
    }
    .btn-primary-scout:hover { background: #a93226; color: #fff; }

    .header-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
</style>
@endpush

@section('page-content')
<main class="main-content" id="mainContent">
    <div class="page-header header-flex">
        <h2 class="view-title">Dealer Management</h2>
        <a href="{{ route('admin.dealers.create') }}" class="btn-action btn-primary-scout">
            <i class="bi bi-plus-lg"></i> Add New Dealer
        </a>
    </div>

    <hr>

    <div class="view-content">
        @if(session('success'))
            <div class="alert alert-success mb-4" style="padding: 12px 16px; border-radius: 6px; background: #e6f4ea; color: #1e8e3e; border: 1px solid #ceead6; font-size: 13px;">
                {{ session('success') }}
            </div>
        @endif

        <table class="dealer-table">
            <thead>
                <tr>
                    <th>Dealer Name</th>
                    <th>Domain</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dealers as $dealer)
                    <tr>
                        <td>
                            <div style="font-weight: 600; color: #222;">{{ $dealer->company_name }}</div>
                            <div style="font-size: 11px; color: #888;">Slug: {{ $dealer->slug }}</div>
                        </td>
                        <td>
                            <code style="background: #f1f3f4; padding: 2px 6px; border-radius: 4px; font-family: monospace;">{{ $dealer->domain }}</code>
                        </td>
                        <td>{{ $dealer->email ?? 'N/A' }}</td>
                        <td>
                            <span class="status-badge status-{{ $dealer->status->value }}">
                                {{ $dealer->status->label() }}
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <a href="{{ route('admin.dealers.edit', $dealer) }}" class="btn-action">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>

                                <a href="{{ route('admin.dealers.integrations.index', $dealer) }}" class="btn-action">
                                    <i class="bi bi-plug"></i> Integrations
                                </a>
                                
                                <form action="{{ route('admin.dealers.toggle-status', $dealer) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn-action">
                                        <i class="bi bi-arrow-repeat"></i> 
                                        {{ $dealer->status === \App\Enums\DealerStatus::ACTIVE ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>

                                <form action="{{ route('admin.dealers.destroy', $dealer) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action text-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px; color: #999;">
                            No dealers found. <a href="{{ route('admin.dealers.create') }}">Create one now</a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            {{ $dealers->links() }}
        </div>
    </div>
</main>
@endsection
