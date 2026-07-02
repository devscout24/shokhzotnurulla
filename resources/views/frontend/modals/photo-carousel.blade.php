{{-- Photo Carousel (opens when clicking an image in the grid modal) --}}
<div class="modal fade" id="modalCarousel" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen modal-dialog-centered">
        <div class="modal-content bg-black border-0 rounded-0">
            <div class="d-flex justify-content-between align-items-center px-3 py-2 position-absolute top-0 start-0 end-0 z-3">
                <span class="text-white fs-5" id="carouselCounter">1 / 1</span>
                <button type="button" data-bs-dismiss="modal"
                    class="btn btn-link text-white text-decoration-none fs-1 lh-1 p-0 m-0 border-0">×</button>
            </div>

            <div class="modal-body d-flex align-items-center justify-content-center p-0 position-relative overflow-hidden">
                <button type="button" id="carouselPrev"
                    class="btn btn-dark bg-dark bg-opacity-50 border-0 rounded-0 position-absolute start-0 top-50 translate-middle-y ms-2 z-3 px-3 py-4 d-none">
                    <span class="fs-3 lh-1 d-block">&lsaquo;</span>
                </button>

                <img id="carouselImage" src="" alt="Photo" class="img-fluid"
                    style="max-height: 90vh; max-width: 100%; object-fit: contain;">

                <button type="button" id="carouselNext"
                    class="btn btn-dark bg-dark bg-opacity-50 border-0 rounded-0 position-absolute end-0 top-50 translate-middle-y me-2 z-3 px-3 py-4 d-none">
                    <span class="fs-3 lh-1 d-block">&rsaquo;</span>
                </button>
            </div>
        </div>
    </div>
</div>
