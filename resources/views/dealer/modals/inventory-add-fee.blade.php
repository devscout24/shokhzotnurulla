<div class="aif-overlay" id="addFeeModal">
    <div class="aif-modal">

        <div class="aif-header">
            <h5 id="feeModalTitle">Add Inventory Fee</h5>
            <button class="aif-btn-close" id="btnCloseFeeModal" type="button">&times;</button>
        </div>

        <div class="aif-body">

            {{-- Name --}}
            <div class="aif-form-group">
                <label class="aif-label">Name <span class="req">*</span></label>
                <input type="text" class="aif-control" id="feeName" placeholder="Enter some text">
            </div>

            {{-- Description --}}
            <div class="aif-form-group">
                <label class="aif-label">Description</label>
                <input type="text" class="aif-control" id="feeDescription" placeholder="Enter some text">
            </div>

            {{-- Type + Amount --}}
            <div class="aif-form-row">
                <div class="aif-form-group">
                    <label class="aif-label">Type <span class="req">*</span></label>
                    <select class="aif-select" id="feeType">
                        <option value="">Select...</option>
                        <option value="amount">Amount</option>
                        <option value="percentage">Percentage</option>
                    </select>
                </div>
                <div class="aif-form-group">
                    <label class="aif-label">Amount <span class="req">*</span></label>
                    <div class="aif-amount-wrap">
                        <span class="aif-amount-prefix" id="feeAmountPrefix">$</span>
                        <input type="number" step="0.01" class="aif-control aif-amount-input"
                               id="feeValue" placeholder="0.00">
                        <span class="aif-amount-suffix" id="feeAmountSuffix" style="display:none;">%</span>
                    </div>
                </div>
            </div>

            {{-- Pre-tax + Optional --}}
            <div class="aif-form-row">
                <div class="aif-form-group">
                    <label class="aif-label">Pre-tax or post-tax <span class="req">*</span></label>
                    <select class="aif-select" id="feeTax">
                        <option value="">Select...</option>
                        <option value="post-tax">Post-tax</option>
                        <option value="pre-tax">Pre-tax</option>
                    </select>
                </div>
                <div class="aif-form-group">
                    <label class="aif-label">Optional or guaranteed <span class="req">*</span></label>
                    <select class="aif-select" id="feeOptional">
                        <option value="">Select...</option>
                        <option value="0">Guaranteed</option>
                        <option value="1">Optional</option>
                    </select>
                </div>
            </div>

            {{-- Vehicle Condition --}}
            <div class="aif-form-group">
                <label class="aif-label">Vehicle Condition</label>
                <select class="aif-select" id="feeCondition" style="max-width:200px;">
                    <option value="any">Any</option>
                    <option value="new">New</option>
                    <option value="used">Used</option>
                    <option value="cpo">CPO</option>
                    <option value="vpo">VPO</option>
                </select>
            </div>

        </div>

        <div class="aif-footer">
            <button type="button" class="aif-btn-save" id="btnSaveFee">
                <i class="bi bi-check-lg me-1"></i> Save
            </button>
        </div>
    </div>
</div>