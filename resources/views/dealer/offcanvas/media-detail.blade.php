{{-- ── OFFCANVAS: Media Details ── --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="mlOffcanvas" aria-labelledby="mlOffcanvasLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="mlOffcanvasLabel">
            <span class="d-inline-block pt-1">Media Details</span>
        </h5>
        <button type="button" class="btn-close" id="mlOffcanvasClose" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body border-top">

        {{-- Preview --}}
        <div class="p-3 bg-light border rounded mb-4">
            <div class="text-center" id="mlOcPreviewWrap">
                <img class="img-fluid" id="mlOcPreviewImg" src="" alt="">
            </div>
        </div>

        {{-- Meta info --}}
        <div id="mlOcMeta"></div>

        <hr class="mx-n3 my-4">

        {{-- File URL --}}
        <div class="mb-3">
            <label class="form-label">File URL</label>
            <div class="input-group">
                <input readonly class="form-control" type="text" id="mlOcUrl">
                <span class="input-group-text cursor-pointer" id="mlOcCopyBtn" title="Copy URL">
                    <i class="bi bi-clipboard"></i>
                </span>
            </div>
        </div>

        {{-- Title --}}
        <div class="mb-3">
            <label class="form-label">Title Attribute</label>
            <input type="text" class="form-control" id="mlOcTitle" name="title" placeholder="Media title">
        </div>

        {{-- Alt Text --}}
        <div class="mb-3">
            <label class="form-label">Alt Text</label>
            <input type="text" class="form-control" id="mlOcAltText" name="alt_text" placeholder="Enter a description">
        </div>

        <hr class="mx-n3 my-4">

        {{-- Actions --}}
        <div class="row">
            <div class="col">
                <button type="button" class="btn btn-block w-100" id="mlOcSaveBtn">
                    <i class="bi bi-check text-white me-1"></i> Save
                </button>
            </div>
            <div class="col">
                <button type="button" class="btn btn-outline-danger btn-block w-100" id="mlOcDeleteBtn">
                    <i class="bi bi-trash me-1"></i> Delete
                </button>
            </div>
        </div>

    </div>
</div>