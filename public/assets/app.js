document.addEventListener("DOMContentLoaded", function () {
    // Sidebar toggle functionality
    const sidebarToggle = document.getElementById("sidebar-toggle");
    if (sidebarToggle) {
        sidebarToggle.addEventListener("click", function () {
            document.querySelector(".sidebar").classList.toggle("collapsed");
        });
    }

    // Active navigation functionality
    function setActiveNavigation() {
        const currentURL = window.location.href;
        const currentRoute = window.currentRoute || ""; // Will be set from blade template
        const navItems = document.querySelectorAll(".nav-item");

        // Remove active class from all items
        navItems.forEach((item) => item.classList.remove("active"));

        // Add active class to matching nav item
        navItems.forEach((item) => {
            const link = item.querySelector("a");
            const href = link.getAttribute("href");

            // Skip empty or hash links
            if (!href || href === "#") {
                return;
            }

            // Check for exact URL match
            if (href === currentURL) {
                item.classList.add("active");
                return;
            }

            // Check for route name based matching
            const dataPage = item.getAttribute("data-page");

            if (
                currentRoute === "admin.dashboard" &&
                dataPage === "admin-dashboard"
            ) {
                item.classList.add("active");
            } else if (
                currentRoute === "admin.guru.index" &&
                dataPage === "data-guru"
            ) {
                item.classList.add("active");
            } else if (
                currentRoute === "admin.siswa.index" &&
                dataPage === "data-siswa"
            ) {
                item.classList.add("active");
            } else if (
                (currentRoute === "school.profile" ||
                    currentRoute === "school.edit") &&
                dataPage === "data-sekolah"
            ) {
                item.classList.add("active");
            } else if (
                currentRoute === "kepala-sekolah.dashboard" &&
                dataPage === "dashboard"
            ) {
                item.classList.add("active");
            } else if (
                currentRoute === "guru.dashboard" &&
                dataPage === "beranda"
            ) {
                item.classList.add("active");
            } else if (
                currentRoute === "siswa.dashboard" &&
                dataPage === "beranda"
            ) {
                item.classList.add("active");
            } else if (
                currentRoute === "profile.show" &&
                dataPage === "profil-saya"
            ) {
                item.classList.add("active");
            }
        });
    }

    // Initialize active navigation
    setActiveNavigation();
});
