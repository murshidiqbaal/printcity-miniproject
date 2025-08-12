
        // Use current location for address
        document.getElementById('getLocationBtn').addEventListener('click', function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const latitude = position.coords.latitude;
                        const longitude = position.coords.longitude;
                        
                        // In a real app, you would call a geocoding API (like Google Maps or Mapbox)
                        // to convert coordinates to a human-readable address
                        // This is just a simulation
                        document.getElementById('address').value = "456 Current Location St";
                        document.getElementById('city').value = "San Francisco";
                        document.getElementById('state').value = "CA";
                        document.getElementById('zip_code').value = "94102";
                        
                        alert("Location detected and address fields populated automatically!");
                    },
                    function(error) {
                        alert("Error getting location: " + error.message);
                    }
                );
            } else {
                alert("Geolocation is not supported by your browser");
            }
        });

        // Form validation
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            const phone = document.getElementById('phone').value.trim();
            const address = document.getElementById('address').value.trim();
            const city = document.getElementById('city').value.trim();
            const state = document.getElementById('state').value.trim();
            const zipCode = document.getElementById('zip_code').value.trim();
            const country = document.getElementById('country').value;
            
            // Basic phone validation
            if (!/^[\d\s\(\)\-\+]{10,}$/.test(phone)) {
                alert("Please enter a valid phone number with at least 10 digits");
                e.preventDefault();
                return;
            }
            
            // Basic address validation
            if (!address || !city || !state || !zipCode || !country) {
                alert("Please fill in all required address fields");
                e.preventDefault();
                return;
            }
            
            // In a real app, you might make an AJAX call to save the form
            // Here we just allow the form to submit normally
        });

        // Preview profile picture when selected
        document.getElementById('profile_picture').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.querySelector('.profile-picture').src = event.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
  