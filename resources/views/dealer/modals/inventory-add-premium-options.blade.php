{{-- Add Premium Build Option Modal --}}
<div class="vd-modal-overlay" id="modalAddOption">
    <div class="vd-modal">
    <form id="formAddOption" autocomplete="off">
        @csrf
        <div class="vd-modal-header">
            <span class="vd-modal-title">Add Premium Build Option</span>
            <button type="button" class="vd-modal-close" data-close-modal="modalAddOption">&times;</button>
        </div>
        <div class="vd-modal-body">
                <div class="vd-modal-field">
                    <label class="vd-modal-label">Factory Code <span class="req">*</span></label>
                    <input type="text" class="vd-input" name="factory_code" required>
                </div>
                <div class="vd-modal-field">
                    <label class="vd-modal-label">Category <span class="req">*</span></label>
                    <input type="text" class="vd-input" name="category" required>
                </div>
                <div class="vd-modal-field">
                    <label class="vd-modal-label">Name <span class="req">*</span></label>
                    <input type="text" class="vd-input" name="name" required>
                </div>
                <div class="vd-modal-field">
                    <label class="vd-modal-label">Description</label>
                    <textarea class="vd-modal-textarea" name="description"></textarea>
                </div>
                <div class="vd-modal-field">
                    <label class="vd-modal-label">MSRP</label>
                    <div class="vd-price-input-wrap" style="max-width:180px;">
                        <span class="vd-price-sym">$</span>
                        <input type="text" name="msrp" placeholder="__,___">
                    </div>
                </div>
        </div>
        <div class="vd-modal-footer">
            <button type="submit" class="vd-btn-save" id="btnSaveOption">
                <i class="bi bi-check-lg"></i> Save
            </button>
        </div>
    </form>
    </div>
</div>