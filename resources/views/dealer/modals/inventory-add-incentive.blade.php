{{-- ══════════════════════════
     Create / Edit Modal
══════════════════════════ --}}
<div class="modal fade" id="incentiveModal" tabindex="-1"
     data-bs-backdrop="true"
     data-bs-keyboard="true"
     aria-labelledby="incentiveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-semibold" id="incentiveModalLabel">Add Incentive</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="incentiveForm" method="POST" action="#" novalidate>
                @csrf
                <input type="hidden" id="incentiveId" value="">

                <div class="modal-body">
                    <div class="row g-3">

                        {{-- Title --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size:13px;">
                                Title <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="incTitle" name="title"
                                   class="form-control" placeholder="e.g. Summer Cash Back Special"
                                   style="font-size:13px;" required>
                            <div class="invalid-feedback" id="errTitle"></div>
                        </div>

                        {{-- Type + Category --}}
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">
                                Type <span class="text-danger">*</span>
                            </label>
                            <select id="incType" name="type" class="form-select" style="font-size:13px;" required>
                                <option value="">Select type...</option>
                                <option value="cash">Cash</option>
                                <option value="finance">Finance</option>
                                <option value="ivc_dvc">IVC / DVC</option>
                                <option value="lease">Lease</option>
                                <option value="percentage_off">Percentage Off</option>
                            </select>
                            <div class="invalid-feedback" id="errType"></div>
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">
                                Category <span class="text-danger">*</span>
                            </label>
                            <select id="incCategory" name="category" class="form-select" style="font-size:13px;" required>
                                <option value="all">All</option>
                                <option value="used">Used</option>
                                <option value="new">New</option>
                                <option value="cpo">CPO</option>
                            </select>
                            <div class="invalid-feedback" id="errCategory"></div>
                        </div>

                        {{-- Amount Type (pehle) --}}
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold" id="incAmountTypeLabel" style="font-size:13px;">
                                Amount Type
                            </label>
                            <select id="incAmountType" name="amount_type" class="form-select" style="font-size:13px;">
                                <option value="">— Select —</option>
                                <option value="fixed">Fixed ($)</option>
                                <option value="percent">Percent (%)</option>
                            </select>
                        </div>

                        {{-- Amount (baad mein) — grouped with sign --}}
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold" id="incAmountLabel" style="font-size:13px;">
                                Amount
                            </label>
                            <div class="input-group">
                                <span class="input-group-text" id="incAmountSign" style="font-size:13px; min-width:36px; justify-content:center;">
                                    #
                                </span>
                                <input type="number" id="incAmount" name="amount"
                                       class="form-control" placeholder="e.g. 1500"
                                       min="0" step="0.01" style="font-size:13px;">
                            </div>
                            <div class="invalid-feedback d-block" id="errAmount"></div>
                        </div>

                        {{-- Program Code --}}
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Program Code</label>
                            <input type="text" id="incProgramCode" name="program_code"
                                   class="form-control" placeholder="Optional"
                                   style="font-size:13px;">
                        </div>

                        {{-- Expires At --}}
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Expires At</label>
                            <input type="date" id="incExpiresAt" name="expires_at"
                                   class="form-control" style="font-size:13px;">
                        </div>

                        {{-- Description --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size:13px;">Description</label>
                            <textarea id="incDescription" name="description"
                                      class="form-control" rows="3"
                                      placeholder="Optional details..."
                                      style="font-size:13px;"></textarea>
                            <div class="invalid-feedback" id="errDesc"></div>
                        </div>

                        {{-- Checkboxes --}}
                        <div class="col-12">
                            <div class="d-flex gap-4 flex-wrap">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                           id="incIsGuaranteed" name="is_guaranteed" value="1">
                                    <label class="form-check-label" for="incIsGuaranteed" style="font-size:13px;">
                                        Guaranteed
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                           id="incIsFeatured" name="is_featured" value="1">
                                    <label class="form-check-label" for="incIsFeatured" style="font-size:13px;">
                                        Featured
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                           id="incIsEnabled" name="is_enabled" value="1" checked>
                                    <label class="form-check-label" for="incIsEnabled" style="font-size:13px;">
                                        Enabled
                                    </label>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary btn-sm"
                            data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm" id="incSubmitBtn">
                        <i class="bi bi-check-lg me-1"></i> Save Incentive
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

{{-- Hidden modal trigger --}}
<button type="button" id="incentiveModalTrigger"
        data-bs-toggle="modal"
        data-bs-target="#incentiveModal"
        style="display:none;"></button>