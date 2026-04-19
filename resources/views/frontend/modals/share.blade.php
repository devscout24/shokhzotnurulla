{{-- Share --}}
@php
    $shareUrl = urlencode(url()->current());
    $shareText = urlencode('Check this out');
@endphp

<div class="modal fade" id="modalShare" tabindex="-1" aria-labelledby="modalSearchLabel" aria-hidden="true">
    <div class="modal-dialog  ">
        <div class="modal-content">
            <h3 class="border-bottom d-flex custom-row-spacing align-items-center bg-white w-100 h5 ps-4 pe-0 pt-3 mb-0 float-start border-theme border-thick d-inline-block pb-3 sticky-top"
                data-padding-right="" data-margin-right="">
                Share<button type="button" data-bs-dismiss="modal"
                    class="btn-xs ms-auto close text-large float-end d-inline-block me-0 py-0 btn btn-link"
                    fdprocessedid="2y3ar">×</button>
            </h3>

            <div class="modal-body">
                <!-- QR Code and Copy Link -->
                <div class="mb-4 d-flex align-items-center row">
                    <div class="col-sm-4 col-12">
                        <img
                            alt="Scan the QR code to share this vehicle"
                            class="w-100"
                            src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode(url()->current()) }}"
                        >
                    </div>
                    <div class="col-sm-8 col-12">
                        <h5><b>Scan to share or click to copy:</b></h5>
                        <div class="input-group">
                            <input readonly class="form-control" type="text"
                                value="{{ url()->current() }}">
                            <span class="input-group-text cursor-pointer" id="copyBtn">
                                <i class="fa-regular fa-clipboard"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Share Buttons as plain buttons -->
                <div class="bg-lighter px-4 pt-4 rounded border">
                    <div class="row g-2">
                        <div class="col-12">
                            <button type="button"
                                onclick="window.open('https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}','_blank')"
                                class="btn btn-default w-100 text-start d-flex align-items-center mb-4">
                                <i class="fa-brands fa-facebook me-2 opacity-75"></i>
                                Facebook
                            </button>
                        </div>

                        <div class="col-12">
                            <button type="button"
                                onclick="window.open('https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareText }}','_blank')"
                                class="btn btn-default w-100 text-start d-flex align-items-center mb-4">
                                <i class="fa-brands fa-twitter me-2 opacity-75"></i>
                                Twitter
                            </button>
                        </div>

                        <div class="d-block d-xl-none col-12">
                            <button type="button"
                                onclick="window.location.href='sms:?body={{ $shareUrl }}'"
                                class="btn btn-default w-100 text-start d-flex align-items-center mb-4">
                                <i class="fa-solid fa-comment me-2"></i>
                                Text Message
                            </button>
                        </div>

                        <div class="col-12">
                            <button type="button"
                                onclick="window.location.href='mailto:?subject=Check this&body={{ $shareUrl }}'"
                                class="btn btn-default w-100 text-start d-flex align-items-center mb-4">
                                <i class="fa-solid fa-envelope me-2"></i>
                                Email
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('page-scripts')
    <script id="copy-script">
        document.getElementById('copyBtn').addEventListener('click', function () {
            const input = this.closest('.input-group').querySelector('input');
            input.select();
            input.setSelectionRange(0, 99999);

            navigator.clipboard.writeText(input.value).then(() => {
                this.innerHTML = '<i class="fa-solid fa-check"></i>';
                setTimeout(() => {
                    this.innerHTML = '<i class="fa-regular fa-clipboard"></i>';
                }, 1500);
            });
        });
    </script>
@endpush