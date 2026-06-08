@extends('layouts.frontend.app')

@section('title', $page->meta_title ?: $page->title . ' | ' . config('app.name'))

@push('page-assets')
    <style>
        :root {
            --of-primary: #ce4f4b;
            --of-secondary: #1a1f36;
            --of-text: #4f566b;
            --of-border: #e0e6ed;
        }

        .dynamic-content-wrapper {
            padding: clamp(20px, 5vw, 60px) 0;
            min-height: 400px;
            color: var(--of-text);
            line-height: 1.6;
        }

        #rendered-content {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .rendered-block {
            margin-bottom: 24px;
            position: relative;
            width: 100%;
            word-wrap: break-word;
            overflow-wrap: break-word;
            transition: all 0.3s ease;
        }

        /* Responsive Layouts */
        .rendered-container {
            display: flex;
            width: 100%;
            flex-wrap: wrap;
            padding: 0 15px;
        }

        .rendered-2col, .rendered-3col {
            display: grid;
            gap: 30px;
            width: 100%;
            margin-bottom: 30px;
        }

        .rendered-2col {
            grid-template-columns: repeat(2, 1fr);
        }

        .rendered-3col {
            grid-template-columns: repeat(3, 1fr);
        }

        @media (max-width: 991px) {
            .rendered-3col {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 767px) {
            .rendered-2col, .rendered-3col {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            .dynamic-content-wrapper {
                padding: 30px 0;
            }
        }

        .rendered-col {
            min-width: 0; /* Fix flex/grid overflow */
            width: 100%;
        }

        /* Typography */
        .rendered-block h1, .rendered-block h2, .rendered-block h3, 
        .rendered-block h4, .rendered-block h5, .rendered-block h6 {
            color: var(--of-secondary);
            font-weight: 800;
            margin-bottom: 0.5em;
            line-height: 1.2;
        }

        .rendered-block p {
            margin-bottom: 1em;
        }

        /* Images & Media */
        img.rendered-img {
            max-width: 100%;
            height: auto !important;
            display: block;
            border-radius: 8px;
        }

        .rendered-video-wrapper {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
            border-radius: 12px;
            background: #000;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .rendered-video-wrapper iframe, 
        .rendered-video-wrapper video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }

        /* Buttons */
        .rendered-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 28px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 700;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
            text-align: center;
        }

        .rendered-btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            color: inherit;
        }

        /* Specialized Blocks Placeholders */
        .block-placeholder {
            padding: 40px;
            background: #f8f9fa;
            border: 2px dashed var(--of-border);
            border-radius: 12px;
            text-align: center;
            color: #ce4f4b;
        }

        .block-placeholder i {
            font-size: 32px;
            margin-bottom: 15px;
            display: block;
        }

        .tradein-card {
            background-color: #166b87;
            border-radius: 10px;
            padding: 20px 16px;
            text-align: center;
            color: white;
        }
        .tradein-card h3 {
            font-size: 13px;
            font-weight: 500;
            line-height: 1.5;
            margin: 0;
            color: white;
        }
        .tradein-card img {
            width: 56px;
            opacity: 0.6;
            display: block;
            margin: 12px auto;
        }
        .tradein-card .btn-tradein {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.45);
            border-radius: 6px;
            padding: 6px 14px;
            text-decoration: none;
            margin-top: 4px;
            background: transparent;
            transition: background 0.15s ease;
        }
        .tradein-card .btn-tradein:hover {
            background: rgba(255, 255, 255, 0.12);
            color: white;
        }

        /* ── Sidebar: New Arrivals ──────────────────────────── */
        .sidebar-divider {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--of-secondary);
            padding: 16px 0 8px;
            margin: 0 12px;
            border-bottom: 2px solid var(--of-primary);
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .sidebar-divider::before {
            content: '\f0ca';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            font-size: 12px;
            color: var(--of-primary);
        }

        .arrival-card {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            text-decoration: none;
            color: var(--of-text);
            border-bottom: 1px solid var(--of-border);
            transition: background 0.15s ease;
        }
        .arrival-card:hover {
            background: #f8f9fa;
            color: var(--of-secondary);
        }

        .arrival-img-wrap {
            width: 60px;
            height: 45px;
            border-radius: 4px;
            overflow: hidden;
            flex-shrink: 0;
            background: #f0f1f3;
        }
        .arrival-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .arrival-info {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .arrival-title {
            font-size: 13px;
            font-weight: 600;
            color: var(--of-secondary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.3;
        }
        .arrival-price {
            font-size: 12px;
            font-weight: 700;
            color: var(--of-primary);
        }

        /* iOS Safari Fixes */
        @supports (-webkit-touch-callout: none) {
            .dynamic-content-wrapper {
                -webkit-overflow-scrolling: touch;
            }
        }
    </style>
@endpush

@section('page-content')
    {{-- Mobile Header Spacer --}}
    <div class="d-block d-xl-none" style="height: 63px;"></div>
    
    <div class="dynamic-content-wrapper row justify-content-center">

        <div class="col-2">
            <div class="sidebar-divider ms-3">{!! $dealerName !!} / Blog / {!! $page->title !!}</div>
            <hr/>
            <p class="sidebar-heading ms-3">Latest blogs</p>
            <ol class="blog-list ms-3">
                @foreach($latestBlogs as $blog)
                    <li>
                        <a href="{{ route('frontend.blog.show', $blog->slug) }}">{{ $blog->title }}</a>
                    </li>
                @endforeach
            </ol>
        </div>
        <div class="container-fluid px-4 col-8">
            <div id="rendered-content">
                <!-- Content will be rendered here via JS -->
            </div>
        </div>
        <div class="col-2" >
            <div class="tradein-card m-3">
                <h5>Trading in? Find out your car's trade-in value today.</h5>
                <img src="{{ asset('assets/frontend/img/streamlinehq-car-tool-keys-transportation-white-200.png') }}"
                    alt="Car keys icon" loading="lazy">
                <a href="javascript:void(0)"
                class="btn-tradein"
                title="Get your trade-in value"
                data-bs-toggle="offcanvas"
                data-bs-target="#getTrade"
                aria-controls="offcanvasRight">
                    Get your trade-in value
                    <i class="fa-solid fa-angle-right" aria-hidden="true"></i>
                </a>
            </div>
            <div class="sidebar-divider">New arrivals</div>
            @foreach($newArrivals as $vehicle)
                <a href="{{ route('frontend.inventory.show', $vehicle->slug) }}" class="arrival-card">
                    <div class="arrival-img-wrap">
                        <img src="{{ $vehicle->primaryPhoto?->url ?? asset('images/placeholder-car.jpg') }}"
                            alt="{{ $vehicle->make->name }} {{ $vehicle->makeModel->name }}">
                    </div>
                    <div class="arrival-info">
                        <span class="arrival-title">{{ $vehicle->year }} {{ $vehicle->make->name }} {{ $vehicle->makeModel->name }}</span>
                        <span class="arrival-price">${{ number_format($vehicle->list_price) }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endsection

@push('page-modals')
    @include('frontend.offcanvas.get-trade-in')
    @include('frontend.offcanvas.unlock-eprice')
@endpush

@push('page-scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Prevent iOS zoom on input focus by ensuring 16px font size
        const style = document.createElement('style');
        style.textContent = `@media screen and (max-width: 767px) { 
            input, select, textarea { font-size: 16px !important; } 
            .rendered-btn { min-height: 44px; }
        }`;
        document.head.appendChild(style);

        let contentData = [];
        try {
            const rawContent = `{!! addslashes($page->content) !!}`;
            contentData = JSON.parse(rawContent);
        } catch (e) {
            console.error("Failed to parse content JSON:", e);
            contentData = {!! $page->content ?: '[]' !!};
        }

        const container = document.getElementById('rendered-content');
        
        if (!contentData || contentData.length === 0) {
            container.innerHTML = '<div class="text-center py-5"><i class="fas fa-file-alt fa-3x mb-3 text-muted"></i><h3>No content published yet.</h3></div>';
            return;
        }

        renderContent(contentData, container);
    });

    function renderContent(data, target) {
        data.forEach(item => {
            const el = createBlockElement(item);
            if (el) target.appendChild(el);
        });
    }

    function createBlockElement(data) {
        const div = document.createElement('div');
        div.className = 'rendered-block type-' + data.type;
        
        // Common Styles Application
        if (data.blockMargin) div.style.margin = data.blockMargin;
        if (data.blockPadding) div.style.padding = data.blockPadding;
        
        switch(data.type) {
            case 'heading':
                const h = document.createElement(data.level || 'h2');
                h.innerText = data.text || '';
                applyTypography(h, data);
                div.appendChild(h);
                break;
                
            case 'text':
                const p = document.createElement('p');
                p.innerText = data.text || '';
                applyTypography(p, data);
                div.appendChild(p);
                break;

            case 'span':
                const span = document.createElement('span');
                span.innerText = data.text || '';
                applyTypography(span, data);
                div.appendChild(span);
                break;
                
            case 'button':
                const a = document.createElement('a');
                a.className = 'rendered-btn';
                a.innerText = data.text || 'Button';
                a.href = data.href || '#';
                if (data.newTab) a.target = '_blank';
                if (data.backgroundColor) a.style.backgroundColor = data.backgroundColor;
                if (data.color) a.style.color = data.color;
                if (data.borderRadius) a.style.borderRadius = data.borderRadius + 'px';
                if (data.fontSize) a.style.fontSize = data.fontSize + 'px';
                if (data.fullWidth) a.style.width = '100%';
                div.appendChild(a);
                break;

            case 'image':
                const img = document.createElement('img');
                img.className = 'rendered-img';
                img.src = data.src || '';
                if (data.width) img.style.width = data.width.toString().includes('%') ? data.width : data.width + 'px';
                if (data.height && data.height !== 'auto') img.style.height = data.height + 'px';
                if (data.borderRadius) img.style.borderRadius = data.borderRadius + 'px';
                if (data.align === 'center') {
                    img.style.marginLeft = 'auto';
                    img.style.marginRight = 'auto';
                }
                div.appendChild(img);
                break;

            case 'container':
                div.className += ' rendered-container';
                if (data.backgroundColor) div.style.backgroundColor = data.backgroundColor;
                if (data.padding) div.style.padding = data.padding;
                if (data.borderRadius) div.style.borderRadius = data.borderRadius + 'px';
                
                const containerBlocks = data.blocks || data.content || [];
                renderContent(containerBlocks, div);
                break;

            case '2col':
            case '3col':
                div.className += ' rendered-' + data.type;
                if (data.gap) div.style.gap = data.gap + 'px';
                
                if (data.columns && Array.isArray(data.columns)) {
                    data.columns.forEach(colData => {
                        const col = document.createElement('div');
                        col.className = 'rendered-col';
                        const children = Array.isArray(colData) ? colData : (colData.content || []);
                        renderContent(children, col);
                        div.appendChild(col);
                    });
                }
                break;

            case 'divider':
                const hr = document.createElement('hr');
                hr.className = 'rendered-divider';
                hr.style.margin = (data.spacing || 20) + 'px 0';
                if (data.color) hr.style.borderColor = data.color;
                if (data.width) hr.style.width = data.width + '%';
                div.appendChild(hr);
                break;

            case 'spacer':
                const spacer = document.createElement('div');
                spacer.style.height = (data.height || 20) + 'px';
                div.appendChild(spacer);
                break;

            case 'video':
                const vWrap = document.createElement('div');
                vWrap.className = 'rendered-video-wrapper';
                
                if (data.host === 'youtube') {
                    const ifr = document.createElement('iframe');
                    let ytId = data.url;
                    const match = data.url.match(/^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/);
                    if (match && match[2].length === 11) ytId = match[2];
                    ifr.src = `https://www.youtube.com/embed/${ytId}`;
                    vWrap.appendChild(ifr);
                } else {
                    const video = document.createElement('video');
                    video.src = data.url || '';
                    video.controls = true;
                    vWrap.appendChild(video);
                }
                div.appendChild(vWrap);
                break;

            case 'html':
                const hDiv = document.createElement('div');
                hDiv.innerHTML = data.code || '';
                div.appendChild(hDiv);
                break;

            case 'iframe':
                const ifr = document.createElement('iframe');
                ifr.src = data.src || '';
                ifr.style.width = '100%';
                ifr.style.height = (data.height || 400) + 'px';
                ifr.style.border = 'none';
                div.appendChild(ifr);
                break;

            // Specialized Placeholders
            case 'inventory':
            case 'form':
            case 'search':
            case 'map':
                div.innerHTML = `<div class="block-placeholder">
                    <i class="fas fa-${data.type === 'inventory' ? 'car' : (data.type === 'form' ? 'file-alt' : (data.type === 'search' ? 'search' : 'map-marker-alt'))}"></i>
                    <strong>${data.type.toUpperCase()} BLOCK</strong>
                    <p class="small m-0">This feature is being optimized for mobile.</p>
                </div>`;
                break;

            default:
                console.warn('Unknown block type:', data.type);
                return null;
        }
        
        return div;
    }

    function applyTypography(el, data) {
        if (data.color) el.style.color = data.color;
        if (data.fontSize) el.style.fontSize = data.fontSize.toString().includes('px') ? data.fontSize : data.fontSize + 'px';
        if (data.textAlign) el.style.textAlign = data.textAlign;
        if (data.fontWeight) el.style.fontWeight = data.fontWeight;
        if (data.lineHeight) el.style.lineHeight = data.lineHeight;
    }
</script>
@endpush
