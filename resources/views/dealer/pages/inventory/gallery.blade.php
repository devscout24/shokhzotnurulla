@extends('layouts.dealer.app')
@section('title', __('Photos') . ' | ' . __(config('app.name')))

@push('page-assets')
    @vite([
        'resources/css/dealer/pages/inventory-gallery.css',
        'resources/js/dealer/pages/inventory-gallery.js',
    ])
@endpush

@php
    $vehicleTitle = strtoupper(
        $vehicle->year . ' ' .
        $vehicle->make->name . ' ' .
        $vehicle->makeModel->name .
        ($vehicle->trim ? ' ' . $vehicle->trim : '')
    );
@endphp

@section('page-content')
<main class="main-content" id="mainContent" style="padding:0;overflow:hidden;">
<div class="view-content inventory-view" style="padding:0;">
    @include('dealer.partials.inventory-topbar')

    <div class="ph-page"
         data-url-upload="{{ route('dealer.inventory.vdp.gallery.upload', $vehicle) }}"
         data-url-reorder="{{ route('dealer.inventory.vdp.gallery.reorder', $vehicle) }}"
         data-url-bulk-delete="{{ route('dealer.inventory.vdp.gallery.bulk.destroy', $vehicle) }}"
         data-url-photo-status-base="{{ route('dealer.inventory.vdp.gallery.photo.status',  [$vehicle, '__ID__']) }}"
         data-url-photo-destroy-base="{{ route('dealer.inventory.vdp.gallery.photo.destroy', [$vehicle, '__ID__']) }}"
         data-url-photo-primary-base="{{ route('dealer.inventory.vdp.gallery.photo.primary', [$vehicle, '__ID__']) }}">

        {{-- ══════ TOP HEADER ══════ --}}
        <div class="ph-header">
            <div class="ph-header-title">
                {{ $vehicleTitle }}
                <span>({{ $vehicle->stock_number }})</span>
            </div>
            <div class="ph-header-actions">
                <a href="#" class="ph-btn-outline">
                    <i class="bi bi-box-arrow-up-right"></i> View Listing
                </a>
                <button type="button" class="ph-btn-danger-outline">
                    <i class="bi bi-trash3"></i> Remove Listing
                </button>
            </div>
        </div>

        {{-- ══════ TOOLBAR ══════ --}}
        <div class="ph-toolbar">

            {{-- Go Back --}}
            <a href="{{ route('dealer.inventory.vdp.show', $vehicle) }}" class="ph-go-back">
                <i class="bi bi-arrow-left"></i> Go Back
            </a>

            {{-- Live / Draft count --}}
            <span class="ph-live-count">
                <span class="ph-count-live">{{ $liveCount }} live</span>
                <span style="color:#ddd;margin:0 4px;">/</span>
                <span class="ph-count-draft">{{ $draftCount }} draft</span>
            </span>

            {{-- Upload --}}
            <button type="button" class="ph-btn-upload" id="btnToggleUpload">
                <i class="bi bi-cloud-upload"></i> Upload
            </button>

            {{-- Bulk Edit --}}
            <div class="ph-bulk-wrap">
                <button type="button" class="ph-btn-bulk" id="btnBulkEdit">
                    <i class="bi bi-check2-square"></i> Bulk Edit
                    <i class="bi bi-chevron-down" style="font-size:10px;"></i>
                </button>
                <div class="ph-bulk-menu" id="bulkMenu">
                    <button class="ph-bulk-menu-item" id="btnBulkPublishAll">
                        <i class="bi bi-check-circle green"></i>
                        Publish all photos Live
                    </button>
                    <button class="ph-bulk-menu-item" id="btnBulkDraftAll">
                        <i class="bi bi-clock" style="color:#555;"></i>
                        Set all to Draft
                    </button>
                    <button class="ph-bulk-menu-item" id="btnBulkDelete">
                        <i class="bi bi-trash3 red"></i>
                        Delete all photos
                    </button>
                    <a href="{{ route('dealer.inventory.vdp.gallery.download', $vehicle) }}"
                       class="ph-bulk-menu-item">
                        <i class="bi bi-cloud-download" style="color:#555;"></i>
                        Download Photos
                    </a>
                </div>
            </div>

            {{-- Publish All --}}
            <button type="button" class="ph-btn-publish" id="btnPublishAll">
                <i class="bi bi-check-circle"></i> Publish All
            </button>

            {{-- Prev / Next --}}
            <div class="ph-nav-arrows">
                <button type="button" class="ph-nav-btn" id="btnPrevPhoto">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <button type="button" class="ph-nav-btn" id="btnNextPhoto">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>

            {{-- Undo / Redo (placeholder) --}}
            <div class="ph-toolbar-right" id="toolbarRight">
                <button type="button" class="ph-icon-btn" title="Undo">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </button>
                <button type="button" class="ph-icon-btn" title="Redo">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
            </div>

        </div>{{-- end toolbar --}}

        {{-- ══════ BODY ══════ --}}
        <div class="ph-body">

            {{-- ── LEFT: grid ── --}}
            <div class="ph-grid-side">

                {{-- Hidden file input --}}
                <input type="file" id="fileInput" multiple accept="image/*" style="display:none;">

                {{-- Drop zone --}}
                <div class="ph-dropzone" id="dropZone">
                    <i class="bi bi-cloud-upload"></i>
                    <p>Drag and drop some files here, or click here to select files.</p>
                    <span class="ph-dropzone-hint">Non-image files will be discarded.</span>
                </div>

                {{-- Upload action bar --}}
                <div class="ph-upload-actions" id="uploadActions">
                    <button type="button" class="ph-btn-do-upload" id="btnDoUpload" disabled>
                        <i class="bi bi-cloud-upload"></i> Upload
                    </button>
                    <button type="button" class="ph-btn-cancel-upload" id="btnCancelUpload">
                        <i class="bi bi-x-lg"></i> Cancel
                    </button>
                    <span class="ph-upload-progress" id="uploadProgress"></span>
                </div>

                {{-- Photo grid --}}
                <div class="ph-grid-wrap">
                    <div class="ph-grid" id="photoGrid">

                        @forelse($photos as $photo)
                            @php
                                $photoUrl         = Storage::disk($photo->disk)->url($photo->path);
                                $urlStatus        = route('dealer.inventory.vdp.gallery.photo.status',  [$vehicle, $photo]);
                                $urlPhotoDestroy  = route('dealer.inventory.vdp.gallery.photo.destroy', [$vehicle, $photo]);
                                $urlPhotoPrimary = route('dealer.inventory.vdp.gallery.photo.primary', [$vehicle, $photo]);
                            @endphp
                            <div class="ph-card {{ $loop->first ? 'selected' : '' }} {{ $photo->is_primary ? 'primary' : '' }}"
                                 data-id="{{ $photo->id }}"
                                 data-src="{{ $photoUrl }}"
                                 data-status="{{ $photo->status }}"
                                 data-url-status="{{ $urlStatus }}"
                                 data-url-destroy="{{ $urlPhotoDestroy }}"
                                 data-url-primary="{{ $urlPhotoPrimary }}"
                                 draggable="true">

                                <img class="ph-card-img"
                                     src="{{ $photoUrl }}"
                                     alt="Photo {{ $photo->sort_order }}"
                                     loading="lazy">

                                <div class="ph-card-footer">
                                    <button type="button"
                                            class="ph-status-btn {{ $photo->status === 'draft' ? 'draft' : '' }}"
                                            data-card-id="{{ $photo->id }}">
                                        @if($photo->status === 'live')
                                            <i class="bi bi-check-circle-fill ph-status-icon"></i>
                                        @else
                                            <i class="bi bi-clock ph-status-icon"></i>
                                        @endif
                                        {{ $photo->status === 'live' ? 'Live' : 'Draft' }}
                                        <i class="bi bi-chevron-down ph-status-chevron"></i>
                                    </button>

                                    <div class="ph-card-menu" id="cardMenu{{ $photo->id }}">
                                        @if($photo->status === 'live')
                                            <button class="ph-card-menu-item" data-action="draft" data-id="{{ $photo->id }}">
                                                <i class="bi bi-clock" style="color:#888;"></i> Set to Draft
                                            </button>
                                        @else
                                            <button class="ph-card-menu-item" data-action="live" data-id="{{ $photo->id }}">
                                                <i class="bi bi-check-circle green"></i> Set to Live
                                            </button>
                                        @endif
                                        @if(!$photo->is_primary)
                                            <button class="ph-card-menu-item" data-action="primary" data-id="{{ $photo->id }}">
                                                <i class="bi bi-star-fill" style="color:#f39c12;"></i> Set as Primary
                                            </button>
                                        @endif
                                        <button class="ph-card-menu-item" data-action="delete" data-id="{{ $photo->id }}">
                                            <i class="bi bi-trash3 red"></i> Delete Photo
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="ph-empty" id="photoEmpty" style="grid-column:1/-1;">
                                <i class="bi bi-images"></i>
                                <p>No photos yet. Click <strong>Upload</strong> to add photos.</p>
                            </div>
                        @endforelse

                    </div>
                </div>
            </div>

            {{-- ── RIGHT: preview ── --}}
            <div class="ph-preview-side">
                <div class="ph-preview-wrap">
                    <div class="ph-preview-img-wrap" id="previewImgWrap">
                        @if($photos->isNotEmpty())
                            <img class="ph-preview-img" id="previewImg"
                                 src="{{ Storage::disk($photos->first()->disk)->url($photos->first()->path) }}"
                                 alt="Preview">
                            <div class="ph-preview-empty" id="previewEmpty" style="display:none;">
                                <i class="bi bi-images"></i>
                                <span>No photo selected</span>
                            </div>
                        @else
                            <div class="ph-preview-empty" id="previewEmpty">
                                <i class="bi bi-images"></i>
                                <span>No photos yet</span>
                            </div>
                            <img class="ph-preview-img" id="previewImg" src="" alt="Preview" style="display:none;">
                        @endif
                    </div>

                    <div class="ph-preview-info">
                        <div class="ph-preview-section-title">
                            <i class="bi bi-stack" style="color:#555;"></i> Layers
                        </div>
                        <p class="ph-layers-empty">You haven't added any layers yet.</p>
                    </div>
                </div>
            </div>

        </div>{{-- end ph-body --}}
    </div>{{-- end ph-page --}}
</div>
</main>
@endsection