{{-- MODAL --}}
<div class="al-overlay" id="addLinkModal">
    <div class="al-modal">
        <div class="al-header">
            <h5 id="alModalTitle">Add Link</h5>
            <button class="al-btn-close" id="btnCloseAlModal">&times;</button>
        </div>
        <div class="al-body">
            <div class="al-field">
                <label class="al-label">Label</label>
                <input type="text" class="al-control" id="alLabel" placeholder="e.g. Inventory">
            </div>
            <div class="al-field">
                <label class="al-label">URL</label>
                <input type="text" class="al-control" id="alUrl" placeholder="e.g. /inventory">
            </div>
            <div class="al-field">
                <label class="al-label">Open in</label>
                <select class="al-select" id="alTarget">
                    <option value="_self">Same tab</option>
                    <option value="_blank">New tab</option>
                </select>
            </div>
            <div class="al-field" id="alParentWrap">
                <label class="al-label">Parent (optional)</label>
                <select class="al-select" id="alParent">
                    <option value="">— None (top level) —</option>
                    <option value="Inventory">Inventory</option>
                    <option value="Finance">Finance</option>
                    <option value="Service">Service</option>
                    <option value="Detailing">Detailing</option>
                    <option value="About">About</option>
                </select>
            </div>
        </div>
        <div class="al-footer">
            <button type="button" class="al-btn-cancel" id="btnCancelAlModal">Cancel</button>
            <button type="button" class="al-btn-save" id="btnSaveAlModal">&#10003; Save</button>
        </div>
    </div>
</div>