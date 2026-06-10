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
        background: purple;
        color: #fff;
        border: none;
    }
    .btn-primary-scout:hover { background: purple; color: #fff; }

    .header-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Search Bar Design */
    .search-container {
        position: relative;
        display: inline-flex;
        align-items: center;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding-left: 10px;
        transition: border-color 0.2s, box-shadow 0.2s;
        width: 220px;
        height: 33px;
        box-sizing: border-box;
    }
    .search-container:focus-within {
        border-color: purple;
        box-shadow: 0 0 0 2px rgba(128, 0, 128, 0.15);
    }
    .search-icon {
        color: #888;
        font-size: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .search-input {
        border: none;
        outline: none;
        padding: 4px 8px 4px 6px;
        font-size: 12.5px;
        width: 100%;
        background: transparent;
        color: #333;
        height: 100%;
    }
</style>
@endpush

@section('page-content')
<main class="main-content" id="mainContent">
    <div class="page-header header-flex">
        <h2 class="view-title">Dealer Management</h2>
        <div style="display: flex; gap: 10px; align-items: center;">
            <div class="search-container">
                <i class="bi bi-search search-icon"></i>
                <input type="text" placeholder="Search dealer..." id="searchInput" class="search-input">
            </div>

            <div class="dropdown">
                <button class="btn-action dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="background: #fff; border: 1px solid #ddd; cursor: pointer;">
                    <i class="bi bi-download"></i> Export
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                    <li><a class="dropdown-item" href="{{ route('admin.dealers.export.csv') }}"><i class="bi bi-filetype-csv"></i> Export as CSV</a></li>
                </ul>
            </div>
            <a href="{{ route('admin.dealers.create') }}" class="btn-action btn-primary-scout">
                <i class="bi bi-plus-lg"></i> Add New Dealer
            </a>
        </div>
    </div>

    <hr>

    <div class="view-content">
        @if(session('success'))
            <div class="alert alert-success mb-4" style="padding: 12px 16px; border-radius: 6px; background: #e6f4ea; color: #1e8e3e; border: 1px solid #ceead6; font-size: 13px;">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger mb-4" style="padding: 12px 16px; border-radius: 6px; background: #fce8e6; color: #d93025; border: 1px solid #f5c6cb; font-size: 13px;">
                {{ session('error') }}
            </div>
        @endif

        <table class="dealer-table">
            <thead>
                <tr>
                    <th>Dealer Name</th>
                    <th>Domain</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Export Data</th>
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
                            <div class="dropdown">
                                <button class="btn-action" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="background: #fff; border: 1px solid #ddd; cursor: pointer;">
                                    <i class="bi bi-download"></i> Data <i class="bi bi-chevron-down" style="font-size: 10px; margin-left: 4px;"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('admin.dealers.export.vehicles', $dealer) }}"><i class="bi bi-car-front"></i> Vehicles (CSV)</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.dealers.export.vehicles.carsforsale', $dealer) }}"><i class="bi bi-car-front"></i> Carsforsale (CSV)</a></li>
                                </ul>
                            </div>
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

                                <form action="{{ route('admin.dealers.destroy', $dealer) }}" method="POST" data-swal-confirm="Are you sure you want to delete this dealer?" data-swal-title="Are you sure?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action text-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>

                                @php
                                    $user = $dealer->owners()->first() ?? $dealer->users()->first();
                                    $isVerified = $user ? $user->email_verified_at !== null : false;
                                @endphp
                                <form action="{{ route('admin.dealers.notify', $dealer) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-action" {{ $isVerified ? 'disabled' : '' }} style="{{ $isVerified ? 'opacity: 0.5; cursor: not-allowed;' : '' }}">
                                        <i class="bi bi-envelope"></i> {{ $isVerified ? 'Setup Complete' : 'Send Setup Link' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: #999;">
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

@push('page-scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const tableRows = document.querySelectorAll('.dealer-table tbody tr');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            
            tableRows.forEach(row => {
                // Skip empty state row if present
                if (row.cells.length < 3) return;
                
                const nameEl = row.querySelector('td:nth-child(1)');
                const domainEl = row.querySelector('td:nth-child(2)');
                const emailEl = row.querySelector('td:nth-child(3)');
                
                if (!nameEl) return;
                
                const nameText = nameEl.textContent.toLowerCase();
                const domainText = domainEl ? domainEl.textContent.toLowerCase() : '';
                const emailText = emailEl ? emailEl.textContent.toLowerCase() : '';
                
                if (nameText.includes(query) || domainText.includes(query) || emailText.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
});
</script>
@endpush
@endsection
