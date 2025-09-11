import anime from 'animejs/lib/anime.es.js';

anime({
  targets: 'h1 span',
  translateY: ['-2rem', '0rem'],
  delay: anime.stagger(100),
  duration: 600,
  loop: true,
  direction: 'alternate',
  easing: 'easeInOutSine'
});
 function handleCredentialResponse(response) {
     // Decode JWT token to get user info
     const data = parseJwt(response.credential);

     // Fill hidden inputs with user info
     document.getElementById('google_email').value = data.email || '';
     document.getElementById('google_name').value = data.name || '';
     // Phone number is not included in the ID token by default
     document.getElementById('google_phone').value = '';

     // Submit the form
     document.getElementById('login-form').submit();
   }

   function parseJwt(token) {
     var base64Url = token.split('.')[1];
     var base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
     var jsonPayload = decodeURIComponent(atob(base64).split('').map(function(c) {
         return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
     }).join(''));

     return JSON.parse(jsonPayload);
   }

   window.onload = function () {
     google.accounts.id.initialize({
       client_id: 'YOUR_GOOGLE_CLIENT_ID.apps.googleusercontent.com',
       callback: handleCredentialResponse,
       scope: 'email profile https://www.googleapis.com/auth/user.phonenumbers.read' // phone number scope (requires extra API)
     });
     google.accounts.id.renderButton(
       document.getElementById("googleSignInDiv"),
       { theme: "outline", size: "large", width: '250' }  // Customize button style to fit your UI
     );
     // Optional: prompt the user to select account
     // google.accounts.id.prompt();
   }


     if (window.history && window.history.pushState) {
      window.history.pushState(null, "", window.location.href);
      window.onpopstate = function () {
          window.history.pushState(null, "", window.location.href);
      };
  }