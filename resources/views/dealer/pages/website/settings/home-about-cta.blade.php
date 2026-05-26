@extends('layouts.dealer.app')

@section('title', __('Home About/CTA Settings') . ' | '. __(config('app.name')))

@push('page-assets')
    @vite([
        'resources/css/dealer/pages/website-settings-general.css',
        'resources/css/dealer/pages/website-settings-home-about-cta.css',
    ])
@endpush

@section('page-content')
    <main class="main-content" id="mainContent">
        <div class="page-header">
            <h2 class="view-title">Website Settings</h2>
        </div>

        <div class="view-content" data-view="home-about-cta">
            <div class="ws-layout">

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
                    <a href="{{ route('dealer.website.settings.home-about-cta') }}" class="menu-item active">
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
                    <a href="{{ route('dealer.website.settings.video') }}" class="menu-item">
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
                    <div class="ha-card">
                        <div class="ha-card-title">Home About/CTA Content</div>

                        <form id="homeAboutCtaForm" action="{{ route('dealer.website.settings.home-about-cta.update') }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <div class="ha-section">
                                <div class="ha-section-title">About Section</div>
                                <div class="ha-grid">
                                    <div>
                                        <label class="form-label">Eyebrow</label>
                                        <input class="form-control form-control-sm ha-input" name="about_eyebrow" value="{{ old('about_eyebrow', data_get($content, 'about.eyebrow')) }}">
                                    </div>
                                    <div>
                                        <label class="form-label">Heading</label>
                                        <input class="form-control form-control-sm ha-input" name="about_heading" value="{{ old('about_heading', data_get($content, 'about.heading')) }}">
                                    </div>
                                    <div>
                                        <label class="form-label">Image Upload</label>
                                        <input class="form-control form-control-sm ha-input" type="file" name="about_image_file" accept="image/*">
                                        <div class="ha-help">Current: {{ data_get($content, 'about.image_url') }}</div>
                                    </div>
                                    <div>
                                        <label class="form-label">Image Alt</label>
                                        <input class="form-control form-control-sm ha-input" name="about_image_alt" value="{{ old('about_image_alt', data_get($content, 'about.image_alt')) }}">
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="form-label">Paragraph 1 (HTML allowed)</label>
                                    <textarea class="form-control form-control-sm ha-textarea" name="about_paragraph_1">{!! old('about_paragraph_1', data_get($content, 'about.paragraphs.0')) !!}</textarea>
                                </div>
                                <div class="mt-3">
                                    <label class="form-label">Paragraph 2 (HTML allowed)</label>
                                    <textarea class="form-control form-control-sm ha-textarea" name="about_paragraph_2">{!! old('about_paragraph_2', data_get($content, 'about.paragraphs.1')) !!}</textarea>
                                    <div class="ha-help">Use links here for cars/trucks/SUVs.</div>
                                </div>
                            </div>

                            <div class="ha-section">
                                <div class="ha-section-title">Stats Cards</div>
                                <div class="ha-grid-3">
                                    @for ($i = 0; $i < 4; $i++)
                                        <div class="border rounded p-3">
                                            <div class="fw-semibold mb-2">Card {{ $i + 1 }}</div>
                                            <div class="mb-2">
                                                <label class="form-label">Icon Class</label>
                                                <input class="form-control form-control-sm" name="stats_{{ $i + 1 }}_icon" value="{{ old('stats_' . ($i + 1) . '_icon', data_get($content, 'stats.' . $i . '.icon')) }}">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">Title</label>
                                                <input class="form-control form-control-sm" name="stats_{{ $i + 1 }}_title" value="{{ old('stats_' . ($i + 1) . '_title', data_get($content, 'stats.' . $i . '.title')) }}">
                                            </div>
                                            <div>
                                                <label class="form-label">Text</label>
                                                <textarea class="form-control form-control-sm" rows="3" name="stats_{{ $i + 1 }}_text">{{ old('stats_' . ($i + 1) . '_text', data_get($content, 'stats.' . $i . '.text')) }}</textarea>
                                            </div>
                                        </div>
                                    @endfor
                                </div>
                            </div>

                            <div class="ha-section">
                                <div class="ha-section-title">Info Card</div>
                                <div class="ha-grid">
                                    <div>
                                        <label class="form-label">Icon Class</label>
                                        <input class="form-control form-control-sm ha-input" name="card_icon" value="{{ old('card_icon', data_get($content, 'card.icon')) }}">
                                    </div>
                                    <div>
                                        <label class="form-label">Title</label>
                                        <input class="form-control form-control-sm ha-input" name="card_title" value="{{ old('card_title', data_get($content, 'card.title')) }}">
                                    </div>
                                    <div>
                                        <label class="form-label">Image Upload</label>
                                        <input class="form-control form-control-sm ha-input" type="file" name="card_image_file" accept="image/*">
                                        <div class="ha-help">Current: {{ data_get($content, 'card.image_url') }}</div>
                                    </div>
                                    <div>
                                        <label class="form-label">Image Alt</label>
                                        <input class="form-control form-control-sm ha-input" name="card_image_alt" value="{{ old('card_image_alt', data_get($content, 'card.image_alt')) }}">
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="form-label">Body</label>
                                    <textarea class="form-control form-control-sm ha-textarea" name="card_text">{{ old('card_text', data_get($content, 'card.text')) }}</textarea>
                                </div>
                            </div>

                            <div class="ha-section">
                                <div class="ha-section-title">CTA Blocks</div>
                                <div class="ha-grid">
                                    @for ($i = 0; $i < 2; $i++)
                                        <div class="border rounded p-3">
                                            <div class="fw-semibold mb-2">CTA {{ $i + 1 }}</div>
                                            <div class="mb-2">
                                                <label class="form-label">Title</label>
                                                <input class="form-control form-control-sm" name="cta_{{ $i + 1 }}_title" value="{{ old('cta_' . ($i + 1) . '_title', data_get($content, 'ctas.' . $i . '.title')) }}">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">Body</label>
                                                <textarea class="form-control form-control-sm" rows="4" name="cta_{{ $i + 1 }}_text">{{ old('cta_' . ($i + 1) . '_text', data_get($content, 'ctas.' . $i . '.text')) }}</textarea>
                                            </div>
                                            <div>
                                                <label class="form-label">Link URL</label>
                                                <input class="form-control form-control-sm" name="cta_{{ $i + 1 }}_link" value="{{ old('cta_' . ($i + 1) . '_link', data_get($content, 'ctas.' . $i . '.link_url')) }}">
                                            </div>
                                        </div>
                                    @endfor
                                </div>
                            </div>

                            <div class="ha-actions">
                                <button type="button" class="ha-btn-save" id="btnSaveHomeAboutCta">&#10003; Save</button>
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
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function showToast(type, message) {
        if (type === 'success') {
            toastr.success(message);
        } else if (type === 'error') {
            toastr.error(message);
        }
    }

    document.getElementById('btnSaveHomeAboutCta').addEventListener('click', function () {
        const form = document.getElementById('homeAboutCtaForm');
        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('success', data.message);
            } else {
                showToast('error', data.message || 'An error occurred while saving.');
            }
        })
        .catch(error => {
            console.error(error);
            showToast('error', 'An error occurred. Please try again.');
        });
    });
</script>
@endpush
