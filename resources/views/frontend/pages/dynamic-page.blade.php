@extends('layouts.frontend.app')

@section('title', $page->meta_title ?: $page->title . ' | ' . config('app.name'))

@push('page-assets')
    <style>
        .dynamic-content-wrapper {
            padding: 40px 0;
            min-height: 400px;
        }
        /* Basic block styles for frontend */
        .rendered-block { margin-bottom: 20px; position: relative; }
        .rendered-container { display: flex; width: 100%; }
        .rendered-2col, .rendered-3col { display: flex; gap: 20px; }
        .rendered-col { flex: 1; min-width: 0; }
        img.rendered-img { max-width: 100%; height: auto; display: block; }
        .rendered-btn { display: inline-block; padding: 10px 20px; text-decoration: none; border-radius: 4px; }
        .rendered-spacer { width: 100%; }
        .rendered-divider { border: 0; border-top: 1px solid #eee; margin: 20px 0; }
    </style>
@endpush

@section('page-content')
    <div class="dynamic-content-wrapper">
        <div class="container" id="rendered-content">
            <!-- Content will be rendered here via JS -->
        </div>
    </div>
@endsection

@push('page-scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const content = {!! $page->content ?: '[]' !!};
        const container = document.getElementById('rendered-content');
        
        if (!content || content.length === 0) {
            container.innerHTML = '<div class="text-center py-5"><h3>No content found for this page.</h3></div>';
            return;
        }

        renderContent(content, container);
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
        
        switch(data.type) {
            case 'heading':
                const h = document.createElement('h1');
                h.innerText = data.text || '';
                if (data.color) h.style.color = data.color;
                if (data.fontSize) {
                    const size = String(data.fontSize);
                    h.style.fontSize = size.endsWith('px') || size.endsWith('%') || size.endsWith('rem') ? size : size + 'px';
                }
                if (data.textAlign) h.style.textAlign = data.textAlign;
                div.appendChild(h);
                break;
                
            case 'text':
                const p = document.createElement('p');
                p.innerText = data.text || '';
                if (data.color) p.style.color = data.color;
                if (data.fontSize) {
                    const size = String(data.fontSize);
                    p.style.fontSize = size.endsWith('px') || size.endsWith('%') || size.endsWith('rem') ? size : size + 'px';
                }
                if (data.textAlign) p.style.textAlign = data.textAlign;
                div.appendChild(p);
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
                if (data.fullWidth) a.style.display = 'block';
                div.appendChild(a);
                break;

            case 'image':
                const img = document.createElement('img');
                img.className = 'rendered-img';
                img.src = data.src || '';
                if (data.width) {
                    const w = String(data.width);
                    img.style.width = w.endsWith('%') || w.endsWith('px') || w.endsWith('vw') ? w : w + 'px';
                }
                if (data.height && data.height !== 'auto') {
                    const h = String(data.height);
                    img.style.height = h.endsWith('px') || h.endsWith('%') ? h : h + 'px';
                } else {
                    img.style.height = 'auto';
                }
                if (data.borderRadius) img.style.borderRadius = data.borderRadius + 'px';
                div.appendChild(img);
                break;

            case 'container':
                div.className += ' rendered-container';
                if (data.backgroundColor) div.style.backgroundColor = data.backgroundColor;
                if (data.paddingTop) div.style.paddingTop = data.paddingTop + 'px';
                if (data.paddingBottom) div.style.paddingBottom = data.paddingBottom + 'px';
                div.style.flexDirection = data.flexDirection || 'column';
                div.style.alignItems = data.alignItems || 'stretch';
                div.style.justifyContent = data.justifyContent || 'flex-start';
                
                const containerBlocks = data.blocks || data.content || [];
                if (Array.isArray(containerBlocks)) {
                    renderContent(containerBlocks, div);
                }
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
                if (data.color) hr.style.borderColor = data.color;
                if (data.spacing) {
                    hr.style.marginTop = data.spacing + 'px';
                    hr.style.marginBottom = data.spacing + 'px';
                }
                div.appendChild(hr);
                break;

            case 'spacer':
                const spacer = document.createElement('div');
                spacer.className = 'rendered-spacer';
                spacer.style.height = (data.height || 20) + 'px';
                div.appendChild(spacer);
                break;

            case 'icon':
                const icon = document.createElement('i');
                icon.className = data.iconClass || 'fas fa-star';
                if (data.color) icon.style.color = data.color;
                if (data.fontSize) icon.style.fontSize = data.fontSize + 'px';
                div.appendChild(icon);
                break;

            case 'video':
                const videoWrapper = document.createElement('div');
                videoWrapper.className = 'rendered-video';
                videoWrapper.style.position = 'relative';
                videoWrapper.style.paddingBottom = '56.25%'; // 16:9
                videoWrapper.style.height = '0';
                videoWrapper.style.overflow = 'hidden';
                videoWrapper.style.borderRadius = '8px';
                videoWrapper.style.background = '#000';

                if (data.host === 'youtube') {
                    const iframe = document.createElement('iframe');
                    let ytId = data.url;
                    // Extract ID if full URL
                    const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
                    const match = data.url.match(regExp);
                    if (match && match[2].length === 11) ytId = match[2];
                    
                    iframe.src = `https://www.youtube.com/embed/${ytId}?autoplay=${data.autoplay ? 1 : 0}&loop=${data.loop ? 1 : 0}&controls=${data.controls ? 1 : 0}`;
                    iframe.style.position = 'absolute';
                    iframe.style.top = '0';
                    iframe.style.left = '0';
                    iframe.style.width = '100%';
                    iframe.style.height = '100%';
                    iframe.style.border = '0';
                    iframe.allow = "accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture";
                    iframe.allowFullscreen = true;
                    videoWrapper.appendChild(iframe);
                } else {
                    const video = document.createElement('video');
                    video.src = data.url || '';
                    video.style.position = 'absolute';
                    video.style.top = '0';
                    video.style.left = '0';
                    video.style.width = '100%';
                    video.style.height = '100%';
                    video.controls = data.controls !== false;
                    video.autoplay = data.autoplay === true;
                    video.loop = data.loop === true;
                    video.muted = data.autoplay === true; // Autoplay requires mute in many browsers
                    if (data.poster) video.poster = data.poster;
                    videoWrapper.appendChild(video);
                }
                div.appendChild(videoWrapper);
                break;

            case 'html':
                const htmlDiv = document.createElement('div');
                htmlDiv.innerHTML = data.code || '';
                div.appendChild(htmlDiv);
                break;

            case 'css':
                if (data.code) {
                    const style = document.createElement('style');
                    style.textContent = data.code;
                    document.head.appendChild(style);
                }
                return null;

            case 'iframe':
                const ifr = document.createElement('iframe');
                ifr.src = data.src || '';
                ifr.title = data.title || 'iFrame';
                ifr.style.width = '100%';
                ifr.style.border = 'none';
                ifr.height = data.height || 300;
                div.appendChild(ifr);
                break;

            default:
                console.log('Unknown block type:', data.type);
                return null;
        }
        
        return div;
    }
</script>
@endpush
