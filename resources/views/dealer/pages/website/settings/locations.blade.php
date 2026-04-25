@extends('layouts.dealer.app')

@section('title', __('Locations Settings') . ' | '. __(config('app.name')))

@push('page-assets')
    @vite([
        'resources/css/dealer/pages/website-settings-locations.css',
        // 'resources/js/dealer/pages/website-settings-locations.js',
    ])
@endpush

@section('page-content')
    <main class="main-content" id="mainContent">
        <div class="page-header">
            <h2 class="view-title">Website Settings</h2>
        </div>

        <div class="view-content" data-view="locations">
            <div class="ws-layout">

                {{-- ── LEFT SIDEBAR ── --}}
                <aside class="ws-sidebar">
                    <a href="{{ route('dealer.website.settings.general') }}" class="menu-item">
                        <span class="ws-icon"><i class="bi bi-info-circle"></i></span>
                        <span>General</span>
                    </a>
                    <a href="{{ route('dealer.website.settings.locations') }}" class="menu-item active">
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
                    {{-- <a href="{{ route('dealer.website.settings.domains') }}" class="menu-item">
                        <span class="ws-icon"><i class="bi bi-globe"></i></span>
                        <span>Domains</span>
                    </a> --}}
                    <a href="{{ route('dealer.website.settings.redirects') }}" class="menu-item">
                        <span class="ws-icon"><i class="bi bi-arrow-left-right"></i></span>
                        <span>Redirects</span>
                    </a>
                    <a href="{{ route('dealer.website.settings.ips.index') }}" class="menu-item">
                        <span class="ws-icon"><i class="bi bi-hdd-network"></i></span>
                        <span>Dealer IP Addresses</span>
                    </a>
                </aside>

                {{-- ── RIGHT CONTENT ── --}}
                <div class="flex-grow-1 p-4 overflow-auto">
                    <div class="loc-card">
                        <div class="loc-card-header">
                            <span class="loc-card-title">Locations</span>
                            <button class="loc-btn-add" type="button" id="btnAddLocation">
                                + Add Location
                            </button>
                        </div>

                        <table class="loc-table">
                            <thead>
                                <tr>
                                    <th style="width:40px;"></th>
                                    <th>Name</th>
                                    <th>Address</th>
                                    <th>City</th>
                                    <th>Order</th>
                                </tr>
                            </thead>
                            <tbody id="locations-tbody">
                                @forelse($locations as $location)
                                    <tr data-id="{{ $location->id }}" data-order="{{ $location->order }}">
                                        <td class="drag-handle-cell"><span class="loc-drag-handle">&#9776;</span></td>
                                        <td>
                                            <div class="loc-name-cell">
                                                <span class="loc-name">{{ $location->name }}</span>
                                                <div class="loc-actions">
                                                    <button class="loc-btn-edit btn-open-edit" data-id="{{ $location->id }}" type="button">
                                                        <i class="bi bi-pencil-square"></i> Edit
                                                    </button>
                                                    <button class="loc-btn-trash" data-id="{{ $location->id }}" type="button">
                                                        <i class="bi bi-trash"></i> Trash
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $location->street1 }}</td>
                                        <td>{{ $location->city }}</td>
                                        <td>{{ $location->order }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center py-4">No locations yet. Click "Add Location" to create one.</td></tr>
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
    @include('dealer.modals.website-settings-add-edit-location')
@endpush

@push('page-scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        // CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Toastr helper
        function showToast(type, message) {
            if (type === 'success') toastr.success(message);
            else toastr.error(message);
        }

        // ── Drag & Drop Reorder ──
        const tbody = document.getElementById('locations-tbody');
        let sortable = new Sortable(tbody, {
            handle: '.loc-drag-handle',
            animation: 150,
            onEnd: function() {
                const order = [];
                document.querySelectorAll('#locations-tbody tr').forEach((tr, index) => {
                    const id = tr.dataset.id;
                    if (id) order.push(id);
                });
                fetch('{{ route("dealer.website.settings.locations.reorder") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ order })
                })
                .then(res => res.json())
                .then(data => { if (data.success) showToast('success', data.message); })
                .catch(err => console.error(err));
            }
        });

        // ── Modal functions ──
        const modal = document.getElementById('editLocationModal');
        const modalTitle = document.getElementById('modalTitle');
        let currentLocationId = null;

        function openModal(locationId = null) {
            currentLocationId = locationId;
            modalTitle.innerText = locationId ? 'Edit Location' : 'Add Location';
            resetModalForm();
            if (locationId) {
                fetch(`/dealer/website/settings/locations/${locationId}/edit`)
                    .then(res => res.json())
                    .then(data => fillModalForm(data));
            }
            modal.classList.add('active');
        }

        function resetModalForm() {
            // General
            document.getElementById('locName').value = '';
            document.getElementById('locStreet').value = '';
            document.getElementById('locSuite').value = '';
            document.getElementById('locCity').value = '';
            document.getElementById('locState').value = 'TN';
            document.getElementById('locZip').value = '';
            document.getElementById('locMapOverride').value = '';
            // Phones
            ['main','sales','service','parts','rentals','collision'].forEach(t => {
                document.getElementById(`phone_${t}`).value = '';
            });
            // Emails
            ['main','sales','service','parts','rentals','collision'].forEach(t => {
                document.getElementById(`email_${t}`).value = '';
            });
            // Hours
            document.querySelectorAll('.day-select').forEach(select => select.value = '');
            document.querySelectorAll('.day-check').forEach(cb => cb.checked = false);
            // Holidays
            document.getElementById('holidayRows').innerHTML = '';
        }

        function fillModalForm(data) {
            // General
            document.getElementById('locName').value = data.name || '';
            document.getElementById('locStreet').value = data.street1 || '';
            document.getElementById('locSuite').value = data.street2 || '';
            document.getElementById('locCity').value = data.city || '';
            document.getElementById('locState').value = data.state || '';
            document.getElementById('locZip').value = data.postalcode || '';
            document.getElementById('locMapOverride').value = data.map_override || '';
            // Phones
            if (data.phones) {
                data.phones.forEach(phone => {
                    const input = document.getElementById(`phone_${phone.type}`);
                    if (input) input.value = phone.number;
                });
            }
            // Emails
            if (data.emails) {
                data.emails.forEach(email => {
                    const input = document.getElementById(`email_${email.type}`);
                    if (input) input.value = email.email;
                });
            }
            // Hours – nested structure: data.hours[department][dayIndex]
            if (data.hours) {
                const departments = ['sales', 'service', 'parts', 'rentals', 'collision'];
                departments.forEach(dept => {
                    const deptHours = data.hours[dept];
                    if (deptHours && deptHours.length) {
                        deptHours.forEach((hour, dayIndex) => {
                            const openSelect = document.querySelector(`.day-select[data-dept="${dept}"][data-day="${dayIndex}"][data-type="open"]`);
                            const closeSelect = document.querySelector(`.day-select[data-dept="${dept}"][data-day="${dayIndex}"][data-type="close"]`);
                            const closedCheck = document.querySelector(`.day-check[data-dept="${dept}"][data-day="${dayIndex}"][data-type="closed"]`);
                            const apptCheck = document.querySelector(`.day-check[data-dept="${dept}"][data-day="${dayIndex}"][data-type="appointment_only"]`);
                            if (openSelect) openSelect.value = hour.open || '';
                            if (closeSelect) closeSelect.value = hour.close || '';
                            if (closedCheck) closedCheck.checked = !!hour.is_closed;
                            if (apptCheck) apptCheck.checked = !!hour.appointment_only;
                        });
                    }
                });
            }
            // Special hours
            if (data.special_hours) {
                data.special_hours.forEach(special => addHolidayRow(special));
            }
        }

        function collectFormData() {
            const data = {
                name: document.getElementById('locName').value,
                street1: document.getElementById('locStreet').value,
                street2: document.getElementById('locSuite').value,
                city: document.getElementById('locCity').value,
                state: document.getElementById('locState').value,
                postalcode: document.getElementById('locZip').value,
                country: document.getElementById('locCountry').value,
                map_override: document.getElementById('locMapOverride').value,
            };
            // Phones
            ['main','sales','service','parts','rentals','collision'].forEach(t => {
                data[`phone_${t}`] = document.getElementById(`phone_${t}`).value;
            });
            // Emails
            ['main','sales','service','parts','rentals','collision'].forEach(t => {
                data[`email_${t}`] = document.getElementById(`email_${t}`).value;
            });
            // Hours
            data.hours = { sales: {}, service: {}, parts: {}, rentals: {}, collision: {} };
            document.querySelectorAll('.hours-table').forEach(table => {
                const dept = table.closest('.el-panel').id.replace('panel-hours-', '');
                const rows = table.querySelectorAll('tbody tr');
                rows.forEach((row, dayIndex) => {
                    const open = row.querySelector('.day-select[data-type="open"]').value;
                    const close = row.querySelector('.day-select[data-type="close"]').value;
                    const closed = row.querySelector('.day-check[data-type="closed"]').checked;
                    const appointment = row.querySelector('.day-check[data-type="appointment_only"]').checked;
                    data.hours[dept][dayIndex] = {
                        open: open !== '' ? open : null,
                        close: close !== '' ? close : null,
                        is_closed: closed,
                        appointment_only: appointment,
                    };
                });
            });
            // Special hours
            data.special_hours = [];
            document.querySelectorAll('#holidayRows tr').forEach(row => {
                const dept = row.querySelector('.holiday-dept-select').value;
                const date = row.querySelector('input[type="date"]').value;
                if (dept && date) {
                    data.special_hours.push({
                        department: dept === 'All' ? null : dept,
                        date: date,
                        is_closed: true,
                        appointment_only: false,
                    });
                }
            });
            return data;
        }

        function saveLocation() {
            const data = collectFormData();
            const url = currentLocationId
                ? `/dealer/website/settings/locations/${currentLocationId}`
                : '{{ route("dealer.website.settings.locations.store") }}';
            const method = currentLocationId ? 'PATCH' : 'POST';

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showToast('success', data.message);
                    modal.classList.remove('active');
                    location.reload(); // Simple reload to refresh table
                } else {
                    showToast('error', 'Failed to save location.');
                }
            })
            .catch(error => {
                console.error(error);
                if (error.errors) {
                    // Laravel validation errors
                    let errorMessages = [];
                    for (const [field, messages] of Object.entries(error.errors)) {
                        errorMessages.push(`${field}: ${messages.join(', ')}`);
                    }
                    showToast('error', errorMessages.join('\n'));
                } else {
                    showToast('error', 'An error occurred. Please try again.');
                }
            });
        }

        // Event listeners
        document.getElementById('btnAddLocation').addEventListener('click', () => openModal());
        document.querySelectorAll('.btn-open-edit').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                openModal(id);
            });
        });
        document.querySelectorAll('.loc-btn-trash').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                if (confirm('Are you sure you want to delete this location?')) {
                    fetch(`/dealer/website/settings/locations/${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': csrfToken }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            showToast('success', data.message);
                            location.reload();
                        }
                    });
                }
            });
        });
        document.getElementById('btnCloseEditLocation').addEventListener('click', () => modal.classList.remove('active'));
        modal.addEventListener('click', e => { if (e.target === modal) modal.classList.remove('active'); });
        document.addEventListener('keydown', e => { if (e.key === 'Escape') modal.classList.remove('active'); });

        // Navigation
        const panelOrder = ['general','phones','emails','hours-sales','hours-service','hours-parts','hours-rentals','hours-collision','holidays'];
        let currentPanel = 'general';
        const btnNext = document.getElementById('btnElNext');

        function switchPanel(panelId) {
            document.querySelectorAll('.el-panel').forEach(p => p.classList.remove('active'));
            document.querySelectorAll('.el-nav-item').forEach(n => n.classList.remove('active'));
            document.getElementById(`panel-${panelId}`).classList.add('active');
            document.querySelector(`.el-nav-item[data-panel="${panelId}"]`).classList.add('active');
            currentPanel = panelId;
            const isLast = panelId === panelOrder[panelOrder.length - 1];
            btnNext.innerHTML = isLast ? '&#10003; Save' : 'Next &rsaquo;';
        }

        document.querySelectorAll('.el-nav-item').forEach(btn => {
            btn.addEventListener('click', () => switchPanel(btn.dataset.panel));
        });

        btnNext.addEventListener('click', () => {
            const idx = panelOrder.indexOf(currentPanel);
            if (idx < panelOrder.length - 1) {
                switchPanel(panelOrder[idx + 1]);
            } else {
                saveLocation();
            }
        });

        // Holiday row functions
        function addHolidayRow(data = null) {
            const today = new Date().toISOString().split('T')[0];
            const tr = document.createElement('tr');
            const deptSelect = `<select class="holiday-dept-select">
                <option value="" disabled selected>Select department</option>
                <option>Sales</option><option>Service</option><option>Parts</option>
                <option>Rentals</option><option>Collision</option><option>All</option>
            </select>`;
            const dateInput = `<div class="holiday-date-wrap">
                <button type="button" class="cal-btn" onclick="this.nextElementSibling.showPicker()">📅</button>
                <input type="date" value="${today}">
            </div>`;
            tr.innerHTML = `
                <tr>
                    <td>${deptSelect}</td>
                    <td>${dateInput}</td>
                    <td><div class="d-flex gap-2"><button type="button" class="btn-duplicate">Duplicate</button><button type="button" class="btn-remove-row">X</button></div></td>
                `;
            if (data) {
                const deptSelectEl = tr.querySelector('.holiday-dept-select');
                const dateInputEl = tr.querySelector('input[type="date"]');
                if (data.department) deptSelectEl.value = data.department;
                if (data.date) dateInputEl.value = data.date;
            }
            tr.querySelector('.btn-duplicate').addEventListener('click', () => duplicateRow(tr));
            tr.querySelector('.btn-remove-row').addEventListener('click', () => tr.remove());
            document.getElementById('holidayRows').appendChild(tr);
        }

        function duplicateRow(row) {
            const clone = row.cloneNode(true);
            clone.querySelector('.btn-duplicate').addEventListener('click', () => duplicateRow(clone));
            clone.querySelector('.btn-remove-row').addEventListener('click', () => clone.remove());
            row.parentNode.insertBefore(clone, row.nextSibling);
        }

        document.getElementById('btnAddHolidayRow').addEventListener('click', () => addHolidayRow());

        // Populate time selects
        const timeOptions = ['[Select]','6:00 AM','6:30 AM','7:00 AM','7:30 AM','8:00 AM','8:30 AM','9:00 AM','9:30 AM','10:00 AM','10:30 AM','11:00 AM','11:30 AM','12:00 PM','12:30 PM','1:00 PM','1:30 PM','2:00 PM','2:30 PM','3:00 PM','3:30 PM','4:00 PM','4:30 PM','5:00 PM','5:30 PM','6:00 PM','6:30 PM','7:00 PM','7:30 PM','8:00 PM','9:00 PM'];
        document.querySelectorAll('.day-select').forEach(select => {
            timeOptions.forEach(opt => {
                const option = document.createElement('option');
                option.value = opt === '[Select]' ? '' : opt;
                option.textContent = opt;
                if (opt === '[Select]') option.disabled = true;
                select.appendChild(option);
            });
        });
    </script>
@endpush
