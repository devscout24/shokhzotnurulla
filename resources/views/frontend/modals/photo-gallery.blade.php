{{-- Photo Gallery Grid --}}
<div class="modal fade" id="modalGallery" tabindex="-1" aria-labelledby="modalSearchLabel" aria-hidden="true"
     data-photos='{{ $photos->map(fn($p) => $p->url)->toJson() }}'>
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <h3 class="photo-gallery-header border-bottom bg-white w-100 h5 ps-4 pe-0 py-2 mb-0 float-start border-theme border-thick d-inline-block sticky-top"
                data-padding-right="" data-margin-right="">
                Photo Gallery
                <button type="button" data-bs-dismiss="modal"
                    class="btn-xl mt-n2 close text-large py-0 float-end d-inline-block btn btn-link"
                    fdprocessedid="jah7us">×</button>
            </h3>

            <div class="modal-body">
                <div class="row g-2">
                    @forelse($photos as $idx => $photo)
                        <div class="col-12 col-sm-6 col-md-4 mb-2" data-gallery-idx="{{ $idx }}" style="cursor:pointer;">
                            <img src="{{ $photo->url }}" loading="lazy"
                                alt="{{ $vehicleTitle ?? 'Vehicle' }} (photo {{ $idx }})" class="img-fluid w-100">
                        </div>
                    @empty
                        <div class="col-12">
                            <p class="text-muted">No photos available.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
