@extends('layouts.dealer.app')

@section('title', __('Redirects Settings') . ' | '. __(config('app.name')))

@push('page-assets')
    @vite([
        'resources/css/dealer/pages/website-settings-redirects.css',
        // 'resources/js/dealer/pages/website-settings-redirects.js',
    ])
@endpush

@section('page-content')
    <main class="main-content" id="mainContent">
        <div class="page-header">
            <h2 class="view-title">Website Settings</h2>
        </div>

        @if (session('import_success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {!! session('import_success') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('import_warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                {!! session('import_warning') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('import_error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {!! session('import_error') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="view-content" data-view="redirects">
            <div class="ws-layout">

                {{-- ── LEFT SIDEBAR ── --}}
                <aside class="ws-sidebar">
                    <a href="/dealer/website/settings" class="menu-item">
                        <span class="ws-icon"><i class="bi bi-info-circle"></i></span>
                        <span>General</span>
                    </a>
                    <a href="/dealer/website/settings/locations" class="menu-item">
                        <span class="ws-icon"><i class="bi bi-geo-alt"></i></span>
                        <span>Locations &amp; Hours</span>
                    </a>
                    <a href="/dealer/website/settings/banners" class="menu-item">
                        <span class="ws-icon"><i class="bi bi-card-image"></i></span>
                        <span>Banners /<br>Announcements</span>
                    </a>
                    <a href="/dealer/website/settings/finance" class="menu-item">
                        <span class="ws-icon"><i class="bi bi-percent"></i></span>
                        <span>Interest Rates</span>
                    </a>
                    <a href="/dealer/website/settings/retail" class="menu-item">
                        <span class="ws-icon"><i class="bi bi-grid"></i></span>
                        <span>Digital Retail</span>
                    </a>
                    {{-- <a href="/dealer/website/settings/domains" class="menu-item">
                        <span class="ws-icon"><i class="bi bi-globe"></i></span>
                        <span>Domains</span>
                    </a> --}}
                    <a href="/dealer/website/settings/redirects" class="menu-item active">
                        <span class="ws-icon"><i class="bi bi-arrow-left-right"></i></span>
                        <span>Redirects</span>
                    </a>
                    <a href="/dealer/website/settings/ips" class="menu-item">
                        <span class="ws-icon"><i class="bi bi-hdd-network"></i></span>
                        <span>Dealer IP Addresses</span>
                    </a>
                </aside>

                {{-- ── RIGHT CONTENT ── --}}
                <div class="rd-content">
                    <div class="rd-card">

                        {{-- Card Header --}}
                        <div class="rd-card-header">
                            <span class="rd-card-title">Redirects</span>
                            <div class="rd-header-actions">
                                <a href="{{ route('dealer.website.settings.redirects.sample') }}" class="rd-btn-upload" style="text-decoration: none;" download>
                                    <i class="bi bi-download"></i> Download Sample CSV
                                </a>
                                <button type="button" class="rd-btn-upload rd-btn-upload-csv">
                                    <i class="bi bi-upload"></i> Upload CSV
                                </button>
                                <button type="button" class="rd-btn-add" id="btnAddRedirect">
                                    + Add Redirect
                                </button>
                            </div>
                        </div>

                        {{-- Table --}}
                        <table class="rd-table">
                            <thead>
                                <tr>
                                    <th>Redirect from</th>
                                    <th>Redirect to</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="redirects-tbody">
                                @forelse($redirects as $redirect)
                                <tr data-id="{{ $redirect->id }}">
                                    <td class="rd-from">{{ $redirect->source_url }}</td>
                                    <td class="rd-to">{{ $redirect->target_url }}</td>
                                    <td class="rd-badge-type">{{ $redirect->status_code == 301 ? 'Permanent' : 'Temporary' }}</td>
                                    <td class="rd-badge-status">{{ $redirect->is_enabled ? 'Enabled' : 'Disabled' }}</td>
                                    <td>
                                        <div class="rd-actions">
                                            <button class="rd-btn-edit btn-open-redirect"
                                                    type="button"
                                                    data-id="{{ $redirect->id }}"
                                                    data-from="{{ $redirect->source_url }}"
                                                    data-to="{{ $redirect->target_url }}"
                                                    data-regex="{{ $redirect->is_regex ? 'Yes' : 'No' }}"
                                                    data-type="{{ $redirect->status_code == 301 ? 'Permanent (301)' : 'Temporary (302)' }}"
                                                    data-status="{{ $redirect->is_enabled ? 'Enabled' : 'Disabled' }}">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </button>
                                            <button class="rd-btn-remove" data-id="{{ $redirect->id }}" type="button">
                                                <i class="bi bi-trash"></i> Remove
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">No redirects yet. Click "Add Redirect" to create one.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                    </div>
                </div>

            </div>
        </div>
    </main>
@endsection

@push('page-modals')
    @include('dealer.modals.website-settings-add-edit-redirect')
@endpush

@push('page-scripts')
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let currentRedirectId = null;

        // Toastr helper
        function showToast(type, message) {
            if (type === 'success') toastr.success(message);
            else toastr.error(message);
        }

        // Modal handling
        const erOverlay = document.getElementById('redirectModal');
        const erTitle   = document.getElementById('erModalTitle');
        const erFrom    = document.getElementById('erFrom');
        const erTo      = document.getElementById('erTo');
        const erRegex   = document.getElementById('erRegex');
        const erType    = document.getElementById('erType');
        const erStatus  = document.getElementById('erStatus');

        function openRedirectModal(data = {}) {
            currentRedirectId = data.id || null;
            erTitle.textContent = data.id ? 'Edit Redirect' : 'Add Redirect';
            erFrom.value = data.from || '';
            erTo.value = data.to || '';

            // Map regex string to select value
            if (data.regex === 'Yes') {
                erRegex.value = '1';
            } else if (data.regex === 'No') {
                erRegex.value = '0';
            } else {
                // If numeric value provided directly (0/1)
                erRegex.value = data.regex === '1' ? '1' : (data.regex === '0' ? '0' : '0');
            }

            // Map type string to select value
            if (data.type === 'Permanent (301)' || data.type === 'Permanent') {
                erType.value = '301';
            } else if (data.type === 'Temporary (302)' || data.type === 'Temporary') {
                erType.value = '302';
            } else {
                // If numeric value provided directly
                erType.value = data.type === '301' ? '301' : (data.type === '302' ? '302' : '301');
            }

            // Map status string to select value
            if (data.status === 'Enabled') {
                erStatus.value = '1';
            } else if (data.status === 'Disabled') {
                erStatus.value = '0';
            } else {
                // If numeric value provided directly
                erStatus.value = data.status === '1' ? '1' : (data.status === '0' ? '0' : '1');
            }

            erOverlay.classList.add('active');
        }

        function closeRedirectModal() {
            erOverlay.classList.remove('active');
            currentRedirectId = null;
        }

        // Add Redirect button
        document.getElementById('btnAddRedirect').addEventListener('click', () => {
            openRedirectModal();
        });

        // Edit buttons (dynamic)
        function bindEditButtons() {
            document.querySelectorAll('.btn-open-redirect').forEach(btn => {
                btn.removeEventListener('click', editHandler);
                btn.addEventListener('click', editHandler);
            });
        }

        function editHandler(e) {
            const btn = e.currentTarget;
            openRedirectModal({
                id:     btn.dataset.id,
                from:   btn.dataset.from,
                to:     btn.dataset.to,
                regex:  btn.dataset.regex,
                type:   btn.dataset.type,
                status: btn.dataset.status,
            });
        }

        // Delete buttons
        function bindDeleteButtons() {
            document.querySelectorAll('.rd-btn-remove').forEach(btn => {
                btn.removeEventListener('click', deleteHandler);
                btn.addEventListener('click', deleteHandler);
            });
        }

        async function deleteHandler(e) {
            const btn = e.currentTarget;
            const id = btn.dataset.id;
            if (!confirm('Are you sure you want to delete this redirect?')) return;

            try {
                const response = await fetch(`/dealer/website/settings/redirects/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                });
                const data = await response.json();
                if (response.ok && data.success) {
                    showToast('success', data.message);
                    // Remove row
                    btn.closest('tr').remove();
                } else {
                    showToast('error', data.message || 'Delete failed.');
                }
            } catch (err) {
                showToast('error', 'An error occurred.');
            }
        }

        // Save redirect (Add/Edit)
        document.getElementById('btnSaveRedirect').addEventListener('click', async () => {
            const formData = {
                source_url:  erFrom.value,
                target_url:  erTo.value,
                is_regex:    erRegex.value,   // '0' or '1'
                status_code: erType.value,    // '301' or '302'
                is_enabled:  erStatus.value,  // '0' or '1'
                _token: csrfToken,
            };

            const url = currentRedirectId
                ? `/dealer/website/settings/redirects/${currentRedirectId}`
                : '{{ route("dealer.website.settings.redirects.store") }}';
            const method = currentRedirectId ? 'PATCH' : 'POST';

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify(formData),
                });

                const data = await response.json();
                if (!response.ok) {
                    // Validation errors or other error
                    if (data.errors) {
                        // Show each error as a separate toast
                        for (const [field, messages] of Object.entries(data.errors)) {
                            messages.forEach(message => {
                                showToast('error', message);
                            });
                        }
                    } else {
                        showToast('error', data.message || 'An error occurred.');
                    }
                    return;
                }

                if (data.success) {
                    {{-- showToast('success', data.message); --}}
                    closeRedirectModal();
                    // Reload page to refresh the table
                    location.reload();
                } else {
                    showToast('error', data.message || 'Failed to save.');
                }
            } catch (err) {
                showToast('error', 'An error occurred. Please try again.');
            }
        });

        // Close modal
        document.getElementById('btnCloseRedirect').addEventListener('click', closeRedirectModal);
        erOverlay.addEventListener('click', e => { if (e.target === erOverlay) closeRedirectModal(); });
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeRedirectModal(); });

        // CSV upload
        const uploadBtn = document.querySelector('.rd-btn-upload-csv');
        const csvInput = document.createElement('input');
        csvInput.type = 'file';
        csvInput.accept = '.csv';
        csvInput.style.display = 'none';
        document.body.appendChild(csvInput);

        uploadBtn.addEventListener('click', () => csvInput.click());

        csvInput.addEventListener('change', async () => {
            if (!csvInput.files.length) return;
            const file = csvInput.files[0];
            const formData = new FormData();
            formData.append('csv_file', file);
            formData.append('_token', csrfToken);

            try {
                const response = await fetch('{{ route("dealer.website.settings.redirects.import") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                    },
                });
                const data = await response.json();
                if (response.ok && data.success) {
                    {{-- showToast('success', data.message); --}}
                    location.reload();
                } else {
                    showToast('error', data.message || 'Import failed.');
                }
            } catch (err) {
                showToast('error', 'An error occurred during import.');
            } finally {
                csvInput.value = ''; // clear input
            }
        });

        // Initial binding
        bindEditButtons();
        bindDeleteButtons();
    </script>
@endpush
