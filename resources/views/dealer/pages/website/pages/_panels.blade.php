{{-- ALL Settings Panels from page.html --}}

{{-- Span Settings --}}
<div id="span-settings-panel" style="display:none">
<button class="hs-back-btn" id="span-back-btn"><i class="fa-solid fa-arrow-left"></i> Span Settings</button>
<div class="hs-row"><label>Text color</label>
<select class="hs-select" id="span-color"><option value="" selected>Default</option><option value="#ef4444">Red</option><option value="#1d4ed8">Blue</option><option value="#15803d">Green</option><option value="#111827">Dark</option><option value="#f97316">Orange</option></select></div>
<div class="hs-row"><label>Text size (px)</label><input class="hs-input" id="span-size" type="number" placeholder="16" min="8" max="72" style="width:120px"/></div>
<div class="hs-row"><label>Align</label><div class="hs-align-group"><button class="span-align-btn active" data-align="left"><i class="fa-solid fa-align-left"></i></button><button class="span-align-btn" data-align="center"><i class="fa-solid fa-align-center"></i></button><button class="span-align-btn" data-align="right"><i class="fa-solid fa-align-right"></i></button></div></div>
<div class="hs-row"><label>Font weight</label><select class="hs-select" id="span-weight"><option value="normal" selected>Normal</option><option value="bold">Bold</option><option value="300">Light</option><option value="700">Bold 700</option></select></div>
<div class="hs-row"><label>Space Top (px)</label><input class="hs-input" id="span-margin-top" type="number" placeholder="0" min="0" max="100" style="width:120px"/></div>
<div class="hs-row"><label>Space Bottom (px)</label><input class="hs-input" id="span-margin-bottom" type="number" placeholder="0" min="0" max="100" style="width:120px"/></div>
<hr class="hs-divider"/>
<div class="hs-actions"><button class="hs-btn-remove" id="span-remove-btn"><i class="fa-regular fa-trash-can"></i> Remove</button><button class="hs-btn-cancel" id="span-cancel-btn">Cancel</button></div>
</div>

{{-- IFrame Settings --}}
<div id="iframe-settings-panel" style="display:none">
<button class="hs-back-btn" id="iframe-back-btn"><i class="fa-solid fa-arrow-left"></i> IFrame Settings</button>
<div class="hs-row"><label>iFrame URL</label><input class="hs-input" id="iframe-url" placeholder="Enter URL"/></div>
<div class="hs-row"><label>Frame title</label><input class="hs-input" id="iframe-title" placeholder="Frame title"/></div>
<div class="hs-row"><label>Height</label><input class="hs-input" id="iframe-height" type="number" value="300" min="50" max="2000"/></div>
<hr class="hs-divider"/>
<div class="hs-actions"><button class="hs-btn-remove" id="iframe-remove-btn"><i class="fa-regular fa-trash-can"></i> Remove</button><button class="hs-btn-cancel" id="iframe-cancel-btn">Cancel</button></div>
</div>

{{-- 2-Column Settings --}}
<div id="2col-settings-panel" style="display:none">
<button class="hs-back-btn" id="col2-back-btn"><i class="fa-solid fa-arrow-left"></i> 2-Column Settings</button>
<div class="hs-row"><label>Gap (px)</label><input class="hs-input" id="col2-gap" type="number" value="20" min="0" max="100"/></div>
<hr class="hs-divider"/>
<div class="hs-actions"><button class="hs-btn-remove" id="col2-remove-btn"><i class="fa-regular fa-trash-can"></i> Remove</button><button class="hs-btn-cancel" id="col2-cancel-btn">Cancel</button></div>
</div>

{{-- Container Settings --}}
<div id="container-settings-panel" style="display:none">
<button class="hs-back-btn" id="container-back-btn"><i class="fa-solid fa-arrow-left"></i> Container Settings</button>
<div class="hs-row"><label>Padding Top (px)</label><input class="hs-input" id="container-padding-top" type="number" value="20" min="0" max="200"/></div>
<div class="hs-row"><label>Padding Bottom (px)</label><input class="hs-input" id="container-padding-bottom" type="number" value="20" min="0" max="200"/></div>
<div class="hs-row"><label>Background Color</label><input class="hs-input" id="container-bg" type="color" value="#ffffff" style="height:40px;padding:2px"/></div>
<hr class="hs-divider"/>
<label class="fw-bold small text-uppercase mb-2 d-block">Flexbox (Layout)</label>
<div class="hs-row"><label>Direction</label><select class="hs-select" id="container-flex-direction"><option value="column" selected>Column (Vertical)</option><option value="row">Row (Horizontal)</option></select></div>
<div class="hs-row"><label>Justify Content</label><select class="hs-select" id="container-justify-content"><option value="flex-start" selected>Start</option><option value="center">Center</option><option value="flex-end">End</option><option value="space-between">Space Between</option></select></div>
<div class="hs-row"><label>Align Items</label><select class="hs-select" id="container-align-items"><option value="stretch" selected>Stretch</option><option value="flex-start">Start</option><option value="center">Center</option><option value="flex-end">End</option></select></div>
<hr class="hs-divider"/>
<div class="hs-actions"><button class="hs-btn-remove" id="container-remove-btn"><i class="fa-regular fa-trash-can"></i> Remove</button><button class="hs-btn-cancel" id="container-cancel-btn">Cancel</button></div>
</div>

{{-- Icon Settings --}}
<div id="icon-settings-panel" style="display:none">
<button class="hs-back-btn" id="icon-back-btn"><i class="fa-solid fa-arrow-left"></i> Icon Settings</button>
<div class="hs-row"><label>Icon (FontAwesome class)</label><input class="hs-input" id="icon-class" placeholder="fa-solid fa-star"/></div>
<div class="hs-row"><label>Icon size (px)</label><input class="hs-input" id="icon-size" type="number" value="24" min="8" max="200"/></div>
<div class="hs-row"><label>Width (%)</label><input class="hs-input" id="icon-width" type="number" value="100" min="5" max="100"/></div>
<div class="hs-row"><label>Vertical Padding (px)</label><input class="hs-input" id="icon-padding" type="number" value="10" min="0" max="200"/></div>
<hr class="hs-divider"/>
<div class="hs-row"><label>Align</label><div class="hs-align-group"><button class="icon-align-btn active" data-align="left"><i class="fa-solid fa-align-left"></i></button><button class="icon-align-btn" data-align="center"><i class="fa-solid fa-align-center"></i></button><button class="icon-align-btn" data-align="right"><i class="fa-solid fa-align-right"></i></button></div></div>
<div class="hs-row"><label>Floating Mode</label><div class="form-check form-switch mb-0"><input class="form-check-input" type="checkbox" role="switch" id="icon-floating"/></div></div>
<div id="icon-float-controls" style="display:none">
<div class="hs-row" style="display:grid;grid-template-columns:1fr 1fr;gap:10px"><div><label class="small">Top (px)</label><input class="hs-input" id="icon-top" type="number" value="0"/></div><div><label class="small">Left (px)</label><input class="hs-input" id="icon-left" type="number" value="0"/></div></div>
</div>
<hr class="hs-divider"/>
<div class="hs-actions"><button class="hs-btn-remove" id="icon-remove-btn"><i class="fa-regular fa-trash-can"></i> Remove</button><button class="hs-btn-cancel" id="icon-cancel-btn">Cancel</button></div>
</div>

{{-- Cart Settings --}}
<div id="cart-settings-panel" style="display:none">
<button class="hs-back-btn" id="cart-back-btn"><i class="fa-solid fa-arrow-left"></i> Cart Settings</button>
<div class="hs-row"><label>Cart Text</label><input class="hs-input" id="cart-text" placeholder="Items (0)"/></div>
<div class="hs-row"><label>Link To</label><input class="hs-input" id="cart-link" placeholder="/cart"/></div>
<div class="hs-row"><label>Align</label><div class="hs-align-group"><button class="cart-align-btn active" data-align="left"><i class="fa-solid fa-align-left"></i></button><button class="cart-align-btn" data-align="center"><i class="fa-solid fa-align-center"></i></button><button class="cart-align-btn" data-align="right"><i class="fa-solid fa-align-right"></i></button></div></div>
<div class="hs-row"><label>Floating Mode</label><div class="form-check form-switch mb-0"><input class="form-check-input" type="checkbox" role="switch" id="cart-floating"/></div></div>
<hr class="hs-divider"/>
<div class="hs-actions"><button class="hs-btn-remove" id="cart-remove-btn"><i class="fa-regular fa-trash-can"></i> Remove</button><button class="hs-btn-cancel" id="cart-cancel-btn">Cancel</button></div>
</div>

{{-- Heading Settings --}}
<div id="heading-settings-panel" style="display:none">
<button class="hs-back-btn" id="hs-back-btn"><i class="fa-solid fa-arrow-left"></i> Heading Settings</button>
<div class="hs-row"><label>Header tag</label><select class="hs-select" id="hs-tag"><option value="h1" selected>H1</option><option value="h2">H2</option><option value="h3">H3</option><option value="h4">H4</option><option value="h5">H5</option><option value="h6">H6</option></select></div>
<div class="hs-row"><label>CSS Class</label><input class="hs-input" id="hs-classes" placeholder="text-medium"/></div>
<div class="hs-row"><label>Align</label><div class="hs-align-group"><button class="hs-align-btn active" data-align="left"><i class="fa-solid fa-align-left"></i></button><button class="hs-align-btn" data-align="center"><i class="fa-solid fa-align-center"></i></button><button class="hs-align-btn" data-align="right"><i class="fa-solid fa-align-right"></i></button></div></div>
<div class="hs-row"><label>Text color</label><select class="hs-select" id="hs-color"><option value="" selected>Default</option><option value="#ef4444">Red</option><option value="#1d4ed8">Blue</option><option value="#15803d">Green</option><option value="#111827">Dark</option><option value="#6b7280">Gray</option></select></div>
<div class="hs-row"><label>Text size (px)</label><input class="hs-input" id="hs-size" type="number" placeholder="32" min="8" max="120" style="width:120px"/></div>
<div class="hs-row"><label>Font weight</label><select class="hs-select" id="hs-weight"><option value="normal" selected>Normal</option><option value="bold">Bold</option><option value="100">100 (Thin)</option><option value="300">300 (Light)</option><option value="500">500 (Medium)</option><option value="700">700 (Bold)</option><option value="900">900 (Black)</option></select></div>
<div class="hs-row"><label>Space Top (px)</label><input class="hs-input" id="hs-margin-top" type="number" placeholder="10" min="0" max="100" style="width:120px"/></div>
<div class="hs-row"><label>Space Bottom (px)</label><input class="hs-input" id="hs-margin-bottom" type="number" placeholder="10" min="0" max="100" style="width:120px"/></div>
<hr class="hs-divider"/>
<div class="hs-actions"><button class="hs-btn-remove" id="hs-remove-btn"><i class="fa-regular fa-trash-can"></i> Remove</button><button class="hs-btn-cancel" id="hs-cancel-btn">Cancel</button></div>
</div>

{{-- Text Settings --}}
<div id="text-settings-panel" style="display:none">
<button class="hs-back-btn" id="ts-back-btn"><i class="fa-solid fa-arrow-left"></i> Text Settings</button>
<div class="hs-row"><label>Text color</label><select class="hs-select" id="ts-color"><option value="" selected>Default</option><option value="#ef4444">Red</option><option value="#1d4ed8">Blue</option><option value="#15803d">Green</option><option value="#111827">Dark</option><option value="#6b7280">Gray</option></select></div>
<div class="hs-row"><label>Align</label><div class="hs-align-group"><button class="ts-align-btn active" data-align="left"><i class="fa-solid fa-align-left"></i></button><button class="ts-align-btn" data-align="center"><i class="fa-solid fa-align-center"></i></button><button class="ts-align-btn" data-align="right"><i class="fa-solid fa-align-right"></i></button></div></div>
<div class="hs-row"><label>Text size (px)</label><input class="hs-input" id="ts-size" type="number" placeholder="16" min="8" max="72" style="width:120px"/></div>
<div class="hs-row"><label>CSS Classes</label><input class="hs-input" id="ts-classes" placeholder="text-muted"/></div>
<hr class="hs-divider"/>
<div class="hs-actions"><button class="hs-btn-remove" id="ts-remove-btn"><i class="fa-regular fa-trash-can"></i> Remove</button><button class="hs-btn-cancel" id="ts-cancel-btn">Cancel</button></div>
</div>

{{-- Button Settings --}}
<div id="button-settings-panel" style="display:none">
<button class="bs-back-btn" id="bs-back-btn"><i class="fa-solid fa-arrow-left"></i> Button Settings</button>
<div class="bs-row"><label>Button text</label><input class="bs-input" id="bs-text" placeholder="GO FOR LIVE"/></div>
<div class="bs-row"><label>Button size</label><div class="bs-toggle-group"><button class="bs-toggle-btn" data-size="small">Small</button><button class="bs-toggle-btn active" data-size="medium">Medium</button><button class="bs-toggle-btn" data-size="large">Large</button></div></div>
<div class="bs-row"><label>Button theme</label><select class="bs-select" id="bs-theme"><option value="red" selected>Red / Default</option><option value="orange">Orange</option><option value="blue">Blue</option><option value="green">Green</option><option value="dark">Dark</option></select></div>
<div class="bs-row"><label>Alignment</label><div class="bs-align-group"><button class="bs-align-btn" data-align="left"><i class="fa-solid fa-align-left"></i></button><button class="bs-align-btn active" data-align="center"><i class="fa-solid fa-align-center"></i></button><button class="bs-align-btn" data-align="right"><i class="fa-solid fa-align-right"></i></button></div></div>
<div class="bs-row"><label>Button style</label><div class="bs-toggle-group"><button class="bs-toggle-btn active" data-bstyle="solid">Solid</button><button class="bs-toggle-btn" data-bstyle="outline">Outline</button></div></div>
<div class="bs-row"><label>Custom icon</label><input class="bs-input" id="bs-icon" placeholder="fa-star"/></div>
<div class="bs-row"><label>Full-width</label><div class="form-check form-switch mb-0"><input class="form-check-input" type="checkbox" role="switch" id="bs-fullwidth"/><label class="form-check-label small" for="bs-fullwidth" id="bs-fullwidth-label">No</label></div></div>
<div class="bs-row" id="bs-link-row"><label>Link button to:</label><input class="bs-input" id="bs-link" placeholder="https://"/></div>
<div class="bs-row"><label>Open in new tab</label><div class="form-check form-switch mb-0"><input class="form-check-input" type="checkbox" role="switch" id="bs-newtab"/><label class="form-check-label small" for="bs-newtab" id="bs-newtab-label">No</label></div></div>
<hr class="bs-divider"/>
<div class="bs-actions"><button class="bs-btn-remove" id="bs-remove-btn"><i class="fa-regular fa-trash-can"></i> Remove</button><button class="bs-btn-cancel" id="bs-cancel-btn">Cancel</button></div>
</div>

{{-- Divider Settings --}}
<div id="divider-settings-panel" style="display:none">
<button class="hs-back-btn" id="ds-back-btn"><i class="fa-solid fa-arrow-left"></i> Divider Settings</button>
<div class="hs-row"><label>CSS Classes</label><input class="hs-input" id="ds-classes" placeholder="my-4"/></div>
<div class="hs-row"><label>Color</label><select class="hs-select" id="ds-color"><option value="" selected>Default</option><option value="#ef4444">Red</option><option value="#1d4ed8">Blue</option><option value="#15803d">Green</option><option value="#cbd5e1">Light Gray</option><option value="#6b7280">Gray</option></select></div>
<hr class="hs-divider"/>
<div class="hs-actions"><button class="hs-btn-remove" id="ds-remove-btn"><i class="fa-regular fa-trash-can"></i> Remove</button><button class="hs-btn-cancel" id="ds-cancel-btn">Cancel</button></div>
</div>

{{-- Image Settings --}}
<div id="image-settings-panel" style="display:none">
<button class="hs-back-btn" id="is-back-btn"><i class="fa-solid fa-arrow-left"></i> Image Settings</button>
<div class="hs-row"><label>Image URL</label><input class="hs-input" id="is-url" placeholder="https://example.com/image.jpg"/></div>
<div class="hs-row"><label>Alt Text</label><input class="hs-input" id="is-alt" placeholder="Image description"/></div>
<div class="hs-row"><label>Width (%)</label><input class="hs-input" id="is-width" type="number" value="100" min="5" max="100"/></div>
<div class="hs-row"><label>Align</label><div class="hs-align-group"><button class="is-align-btn active" data-align="left"><i class="fa-solid fa-align-left"></i></button><button class="is-align-btn" data-align="center"><i class="fa-solid fa-align-center"></i></button><button class="is-align-btn" data-align="right"><i class="fa-solid fa-align-right"></i></button></div></div>
<div class="hs-row"><label>CSS Classes</label><input class="hs-input" id="is-classes" placeholder="rounded shadow"/></div>
<hr class="hs-divider"/>
<div class="hs-actions"><button class="hs-btn-remove" id="is-remove-btn"><i class="fa-regular fa-trash-can"></i> Remove</button><button class="hs-btn-cancel" id="is-cancel-btn">Cancel</button></div>
</div>

{{-- Accordion Settings --}}
<div id="accordion-settings-panel" style="display:none">
<button class="hs-back-btn" id="as-back-btn"><i class="fa-solid fa-arrow-left"></i> Accordion Settings</button>
<div class="hs-row mt-3"><button class="btn btn-outline-danger btn-sm w-100" id="as-add-item"><i class="fa-solid fa-plus me-2"></i> Add New Item</button></div>
<div class="hs-row"><label>CSS Classes</label><input class="hs-input" id="as-classes" placeholder="mt-4"/></div>
<hr class="hs-divider"/>
<div class="hs-actions"><button class="hs-btn-remove" id="as-remove-btn"><i class="fa-regular fa-trash-can"></i> Remove Block</button><button class="hs-btn-cancel" id="as-cancel-btn">Cancel</button></div>
</div>

{{-- Card Settings --}}
<div id="card-settings-panel" style="display:none">
<button class="hs-back-btn" id="cs-back-btn"><i class="fa-solid fa-arrow-left"></i> Card Settings</button>
<div class="hs-row"><label>Card width (%)</label><input class="hs-input" id="cs-width" type="number" value="100" min="5" max="100"/></div>
<div class="hs-row"><label>Background color</label><select class="hs-select" id="cs-bg-color"><option value="transparent" selected>Transparent</option><option value="#ffffff">White</option><option value="#f8f9fa">Light Gray</option><option value="#343a40">Dark</option></select></div>
<hr class="hs-divider"/>
<label class="fw-bold small text-uppercase mb-2 d-block">Flex Controls</label>
<div class="hs-row"><label>Direction</label><select class="hs-select" id="cs-flex-direction"><option value="row" selected>Row (horizontal)</option><option value="row-reverse">Row Reverse</option><option value="column">Column (vertical)</option><option value="column-reverse">Column Reverse</option></select></div>
<div class="hs-row"><label>Justify content</label><select class="hs-select" id="cs-justify-content"><option value="flex-start" selected>Start</option><option value="center">Center</option><option value="flex-end">End</option><option value="space-between">Space Between</option><option value="space-around">Space Around</option><option value="space-evenly">Space Evenly</option></select></div>
<div class="hs-row"><label>Align items</label><select class="hs-select" id="cs-align-items"><option value="stretch" selected>Stretch</option><option value="flex-start">Start</option><option value="center">Center</option><option value="flex-end">End</option><option value="baseline">Baseline</option></select></div>
<div class="hs-row"><label>Flex wrap</label><select class="hs-select" id="cs-flex-wrap"><option value="nowrap" selected>No Wrap</option><option value="wrap">Wrap</option><option value="wrap-reverse">Wrap Reverse</option></select></div>
<div class="hs-row"><label>Gap (px)</label><input class="hs-input" id="cs-gap" type="number" value="0" min="0" max="200"/></div>
<hr class="hs-divider"/>
<div class="hs-actions"><button class="hs-btn-remove" id="cs-remove-btn"><i class="fa-regular fa-trash-can"></i> Remove</button><button class="hs-btn-cancel" id="cs-cancel-btn">Cancel</button></div>
</div>

{{-- Spacer Settings --}}
<div id="spacer-settings-panel" style="display:none">
<button class="hs-back-btn" id="ss-back-btn"><i class="fa-solid fa-arrow-left"></i> Spacer Settings</button>
<div class="hs-row"><label>Display on:</label><select class="hs-select" id="ss-display"><option value="all" selected>Desktop + Mobile</option><option value="desktop">Desktop only</option><option value="mobile">Mobile only</option></select></div>
<div class="hs-row"><label>Desktop height (pixels)</label><input class="hs-input" id="ss-height-desktop" type="number" value="10" min="0" max="500"/></div>
<div class="hs-row"><label>Mobile height (pixels)</label><input class="hs-input" id="ss-height-mobile" type="number" value="10" min="0" max="500"/></div>
<hr class="hs-divider"/>
<div class="hs-actions"><button class="hs-btn-remove" id="ss-remove-btn"><i class="fa-regular fa-trash-can"></i> Remove</button><button class="hs-btn-cancel" id="ss-cancel-btn">Cancel</button></div>
</div>

{{-- 3-Column Settings --}}
<div id="3col-settings-panel" style="display:none">
<button class="hs-back-btn" id="col3-back-btn"><i class="fa-solid fa-arrow-left"></i> 3-Column Settings</button>
<div class="hs-row"><label>Gap (px)</label><input class="hs-input" id="col3-gap" type="number" value="20" min="0" max="100"/></div>
<hr class="hs-divider"/>
<div class="hs-actions"><button class="hs-btn-remove" id="col3-remove-btn"><i class="fa-regular fa-trash-can"></i> Remove</button><button class="hs-btn-cancel" id="col3-cancel-btn">Cancel</button></div>
</div>

{{-- HTML Settings --}}
<div id="html-settings-panel" style="display:none">
<button class="hs-back-btn" id="html-back-btn"><i class="fa-solid fa-arrow-left"></i> HTML Settings</button>
<div class="hs-row"><label>Custom HTML</label><textarea class="hs-input" id="html-code" style="min-height:250px;font-family:monospace;font-size:13px" placeholder="<div>Hello World</div>"></textarea></div>
<hr class="hs-divider"/>
<div class="hs-actions"><button class="hs-btn-remove" id="html-remove-btn"><i class="fa-regular fa-trash-can"></i> Remove</button><button class="hs-btn-cancel" id="html-cancel-btn">Cancel</button></div>
</div>

{{-- CSS Settings --}}
<div id="css-settings-panel" style="display:none">
<button class="hs-back-btn" id="css-back-btn"><i class="fa-solid fa-arrow-left"></i> CSS Settings</button>
<div class="hs-row"><label>Custom CSS</label><textarea class="hs-input" id="css-code" style="min-height:250px;font-family:monospace;font-size:13px" placeholder=".my-class { color: red; }"></textarea></div>
<hr class="hs-divider"/>
<div class="hs-actions"><button class="hs-btn-remove" id="css-remove-btn"><i class="fa-regular fa-trash-can"></i> Remove</button><button class="hs-btn-cancel" id="css-cancel-btn">Cancel</button></div>
</div>
