@extends('layouts.dealer.app')

@section('title', __('Manage users') . ' | ' . __(config('app.name')))

@push('page-styles')
<style>
    .user-management-container {
        padding: 20px;
        display: flex;
        justify-content: center;
    }
    .user-table-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        width: 100%;
        max-width: 900px;
        overflow: hidden;
    }
    @media (min-width: 992px) {
        .user-table-card { width: 60%; }
    }
    .card-header-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
    }
    .table-responsive-scout {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .user-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 600px;
    }
    .user-table th {
        background: #fcfcfc;
        padding: 12px 20px;
        text-align: left;
        font-size: 12px;
        font-weight: 600;
        color: #777;
        text-transform: uppercase;
        border-bottom: 1px solid #eee;
    }
    .user-table td {
        padding: 15px 20px;
        font-size: 13px;
        color: #444;
        border-bottom: 1px solid #eee;
        vertical-align: middle;
    }
    .role-badge {
        font-size: 10px;
        font-weight: 700;
        color: #888;
        display: block;
        margin-bottom: 4px;
    }
    .owner-tag {
        background: #f1f1f1;
        color: #666;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: bold;
        margin-left: 5px;
    }
    .btn-add-user {
        background: #ce4f4b;
        color: #fff !important;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        text-decoration: none;
    }
    .btn-add-user:hover { background: #b8433f; }
    
    /* Dropdown Styles */
    .dropdown-scout {
        position: relative;
        display: inline-block;
    }
    .btn-dots {
        background: #f8f9fa;
        border: 1px solid #eee;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #666;
    }
    .dropdown-menu-scout {
        display: none;
        position: absolute;
        right: 0;
        top: 100%;
        background: #fff;
        min-width: 150px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border-radius: 6px;
        z-index: 100;
        padding: 5px 0;
        border: 1px solid #eee;
    }
    .dropdown-item-scout {
        padding: 8px 15px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        color: #333;
        text-decoration: none;
        cursor: pointer;
        text-align: left;
    }
    .dropdown-item-scout:hover { background: #f8f9fa; }
    .dropdown-item-scout.danger { color: #ce4f4b; }
    .dropdown-item-scout.disabled { color: #ccc; cursor: not-allowed; }

    /* Modal Styles */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }
    .modal-content-scout {
        background: #fff;
        width: 90%;
        max-width: 500px;
        border-radius: 8px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        overflow: hidden;
    }
    .modal-header-scout {
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .modal-body-scout {
        padding: 20px;
    }
    .form-group-scout {
        margin-bottom: 15px;
    }
    .form-group-scout label {
        font-size: 13px;
        font-weight: 600;
        color: #333;
        margin-bottom: 6px;
        display: block;
    }
    .form-control-scout {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 13px;
    }
    .role-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-top: 5px;
    }
    .role-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #555;
    }
    .password-wrapper {
        display: flex;
        gap: 8px;
        margin-top: 5px;
    }
    .password-input {
        flex: 1;
        background: #f8f9fa;
        font-family: monospace;
    }
    .btn-password-action {
        padding: 6px 10px;
        border: 1px solid #ddd;
        background: #fff;
        border-radius: 4px;
        font-size: 12px;
        cursor: pointer;
    }
    .modal-footer-scout {
        padding: 15px 20px;
        border-top: 1px solid #eee;
        text-align: right;
    }
    .btn-save-user {
        background: #ce4f4b;
        color: #fff !important;
        border: none;
        padding: 10px 24px;
        border-radius: 4px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
    }
    
    /* Toaster Styles */
    #toaster {
        position: fixed;
        bottom: 30px;
        right: 30px;
        padding: 12px 25px;
        background: #2c3e50;
        color: #fff;
        border-radius: 4px;
        font-size: 14px;
        z-index: 2000;
        display: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    #toaster.success { background: #27ae60; }
    #toaster.error { background: #ce4f4b; }
</style>
@endpush

@section('page-content')
<main class="main-content" id="mainContent">
    <div class="user-management-container">
        
        <div class="user-table-card">
            <div class="card-header-flex">
                <h6 style="margin: 0; color: #444;">Manage users</h6>
                <button type="button" class="btn-add-user" onclick="openModal('addUserModal')">
                    <i class="bi bi-person-plus-fill"></i> Add User
                </button>
            </div>
            
            <div class="table-responsive-scout">
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Permissions</th>
                            <th>MFA</th>
                            <th>Locked Out</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center;">
                                    <span style="font-weight: 600;">{{ $user->full_name }}</span>
                                    @if($user->is_owner)
                                        <span class="owner-tag">OWNER</span>
                                    @endif
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="role-badge">ROLES</span>
                                @php
                                    $roleMap = [
                                        'dealer_owner' => 'Owner',
                                        'dealer_manager' => 'Manager',
                                        'dealer_sales' => 'Sales',
                                        'dealer_support' => 'Support',
                                    ];
                                    $roleNames = $user->roles->pluck('name')->map(fn($n) => $roleMap[$n] ?? $n)->toArray();
                                    if($user->is_owner && !in_array('Owner', $roleNames)) {
                                        array_unshift($roleNames, 'Owner');
                                    }
                                @endphp
                                {{ implode(', ', array_unique($roleNames)) }}
                            </td>
                            <td style="color: #ce4f4b;"><i class="bi bi-x-lg"></i> No</td>
                            <td style="color: #2c3e50;"><i class="bi bi-unlock"></i> No</td>
                            <td style="text-align: right;">
                                <div class="dropdown-scout">
                                    <button type="button" class="btn-dots" onclick="toggleDropdown(this)">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <div class="dropdown-menu-scout">
                                        <div class="dropdown-item-scout" onclick="openEditModal({{ $user->toJson() }})">
                                            <i class="bi bi-pencil"></i> Edit User
                                        </div>
                                        <div class="dropdown-item-scout disabled">
                                            <i class="bi bi-key" style="color: #ccc;"></i> <span style="color: #ccc;">Reset Password</span>
                                        </div>
                                        <div class="settings-divider" style="height: 1px; background: #eee; margin: 5px 0;"></div>
                                        @if($user->id !== auth()->id())
                                        <div class="dropdown-item-scout danger" onclick="openDeleteModal({{ $user->id }})">
                                            <i class="bi bi-trash"></i> Remove
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</main>

<!-- Modals & Toaster -->
<div id="toaster"></div>

<!-- Add User Modal -->
<div class="modal-overlay" id="addUserModal">
    <div class="modal-content-scout">
        <div class="modal-header-scout">
            <h6 style="margin: 0;">Add User</h6>
            <button type="button" class="btn-close" onclick="closeModal('addUserModal')" style="border: none; background: transparent; font-size: 20px; color: #999;">&times;</button>
        </div>
        <form id="addUserForm">
            <div class="modal-body-scout">
                <div class="form-group-scout">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control-scout" placeholder="user@email.com" required>
                </div>
                <div style="display: flex; gap: 15px;">
                    <div class="form-group-scout" style="flex: 1;">
                        <label>First Name</label>
                        <input type="text" name="first_name" class="form-control-scout" placeholder="First name" required>
                    </div>
                    <div class="form-group-scout" style="flex: 1;">
                        <label>Last Name</label>
                        <input type="text" name="last_name" class="form-control-scout" placeholder="Last name" required>
                    </div>
                </div>
                <div class="form-group-scout">
                    <label>Time Zone</label>
                    <select name="timezone" class="form-control-scout">
                        <option value="">Select timezone</option>
                        @foreach($timezones as $tz)
                            <option value="{{ $tz }}" {{ $tz === 'America/Chicago' ? 'selected' : '' }}>{{ $tz }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group-scout">
                    <label>Roles</label>
                    <div class="role-list">
                        <div class="role-item">
                            <input type="checkbox" name="roles[]" value="dealer_manager" id="add_role_manager">
                            <label for="add_role_manager" style="margin: 0; font-weight: normal;">Manager</label>
                        </div>
                        <div class="role-item">
                            <input type="checkbox" name="roles[]" value="dealer_sales" id="add_role_sales">
                            <label for="add_role_sales" style="margin: 0; font-weight: normal;">Sales</label>
                        </div>
                        <div class="role-item">
                            <input type="checkbox" name="roles[]" value="dealer_support" id="add_role_support">
                            <label for="add_role_support" style="margin: 0; font-weight: normal;">Support</label>
                        </div>
                    </div>
                </div>
                
                <div class="form-group-scout">
                    <label>Temporary Password</label>
                    <div class="password-wrapper">
                        <input type="text" name="password" id="temp_password" class="form-control-scout password-input" readonly>
                        <button type="button" class="btn-password-action" onclick="generatePassword()">
                            <i class="bi bi-arrow-clockwise"></i> Regenerate
                        </button>
                        <button type="button" class="btn-password-action" onclick="copyPassword()">
                            <i class="bi bi-files"></i> Copy
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer-scout">
                <button type="submit" class="btn-save-user">
                    <i class="bi bi-check-lg"></i> Save
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal-overlay" id="editUserModal">
    <div class="modal-content-scout">
        <div class="modal-header-scout">
            <h6 style="margin: 0;">Edit User</h6>
            <button type="button" class="btn-close" onclick="closeModal('editUserModal')" style="border: none; background: transparent; font-size: 20px; color: #999;">&times;</button>
        </div>
        <form id="editUserForm">
            <input type="hidden" name="user_id" id="edit_user_id">
            <div class="modal-body-scout">
                <div class="form-group-scout" style="display: flex; justify-content: space-between;">
                    <label>Email Address</label>
                    <div id="edit_email_text" style="font-size: 13px; color: #555;"></div>
                </div>
                <div style="display: flex; gap: 15px;">
                    <div class="form-group-scout" style="flex: 1;">
                        <label>First Name</label>
                        <input type="text" name="first_name" id="edit_first_name" class="form-control-scout" required>
                    </div>
                    <div class="form-group-scout" style="flex: 1;">
                        <label>Last Name</label>
                        <input type="text" name="last_name" id="edit_last_name" class="form-control-scout" required>
                    </div>
                </div>
                <div class="form-group-scout">
                    <label>Time Zone</label>
                    <select name="timezone" id="edit_timezone" class="form-control-scout">
                        @foreach($timezones as $tz)
                            <option value="{{ $tz }}">{{ $tz }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group-scout">
                    <label>Roles</label>
                    <div class="role-list">
                        <div class="role-item">
                            <input type="checkbox" name="roles[]" value="dealer_manager" id="edit_role_manager">
                            <label for="edit_role_manager" style="margin: 0; font-weight: normal;">Manager</label>
                        </div>
                        <div class="role-item">
                            <input type="checkbox" name="roles[]" value="dealer_sales" id="edit_role_sales">
                            <label for="edit_role_sales" style="margin: 0; font-weight: normal;">Sales</label>
                        </div>
                        <div class="role-item">
                            <input type="checkbox" name="roles[]" value="dealer_support" id="edit_role_support">
                            <label for="edit_role_support" style="margin: 0; font-weight: normal;">Support</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer-scout">
                <button type="submit" class="btn-save-user">
                    <i class="bi bi-check-lg"></i> Save
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal-content-scout" style="max-width: 400px;">
        <div class="modal-header-scout">
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="bi bi-info-circle-fill" style="color: #f39c12; font-size: 18px;"></i>
                <h6 style="margin: 0; font-weight: 600;">Are you sure?</h6>
            </div>
            <button type="button" class="btn-close" onclick="closeModal('deleteModal')" style="border: none; background: transparent; font-size: 20px; color: #999;">&times;</button>
        </div>
        <div class="modal-body-scout" style="padding: 25px 20px;">
            <p style="font-size: 14px; color: #555; margin: 0;">Are you sure you want to delete this user?</p>
        </div>
        <div class="modal-footer-scout" style="background: #fcfcfc;">
            <button type="button" class="btn-action" onclick="closeModal('deleteModal')" style="padding: 8px 20px; border: 1px solid #ddd; background: #fff; border-radius: 4px; font-size: 13px; margin-right: 10px; cursor: pointer;">Cancel</button>
            <button type="button" id="confirmDeleteBtn" class="btn-save-user" style="padding: 8px 20px;">Continue</button>
        </div>
    </div>
</div>
@endsection

@push('page-scripts')
<script>
    let deleteUserId = null;

    function showToaster(message, type = 'success') {
        const toaster = document.getElementById('toaster');
        toaster.innerText = message;
        toaster.className = type;
        toaster.style.display = 'block';
        setTimeout(() => { toaster.style.display = 'none'; }, 3000);
    }

    function toggleDropdown(btn) {
        const menu = btn.nextElementSibling;
        const isOpen = menu.style.display === 'block';
        document.querySelectorAll('.dropdown-menu-scout').forEach(m => m.style.display = 'none');
        if (!isOpen) { menu.style.display = 'block'; }
    }

    window.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown-scout')) {
            document.querySelectorAll('.dropdown-menu-scout').forEach(m => m.style.display = 'none');
        }
    });

    function openModal(id) {
        document.getElementById(id).style.display = 'flex';
        if (id === 'addUserModal') generatePassword();
    }

    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }

    function openEditModal(user) {
        document.getElementById('edit_user_id').value = user.id;
        document.getElementById('edit_email_text').innerText = user.email;
        document.getElementById('edit_first_name').value = user.first_name;
        document.getElementById('edit_last_name').value = user.last_name;
        document.getElementById('edit_timezone').value = user.timezone || 'UTC';
        
        document.querySelectorAll('#editUserModal input[type="checkbox"]').forEach(cb => cb.checked = false);
        if (user.roles) {
            user.roles.forEach(role => {
                const cb = document.querySelector(`#editUserModal input[value="${role.name}"]`);
                if (cb) cb.checked = true;
            });
        }
        openModal('editUserModal');
    }

    function openDeleteModal(userId) {
        deleteUserId = userId;
        openModal('deleteModal');
    }

    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (!deleteUserId) return;
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = "{{ url('dealer/settings/users') }}/" + deleteUserId;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = "{{ csrf_token() }}";
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    });

    function generatePassword() {
        const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$%^&*";
        let pass = "";
        for (let i = 0; i < 12; i++) { pass += chars[Math.floor(Math.random() * chars.length)]; }
        document.getElementById('temp_password').value = pass;
    }

    function copyPassword() {
        const passInput = document.getElementById('temp_password');
        passInput.select();
        document.execCommand("copy");
        showToaster("Password copied to clipboard!");
    }

    document.getElementById('addUserForm').addEventListener('submit', function(e) {
        e.preventDefault();
        submitForm("{{ route('dealer.settings.users.store') }}", new FormData(this));
    });

    document.getElementById('editUserForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const userId = document.getElementById('edit_user_id').value;
        const formData = new FormData(this);
        formData.append('_method', 'PATCH');
        submitForm("{{ url('dealer/settings/users') }}/" + userId, formData);
    });

    function submitForm(url, formData) {
        fetch(url, {
            method: 'POST', // Always use POST with _method override for PATCH/DELETE
            body: formData,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                showToaster(data.message);
                setTimeout(() => { location.reload(); }, 1000);
            } else {
                showToaster(data.message || 'Something went wrong', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToaster('An error occurred. Please check the console.', 'error');
        });
    }
</script>
@endpush
