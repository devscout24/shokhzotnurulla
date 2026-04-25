@extends('layouts.dealer.app')

@section('title', __('Banners Settings') . ' | '. __(config('app.name')))

@push('page-assets')
    @vite([
        'resources/css/dealer/pages/website-settings-banners.css',
        // 'resources/js/dealer/pages/website-settings-banners.js',
    ])
@endpush

@section('page-content')
    <main class="main-content" id="mainContent">
        <div class="page-header">
            <h2 class="view-title">Website Settings</h2>
        </div>

        <div class="view-content" data-view="banners">
            <div class="ws-layout">
                {{-- Sidebar (unchanged) --}}
                <aside class="ws-sidebar">
                    <a href="{{ route('dealer.website.settings.general') }}" class="menu-item">
                        <span class="ws-icon"><i class="bi bi-info-circle"></i></span>
                        <span>General</span>
                    </a>
                    <a href="{{ route('dealer.website.settings.locations') }}" class="menu-item">
                        <span class="ws-icon"><i class="bi bi-geo-alt"></i></span>
                        <span>Locations &amp; Hours</span>
                    </a>
                    <a href="{{ route('dealer.website.settings.banners') }}" class="menu-item active">
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

                {{-- Right content --}}
                <div class="bn-content">
                    <p class="bn-intro">
                        Persistent banners display a banner or text message consistently across all pages,
                        right below the header. It displays text by default—selecting desktop and mobile
                        images will override text. Banners can also be optionally linked to a page/URL.
                    </p>

                    <div class="bn-card">
                        <div class="bn-card-title">Persistent Banner Settings</div>

                        {{-- Text / announcement --}}
                        <div class="bn-row">
                            <div class="bn-label-col">Text / announcement</div>
                            <div class="bn-input-col">
                                <input type="text"
                                       name="banner_text"
                                       class="bn-control"
                                       placeholder="We are closed today due to weather"
                                       value="{{ old('banner_text', $dealer->banner_text) }}">
                            </div>
                        </div>

                        {{-- Title text on banner hover --}}
                        <div class="bn-row">
                            <div class="bn-label-col">Title text on banner hover</div>
                            <div class="bn-input-col">
                                <input type="text"
                                       name="banner_hover_title"
                                       class="bn-control"
                                       placeholder="Click here to learn more"
                                       value="{{ old('banner_hover_title', $dealer->banner_hover_title) }}">
                            </div>
                        </div>

                        {{-- Text color --}}
                        <div class="bn-row">
                            <div class="bn-label-col">Text color</div>
                            <div class="bn-input-col">
                                <div class="bn-color-wrap">
                                    <div class="bn-color-box" id="textColorBox" title="Pick text color">
                                        <div class="bn-color-preview" id="textColorPreview"></div>
                                        <input type="color"
                                               id="textColorPicker"
                                               name="banner_text_color"
                                               value="{{ old('banner_text_color', $dealer->banner_text_color ?? '#ffffff') }}">
                                    </div>
                                    <input type="text"
                                           class="bn-control"
                                           id="textColorHex"
                                           name="banner_text_color_hex"
                                           placeholder="#ffffff"
                                           value="{{ old('banner_text_color', $dealer->banner_text_color ?? '') }}"
                                           maxlength="7">
                                </div>
                            </div>
                        </div>

                        {{-- Background color --}}
                        <div class="bn-row">
                            <div class="bn-label-col">Background color</div>
                            <div class="bn-input-col">
                                <div class="bn-color-wrap">
                                    <div class="bn-color-box" id="bgColorBox" title="Pick background color">
                                        <div class="bn-color-preview" id="bgColorPreview"></div>
                                        <input type="color"
                                               id="bgColorPicker"
                                               name="banner_bg_color"
                                               value="{{ old('banner_bg_color', $dealer->banner_bg_color ?? '#c0392b') }}">
                                    </div>
                                    <input type="text"
                                           class="bn-control"
                                           id="bgColorHex"
                                           name="banner_bg_color_hex"
                                           placeholder="#c0392b"
                                           value="{{ old('banner_bg_color', $dealer->banner_bg_color ?? '') }}"
                                           maxlength="7">
                                </div>
                            </div>
                        </div>

                        {{-- Banner image desktop --}}
                        <div class="bn-row">
                            <div class="bn-label-col">
                                Banner image (desktop) <em>1420x40</em>
                            </div>
                            <div class="bn-input-col">
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button"
                                            class="bn-select-media"
                                            data-target="banner_desktop_media_id"
                                            data-preview="desktopPreview">
                                        <i class="bi bi-image"></i> Select Media
                                    </button>
                                    <button type="button"
                                            class="bn-select-media"
                                            id="btnUploadDesktopImage">
                                        <i class="bi bi-cloud-upload"></i> Upload New
                                    </button>
                                    <input type="file"
                                           id="desktopBannerInput"
                                           class="bn-file-input"
                                           accept="image/*">
                                </div>
                                <input type="hidden" name="banner_desktop_media_id"
                                       id="banner_desktop_media_id"
                                       value="{{ $dealer->banner_desktop_media_id }}">
                                <div class="bn-image-preview mt-2" id="desktopPreviewContainer">
                                    @if($dealer->bannerDesktopMedia)
                                        <img src="{{ $dealer->bannerDesktopMedia->url }}"
                                             alt="Preview"
                                             style="max-width: 400px; height: auto; border-radius: 4px; border: 1px solid #ddd;"
                                             class="preview-img"
                                             id="desktopPreview">
                                    @else
                                        <img src="" alt="Preview"
                                             style="max-width: 280px; max-height: 40px; display: none;"
                                             id="desktopPreview">
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Banner image mobile --}}
                        <div class="bn-row">
                            <div class="bn-label-col">
                                Banner image (mobile) <em>600x100</em>
                            </div>
                            <div class="bn-input-col">
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button"
                                            class="bn-select-media"
                                            data-target="banner_mobile_media_id"
                                            data-preview="mobilePreview">
                                        <i class="bi bi-image"></i> Select Media
                                    </button>
                                    <button type="button"
                                            class="bn-select-media"
                                            id="btnUploadMobileImage">
                                        <i class="bi bi-cloud-upload"></i> Upload New
                                    </button>
                                    <input type="file"
                                           id="mobileBannerInput"
                                           class="bn-file-input"
                                           accept="image/*">
                                </div>
                                <input type="hidden" name="banner_mobile_media_id"
                                       id="banner_mobile_media_id"
                                       value="{{ $dealer->banner_mobile_media_id }}">
                                <div class="bn-image-preview mt-2" id="mobilePreviewContainer">
                                    @if($dealer->bannerMobileMedia)
                                        <img src="{{ $dealer->bannerMobileMedia->url }}"
                                             alt="Preview"
                                             style="max-width: 400px; height: auto; border-radius: 4px; border: 1px solid #ddd;"
                                             class="preview-img"
                                             id="mobilePreview">
                                    @else
                                        <img src="" alt="Preview"
                                             style="max-width: 200px; max-height: 60px; display: none;"
                                             id="mobilePreview">
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Save --}}
                        <div class="bn-save-wrap">
                            <button type="button" class="bn-btn-save" id="btnSaveBanner">
                                &#10003; Save
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- Media Selection Modal --}}
    <div class="sm-overlay" id="selectMediaModal">
        <div class="sm-modal">
            <div class="sm-header">
                <h5>Select Media</h5>
                <button class="sm-btn-close" id="btnCloseSelectMedia">&times;</button>
            </div>

            <div class="sm-toolbar">
                <div class="sm-toolbar-left">
                    <span>Select image:</span>
                </div>
                <div class="sm-toolbar-right">
                    <input type="text" class="sm-search" id="smSearch" placeholder="Search media">
                    <button type="button" class="sm-btn-add-media" id="btnToggleUpload">+ Add Media</button>
                </div>
            </div>

            <div class="sm-dropzone" id="smDropzone">
                <span class="sm-dropzone-icon">&#9729;</span>
                <p>Drag and drop some files here, or click here to select files. Non image or video files will be discarded.</p>
                <input type="file" id="smFileInput" style="display:none;" multiple accept="image/*,video/*">
            </div>
            <div class="sm-upload-actions" id="smUploadActions">
                <button type="button" class="sm-btn-upload" id="btnDoUpload"><i class="bi bi-upload"></i> Upload</button>
                <button type="button" class="sm-btn-close-upload" id="btnCloseUpload">✕ Close</button>
            </div>

            <div class="sm-display-wrap">
                <span>Display</span>
                <select class="sm-display-select" id="smDisplayMode">
                    <option value="compact" selected>Compact</option>
                    <option value="list">List</option>
                </select>
            </div>

            <div class="sm-body">
                <div class="sm-grid compact" id="smGrid"></div>
            </div>

            <div class="sm-footer">
                <div class="sm-footer-actions">
                    <button type="button" class="sm-btn-select" id="btnConfirmMedia">✓ Select Media</button>
                </div>
                <div class="sm-pagination" id="smPagination"></div>
                <div class="sm-showing" id="smShowing"></div>
            </div>
        </div>
    </div>

    {{-- Hidden trigger target tracker --}}
    {{-- <input type="hidden" id="smTargetInput"> --}}
@endsection

@push('page-scripts')
    <script>
        // CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Toastr helper
        function showToast(type, message) {
            if (type === 'success') toastr.success(message);
            else toastr.error(message);
        }

        // Color picker sync (same as before)
        function initColorPicker(pickerId, hexId, previewId) {
            const picker = document.getElementById(pickerId);
            const hex = document.getElementById(hexId);
            const preview = document.getElementById(previewId);
            function updatePreview(val) {
                preview.style.background = val || '#fff';
            }
            updatePreview(picker.value);
            picker.addEventListener('input', () => {
                hex.value = picker.value;
                updatePreview(picker.value);
            });
            hex.addEventListener('input', () => {
                const val = hex.value.trim();
                if (/^#[0-9a-fA-F]{6}$/.test(val)) {
                    picker.value = val;
                    updatePreview(val);
                }
            });
            document.getElementById(pickerId.replace('Picker','Box')).addEventListener('click', () => picker.click());
        }
        initColorPicker('textColorPicker', 'textColorHex', 'textColorPreview');
        initColorPicker('bgColorPicker', 'bgColorHex', 'bgColorPreview');

        // Upload new image (desktop/mobile)
        async function uploadAndSetImage(file, targetHiddenId, previewImgId) {
            const formData = new FormData();
            formData.append('files[]', file);
            formData.append('_token', csrfToken);

            const response = await fetch('{{ route("dealer.website.media.upload") }}', {
                method: 'POST',
                body: formData,
            });
            const result = await response.json();
            if (result.success && result.media && result.media.length) {
                const media = result.media[0];
                document.getElementById(targetHiddenId).value = media.id;

                // Update preview
                const previewImg = document.getElementById(previewImgId);
                if (previewImg) {
                    previewImg.src = media.url;
                    previewImg.style.display = 'block';
                }
                showToast('success', 'Image uploaded and selected.');
            } else {
                showToast('error', 'Upload failed.');
            }
        }

        document.getElementById('desktopBannerInput').addEventListener('change', function(e) {
            if (this.files.length) {
                uploadAndSetImage(this.files[0], 'banner_desktop_media_id', 'desktopPreview');
            }
        });
        document.getElementById('mobileBannerInput').addEventListener('change', function(e) {
            if (this.files.length) {
                uploadAndSetImage(this.files[0], 'banner_mobile_media_id', 'mobilePreview');
            }
        });
        document.getElementById('btnUploadDesktopImage').addEventListener('click', () => document.getElementById('desktopBannerInput').click());
        document.getElementById('btnUploadMobileImage').addEventListener('click', () => document.getElementById('mobileBannerInput').click());

        // Media selection modal – real API
        let currentMediaTarget = null; // id of hidden input we are filling
        let mediaPage = 1;
        let mediaSearch = '';
        let selectedMediaId = null;
        let allMedia = []; // store current page items

        const smOverlay = document.getElementById('selectMediaModal');
        const smGrid = document.getElementById('smGrid');
        const smPagination = document.getElementById('smPagination');
        const smShowing = document.getElementById('smShowing');

        async function loadMedia() {
            const url = `{{ route("dealer.website.media.list") }}?page=${mediaPage}&search=${encodeURIComponent(mediaSearch)}&type=image`;
            const response = await fetch(url);
            const data = await response.json();
            allMedia = data.data; // array of media objects
            renderMediaGrid();
            renderPagination(data);
            smShowing.textContent = `Showing ${data.from || 0} - ${data.to || 0} of ${data.total || 0}`;
        }

        function renderMediaGrid() {
            smGrid.innerHTML = '';
            allMedia.forEach(media => {
                const div = document.createElement('div');
                div.className = 'sm-item' + (selectedMediaId === media.id ? ' selected' : '');
                div.dataset.id = media.id;
                div.innerHTML = `<img src="${media.url}" alt="${media.original_name}" loading="lazy">`;
                div.addEventListener('click', () => {
                    selectedMediaId = media.id;
                    document.querySelectorAll('.sm-item').forEach(el => el.classList.remove('selected'));
                    div.classList.add('selected');
                });
                smGrid.appendChild(div);
            });
        }

        function renderPagination(data) {
            smPagination.innerHTML = '';
            const totalPages = data.last_page || 1;
            const prev = document.createElement('button');
            prev.className = 'sm-page-btn';
            prev.innerHTML = '‹';
            prev.disabled = mediaPage === 1;
            prev.addEventListener('click', () => { mediaPage--; loadMedia(); });
            smPagination.appendChild(prev);

            for (let i = 1; i <= totalPages; i++) {
                const btn = document.createElement('button');
                btn.className = 'sm-page-btn' + (i === mediaPage ? ' active' : '');
                btn.textContent = i;
                btn.addEventListener('click', () => { mediaPage = i; loadMedia(); });
                smPagination.appendChild(btn);
            }

            const next = document.createElement('button');
            next.className = 'sm-page-btn';
            next.innerHTML = '›';
            next.disabled = mediaPage === totalPages;
            next.addEventListener('click', () => { mediaPage++; loadMedia(); });
            smPagination.appendChild(next);
        }

        // Open modal with target field
        function openSelectMedia(targetId) {
            currentMediaTarget = targetId;
            selectedMediaId = null;
            mediaPage = 1;
            mediaSearch = '';
            document.getElementById('smSearch').value = '';
            loadMedia();
            smOverlay.classList.add('active');
        }

        // Close modal
        function closeSelectMedia() {
            smOverlay.classList.remove('active');
        }

        // Event listeners for media modal
        document.querySelectorAll('.bn-select-media').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const target = btn.dataset.target;
                if (target) openSelectMedia(target);
            });
        });
        document.getElementById('btnCloseSelectMedia').addEventListener('click', closeSelectMedia);
        smOverlay.addEventListener('click', e => { if (e.target === smOverlay) closeSelectMedia(); });
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSelectMedia(); });

        // Search
        document.getElementById('smSearch').addEventListener('input', function() {
            mediaSearch = this.value;
            mediaPage = 1;
            loadMedia();
        });

        // Display mode
        document.getElementById('smDisplayMode').addEventListener('change', function() {
            smGrid.className = 'sm-grid ' + this.value;
        });

        // Confirm selection
        document.getElementById('btnConfirmMedia').addEventListener('click', () => {
            if (!selectedMediaId) {
                alert('Please select an image.');
                return;
            }
            const selected = allMedia.find(m => m.id === selectedMediaId);
            if (selected && currentMediaTarget) {
                // Update hidden field
                document.getElementById(currentMediaTarget).value = selected.id;

                // Update preview
                const previewId = currentMediaTarget === 'banner_desktop_media_id' ? 'desktopPreview' : 'mobilePreview';
                const previewImg = document.getElementById(previewId);
                if (previewImg) {
                    previewImg.src = selected.url;
                    previewImg.style.display = 'block';
                }

                closeSelectMedia();
            }
        });

        // Upload new media from modal
        function showUploadZone() {
            document.getElementById('smDropzone').classList.add('visible');
            document.getElementById('smUploadActions').classList.add('visible');
        }
        function hideUploadZone() {
            document.getElementById('smDropzone').classList.remove('visible');
            document.getElementById('smUploadActions').classList.remove('visible');
        }
        document.getElementById('btnToggleUpload').addEventListener('click', () => {
            const dz = document.getElementById('smDropzone');
            dz.classList.contains('visible') ? hideUploadZone() : showUploadZone();
        });
        document.getElementById('btnCloseUpload').addEventListener('click', hideUploadZone);

        const dropzone = document.getElementById('smDropzone');
        dropzone.addEventListener('click', () => document.getElementById('smFileInput').click());
        dropzone.addEventListener('dragover', e => { e.preventDefault(); dropzone.classList.add('dragover'); });
        dropzone.addEventListener('dragleave', () => dropzone.classList.remove('dragover'));
        dropzone.addEventListener('drop', e => {
            e.preventDefault();
            dropzone.classList.remove('dragover');
            handleFiles(e.dataTransfer.files);
        });
        document.getElementById('smFileInput').addEventListener('change', function() {
            handleFiles(this.files);
        });
        document.getElementById('btnDoUpload').addEventListener('click', () => document.getElementById('smFileInput').click());

        async function handleFiles(files) {
            const formData = new FormData();
            for (let file of files) {
                formData.append('files[]', file);
            }
            formData.append('_token', csrfToken);
            try {
                const response = await fetch('{{ route("dealer.website.media.upload") }}', {
                    method: 'POST',
                    body: formData,
                });
                const result = await response.json();
                if (result.success) {
                    showToast('success', result.message);
                    hideUploadZone();
                    loadMedia(); // refresh grid
                } else {
                    showToast('error', 'Upload failed.');
                }
            } catch (err) {
                showToast('error', 'Upload failed.');
            }
        }

        // Save banner settings
        document.getElementById('btnSaveBanner').addEventListener('click', function () {
            const formData = new FormData();
            formData.append('banner_text', document.querySelector('[name="banner_text"]').value);
            formData.append('banner_hover_title', document.querySelector('[name="banner_hover_title"]').value);
            formData.append('banner_text_color', document.getElementById('textColorPicker').value);
            formData.append('banner_bg_color', document.getElementById('bgColorPicker').value);
            formData.append('banner_desktop_media_id', document.getElementById('banner_desktop_media_id').value);
            formData.append('banner_mobile_media_id', document.getElementById('banner_mobile_media_id').value);
            formData.append('_token', csrfToken);
            formData.append('_method', 'PATCH');

            fetch('{{ route("dealer.website.settings.banners.update") }}', {
                method: 'POST', // because we use _method PATCH
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('success', data.message);
                } else {
                    showToast('error', 'Failed to save.');
                }
            })
            .catch(() => showToast('error', 'An error occurred.'));
        });
    </script>
@endpush
