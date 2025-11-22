<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>head</title>
    <!-- <link rel="stylesheet" href="../useracc/user.css"> -->
   
    <style>
      /* Header Specific Styles */
:root {
  --primary-black: #000000;
  --primary-white: #ffffff;
  --light-blue: #e3f2fd;
  --medium-blue: #90caf9;
  --dark-blue: #1976d2;
  --light-gray: #f5f5f5;
  --medium-gray: #e0e0e0;
  --dark-gray: #424242;
  --text-gray: #757575;
  --error-red: #f44336;
}

/* Reset and Base Styles */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  line-height: 1.6;
  background-color: var(--light-gray);
}

/* Header Styles */
header {
  background-color: var(--primary-black);
  color: var(--primary-white);
  padding: 1rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  position: sticky;
  top: 0;
  z-index: 1000;
}

header h1 {
  margin: 0;
  font-size: 1.8rem;
  font-weight: 700;
  background: linear-gradient(135deg, var(--primary-white), var(--medium-blue));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

/* Navigation Styles */
nav {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
  align-items: center;
}

nav a {
  color: var(--primary-white);
  text-decoration: none;
  padding: 0.6rem 1.2rem;
  border-radius: 6px;
  transition: all 0.3s ease;
  font-size: 0.9rem;
  font-weight: 500;
  position: relative;
  overflow: hidden;
}

nav a::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
  transition: left 0.5s ease;
}

nav a:hover::before {
  left: 100%;
}

nav a:hover {
  background-color: var(--dark-gray);
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

/* Specific link styles */
#logout {
  background-color: var(--error-red);
  font-weight: 600;
}

#logout:hover {
  background-color: #d32f2f;
  transform: translateY(-2px);
}

/* Active link indicator */
nav a:active {
  transform: translateY(0);
}

/* Focus styles for accessibility */
nav a:focus {
  outline: 2px solid var(--medium-blue);
  outline-offset: 2px;
}

/* Responsive Design */
@media (max-width: 1024px) {
  header {
    padding: 1rem 1.5rem;
  }
  
  nav {
    gap: 0.4rem;
  }
  
  nav a {
    padding: 0.5rem 1rem;
    font-size: 0.85rem;
  }
}

@media (max-width: 768px) {
  header {
    flex-direction: column;
    gap: 1rem;
    padding: 1rem;
  }
  
  header h1 {
    font-size: 1.5rem;
    text-align: center;
  }
  
  nav {
    justify-content: center;
    width: 100%;
  }
  
  nav a {
    flex: 1;
    text-align: center;
    min-width: 120px;
    padding: 0.6rem 0.8rem;
  }
}

@media (max-width: 480px) {
  header {
    padding: 0.8rem;
  }
  
  header h1 {
    font-size: 1.3rem;
  }
  
  nav {
    flex-direction: column;
    gap: 0.5rem;
    width: 100%;
  }
  
  nav a {
    width: 100%;
    text-align: center;
    padding: 0.8rem;
    font-size: 0.9rem;
  }
}

/* Animation for header */
@keyframes slideDown {
  from {
    transform: translateY(-100%);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

header {
  animation: slideDown 0.5s ease-out;
}

/* Print Styles */
@media print {
  header {
    background-color: var(--primary-white) !important;
    color: var(--primary-black) !important;
    box-shadow: none !important;
    border-bottom: 2px solid var(--primary-black);
  }
  
  header h1 {
    background: none !important;
    -webkit-text-fill-color: var(--primary-black) !important;
    color: var(--primary-black) !important;
  }
  
  nav a {
    color: var(--primary-black) !important;
    border: 1px solid var(--primary-black);
  }
}

/* Utility Classes */
.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border: 0;
}

/* Scrollbar Styling for Webkit Browsers */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: var(--light-gray);
}

::-webkit-scrollbar-thumb {
  background: var(--medium-gray);
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: var(--dark-gray);
}
    </style>
</head>
<body>
    <header>
    
    
    <nav>
      <a href="../../main/index/index.php">Home</a>
      <a href="../../main/cart/cart.php">Cart</a>
      <!-- <a href="../useracc/login/logout.php" id="logout">Logout</a> -->
      <a href="../orderhistory/orderhistory.php">Order History</a>
      <a href="../register/register.php">Register</a>
      <a href="../adress/adress.php">Address Book</a>
      <a href="../wishlist/wishlist.php">Wishlist</a>
      <a href="../login/logout.php">log out</a>
    </nav>
  </header>
</body>
</html>