import "./bootstrap";

// Import Bootstrap JavaScript
import "bootstrap";

// Custom JavaScript for the application
document.addEventListener("DOMContentLoaded", function () {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(
        document.querySelectorAll('[data-bs-toggle="tooltip"]')
    );
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize popovers
    const popoverTriggerList = [].slice.call(
        document.querySelectorAll('[data-bs-toggle="popover"]')
    );
    const popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Sidebar toggle for mobile
    const sidebarToggle = document.querySelector(".navbar-toggler");
    const sidebar = document.querySelector(".sidebar");

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener("click", function () {
            sidebar.classList.toggle("show");
        });
    }

    // Auto hide alerts after 5 seconds
    const alerts = document.querySelectorAll(".alert:not(.alert-permanent)");
    alerts.forEach(function (alert) {
        setTimeout(function () {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // Smooth scrolling for anchor links
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(function (link) {
        link.addEventListener("click", function (e) {
            const href = this.getAttribute("href");
            // Abaikan "#" kosong (mis. toggle dropdown) agar tidak error selector
            if (!href || href === "#") return;
            e.preventDefault();
            let target = null;
            try {
                target = document.querySelector(href);
            } catch (err) {
                return;
            }
            if (target) {
                target.scrollIntoView({
                    behavior: "smooth",
                    block: "start",
                });
            }
        });
    });

    // Material Design 3 Ripple Effect
    document.addEventListener("click", function (e) {
        const target = e.target.closest(".btn, .card-clickable, .ripple-effect");
        if (!target) return;

        const rect = target.getBoundingClientRect();
        const ripple = document.createElement("span");
        ripple.classList.add("m3-ripple");

        const diameter = Math.max(rect.width, rect.height);
        const radius = diameter / 2;

        ripple.style.width = ripple.style.height = `${diameter}px`;
        ripple.style.left = `${e.clientX - rect.left - radius}px`;
        ripple.style.top = `${e.clientY - rect.top - radius}px`;

        const existingRipple = target.querySelector(".m3-ripple");
        if (existingRipple) {
            existingRipple.remove();
        }

        target.appendChild(ripple);

        setTimeout(() => {
            ripple.remove();
        }, 500);
    });

    // Top Scroll Progress Bar (Minimalist Reading Indicator)
    let scrollProgressBar = document.getElementById("scrollProgressBar");
    if (!scrollProgressBar) {
        scrollProgressBar = document.createElement("div");
        scrollProgressBar.id = "scrollProgressBar";
        scrollProgressBar.className = "scroll-progress-bar";
        scrollProgressBar.setAttribute("aria-hidden", "true");
        document.body.prepend(scrollProgressBar);
    }

    const updateScrollProgress = () => {
        const scrollTop = window.scrollY || document.documentElement.scrollTop;
        const scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const progress = scrollHeight > 0 ? Math.min(Math.max(scrollTop / scrollHeight, 0), 1) : 0;
        scrollProgressBar.style.transform = `scaleX(${progress})`;
    };

    window.addEventListener("scroll", updateScrollProgress, { passive: true });
    window.addEventListener("resize", updateScrollProgress, { passive: true });
    updateScrollProgress();

    // Guest Navbar Adaptive Scroll & Mobile Collapse State
    const guestNavbar = document.getElementById("guestNavbar");
    const guestNavbarCollapse = document.getElementById("navbarNav");

    if (guestNavbar) {
        const updateGuestNavbarState = () => {
            if (window.scrollY > 30) {
                guestNavbar.classList.add("is-scrolled");
            } else {
                guestNavbar.classList.remove("is-scrolled");
            }
        };

        updateGuestNavbarState();
        window.addEventListener("scroll", updateGuestNavbarState, { passive: true });

        if (guestNavbarCollapse) {
            guestNavbarCollapse.addEventListener("show.bs.collapse", () => {
                guestNavbar.classList.add("menu-expanded");
            });
            guestNavbarCollapse.addEventListener("hide.bs.collapse", () => {
                guestNavbar.classList.remove("menu-expanded");
            });
        }
    }

    // Hero Carousel Dynamic Caption Synchronization
    const heroCarousel = document.getElementById("heroCarousel");
    if (heroCarousel) {
        const captionItems = document.querySelectorAll(".hero-caption-item");
        const slideCounterCurrent = document.getElementById("heroSlideCurrent");

        heroCarousel.addEventListener("slide.bs.carousel", function (event) {
            const nextIndex = event.to;

            captionItems.forEach((item, idx) => {
                if (idx === nextIndex) {
                    item.classList.add("active");
                } else {
                    item.classList.remove("active");
                }
            });

            if (slideCounterCurrent) {
                slideCounterCurrent.textContent = String(nextIndex + 1).padStart(2, "0");
            }
        });
    }
});
