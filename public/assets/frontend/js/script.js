    document.querySelectorAll(".dropdown-toggle").forEach(function (btn) {
            btn.addEventListener("click", function (e) {
                e.preventDefault();

                let dropdown = this.closest(".dropdown");
                let menu = dropdown.querySelector(".dropdown-menu");

                // check if already open
                let isOpen = menu.classList.contains("show");

                // close all dropdowns
                document.querySelectorAll(".dropdown-menu").forEach(function (m) {
                    m.classList.remove("show");
                });

                // toggle current
                if (!isOpen) {
                    menu.classList.add("show");
                }
            });
        });

        // close when clicking outside
        document.addEventListener("click", function (e) {
            if (!e.target.closest(".dropdown")) {
                document.querySelectorAll(".dropdown-menu").forEach(function (menu) {
                    menu.classList.remove("show");
                });
            }
        });

        const menuBtn = document.getElementById("menuBtn");
        const closeBtn = document.getElementById("closeDrawerBtn");
        const drawer = document.getElementById("mobileDrawer");

        // Open drawer
        menuBtn.addEventListener("click", () => {
            drawer.classList.add("show");
        });

        // Close drawer
        closeBtn.addEventListener("click", () => {
            drawer.classList.remove("show");
        });
        const parentNavs = document.querySelectorAll('.parent-nav');

        parentNavs.forEach(parentNav => {
            const submenu = parentNav.querySelector('.submenu');

            parentNav.addEventListener('click', function (e) {
                // Prevent toggling when clicking submenu links
                if (e.target.tagName.toLowerCase() === 'a') return;

                // Toggle this submenu
                submenu.style.display = submenu.style.display === 'block' ? 'none' : 'block';
                parentNav.classList.toggle('open');
            });
        });

document.querySelectorAll(".img-srp-container").forEach(container => {

    const images = Array.from(container.querySelectorAll(".img-srp"));
    const extraDiv = container.querySelector("#has-more-photos"); // your extra div
    const leftBtn = container.querySelector(".left-toggle");
    const rightBtn = container.querySelector(".right-toggle");

    let currentIndex = 0;

    function showSlide(index) {
        images.forEach((img, i) => {
            // Show last image only on its own or with extra div
            if (i === index || (i === images.length - 1 && index === images.length)) {
                img.classList.remove("d-none");
                img.classList.add("d-block");
            } else {
                img.classList.remove("d-block");
                img.classList.add("d-none");
            }
        });

        // Show extra div only on the final slide
        if (extraDiv) {
            if (index === images.length) {
                extraDiv.classList.remove("d-none");
                extraDiv.classList.add("d-block");
            } else {
                extraDiv.classList.remove("d-block");
                extraDiv.classList.add("d-none");
            }
        }

        // Hide arrows on the final slide
        leftBtn.style.display = images.length > 1 && index !== images.length ? "block" : "none";
        rightBtn.style.display = images.length > 1 && index !== images.length ? "block" : "none";
    }

    rightBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        currentIndex = (currentIndex + 1) % (images.length + 1); // +1 for extra div
        showSlide(currentIndex);
    });

    leftBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        currentIndex = (currentIndex - 1 + (images.length + 1)) % (images.length + 1);
        showSlide(currentIndex);
    });

    // Initialize
    showSlide(currentIndex);
});

document.querySelectorAll(".btn-group button").forEach(btn => {
    btn.addEventListener("click", () => {
        const tab = btn.dataset.tab;

        // Toggle active class on buttons
        btn.parentElement.querySelectorAll("button").forEach(b => b.classList.remove("active"));
        btn.classList.add("active");

        // Toggle table content
        document.querySelectorAll(".tab-content").forEach(content => {
            if (content.dataset.tab === tab) {
                content.classList.remove("d-none");
            } else {
                content.classList.add("d-none");
            }
        });
    });
});