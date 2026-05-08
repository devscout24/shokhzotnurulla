@extends('layouts.admin.app')

@section('title', __('Edit Dealer') . ' | '. __(config('app.name')))

@push('page-styles')
<style>
    .form-card {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 24px;
        max-width: 600px;
        margin-top: 20px;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #444;
        margin-bottom: 8px;
    }
    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
        transition: border-color 0.2s;
    }
    .form-control:focus {
        border-color: #c0392b;
        outline: none;
        box-shadow: 0 0 0 3px rgba(192, 57, 43, 0.1);
    }
    .text-danger { color: #d93025; font-size: 12px; margin-top: 4px; }
    
    .btn-save {
        background: #c0392b;
        color: #fff;
        border: none;
        padding: 10px 24px;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-save:hover { background: #a93226; }
    .btn-cancel {
        color: #666;
        text-decoration: none;
        font-size: 14px;
        margin-left: 15px;
    }
</style>
@endpush

@section('page-content')
<main class="main-content" id="mainContent">
    <div class="page-header">
        <h2 class="view-title">Edit Dealer: {{ $dealer->company_name }}</h2>
    </div>

    <hr>

    <div class="view-content">
        <div class="form-card">
            <form action="{{ route('admin.dealers.update', $dealer) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label">Company Name</label>
                    <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $dealer->company_name) }}" required>
                    @error('company_name') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Domains</label>
                    <div id="domains-container">
                        @foreach($dealer->domains as $index => $domainRecord)
                            <div class="domain-input-wrapper" style="display: flex; gap: 8px; margin-bottom: 8px;">
                                <input type="text" name="domains[]" class="form-control" value="{{ $domainRecord->domain }}" required>
                                @if($index === 0)
                                    <button type="button" class="btn-action" onclick="addDomainRow()" style="padding: 10px;"><i class="bi bi-plus"></i></button>
                                @else
                                    <button type="button" class="btn-action text-danger" onclick="this.parentElement.remove()" style="padding: 10px;"><i class="bi bi-trash"></i></button>
                                @endif
                            </div>
                        @endforeach
                        @if($dealer->domains->isEmpty())
                            <div class="domain-input-wrapper" style="display: flex; gap: 8px; margin-bottom: 8px;">
                                <input type="text" name="domains[]" class="form-control" value="{{ $dealer->domain }}" required>
                                <button type="button" class="btn-action" onclick="addDomainRow()" style="padding: 10px;"><i class="bi bi-plus"></i></button>
                            </div>
                        @endif
                    </div>
                    @error('domains') <div class="text-danger">{{ $message }}</div> @enderror
                    @error('domains.*') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $dealer->email) }}">
                    @error('email') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $dealer->phone) }}">
                    @error('phone') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        @foreach(\App\Enums\DealerStatus::cases() as $status)
                            <option value="{{ $status->value }}" {{ old('status', $dealer->status->value) === $status->value ? 'selected' : '' }}>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('status') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div style="margin-top: 30px; display: flex; align-items: center;">
                    <button type="submit" class="btn-save">Update Dealer</button>
                    <a href="{{ route('admin.dealers.index') }}" class="btn-cancel">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</main>
@endsection

@push('page-scripts')
<script>
    function addDomainRow() {
        const container = document.getElementById('domains-container');
        const div = document.createElement('div');
        div.className = 'domain-input-wrapper';
        div.style.display = 'flex';
        div.style.gap = '8px';
        div.style.marginBottom = '8px';
        div.innerHTML = `
            <input type="text" name="domains[]" class="form-control" placeholder="e.g. sub.domain.com" required>
            <button type="button" class="btn-action text-danger" onclick="this.parentElement.remove()" style="padding: 10px;"><i class="bi bi-trash"></i></button>
        `;
        container.appendChild(div);
    }
</script>
@endpush
