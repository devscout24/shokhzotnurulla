document.querySelectorAll('.collapse-header').forEach(header => {
    header.addEventListener('click', () => {
        // Find the next sibling that has class collapse-content
        let content = header.closest('.card-footer').nextElementSibling;
        while (content && !content.classList.contains('collapse-content')) {
            content = content.nextElementSibling;
        }
        if (!content) return;

        content.classList.toggle('open');
        header.classList.toggle('active');
    });
});

// Select all accordion headers
const accordionHeaders = document.querySelectorAll('.accordion-header');

accordionHeaders.forEach(header => {
    header.addEventListener('click', () => {
        const content = header.nextElementSibling;
        const icon = header.querySelector('.collapse-icon');

        // Toggle active classes
        content.classList.toggle('active');
        icon.classList.toggle('active');
    });
});


const toggle = document.getElementById('toggleLocation');
const locationBox = document.getElementById('locationbox');
const toggleIcon = document.getElementById('toggleIcon').querySelector('i');

toggle.addEventListener('click', () => {
    // Toggle visibility
    if (locationBox.style.display === "none" || locationBox.style.display === "") {
        locationBox.style.display = "block";
        toggleIcon.classList.remove('fa-square-plus');
        toggleIcon.classList.add('fa-square-minus');
    } else {
        locationBox.style.display = "none";
        toggleIcon.classList.remove('fa-square-minus');
        toggleIcon.classList.add('fa-square-plus');
    }
});

document.addEventListener("DOMContentLoaded", () => {
    const continueBtn = document.getElementById("continueBtn");
    const formWrapper = document.getElementById("formWrapper");
    const confirmationSection = document.getElementById("confirmationSection");

    // Show confirmation when Continue is clicked
    continueBtn.addEventListener("click", () => {
        // Here you can add your validation if needed
        formWrapper.classList.add("hidden");
        confirmationSection.classList.remove("hidden");
    });

    // Handle "Get approved" button click
    const getApprovedBtn = confirmationSection.querySelector('[data-cy="btn-confirmation"]');

    getApprovedBtn.addEventListener("click", () => {
        // Hide current offcanvas
        const askQuestionOffcanvas = document.getElementById("askQuestion");
        const offcanvasInstance = bootstrap.Offcanvas.getInstance(askQuestionOffcanvas);
        offcanvasInstance.hide();

        // Show new offcanvas
        const getApprovedOffcanvas = document.getElementById("getApprovved");
        const newOffcanvas = new bootstrap.Offcanvas(getApprovedOffcanvas);
        newOffcanvas.show();
    });
});


document.addEventListener("DOMContentLoaded", () => {
    const continueBtn = document.getElementById("continueBtn2");
    const formWrapper = document.getElementById("formWrapper2");
    const confirmationSection = document.getElementById("confirmationManager");

    // Show confirmation when Continue is clicked
    continueBtn.addEventListener("click", () => {
        // Here you can add your validation if needed
        formWrapper.classList.add("hidden");
        confirmationSection.classList.remove("hidden");
    });

    // Handle "Get approved" button click
    const getApprovedBtn = confirmationSection.querySelector('[data-cy="btn-confirmation"]');

    getApprovedBtn.addEventListener("click", () => {
        // Hide current offcanvas
        const askQuestionOffcanvas = document.getElementById("askQuestion");
        const offcanvasInstance = bootstrap.Offcanvas.getInstance(askQuestionOffcanvas);
        offcanvasInstance.hide();

        // Show new offcanvas
        const getApprovedOffcanvas = document.getElementById("getApprovved");
        const newOffcanvas = new bootstrap.Offcanvas(getApprovedOffcanvas);
        newOffcanvas.show();
    });
});



// Get approved


document.addEventListener("DOMContentLoaded", () => {

    const choiceSection = document.getElementById("choiceSection");
    const mySelfForm = document.getElementById("my-self");
    const coBorrowerForm = document.getElementById("co-borrower");

    const step1Div = document.getElementById("step_1");
    const step2Div = document.getElementById("step_2");
    const step3Div = document.getElementById("step_3");
    const step4Div = document.getElementById("step_4");

    const stepsContent = [step1Div, step2Div, step3Div, step4Div];

    const stepHeader1 = document.getElementById("wizard-step-1");
    const stepHeader2 = document.getElementById("wizard-step-2");
    const stepHeader3 = document.getElementById("wizard-step-3");
    const stepHeader4 = document.getElementById("wizard-step-4");

    const stepHeaders = [
        stepHeader1,
        stepHeader2,
        stepHeader3,
        stepHeader4
    ];

    const indicators = stepHeaders.map(step => step.querySelector(".wizardstep-indicator"));

    const btnMyself = document.getElementById("btn-myself");
    const btnCoBorrower = document.getElementById("btn-coborrower-main");

    const btnBackMyself = document.getElementById("btn-back-myself");
    const btnBackCoBorrower = document.getElementById("btn-back-coborrower");

    const btnSelfContinue = document.getElementById("btn-selfContinue");
    const btnBorrowContinue = document.getElementById("btn-borrowContin");

    const step2Continue = document.getElementById("step2Continue");
    const step3Continue = document.getElementById("step3Continue");


    // ACTIVE STEP TITLE
    const setActiveStep = (stepNumber) => {

        stepHeaders.forEach((step, index) => {

            const title = step.querySelector("b");

            if ((index + 1) === stepNumber) {

                title.classList.remove("text-muted");
                title.classList.add("text-primary");
                step.classList.add("wizardstep-active");

            } else {

                title.classList.remove("text-primary");
                title.classList.add("text-muted");
                step.classList.remove("wizardstep-active");

            }

        });

    };


    // SHOW STEP CONTENT
    const showStep = (stepNumber) => {

        stepsContent.forEach((div, index) => {

            if (div) {
                div.style.display = (index + 1 === stepNumber) ? "block" : "none";
            }

        });

        setActiveStep(stepNumber);

    };


    // COMPLETE STEP
    const completeStep = (currentStep) => {

        const indicator = indicators[currentStep - 1];

        if (indicator) {

            indicator.textContent = "Edit";
            indicator.style.cursor = "pointer";

            indicator.onclick = () => {

                // restore correct content
                if (currentStep === 1) {

                    choiceSection.style.display = "block";
                    mySelfForm.style.display = "none";
                    coBorrowerForm.style.display = "none";

                }

                showStep(currentStep);

            };

        }

        showStep(currentStep + 1);

    };


    // CHOICE BUTTONS
    btnMyself.addEventListener("click", () => {

        choiceSection.style.display = "none";
        mySelfForm.style.display = "block";

    });

    btnCoBorrower.addEventListener("click", () => {

        choiceSection.style.display = "none";
        coBorrowerForm.style.display = "block";

    });


    // BACK BUTTONS
    btnBackMyself.addEventListener("click", () => {

        mySelfForm.style.display = "none";
        choiceSection.style.display = "block";
        setActiveStep(1);

    });

    btnBackCoBorrower.addEventListener("click", () => {

        coBorrowerForm.style.display = "none";
        choiceSection.style.display = "block";
        setActiveStep(1);

    });


    // CONTINUE BUTTONS
    btnSelfContinue.addEventListener("click", () => {

        mySelfForm.style.display = "none";
        coBorrowerForm.style.display = "none";
        completeStep(1);

    });

    btnBorrowContinue.addEventListener("click", () => {

        mySelfForm.style.display = "none";
        coBorrowerForm.style.display = "none";
        completeStep(1);

    });


    if (step2Continue)
        step2Continue.addEventListener("click", () => completeStep(2));

    if (step3Continue)
        step3Continue.addEventListener("click", () => completeStep(3));


    // INITIAL STATE
    step2Div.style.display = "none";
    step3Div.style.display = "none";
    step4Div.style.display = "none";

    setActiveStep(1);

});





const stars = document.querySelectorAll("#star-rating .star");
const optionDiv = document.getElementById("optionDiv");
const optionDiv2 = document.getElementById("optionDiv2");

stars.forEach((star, idx) => {
    star.addEventListener("click", () => {
        // Fill stars up to clicked index
        stars.forEach((s, i) => {
            s.classList.toggle("filled", i <= idx);
        });

        // Show the option div
        optionDiv.style.display = "block";
        optionDiv2.style.display = "block";
    });
});





// ----- ELEMENTS -----
const daysContainer = document.getElementById("daysContainer");
const prevWeekBtn = document.getElementById("prevWeekBtn");
const nextWeekBtn = document.getElementById("nextWeekBtn");

const step1Div = document.getElementById("step-1");
const step2Div = document.getElementById("step-2");
const successDiv = document.getElementById("successDiv"); // create this in your HTML

const dateContainer = document.getElementById("select-date");
const timeContainer = document.getElementById("select-time");
const selectDifferentDayBtn = timeContainer.querySelector("button");

const continueBtnStep1 = document.getElementById("continueBtn3");
const timeSelect = timeContainer.querySelector("select");

const continueBtnStep2 = document.getElementById("continueBtn4");

const stepHeaders = document.querySelectorAll(".wizardstep");

// ----- INITIAL VISIBILITY -----
step1Div.classList.add("d-block");
step1Div.classList.remove("d-none");

step2Div.classList.add("d-none");
step2Div.classList.remove("d-block");

successDiv.classList.add("d-none");
successDiv.classList.remove("d-block");

dateContainer.classList.add("d-block");
dateContainer.classList.remove("d-none");

timeContainer.classList.add("d-none");
timeContainer.classList.remove("d-block");

// ----- WEEK DATES -----
let currentStartDate = new Date();
currentStartDate.setHours(0, 0, 0, 0);

function getWeekDates(startDate) {
    const dates = [];
    let date = new Date(startDate);
    for (let i = 0; i < 6; i++) {
        dates.push(new Date(date));
        date.setDate(date.getDate() + 1);
    }
    return dates;
}

// ----- RENDER DAYS -----
function renderDays() {
    daysContainer.innerHTML = "";
    const dates = getWeekDates(currentStartDate);

    dates.forEach(date => {
        const dayDiv = document.createElement("div");
        dayDiv.className = "p-3 hover-light font-weight-bold border-bottom border-theme border-thick text-center  allowed col-sm-2 col-4";
        dayDiv.innerHTML = `
            <div class="day-name">${date.toLocaleDateString('en-US', { weekday: 'long' })}</div>
            <div class="h1 my-2 day-number">${date.getDate()}</div>
            <div class="month">${date.toLocaleDateString('en-US', { month: 'long' })}</div>
        `;

        dayDiv.addEventListener("click", () => {
            document.querySelectorAll(".day-card").forEach(d => {
                d.classList.remove("selected");
                d.querySelector(".day-number").classList.remove("selected");
                const oldSvg = d.querySelector(".check-icon");
                if (oldSvg) oldSvg.remove();
                d.querySelector(".month").style.display = "block";
            });

            dayDiv.classList.add("selected");
            dayDiv.querySelector(".day-number").classList.add("selected");
            dayDiv.querySelector(".month").style.display = "none";

            const svgSpan = document.createElement("span");
            svgSpan.className = "d-inline-block check-icon text-primary";
            svgSpan.innerHTML = `
                 <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#166B87" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                     <circle cx="12" cy="12" r="10"/>
                     <path d="M9 12l2 2 4-4"/>
                 </svg>`;
            dayDiv.insertBefore(svgSpan, dayDiv.querySelector(".month"));

            // Show time selection
            dateContainer.classList.remove("d-block");
            dateContainer.classList.add("d-none");
            timeContainer.classList.remove("d-none");
            timeContainer.classList.add("d-block");
        });

        daysContainer.appendChild(dayDiv);
    });

    const today = new Date();
    today.setHours(0, 0, 0, 0);
    prevWeekBtn.disabled = currentStartDate <= today;
}

// ----- WEEK NAVIGATION -----
nextWeekBtn.addEventListener("click", () => {
    currentStartDate.setDate(currentStartDate.getDate() + 6);
    renderDays();
});

prevWeekBtn.addEventListener("click", () => {
    currentStartDate.setDate(currentStartDate.getDate() - 6);
    renderDays();
});

// ----- BACK BUTTON STEP 1 -----
selectDifferentDayBtn.addEventListener("click", () => {
    timeContainer.classList.remove("d-block");
    timeContainer.classList.add("d-none");

    dateContainer.classList.remove("d-none");
    dateContainer.classList.add("d-block");
});

// ----- CONTINUE STEP 1 -----
continueBtnStep1.addEventListener("click", () => {
    if (!timeSelect.value) {
        alert("Please select a time");
        return;
    }

    step1Div.classList.add("d-none");
    step1Div.classList.remove("d-block");

    step2Div.classList.remove("d-none");
    step2Div.classList.add("d-block");

   // Update wizard header styles
stepHeaders[0].querySelector("b").classList.remove("text-primary");
stepHeaders[0].querySelector("b").classList.add("text-muted");

stepHeaders[1].classList.add("wizardstep-active");
stepHeaders[1].querySelector("b").classList.remove("text-muted");
stepHeaders[1].querySelector("b").classList.add("text-primary");

// Replace 1/2 with Edit button
const indicator = stepHeaders[0].querySelector(".wizardstep-indicator");

indicator.innerHTML = `<button class="btn text-muted btn-sm btn-link edit-step">Edit</button>`;
});

// ----- EDIT STEP -----
document.addEventListener("click", function(e){

    if(e.target.classList.contains("edit-step")){

        // show step 1 again
        step2Div.classList.add("d-none");
        step1Div.classList.remove("d-none");
        step1Div.classList.add("d-block");

        // restore wizard header styles
        stepHeaders[0].querySelector("b").classList.remove("text-muted");
        stepHeaders[0].querySelector("b").classList.add("text-primary");

        stepHeaders[1].querySelector("b").classList.remove("text-primary");
        stepHeaders[1].querySelector("b").classList.add("text-muted");

    }

});

// ----- CONTINUE STEP 2 -----
continueBtnStep2.addEventListener("click", () => {
    // Hide both steps
    step1Div.classList.add("d-none");
    step2Div.classList.add("d-none");

    // Show success div
    successDiv.classList.remove("d-none");
    successDiv.classList.add("d-block");

    // Keep wizard numbers (1 / 2) as-is but deactivate styling
    stepHeaders.forEach(header => {
        header.querySelector("b").classList.remove("text-primary");
        header.querySelector("b").classList.add("text-muted");
    });
});

// ----- INITIAL RENDER -----
renderDays();




// --------------------
// ELEMENTS
// --------------------
const yearSelect = document.querySelector("select[name='year']");
const makeSelect = document.querySelector("select[name='make']");
const modelSelect = document.querySelector("select[name='model']");
const trimSelect = document.querySelector("select[name='trim']");
const trimDiv = trimSelect.closest(".mb-4");

const continueBtn = document.getElementById("continuee");
const continueBtn2 = document.getElementById("continuee2");
const photoConti = document.getElementById("photoConti");
const skipPhoto = document.getElementById("skipPhoto");
const continueBtn4 = document.getElementById("continueBtn5");

const startOverBtn = document.getElementById("start-over");

const info1 = document.getElementById("info-1");
const info2 = document.getElementById("info-2");

const step1Content = document.getElementById("stepp1");
const step2Content = document.getElementById("stepp2");
const step3Content = document.getElementById("stepp3");
const step4Content = document.getElementById("stepp4");
const vinInput = document.querySelector("input[name='vin']");
vinInput.addEventListener("input", checkAllFilled);

// --------------------
// STEP HEADERS
// --------------------
const wizardStep1 = document.getElementById("wizardstep_1");
const wizardStep2 = document.getElementById("wizardstep_2");
const wizardStep3 = document.getElementById("wizardstep_3");
const wizardStep4 = document.getElementById("wizardstep_4");

const steps = [wizardStep1, wizardStep2, wizardStep3, wizardStep4];
const stepContents = [step1Content, step2Content, step3Content, step4Content];

// --------------------
// LOAN / LEASE
// --------------------
const loanLeaseDiv = document.getElementById("loan-lease");
const loanLeaseButtons = document.querySelectorAll("#info-2 [role='group'] .btn");

// --------------------
// INITIAL STATE
// --------------------
modelSelect.disabled = true;
trimSelect.disabled = true;
trimDiv.style.display = "none";

info2.style.display = "none";
step2Content.style.display = "none";
step3Content.style.display = "none";
step4Content.style.display = "none";

continueBtn.style.display = "none";
continueBtn.disabled = true;

startOverBtn.style.display = "none";

loanLeaseDiv.style.display = "none";

// --------------------
// DATA
// --------------------
const modelsByMake = {
    Ford: ["F-150", "Mustang", "Explorer", "Escape"],
    Toyota: ["Camry", "Corolla", "RAV4"],
    Honda: ["Civic", "Accord", "CR-V"],
    BMW: ["3 Series", "5 Series"]
};

const trimsByModel = {
    "F-150": ["XL", "XLT", "Lariat"],
    Mustang: ["EcoBoost", "GT"],
    Camry: ["LE", "SE", "XSE"],
    Civic: ["LX", "EX"],
    "3 Series": ["330i", "M340i"]
};

// --------------------
// POPULATE MODELS
// --------------------
function populateModels(make) {
    modelSelect.innerHTML = '<option value="">[Select Model]</option>';
    trimSelect.innerHTML = '<option value="">[Select Trim]</option>';

    trimDiv.style.display = "none";
    trimSelect.disabled = true;

    if (modelsByMake[make]) {
        modelsByMake[make].forEach(model => {
            const option = document.createElement("option");
            option.value = model;
            option.textContent = model;
            modelSelect.appendChild(option);
        });
        modelSelect.disabled = false;
    }
}

// --------------------
// POPULATE TRIMS
// --------------------
function populateTrims(model) {
    trimSelect.innerHTML = '<option value="">[Select Trim]</option>';

    if (trimsByModel[model]) {
        trimsByModel[model].forEach(trim => {
            const option = document.createElement("option");
            option.value = trim;
            option.textContent = trim;
            trimSelect.appendChild(option);
        });
        trimDiv.style.display = "block";
        trimSelect.disabled = false;
    }
}

function checkAllFilled() {
    const activeTabBtn = document.querySelector(".btn[data-bs-toggle='tab'].active");
    const activeTabId = activeTabBtn.getAttribute("data-bs-target").replace("#", "");

    let allFilled = false;

    // --------------------
    // YMM TAB
    // --------------------
    if (activeTabId === "ymm") {
        allFilled = yearSelect.value && makeSelect.value && modelSelect.value && trimSelect.value;
    }

    // --------------------
    // VIN TAB
    // --------------------
    if (activeTabId === "vin") {
        allFilled = vinInput.value.trim().length === 11;
    }

    // --------------------
    // BUTTON STATE
    // --------------------
    continueBtn.disabled = !allFilled;
    continueBtn.style.display = allFilled ? "flex" : "none";

    // --------------------
    // START OVER
    // --------------------
    if (yearSelect.value || makeSelect.value || modelSelect.value || trimSelect.value || vinInput.value) {
        startOverBtn.style.display = "inline-block";
    } else {
        startOverBtn.style.display = "none";
    }

    // --------------------
    // STEP SWITCH
    // --------------------
if (activeTabId === "ymm") {  // only toggle info1/info2 for the first tab
    info1.style.display = allFilled ? "none" : "block";
    info2.style.display = allFilled ? "block" : "none";
}
}

// --------------------
// ACTIVATE STEP
// --------------------
function activateStep(stepIndex) {
    stepContents.forEach((content, i) => {
        if (content) content.style.display = (i === stepIndex) ? "block" : "none";
    });

    steps.forEach((step, i) => {
        const title = step.querySelector("b");
        if (i === stepIndex) {
            step.classList.add("wizardstep-active");
            title.classList.remove("text-muted");
            title.classList.add("text-primary");
        } else {
            step.classList.remove("wizardstep-active");
            title.classList.remove("text-primary");
            title.classList.add("text-muted");
        }
    });
}

// --------------------
// ADD EDIT BUTTON
// --------------------
function addEditButton(stepIndex) {
    const indicator = steps[stepIndex].querySelector(".wizardstep-indicator");
    if (!indicator.querySelector(".edit-step")) {
        indicator.innerHTML =
            `<button class="btn btn-sm btn-link text-muted edit-step" data-step="${stepIndex}">Edit</button>`;
    }
}

// --------------------
// EVENT LISTENERS
// --------------------
yearSelect.addEventListener("change", checkAllFilled);

makeSelect.addEventListener("change", () => {
    populateModels(makeSelect.value);
    checkAllFilled();
});

modelSelect.addEventListener("change", () => {
    populateTrims(modelSelect.value);
    checkAllFilled();
});

trimSelect.addEventListener("change", checkAllFilled);

// --------------------
// CONTINUE BUTTONS
// --------------------

// Step 1 -> Step 2
continueBtn.addEventListener("click", () => {
    addEditButton(0);
    activateStep(1);
});

// Step 2 -> Step 3
continueBtn2.addEventListener("click", () => {
    addEditButton(1);
    activateStep(2);
});

// Step 3 -> Step 4
[photoConti, skipPhoto].forEach(button => {
    button.addEventListener("click", () => {
        addEditButton(2);
        activateStep(3);
    });
});

// --------------------
// EDIT CLICK
// --------------------
document.addEventListener("click", function (e) {
    if (e.target.classList.contains("edit-step")) {
        const step = parseInt(e.target.dataset.step);
        activateStep(step);
    }
});

// --------------------
// START OVER
// --------------------
startOverBtn.addEventListener("click", () => {
    yearSelect.value = "";
    makeSelect.value = "";
    modelSelect.value = "";
    trimSelect.value = "";

    modelSelect.disabled = true;
    trimSelect.disabled = true;
    trimDiv.style.display = "none";

    info1.style.display = "block";
    info2.style.display = "none";

    continueBtn.style.display = "none";
    startOverBtn.style.display = "none";

    activateStep(0);
});

// --------------------
// LOAN / LEASE
// --------------------
loanLeaseButtons.forEach(button => {
    button.addEventListener("click", () => {
        const value = button.textContent.trim().toLowerCase();

        loanLeaseDiv.style.display =
            (value === "loan" || value === "lease") ? "block" : "none";

        loanLeaseButtons.forEach(btn => btn.classList.remove("active"));
        button.classList.add("active");
    });
});

// --------------------
// FINISH (LAST STEP)
// --------------------
function finishWizard() {
    // hide all step content
    stepContents.forEach(content => {
        if (content) content.style.display = "none";
    });

    // reset step headers (remove edit buttons, show numbers)
    steps.forEach((step, index) => {
        const indicator = step.querySelector(".wizardstep-indicator");
        indicator.innerHTML = `<span>${index + 1}</span>`;
        step.classList.remove("wizardstep-active");
        const title = step.querySelector("b");
        title.classList.remove("text-primary");
        title.classList.add("text-muted");
    });

    // show success div
    const successDiv2 = document.getElementById("successDiv2");
    if (successDiv2) {
        successDiv2.classList.remove("hidden");
        successDiv2.style.display = "block";
    }
}

// --------------------
// FINAL STEP BUTTON
// --------------------
continueBtn4.addEventListener("click", finishWizard);


document.getElementById("toggleLocationMobile").addEventListener("click", function () {

    const box = document.getElementById("locationboxMobile");
    const icon = document.getElementById("toggleIcon").querySelector("i");

    if (box.style.display === "none" || box.style.display === "") {
        box.style.display = "block";
        icon.classList.remove("fa-square-plus");
        icon.classList.add("fa-square-minus");
    } else {
        box.style.display = "none";
        icon.classList.remove("fa-square-minus");
        icon.classList.add("fa-square-plus");
    }

});



document.getElementById("toggleAmountDown").addEventListener("click", function () {

    const content = document.getElementById("amountDownContent");
    const icon = document.querySelector("#amountIcon i");

    // toggle visibility
    content.classList.toggle("d-block");

    // toggle icon
    if (content.classList.contains("d-block")) {
        icon.classList.remove("fa-square-plus");
        icon.classList.add("fa-square-minus");
    } else {
        icon.classList.remove("fa-square-minus");
        icon.classList.add("fa-square-plus");
    }

});

document.getElementById("toggleTrade").addEventListener("click", function () {

    const content = document.getElementById("tradeContent");
    const icon = document.querySelector("#tradeIcon i");

    // toggle visibility
    content.classList.toggle("d-block");

    // toggle icon
    if (content.classList.contains("d-block")) {
        icon.classList.remove("fa-square-plus");
        icon.classList.add("fa-square-minus");
    } else {
        icon.classList.remove("fa-square-minus");
        icon.classList.add("fa-square-plus");
    }

});


// input plus  minus 

 const loanInput = document.getElementById('loanBalanceInput');
  const loanIncrementBtn = document.getElementById('loanIncrement');
  const loanDecrementBtn = document.getElementById('loanDecrement');
  const loanStep = 500; // change value by 500 each click
  const loanMax = parseInt(loanInput.max) || 1000000;
  const loanMin = parseInt(loanInput.min) || 0;

  function getLoanValue() {
    return parseInt(loanInput.value.replace(/,/g, '')) || 0;
  }

  function setLoanValue(val) {
    loanInput.value = val.toLocaleString();
  }

  loanIncrementBtn.addEventListener('click', () => {
    let val = getLoanValue();
    val += loanStep;
    if (val > loanMax) val = loanMax;
    setLoanValue(val);
  });

  loanDecrementBtn.addEventListener('click', () => {
    let val = getLoanValue();
    val -= loanStep;
    if (val < loanMin) val = loanMin;
    setLoanValue(val);
  });

  // Format manually typed numbers with commas
  loanInput.addEventListener('input', () => {
    let val = getLoanValue();
    if (val > loanMax) val = loanMax;
    if (val < loanMin) val = loanMin;
    loanInput.value = val.toLocaleString();
  });


  const input2 = document.getElementById('tradeValueInput');
  const incrementBtn = document.getElementById('increment');
  const decrementBtn = document.getElementById('decrement');
  const step2 = 1000; // change value by 1,000 each click
  const max = parseInt(input2.max) || 1000000;
  const min = parseInt(input2.min) || 0;

  // Convert input2 value to number safely
  function getValue() {
    let val = parseInt(input2.value.replace(/,/g, '')) || 0;
    return val;
  }

  function setValue(val) {
    input2.value = val.toLocaleString(); // format with commas
  }

  incrementBtn.addEventListener('click', () => {
    let val = getValue();
    val += step2;
    if (val > max) val = max;
    setValue(val);
  });

  decrementBtn.addEventListener('click', () => {
    let val = getValue();
    val -= step2;
    if (val < min) val = min;
    setValue(val);
  });

  // Optional: Format manually typed numbers with commas
  input2.addEventListener('input2', () => {
    let val = getValue();
    if (val > max) val = max;
    if (val < min) val = min;
    input2.value = val.toLocaleString();
  });





  const input = document.getElementById('unitPrice');
  const increaseBtn = document.getElementById('increase');
  const decreaseBtn = document.getElementById('decrease');

  // Set step value
  const step = 1000;

  increaseBtn.addEventListener('click', () => {
    let current = parseInt(input.value.replace(/,/g, ''), 10);
    let max = parseInt(input.max, 10);
    current += step;
    if (current > max) current = max;
    input.value = current.toLocaleString();
  });

  decreaseBtn.addEventListener('click', () => {
    let current = parseInt(input.value.replace(/,/g, ''), 10);
    let min = parseInt(input.min, 10);
    current -= step;
    if (current < min) current = min;
    input.value = current.toLocaleString();
  });
  


const track = document.querySelector(".slider-track");
const handleMin = document.getElementById("handle-min");
const handleMax = document.getElementById("handle-max");

const minValueDisplay = document.querySelector(".text-start");
const maxValueDisplay = document.querySelector(".text-end");

const MIN = 5000;
const MAX = 32000;

let minVal = MIN;
let maxVal = MAX;

let activeHandle = null;

// --------------------
// UPDATE UI
// --------------------
function updateUI() {

    const percentMin = ((minVal - MIN) / (MAX - MIN)) * 100;
    const percentMax = ((maxVal - MIN) / (MAX - MIN)) * 100;

    handleMin.style.left = percentMin + "%";
    handleMax.style.left = percentMax + "%";

    track.style.background = `
        linear-gradient(to right,
            #ccc ${percentMin}%,
            #166B87 ${percentMin}%,
            #166B87 ${percentMax}%,
            #ccc ${percentMax}%
        )
    `;

    minValueDisplay.textContent = minVal;
    maxValueDisplay.textContent = maxVal;
}

// --------------------
// START DRAG
// --------------------
handleMin.addEventListener("mousedown", () => {
    activeHandle = "min";
});

handleMax.addEventListener("mousedown", () => {
    activeHandle = "max";
});

// --------------------
// STOP DRAG
// --------------------
document.addEventListener("mouseup", () => {
    activeHandle = null;
});

// --------------------
// DRAGGING
// --------------------
document.addEventListener("mousemove", (e) => {

    if (!activeHandle) return;

    const rect = track.getBoundingClientRect();
    let percent = (e.clientX - rect.left) / rect.width;

    percent = Math.max(0, Math.min(1, percent));

    let value = Math.round(MIN + percent * (MAX - MIN));

    if (activeHandle === "min") {
        if (value >= maxVal) value = maxVal - 100;
        minVal = value;
    }

    if (activeHandle === "max") {
        if (value <= minVal) value = minVal + 100;
        maxVal = value;
    }

    updateUI();
});

// --------------------
// INIT
// --------------------
updateUI();