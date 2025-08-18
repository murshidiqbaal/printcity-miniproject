document.addEventListener("DOMContentLoaded", function () {
    const orderForm = document.getElementById("order-form");
    orderForm.addEventListener("submit", function (e) {
        e.preventDefault();

        const formData = new FormData(orderForm);
        fetch("submitorder.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            console.log(data);
            // Handle success (e.g., show a success message, redirect, etc.)
        })
        .catch(error => {
            console.error("Error:", error);
            // Handle error (e.g., show an error message)
        });
    });
});