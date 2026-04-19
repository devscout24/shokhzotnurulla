{{-- ══════════════════════════════
     EDIT / ADD REDIRECT MODAL
══════════════════════════════ --}}
<div class="er-overlay" id="redirectModal">
    <div class="er-modal">

        <div class="er-header">
            <h5 id="erModalTitle">Edit Redirect</h5>
            <button class="er-btn-close" id="btnCloseRedirect">&times;</button>
        </div>

        <div class="er-body">
            <div>
                <label class="er-label">Redirect from</label>
                <input type="text"
                       class="er-control"
                       id="erFrom"
                       placeholder="/old-page">
            </div>
            <div>
                <label class="er-label">Redirect to</label>
                <input type="text"
                       class="er-control"
                       id="erTo"
                       placeholder="/new-page">
            </div>
            <div>
                <label class="er-label">Regular expression?</label>
                <select class="er-select" id="erRegex">
                    <option value="0" selected>No</option>
                    <option value="1">Yes</option>
                </select>
            </div>
            <div>
                <label class="er-label">Type</label>
                <select class="er-select" id="erType">
                    <option value="301" selected>Permanent (301)</option>
                    <option value="302">Temporary (302)</option>
                </select>
            </div>
            <div>
                <label class="er-label">Status</label>
                <select class="er-select" id="erStatus">
                    <option value="1" selected>Enabled</option>
                    <option value="0">Disabled</option>
                </select>
            </div>
        </div>

        <div class="er-footer">
            <button type="button" class="er-btn-save" id="btnSaveRedirect">
                &#10003; Save
            </button>
        </div>

    </div>
</div>