<?php
// Include necessary files
include '../../../databse/db_connection.php';
include 'index_backend.php';

// Start session for wishlist functionality
// session_start();


// Get data for the homepage
$categories = getCategories($conn);
$banners = getBanners($conn);
$featured_products = getFeaturedProducts($conn, 8);
$all_products = getAllProducts($conn, 12);

// Handle search if search term is provided
$search_results = array();
if (isset($_GET['q']) && !empty($_GET['q'])) {
    $search_term = $_GET['q'];
    $search_results = searchProducts($conn, $search_term);
}

// Check if user is logged in for wishlist functionality
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Function to get all product images
function getProductImages($conn) {
    $images = array();
    
    $sql = "SELECT * FROM product_images ORDER BY product_id, sort_order, created_at";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $images[] = $row;
        }
    }
    
    return $images;
}

// Get all images
$productImages = getProductImages($conn);
?>

<?php
// wishlist_backend.php
session_start();

// Function to add item to wishlist
function addToWishlist($customer_id, $product_id, $conn) {
    // Check if item already exists in wishlist
    $check_sql = "SELECT id FROM wishlist WHERE customer_id = ? AND product_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $customer_id, $product_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        // Item already in wishlist
        return "Item already in wishlist";
    } else {
        // Insert new item into wishlist
        $insert_sql = "INSERT INTO wishlist (customer_id, product_id) VALUES (?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("ii", $customer_id, $product_id);
        
        if ($insert_stmt->execute()) {
            return "Item added to wishlist successfully";
        } else {
            return "Error adding item to wishlist: " . $conn->error;
        }
    }
}

// Process the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login/login.php");
        exit();
    }
    
    $action = $_POST['action'] ?? '';
    $user_id = $_POST['user_id'] ?? '';
    $product_id = $_POST['product_id'] ?? '';
    
    if ($action == 'add_to_wishlist' && !empty($user_id) && !empty($product_id)) {
        $result = addToWishlist($user_id, $product_id, $conn);
        
        // Store result in session to display on redirect
        $_SESSION['wishlist_message'] = $result;
        
        // Redirect back to the previous page
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
}

// $conn->close();
?>





<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ToyBox — Online Toys Marketplace</title>
  <link rel="stylesheet" href="../style1.css">
   <!-- <link rel="stylesheet" href="../common1.css"> -->
  <!-- <link rel="stylesheet" href="index.css"> -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="index.js"></script>
  
       <style>
        
/* Homepage Specific Styles */
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
  --success-green: #4caf50;
  --error-red: #f44336;
  --heart-red: #e91e63;
  --featured-orange: #ff9800;
}

/* Main Container */
.container {
  max-width: 1400px; /* Increased for 4 cards */
  margin: 0 auto;
  padding: 0 1rem;
}

/* Simple Categories */
nav[aria-label="Categories"] {
    background: #f8fafc;
    padding: 20px 0;
    margin-bottom: 30px;
}

nav[aria-label="Categories"] h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #333;
}

#categories-list {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 10px;
    list-style: none;
    /* padding: 15px; */
    background: #9fa0a1ff !important;
}

#categories-list li {
    margin: 0;
}

#categories-list a {
    display: block;
    /* padding: 8px 16px; */
    /* background: white; */
    /* color: #333; */
    text-decoration: none;
    /* border-radius: 20px; */
    font-weight: 500;
}

#categories-list a:hover {
    /* background: #f59e0b; */
    color: white;
}


/* Section Headers */
.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  padding-bottom: 0.5rem;
  border-bottom: 2px solid var(--light-blue);
}

.section-header h2 {
  color: var(--primary-black);
  font-size: 1.8rem;
  font-weight: 700;
  margin: 0;
}

.view-all {
  color: var(--dark-blue);
  text-decoration: none;
  font-weight: 600;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  transition: all 0.3s ease;
}

.view-all:hover {
  background: var(--light-blue);
  transform: translateX(5px);
}

/* Products Grid - 4 cards per row */
.products-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr); /* Fixed 4 columns */
  gap: 1.5rem; /* Reduced gap for better fit */
  margin-bottom: 3rem;
}

/* Product Cards - Smaller for 4 in a row */
.card {
  background: var(--primary-white);
  border-radius: 10px; /* Slightly smaller */
  overflow: hidden;
  box-shadow: 0 3px 5px rgba(0,0,0,0.1); /* Smaller shadow */
  transition: all 0.3s ease;
  position: relative;
  border: 1px solid var(--medium-gray);
  height: 100%; /* Ensure consistent height */
  display: flex;
  flex-direction: column;
}

.card:hover {
  transform: translateY(-5px); /* Smaller hover lift */
  box-shadow: 0 8px 15px rgba(0,0,0,0.15);
  border-color: var(--medium-blue);
}

.image-container {
  position: relative;
  background: var(--light-gray);
  height: 200px; /* Reduced height */
  overflow: hidden;
  flex-shrink: 0; /* Prevent image container from shrinking */
}

.image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.card:hover .image {
  transform: scale(1.05);
}

/* Wishlist Button - Smaller */
.wishlist-form {
  position: absolute;
  top: 8px;
  right: 8px;
  z-index: 2;
}

.wishlist-btn {
  background: var(--primary-white);
  border: none;
  border-radius: 50%;
  width: 32px; /* Smaller */
  height: 32px; /* Smaller */
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 2px 6px rgba(0,0,0,0.2);
  color: var(--text-gray);
  font-size: 0.8rem; /* Smaller icon */
}

.wishlist-btn:hover {
  background: var(--heart-red);
  color: var(--primary-white);
  transform: scale(1.1);
}

.wishlist-btn.added {
  background: var(--heart-red);
  color: var(--primary-white);
}

/* Badge - Smaller */
.badge {
  position: absolute;
  top: 8px;
  left: 8px;
  background: var(--featured-orange);
  color: var(--primary-white);
  padding: 0.2rem 0.6rem; /* Smaller padding */
  border-radius: 15px; /* Smaller radius */
  font-size: 0.7rem; /* Smaller font */
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

/* Product Info - Compact */
.info {
  padding: 1rem; /* Reduced padding */
  position: relative;
  flex-grow: 1; /* Allow info to grow and fill space */
  display: flex;
  flex-direction: column;
}

.name {
  color: var(--primary-black);
  font-size: 1rem; /* Smaller font */
  font-weight: 600;
  margin: 0 0 0.4rem 0;
  line-height: 1.3;
  display: -webkit-box;
  -webkit-line-clamp: 2; /* Limit to 2 lines */
  -webkit-box-orient: vertical;
  overflow: hidden;
  min-height: 2.6rem; /* Fixed height for title */
}

.category {
  color: var(--text-gray);
  font-size: 0.8rem; /* Smaller font */
  margin: 0 0 0.5rem 0;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.rating {
  margin-bottom: 0.5rem;
  color: var(--featured-orange);
  font-size: 0.8rem; /* Smaller stars */
}

.price {
  color: var(--dark-blue);
  font-size: 1.1rem; /* Slightly smaller */
  font-weight: 700;
  margin: 0 0 0.8rem 0;
}

.original-price {
  color: var(--text-gray);
  text-decoration: line-through;
  font-size: 0.9rem; /* Smaller */
  margin-left: 0.5rem;
  font-weight: 400;
}

.discount {
  color: var(--error-red);
  font-size: 0.8rem; /* Smaller */
  font-weight: 600;
  margin-left: 0.5rem;
}

/* Product Actions - Smaller and Compact */
.product-actions {
  display: flex;
  gap: 0.5rem; /* Smaller gap */
  flex-wrap: wrap;
  margin-top: auto; /* Push to bottom */
}

.view-details {
  flex: 1;
  background: var(--primary-black);
  color: var(--primary-white);
  padding: 0.5rem 0.8rem; /* Smaller padding */
  text-decoration: none;
  border-radius: 5px; /* Smaller radius */
  text-align: center;
  font-weight: 600;
  transition: all 0.3s ease;
  min-width: 0; /* Allow flexible width */
  font-size: 0.8rem; /* Smaller font */
  white-space: nowrap;
}

.view-details:hover {
  background: var(--dark-gray);
  transform: translateY(-1px); /* Smaller hover effect */
}

.add-to-cart-form {
  flex: 1;
  min-width: 0; /* Allow flexible width */
}

.add-to-cart {
  width: 100%;
  background: var(--dark-blue);
  color: var(--primary-white);
  border: none;
  padding: 0.5rem 0.8rem; /* Smaller padding */
  border-radius: 5px; /* Smaller radius */
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.3rem; /* Smaller gap */
  font-size: 0.8rem; /* Smaller font */
  white-space: nowrap;
}

.add-to-cart:hover {
  background: #1565c0;
  transform: translateY(-1px); /* Smaller hover effect */
  box-shadow: 0 3px 8px rgba(25, 118, 210, 0.3);
}

.add-to-cart i {
  font-size: 0.7rem; /* Smaller icon */
}

/* Promotional Banners */
#promotional-banners {
  position: relative;
  margin-bottom: 3rem;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

#banners-container {
  display: flex;
  overflow: hidden;
  height: 350px; /* Slightly smaller */
  position: relative;
}

.banner {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  opacity: 0;
  transition: opacity 0.5s ease;
}

.banner.active {
  opacity: 1;
}

.banner img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.banner-content {
  position: absolute;
  top: 50%;
  left: 10%;
  transform: translateY(-50%);
  background: rgba(0,0,0,0.7);
  padding: 1.5rem; /* Smaller padding */
  border-radius: 8px;
  max-width: 450px; /* Slightly smaller */
  color: var(--primary-white);
}

.banner-content h2 {
  font-size: 2rem; /* Smaller */
  margin: 0 0 0.8rem 0;
  color: var(--primary-white);
}

.banner-content p {
  font-size: 1rem; /* Smaller */
  margin: 0 0 1.2rem 0;
  line-height: 1.4;
}

.banner-btn {
  background: var(--dark-blue);
  color: var(--primary-white);
  padding: 0.6rem 1.2rem; /* Smaller */
  text-decoration: none;
  border-radius: 5px;
  font-weight: 600;
  transition: all 0.3s ease;
  font-size: 0.9rem;
}

.banner-btn:hover {
  background: var(--medium-blue);
  transform: translateY(-1px);
}

/* Banner Controls */
#banner-controls {
  position: absolute;
  bottom: 15px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  gap: 0.8rem;
}

#banner-prev,
#banner-next {
  background: rgba(255,255,255,0.9);
  border: none;
  width: 35px; /* Smaller */
  height: 35px; /* Smaller */
  border-radius: 50%;
  cursor: pointer;
  transition: all 0.3s ease;
  color: var(--primary-black);
  font-weight: bold;
  font-size: 0.8rem;
}

#banner-prev:hover,
#banner-next:hover {
  background: var(--primary-white);
  transform: scale(1.1);
}

/* Image Gallery */
.images-container {
  display: flex;
  overflow-x: auto;
  gap: 1rem;
  padding: 1rem 0;
  scroll-behavior: smooth;
  margin-bottom: 1rem;
}

.images-container::-webkit-scrollbar {
  height: 6px;
}

.images-container::-webkit-scrollbar-track {
  background: var(--light-gray);
  border-radius: 3px;
}

.images-container::-webkit-scrollbar-thumb {
  background: var(--medium-gray);
  border-radius: 3px;
}

.images-container::-webkit-scrollbar-thumb:hover {
  background: var(--dark-gray);
}

.image-wrapper {
  flex: 0 0 auto;
  width: 180px; /* Slightly smaller */
  background: var(--primary-white);
  border-radius: 6px;
  overflow: hidden;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  border: 1px solid var(--medium-gray);
}

.product-image {
  width: 100%;
  height: 130px; /* Smaller */
  object-fit: cover;
}

.image-info {
  padding: 0.6rem;
  font-size: 0.75rem;
  color: var(--text-gray);
}

.primary-badge {
  background: var(--dark-blue);
  color: var(--primary-white);
  padding: 0.15rem 0.4rem;
  border-radius: 3px;
  font-size: 0.65rem;
  margin-left: 0.5rem;
}

.scroll-hint {
  text-align: center;
  color: var(--text-gray);
  font-style: italic;
  margin-bottom: 2rem;
  font-size: 0.9rem;
}

/* Wishlist Notification */
.wishlist-notification {
  position: fixed;
  top: 15px;
  right: 15px;
  padding: 0.8rem 1.2rem;
  border-radius: 5px;
  font-weight: 600;
  z-index: 1000;
  transform: translateX(400px);
  opacity: 0;
  transition: all 0.3s ease;
  max-width: 280px;
  font-size: 0.9rem;
}

.wishlist-notification.show {
  transform: translateX(0);
  opacity: 1;
}

.wishlist-notification.success {
  background: var(--success-green);
  color: var(--primary-white);
  border-left: 3px solid #2e7d32;
}

.wishlist-notification.error {
  background: var(--error-red);
  color: var(--primary-white);
  border-left: 3px solid #c62828;
}

/* Responsive Design for 4-column layout */
@media (max-width: 1200px) {
  .products-grid {
    grid-template-columns: repeat(4, 1fr); /* Keep 4 columns */
    gap: 1.2rem;
  }
  
  .container {
    max-width: 1200px;
    padding: 0 1.5rem;
  }
}

@media (max-width: 1024px) {
  .products-grid {
    grid-template-columns: repeat(3, 1fr); /* 3 columns on tablets */
    gap: 1.5rem;
  }
  
  .container {
    max-width: 1000px;
  }
  
  .image-container {
    height: 180px;
  }
}

@media (max-width: 768px) {
  .products-grid {
    grid-template-columns: repeat(2, 1fr); /* 2 columns on small tablets */
    gap: 1rem;
  }
  
  #categories-list {
    gap: 1rem;
  }
  
  #categories-list a {
    padding: 0.6rem 1.2rem;
    font-size: 0.9rem;
  }
  
  .section-header {
    flex-direction: column;
    gap: 1rem;
    align-items: flex-start;
  }
  
  .image-container {
    height: 160px;
  }
  
  .info {
    padding: 0.8rem;
  }
  
  .product-actions {
    flex-direction: column;
    gap: 0.4rem;
  }
}

@media (max-width: 480px) {
  .products-grid {
    grid-template-columns: 1fr; /* 1 column on mobile */
    gap: 1rem;
  }
  
  .container {
    padding: 0 0.5rem;
  }
  
  #categories-list {
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
  }
  
  .image-container {
    height: 140px;
  }
  
  .wishlist-notification {
    right: 10px;
    left: 10px;
    max-width: none;
  }
  
  .product-actions {
    flex-direction: row; /* Keep buttons side by side on mobile */
  }
}

/* Animation for cards */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.card {
  animation: fadeInUp 0.6s ease-out;
}

.card:nth-child(1) { animation-delay: 0.1s; }
.card:nth-child(2) { animation-delay: 0.2s; }
.card:nth-child(3) { animation-delay: 0.3s; }
.card:nth-child(4) { animation-delay: 0.4s; }
  </style>
</head>
<body>

  <?php include '../header1.php'; ?>

  <nav aria-label="Categories">
    <h2 style="text-align: center;">Categories</h2>
    <div class="container">
      <ul id="categories-list" style="background-color: darkgrey;">
        <?php foreach ($categories as $category): ?>
          <li>
            <a href="../productlist/productlist.php?category=<?php echo urlencode($category['slug']); ?>">
              <?php echo htmlspecialchars($category['name']); ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </nav>

  <main class="container">
    
    <?php if (!empty($search_results)): ?>
      <!-- Search Results Section -->
      <section id="search-results" aria-labelledby="search-results-heading">
        <div class="section-header">
          <h2 id="search-results-heading">Search Results for "<?php echo htmlspecialchars($_GET['q']); ?>"</h2>
          <p><?php echo count($search_results); ?> products found</p>
        </div>
        <div class="products-grid" role="list">
          <?php foreach ($search_results as $product): ?>
            <article class="card" role="listitem">
              <div class="image-container">
                <img class="image" src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" width="200" height="200">
                
                <!-- Wishlist Heart Button -->
                                
                <?php if ($product['is_featured']): ?>
                  <span class="badge">Featured</span>
                <?php endif; ?>
              </div>
              <div class="info">
                <!-- Wishlist Button as Form -->
                   <form method="POST" action="index.php" class="wishlist-form">
                    <input type="hidden" name="action" value="add_to_wishlist">
                    <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <button type="submit" class="wishlist-btn" title="Add to Wishlist">
                    <i class="fas fa-heart"></i>
                  </button>
               </form>

                <h3 class="name"><?php echo htmlspecialchars($product['name']); ?></h3>
                <p class="category"><?php echo htmlspecialchars($product['category_name']); ?></p>
                <div class="rating">
                  <?php echo generateStarRating($product['rating']); ?>
                </div>
                <p class="price">
                  <?php echo formatPrice($product['price'], $product['original_price']); ?>
                </p>
                <div class="product-actions">
                  <a href="../productdetails/productdetails.php?id=<?php echo $product['id']; ?>" class="view-details">
                    View Details
                  </a>
                  <form method="POST" action="../cart/cart_backend.php" class="add-to-cart-form">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="add-to-cart">
                      <i class="fas fa-cart-plus"></i> Add to cart
                    </button>
                  </form>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </section>
    <?php else: ?>

      <div class="container">
        <h2>Product Images Gallery</h2>
        
        <?php if (empty($productImages)): ?>
            <p>No images found.</p>
        <?php else: ?>
            <div class="images-container" id="imagesScroll">
                <?php foreach ($productImages as $image): ?>
                    <div class="image-wrapper">
                        <img 
                            src="<?php echo htmlspecialchars($image['image_url']); ?>" 
                            alt="<?php echo htmlspecialchars($image['alt_text'] ?? 'Product Image'); ?>" 
                            class="product-image"
                            onerror="this.src='https://via.placeholder.com/200x150?text=Image+Not+Found'"
                        >
                        <!-- <div class="image-info">
                            <strong>Product ID: <?php echo $image['product_id']; ?></strong>
                            <?php if ($image['is_primary']): ?>
                                <span class="primary-badge">Primary</span>
                            <?php endif; ?>
                            <br>
                            <small>Order: <?php echo $image['sort_order']; ?></small>
                        </div> -->
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="scroll-hint">← Scroll horizontally to view more images →</div>
        <?php endif; ?>
    </div>

    <script>
        // Optional: Auto-scroll functionality
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('imagesScroll');
            let scrollAmount = 0;
            const scrollStep = 2;
            
            // Auto-scroll from right to left
            function autoScroll() {
                if (container.scrollLeft <= 0) {
                    // Reached the left end, reset to right
                    container.scrollLeft = container.scrollWidth;
                } else {
                    container.scrollLeft -= scrollStep;
                }
            }
            
            // Uncomment below to enable auto-scroll
            // setInterval(autoScroll, 50);
            
            // Pause auto-scroll on hover
            container.addEventListener('mouseenter', function() {
                // Clear interval if auto-scroll is enabled
                // clearInterval(scrollInterval);
            });
            
            container.addEventListener('mouseleave', function() {
                // Restart auto-scroll if enabled
                // scrollInterval = setInterval(autoScroll, 50);
            });
        });
    </script>

      
      <!-- Normal Homepage Content -->
      <section id="promotional-banners" aria-label="Promotional banners">
        <div id="banners-container">
          <?php foreach ($banners as $banner): ?>
            <div class="banner">
              <img src="<?php echo htmlspecialchars($banner['image_url']); ?>" alt="<?php echo htmlspecialchars($banner['title']); ?>">
              <div class="banner-content">
                <h2><?php echo htmlspecialchars($banner['title']); ?></h2>
                <p><?php echo htmlspecialchars($banner['description']); ?></p>
                <?php if ($banner['button_text'] && $banner['button_url']): ?>
                  <a href="<?php echo htmlspecialchars($banner['button_url']); ?>" class="banner-btn">
                    <?php echo htmlspecialchars($banner['button_text']); ?>
                  </a>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        <div id="banner-controls">
          <button id="banner-prev" aria-label="Previous banner">◀</button>
          <button id="banner-next" aria-label="Next banner">▶</button>
        </div>
      </section>

      <section id="featured" aria-labelledby="featured-heading" >
        <div class="section-header">
          <h2 id="featured-heading">Featured products</h2>
          <a href="../productlist/productlist.php" class="view-all">View all</a>
        </div>
        <div id="featured-grid" class="products-grid" role="list" >
          <?php foreach ($featured_products as $product): ?>
            <article class="card" role="listitem" >
              <div class="image-container">
                <img class="image" src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" width="200" height="200">
                
                <!-- Wishlist Heart Button -->
               <!-- Wishlist Button as Form -->
                   <form method="POST" action="index.php" class="wishlist-form">
                    <input type="hidden" name="action" value="add_to_wishlist">
                    <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <button type="submit" class="wishlist-btn" title="Add to Wishlist">
                    <i class="fas fa-heart"></i>
                  </button>
               </form>
    
                <span class="badge">Featured</span>
              </div>
              <div class="info">
                <h3 class="name"><?php echo htmlspecialchars($product['name']); ?></h3>
                <p class="category"><?php echo htmlspecialchars($product['category_name']); ?></p>
                <div class="rating">
                  <?php echo generateStarRating($product['rating']); ?>
                </div>
                <p class="price">
                  <?php echo formatPrice($product['price'], $product['original_price']); ?>
                </p>
                <div class="product-actions">
                  <a href="../productdetails/productdetails.php?id=<?php echo $product['id']; ?>" class="view-details">
                    View Details
                  </a>
                  <form method="POST" action="../cart/cart_backend.php" class="add-to-cart-form">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="add-to-cart">
                      <i class="fas fa-cart-plus"></i> Add to cart
                    </button>
                  </form>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </section>

      <section id="all-products" aria-labelledby="all-products-heading">
        <div class="section-header">
          <h2 id="all-products-heading">All products</h2>
          <a href="../productlist/productlist.php" class="view-all">View all</a>
        </div>
        <div id="products-grid" class="products-grid" role="list">
          <?php foreach ($all_products as $product): ?>
            <article class="card" role="listitem">
              <div class="image-container">
                <img class="image" src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" width="200" height="200">
                
                <!-- Wishlist Heart Button -->
               <!-- Wishlist Button as Form -->
                   <form method="POST" action="index.php" class="wishlist-form">
                    <input type="hidden" name="action" value="add_to_wishlist">
                    <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <button type="submit" class="wishlist-btn" title="Add to Wishlist">
                    <i class="fas fa-heart"></i>
                  </button>
               </form>

                
                <?php if ($product['is_featured']): ?>
                  <span class="badge">Featured</span>
                <?php endif; ?>
              </div>
              <div class="info">
                <h3 class="name"><?php echo htmlspecialchars($product['name']); ?></h3>
                <p class="category"><?php echo htmlspecialchars($product['category_name']); ?></p>
                <div class="rating">
                  <?php echo generateStarRating($product['rating']); ?>
                </div>
                <p class="price">
                  <?php echo formatPrice($product['price'], $product['original_price']); ?>
                </p>
                <div class="product-actions">
                  <a href="../productdetails/productdetails.php?id=<?php echo $product['id']; ?>" class="view-details">
                    View Details
                  </a>
                  <form method="POST" action="../cart/cart_backend.php" class="add-to-cart-form">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="add-to-cart">
                      <i class="fas fa-cart-plus"></i> Add to cart
                    </button>
                  </form>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endif; ?>
  </main>
   
  <?php include '../footer.php'; ?>

  <!-- Wishlist Notification -->
  <div id="wishlist-notification" class="wishlist-notification"></div>

  <script>
    // Wishlist functionality
    document.addEventListener('DOMContentLoaded', function() {
      const wishlistButtons = document.querySelectorAll('.wishlist-btn');
      const notification = document.getElementById('wishlist-notification');
      
      wishlistButtons.forEach(button => {
        button.addEventListener('click', function() {
          const productId = this.getAttribute('data-product-id');
          const heartIcon = this.querySelector('.fa-heart');
          
          // Send AJAX request to add to wishlist
          const xhr = new XMLHttpRequest();
          xhr.open('POST', 'wishlist_handler.php', true);
          xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
          
          xhr.onload = function() {
            if (xhr.status === 200) {
              const response = JSON.parse(xhr.responseText);
              
              if (response.success) {
                // Show success notification
                showNotification(response.message, 'success');
                
                // Change heart color
                heartIcon.style.color = '#e91e63';
                button.classList.add('added');
                
                // Redirect to wishlist page after 1.5 seconds
                setTimeout(() => {
                  window.location.href = '../user/wishlist/wishlist.php';
                }, 1500);
              } else {
                // Show error notification
                showNotification(response.message, 'error');
                
                if (response.redirect) {
                  // Redirect to login page if not logged in
                  setTimeout(() => {
                    window.location.href = response.redirect;
                  }, 1500);
                }
              }
            }
          };
          
          xhr.send('action=add_to_wishlist&product_id=' + productId);
        });
      });
      
      function showNotification(message, type) {
        notification.textContent = message;
        notification.className = 'wishlist-notification ' + type + ' show';
        
        setTimeout(() => {
          notification.classList.remove('show');
        }, 3000);
      }
    });
  </script>
</body>
</html>