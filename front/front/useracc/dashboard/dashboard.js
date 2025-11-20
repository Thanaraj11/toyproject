// Simulated logged-in username
    const loggedInUser = 'JohnDoe';
    document.getElementById('username').textContent = loggedInUser;

    document.getElementById('logout').addEventListener('click', e => {
      e.preventDefault();
      alert('Logged out successfully!');
      window.location.href = 'login.html';
    });