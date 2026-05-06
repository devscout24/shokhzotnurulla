{{-- ALL Settings Panels for Overfuel Parity --}}

@php
$visibilityHtml = '
<hr class="hs-divider"/>
<label class="fw-bold small text-uppercase mb-2 d-block">Visibility</label>
<div class="hs-row d-flex justify-content-between align-items-center">
    <label class="mb-0">Show on Desktop</label>
    <div class="form-check form-switch mb-0"><input class="form-check-input visibility-toggle" type="checkbox" data-device="desktop" checked></div>
</div>
<div class="hs-row d-flex justify-content-between align-items-center">
    <label class="mb-0">Show on Mobile</label>
    <div class="form-check form-switch mb-0"><input class="form-check-input visibility-toggle" type="checkbox" data-device="mobile" checked></div>
</div>
';
@endphp

{{-- Span Settings --}}
<div id="span-settings-panel" style="display:none">
<button type="button" class="hs-back-btn" id="span-back-btn"><i class="fa-solid fa-arrow-left"></i> Span Settings</button>
<div class="hs-row"><label>Text color</label>
<select class="hs-select" id="span-color"><option value="" selected>Default</option><option value="#ef4444">Red</option><option value="#1d4ed8">Blue</option><option value="#15803d">Green</option><option value="#111827">Dark</option><option value="#f97316">Orange</option></select></div>
<div class="hs-row"><label>Text size (px)</label><input class="hs-input" id="span-size" type="number" placeholder="16" min="8" max="72" style="width:120px"/></div>
<div class="hs-row"><label>Align</label><div class="hs-align-group"><button type="button" class="span-align-btn active" data-align="left"><i class="fa-solid fa-align-left"></i></button><button type="button" class="span-align-btn" data-align="center"><i class="fa-solid fa-align-center"></i></button><button type="button" class="span-align-btn" data-align="right"><i class="fa-solid fa-align-right"></i></button></div></div>
<div class="hs-row"><label>Font weight</label><select class="hs-select" id="span-weight"><option value="normal" selected>Normal</option><option value="bold">Bold</option></select></div>
{!! $visibilityHtml !!}
<hr class="hs-divider"/>
<div class="hs-actions"><button type="button" class="hs-btn-remove" id="span-remove-btn"><i class="fa-regular fa-trash-can"></i> Remove</button><button type="button" class="hs-btn-cancel" id="span-cancel-btn">Cancel</button></div>
</div>

{{-- Heading Settings --}}
<div id="heading-settings-panel" style="display:none">
<button type="button" class="hs-back-btn" id="hs-back-btn"><i class="fa-solid fa-arrow-left"></i> Heading Settings</button>
<div class="hs-row"><label>Header tag</label><select class="hs-select" id="hs-tag"><option value="h1" selected>H1</option><option value="h2">H2</option><option value="h3">H3</option><option value="h4">H4</option><option value="h5">H5</option><option value="h6">H6</option></select></div>
<div class="hs-row"><label>CSS Class</label><input class="hs-input" id="hs-classes" placeholder="text-medium"/></div>
<div class="hs-row"><label>Align</label><div class="hs-align-group"><button type="button" class="hs-align-btn active" data-align="left"><i class="fa-solid fa-align-left"></i></button><button type="button" class="hs-align-btn" data-align="center"><i class="fa-solid fa-align-center"></i></button><button type="button" class="hs-align-btn" data-align="right"><i class="fa-solid fa-align-right"></i></button></div></div>
<div class="hs-row"><label>Text color</label><input class="hs-input" id="hs-color" type="color" value="#111827" style="height:40px;padding:2px"/></div>
<div class="hs-row"><label>Text size (px)</label><input class="hs-input" id="hs-size" type="number" placeholder="32" min="8" max="120" style="width:120px"/></div>
{!! $visibilityHtml !!}
<hr class="hs-divider"/>
<div class="hs-actions">
    <button type="button" class="btn btn-danger btn-sm flex-1 fw-bold" onclick="closeAllPanels()" style="background:#c0392b; border-radius:8px; padding:10px; flex:1">Apply Changes</button>
    <button type="button" class="hs-btn-remove" id="hs-remove-btn" style="flex:1"><i class="fa-regular fa-trash-can"></i> Remove</button>
</div>
<div class="hs-actions mt-2">
    <button type="button" class="hs-btn-cancel w-100" id="hs-cancel-btn" style="border:1px solid #e0e6ed; background:#fff; padding:10px; border-radius:8px; font-weight:700">Cancel</button>
</div>
</div>

{{-- Text Settings --}}
<div id="text-settings-panel" style="display:none">
<button type="button" class="hs-back-btn" id="ts-back-btn"><i class="fa-solid fa-arrow-left"></i> Text Settings</button>
<div class="hs-row"><label>Text color</label><input class="hs-input" id="ts-color" type="color" value="#111827" style="height:40px;padding:2px"/></div>
<div class="hs-row"><label>Align</label><div class="hs-align-group"><button type="button" class="ts-align-btn active" data-align="left"><i class="fa-solid fa-align-left"></i></button><button type="button" class="ts-align-btn" data-align="center"><i class="fa-solid fa-align-center"></i></button><button type="button" class="ts-align-btn" data-align="right"><i class="fa-solid fa-align-right"></i></button></div></div>
<div class="hs-row"><label>Text size (px)</label><input class="hs-input" id="ts-size" type="number" placeholder="16" min="8" max="72" style="width:120px"/></div>
{!! $visibilityHtml !!}
<hr class="hs-divider"/>
<div class="hs-actions">
    <button type="button" class="btn btn-danger btn-sm flex-1 fw-bold" onclick="closeAllPanels()" style="background:#c0392b; border-radius:8px; padding:10px; flex:1">Apply Changes</button>
    <button type="button" class="hs-btn-remove" id="ts-remove-btn" style="flex:1"><i class="fa-regular fa-trash-can"></i> Remove</button>
</div>
<div class="hs-actions mt-2">
    <button type="button" class="hs-btn-cancel w-100" id="ts-cancel-btn" style="border:1px solid #e0e6ed; background:#fff; padding:10px; border-radius:8px; font-weight:700">Cancel</button>
</div>
</div>

{{-- Button Settings --}}
<div id="button-settings-panel" style="display:none">
<button type="button" class="bs-back-btn" id="bs-back-btn"><i class="fa-solid fa-arrow-left"></i> Button Settings</button>
<div class="bs-row"><label>Button text</label><input class="bs-input" id="bs-text" placeholder="GO FOR LIVE"/></div>
<div class="bs-row"><label>Button theme</label><select class="bs-select" id="bs-theme"><option value="red" selected>Red / Default</option><option value="dark">Dark</option><option value="blue">Blue</option></select></div>
<div class="bs-row"><label>Link to</label><input class="bs-input" id="bs-link" placeholder="https://"/></div>
<div class="bs-row"><label>Full-width</label><div class="form-check form-switch mb-0"><input class="form-check-input" type="checkbox" id="bs-fullwidth"/></div></div>
{!! $visibilityHtml !!}
<hr class="bs-divider"/>
<div class="hs-actions">
    <button type="button" class="btn btn-danger btn-sm flex-1 fw-bold" onclick="closeAllPanels()" style="background:#c0392b; border-radius:8px; padding:10px; flex:1">Apply Changes</button>
    <button type="button" class="hs-btn-remove" id="bs-remove-btn" style="flex:1"><i class="fa-regular fa-trash-can"></i> Remove</button>
</div>
<div class="hs-actions mt-2">
    <button type="button" class="hs-btn-cancel w-100" id="bs-cancel-btn" style="border:1px solid #e0e6ed; background:#fff; padding:10px; border-radius:8px; font-weight:700">Cancel</button>
</div>
</div>

{{-- Image Settings --}}
<div id="image-settings-panel" style="display:none">
<button type="button" class="hs-back-btn" id="is-back-btn"><i class="fa-solid fa-arrow-left"></i> Image Settings</button>
<div class="hs-row"><label>Image Source</label>
<div style="display:flex; gap:5px"><input class="hs-input" id="is-url" placeholder="URL"/><button type="button" class="btn btn-outline-danger" id="is-upload-btn"><i class="fa-solid fa-upload"></i></button></div>
<input type="file" id="is-upload-input" style="display:none" accept="image/*"></div>
<div class="hs-row"><label>Alt Text (Description)</label><input class="hs-input" id="is-alt" placeholder="Image description..."/></div>
<div class="hs-row"><label>Link Image to</label><input class="hs-input" id="is-link" placeholder="https://..."/></div>
<div class="hs-row d-flex justify-content-between align-items-center"><label class="mb-0">Open in New Tab</label><div class="form-check form-switch mb-0"><input class="form-check-input" type="checkbox" id="is-newtab"></div></div>
<div class="hs-row"><label>Align</label><div class="hs-align-group"><button type="button" class="is-align-btn" data-align="left"><i class="fa-solid fa-align-left"></i></button><button type="button" class="is-align-btn" data-align="center"><i class="fa-solid fa-align-center"></i></button><button type="button" class="is-align-btn" data-align="right"><i class="fa-solid fa-align-right"></i></button></div></div>
<div class="hs-row"><label>Width (%)</label><input class="hs-input" id="is-width" type="number" value="100"/></div>
<div class="hs-row"><label>Height (px)</label><input class="hs-input" id="is-height" type="number" placeholder="auto"/></div>
<div class="hs-row"><label>Opacity (0-1)</label><input class="hs-input" id="is-opacity" type="number" step="0.1" min="0" max="1" value="1"/></div>
{!! $visibilityHtml !!}
<hr class="hs-divider"/>
<div class="hs-actions">
    <button type="button" class="btn btn-danger btn-sm flex-1 fw-bold" id="is-apply-btn" style="background:#c0392b; border-radius:8px; padding:10px; flex:1">Apply Changes</button>
    <button type="button" class="hs-btn-remove" id="is-remove-btn" style="flex:1"><i class="fa-regular fa-trash-can"></i> Remove</button>
</div>
<div class="hs-actions mt-2">
    <button type="button" class="hs-btn-cancel w-100" id="is-cancel-btn" style="border:1px solid #e0e6ed; background:#fff; padding:10px; border-radius:8px; font-weight:700">Cancel</button>
</div>
</div>

{{-- Video Settings --}}
<div id="video-settings-panel" style="display:none">
<button type="button" class="hs-back-btn" id="vs-back-btn"><i class="fa-solid fa-arrow-left"></i> Video Settings</button>
<div class="hs-row"><label>Video Host</label><select class="hs-select" id="vs-host"><option value="youtube">YouTube</option><option value="overfuel">Overfuel / MP4</option></select></div>
<div class="hs-row"><label>Video URL / ID</label>
    <div class="input-group">
        <input class="hs-input" id="vs-url" placeholder="YouTube ID or MP4 URL"/>
        <button type="button" class="btn btn-outline-secondary" id="vs-upload-btn" title="Upload local video"><i class="fa-solid fa-upload"></i></button>
    </div>
</div>
<div class="hs-row"><label>Poster Image (URL)</label><input class="hs-input" id="vs-poster" placeholder="URL to preview image"/></div>
<div class="hs-row d-flex justify-content-between align-items-center"><label class="mb-0">Auto-play</label><div class="form-check form-switch mb-0"><input class="form-check-input" type="checkbox" id="vs-autoplay"></div></div>
<div class="hs-row d-flex justify-content-between align-items-center"><label class="mb-0">Loop Playback</label><div class="form-check form-switch mb-0"><input class="form-check-input" type="checkbox" id="vs-loop"></div></div>
<div class="hs-row d-flex justify-content-between align-items-center"><label class="mb-0">Hide Controls</label><div class="form-check form-switch mb-0"><input class="form-check-input" type="checkbox" id="vs-controls"></div></div>
{!! $visibilityHtml !!}
<hr class="hs-divider"/>
<div class="hs-actions">
    <button type="button" class="btn btn-danger btn-sm flex-1 fw-bold" id="vs-apply-btn" style="background:#c0392b; border-radius:8px; padding:10px; flex:1">Apply Changes</button>
    <button type="button" class="hs-btn-remove" id="vs-remove-btn" style="flex:1"><i class="fa-regular fa-trash-can"></i> Remove</button>
</div>
<div class="hs-actions mt-2">
    <button type="button" class="hs-btn-cancel w-100" id="vs-cancel-btn" style="border:1px solid #e0e6ed; background:#fff; padding:10px; border-radius:8px; font-weight:700">Cancel</button>
</div>
</div>

{{-- Spacer Settings --}}
<div id="spacer-settings-panel" style="display:none">
<button type="button" class="hs-back-btn" id="ss-back-btn"><i class="fa-solid fa-arrow-left"></i> Spacer Settings</button>
<div class="hs-row"><label>Height (px)</label><input class="hs-input" id="ss-height" type="number" value="40"/></div>
{!! $visibilityHtml !!}
<hr class="hs-divider"/>
<div class="hs-actions"><button type="button" class="hs-btn-remove" id="ss-remove-btn"><i class="fa-regular fa-trash-can"></i> Remove</button><button type="button" class="hs-btn-cancel" id="ss-cancel-btn">Cancel</button></div>
</div>
<div id="container-settings-panel" style="display:none">
<button type="button" class="hs-back-btn" id="container-back-btn"><i class="fa-solid fa-arrow-left"></i> Container Settings</button>
<div class="hs-row"><label>Padding Vertical (px)</label><input class="hs-input" id="container-padding" type="number" value="40"/></div>
<div class="hs-row"><label>Background Color</label><input class="hs-input" id="container-bg" type="color" value="#ffffff"/></div>
{!! $visibilityHtml !!}
<hr class="hs-divider"/>
<div class="hs-actions"><button type="button" class="hs-btn-remove" id="container-remove-btn"><i class="fa-regular fa-trash-can"></i> Remove</button><button type="button" class="hs-btn-cancel" id="container-cancel-btn">Cancel</button></div>
</div>

{{-- Inventory Settings --}}
<div id="inventory-settings-panel" style="display:none">
<button type="button" class="hs-back-btn" id="inv-back-btn"><i class="fa-solid fa-arrow-left"></i> Inventory Settings</button>
<div class="hs-row"><label>Dealer ID</label><input class="hs-input" id="inv-dealer-id" placeholder="12345"/></div>
<div class="hs-row"><label>Condition</label><select class="hs-select" id="inv-condition"><option value="all">All</option><option value="new">New</option><option value="used">Used</option></select></div>
<div class="hs-row"><label>Make</label><input class="hs-input" id="inv-make" placeholder="e.g. Honda"/></div>
<div class="hs-row"><label>Model</label><input class="hs-input" id="inv-model" placeholder="e.g. Civic"/></div>
<div class="hs-row"><label>Min Price</label><input class="hs-input" id="inv-min-price" type="number" placeholder="0"/></div>
<div class="hs-row"><label>Max Price</label><input class="hs-input" id="inv-max-price" type="number" placeholder="100000"/></div>
<div class="hs-row"><label>Max Mileage</label><input class="hs-input" id="inv-max-mileage" type="number" placeholder="150000"/></div>
<div class="hs-row"><label>Sort By</label><select class="hs-select" id="inv-sort"><option value="price_asc">Price: Low to High</option><option value="price_desc">Price: High to Low</option><option value="mileage_asc">Mileage: Lowest First</option></select></div>
<div class="hs-row d-flex justify-content-between align-items-center"><label class="mb-0">Highlighted Only</label><div class="form-check form-switch mb-0"><input class="form-check-input" type="checkbox" id="inv-highlighted"></div></div>
{!! $visibilityHtml !!}
<hr class="hs-divider"/>
<div class="hs-actions">
    <button type="button" class="btn btn-danger btn-sm flex-1 fw-bold" onclick="closeAllPanels()" style="background:#ce4f4b; border-radius:8px; padding:10px; flex:1">Apply Changes</button>
    <button type="button" class="hs-btn-remove" id="inv-remove-btn" style="flex:1"><i class="fa-regular fa-trash-can"></i> Remove</button>
</div>
</div>

{{-- Form Settings --}}
<div id="form-settings-panel" style="display:none">
<button type="button" class="hs-back-btn" id="fs-back-btn"><i class="fa-solid fa-arrow-left"></i> Form Settings</button>
<div class="hs-row"><label>Select Form</label><select class="hs-select" id="fs-id"><option value="general">General Contact</option><option value="test-drive">Schedule Test Drive</option><option value="finance">Finance Application</option></select></div>
<div class="hs-row"><label>Custom Form Name (Internal)</label><input class="hs-input" id="fs-name" placeholder="Summer Campaign Form"/></div>
<div class="hs-row"><label>Receiver Email</label><input class="hs-input" id="fs-email" placeholder="sales@dealer.com"/></div>
<div class="hs-row"><label>Success Msg</label><input class="hs-input" id="fs-success" placeholder="Thank you!"/></div>
{!! $visibilityHtml !!}
<hr class="hs-divider"/>
<div class="hs-actions">
    <button type="button" class="btn btn-danger btn-sm flex-1 fw-bold" onclick="closeAllPanels()" style="background:#ce4f4b; border-radius:8px; padding:10px; flex:1">Apply Changes</button>
    <button type="button" class="hs-btn-remove" id="fs-remove-btn" style="flex:1"><i class="fa-regular fa-trash-can"></i> Remove</button>
</div>
</div>

{{-- Map Settings --}}
<div id="map-settings-panel" style="display:none">
<button type="button" class="hs-back-btn" id="map-back-btn"><i class="fa-solid fa-arrow-left"></i> Map Settings</button>
<div class="hs-row"><label>Main Title</label><input class="hs-input" id="map-title" placeholder="Visit Us Today"/></div>
<div class="hs-row"><label>Subtitle</label><input class="hs-input" id="map-subtitle" placeholder="Located in the heart of the city"/></div>
<div class="hs-row"><label>Address</label><input class="hs-input" id="map-address" placeholder="123 Dealer St"/></div>
<div class="hs-row"><label>Map Zoom</label><input class="hs-input" id="map-zoom" type="number" value="14"/></div>
<div class="hs-row"><label>Locations to Show</label>
    <div class="d-flex flex-column gap-2 p-2 border rounded bg-light">
        <label class="d-flex align-items-center gap-2 small m-0"><input type="checkbox" checked> Primary Dealership</label>
        <label class="d-flex align-items-center gap-2 small m-0"><input type="checkbox"> Service Center</label>
        <label class="d-flex align-items-center gap-2 small m-0"><input type="checkbox"> Body Shop</label>
    </div>
</div>
{!! $visibilityHtml !!}
<hr class="hs-divider"/>
<div class="hs-actions">
    <button type="button" class="btn btn-danger btn-sm flex-1 fw-bold" onclick="closeAllPanels()" style="background:#ce4f4b; border-radius:8px; padding:10px; flex:1">Apply Changes</button>
    <button type="button" class="hs-btn-remove" id="map-remove-btn" style="flex:1"><i class="fa-regular fa-trash-can"></i> Remove</button>
</div>
</div>

{{-- 2-Column Settings --}}
<div id="2col-settings-panel" style="display:none">
<button type="button" class="hs-back-btn" id="col2-back-btn"><i class="fa-solid fa-arrow-left"></i> 2-Column Settings</button>
<div class="hs-row"><label>Gap (px)</label><input class="hs-input" id="col2-gap" type="number" value="20"/></div>
{!! $visibilityHtml !!}
<hr class="hs-divider"/>
<div class="hs-actions"><button type="button" class="hs-btn-remove" id="col2-remove-btn"><i class="fa-regular fa-trash-can"></i> Remove</button><button type="button" class="hs-btn-cancel" id="col2-cancel-btn">Cancel</button></div>
</div>

{{-- 3-Column Settings --}}
<div id="3col-settings-panel" style="display:none">
<button type="button" class="hs-back-btn" id="col3-back-btn"><i class="fa-solid fa-arrow-left"></i> 3-Column Settings</button>
<div class="hs-row"><label>Gap (px)</label><input class="hs-input" id="col3-gap" type="number" value="20"/></div>
{!! $visibilityHtml !!}
<hr class="hs-divider"/>
<div class="hs-actions"><button type="button" class="hs-btn-remove" id="col3-remove-btn"><i class="fa-regular fa-trash-can"></i> Remove</button><button type="button" class="hs-btn-cancel" id="col3-cancel-btn">Cancel</button></div>
</div>

{{-- Accordion Settings --}}
<div id="accordion-settings-panel" style="display:none">
<button type="button" class="hs-back-btn" id="as-back-btn"><i class="fa-solid fa-arrow-left"></i> Accordion Settings</button>
<div class="hs-row mt-2"><button type="button" class="btn btn-outline-danger btn-sm w-100" id="as-add-item">Add Item</button></div>
{!! $visibilityHtml !!}
<hr class="hs-divider"/>
<div class="hs-actions"><button type="button" class="hs-btn-remove" id="as-remove-btn"><i class="fa-regular fa-trash-can"></i> Remove</button><button type="button" class="hs-btn-cancel" id="as-cancel-btn">Cancel</button></div>
</div>

{{-- Card Settings --}}
<div id="card-settings-panel" style="display:none">
<button type="button" class="hs-back-btn" id="cs-back-btn"><i class="fa-solid fa-arrow-left"></i> Card Settings</button>
<div class="hs-row"><label>Width (%)</label><input class="hs-input" id="cs-width" type="number" value="100"/></div>
{!! $visibilityHtml !!}
<hr class="hs-divider"/>
<div class="hs-actions"><button type="button" class="hs-btn-remove" id="cs-remove-btn"><i class="fa-regular fa-trash-can"></i> Remove</button><button type="button" class="hs-btn-cancel" id="cs-cancel-btn">Cancel</button></div>
</div>

{{-- Carousel Settings --}}
<div id="carousel-settings-panel" style="display:none">
<button type="button" class="hs-back-btn" id="car-back-btn"><i class="fa-solid fa-arrow-left"></i> Carousel Settings</button>
<div class="hs-row"><label>Interval (ms)</label><input class="hs-input" id="car-interval" type="number" value="5000"/></div>
{!! $visibilityHtml !!}
<hr class="hs-divider"/>
<div class="hs-actions"><button type="button" class="hs-btn-remove" id="car-remove-btn"><i class="fa-regular fa-trash-can"></i> Remove</button><button type="button" class="hs-btn-cancel" id="car-cancel-btn">Cancel</button></div>
</div>

{{-- Icon Settings --}}
<div id="icon-settings-panel" style="display:none">
<button type="button" class="hs-back-btn" id="ics-back-btn"><i class="fa-solid fa-arrow-left"></i> Icon Settings</button>
<div class="hs-row"><label>Icon Class (e.g. fa-star)</label><input class="hs-input" id="ics-icon" placeholder="fa-star"/></div>
<div class="hs-row"><label>Icon Size (px)</label><input class="hs-input" id="ics-size" type="number" value="24"/></div>
<div class="hs-row"><label>Icon Color</label><input class="hs-input" id="ics-color" type="color" value="#111827" style="height:40px;padding:2px"/></div>
{!! $visibilityHtml !!}
<hr class="hs-divider"/>
<div class="hs-actions"><button type="button" class="hs-btn-remove" id="ics-remove-btn"><i class="fa-regular fa-trash-can"></i> Remove</button><button type="button" class="hs-btn-cancel" id="ics-cancel-btn">Cancel</button></div>
</div>
{{-- Search Settings --}}
<div id="search-settings-panel" style="display:none">
<button type="button" class="hs-back-btn" id="search-back-btn"><i class="fa-solid fa-arrow-left"></i> Search Settings</button>
<div class="hs-row"><label>Placeholder</label><input class="hs-input" id="search-placeholder" placeholder="Search..."/></div>
<div class="hs-row"><label>Input Size</label><select class="hs-select" id="search-size"><option value="small">Small</option><option value="medium" selected>Medium</option><option value="large">Large</option></select></div>
<div class="hs-row d-flex justify-content-between align-items-center"><label class="mb-0">Honda Store branding</label><div class="form-check form-switch mb-0"><input class="form-check-input" type="checkbox" id="search-honda"></div></div>
<div class="hs-row d-flex justify-content-between align-items-center"><label class="mb-0">Acura Store branding</label><div class="form-check form-switch mb-0"><input class="form-check-input" type="checkbox" id="search-acura"></div></div>
{!! $visibilityHtml !!}
<hr class="hs-divider"/>
<div class="hs-actions">
    <button type="button" class="btn btn-danger btn-sm flex-1 fw-bold" onclick="closeAllPanels()" style="background:#ce4f4b; border-radius:8px; padding:10px; flex:1">Apply Changes</button>
    <button type="button" class="hs-btn-remove" id="search-remove-btn" style="flex:1"><i class="fa-regular fa-trash-can"></i> Remove</button>
</div>
</div>

{{-- Cart Settings --}}
<div id="cart-settings-panel" style="display:none">
<button type="button" class="hs-back-btn" id="cart-back-btn"><i class="fa-solid fa-arrow-left"></i> Cart Settings</button>
<div class="hs-row"><label>Cart Display</label><select class="hs-select"><option>Icon + Count</option><option>Icon Only</option></select></div>
{!! $visibilityHtml !!}
<hr class="hs-divider"/>
<div class="hs-actions"><button type="button" class="hs-btn-remove" id="cart-remove-btn"><i class="fa-regular fa-trash-can"></i> Remove</button><button type="button" class="hs-btn-cancel" id="cart-cancel-btn">Cancel</button></div>
</div>
<div id="html-settings-panel" style="display:none">
<button type="button" class="hs-back-btn" id="html-back-btn"><i class="fa-solid fa-arrow-left"></i> HTML Settings</button>
<div class="hs-row"><label>Custom HTML</label><textarea class="hs-input" id="html-code" style="min-height:200px"></textarea></div>
{!! $visibilityHtml !!}
<hr class="hs-divider"/>
<div class="hs-actions"><button type="button" class="hs-btn-remove" id="html-remove-btn"><i class="fa-regular fa-trash-can"></i> Remove</button><button type="button" class="hs-btn-cancel" id="html-cancel-btn">Cancel</button></div>
</div>

{{-- CSS Settings --}}
<div id="css-settings-panel" style="display:none">
<button type="button" class="hs-back-btn" id="css-back-btn"><i class="fa-solid fa-arrow-left"></i> CSS Settings</button>
<div class="hs-row"><label>Custom CSS</label><textarea class="hs-input" id="css-code" style="min-height:200px"></textarea></div>
{!! $visibilityHtml !!}
<hr class="hs-divider"/>
<div class="hs-actions"><button type="button" class="hs-btn-remove" id="css-remove-btn"><i class="fa-regular fa-trash-can"></i> Remove</button><button type="button" class="hs-btn-cancel" id="css-cancel-btn">Cancel</button></div>
</div>

{{-- Divider Settings --}}
<div id="divider-settings-panel" style="display:none">
<button type="button" class="hs-back-btn" id="ds-back-btn"><i class="fa-solid fa-arrow-left"></i> Divider Settings</button>
<div class="hs-row"><label>Color</label><input class="hs-input" id="ds-color" type="color" value="#e0e6ed" style="height:40px;padding:2px"/></div>
<div class="hs-row"><label>Height (px)</label><input class="hs-input" id="ds-height" type="number" value="1" min="1" max="20"/></div>
{!! $visibilityHtml !!}
<hr class="hs-divider"/>
<div class="hs-actions"><button type="button" class="hs-btn-remove" id="ds-remove-btn"><i class="fa-regular fa-trash-can"></i> Remove</button><button type="button" class="hs-btn-cancel" id="ds-cancel-btn">Cancel</button></div>
</div>

{{-- iFrame Settings --}}
<div id="iframe-settings-panel" style="display:none">
<button type="button" class="hs-back-btn" id="iframe-back-btn"><i class="fa-solid fa-arrow-left"></i> iFrame Settings</button>
<div class="hs-row"><label>Source URL</label><input class="hs-input" id="iframe-url" placeholder="https://..."/></div>
<div class="hs-row"><label>Title (Optional)</label><input class="hs-input" id="iframe-title" placeholder="Frame title..."/></div>
<div class="hs-row"><label>Height (px)</label><input class="hs-input" id="iframe-height" type="number" value="300"/></div>
{!! $visibilityHtml !!}
<hr class="hs-divider"/>
<div class="hs-actions"><button type="button" class="hs-btn-remove" id="iframe-remove-btn"><i class="fa-regular fa-trash-can"></i> Remove</button><button type="button" class="hs-btn-cancel" id="iframe-cancel-btn">Cancel</button></div>
</div>

{{-- Tabs Settings --}}
<div id="tabs-settings-panel" style="display:none">
<button type="button" class="hs-back-btn" id="tabs-back-btn"><i class="fa-solid fa-arrow-left"></i> Tabs Settings</button>
<div class="hs-row mt-2"><button type="button" class="btn btn-outline-danger btn-sm w-100" id="tabs-add-btn">Add Tab</button></div>
{!! $visibilityHtml !!}
<hr class="hs-divider"/>
<div class="hs-actions"><button type="button" class="hs-btn-remove" id="tabs-remove-btn"><i class="fa-regular fa-trash-can"></i> Remove</button><button type="button" class="hs-btn-cancel" id="tabs-cancel-btn">Cancel</button></div>
</div>

{{-- Overlay Settings --}}
<div id="overlay-settings-panel" style="display:none">
<button type="button" class="hs-back-btn" id="ov-back-btn"><i class="fa-solid fa-arrow-left"></i> Overlay Settings</button>
<div class="hs-row"><label>Opacity (0-1)</label><input class="hs-input" id="ov-opacity" type="number" step="0.1" min="0" max="1" value="0.5"/></div>
<div class="hs-row"><label>Color</label><input class="hs-input" id="ov-color" type="color" value="#000000"/></div>
{!! $visibilityHtml !!}
<hr class="hs-divider"/>
<div class="hs-actions"><button type="button" class="hs-btn-remove" id="ov-remove-btn"><i class="fa-regular fa-trash-can"></i> Remove</button><button type="button" class="hs-btn-cancel" id="ov-cancel-btn">Cancel</button></div>
</div>

{{-- Blog Settings --}}
<div id="blog-settings-panel" style="display:none">
<button type="button" class="hs-back-btn" id="blg-back-btn"><i class="fa-solid fa-arrow-left"></i> Blog Settings</button>
<div class="hs-row"><label>Category</label><select class="hs-select" id="blg-category"><option value="all">All Categories</option></select></div>
<div class="hs-row"><label>Post Count</label><input class="hs-input" id="blg-count" type="number" value="3" min="1" max="12"/></div>
{!! $visibilityHtml !!}
<hr class="hs-divider"/>
<div class="hs-actions"><button type="button" class="hs-btn-remove" id="blg-remove-btn"><i class="fa-regular fa-trash-can"></i> Remove</button><button type="button" class="hs-btn-cancel" id="blg-cancel-btn">Cancel</button></div>
</div>

{{-- Content Block Settings --}}
<div id="content-block-settings-panel" style="display:none">
<button type="button" class="hs-back-btn" id="cb-back-btn"><i class="fa-solid fa-arrow-left"></i> Content Block</button>
<div class="hs-row"><label>Select Block</label><select class="hs-select" id="cb-id"><option value="">Choose a block...</option></select></div>
{!! $visibilityHtml !!}
<hr class="hs-divider"/>
<div class="hs-actions"><button type="button" class="hs-btn-remove" id="cb-remove-btn"><i class="fa-regular fa-trash-can"></i> Remove</button><button type="button" class="hs-btn-cancel" id="cb-cancel-btn">Cancel</button></div>
</div>

{{-- Body Types Settings --}}
<div id="body-types-settings-panel" style="display:none">
<button type="button" class="hs-back-btn" id="bt-back-btn"><i class="fa-solid fa-arrow-left"></i> Body Types</button>
<div class="hs-row"><label>Display Mode</label><select class="hs-select" id="bt-mode"><option value="grid">Grid</option><option value="list">List</option></select></div>
{!! $visibilityHtml !!}
<hr class="hs-divider"/>
<div class="hs-actions"><button type="button" class="hs-btn-remove" id="bt-remove-btn"><i class="fa-regular fa-trash-can"></i> Remove</button><button type="button" class="hs-btn-cancel" id="bt-cancel-btn">Cancel</button></div>
</div>

{{-- Plugin Settings --}}
<div id="plugin-settings-panel" style="display:none">
<button type="button" class="hs-back-btn" id="plg-back-btn"><i class="fa-solid fa-arrow-left"></i> Plugin Settings</button>
<div class="hs-row"><label>Plugin ID / Name</label><input class="hs-input" id="plg-id" placeholder="e.g. chat-widget"/></div>
{!! $visibilityHtml !!}
<hr class="hs-divider"/>
<div class="hs-actions"><button type="button" class="hs-btn-remove" id="plg-remove-btn"><i class="fa-regular fa-trash-can"></i> Remove</button><button type="button" class="hs-btn-cancel" id="plg-cancel-btn">Cancel</button></div>
</div>
