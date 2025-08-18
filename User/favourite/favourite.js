



document.addEventListener("DOMContentLoaded", () => {
    const favBtn = document.getElementById("add-to-favourite");

    favBtn.addEventListener("click", () => {
        let productId = favBtn.getAttribute("data-product-id");

        fetch("../favourite/add_to_favourite.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: `product_id=${productId}`
        })
        .then(response => response.text())
        .then(data => {
            console.log(data); // Debugging
            alert(data); // Optional popup message
        })
        .catch(error => console.error("Error:", error));
    });
});


document.addEventListener("DOMContentLoaded", () => {
    const favBtn = document.getElementById("add-to-favourite");

    favBtn.addEventListener("click", () => {
        const productId = favBtn.getAttribute("data-product-id");

        fetch("../favourite/favourite.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "product_id=" + productId
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === "added") {
                favBtn.classList.remove("bg-blue-600", "hover:bg-blue-700");
                favBtn.classList.add("bg-red-600", "hover:bg-red-700");
                favBtn.innerHTML = '<i class="fas fa-heart mr-2"></i> Favourited';
            } else if (data.status === "removed") {
                favBtn.classList.remove("bg-red-600", "hover:bg-red-700");
                favBtn.classList.add("bg-blue-600", "hover:bg-blue-700");
                favBtn.innerHTML = '<i class="fas fa-heart mr-2"></i> Favourite';
            } else {
                console.error("Error:", data.message);
            }
        })
        .catch(error => console.error("Fetch error:", error));
    });
});

