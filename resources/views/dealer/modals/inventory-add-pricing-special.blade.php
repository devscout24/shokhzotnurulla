<div class="ps-modal-overlay" id="addPricingSpecialModal">
    <div class="ps-modal">

        {{-- Header --}}
        <div class="ps-modal-header">
            <h5 id="psModalTitle">Add Pricing Special</h5>
            <button class="btn-close-modal" id="btnClosePsModal" type="button">&times;</button>
        </div>

        {{-- Body --}}
        <div class="ps-modal-body">
            <form id="addPricingSpecialForm" novalidate>
                <input type="hidden" id="psSpecialId" value="">

                {{-- Row 1: Title / Require form fill / Priority --}}
                <div class="ps-form-row">
                    <div class="ps-form-col">
                        <label class="ps-form-label">Title <span class="ps-req">*</span></label>
                        <input type="text" id="psTitle" name="title"
                               class="ps-form-control" placeholder="Enter some text">
                        <div class="ps-invalid-feedback" id="errPsTitle"></div>
                    </div>
                    <div class="ps-form-col">
                        <label class="ps-form-label">Require form fill?</label>
                        <select id="psType" name="type" class="ps-form-select">
                            <option value="">Select...</option>
                            <option value="formfill">Yes</option>
                            <option value="override">No</option>
                        </select>
                    </div>
                    <div class="ps-form-col">
                        <label class="ps-form-label">Priority (higher takes precedence)</label>
                        <input type="number" id="psPriority" name="priority"
                               class="ps-form-control" value="0" min="0" max="100">
                    </div>
                </div>

                {{-- Row 2: Discount Type + dynamic fields --}}
                <div class="ps-form-row">
                    {{-- Stackable — override only --}}
                    <div class="ps-form-col" id="psStackableWrap" style="display:none;">
                        <label class="ps-form-label">Stackable?</label>
                        <select id="psStackable" name="stackable" class="ps-form-select">
                            <option value="">Select...</option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>

                    {{-- Button Text — formfill only --}}
                    <div class="ps-form-col" id="psButtonTextWrap" style="display:none;">
                        <label class="ps-form-label">Button Text Override</label>
                        <input type="text" id="psButtonText" name="button_text"
                               class="ps-form-control" placeholder="Get e-Price">
                    </div>

                    {{-- Stackable — override only --}}
                    <div class="ps-form-col" style="max-width:220px;">
                        <label class="ps-form-label">Discount Type</label>
                        <select id="psDiscountType" name="discount_type" class="ps-form-select">
                            <option value="">Select...</option>
                            <option value="fixed">Fixed Amount</option>
                            <option value="percentage">Percentage off</option>
                            <option value="dollars">Dollars off</option>
                            <option value="offsetdollar">Offset dollars off</option>
                            <option value="special">Special Financing</option>
                            <option value="offsetincrease">Offset price increase</option>
                            <option value="increase">Price increase</option>
                        </select>
                        <div class="ps-invalid-feedback" id="errPsDiscountType"></div>
                    </div>

                    {{-- Amount --}}
                    <div class="ps-form-col" id="psAmountWrap" style="display:none;">
                        <label class="ps-form-label" id="psAmountLabel">Amount</label>
                        <div class="ps-input-group">
                            <span class="ps-input-group-text" id="psAmountSign">$</span>
                            <input type="text" id="psAmount" name="amount"
                                   class="ps-form-control" placeholder="__,___">
                            <span class="ps-input-group-text ps-input-group-text-right"
                                  id="psAmountSuffix" style="display:none;">%</span>
                        </div>
                    </div>

                    {{-- Discount Label — override only --}}
                    <div class="ps-form-col" id="psDiscountLabelWrap" style="display:none;">
                        <label class="ps-form-label">Discount Label</label>
                        <input type="text" id="psDiscountLabel" name="discount_label"
                               class="ps-form-control" placeholder="Call for e-Price">
                    </div>
                </div>

                {{-- ── ROW 3: Finance Rate + Term — special only ── --}}
                <div class="ps-form-row" id="psFinanceWrap" style="display:none;">
                    <div class="ps-form-col" style="max-width:200px;">
                        <label class="ps-form-label">Finance Rate</label>
                        <div class="ps-input-group">
                            <input type="text" id="psFinanceRate" name="finance_rate"
                                   class="ps-form-control" placeholder="0.9">
                            <span class="ps-input-group-text ps-input-group-text-right">%</span>
                        </div>
                    </div>
                    <div class="ps-form-col" style="max-width:200px;">
                        <label class="ps-form-label">Finance Term (months)</label>
                        <div class="ps-input-group">
                            <input type="number" id="psFinanceTerm" name="finance_term"
                                   class="ps-form-control" placeholder="36">
                            <span class="ps-input-group-text ps-input-group-text-right">mo.</span>
                        </div>
                    </div>
                </div>

                {{-- ── Vehicle Selection ── --}}
                <div class="ps-section-title">Vehicle Selection</div>

                <label class="ps-form-label">Location(s):</label>
                <button type="button" class="ps-locations-btn">
                    <i class="bi bi-geo-alt-fill" style="color:#c0392b;font-size:12px;"></i>
                    All Locations
                    <i class="bi bi-chevron-down ms-auto" style="font-size:10px;color:#888;"></i>
                </button>

                <div class="ps-form-row">
                    <div class="ps-form-col">
                        <label class="ps-form-label">Condition</label>
                        <select id="psCondition" name="condition" class="ps-form-select ps-criteria">
                            <option value="">Select...</option>
                            <option value="New">New</option>
                            <option value="Pre-owned">Pre-owned</option>
                        </select>
                    </div>
                    <div class="ps-form-col">
                        <label class="ps-form-label">Certified?</label>
                        <select id="psIsCertified" name="is_certified" class="ps-form-select ps-criteria">
                            <option value="">Select...</option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="ps-form-col">
                        <label class="ps-form-label">Model Number</label>
                        <input type="text" id="psModelNumber" name="model_number"
                               class="ps-form-control ps-criteria" placeholder="Enter some text">
                    </div>
                </div>

                <div class="ps-form-row">
                    <div class="ps-form-col">
                        <label class="ps-form-label">Year</label>
                        <select id="psYear" name="year" class="ps-form-select ps-criteria">
                            <option value="">Select...</option>
                            @for($y = date('Y') + 1; $y >= 2000; $y--)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="ps-form-col">
                        <label class="ps-form-label">Make</label>
                        <select id="psMakeId" name="make_id" class="ps-form-select ps-criteria">
                            <option value="">Select...</option>
                            @foreach($makes as $make)
                                <option value="{{ $make->id }}">{{ $make->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="ps-form-col">
                        <label class="ps-form-label">Model</label>
                        <select id="psMakeModelId" name="make_model_id"
                                class="ps-form-select ps-criteria" disabled>
                            <option value="">Select...</option>
                        </select>
                    </div>
                </div>

                <div class="ps-form-row">
                    <div class="ps-form-col">
                        <label class="ps-form-label">Trim</label>
                        <input type="text" id="psTrim" name="trim"
                               class="ps-form-control ps-criteria" placeholder="Select...">
                    </div>
                    <div class="ps-form-col">
                        <label class="ps-form-label">Body Style</label>
                        <select id="psBodyStyle" name="body_style"
                                class="ps-form-select ps-criteria">
                            <option value="">Select...</option>
                            @foreach($bodyStyles as $bodyStyle)
                                <option value="{{ $bodyStyle->name }}">{{ $bodyStyle->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="ps-form-col">
                        <label class="ps-form-label">Exterior Color</label>
                        <select id="psExteriorColorId" name="exterior_color_id"
                                class="ps-form-select ps-criteria">
                            <option value="">Select...</option>
                            @foreach($colors as $color)
                                <option value="{{ $color->id }}">{{ $color->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="ps-form-row">
                    <div class="ps-form-col">
                        <label class="ps-form-label">Stock Number</label>
                        <select id="psStockNumber" name="stock_number"
                                class="ps-form-select ps-criteria">
                            <option value="">Select...</option>
                            @foreach($stockNumbers as $sn)
                                <option value="{{ $sn }}">{{ $sn }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="ps-form-col">
                        <label class="ps-form-label">Tag</label>
                        <input type="text" id="psTag" name="tag"
                               class="ps-form-control ps-criteria" placeholder="Enter some text">
                    </div>
                    <div class="ps-form-col"></div>
                </div>

                <div class="ps-form-row">
                    <div class="ps-form-col">
                        <label class="ps-form-label">Min. days in stock</label>
                        <input type="text" id="psMinDays" name="min_days"
                               class="ps-form-control ps-criteria" placeholder="_,___">
                    </div>
                    <div class="ps-form-col">
                        <label class="ps-form-label">Max. days in stock</label>
                        <input type="text" id="psMaxDays" name="max_days"
                               class="ps-form-control ps-criteria" placeholder="_,___">
                    </div>
                    <div class="ps-form-col"></div>
                </div>

                {{-- Match count --}}
                <div class="ps-match-box" id="psMatchBox">
                    <span id="psMatchCount">—</span> vehicles match your criteria
                </div>

                {{-- ── Schedule & Disclaimer ── --}}
                <div class="ps-section-title">Schedule &amp; Disclaimer</div>

                {{-- Send Email + Hide Price — formfill only --}}
                <div id="psSendEmailRow" class="ps-form-row">
                    <div class="ps-form-col">
                        <label class="ps-form-label">Send Email Confirmation</label>
                        <select id="psSendEmail" name="send_email" class="ps-form-select">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                    <div class="ps-form-col d-flex align-items-end pb-1">
                        <label class="ps-form-label" style="margin-bottom:0;">
                            After form is submitted on website
                        </label>
                        <label class="ps-toggle ms-auto">
                            <input type="checkbox" id="psHidePrice" name="hide_price" value="1">
                            <span class="ps-toggle-slider"></span>
                        </label>
                        <span class="ps-toggle-label ms-2" style="font-size:12px;color:#555;">
                            Do not display discounted price on website
                        </span>
                    </div>
                </div>

                <div class="ps-form-row">
                    <div class="ps-form-col">
                        <label class="ps-form-label">Start date</label>
                        <div class="ps-date-wrap">
                            <span class="cal-icon"><i class="bi bi-calendar3"></i></span>
                            <input type="date" id="psStartsAt" name="starts_at"
                                   class="ps-form-control">
                        </div>
                    </div>
                    <div class="ps-form-col">
                        <label class="ps-form-label">End date</label>
                        <div class="ps-date-wrap">
                            <span class="cal-icon"><i class="bi bi-calendar3"></i></span>
                            <input type="date" id="psEndsAt" name="ends_at"
                                   class="ps-form-control">
                        </div>
                    </div>
                </div>

                <div class="ps-form-row">
                    <div class="ps-form-col">
                        <label class="ps-form-label">Internal Notes</label>
                        <textarea id="psNotes" name="notes"
                                  class="ps-form-textarea"
                                  placeholder="Enter some text"></textarea>
                    </div>
                    <div class="ps-form-col">
                        <label class="ps-form-label">Disclaimer</label>
                        <textarea id="psDisclaimer" name="disclaimer"
                                  class="ps-form-textarea"
                                  placeholder="Enter some text"></textarea>
                    </div>
                </div>

            </form>
        </div>

        {{-- Footer --}}
        <div class="ps-modal-footer">
            <button type="button" class="ps-btn-cancel" id="btnCancelPsModal">Cancel</button>
            <button type="button" class="ps-btn-save" id="psSaveBtn">
                <i class="bi bi-check-lg me-1"></i> Save
            </button>
        </div>

    </div>
</div>