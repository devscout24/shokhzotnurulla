@extends('layouts.dealer.app')

@section('title', __('Website Settings') . ' | '. __(config('app.name')))

@push('page-assets')
    @vite([
        'resources/css/dealer/pages/website-settings-general.css',
        // 'resources/js/dealer/pages/website-settings-general.js',
    ])
@endpush

@section('page-content')
    <main class="main-content" id="mainContent">
        <div class="page-header">
            <h2 class="view-title">Website Settings</h2>
        </div>

        <div class="view-content" data-view="general">
            <div class="d-flex" style="min-height: calc(100vh - 60px); background: #f2f2f2; font-size: 13px;">

                {{-- ── LEFT SIDEBAR ── --}}
                <aside class="ws-sidebar">
                    <a href="{{ route('dealer.website.settings.general') }}" class="menu-item active">
                        <span class="ws-icon"><i class="bi bi-info-circle"></i></span>
                        <span>General</span>
                    </a>
                    <a href="{{ route('dealer.website.settings.locations') }}" class="menu-item">
                        <span class="ws-icon"><i class="bi bi-geo-alt"></i></span>
                        <span>Locations &amp; Hours</span>
                    </a>
                    <a href="{{ route('dealer.website.settings.banners') }}" class="menu-item">
                        <span class="ws-icon"><i class="bi bi-card-image"></i></span>
                        <span>Banners /<br>Announcements</span>
                    </a>
                    <a href="{{ route('dealer.website.settings.finance') }}" class="menu-item">
                        <span class="ws-icon"><i class="bi bi-percent"></i></span>
                        <span>Interest Rates</span>
                    </a>
                    <a href="{{ route('dealer.website.settings.retail') }}" class="menu-item">
                        <span class="ws-icon"><i class="bi bi-grid"></i></span>
                        <span>Digital Retail</span>
                    </a>
                    {{-- <a href="{{ route('dealer.website.settings.domains') }}" class="menu-item">
                        <span class="ws-icon"><i class="bi bi-globe"></i></span>
                        <span>Domains</span>
                    </a> --}}
                    <a href="{{ route('dealer.website.settings.redirects') }}" class="menu-item">
                        <span class="ws-icon"><i class="bi bi-arrow-left-right"></i></span>
                        <span>Redirects</span>
                    </a>
                    <a href="{{ route('dealer.website.settings.ips.index') }}" class="menu-item">
                        <span class="ws-icon"><i class="bi bi-hdd-network"></i></span>
                        <span>Dealer IP Addresses</span>
                    </a>
                </aside>

                {{-- ── RIGHT CONTENT ── --}}
                <div class="flex-grow-1 p-4 overflow-auto">
                    <div class="bg-white border rounded" style="max-width: 1200px;">

                        {{-- Card Title --}}
                        <div class="px-3 py-3 border-bottom fw-semibold" style="font-size: 14px; color: #333;">
                            General
                        </div>

                        {{-- Legal Name --}}
                        <div class="ws-setting-row">
                            <div class="ws-label-col">
                                <div class="fw-semibold text-dark" style="font-size:13px;">Legal Name</div>
                            </div>
                            <div class="ws-input-col">
                                <input type="text"
                                       name="legal_name"
                                       class="form-control form-control-sm"
                                       value="{{ old('legal_name', $dealer->legal_name) }}">
                            </div>
                        </div>

                        {{-- Corporate Address --}}
                        <div class="ws-setting-row">
                            <div class="ws-label-col">
                                <div class="fw-semibold text-dark mb-1" style="font-size:13px;">Corporate Address</div>
                                <div class="text-muted" style="font-size:11.5px;">
                                    The full corporate mailing address of the dealership
                                </div>
                            </div>
                            <div class="ws-input-col">
                                <input type="text"
                                       name="corporate_address"
                                       class="form-control form-control-sm"
                                       value="{{ old('corporate_address', $dealer->corporate_address) }}">
                            </div>
                        </div>

                        {{-- Corporate Support Email --}}
                        <div class="ws-setting-row">
                            <div class="ws-label-col">
                                <div class="fw-semibold text-dark" style="font-size:13px;">Corporate Support Email</div>
                            </div>
                            <div class="ws-input-col">
                                <input type="email"
                                       name="support_email"
                                       class="form-control form-control-sm"
                                       value="{{ old('support_email', $dealer->support_email) }}">
                            </div>
                        </div>

                        {{-- Abandoned Form Minutes --}}
                        <div class="ws-setting-row">
                            <div class="ws-label-col">
                                <div class="fw-semibold text-dark mb-1" style="font-size:13px;">Abandoned Form Minutes</div>
                                <div class="text-muted" style="font-size:11.5px;">
                                    This is the number of minutes past which a multi-step form that is partially
                                    completed will be considered abandoned.
                                </div>
                            </div>
                            <div class="ws-input-col">
                                <input type="number"
                                       name="abandoned_form_minutes"
                                       class="form-control form-control-sm"
                                       value="{{ old('abandoned_form_minutes', $dealer->abandoned_form_minutes ?? 45) }}"
                                       min="1">
                            </div>
                        </div>

                        {{-- Save --}}
                        <div class="px-3 py-3">
                            <button type="button" class="ws-btn-save" id="btnSaveGeneral">
                                &#10003; Save
                            </button>
                        </div>

                    </div>{{-- end card --}}

                    {{-- ══════════════════════════════
                         DISCLAIMERS SECTION
                    ══════════════════════════════ --}}
                    <div class="disc-section">
                        <div class="disc-section-title">Disclaimers</div>

                        {{-- Finance Disclaimer --}}
                        <div class="disc-row">
                            <div class="disc-label-col">
                                <div class="disc-label">Finance Disclaimer</div>
                                <div class="disc-hint">
                                    This disclaimer may be displayed in various modules like finance apps or payment calculators.
                                </div>
                            </div>
                            <div class="disc-editor-col">
                                <div class="rte-wrapper">
                                    <div class="rte-toolbar" data-target="finance_disclaimer">
                                        <button type="button" data-cmd="bold"        title="Bold"><b>B</b></button>
                                        <button type="button" data-cmd="italic"      title="Italic"><i>I</i></button>
                                        <button type="button" data-cmd="underline"   title="Underline"><u>U</u></button>
                                        <div class="rte-sep"></div>
                                        <button type="button" data-cmd="justifyLeft"   title="Align Left">&#8676;</button>
                                        <button type="button" data-cmd="justifyCenter" title="Center">&#8596;</button>
                                        <button type="button" data-cmd="justifyRight"  title="Align Right">&#8677;</button>
                                        <div class="rte-sep"></div>
                                        <button type="button" data-cmd="insertUnorderedList" title="Bullet List">&#8226;&#8801;</button>
                                        <button type="button" data-cmd="insertOrderedList"   title="Numbered List">1&#8801;</button>
                                        <div class="rte-sep"></div>
                                        <button type="button" data-cmd="outdent"  title="Outdent">&#8676;</button>
                                        <button type="button" data-cmd="indent"   title="Indent">&#8677;</button>
                                        <div class="rte-sep"></div>
                                        <button type="button" data-cmd="createLink" title="Insert Link">&#128279;</button>
                                    </div>
                                    <div class="rte-body rte-lg"
                                         id="editor_finance"
                                         contenteditable="true"
                                         data-placeholder="Enter finance disclaimer...">{{ $dealer->finance_disclaimer ?? 'The payment estimator is not an advertisement or offer for specific terms of credit and actual terms may vary. Payment amounts presented are for illustrative purposes only and may not be available. Not all models are available in all states. Actual vehicle price may vary by Dealer. The Estimated Monthly Payment amount calculated is based on the variables entered, the price of the vehicle you entered, the term you select, the down payment you enter, the Annual Percentage Rate (APR) you select, and any net trade-in amount. The payment estimate displayed does not include taxes, title, license and/or registration fees. Payment amount is for illustrative purposes only. Actual prices may vary by Dealer. Payment amounts may be different due to various factors such as fees, specials, rebates, term, down payment, APR, net trade-in, and applicable tax rate. Actual APR is based on available finance programs and the creditworthiness of the customer. Not all customers will qualify for credit or for the lowest rate. Please contact an authorized dealer for actual rates, program details and actual terms.' }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- Inventory Disclaimer --}}
                        <div class="disc-row">
                            <div class="disc-label-col">
                                <div class="disc-label">Inventory Disclaimer</div>
                                <div class="disc-hint">
                                    This disclaimer will be displayed at the bottom of your SRP/VDP pages.
                                </div>
                            </div>
                            <div class="disc-editor-col">
                                <div class="rte-wrapper">
                                    <div class="rte-toolbar" data-target="inventory_disclaimer">
                                        <button type="button" data-cmd="bold"><b>B</b></button>
                                        <button type="button" data-cmd="italic"><i>I</i></button>
                                        <button type="button" data-cmd="underline"><u>U</u></button>
                                        <div class="rte-sep"></div>
                                        <button type="button" data-cmd="justifyLeft">&#8676;</button>
                                        <button type="button" data-cmd="justifyCenter">&#8596;</button>
                                        <button type="button" data-cmd="justifyRight">&#8677;</button>
                                        <div class="rte-sep"></div>
                                        <button type="button" data-cmd="insertUnorderedList">&#8226;&#8801;</button>
                                        <button type="button" data-cmd="insertOrderedList">1&#8801;</button>
                                        <div class="rte-sep"></div>
                                        <button type="button" data-cmd="outdent">&#8676;</button>
                                        <button type="button" data-cmd="indent">&#8677;</button>
                                        <div class="rte-sep"></div>
                                        <button type="button" data-cmd="createLink">&#128279;</button>
                                    </div>
                                    <div class="rte-body"
                                         id="editor_inventory"
                                         contenteditable="true"
                                         data-placeholder="Enter inventory disclaimer...">{{ $dealer->inventory_disclaimer ?? "It is the customer's sole responsibility to verify the existence and condition of any equipment listed. Neither the dealership nor eBizAutos is responsible for misprints on prices or equipment. It is the customer's sole responsibility to verify the accuracy of the prices with the dealer, including the pricing for all added accessories. Financing Fees apply for all vehicles not purchased with cash. See Dealer for details." }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- Deposit Disclaimer --}}
                        <div class="disc-row">
                            <div class="disc-label-col">
                                <div class="disc-label">Deposit disclaimer</div>
                                <div class="disc-hint">
                                    Disclaimer text to be displayed on the deposit credit card form.
                                </div>
                            </div>
                            <div class="disc-editor-col">
                                <div class="rte-wrapper">
                                    <div class="rte-toolbar" data-target="deposit_disclaimer">
                                        <button type="button" data-cmd="bold"><b>B</b></button>
                                        <button type="button" data-cmd="italic"><i>I</i></button>
                                        <button type="button" data-cmd="underline"><u>U</u></button>
                                        <div class="rte-sep"></div>
                                        <button type="button" data-cmd="justifyLeft">&#8676;</button>
                                        <button type="button" data-cmd="justifyCenter">&#8596;</button>
                                        <button type="button" data-cmd="justifyRight">&#8677;</button>
                                        <div class="rte-sep"></div>
                                        <button type="button" data-cmd="insertUnorderedList">&#8226;&#8801;</button>
                                        <button type="button" data-cmd="insertOrderedList">1&#8801;</button>
                                        <div class="rte-sep"></div>
                                        <button type="button" data-cmd="outdent">&#8676;</button>
                                        <button type="button" data-cmd="indent">&#8677;</button>
                                        <div class="rte-sep"></div>
                                        <button type="button" data-cmd="createLink">&#128279;</button>
                                    </div>
                                    <div class="rte-body"
                                         id="editor_deposit"
                                         contenteditable="true"
                                         data-placeholder="Enter deposit disclaimer...">{{ $dealer->deposit_disclaimer ?? 'By clicking the Securely Send Deposit button I authorize Angel Motors Inc to charge the amount listed above to the credit card provided herein, as a non-refundable down payment, with the intent to purchase. I agree to pay for this purchase in accordance with the issuing bank cardholder agreement. I understand that I waive all my rights to dispute this transaction with the issuing bank cardholder. Please DO NOT complete this credit card authorization form if you are not committed to purchasing the vehicle. Feel free to contact us with any questions prior to completing this form.*' }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- Pricing Disclaimer --}}
                        <div class="disc-row">
                            <div class="disc-label-col">
                                <div class="disc-label">Pricing disclaimer</div>
                                <div class="disc-hint">
                                    Disclaimer text to be displayed in tooltip next to price on VDP.
                                </div>
                            </div>
                            <div class="disc-editor-col">
                                <div class="rte-wrapper">
                                    <div class="rte-toolbar" data-target="pricing_disclaimer">
                                        <button type="button" data-cmd="bold"><b>B</b></button>
                                        <button type="button" data-cmd="italic"><i>I</i></button>
                                        <button type="button" data-cmd="underline"><u>U</u></button>
                                        <div class="rte-sep"></div>
                                        <button type="button" data-cmd="justifyLeft">&#8676;</button>
                                        <button type="button" data-cmd="justifyCenter">&#8596;</button>
                                        <button type="button" data-cmd="justifyRight">&#8677;</button>
                                        <div class="rte-sep"></div>
                                        <button type="button" data-cmd="insertUnorderedList">&#8226;&#8801;</button>
                                        <button type="button" data-cmd="insertOrderedList">1&#8801;</button>
                                        <div class="rte-sep"></div>
                                        <button type="button" data-cmd="outdent">&#8676;</button>
                                        <button type="button" data-cmd="indent">&#8677;</button>
                                        <div class="rte-sep"></div>
                                        <button type="button" data-cmd="createLink">&#128279;</button>
                                    </div>
                                    <div class="rte-body"
                                         id="editor_pricing"
                                         contenteditable="true"
                                         data-placeholder="Enter pricing disclaimer...">{{ $dealer->pricing_disclaimer ?? '' }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- Optional Disclaimer on Forms --}}
                        <div class="disc-row">
                            <div class="disc-label-col">
                                <div class="disc-label">Optional disclaimer on forms</div>
                                <div class="disc-hint">
                                    This will display an optional disclaimer at the bottom of the form, like consent to dealership to communicate via text message.
                                </div>
                            </div>
                            <div class="disc-editor-col">
                                <div class="rte-wrapper">
                                    <div class="rte-toolbar" data-target="optional_disclaimer">
                                        <button type="button" data-cmd="bold"><b>B</b></button>
                                        <button type="button" data-cmd="italic"><i>I</i></button>
                                        <button type="button" data-cmd="underline"><u>U</u></button>
                                        <div class="rte-sep"></div>
                                        <button type="button" data-cmd="justifyLeft">&#8676;</button>
                                        <button type="button" data-cmd="justifyCenter">&#8596;</button>
                                        <button type="button" data-cmd="justifyRight">&#8677;</button>
                                        <div class="rte-sep"></div>
                                        <button type="button" data-cmd="insertUnorderedList">&#8226;&#8801;</button>
                                        <button type="button" data-cmd="insertOrderedList">1&#8801;</button>
                                        <div class="rte-sep"></div>
                                        <button type="button" data-cmd="outdent">&#8676;</button>
                                        <button type="button" data-cmd="indent">&#8677;</button>
                                        <div class="rte-sep"></div>
                                        <button type="button" data-cmd="createLink">&#128279;</button>
                                    </div>
                                    <div class="rte-body"
                                         id="editor_optional"
                                         contenteditable="true"
                                         data-placeholder="Enter optional disclaimer...">{{ $dealer->optional_disclaimer ?? '' }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- Save --}}
                        <div class="disc-save-wrap">
                            <button type="button" class="disc-btn-save" id="btnSaveDisclaimers">
                                &#10003; Save
                            </button>
                        </div>

                    </div>{{-- end disc-section --}}

                    {{-- Social Section --}}
                    <div class="social-section">
                        <div class="social-section-title">Social</div>

                        {{-- Facebook --}}
                        <div class="social-row">
                            <div class="social-label-col">Facebook</div>
                            <div class="social-input-col">
                                <input type="url"
                                       name="social_facebook"
                                       class="form-control"
                                       placeholder="https://www.facebook.com/your-page"
                                       value="{{ old('social_facebook', $dealer->social_links['facebook'] ?? '') }}">
                            </div>
                        </div>

                        {{-- Instagram --}}
                        <div class="social-row">
                            <div class="social-label-col">Instagram</div>
                            <div class="social-input-col">
                                <input type="url"
                                       name="social_instagram"
                                       class="form-control"
                                       placeholder="https://www.instagram.com/your-profile"
                                       value="{{ old('social_instagram', $dealer->social_links['instagram'] ?? '') }}">
                            </div>
                        </div>

                        {{-- Pinterest --}}
                        <div class="social-row">
                            <div class="social-label-col">Pinterest</div>
                            <div class="social-input-col">
                                <input type="url"
                                       name="social_pinterest"
                                       class="form-control"
                                       placeholder="https://www.pinterest.com/your-account"
                                       value="{{ old('social_pinterest', $dealer->social_links['pinterest'] ?? '') }}">
                            </div>
                        </div>

                        {{-- Twitter --}}
                        <div class="social-row">
                            <div class="social-label-col">Twitter</div>
                            <div class="social-input-col">
                                <input type="url"
                                       name="social_twitter"
                                       class="form-control"
                                       placeholder="https://twitter.com/your-handle"
                                       value="{{ old('social_twitter', $dealer->social_links['twitter'] ?? '') }}">
                            </div>
                        </div>

                        {{-- TikTok --}}
                        <div class="social-row">
                            <div class="social-label-col">TikTok</div>
                            <div class="social-input-col">
                                <input type="url"
                                       name="social_tiktok"
                                       class="form-control"
                                       placeholder="https://www.tiktok.com/@your-account"
                                       value="{{ old('social_tiktok', $dealer->social_links['tiktok'] ?? '') }}">
                            </div>
                        </div>

                        {{-- YouTube --}}
                        <div class="social-row">
                            <div class="social-label-col">YouTube</div>
                            <div class="social-input-col">
                                <input type="url"
                                       name="social_youtube"
                                       class="form-control"
                                       placeholder="https://www.youtube.com/channel/..."
                                       value="{{ old('social_youtube', $dealer->social_links['youtube'] ?? '') }}">
                            </div>
                        </div>

                        {{-- LinkedIn --}}
                        <div class="social-row">
                            <div class="social-label-col">LinkedIn</div>
                            <div class="social-input-col">
                                <input type="url"
                                       name="social_linkedin"
                                       class="form-control"
                                       placeholder="https://www.linkedin.com/company/..."
                                       value="{{ old('social_linkedin', $dealer->social_links['linkedin'] ?? '') }}">
                            </div>
                        </div>

                        {{-- Save --}}
                        <div class="social-save-wrap">
                            <button type="button" class="social-btn-save" id="btnSaveSocial">
                                &#10003; Save
                            </button>
                        </div>

                    </div>{{-- end social-section --}}
                </div>{{-- end right content --}}
            </div>{{-- end d-flex --}}
        </div>{{-- end view-content --}}
    </main>
@endsection

@push('page-scripts')
<script>
    // Get CSRF token from meta tag (assuming it's present in layout)
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Helper to show toastr notifications
    function showToast(type, message) {
        if (type === 'success') {
            toastr.success(message);
        } else if (type === 'error') {
            toastr.error(message);
        }
    }

    // Save General Settings
    document.getElementById('btnSaveGeneral').addEventListener('click', function () {
        const data = {
            legal_name: document.querySelector('[name="legal_name"]').value,
            corporate_address: document.querySelector('[name="corporate_address"]').value,
            support_email: document.querySelector('[name="support_email"]').value,
            abandoned_form_minutes: document.querySelector('[name="abandoned_form_minutes"]').value,
        };

        fetch('{{ route("dealer.website.settings.general.update") }}', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('success', data.message);
            } else {
                showToast('error', 'An error occurred while saving.');
            }
        })
        .catch(error => {
            console.error(error);
            showToast('error', 'An error occurred. Please try again.');
        });
    });

    // Save Disclaimers
    document.getElementById('btnSaveDisclaimers').addEventListener('click', function () {
        const data = {
            finance_disclaimer: document.getElementById('editor_finance').innerHTML,
            inventory_disclaimer: document.getElementById('editor_inventory').innerHTML,
            deposit_disclaimer: document.getElementById('editor_deposit').innerHTML,
            pricing_disclaimer: document.getElementById('editor_pricing').innerHTML,
            optional_disclaimer: document.getElementById('editor_optional').innerHTML,
        };

        fetch('{{ route("dealer.website.settings.disclaimers.update") }}', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('success', data.message);
            } else {
                showToast('error', 'An error occurred while saving.');
            }
        })
        .catch(error => {
            console.error(error);
            showToast('error', 'An error occurred. Please try again.');
        });
    });

    // Save Social Links
    document.getElementById('btnSaveSocial').addEventListener('click', function () {
        const data = {
            facebook: document.querySelector('[name="social_facebook"]').value,
            instagram: document.querySelector('[name="social_instagram"]').value,
            pinterest: document.querySelector('[name="social_pinterest"]').value,
            twitter: document.querySelector('[name="social_twitter"]').value,
            tiktok: document.querySelector('[name="social_tiktok"]').value,
            youtube: document.querySelector('[name="social_youtube"]').value,
            linkedin: document.querySelector('[name="social_linkedin"]').value,
        };

        fetch('{{ route("dealer.website.settings.social.update") }}', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('success', data.message);
            } else {
                showToast('error', 'An error occurred while saving.');
            }
        })
        .catch(error => {
            console.error(error);
            showToast('error', 'An error occurred. Please try again.');
        });
    });

    // Rich text editor toolbar handling (as before)
    document.querySelectorAll('.rte-toolbar').forEach(toolbar => {
        toolbar.querySelectorAll('button[data-cmd]').forEach(btn => {
            btn.addEventListener('mousedown', function (e) {
                e.preventDefault();
                const cmd = this.dataset.cmd;
                const wrap = this.closest('.rte-wrapper');
                const editor = wrap.querySelector('.rte-body');
                editor.focus();

                if (cmd === 'createLink') {
                    const url = prompt('Enter URL:', 'https://');
                    if (url) document.execCommand('createLink', false, url);
                } else {
                    document.execCommand(cmd, false, null);
                }

                if (['bold','italic','underline'].includes(cmd)) {
                    this.classList.toggle('active', document.queryCommandState(cmd));
                }
            });
        });
    });

    function updateToolbarState() {
        document.querySelectorAll('.rte-toolbar').forEach(toolbar => {
            ['bold','italic','underline'].forEach(cmd => {
                const btn = toolbar.querySelector(`[data-cmd="${cmd}"]`);
                if (btn) btn.classList.toggle('active', document.queryCommandState(cmd));
            });
        });
    }

    document.querySelectorAll('.rte-body').forEach(editor => {
        editor.addEventListener('keyup', updateToolbarState);
        editor.addEventListener('mouseup', updateToolbarState);
        editor.addEventListener('focus', updateToolbarState);
    });
</script>
@endpush