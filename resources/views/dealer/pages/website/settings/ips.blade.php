@extends('layouts.dealer.app')
@section('title', __('Dealer IP Addresses') . ' | '. __(config('app.name')))

@push('page-assets')
    @vite([
        'resources/css/dealer/pages/website-settings-ip-addresses.css',
        // 'resources/js/dealer/pages/website-settings-ip-addresses.js',
    ])
@endpush

@section('page-content')
    <main class="main-content" id="mainContent">
        <div class="page-header">
            <h2 class="view-title">Website Settings</h2>
        </div>

        <div class="view-content" data-view="dealer-ips">
            <div class="ws-layout">

                <aside class="ws-sidebar">
                    <a href="{{ route('dealer.website.settings.general') }}" class="menu-item">
                        <span class="ws-icon"><i class="bi bi-info-circle"></i></span>
                        <span>General</span>
                    </a>
                    <a href="{{ route('dealer.website.settings.locations') }}" class="menu-item">
                        <span class="ws-icon"><i class="bi bi-geo-alt"></i></span>
                        <span>Locations &amp; Hours</span>
                    </a>
                    <a href="{{ route('dealer.website.settings.banners') }}" class="menu-item">
                        <span class="ws-icon"><i class="bi bi-card-image"></i></span>
                        <span>Banners /<br>Announcements</span>
                    </a>
                    <a href="{{ route('dealer.website.settings.finance') }}" class="menu-item">
                        <span class="ws-icon"><i class="bi bi-percent"></i></span>
                        <span>Interest Rates</span>
                    </a>
                    <a href="{{ route('dealer.website.settings.retail') }}" class="menu-item">
                        <span class="ws-icon"><i class="bi bi-grid"></i></span>
                        <span>Digital Retail</span>
                    </a>
                    {{-- <a href="/dealer/website/settings/domains" class="menu-item">
                        <span class="ws-icon"><i class="bi bi-globe"></i></span>
                        <span>Domains</span>
                    </a> --}}
                    <a href="{{ route('dealer.website.settings.redirects') }}" class="menu-item">
                        <span class="ws-icon"><i class="bi bi-arrow-left-right"></i></span>
                        <span>Redirects</span>
                    </a>
                    <a href="{{ route('dealer.website.settings.ips.index') }}" class="menu-item active">
                        <span class="ws-icon"><i class="bi bi-hdd-network"></i></span>
                        <span>Dealer IP<br>Addresses</span>
                    </a>
                </aside>

                <div class="ip-content">

                    <div class="ip-topbar">
                        <p class="ip-topbar-note">
                            New events from the IP addresses listed below will be excluded from reporting.
                        </p>
                        <button type="button" class="ip-btn-add" id="btnAddIp">
                            + Add IP
                        </button>
                    </div>

                    <div class="ip-card">
                        <div class="ip-card-title">Dealer IPs</div>

                        <table class="ip-table">
                            <thead>
                                <tr>
                                    <th>IP</th>
                                    <th>RDNS</th>
                                    <th>Description</th>
                                    <th></th>
                                </thead>
                            <tbody id="ipTableBody">
                                @forelse($dealerIps as $ip)
                                <tr data-id="{{ $ip->id }}">
                                    <td class="ip-addr">{{ $ip->ip_address }}</td>
                                    <td class="ip-rdns">{{ $ip->rdns ?? '—' }}</td>
                                    <td class="ip-desc">{{ $ip->description ?? '—' }}</td>
                                    <td class="ip-actions-col">
                                        <button class="ip-dots-btn" type="button"
                                                data-id="{{ $ip->id }}"
                                                data-ip="{{ $ip->ip_address }}"
                                                data-desc="{{ $ip->description }}">
                                            &bull;&bull;&bull;
                                        </button>
                                        <div class="ip-dropdown">
                                            <button class="ip-dropdown-item ip-edit-btn" type="button">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </button>
                                            <button class="ip-dropdown-item danger ip-remove-btn" type="button">
                                                <i class="bi bi-trash"></i> Remove
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">No IPs yet. Click "Add IP" to create one.</td>
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
    @include('dealer.modals.website-settings-add-edit-ip-address')
@endpush

@push('page-scripts')
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let currentIpId = null;

        function showToast(type, message) {
            if (type === 'success') toastr.success(message);
            else toastr.error(message);
        }

        // Modal handling
        const ipOverlay = document.getElementById('ipModal');
        const ipTitle = document.getElementById('ipModalTitle');
        const ipAddressInput = document.getElementById('ipAddressInput');
        const ipDescInput = document.getElementById('ipDescInput');

        function openIpModal(data = {}) {
            currentIpId = data.id || null;
            ipTitle.textContent = data.id ? 'Edit IP Address' : 'Add IP Address';
            ipAddressInput.value = data.ip || '';
            ipDescInput.value = data.desc || '';
            ipOverlay.classList.add('active');
        }

        function closeIpModal() {
            ipOverlay.classList.remove('active');
            currentIpId = null;
        }

        document.getElementById('btnAddIp').addEventListener('click', () => openIpModal());

        // Dynamic dropdown handling
        function bindDropdownEvents() {
            document.querySelectorAll('.ip-dots-btn').forEach(btn => {
                btn.removeEventListener('click', toggleDropdown);
                btn.addEventListener('click', toggleDropdown);
            });

            document.querySelectorAll('.ip-edit-btn').forEach(btn => {
                btn.removeEventListener('click', editHandler);
                btn.addEventListener('click', editHandler);
            });

            document.querySelectorAll('.ip-remove-btn').forEach(btn => {
                btn.removeEventListener('click', removeHandler);
                btn.addEventListener('click', removeHandler);
            });
        }

        function toggleDropdown(e) {
            e.stopPropagation();
            const btn = e.currentTarget;
            const dropdown = btn.nextElementSibling;
            const isOpen = dropdown.classList.contains('open');

            // Close all others
            document.querySelectorAll('.ip-dropdown.open').forEach(d => d.classList.remove('open'));
            document.querySelectorAll('.ip-dots-btn.open').forEach(d => d.classList.remove('open'));

            if (!isOpen) {
                dropdown.classList.add('open');
                btn.classList.add('open');
            }
        }

        function editHandler(e) {
            const btn = e.currentTarget;
            const row = btn.closest('tr');
            const dotsBtn = row.querySelector('.ip-dots-btn');
            const id = dotsBtn.dataset.id;
            const ip = dotsBtn.dataset.ip;
            const desc = dotsBtn.dataset.desc;
            openIpModal({ id, ip, desc });
            closeAllDropdowns();
        }

        async function removeHandler(e) {
            const btn = e.currentTarget;
            const row = btn.closest('tr');
            const id = row.dataset.id;
            if (!confirm('Remove this IP address?')) return;

            try {
                const response = await fetch(`/dealer/website/settings/ips/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                });
                const data = await response.json();
                if (response.ok && data.success) {
                    showToast('success', data.message);
                    row.remove();
                    if (document.querySelectorAll('#ipTableBody tr').length === 0) {
                        document.getElementById('ipTableBody').innerHTML = '<tr><td colspan="4" class="text-center py-4">No IPs yet. Click "Add IP" to create one.</td></tr>';
                    }
                } else {
                    showToast('error', data.message || 'Delete failed.');
                }
            } catch (err) {
                showToast('error', 'An error occurred.');
            }
            closeAllDropdowns();
        }

        function closeAllDropdowns() {
            document.querySelectorAll('.ip-dropdown.open').forEach(d => d.classList.remove('open'));
            document.querySelectorAll('.ip-dots-btn.open').forEach(d => d.classList.remove('open'));
        }

        document.addEventListener('click', (e) => {
            if (!e.target.closest('.ip-dots-btn') && !e.target.closest('.ip-dropdown')) {
                closeAllDropdowns();
            }
        });

        // Save IP (add/edit)
        document.getElementById('btnSaveIp').addEventListener('click', async () => {
            const ip = ipAddressInput.value.trim();
            const description = ipDescInput.value.trim();

            const formData = { ip_address: ip, description: description };

            const url = currentIpId
                ? `/dealer/website/settings/ips/${currentIpId}`
                : '{{ route("dealer.website.settings.ips.store") }}';
            const method = currentIpId ? 'PATCH' : 'POST';

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
                    if (data.errors) {
                        // Show each error as separate toast
                        for (const [field, messages] of Object.entries(data.errors)) {
                            messages.forEach(msg => showToast('error', msg));
                        }
                    } else {
                        showToast('error', data.message || 'An error occurred.');
                    }
                    return;
                }

                if (data.success) {
                    {{-- showToast('success', data.message); --}}
                    closeIpModal();
                    bindDropdownEvents();
                    location.reload();
                } else {
                    showToast('error', data.message || 'Failed to save.');
                }
            } catch (err) {
                showToast('error', 'An error occurred. Please try again.');
            }
        });

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Close modal
        document.getElementById('btnCloseIpModal').addEventListener('click', closeIpModal);
        ipOverlay.addEventListener('click', e => { if (e.target === ipOverlay) closeIpModal(); });
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeIpModal(); });

        // Initial bind
        bindDropdownEvents();
    </script>
@endpush
