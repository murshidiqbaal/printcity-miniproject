// script.js
document.addEventListener("DOMContentLoaded", () => {
  const toggleBtn = document.getElementById("toggleBtn");
  const sidebar = document.querySelector(".sidebar");

  toggleBtn.addEventListener("click", () => {
    sidebar.classList.toggle("hidden");
  });

  // Optional: Close sidebar when a link is clicked (for mobile experience)
  const links = document.querySelectorAll(".sidebar a");
  links.forEach(link => {
    link.addEventListener("click", () => {
      sidebar.classList.add("hidden");
    });
  });
});
  
    // Switch sections without page reload
    function showSection(sectionId) {
        document.querySelectorAll('.section').forEach(sec => {
            sec.classList.remove('active');
        });
        document.getElementById(sectionId).classList.add('active');

        // Optional: Load data dynamically via PHP when switching
        if (sectionId === 'orders') {
            fetch('fetch_orders.php') // Your PHP file to get orders
                .then(response => response.text())
                .then(data => {
                    document.getElementById('ordersList').innerHTML = data;
                });
        }
    }