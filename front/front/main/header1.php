<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToyBox - Header Redesign</title>
    <link href="../main/style1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
       :root {
    /* Color Palette */
    --primary-color: #78cbeaff;
    --secondary-color: #D3D3D3;
    --accent-color: #FFD166;
    --accent-hover: #FFC145;
    --danger-color: #FF6B6B;
    --text-dark: #333333;
    --text-light: #ffffff;
    --bg-light: #f8f9fa;
    --bg-white: #ffffff;
    
    /* Gradient */
    --header-gradient: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    
    /* Typography */
    --font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    --font-weight-normal: 400;
    --font-weight-medium: 500;
    --font-weight-bold: 600;
    --font-size-xs: 0.8rem;
    --font-size-sm: 0.9rem;
    --font-size-base: 1rem;
    --font-size-lg: 1.5rem;
    --font-size-xl: 1.8rem;
    --font-size-2xl: 2rem;
    --font-size-3xl: 2.5rem;
    
    /* Spacing */
    --space-xs: 5px;
    --space-sm: 10px;
    --space-md: 10px;
    --space-lg: 20px;
    --space-xl: 25px;
    --space-2xl: 30px;
    --space-3xl: 40px;
    
    /* Border Radius */
    --radius-sm: 4px;
    --radius-md: 10px;
    --radius-lg: 30px;
    --radius-full: 50%;
    
    /* Shadows */
    --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.1);
    --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 4px 12px rgba(0, 0, 0, 0.1);
    --shadow-xl: 0 6px 8px rgba(0, 0, 0, 0.15);
    --shadow-inset: inset 0 2px 5px rgba(0, 0, 0, 0.1);
    
    /* Transitions */
    --transition-fast: 0.2s ease;
    --transition-normal: 0.3s ease;
    --transition-slow: 0.5s ease;
    
    /* Layout */
    --header-height: 80px;
    --container-max-width: 1200px;
    --search-max-width: 500px;
    --cart-icon-size: 50px;
    --cart-badge-size: 24px;
    
    /* Z-index */
    --z-dropdown: 1000;
    --z-sticky: 1020;
    --z-modal: 1030;
    --z-popover: 1040;
    --z-tooltip: 1050;
}

/* Reset and Base Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: var(--font-family);
    line-height: 1.6;
    color: var(--text-dark);
    background-color: var(--bg-light);
}

.container {
    max-width: var(--container-max-width);
    margin: 0 auto;
    padding: 0 var(--space-lg);
}

/* Header Styles */
header {
    background: var(--header-gradient);
    color: var(--text-light);
    box-shadow: var(--shadow-lg);
    position: sticky;
    top: 0;
    z-index: var(--z-sticky);
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--space-md) 0;
    min-height: var(--header-height);
}

/* Logo Styles */
.logo {
    display: flex;
    align-items: center;
    text-decoration: none;
    color: var(--text-dark);
    font-weight: var(--font-weight-bold);
    gap: var(--space-sm);
    flex-shrink: 0;
}

.logo i {
    font-size: var(--font-size-2xl);
    color: var(--accent-color);
}

.logo h1 {
    font-size: var(--font-size-2xl);
    margin: 0;
    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.2);
}

/* Search Form Styles */
#search-form {
    display: flex;
    flex-grow: 1;
    max-width: var(--search-max-width);
    margin: 0 var(--space-2xl);
}

#search-input {
    flex-grow: 1;
    padding: var(--space-md) var(--space-lg);
    border: none;
    border-radius: var(--radius-lg) 0 0 var(--radius-lg);
    font-size: var(--font-size-base);
    outline: none;
    box-shadow: var(--shadow-inset);
}

#search-form button {
    background-color: var(--accent-color);
    color: var(--text-dark);
    border: none;
    padding: var(--space-md) var(--space-xl);
    border-radius: 0 var(--radius-lg) var(--radius-lg) 0;
    cursor: pointer;
    font-size: var(--font-size-base);
    font-weight: var(--font-weight-bold);
    transition: all var(--transition-normal);
}

#search-form button:hover {
    background-color: var(--accent-hover);
    transform: translateY(-2px);
}

/* Navigation Styles */
.main-nav-links {
    display: flex;
    list-style: none;
    gap: var(--space-xl);
    margin-right: var(--space-lg);
}

.main-nav-links a {
    color: var(--text-light);
    text-decoration: none;
    font-weight: var(--font-weight-medium);
    transition: all var(--transition-normal);
    padding: var(--space-sm) 0;
    position: relative;
}

.main-nav-links a:hover {
    color: var(--accent-color);
}

.main-nav-links a::after {
    content: '';
    position: absolute;
    width: 0;
    height: 2px;
    bottom: 0;
    left: 0;
    background-color: var(--accent-color);
    transition: width var(--transition-normal);
}

.main-nav-links a:hover::after {
    width: 100%;
}

/* Cart Styles */
.cart-section {
    display: flex;
    align-items: center;
    gap: var(--space-md);
}

.view-cart {
    background-color: var(--accent-color);
    color: var(--text-dark);
    padding: var(--space-sm) var(--space-lg);
    border-radius: var(--radius-lg);
    text-decoration: none;
    font-weight: var(--font-weight-bold);
    transition: all var(--transition-normal);
    box-shadow: var(--shadow-md);
}

.view-cart:hover {
    background-color: var(--accent-hover);
    transform: translateY(-2px);
    box-shadow: var(--shadow-xl);
}

.cart-btn {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    border-radius: var(--radius-full);
    width: var(--cart-icon-size);
    height: var(--cart-icon-size);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    cursor: pointer;
    transition: all var(--transition-normal);
}

.cart-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: translateY(-2px);
}

.cart-btn a {
    color: var(--text-light);
    text-decoration: none;
    font-size: var(--font-size-lg);
}

.cart-count {
    position: absolute;
    top: -5px;
    right: -5px;
    background-color: var(--danger-color);
    color: var(--text-light);
    border-radius: var(--radius-full);
    width: var(--cart-badge-size);
    height: var(--cart-badge-size);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: var(--font-size-xs);
    font-weight: var(--font-weight-bold);
    box-shadow: var(--shadow-sm);
}

/* Responsive Design */
@media (max-width: 992px) {
    :root {
        --search-max-width: 100%;
        --space-xl: 15px;
    }
    
    .header-content {
        flex-wrap: wrap;
        gap: var(--space-md);
    }
    
    #search-form {
        order: 3;
        max-width: var(--search-max-width);
        margin: var(--space-sm) 0 0 0;
    }
    
    .main-nav-links {
        margin-right: 0;
    }
}

@media (max-width: 768px) {
    :root {
        --font-size-2xl: 1.8rem;
        --space-xl: 10px;
    }
    
    .header-content {
        flex-direction: column;
        align-items: stretch;
    }
    
    .logo {
        justify-content: center;
    }
    
    .main-nav-links {
        justify-content: center;
        flex-wrap: wrap;
        gap: var(--space-md);
        margin: var(--space-sm) 0;
    }
    
    .cart-section {
        justify-content: center;
    }
}

@media (max-width: 480px) {
    :root {
        --font-size-2xl: 1.6rem;
        --space-lg: 15px;
    }
    
    .main-nav-links {
        flex-direction: column;
        align-items: center;
        gap: var(--space-sm);
    }
    
    .cart-section {
        flex-direction: column;
        gap: var(--space-sm);
    }
}

/* Utility Classes */
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

.text-center {
    text-align: center;
}

.flex {
    display: flex;
}

.flex-center {
    display: flex;
    align-items: center;
    justify-content: center;
}

.flex-between {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.mt-1 { margin-top: var(--space-xs); }
.mt-2 { margin-top: var(--space-sm); }
.mt-3 { margin-top: var(--space-md); }
.mt-4 { margin-top: var(--space-lg); }

.mb-1 { margin-bottom: var(--space-xs); }
.mb-2 { margin-bottom: var(--space-sm); }
.mb-3 { margin-bottom: var(--space-md); }
.mb-4 { margin-bottom: var(--space-lg); }

.p-1 { padding: var(--space-xs); }
.p-2 { padding: var(--space-sm); }
.p-3 { padding: var(--space-md); }
.p-4 { padding: var(--space-lg); }
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
                    <li><a href="../informationalpages/contact/contact.php">Contact</a></li>
                    <li><a href="../informationalpages/about/about.php">About Us</a></li>
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
    
    <!-- <main class="demo-content">
        <h2>Welcome to ToyBox - Your Ultimate Toy Store</h2>
        <p>This header layout features all elements in a single row as requested:</p>
        <p><strong>ToyBox logo | Search bar | Navigation links | View Cart button | Cart icon</strong></p>
        
        <div class="features">
            <div class="feature">
                <i class="fas fa-shipping-fast"></i>
                <h3>Fast Delivery</h3>
                <p>Get your toys delivered to your doorstep in just 2-3 business days.</p>
            </div>
            <div class="feature">
                <i class="fas fa-gift"></i>
                <h3>Best Brands</h3>
                <p>We carry all the top toy brands that kids love and parents trust.</p>
            </div>
            <div class="feature">
                <i class="fas fa-headset"></i>
                <h3>24/7 Support</h3>
                <p>Our customer service team is always ready to help with any questions.</p>
            </div>
        </div>
    </main> -->
</body>
</html>