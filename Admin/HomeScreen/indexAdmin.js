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
  // function loadPage(page) {
  //     let content = '';
  //     switch (page) {
  //       case 'dashboard':
  //         content = `
  //           <h1>Dashboard</h1>
  //           <p>Welcome to your dashboard overview.</p>
  //         `;
  //         break;
  //       case 'products':
  //         content = `
  //           <h1>Products</h1>
  //           <p>Here you can manage all your products.</p>
  //         `;
  //         break;
  //       case 'orders':
  //         content = `
  //           <h1>Orders</h1>
  //           <p>View and manage all customer orders here.</p>
  //         `;
  //         break;
  //       case 'customers':
  //         content = `
  //           <h1>Customers</h1>
  //           <p>Manage customer data and relationships here.</p>
  //         `;
  //         break;
  //       case 'settings':
  //         content = `
  //           <h1>Settings</h1>
  //           <p>Change your system preferences and configurations here.</p>
  //         `;
  //         break;
  //     }
  //     document.getElementById('mainContent').innerHTML = content;
  //   }
  
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