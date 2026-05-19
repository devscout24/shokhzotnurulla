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

    <div class="dynamic-content-wrapper">
        <div class="container-fluid px-4">
            <div id="rendered-content">
                <!-- Content will be rendered here via JS -->
            </div>
        </div>
    </div>
@endsection

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
