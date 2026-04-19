<div class="ar-overlay" id="addRateModal">
    <div class="ar-modal">

        <div class="ar-header">
            <h5>Add Rate to Sheet</h5>
            <button class="btn-close-ar" id="btnCloseAR" type="button">&times;</button>
        </div>

        <div class="ar-body">

            <div class="ar-form-row">
                <div class="ar-form-col">
                    <label class="ar-label">Min. Model Year</label>
                    <select class="ar-select" id="arMinYear">
                        @for($y = date('Y') + 1; $y >= 2000; $y--)
                            <option value="{{ $y }}" {{ $y == date('Y') - 2 ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="ar-form-col">
                    <label class="ar-label">Max. Model Year</label>
                    <select class="ar-select" id="arMaxYear">
                        @for($y = date('Y') + 1; $y >= 2000; $y--)
                            <option value="{{ $y }}" {{ $y == date('Y') + 1 ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="ar-form-row">
                <div class="ar-form-col">
                    <label class="ar-label">Minimum Term (months)</label>
                    <input type="number" class="ar-control" id="arMinTerm" value="0" min="0" max="200">
                </div>
                <div class="ar-form-col">
                    <label class="ar-label">Maximum Term (months)</label>
                    <input type="number" class="ar-control" id="arMaxTerm" value="36" min="0" max="200">
                </div>
            </div>

            <div class="ar-form-row">
                <div class="ar-form-col">
                    <label class="ar-label">Min. Credit Score</label>
                    <input type="number" class="ar-control" id="arMinCredit" value="720" min="300" max="850">
                </div>
                <div class="ar-form-col">
                    <label class="ar-label">Max. Credit Score</label>
                    <input type="number" class="ar-control" id="arMaxCredit" value="759" min="300" max="850">
                </div>
            </div>

            <div class="ar-form-row">
                <div class="ar-form-col">
                    <label class="ar-label">Vehicle Condition</label>
                    <select class="ar-select" id="arCondition">
                        <option value="any">Any</option>
                        <option value="new" selected>New</option>
                        <option value="used">Used</option>
                        <option value="cpo">CPO</option>
                        <option value="vpo">VPO</option>
                    </select>
                </div>
                <div class="ar-form-col">
                    <label class="ar-label">Interest Rate <span class="req">*</span></label>
                    <div class="ar-rate-wrap">
                        <input type="number" step="0.01" class="ar-control" id="arRate"
                               value="5.99" min="0" max="99.99">
                        <span class="pct">%</span>
                    </div>
                </div>
            </div>

            {{-- Live Helper --}}
            <div class="ar-helper">
                <div class="ar-helper-title">Helper:</div>
                <ul id="arHelperList">
                    <li>Model years: <em id="hMinYear">—</em> - <em id="hMaxYear">—</em></li>
                    <li>Condition: <em id="hCondition">—</em></li>
                    <li>Loan term: <em id="hMinTerm">—</em> - <em id="hMaxTerm">—</em> months</li>
                    <li>Credit score: <em id="hMinCredit">—</em> - <em id="hMaxCredit">—</em></li>
                    <li>Rate: <em id="hRate">—</em>%</li>
                </ul>
            </div>

            <div class="ar-warning">
                Clicking "Add to Sheet" below will save the rate immediately.
                You can edit values inline and click <strong>Save</strong> to update them.
            </div>

        </div>

        <div class="ar-footer">
            <button type="button" class="ar-btn-add" id="btnAddToSheet">
                <i class="bi bi-check-lg me-1"></i> Add to Sheet
            </button>
        </div>
    </div>
</div>