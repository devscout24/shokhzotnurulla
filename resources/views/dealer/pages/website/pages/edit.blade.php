@extends('layouts.dealer.app')

@section('title', __('Edit Page') . ' | ' . __(config('app.name')))

@push('page-assets')
    <style>
        .page-builder-container {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 20px;
            min-height: 600px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            background: #fafafa;
            overflow: hidden;
        }

        .builder-sidebar {
            background: #fff;
            border-right: 1px solid #e0e0e0;
            overflow-y: auto;
            padding: 15px;
        }

        .builder-blocks-title {
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 12px;
        }

        .block-item {
            padding: 10px;
            margin-bottom: 8px;
            background: #f5f5f5;
            border: 1px solid #ddd;
            border-radius: 5px;
            cursor: move;
            user-select: none;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 500;
            color: #333;
        }

        .block-item:hover {
            background: #efefef;
            border-color: #bbb;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .block-item i {
            font-size: 16px;
            width: 24px;
            text-align: center;
            color: #e74c3c;
        }

        .builder-canvas {
            background: #fff;
            padding: 20px;
            overflow-y: auto;
            position: relative;
        }

        .canvas-placeholder {
            text-align: center;
            color: #999;
            padding: 40px 20px;
            font-size: 14px;
            border: 2px dashed #ddd;
            border-radius: 5px;
            background: #fafafa;
        }

        .content-block {
            background: #fff;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
            position: relative;
            transition: all 0.2s;
        }

        .content-block:hover {
            border-color: #e74c3c;
            box-shadow: 0 2px 8px rgba(231, 76, 60, 0.15);
        }

        .block-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .block-type {
            font-weight: 600;
            font-size: 12px;
            color: #e74c3c;
            text-transform: uppercase;
        }

        .block-actions {
            display: flex;
            gap: 5px;
        }

        .block-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px 8px;
            color: #999;
            font-size: 14px;
            transition: color 0.2s;
        }

        .block-btn:hover {
            color: #e74c3c;
        }

        .block-content {
            font-size: 13px;
        }

        .block-content input,
        .block-content textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: inherit;
            font-size: 13px;
        }

        .block-content textarea {
            resize: vertical;
            min-height: 60px;
        }

        .form-section {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #e0e0e0;
        }

        .form-section h3 {
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #333;
        }

        .save-zone {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 15px;
        }

        @media (max-width: 1200px) {
            .page-builder-container {
                grid-template-columns: 1fr;
            }
            .builder-sidebar {
                display: flex;
                overflow-x: auto;
                border-right: none;
                border-bottom: 1px solid #e0e0e0;
                flex-wrap: wrap;
                gap: 10px;
            }
            .builder-blocks-title {
                width: 100%;
            }
            .block-item {
                margin-bottom: 0;
            }
        }
    </style>
@endpush

@section('page-content')
<main class="main-content" id="mainContent">
    <div class="page-header">
        <h2 class="view-title">{{ __('Edit Page') }}: {{ $page->title }}</h2>
    </div>

    <div class="view-content">
        <form method="POST" action="{{ $routes['update'] }}" id="pageForm" style="max-width:1400px;">
            @csrf
            @method('PATCH')

            <!-- Status Badge -->
            <div style="margin-bottom: 20px;">
                <span class="status-badge" style="background: {{ $page->is_active ? '#d4edda' : '#f8d7da' }}; color: {{ $page->is_active ? '#155724' : '#721c24' }};">
                    {{ $page->getStatusLabel() }}
                </span>
                <small style="color: #666; margin-left: 15px;">
                    <strong>{{ __('Created:') }}</strong> {{ $page->created_at->format('M d, Y H:i') }} |
                    <strong>{{ __('Updated:') }}</strong> {{ $page->updated_at->format('M d, Y H:i') }}
                </small>
            </div>

            <!-- Section 1: Basic Info -->
            <div class="form-section">
                <h3>{{ __('Page Information') }}</h3>

                <div class="form-group" style="margin-bottom:15px;">
                    <label for="title" class="form-label">{{ __('Page Title') }} <span style="color:#e74c3c;">*</span></label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $page->title) }}" required placeholder="{{ __('Enter page title') }}">
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group" style="margin-bottom:15px;">
                    <label for="slug" class="form-label">{{ __('URL Slug') }} <span style="color:#e74c3c;">*</span></label>
                    <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $page->slug) }}" required placeholder="{{ __('page-url-slug') }}" pattern="^[a-z0-9\-]+$">
                    <small style="color:#7f8c8d;">{{ __('Lowercase letters, numbers, and hyphens only') }}</small>
                    @error('slug')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Section 2: Page Builder -->
            <div class="form-section">
                <h3>{{ __('Page Content') }} <span style="color:#e74c3c;">*</span></h3>
                <p style="font-size: 13px; color: #666; margin-bottom: 15px;">{{ __('Drag blocks from the left sidebar to build your page') }}</p>

                <div class="page-builder-container">
                    <!-- Sidebar: Available Blocks -->
                    <div class="builder-sidebar">
                        <div class="builder-blocks-title">{{ __('Blocks') }}</div>

                        <div class="block-item" draggable="true" data-block-type="heading">
                            <i class="bi bi-type-h1"></i> {{ __('Heading') }}
                        </div>

                        <div class="block-item" draggable="true" data-block-type="paragraph">
                            <i class="bi bi-type"></i> {{ __('Paragraph') }}
                        </div>

                        <div class="block-item" draggable="true" data-block-type="image">
                            <i class="bi bi-image"></i> {{ __('Image') }}
                        </div>

                        <div class="block-item" draggable="true" data-block-type="button">
                            <i class="bi bi-cursor-fill"></i> {{ __('Button') }}
                        </div>

                        <div class="block-item" draggable="true" data-block-type="columns">
                            <i class="bi bi-columns-gap"></i> {{ __('Columns') }}
                        </div>

                        <div class="block-item" draggable="true" data-block-type="separator">
                            <i class="bi bi-dash-lg"></i> {{ __('Divider') }}
                        </div>

                        <div class="block-item" draggable="true" data-block-type="quote">
                            <i class="bi bi-quote"></i> {{ __('Quote') }}
                        </div>

                        <div class="block-item" draggable="true" data-block-type="list">
                            <i class="bi bi-list-ul"></i> {{ __('List') }}
                        </div>

                        <div class="block-item" draggable="true" data-block-type="spacer">
                            <i class="bi bi-arrow-up-down"></i> {{ __('Spacer') }}
                        </div>
                    </div>

                    <!-- Canvas: Drop Area -->
                    <div class="builder-canvas" id="builderCanvas">
                        <div class="canvas-placeholder" id="canvasPlaceholder">
                            {{ __('Drag blocks here to build your page') }}
                        </div>
                    </div>
                </div>

                <!-- Hidden field to store page structure -->
                <input type="hidden" name="content" id="contentStructure" value="{{ old('content', $page->content) }}">
            </div>

            <!-- Section 3: SEO -->
            <div class="form-section">
                <h3>{{ __('SEO Settings') }}</h3>

                <div class="form-group" style="margin-bottom:15px;">
                    <label for="meta_title" class="form-label">{{ __('Meta Title') }}</label>
                    <input type="text" class="form-control @error('meta_title') is-invalid @enderror" id="meta_title" name="meta_title" value="{{ old('meta_title', $page->meta_title) }}" placeholder="{{ __('Enter SEO title') }}" maxlength="255">
                    <small style="color:#7f8c8d;">{{ __('Leave empty to use page title') }}</small>
                </div>

                <div class="form-group" style="margin-bottom:15px;">
                    <label for="meta_description" class="form-label">{{ __('Meta Description') }}</label>
                    <textarea class="form-control @error('meta_description') is-invalid @enderror" id="meta_description" name="meta_description" placeholder="{{ __('Enter SEO description') }}" maxlength="255" style="min-height: 60px;">{{ old('meta_description', $page->meta_description) }}</textarea>
                </div>

                <div class="form-group">
                    <label for="meta_keywords" class="form-label">{{ __('Meta Keywords') }}</label>
                    <input type="text" class="form-control @error('meta_keywords') is-invalid @enderror" id="meta_keywords" name="meta_keywords" value="{{ old('meta_keywords', $page->meta_keywords) }}" placeholder="{{ __('keyword1, keyword2, keyword3') }}">
                </div>
            </div>

            <!-- Section 4: Tags & Publishing -->
            <div class="form-section">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <h3 style="font-size: 14px; font-weight: 600; margin-bottom: 12px;">{{ __('Tags') }}</h3>
                        <input type="text" class="form-control @error('tags') is-invalid @enderror" id="tags" placeholder="{{ __('Add tags...') }}" value="">
                        <small style="color:#7f8c8d; display: block; margin-top: 5px;">{{ __('Press Enter to add') }}</small>
                        <div id="tags-container" style="margin-top:10px;"></div>
                        <input type="hidden" name="tags" id="tags-hidden" value="[]">
                    </div>

                    <div>
                        <h3 style="font-size: 14px; font-weight: 600; margin-bottom: 12px;">{{ __('Publishing') }}</h3>
                        <div class="form-check" style="margin-bottom: 10px;">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $page->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
                        </div>
                        <div class="form-check" style="margin-bottom: 10px;">
                            <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured', $page->is_featured) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_featured">{{ __('Featured') }}</label>
                        </div>
                        <div>
                            <label for="published_at" class="form-label" style="font-size: 13px; margin-top: 10px;">{{ __('Publish Date') }}</label>
                            <input type="datetime-local" class="form-control @error('published_at') is-invalid @enderror" id="published_at" name="published_at" value="{{ old('published_at', $page->published_at?->format('Y-m-d\TH:i')) }}" style="font-size: 13px;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Section -->
            <div class="save-zone">
                <button type="submit" class="btn btn-primary" style="padding: 10px 30px; font-weight: 600;">
                    <i class="bi bi-check-circle"></i> {{ __('Update Page') }}
                </button>
                <a href="{{ route('dealer.website.pages.index') }}" class="btn btn-secondary" style="padding: 10px 30px; margin-left: 10px;">
                    {{ __('Cancel') }}
                </a>
            </div>
        </form>
    </div>
</main>

<script>
// Page Builder Logic
let pageBlocks = [];
let draggedBlockType = null;

const builderCanvas = document.getElementById('builderCanvas');
const contentStructure = document.getElementById('contentStructure');
const canvasPlaceholder = document.getElementById('canvasPlaceholder');

// Block Templates
const blockTemplates = {
    heading: { type: 'heading', content: 'Enter heading text', level: 'h2' },
    paragraph: { type: 'paragraph', content: 'Enter paragraph text' },
    image: { type: 'image', src: '', alt: '', width: '100%' },
    button: { type: 'button', text: 'Click Me', url: '#', color: 'primary' },
    columns: { type: 'columns', cols: 2, content: ['Column 1', 'Column 2'] },
    separator: { type: 'separator' },
    quote: { type: 'quote', text: 'Enter quote text', author: '' },
    list: { type: 'list', items: ['Item 1', 'Item 2', 'Item 3'] },
    spacer: { type: 'spacer', height: '20px' }
};

// Load existing content
function loadExistingContent() {
    try {
        const content = contentStructure.value;
        if (content && content.trim() !== '[]' && content.trim() !== '') {
            pageBlocks = JSON.parse(content);
            renderCanvas();
        }
    } catch (e) {
        console.error('Error loading content:', e);
    }
}

// Drag and Drop - Dragging from Sidebar
document.querySelectorAll('.block-item').forEach(item => {
    item.addEventListener('dragstart', (e) => {
        draggedBlockType = e.currentTarget.dataset.blockType;
        e.dataTransfer.effectAllowed = 'copy';
        e.dataTransfer.setData('text/plain', draggedBlockType);
    });
});

// Canvas Drop Zone
builderCanvas.addEventListener('dragover', (e) => {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'copy';
    builderCanvas.style.background = '#f0f0f0';
});

builderCanvas.addEventListener('dragleave', () => {
    builderCanvas.style.background = '#fff';
});

builderCanvas.addEventListener('drop', (e) => {
    e.preventDefault();
    builderCanvas.style.background = '#fff';

    if (draggedBlockType) {
        const blockData = JSON.parse(JSON.stringify(blockTemplates[draggedBlockType]));
        blockData.id = 'block-' + Date.now() + Math.random();
        pageBlocks.push(blockData);
        renderCanvas();
        draggedBlockType = null;
    }
});

// Render Canvas
function renderCanvas() {
    if (pageBlocks.length === 0) {
        canvasPlaceholder.style.display = 'block';
        builderCanvas.innerHTML = '<div class="canvas-placeholder" id="canvasPlaceholder">Drag blocks here to build your page</div>';
    } else {
        canvasPlaceholder.style.display = 'none';
        builderCanvas.innerHTML = '';
        pageBlocks.forEach((block, idx) => {
            builderCanvas.appendChild(createBlockElement(block, idx));
        });
    }
    updateContentStructure();
}

// Create Block Element
function createBlockElement(block, idx) {
    const blockEl = document.createElement('div');
    blockEl.className = 'content-block';
    blockEl.dataset.blockId = block.id;

    let contentHTML = '';

    switch (block.type) {
        case 'heading':
            contentHTML = `
                <div class="block-header">
                    <span class="block-type">Heading</span>
                    <div class="block-actions">
                        <button type="button" class="block-btn" onclick="editBlock('${block.id}')">✏️</button>
                        <button type="button" class="block-btn" onclick="deleteBlock('${block.id}')">🗑️</button>
                    </div>
                </div>
                <div class="block-content">
                    <input type="text" value="${block.content}" placeholder="Heading text" onchange="updateBlockContent('${block.id}', 'content', this.value)">
                </div>
            `;
            break;

        case 'paragraph':
            contentHTML = `
                <div class="block-header">
                    <span class="block-type">Paragraph</span>
                    <div class="block-actions">
                        <button type="button" class="block-btn" onclick="deleteBlock('${block.id}')">🗑️</button>
                    </div>
                </div>
                <div class="block-content">
                    <textarea placeholder="Paragraph text" onchange="updateBlockContent('${block.id}', 'content', this.value)">${block.content}</textarea>
                </div>
            `;
            break;

        case 'image':
            contentHTML = `
                <div class="block-header">
                    <span class="block-type">Image</span>
                    <div class="block-actions">
                        <button type="button" class="block-btn" onclick="deleteBlock('${block.id}')">🗑️</button>
                    </div>
                </div>
                <div class="block-content">
                    <input type="text" value="${block.src}" placeholder="Image URL" onchange="updateBlockContent('${block.id}', 'src', this.value)" style="margin-bottom: 8px;">
                    <input type="text" value="${block.alt}" placeholder="Alt text" onchange="updateBlockContent('${block.id}', 'alt', this.value)">
                </div>
            `;
            break;

        case 'button':
            contentHTML = `
                <div class="block-header">
                    <span class="block-type">Button</span>
                    <div class="block-actions">
                        <button type="button" class="block-btn" onclick="deleteBlock('${block.id}')">🗑️</button>
                    </div>
                </div>
                <div class="block-content">
                    <input type="text" value="${block.text}" placeholder="Button text" onchange="updateBlockContent('${block.id}', 'text', this.value)" style="margin-bottom: 8px;">
                    <input type="text" value="${block.url}" placeholder="Button URL" onchange="updateBlockContent('${block.id}', 'url', this.value)">
                </div>
            `;
            break;

        case 'separator':
            contentHTML = `
                <div class="block-header">
                    <span class="block-type">Divider</span>
                    <div class="block-actions">
                        <button type="button" class="block-btn" onclick="deleteBlock('${block.id}')">🗑️</button>
                    </div>
                </div>
                <div style="height: 1px; background: #ddd; margin: 10px 0;"></div>
            `;
            break;

        default:
            contentHTML = `
                <div class="block-header">
                    <span class="block-type">${block.type.charAt(0).toUpperCase() + block.type.slice(1)}</span>
                    <div class="block-actions">
                        <button type="button" class="block-btn" onclick="deleteBlock('${block.id}')">🗑️</button>
                    </div>
                </div>
                <div class="block-content">[${block.type} block]</div>
            `;
    }

    blockEl.innerHTML = contentHTML;
    return blockEl;
}

// Update Block
function updateBlockContent(blockId, field, value) {
    const block = pageBlocks.find(b => b.id === blockId);
    if (block) {
        block[field] = value;
        updateContentStructure();
    }
}

// Delete Block
function deleteBlock(blockId) {
    pageBlocks = pageBlocks.filter(b => b.id !== blockId);
    renderCanvas();
}

// Update Content Structure
function updateContentStructure() {
    contentStructure.value = JSON.stringify(pageBlocks);
}

// Tags Management
let tags = @json($page->tags ?? []);
const tagInput = document.getElementById('tags');
const tagsContainer = document.getElementById('tags-container');
const tagsHidden = document.getElementById('tags-hidden');

function renderTags() {
    tagsContainer.innerHTML = '';
    tags.forEach((tag, index) => {
        const badge = document.createElement('span');
        badge.className = 'badge bg-primary me-2 mb-2';
        badge.innerHTML = `${tag} <button type="button" class="btn-close btn-close-white" onclick="removeTag(${index})" style="font-size: 0.7rem;"></button>`;
        tagsContainer.appendChild(badge);
    });
    tagsHidden.value = JSON.stringify(tags);
}

function removeTag(index) {
    tags.splice(index, 1);
    renderTags();
}

tagInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' || e.key === ',') {
        e.preventDefault();
        const tag = tagInput.value.trim();
        if (tag && !tags.includes(tag)) {
            tags.push(tag);
            tagInput.value = '';
            renderTags();
        }
    }
});

tagInput.addEventListener('blur', () => {
    const tag = tagInput.value.trim();
    if (tag && !tags.includes(tag)) {
        tags.push(tag);
        tagInput.value = '';
        renderTags();
    }
});

// Initial renders
loadExistingContent();
renderTags();

// Validate on submit
document.getElementById('pageForm').addEventListener('submit', function(e) {
    if (pageBlocks.length === 0) {
        e.preventDefault();
        alert('Please add at least one block to your page');
    }
});
</script>
@endsection
