{{-- ADD INVENTORY MODAL --}}
<div class="inv-modal-overlay" id="invModalOverlay"
     data-url-models="{{ route('dealer.inventory.models') }}"
     data-url-vin-decode="{{ route('dealer.inventory.vin-decode') }}">

    <div class="inv-modal">

        {{-- Shared header --}}
        <div class="inv-modal-header">
            <span class="inv-modal-title">Add Inventory</span>
            <button type="button" class="inv-modal-close" id="invModalClose">&times;</button>
        </div>

        {{-- ────────────────────────
             STEP 1 : VIN entry
        ──────────────────────── --}}
        <div id="invStep1">
            <div class="inv-modal-body">

                <div class="inv-modal-notice">
                    <span class="inv-notice-icon">i</span>
                    <span>
                        New entries are marked as <strong><em>Draft</em></strong> by default.
                        They won't be visible to the public until you change the status to
                        <strong>Active For Sale</strong>.
                    </span>
                </div>

                <div class="inv-modal-label">VIN Number</div>
                <div class="inv-vin-wrap" id="invVinWrap">
                    <span class="inv-vin-icon">
                        <i class="bi bi-upc-scan"></i>
                    </span>
                    <input type="text"
                           id="invVinInput"
                           class="inv-vin-input"
                           placeholder="Enter 17-character VIN"
                           maxlength="17"
                           autocomplete="off"
                           required>
                </div>
                <div class="inv-vin-error" id="invVinError"></div>

            </div>
            <div class="inv-modal-footer">
                <button type="button" id="invBtnStep1Save" class="inv-btn-save">
                    Next <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        </div>
        {{-- end Step 1 --}}

        {{-- ────────────────────────
             STEP 2 : Vehicle details
        ──────────────────────── --}}
        <div id="invStep2" style="display:none;">

            <form method="POST"
                  action="{{ route('dealer.inventory.store') }}"
                  id="invStep2Form">
                @csrf

                {{-- Hidden VIN — populated by JS from step 1 --}}
                <input type="hidden" name="vin" id="invVinHidden">
                {{-- ── VIN-decoded hidden fields ────────────────────────── --}}
                <input type="hidden" id="invFuelTypeId"         name="fuel_type_id">
                <input type="hidden" id="invTransmissionTypeId" name="transmission_type_id">
                <input type="hidden" id="invDrivetrainTypeId"   name="drivetrain_type_id">
                <input type="hidden" id="invDoors"              name="doors">
                <input type="hidden" id="invEngine"             name="engine">
                <input type="hidden" id="invCylinders"          name="cylinders">
                <input type="hidden" id="invDisplacement"       name="displacement">
                <input type="hidden" id="invMaxHorsepower"      name="max_horsepower">
                <input type="hidden" id="invBlockType"          name="block_type">
                <input type="hidden" id="invTransmissionStd"    name="transmission_standard">
                <input type="hidden" id="invDrivetrainStd"      name="drivetrain_standard">
                <input type="hidden" id="invGvwr"               name="gvwr">

                <div class="inv-step2-body">

                    <div class="inv-modal-notice green">
                        <span class="inv-notice-icon green">
                            <i class="bi bi-check-lg"></i>
                        </span>
                        <span>
                            Please confirm the vehicle details below.
                            Correct anything before saving.
                        </span>
                    </div>

                    <div class="inv-form-grid">

                        {{-- Year --}}
                        <div class="inv-form-field">
                            <label class="inv-form-label">
                                Year <span class="inv-req">*</span>
                            </label>
                            <input type="number"
                                   id="invYear"
                                   name="year"
                                   class="inv-form-input"
                                   placeholder="{{ date('Y') }}"
                                   min="1900"
                                   max="{{ date('Y') + 2 }}"
                                   required>
                        </div>

                        {{-- Make --}}
                        <div class="inv-form-field">
                            <label class="inv-form-label">
                                Make <span class="inv-req">*</span>
                            </label>
                            <div class="inv-form-select-wrap">
                                <select class="inv-form-select"
                                        id="invMakeId"
                                        name="make_id"
                                        required>
                                    <option value="">Select Make</option>
                                    @foreach($makes as $make)
                                        <option value="{{ $make->id }}">{{ $make->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Model — AJAX populated on make change --}}
                        <div class="inv-form-field">
                            <label class="inv-form-label">
                                Model <span class="inv-req">*</span>
                            </label>
                            <div class="inv-form-select-wrap">
                                <select class="inv-form-select"
                                        id="invModelId"
                                        name="make_model_id"
                                        disabled
                                        required>
                                    <option value="">Select Make first</option>
                                </select>
                            </div>
                        </div>

                        {{-- Trim — AJAX populated on model change --}}
                        <div class="inv-form-field">
                            <label class="inv-form-label">Trim</label>
                            <input type="text"
                                   id="invTrimInput"
                                   name="trim"
                                   class="inv-form-input"
                                   placeholder="e.g. LE, Sport, Limited">
                        </div>

                        {{-- Stock # --}}
                        <div class="inv-form-field">
                            <label class="inv-form-label">
                                Stock # <span class="inv-req">*</span>
                            </label>
                            <input type="text"
                                   id="invStock"
                                   name="stock_number"
                                   class="inv-form-input"
                                   placeholder="e.g. A10234"
                                   required>
                        </div>

                        {{-- List Price --}}
                        <div class="inv-form-field">
                            <label class="inv-form-label">List Price</label>
                            <div class="inv-price-field">
                                <span class="inv-price-field-sym">$</span>
                                <input type="number"
                                       id="invListPrice"
                                       name="list_price"
                                       placeholder="0"
                                       min="0"
                                       step="1">
                            </div>
                        </div>

                        {{-- Exterior Color --}}
                        <div class="inv-form-field">
                            <label class="inv-form-label">Exterior Color</label>
                            <div class="inv-form-select-wrap">
                                <select class="inv-form-select" name="exterior_color_id">
                                    <option value="">Select Color</option>
                                    @foreach($colors as $color)
                                        <option value="{{ $color->id }}">{{ $color->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Interior Color --}}
                        <div class="inv-form-field">
                            <label class="inv-form-label">Interior Color</label>
                            <div class="inv-form-select-wrap">
                                <select class="inv-form-select" name="interior_color_id">
                                    <option value="">Select Color</option>
                                    @foreach($colors as $color)
                                        <option value="{{ $color->id }}">{{ $color->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Body Type — grouped ──────────────────────── --}}
                        <div class="inv-form-field">
                            <label class="inv-form-label">
                                Body Style <span class="inv-req">*</span>
                            </label>
                            <div class="inv-form-select-wrap">
                                <select class="inv-form-select"
                                        name="body_type_id"
                                        required>
                                    <option value="">Select...</option>
                                    @foreach($bodyTypeGroups as $group)
                                        <optgroup label="{{ $group->name }}">
                                            @foreach($group->bodyTypes as $bodyType)
                                                <option value="{{ $bodyType->id }}">
                                                    {{ $bodyType->name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Body Sub-type (style) — grouped ─────────── --}}
                        <div class="inv-form-field">
                            <label class="inv-form-label">Addl. Body Style</label>
                            <div class="inv-form-select-wrap">
                                <select class="inv-form-select" name="body_style_id">
                                    <option value="">Select...</option>
                                    @foreach($bodyStyleGroups as $group)
                                        <optgroup label="{{ $group->name }}">
                                            @foreach($group->bodyStyles as $bodyStyle)
                                                <option value="{{ $bodyStyle->id }}">
                                                    {{ $bodyStyle->name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Mileage --}}
                        <div class="inv-form-field">
                            <label class="inv-form-label">
                                Mileage <span class="inv-req">*</span>
                            </label>
                            <input type="text"
                                   id="invMileage"
                                   name="mileage"
                                   class="inv-form-input"
                                   placeholder="__,___"
                                   required>
                        </div>

                        {{-- Condition --}}
                        <div class="inv-form-field">
                            <label class="inv-form-label">
                                Condition <span class="inv-req">*</span>
                            </label>
                            <div class="inv-form-select-wrap">
                                <select class="inv-form-select"
                                        name="vehicle_condition"
                                        required>
                                    <option value="">Select Condition</option>
                                    <option value="Used">Used</option>
                                    <option value="New">New</option>
                                    <option value="Certified Pre-Owned">Certified Pre-Owned</option>
                                </select>
                            </div>
                        </div>

                        {{-- Location — read only, always current dealer --}}
                        <div class="inv-form-field inv-form-field-full">
                            <label class="inv-form-label">
                                Location <span class="inv-req">*</span>
                            </label>
                            <input type="text"
                                   class="inv-form-input"
                                   value="{{ $dealer->name }}"
                                   readonly
                                   style="background:#f7f7f7;color:#888;cursor:default;">
                        </div>

                    </div>{{-- end .inv-form-grid --}}
                </div>{{-- end .inv-step2-body --}}

                <div class="inv-modal-footer space-between">
                    <button type="button" id="invBtnBack" class="inv-btn-back">
                        <i class="bi bi-arrow-left"></i> Back
                    </button>
                    <button type="submit" id="invBtnStep2Save" class="inv-btn-save">
                        <i class="bi bi-check-lg"></i> Save
                    </button>
                </div>

            </form>
        </div>
        {{-- end Step 2 --}}

    </div>{{-- end .inv-modal --}}
</div>
{{-- end .inv-modal-overlay --}}