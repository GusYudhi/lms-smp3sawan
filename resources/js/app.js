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
            e.preventDefault();
            const target = document.querySelector(this.getAttribute("href"));
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
});
