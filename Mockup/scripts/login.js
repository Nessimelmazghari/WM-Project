document.addEventListener('DOMContentLoaded', function () {
  const loginForm = document.getElementById('loginForm');
  
  // Handle login form submission
  loginForm.addEventListener('submit', function(event) {
    event.preventDefault();
    
    // In een echte applicatie zou hier een server-side verificatie plaatsvinden
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    
    // Simuleer succesvolle login en redirect naar de Nesflixer-pagina
    if (username && password) {
      alert(`Logged in as: ${username}`);
      window.location.href = 'index.html';  // Redirect naar Nesflixer-pagina
    } else {
      alert('Please enter valid credentials!');
    }
    
    // Reset formulier na poging
    loginForm.reset();
  });
});
