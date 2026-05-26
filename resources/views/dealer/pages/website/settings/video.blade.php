@extends('layouts.dealer.app')

@section('title', __('Background Video') . ' | '. __(config('app.name')))

@section('page-content')
    <main class="main-content" id="mainContent">
        <div class="page-header">
            <h2 class="view-title">Website Settings</h2>
        </div>

        <div class="view-content" data-view="video">
            <div class="d-flex" style="min-height: calc(100vh - 60px); background: #f2f2f2; font-size: 13px;">

                {{-- ── LEFT SIDEBAR ── --}}
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
                    <a href="{{ route('dealer.website.settings.home-about-cta') }}" class="menu-item">
                        <span class="ws-icon"><i class="bi bi-layout-text-window-reverse"></i></span>
                        <span>Home About/CTA</span>
                    </a>
                    <a href="{{ route('dealer.website.settings.finance') }}" class="menu-item">
                        <span class="ws-icon"><i class="bi bi-percent"></i></span>
                        <span>Interest Rates</span>
                    </a>
                    <a href="{{ route('dealer.website.settings.retail') }}" class="menu-item">
                        <span class="ws-icon"><i class="bi bi-grid"></i></span>
                        <span>Digital Retail</span>
                    </a>
                    <a href="{{ route('dealer.website.settings.video') }}" class="menu-item active">
                        <span class="ws-icon"><i class="bi bi-camera-video"></i></span>
                        <span>Background Video</span>
                    </a>
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
                    <div class="bg-white border rounded" style="max-width: 1200px;">
                        @php
                            $currentVideoUrl = $dealer->video_url
                                ? (preg_match('/^https?:\\/\\//', $dealer->video_url) ? $dealer->video_url : url('/' . ltrim($dealer->video_url, '/')))
                                : null;
                        @endphp
                        <form id="videoSettingsForm" action="{{ route('dealer.website.settings.video.update') }}" enctype="multipart/form-data" method="POST">
                            @csrf

                            <div class="px-3 py-3 border-bottom d-flex justify-content-between align-items-center">
                                <span class="fw-semibold" style="font-size: 14px; color: #333;">Background Video</span>
                                <button type="submit" class="btn btn-primary btn-sm px-3" id="btnSaveVideo">Save Changes</button>
                            </div>

                            <div class="p-4">
                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Upload Video File</label>
                                        <p class="text-muted small">Select an MP4 video from your computer.</p>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="p-3 border rounded bg-light">
                                            <input type="file" name="video_file" class="d-none" accept="video/mp4" id="videoFileInput">
                                            <input type="hidden" name="video_source" value="overfuel">
                                            <button type="button" class="btn btn-outline-secondary btn-sm" id="btnChooseVideo">Choose File</button>
                                            <div class="mt-2" id="currentVideoInfo">
                                                @if($currentVideoUrl)
                                                    <small class="text-muted">Current file: <a href="{{ $currentVideoUrl }}" target="_blank" class="text-decoration-none"><i class="bi bi-play-circle"></i> View Saved Video</a></small>
                                                @else
                                                    <small class="text-muted">No video uploaded yet.</small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="videoPreviewContainer" class="mt-4 d-none">
                                    <label class="form-label fw-semibold">Video Preview</label>
                                    <div class="ratio ratio-16x9 bg-black rounded shadow-sm overflow-hidden border">
                                        <video id="videoFilePreview" src="" controls class="d-none w-100 h-100"></video>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('page-scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const fileInput = document.getElementById('videoFileInput');
    const videoFilePreview = document.getElementById('videoFilePreview');
    const previewContainer = document.getElementById('videoPreviewContainer');
    const chooseBtn = document.getElementById('btnChooseVideo');

    let activeVideoFileUrl = {!! json_encode($currentVideoUrl) !!};

    function updatePreview() {
        if (videoFilePreview) {
            videoFilePreview.classList.add('d-none');
            videoFilePreview.setAttribute('src', '');
        }
        if (previewContainer) previewContainer.classList.add('d-none');

        if (activeVideoFileUrl && videoFilePreview && previewContainer) {
            videoFilePreview.setAttribute('src', activeVideoFileUrl);
            videoFilePreview.classList.remove('d-none');
            previewContainer.classList.remove('d-none');
        }
    }

    if (fileInput) {
        fileInput.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                if (file.type !== 'video/mp4') {
                    if (window.toastr) toastr.error('Please select an MP4 video file.');
                    this.value = '';
                    return;
                }
                activeVideoFileUrl = URL.createObjectURL(file);
                updatePreview();
            }
        });
    }

    if (chooseBtn && fileInput) {
        chooseBtn.addEventListener('click', () => fileInput.click());
    }

    updatePreview();
});
</script>
@endpush

@push('page-assets')
<style>
    .ws-sidebar { width: 240px; background: #fff; border-right: 1px solid #ddd; }
    .ws-sidebar .menu-item { display: flex; align-items: center; padding: 12px 20px; color: #444; text-decoration: none; border-bottom: 1px solid #f0f0f0; }
    .ws-sidebar .menu-item:hover { background: #f9f9f9; }
    .ws-sidebar .menu-item.active { background: #eef4ff; color: #0056b3; font-weight: 600; border-right: 3px solid #0056b3; }
    .ws-sidebar .ws-icon { margin-right: 12px; font-size: 18px; width: 24px; text-align: center; }
    
    .source-card { width: 100px; transition: all 0.2s; border: 2px solid #eee !important; background: #fff; }
    .source-card:hover { border-color: #0056b3 !important; background: #f8fbff; }
    .source-card.active { border-color: #0056b3 !important; background: #eef4ff; color: #0056b3; font-weight: 600; }
    .source-card i { font-size: 1.5rem; }
    .cursor-pointer { cursor: pointer; }
</style>
@endpush
