<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToyBox - Header Redesign</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style1.css">

    
    <style>
       /* ===== HEADER STYLES ===== */
header {
    background: var(--primary-white);
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    position: sticky;
    top: 0;
    z-index: 1000;
    border-bottom: 1px solid var(--medium-gray);
}

.header-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 0;
    gap: 2rem;
}

/* Logo Styles */
.logo {
    display: flex;
    align-items: center;
    text-decoration: none;
    color: var(--primary-black);
    gap: 0.5rem;
    flex-shrink: 0;
}

.logo i {
    font-size: 2rem;
    color: var(--dark-blue);
}

.logo h1 {
    font-size: 1.8rem;
    font-weight: 700;
    margin: 0;
    color: var(--primary-black);
}

.logo:hover {
    color: var(--dark-blue);
}

.logo:hover i {
    transform: scale(1.1);
    transition: transform 0.3s ease;
}

/* Search Form */
#search-form {
    display: flex;
    flex: 1;
    max-width: 500px;
    position: relative;
}

#search-input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid var(--medium-gray);
    border-radius: 25px;
    font-size: 0.9rem;
    background: var(--light-gray);
    transition: all 0.3s ease;
}

#search-input:focus {
    background: var(--primary-white);
    border-color: var(--dark-blue);
    box-shadow: 0 0 0 3px rgba(25, 118, 210, 0.1);
}

#search-form button {
    position: absolute;
    right: 5px;
    top: 50%;
    transform: translateY(-50%);
    background: var(--dark-blue);
    color: var(--primary-white);
    border: none;
    border-radius: 20px;
    padding: 0.5rem 1rem;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
}

#search-form button:hover {
    background: #1565c0;
    transform: translateY(-50%) scale(1.05);
}

/* Navigation Links */
.main-nav-links {
    display: flex;
    list-style: none;
    gap: 1.5rem;
    margin: 0;
    padding: 0;
    flex-shrink: 0;
}

.main-nav-links a {
    text-decoration: none;
    color: var(--dark-gray);
    font-weight: 600;
    padding: 0.5rem 0;
    position: relative;
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

.main-nav-links a:hover {
    color: var(--dark-blue);
}

.main-nav-links a::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 0;
    height: 2px;
    background: var(--dark-blue);
    transition: width 0.3s ease;
}

.main-nav-links a:hover::after {
    width: 100%;
}

/* Cart Section */
.cart-section {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-shrink: 0;
}

.view-cart {
    background: var(--dark-blue);
    color: var(--primary-white);
    padding: 0.5rem 1rem;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    display: none; /* Hidden by default, shown on mobile */
}

.view-cart:hover {
    background: #1565c0;
    transform: translateY(-1px);
}

.cart-btn {
    position: relative;
    background: none;
    border: none;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.cart-btn a {
    text-decoration: none;
    color: var(--dark-gray);
    display: flex;
    align-items: center;
    justify-content: center;
}

.cart-btn:hover {
    background: var(--light-gray);
}

.cart-btn i {
    font-size: 1.3rem;
    color: var(--dark-gray);
}

.cart-btn:hover i {
    color: var(--dark-blue);
}

.cart-count {
    position: absolute;
    top: -5px;
    right: -5px;
    background: var(--heart-red);
    color: var(--primary-white);
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.7rem;
    font-weight: 600;
}

/* Accessibility */
.visually-hidden {
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

/* ===== RESPONSIVE DESIGN ===== */
@media (max-width: 1024px) {
    .header-content {
        gap: 1.5rem;
    }
    
    .main-nav-links {
        gap: 1rem;
    }
    
    #search-form {
        max-width: 400px;
    }
}

@media (max-width: 768px) {
    .header-content {
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .logo {
        order: 1;
    }
    
    #search-form {
        order: 3;
        max-width: 100%;
        min-width: 100%;
    }
    
    .main-nav-links {
        order: 2;
        gap: 0.8rem;
    }
    
    .cart-section {
        order: 4;
    }
    
    .main-nav-links a {
        font-size: 0.85rem;
    }
    
    .logo h1 {
        font-size: 1.5rem;
    }
    
    .logo i {
        font-size: 1.7rem;
    }
}

@media (max-width: 480px) {
    .header-content {
        padding: 0.8rem 0;
    }
    
    .main-nav-links {
        display: none; /* Hide nav links on mobile, consider hamburger menu */
    }
    
    .view-cart {
        display: inline-block; /* Show text cart link on mobile */
    }
    
    .cart-btn {
        display: none; /* Hide icon cart button on mobile */
    }
    
    .logo h1 {
        font-size: 1.3rem;
    }
    
    .logo i {
        font-size: 1.5rem;
    }
}

/* Mobile Navigation Alternative */
@media (max-width: 480px) {
    /* If you want to implement a hamburger menu instead of hiding nav */
    .mobile-menu-btn {
        display: block;
        background: none;
        border: none;
        font-size: 1.5rem;
        color: var(--dark-gray);
        cursor: pointer;
        padding: 0.5rem;
    }
    
    .main-nav-links.mobile-open {
        display: flex;
        flex-direction: column;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: var(--primary-white);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        padding: 1rem;
        gap: 0;
    }
    
    .main-nav-links.mobile-open li {
        width: 100%;
    }
    
    .main-nav-links.mobile-open a {
        display: block;
        padding: 0.8rem 1rem;
        border-bottom: 1px solid var(--light-gray);
    }
    
    .main-nav-links.mobile-open a:last-child {
        border-bottom: none;
    }
}

/* Animation for header */
header {
    animation: slideDown 0.3s ease-out;
}

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
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <a href="../../main/index/index.php" class="logo">
                    <i class="fas fa-box-open"></i>
                    <h1>ToyBox</h1>
                </a>
                
                <form id="search-form" method="GET" action="" role="search" aria-label="Search products">
                    <label for="search-input" class="visually-hidden">Search products</label>
                    <input id="search-input" type="search" name="q" placeholder="Search toys, e.g. robot" aria-label="Search products" value="">
                    <button type="submit">Search</button>
                </form>
                
                <ul class="main-nav-links">
                    <li><a href="../index/index.php">Home</a></li>
                    <li><a href="../productlist/productlist.php">Categories</a></li>
                    <li><a href="../../informationalpages/contact/contact.php">Contact</a></li>
                    <li><a href="../../informationalpages/about/about.php">About Us</a></li>
                    <li><a href="../../front/useracc/login/login.php">Sign in</a></li>

                </ul>
                
                <div class="cart-section">
                    <a href="../cart/cart.php" class="view-cart">View Cart</a>
                    <button class="cart-btn">
                        <a href="../cart/cart.php"><i class="fas fa-shopping-cart"></i>
                        <span class="cart-count"></span></a>
                    </button>
                </div>
            </div>
        </div>
    </header>
    
    
</body>
</html>