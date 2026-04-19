{{-- ══════════════════════════════════════════════════
     FIND CONTACT CANVAS
══════════════════════════════════════════════════ --}}
<div class="vd-canvas-overlay" id="canvasOverlay"></div>

<div class="vd-canvas" id="findContactCanvas">

    {{-- Canvas header --}}
    <div class="vd-canvas-header">
        <i class="bi bi-search"></i>
        <span class="vd-canvas-title">Find Contact</span>
        <button type="button" class="vd-canvas-close" id="canvasClose">&times;</button>
    </div>

    {{-- Search bar --}}
    <div class="vd-canvas-search-wrap">
        <div class="vd-canvas-search">
            <i class="bi bi-search"></i>
            <input type="text" placeholder="Search by name or email" id="contactSearch">
        </div>
    </div>

    {{-- Screen 1: Search + Create New --}}
    <div class="vd-canvas-screen active" id="screenSearch">
        <button type="button" class="vd-btn-create-new" id="btnCreateNew">
            <i class="bi bi-person-plus-fill"></i> Create New
        </button>
        {{-- search results would appear here --}}
        <div style="flex:1;display:flex;align-items:center;justify-content:center;">
            <p style="font-size:13px;color:#bbb;">Start typing to search contacts</p>
        </div>
    </div>

    {{-- Screen 2: Create New Contact form --}}
    <div class="vd-canvas-screen" id="screenCreateNew" style="flex-direction:column;">
        <button type="button" class="vd-canvas-back" id="btnCanvasBack">
            <i class="bi bi-arrow-left"></i> Back
        </button>
        <div class="vd-canvas-form">
            <div class="vd-cf-grid">
                <div class="vd-cf-field">
                    <label class="vd-cf-label">Sold Price</label>
                    <div class="vd-price-input-wrap">
                        <span class="vd-price-sym">$</span>
                        <input type="text" placeholder="__,___">
                    </div>
                </div>
                <div class="vd-cf-field">
                    <label class="vd-cf-label">Sold Date</label>
                    <div class="vd-date-wrap">
                        <span class="vd-date-icon"><i class="bi bi-calendar3"></i></span>
                        <input type="text" placeholder="">
                    </div>
                </div>
                <div class="vd-cf-field">
                    <label class="vd-cf-label">First Name</label>
                    <input type="text" class="vd-input" placeholder="Enter some text">
                </div>
                <div class="vd-cf-field">
                    <label class="vd-cf-label">Last Name</label>
                    <input type="text" class="vd-input" placeholder="Enter some text">
                </div>
                <div class="vd-cf-field">
                    <label class="vd-cf-label">Email</label>
                    <input type="email" class="vd-input" placeholder="email@domain.com">
                </div>
                <div class="vd-cf-field">
                    <label class="vd-cf-label">Phone</label>
                    <input type="text" class="vd-input" placeholder="(__)  __-____">
                </div>
                <div class="vd-cf-field vd-cf-field-full">
                    <label class="vd-cf-label">Address</label>
                    <input type="text" class="vd-input" placeholder="">
                </div>
            </div>
        </div>
        <div class="vd-canvas-footer">
            <button type="button" class="vd-btn-canvas-save">Save</button>
        </div>
    </div>

</div>