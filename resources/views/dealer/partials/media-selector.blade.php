<div class="faq-modal-overlay" id="mediaModalOverlay">
    <div class="faq-modal" style="width: 1000px; max-width: 95vw; height: 90vh; display: flex; flex-direction: column; overflow: hidden;">
        <div style="padding: 20px 25px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; flex-shrink: 0;">
            <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #333;">Select Media</h3>
            <button style="background:none; border:none; font-size:24px; cursor:pointer; color: #999;" id="mediaModalClose">&times;</button>
        </div>
        <div style="padding: 25px; border-bottom: 1px solid #f9f9f9; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;">
            <div style="font-size: 14px; font-weight: 600; color: #333;">Select image:</div>
            <div style="display: flex; gap: 12px; align-items: center;">
                <div style="width: 280px;"><input type="text" id="mediaSearchInput" class="bulk-input" placeholder="Search media..."></div>
                <button class="btn-save-red" style="height: 42px;"><i class="bi bi-plus-lg"></i> Add Media</button>
            </div>
        </div>
        <div class="media-display-filter" style="flex-shrink: 0;">
            <span class="display-label">Display</span>
            <div class="display-select-wrap">
                <select class="bulk-select display-select" id="mediaDisplaySelect">
                    <option value="Expanded">Expanded</option>
                    <option value="Comfortable" selected>Comfortable</option>
                    <option value="Compact">Compact</option>
                </select>
            </div>
        </div>
        <div class="media-grid expanded" id="mediaGrid" style="flex: 1; overflow-y: auto;"></div>
        <div class="pagination-wrap" style="flex-shrink: 0; border-top: 1px solid #eee; padding: 20px 25px; display: flex; align-items: center; justify-content: space-between; flex-direction: row;">
            <div id="mediaPaginationInfo" class="pag-info" style="margin: 0;"></div>
            <div id="mediaPagination" class="pagination-list"></div>
            <div class="media-modal-footer-btn-wrap" style="position: static;">
                <button class="btn-save-red" id="confirmMediaBtn" style="opacity: 0.5; pointer-events: none; background: #ea9b93; padding: 10px 25px;"><i class="bi bi-check-lg"></i> Select Media</button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Media Grid & Cards */
    .media-grid { display: grid; gap: 20px; padding: 25px; flex: 1; overflow-y: auto; background: #fff; align-content: start; }
    .media-grid.expanded { grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); }
    .media-grid.comfortable { grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 15px; }
    .media-grid.compact { grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 10px; }

    .media-card { border: 1px solid #eee; border-radius: 8px; overflow: hidden; cursor: pointer; transition: all .2s; background: #fff; display: flex; flex-direction: column; }
    .media-card:hover { border-color: #c0392b; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .media-card.selected { border-color: #c0392b; border-width: 2px; box-shadow: 0 0 0 3px rgba(192, 57, 43, 0.1); }
    .media-thumb { aspect-ratio: 4/3; width: 100%; object-fit: cover; border-bottom: 1px solid #f5f5f5; }
    
    .media-info { padding: 12px; font-size: 11px; color: #666; line-height: 1.5; font-family: 'Inter', -apple-system, sans-serif; }
    .media-info b { color: #333; font-weight: 700; }
    .media-info div { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    /* Mode-based visibility */
    .media-grid.comfortable .media-info-author, 
    .media-grid.comfortable .media-info-size, 
    .media-grid.comfortable .media-info-title { display: none; }
    .media-grid.comfortable .media-info { text-align: center; padding: 8px; }
    .media-grid.compact .media-info { display: none; }
    .media-grid.compact .media-thumb { border-bottom: none; }

    /* Display Filter */
    .media-display-filter { display: flex; align-items: center; justify-content: flex-end; gap: 10px; padding: 10px 25px; background: #fcfcfc; border-bottom: 1px solid #eee; font-family: 'Inter', -apple-system, sans-serif; }
    .display-label { font-size: 12px; color: #777; font-weight: 500; }
    .display-select-wrap { position: relative; width: 140px; }
    .display-select { height: 32px !important; font-size: 13px !important; border-radius: 4px !important; padding: 0 12px !important; font-family: 'Inter', -apple-system, sans-serif !important; border-color: #e0e0e0 !important; cursor: pointer; }

    /* Pagination */
    .pagination-wrap { padding: 25px; border-top: 1px solid #eee; position: relative; display: flex; flex-direction: column; align-items: center; gap: 15px; }
    .pagination-list { display: flex; align-items: center; gap: 5px; }
    .pag-btn { width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; border: 1px solid #e0e0e0; border-radius: 4px; background: #fff; color: #666; font-size: 13px; cursor: pointer; transition: all .2s; }
    .pag-btn:hover { background: #f8f9fa; border-color: #ccc; }
    .pag-btn.active { background: #c0392b; color: #fff; border-color: #c0392b; }
    .pag-btn.disabled { opacity: 0.5; pointer-events: none; }
    .pag-dots { color: #999; padding: 0 5px; font-size: 18px; line-height: 1; }
    .pag-info { font-size: 13px; color: #777; margin-top: 5px; }
    .media-modal-footer-btn-wrap { position: absolute; right: 25px; top: 25px; }
</style>
