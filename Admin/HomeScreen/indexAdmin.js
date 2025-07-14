 const links = document.querySelectorAll('.nav-links a');
    const iframe = document.getElementById('content-frame');

    links.forEach(link => {
      link.addEventListener('click', (e) => {
        e.preventDefault();

        links.forEach(l => l.classList.remove('active'));
        link.classList.add('active');

        const page = link.getAttribute('data-page');

        if (page === "dashboard") {
          iframe.srcdoc = "<h1 style='padding: 30px;'>Welcome to PrintCity Admin</h1><p style='padding: 0 30px;'>This is your admin dashboard. Use the sidebar to navigate.</p>";
        } else if (page === "customers") {
          iframe.srcdoc = "<h2 style='padding:20px;'>Customers Page (Under Construction)</h2>";
        } else if (page === "settings") {
          iframe.srcdoc = "<h2 style='padding:20px;'>Settings Page (Coming Soon)</h2>";
        } else {
          iframe.src = page;
        }
      });
    });