{{-- ══════════════════════════════
     ADD / EDIT IP MODAL
══════════════════════════════ --}}
<div class="ip-overlay" id="ipModal">
    <div class="ip-modal">
        <div class="ip-modal-header">
            <h5 id="ipModalTitle">Add IP Address</h5>
            <button class="ip-modal-close" id="btnCloseIpModal">&times;</button>
        </div>
        <p class="ip-modal-note">
            Add a single IP address. CIDR ranges are not allowed by the API.
        </p>
        <div class="ip-modal-body">
            <div class="ip-field-row">
                <label class="ip-field-label">IP Address</label>
                <input type="text" class="ip-field-input" id="ipAddressInput" placeholder="Enter some text">
            </div>
            <div class="ip-field-row">
                <label class="ip-field-label">Description</label>
                <input type="text" class="ip-field-input" id="ipDescInput" placeholder="Enter some text">
            </div>
        </div>
        <div class="ip-modal-footer">
            <button type="button" class="ip-btn-save" id="btnSaveIp">✓ Save</button>
        </div>
    </div>
</div>