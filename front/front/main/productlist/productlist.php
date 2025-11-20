<?php
// Include necessary files
include '../../../databse/db_connection.php';
include 'productlist_backend.php';

// Start session for wishlist functionality
session_start();

// Initialize variables
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 12;

// Get filter parameters
$filters = array(
    'category' => isset($_GET['category']) ? $_GET['category'] : 'all',
    'min_price' => isset($_GET['min_price']) ? $_GET['min_price'] : '',
    'max_price' => isset($_GET['max_price']) ? $_GET['max_price'] : '',
    'sort' => isset($_GET['sort']) ? $_GET['sort'] : 'name',
    'search' => isset($_GET['search']) ? $_GET['search'] : ''
);

// Apply filters if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $filters['category'] = $_POST['category'] ?? 'all';
    $filters['min_price'] = $_POST['min_price'] ?? '';
    $filters['max_price'] = $_POST['max_price'] ?? '';
    $filters['sort'] = $_POST['sort'] ?? 'name';
    
    // Build query string for redirect
    $query_params = http_build_query(array_filter($filters));
    header('Location: productlist.php?' . $query_params);
    exit;
}

// Get data
$categories = getCategoriesForFilter($conn);
$price_range = getPriceRange($conn, $filters);
$result = getFilteredProducts($conn, $filters, $page, $per_page);

$products = $result['products'];
$total_count = $result['total_count'];
$total_pages = $result['total_pages'];

// Build base URL for pagination
$base_url = 'productlist.php?' . http_build_query(array_filter($filters));

// Check if user is logged in for wishlist functionality
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="../style1.css">
  <link rel="stylesheet" href="productlist.css">
  <title>Online Shop — Products</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    /* Wishlist heart button styles */
    .wishlist-btn {
      position: absolute;
      top: 10px;
      right: 10px;
      background: rgba(255, 255, 255, 0.9);
      border: none;
      border-radius: 50%;
      width: 36px;
      height: 36px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.3s ease;
      z-index: 10;
    }
    
    .wishlist-btn:hover {
      background: white;
      transform: scale(1.1);
    }
    
    .wishlist-btn .fa-heart {
      color: #ccc;
      font-size: 18px;
      transition: all 0.3s ease;
    }
    
    .wishlist-btn:hover .fa-heart,
    .wishlist-btn.active .fa-heart {
      color: #e91e63;
    }
    
    .wishlist-btn.added .fa-heart {
      color: #e91e63;
    }
    
    .image-container {
      position: relative;
    }
    
    /* Notification styles */
    .wishlist-notification {
      position: fixed;
      top: 20px;
      right: 20px;
      padding: 1rem 1.5rem;
      border-radius: 4px;
      color: white;
      font-weight: 600;
      z-index: 1000;
      opacity: 0;
      transform: translateX(100%);
      transition: all 0.3s;
    }
    
    .wishlist-notification.show {
      opacity: 1;
      transform: translateX(0);
    }
    
    .wishlist-notification.success {
      background: #28a745;
    }
    
    .wishlist-notification.error {
      background: #dc3545;
    }
  </style>
</head>
<body>
  
  <?php include '../header1.php'; ?>
<h1 style="text-align: center;">Product Catalog</h1>
  <main>
    <aside id="filters">
      <h2>Filter Options</h2>
      <form id="filter-form" method="POST" action="">
        <fieldset>
          <legend>Category</legend>
          <select id="filter-category" name="category">
            <option value="all" <?php echo $filters['category'] == 'all' ? 'selected' : ''; ?>>All Categories</option>
            <?php foreach ($categories as $category): ?>
              <option value="<?php echo htmlspecialchars($category['slug']); ?>" 
                      <?php echo $filters['category'] == $category['slug'] ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($category['name']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </fieldset>

        <fieldset>
          <legend>Price Range</legend>
          <label>
            Min: <input type="number" id="filter-min-price" name="min_price" 
                       placeholder="0" step="0.01" min="0" 
                       value="<?php echo htmlspecialchars($filters['min_price']); ?>">
          </label>
          <label>
            Max: <input type="number" id="filter-max-price" name="max_price" 
                       placeholder="<?php echo number_format($price_range['max_price'], 2); ?>" 
                       step="0.01" min="0" 
                       value="<?php echo htmlspecialchars($filters['max_price']); ?>">
          </label>
        </fieldset>

        <fieldset>
          <legend>Sort By</legend>
          <select name="sort">
            <option value="name" <?php echo $filters['sort'] == 'name' ? 'selected' : ''; ?>>Name</option>
            <option value="price-asc" <?php echo $filters['sort'] == 'price-asc' ? 'selected' : ''; ?>>Price: Low to High</option>
            <option value="price-desc" <?php echo $filters['sort'] == 'price-desc' ? 'selected' : ''; ?>>Price: High to Low</option>
            <option value="rating" <?php echo $filters['sort'] == 'rating' ? 'selected' : ''; ?>>Highest Rated</option>
            <option value="newest" <?php echo $filters['sort'] == 'newest' ? 'selected' : ''; ?>>Newest First</option>
          </select>
        </fieldset>

        <button type="submit">Apply Filters</button>
        <?php if ($filters['category'] != 'all' || $filters['min_price'] || $filters['max_price']): ?>
          <a href="productlist.php" class="clear-filters">Clear Filters</a>
        <?php endif; ?>
      </form>
    </aside>

    <section id="products-section">
      <header>
        <h2>Products 
          <?php if ($filters['category'] != 'all'): ?>
            <span class="category-badge"><?php 
              $current_category = array_filter($categories, function($cat) use ($filters) {
                return $cat['slug'] == $filters['category'];
              });
              if (!empty($current_category)) {
                echo htmlspecialchars(current($current_category)['name']);
              }
            ?></span>
          <?php endif; ?>
        </h2>
        
        <div class="results-info">
          <p>Showing <?php echo count($products); ?> of <?php echo $total_count; ?> products</p>
        </div>

        <div class="view-controls">
          <label for="sort-options">Sort by:</label>
          <select id="sort-options" onchange="updateSort(this.value)">
            <option value="name" <?php echo $filters['sort'] == 'name' ? 'selected' : ''; ?>>Name</option>
            <option value="price-asc" <?php echo $filters['sort'] == 'price-asc' ? 'selected' : ''; ?>>Price: Low to High</option>
            <option value="price-desc" <?php echo $filters['sort'] == 'price-desc' ? 'selected' : ''; ?>>Price: High to Low</option>
            <option value="rating" <?php echo $filters['sort'] == 'rating' ? 'selected' : ''; ?>>Highest Rated</option>
            <option value="newest" <?php echo $filters['sort'] == 'newest' ? 'selected' : ''; ?>>Newest First</option>
          </select>

          <label for="view-options">View:</label>
          <select id="view-options" onchange="toggleView(this.value)">
            <option value="grid">Grid</option>
            <option value="list">List</option>
          </select>
        </div>
      </header>

      <div id="products-container" class="products-grid" role="list">
        <?php if (empty($products)): ?>
          <div class="no-products">
            <p>No products found matching your criteria.</p>
            <a href="productlist.php" class="btn btn-primary">View All Products</a>
          </div>
        <?php else: ?>
          <?php foreach ($products as $product): ?>
            <article class="card" role="listitem">
              <div class="image-container">
                <img class="image" src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>" width="150" height="150">
                
                <!-- Wishlist Heart Button -->
                <button class="wishlist-btn" data-product-id="<?php echo $product['id']; ?>" title="Add to Wishlist">
                  <i class="fas fa-heart"></i>
                </button>
                
                <?php if ($product['is_featured']): ?>
                  <span class="badge">Featured</span>
                <?php endif; ?>
                <?php if ($product['original_price'] && $product['original_price'] > $product['price']): ?>
                  <span class="sale-badge">Sale</span>
                <?php endif; ?>
              </div>
              <div class="details">
                <h3 class="name">
                  <a href="../productdetails/productdetails.php?id=<?php echo $product['id']; ?>">
                    <?php echo htmlspecialchars($product['name']); ?>
                  </a>
                </h3>
                <p class="category"><?php echo htmlspecialchars($product['category_name']); ?></p>
                <div class="rating">
                  <?php 
                  $rating = $product['rating'];
                  $full_stars = floor($rating);
                  $half_star = ($rating - $full_stars) >= 0.5;
                  $empty_stars = 5 - $full_stars - ($half_star ? 1 : 0);
                  
                  for ($i = 0; $i < $full_stars; $i++) {
                      echo '<i class="fas fa-star"></i>';
                  }
                  if ($half_star) {
                      echo '<i class="fas fa-star-half-alt"></i>';
                  }
                  for ($i = 0; $i < $empty_stars; $i++) {
                      echo '<i class="far fa-star"></i>';
                  }
                  ?>
                  <span class="review-count">(<?php echo $product['review_count']; ?>)</span>
                </div>
                <p class="price">
                  <?php echo formatPriceDisplay($product['price'], $product['original_price']); ?>
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
        <?php endif; ?>
      </div>

      <?php if ($total_pages > 1): ?>
        <nav id="pagination" aria-label="Product pages">
          <?php echo generatePagination($page, $total_pages, $base_url); ?>
        </nav>
      <?php endif; ?>
    </section>
  </main>

  <!-- Wishlist Notification -->
  <div id="wishlist-notification" class="wishlist-notification"></div>

  <script>
    function updateSort(sortValue) {
      const url = new URL(window.location);
      url.searchParams.set('sort', sortValue);
      window.location.href = url.toString();
    }

    function toggleView(viewValue) {
      const container = document.getElementById('products-container');
      if (viewValue === 'list') {
        container.classList.add('list-view');
        container.classList.remove('grid-view');
      } else {
        container.classList.add('grid-view');
        container.classList.remove('list-view');
      }
    }

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
          xhr.open('POST', '../index/wishlist_handler.php', true);
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

      // Set initial view based on current selection
      const viewOptions = document.getElementById('view-options');
      if (viewOptions) {
        toggleView(viewOptions.value);
      }

      // Update price placeholders
      const minPriceInput = document.getElementById('filter-min-price');
      const maxPriceInput = document.getElementById('filter-max-price');
      
      if (minPriceInput && !minPriceInput.value) {
        minPriceInput.placeholder = '0';
      }
      
      if (maxPriceInput && !maxPriceInput.value) {
        maxPriceInput.placeholder = '<?php echo number_format($price_range["max_price"], 2); ?>';
      }
    });
  </script>
</body>
</html>