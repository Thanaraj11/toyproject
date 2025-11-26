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
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


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

 <link rel="stylesheet" href="index.css">
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="index.js"></script>
  
       
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
                    View <br>Details
                  </a>
                  <form method="POST" action="../cart/cart_backend.php" class="add-to-cart-form">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="add-to-cart">
                      <i class="fas fa-cart-plus"></i><br> Add to cart
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
                    View 
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
                    View 
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