const track = document.querySelector(".slider-track");
const handleMin = document.getElementById("handle-min");
const handleMax = document.getElementById("handle-max");

const minValue = 5000;
const maxValue = 32000;

let trackRect = track.getBoundingClientRect();

function updateHandlePosition(handle, value) {
    const percent = (value - minValue) / (maxValue - minValue);
    const x = percent * trackRect.width;
    handle.style.left = `${x}px`;
    handle.setAttribute("aria-valuenow", value);
}

// Initialize handles
updateHandlePosition(handleMin, minValue);
updateHandlePosition(handleMax, maxValue);

function handleDrag(handle, isMin) {
    function onMouseMove(e) {
        let x = e.clientX - trackRect.left;
        if (x < 0) x = 0;
        if (x > trackRect.width) x = trackRect.width;

        // Prevent overlap
        if (isMin) {
            const maxX = parseFloat(handleMax.style.left) || trackRect.width;
            if (x > maxX) x = maxX;
        } else {
            const minX = parseFloat(handleMin.style.left) || 0;
            if (x < minX) x = minX;
        }

        const value = Math.round((x / trackRect.width) * (maxValue - minValue) + minValue);
        handle.style.left = `${x}px`;
        handle.setAttribute("aria-valuenow", value);
    }

    function onMouseUp() {
        document.removeEventListener("mousemove", onMouseMove);
        document.removeEventListener("mouseup", onMouseUp);
    }

    document.addEventListener("mousemove", onMouseMove);
    document.addEventListener("mouseup", onMouseUp);
}

// Mouse events
handleMin.addEventListener("mousedown", () => handleDrag(handleMin, true));
handleMax.addEventListener("mousedown", () => handleDrag(handleMax, false));


document.querySelectorAll(".dropdown-toggle-btn").forEach(btn => {

    btn.addEventListener("click", function(){

        const parent = this.closest(".filter-dropdown");

        parent.classList.toggle("active");

    });

});

document.querySelectorAll(".make-checkbox").forEach(function(checkbox){

    checkbox.addEventListener("change", function(){

        const parent = this.closest(".make-item");
        const modelList = parent.querySelector(".model-list");

        if(this.checked){
            modelList.style.display = "block";
        }else{
            modelList.style.display = "none";
        }

    });

});



document.addEventListener("DOMContentLoaded", function () {

    const dropdownItems = document.querySelectorAll('.dropdown-item');
    const sortByText = document.querySelector('[data-cy="sortby-selected"]');

    if (!dropdownItems.length) return; // stop if no items found

    function createCheckmark() {
        const span = document.createElement('span');
        span.className = 'me-2 text-primary checkmark';
        span.style.display = 'none';
        span.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
            <path d="M20.285 6.709a1 1 0 0 0-1.414-1.418l-9.172 9.183-4.243-4.243a1 1 0 1 0-1.414 1.414l5 5a1 1 0 0 0 1.414 0l10-10z"/>
        </svg>`;
        return span;
    }

    dropdownItems.forEach((item, index) => {
        const check = createCheckmark();
        item.prepend(check);

        // ✅ Show first item selected by default
        if (index === 0) {
            check.style.display = 'inline-block';
            sortByText.textContent = 'Sort by ' + item.textContent.trim();
        }

        item.addEventListener('click', () => {
            dropdownItems.forEach(i => {
                const mark = i.querySelector('.checkmark');
                if (mark) mark.style.display = 'none';
            });

            check.style.display = 'inline-block';
            sortByText.textContent = 'Sort by ' + item.textContent.trim();
        });
    });

});

  const button = document.getElementById('sortby');
  const iconSpan = button.querySelector('span');

  button.addEventListener('click', () => {
    // Toggle the "show" class on the button
    button.classList.toggle('show');

    // Toggle the text color from primary to white
    if (iconSpan.classList.contains('text-primary')) {
      iconSpan.classList.remove('text-primary');
      iconSpan.classList.add('text-white');
    } else {
      iconSpan.classList.remove('text-white');
      iconSpan.classList.add('text-primary');
    }
  });

  

  document.addEventListener("DOMContentLoaded", () => {
    const sliderTrack = document.querySelector(".price-financing-slider");
    const handleMin = document.getElementById("handle-min");
    const handleMax = document.getElementById("handle-max");

    const minHidden = document.getElementById("minprice");
    const maxHidden = document.getElementById("maxprice");
    const minInput = document.querySelector('[name="price-display-min"]');
    const maxInput = document.querySelector('[name="price-display-max"]');

    const cards = document.querySelectorAll(".srp-cardcontainer");

    const minVal = parseInt(handleMin.getAttribute("aria-valuemin"), 10);
    const maxVal = parseInt(handleMin.getAttribute("aria-valuemax"), 10);

    function parsePrice(str) {
        return parseInt(str.replace(/[$,]/g, ''), 10);
    }

    function formatPrice(num) {
        return '$' + num.toLocaleString();
    }

    function updateGradient() {
        const minNow = parseInt(handleMin.getAttribute("aria-valuenow"), 10);
        const maxNow = parseInt(handleMax.getAttribute("aria-valuenow"), 10);

        const minPercent = ((minNow - minVal) / (maxVal - minVal)) * 100;
        const maxPercent = ((maxNow - minVal) / (maxVal - minVal)) * 100;

        sliderTrack.style.background = `linear-gradient(to right, 
            #ccc 0%, #ccc ${minPercent}%, 
            #166B87 ${minPercent}%, #166B87 ${maxPercent}%, 
            #ccc ${maxPercent}%, #ccc 100%)`;
    }

    function filterCards() {
        const minPrice = parseInt(minHidden.value, 10);
        const maxPrice = parseInt(maxHidden.value, 10);

        cards.forEach(card => {
            const priceEl = card.querySelector(".label-price");
            if (!priceEl) return;

            const price = parsePrice(priceEl.textContent);
            card.style.display = (price >= minPrice && price <= maxPrice) ? "block" : "none";
        });
    }

    function updateValues() {
        const minNow = parseInt(handleMin.getAttribute("aria-valuenow"), 10);
        const maxNow = parseInt(handleMax.getAttribute("aria-valuenow"), 10);

        minHidden.value = minNow;
        maxHidden.value = maxNow;

        minInput.value = formatPrice(minNow);
        maxInput.value = formatPrice(maxNow);

        updateGradient();
        filterCards();
    }

    // Listen to slider changes
    [handleMin, handleMax].forEach(handle => {
        handle.addEventListener("mousemove", updateValues);
        handle.addEventListener("touchmove", updateValues);
        handle.addEventListener("mouseup", updateValues);
        handle.addEventListener("touchend", updateValues);
    });

    // Listen to manual input changes
    minInput.addEventListener("change", () => {
        const val = parsePrice(minInput.value);
        minHidden.value = val;
        handleMin.setAttribute("aria-valuenow", val);
        updateValues();
    });

    maxInput.addEventListener("change", () => {
        const val = parsePrice(maxInput.value);
        maxHidden.value = val;
        handleMax.setAttribute("aria-valuenow", val);
        updateValues();
    });

    // Initial setup
    updateValues();
});

document.addEventListener("DOMContentLoaded", () => {
    const choiceSection = document.getElementById("choiceSection");
    const mySelfForm = document.getElementById("my-self");
    const coBorrowerForm = document.getElementById("co-borrower");

    // Choice buttons
    const btnMyself = document.getElementById("btn-myself");
    const btnCoBorrower = document.getElementById("btn-coborrower");

    // Back buttons
    const btnBackMyself = document.getElementById("btn-back-myself");
    const btnBackCoBorrower = document.getElementById("btn-back-coborrower");

    // Show "Myself" form
    btnMyself.addEventListener("click", () => {
        choiceSection.style.display = "none";
        mySelfForm.style.display = "block";
    });

    // Show "Co-borrower" form
    btnCoBorrower.addEventListener("click", () => {
        choiceSection.style.display = "none";
        coBorrowerForm.style.display = "block";
    });

    // Back to choice from "Myself"
    btnBackMyself.addEventListener("click", () => {
        mySelfForm.style.display = "none";
        choiceSection.style.display = "block";
    });

    // Back to choice from "Co-borrower"
    btnBackCoBorrower.addEventListener("click", () => {
        coBorrowerForm.style.display = "none";
        choiceSection.style.display = "block";
    });
});