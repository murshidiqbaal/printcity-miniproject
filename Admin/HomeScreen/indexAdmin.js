function showSection(sectionId) {
    // Hide all sections
    document.querySelectorAll('.section').forEach(sec => {
        sec.classList.remove('active');
    });
    // Show the selected section
    document.getElementById(sectionId).classList.add('active');

    // Optionally, you can also update the active link in the sidebar
    document.querySelectorAll('.nav-links a').forEach(link => {
        link.classList.remove('active');
    });
    document.querySelector(`.nav-links a[onclick*="${sectionId}"]`).classList.add('active');
}

// Initialize by showing the dashboard section
document.addEventListener("DOMContentLoaded", () => {
    showSection('dashboard');
});
