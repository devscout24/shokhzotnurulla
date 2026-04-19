@extends('layouts.dealer.app')

@section('title', __('Media') . ' | '. __(config('app.name')))

@push('page-assets')
    @vite([
        'resources/css/dealer/pages/media.css',
        'resources/js/dealer/pages/media.js',
    ])
@endpush

@section('page-content')
    <main class="main-content" id="mainContent">
        <div class="page-header">
            <h2 class="view-title">Media Library</h2>
        </div>

        <div class="view-content" data-view="media-library">
            <div class="ml-page">

                {{-- ── TOOLBAR ── --}}
                <div class="ml-toolbar">
                    <div class="ml-tabs">
                        <button class="ml-tab active" data-tab="all">All Media</button>
                        <button class="ml-tab" data-tab="image">Images</button>
                        <button class="ml-tab" data-tab="video">Video</button>
                        <button class="ml-tab" data-tab="file">Files</button>
                    </div>
                    <div class="ml-toolbar-right">
                        <input type="text"
                               class="ml-search"
                               id="mlSearch"
                               placeholder="Search media">
                        <button type="button" class="ml-btn-add" id="mlBtnAddMedia">
                            + Add Media
                        </button>
                    </div>
                </div>

                <hr class="ml-divider">

                {{-- ── UPLOAD ZONE (hidden by default) ── --}}
                <div class="ml-upload-zone" id="mlUploadZone">
                    <i class="bi bi-cloud-upload"></i>
                    <p>Drag and drop some files here, or click here to select files.<br>
                       Non-image or video files will be discarded.</p>
                </div>
                <div class="ml-upload-actions" id="mlUploadActions">
                    <button type="button" class="ml-btn-upload" id="mlBtnDoUpload" disabled>
                        <i class="bi bi-upload"></i> Upload
                    </button>
                    <button type="button" class="ml-btn-close-upload" id="mlBtnCloseUpload">
                        &#10005; Cancel
                    </button>
                    <span id="mlUploadProgress"></span>  {{-- YEH ADD KARO --}}
                </div>
                <input
                  type="file"
                  id="mlFileInput"
                  multiple
                  accept="
                    image/*,
                    video/*,
                    .pdf,
                    .doc,
                    .docx,
                    .xls,
                    .xlsx,
                    .csv
                  "
                >

                {{-- ── DISPLAY TOGGLE ── --}}
                <div class="ml-display-bar">
                    <span>Display</span>
                    <select class="ml-display-select" id="mlDisplayMode">
                        <option value="compact" selected>Compact</option>
                        <option value="comfortable">Comfortable</option>
                        <option value="expanded">Expanded</option>
                    </select>
                </div>

                {{-- ── MEDIA GRID ── --}}
                <div id="mlGrid"></div>

                {{-- ── PAGINATION ── --}}
                <div class="ml-pagination-wrap" id="mlPaginationWrap">
                    <div class="ml-pagination" id="mlPagination"></div>
                    <div class="ml-showing" id="mlShowing"></div>
                </div>

            </div>
        </div>
    </main>
@endsection

@push('page-modals')
    @include('dealer.offcanvas.media-detail')
@endpush

@push('page-scripts')
    <script>
        window.mediaRoutes = {
            list:    '{{ route('dealer.website.media.list') }}',
            upload:  '{{ route('dealer.website.media.upload') }}',
            update:  '{{ route('dealer.website.media.update', ['media' => '__id__']) }}',
            destroy: '{{ route('dealer.website.media.destroy', ['media' => '__id__']) }}',
        };
    </script>
@endpush