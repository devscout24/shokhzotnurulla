{{-- Add Printable Modal --}}
<div class="vd-modal-overlay" id="modalAddPrintable">
    <div class="vd-modal">
        <div class="vd-modal-header">
            <span class="vd-modal-title">Add Printable</span>
            <button type="button" class="vd-modal-close" data-close-modal="modalAddPrintable">&times;</button>
        </div>
        <div class="vd-modal-body">

            <div class="vd-modal-row" style="margin-bottom:14px;">
                <div class="vd-modal-field" style="margin-bottom:0;">
                    <label class="vd-modal-label">
                        Name <span class="req" style="color:#c0392b;">*</span>
                    </label>
                    <select class="vd-select" id="printableNameSelect">
                        <option value="">Select...</option>
                        {{-- Options populated dynamically by JS (only available/unused types shown) --}}
                    </select>
                    <div style="font-size:11.5px;color:#aaa;margin-top:5px;">
                        Only one of each printable can be created.
                    </div>
                </div>
                <div class="vd-modal-field" style="margin-bottom:0;">
                    <label class="vd-modal-label">CTA</label>
                    <select class="vd-select" id="printableCtaSelect">
                        <option value="">Select...</option>
                        <option value="Print Sticker">Print Sticker</option>
                        <option value="Print Guide">Print Guide</option>
                        <option value="Generate Quote">Generate Quote</option>
                    </select>
                </div>
            </div>

            <div class="vd-modal-field">
                <label class="vd-modal-label">Description</label>
                <input type="text" class="vd-input" id="printableDescription" placeholder="Optional description">
            </div>

            <div class="vd-modal-field">
                <label class="vd-modal-label">Layout</label>
                <select class="vd-select" id="printableLayout" style="max-width:200px;">
                    <option value="portrait">Portrait</option>
                    <option value="landscape">Landscape</option>
                </select>
            </div>

        </div>
        <div class="vd-modal-footer">
            <button type="button" class="vd-btn-cancel" data-close-modal="modalAddPrintable">Cancel</button>
            <button type="button" class="vd-btn-save" id="btnCreatePrintable">
                <i class="bi bi-check-lg"></i> Create
            </button>
        </div>
    </div>
</div>