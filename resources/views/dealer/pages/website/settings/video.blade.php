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
                        <form id="videoSettingsForm" enctype="multipart/form-data" method="POST">
                            @csrf
                            @method('PATCH')

                            <div class="px-3 py-3 border-bottom d-flex justify-content-between align-items-center">
                                <span class="fw-semibold" style="font-size: 14px; color: #333;">Background Video</span>
                                <button type="submit" class="btn btn-primary btn-sm px-3" id="btnSaveVideo">Save Changes</button>
                            </div>

                            <div class="p-4">
                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Video source</label>
                                        <p class="text-muted small">Select where your video is hosted.</p>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="d-flex flex-wrap gap-3">
                                            <div class="source-card border rounded p-2 text-center cursor-pointer {{ !$dealer->video_source ? 'active' : '' }}" data-value="">
                                                <i class="bi bi-x-circle d-block mb-1"></i>
                                                <small>None</small>
                                                <input type="radio" name="video_source" value="" class="d-none" {{ !$dealer->video_source ? 'checked' : '' }}>
                                            </div>
                                            <div class="source-card border rounded p-2 text-center cursor-pointer {{ $dealer->video_source == 'youtube' ? 'active' : '' }}" data-value="youtube">
                                                <i class="bi bi-youtube d-block mb-1"></i>
                                                <small>YouTube</small>
                                                <input type="radio" name="video_source" value="youtube" class="d-none" {{ $dealer->video_source == 'youtube' ? 'checked' : '' }}>
                                            </div>
                                            <div class="source-card border rounded p-2 text-center cursor-pointer {{ $dealer->video_source == 'overfuel' ? 'active' : '' }}" data-value="overfuel">
                                                <i class="bi bi-cloud-upload d-block mb-1"></i>
                                                <small>Upload MP4</small>
                                                <input type="radio" name="video_source" value="overfuel" class="d-none" {{ $dealer->video_source == 'overfuel' ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-4" id="youtubeInputContainer" style="display: none;">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">YouTube Video ID / URL</label>
                                        <p class="text-muted small">Provide the Video ID or full URL for YouTube.</p>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" name="video_url" class="form-control form-control-sm" value="{{ $dealer->video_source == 'youtube' ? $dealer->video_url : '' }}" placeholder="dQw4w9WgXcQ or https://www.youtube.com/watch?v=dQw4w9WgXcQ">
                                    </div>
                                </div>

                                <div class="row mb-4" id="videoFileInputContainer" style="display: none;">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Upload Video File</label>
                                        <p class="text-muted small">Select an MP4 video from your computer.</p>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="p-3 border rounded bg-light">
                                            <input type="file" name="video_file" class="form-control form-control-sm" accept="video/mp4" id="videoFileInput">
                                            <div class="progress mt-2 d-none" style="height: 6px;" id="uploadProgressContainer">
                                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;" id="uploadProgressBar"></div>
                                            </div>
                                            <div class="mt-2" id="currentVideoInfo">
                                                @if($dealer->video_source == 'overfuel' && $dealer->video_url)
                                                    <small class="text-muted">Current file: <a href="{{ asset($dealer->video_url) }}" target="_blank" class="text-decoration-none"><i class="bi bi-play-circle"></i> View Saved Video</a></small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="videoPreviewContainer" class="mt-4 d-none">
                                    <label class="form-label fw-semibold">Video Preview</label>
                                    <div class="ratio ratio-16x9 bg-black rounded shadow-sm overflow-hidden border">
                                        <iframe id="videoPreview" src="" allowfullscreen class="d-none border-0"></iframe>
                                        <video id="videoFilePreview" src="" controls class="d-none w-100 h-100"></video>
                                    </div>
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
$(function() {
    const $form = $('#videoSettingsForm');
    const $sourceCards = $('.source-card');
    const $urlInput = $('input[name="video_url"]');
    const $fileInput = $('input[name="video_file"]');
    const $preview = $('#videoPreview');
    const $videoFilePreview = $('#videoFilePreview');
    const $previewContainer = $('#videoPreviewContainer');
    const $youtubeContainer = $('#youtubeInputContainer');
    const $fileContainer = $('#videoFileInputContainer');

    let activeVideoFileUrl = {!! json_encode($dealer->video_source === 'overfuel' && $dealer->video_url ? asset($dealer->video_url) : null) !!};

    function updatePreview() {
        const source = $('input[name="video_source"]:checked').val();
        const youtubeUrl = $urlInput.val();

        $youtubeContainer.hide();
        $fileContainer.hide();
        $preview.addClass('d-none').attr('src', '');
        $videoFilePreview.addClass('d-none').attr('src', '');
        $previewContainer.addClass('d-none');

        if (source === 'youtube') {
            $youtubeContainer.show();
            if (youtubeUrl) {
                const videoId = youtubeUrl.includes('v=') ? youtubeUrl.split('v=').pop().split('&')[0] : youtubeUrl.split('/').pop();
                if (videoId && videoId.length > 5) {
                    const embedUrl = `https://www.youtube.com/embed/${videoId}?autoplay=1&mute=1&loop=1&playlist=${videoId}`;
                    $preview.attr('src', embedUrl).removeClass('d-none');
                    $previewContainer.removeClass('d-none');
                }
            }
        } else if (source === 'overfuel') {
            $fileContainer.show();
            if (activeVideoFileUrl) {
                $videoFilePreview.attr('src', activeVideoFileUrl).removeClass('d-none');
                $previewContainer.removeClass('d-none');
            }
        }
    }

    $sourceCards.on('click', function() {
        const val = $(this).data('value');
        $sourceCards.removeClass('active');
        $(this).addClass('active').find('input').prop('checked', true);
        updatePreview();
    });

    $urlInput.on('input', updatePreview);
    updatePreview();

    $form.on('submit', function(e) {
        e.preventDefault();
        const $btn = $('#btnSaveVideo');
        const $progressBar = $('#uploadProgressBar');
        const $progressContainer = $('#uploadProgressContainer');
        const source = $('input[name="video_source"]:checked').val();
        
        $btn.prop('disabled', true).text('Saving...');
        if (source === 'overfuel' && $fileInput[0].files.length > 0) {
            $progressContainer.removeClass('d-none');
        }

        const formData = new FormData(this);

        $.ajax({
            url: "{{ route('dealer.website.settings.video.update') }}",
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            xhr: function() {
                const xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable) {
                        const percentComplete = Math.round((evt.loaded / evt.total) * 100);
                        $progressBar.css('width', percentComplete + '%').text(percentComplete + '%');
                    }
                }, false);
                return xhr;
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    if (response.video_url && source === 'overfuel') {
                         activeVideoFileUrl = response.video_url.startsWith('http') ? response.video_url : window.location.origin + response.video_url;
                         $('#currentVideoInfo').html(`<small class="text-muted">Current file: <a href="${activeVideoFileUrl}" target="_blank" class="text-decoration-none"><i class="bi bi-play-circle"></i> View Saved Video</a></small>`);
                         updatePreview();
                    }
                    $fileInput.val('');
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Something went wrong. Check file size.';
                toastr.error(message);
            },
            complete: function() {
                $btn.prop('disabled', false).text('Save Changes');
                $progressContainer.addClass('d-none');
                $progressBar.css('width', '0%').text('0%');
            }
        });
    });
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
