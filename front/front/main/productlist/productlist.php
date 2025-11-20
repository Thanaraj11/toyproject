<?php
// Include necessary files
include '../../../databse/db_connection.php';
include 'productlist_backend.php';

// Start session for wishlist functionality
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


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
  <!-- <link rel="stylesheet" href="../style1.css">
  <link rel="stylesheet" href="productlist.css"> -->
  <title>Online Shop — Products</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    /* Product List Specific Styles */
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
  --star-color: #ff9800;
  --sale-red: #f44336;
}

/* Main Layout */
body {
  background-color: var(--light-gray);
  color: var(--primary-black);
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  line-height: 1.6;
  margin: 0;
  padding: 0;
}

/* Page Header */
h1 {
  text-align: center;
  color: var(--primary-black);
  font-size: 2.5rem;
  font-weight: 700;
  margin: 2rem 0;
  padding: 0 1rem;
}

/* Main Content Layout */
main {
  display: grid;
  grid-template-columns: 280px 1fr;
  gap: 2rem;
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 1rem 2rem;
}

/* Filters Sidebar */
#filters {
  background: var(--primary-white);
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  height: fit-content;
  position: sticky;
  top: 2rem;
}

#filters h2 {
  color: var(--primary-black);
  font-size: 1.3rem;
  font-weight: 600;
  margin-bottom: 1.5rem;
  border-bottom: 2px solid var(--light-blue);
  padding-bottom: 0.5rem;
}

/* Filter Form */
#filter-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

#filter-form fieldset {
  border: 1px solid var(--medium-gray);
  border-radius: 8px;
  padding: 1rem;
  margin: 0;
}

#filter-form legend {
  color: var(--dark-gray);
  font-weight: 600;
  padding: 0 0.5rem;
  font-size: 0.9rem;
}

#filter-form select,
#filter-form input {
  width: 100%;
  padding: 0.75rem;
  border: 2px solid var(--medium-gray);
  border-radius: 6px;
  font-size: 0.9rem;
  background: var(--primary-white);
  color: var(--primary-black);
  transition: border-color 0.3s ease;
  margin-top: 0.5rem;
}

#filter-form select:focus,
#filter-form input:focus {
  outline: none;
  border-color: var(--dark-blue);
}

#filter-form label {
  display: block;
  margin-bottom: 0.75rem;
  color: var(--dark-gray);
  font-weight: 500;
}

#filter-form button[type="submit"] {
  background: var(--dark-blue);
  color: var(--primary-white);
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 6px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  width: 100%;
}

#filter-form button[type="submit"]:hover {
  background: #1565c0;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(25, 118, 210, 0.3);
}

.clear-filters {
  display: block;
  text-align: center;
  color: var(--text-gray);
  text-decoration: none;
  padding: 0.5rem;
  border: 1px solid var(--medium-gray);
  border-radius: 6px;
  transition: all 0.3s ease;
  margin-top: 0.5rem;
}

.clear-filters:hover {
  background: var(--light-gray);
  color: var(--primary-black);
}

/* Products Section */
#products-section {
  background: var(--primary-white);
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

#products-section header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  flex-wrap: wrap;
  gap: 1rem;
}

#products-section h2 {
  color: var(--primary-black);
  font-size: 1.5rem;
  font-weight: 600;
  margin: 0;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.category-badge {
  background: var(--light-blue);
  color: var(--dark-blue);
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
}

.results-info {
  color: var(--text-gray);
  font-size: 0.9rem;
}

.view-controls {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex-wrap: wrap;
}

.view-controls label {
  color: var(--dark-gray);
  font-weight: 500;
  font-size: 0.9rem;
}

.view-controls select {
  padding: 0.5rem;
  border: 1px solid var(--medium-gray);
  border-radius: 4px;
  background: var(--primary-white);
  color: var(--primary-black);
  font-size: 0.9rem;
}

/* Products Grid View */
.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

/* Product Cards */
.card {
  background: var(--primary-white);
  border: 1px solid var(--medium-gray);
  border-radius: 10px;
  overflow: hidden;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 20px rgba(0,0,0,0.15);
  border-color: var(--medium-blue);
}

.image-container {
  position: relative;
  background: var(--light-gray);
  height: 200px;
  overflow: hidden;
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

/* Wishlist Button */
.wishlist-btn {
  position: absolute;
  top: 10px;
  right: 10px;
  background: var(--primary-white);
  border: none;
  border-radius: 50%;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 2px 6px rgba(0,0,0,0.2);
  color: var(--text-gray);
  font-size: 0.9rem;
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

/* Badges */
.badge {
  position: absolute;
  top: 10px;
  left: 10px;
  background: var(--featured-orange);
  color: var(--primary-white);
  padding: 0.3rem 0.7rem;
  border-radius: 15px;
  font-size: 0.7rem;
  font-weight: 600;
  text-transform: uppercase;
}

.sale-badge {
  position: absolute;
  top: 45px;
  left: 10px;
  background: var(--sale-red);
  color: var(--primary-white);
  padding: 0.3rem 0.7rem;
  border-radius: 15px;
  font-size: 0.7rem;
  font-weight: 600;
  text-transform: uppercase;
}

/* Product Details */
.details {
  padding: 1.25rem;
}

.name {
  margin: 0 0 0.5rem 0;
}

.name a {
  color: var(--primary-black);
  text-decoration: none;
  font-size: 1.1rem;
  font-weight: 600;
  line-height: 1.3;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.name a:hover {
  color: var(--dark-blue);
}

.category {
  color: var(--text-gray);
  font-size: 0.8rem;
  margin: 0 0 0.75rem 0;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.rating {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.75rem;
  color: var(--star-color);
  font-size: 0.8rem;
}

.review-count {
  color: var(--text-gray);
  font-size: 0.75rem;
}

.price {
  color: var(--dark-blue);
  font-size: 1.3rem;
  font-weight: 700;
  margin: 0 0 1rem 0;
}

.original-price {
  color: var(--text-gray);
  text-decoration: line-through;
  font-size: 1rem;
  margin-left: 0.5rem;
  font-weight: 400;
}

.discount {
  color: var(--sale-red);
  font-size: 0.8rem;
  font-weight: 600;
  margin-left: 0.5rem;
}

/* Product Actions */
.product-actions {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.view-details {
  flex: 1;
  background: var(--primary-black);
  color: var(--primary-white);
  padding: 0.6rem 1rem;
  text-decoration: none;
  border-radius: 6px;
  text-align: center;
  font-weight: 600;
  transition: all 0.3s ease;
  font-size: 0.85rem;
  min-width: 100px;
}

.view-details:hover {
  background: var(--dark-gray);
  transform: translateY(-1px);
}

.add-to-cart-form {
  flex: 1;
  min-width: 120px;
}

.add-to-cart {
  width: 100%;
  background: var(--dark-blue);
  color: var(--primary-white);
  border: none;
  padding: 0.6rem 1rem;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.4rem;
  font-size: 0.85rem;
}

.add-to-cart:hover {
  background: #1565c0;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(25, 118, 210, 0.3);
}

.add-to-cart i {
  font-size: 0.8rem;
}

/* List View */
.products-grid.list-view {
  grid-template-columns: 1fr;
}

.products-grid.list-view .card {
  display: grid;
  grid-template-columns: 200px 1fr;
  gap: 1.5rem;
}

.products-grid.list-view .image-container {
  height: 180px;
}

.products-grid.list-view .details {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  padding: 1.5rem 1.5rem 1.5rem 0;
}

/* No Products */
.no-products {
  grid-column: 1 / -1;
  text-align: center;
  padding: 3rem 2rem;
  background: var(--light-gray);
  border-radius: 10px;
  border: 2px dashed var(--medium-gray);
}

.no-products p {
  color: var(--text-gray);
  font-size: 1.1rem;
  margin-bottom: 1.5rem;
}

.btn-primary {
  background: var(--dark-blue);
  color: var(--primary-white);
  padding: 0.75rem 1.5rem;
  text-decoration: none;
  border-radius: 6px;
  font-weight: 600;
  transition: all 0.3s ease;
  display: inline-block;
}

.btn-primary:hover {
  background: #1565c0;
  transform: translateY(-2px);
}

/* Pagination */
#pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 0.5rem;
  margin-top: 2rem;
  flex-wrap: wrap;
}

.pagination-link,
.pagination-current {
  padding: 0.5rem 0.75rem;
  border-radius: 6px;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.3s ease;
  min-width: 40px;
  text-align: center;
}

.pagination-link {
  background: var(--light-gray);
  color: var(--dark-gray);
  border: 1px solid var(--medium-gray);
}

.pagination-link:hover {
  background: var(--medium-blue);
  color: var(--primary-white);
  border-color: var(--dark-blue);
}

.pagination-current {
  background: var(--dark-blue);
  color: var(--primary-white);
  border: 1px solid var(--dark-blue);
}

.pagination-ellipsis {
  color: var(--text-gray);
  padding: 0.5rem;
}

/* Wishlist Notification */
.wishlist-notification {
  position: fixed;
  top: 20px;
  right: 20px;
  padding: 1rem 1.5rem;
  border-radius: 6px;
  font-weight: 600;
  z-index: 1000;
  transform: translateX(400px);
  opacity: 0;
  transition: all 0.3s ease;
  max-width: 300px;
}

.wishlist-notification.show {
  transform: translateX(0);
  opacity: 1;
}

.wishlist-notification.success {
  background: var(--success-green);
  color: var(--primary-white);
  border-left: 4px solid #2e7d32;
}

.wishlist-notification.error {
  background: var(--error-red);
  color: var(--primary-white);
  border-left: 4px solid #c62828;
}

/* Products Header */
.products-header {
    margin-bottom: 20px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #dee2e6;
}

.products-header h2 {
    margin: 0 0 10px 0;
    font-size: 1.5rem;
    color: #333;
    display: flex;
    align-items: center;
    gap: 10px;
}

.category-badge {
    background: #007bff;
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.results-info {
    margin-bottom: 15px;
    color: #666;
    font-size: 0.9rem;
}

.results-info p {
    margin: 0;
}

.view-controls {
    display: flex;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
}

.view-controls label {
    color: #555;
    font-weight: 500;
    font-size: 0.9rem;
}

.view-controls select {
    padding: 6px 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    background: white;
    color: #333;
    font-size: 0.9rem;
    min-width: 140px;
}

.view-controls select:focus {
    outline: none;
    border-color: #007bff;
}

/* Responsive */
@media (max-width: 768px) {
    .products-header {
        padding: 12px;
    }
    
    .products-header h2 {
        font-size: 1.3rem;
    }
    
    .view-controls {
        gap: 10px;
    }
    
    .view-controls select {
        min-width: 120px;
    }
}

@media (max-width: 480px) {
    .view-controls {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    
    .view-controls select {
        width: 100%;
    }
}


/* Responsive Design */
@media (max-width: 1024px) {
  main {
    grid-template-columns: 250px 1fr;
    gap: 1.5rem;
  }
  
  .products-grid {
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  }
}

@media (max-width: 768px) {
  main {
    grid-template-columns: 1fr;
    gap: 1.5rem;
    padding: 0 0.5rem 1rem;
  }
  
  #filters {
    position: static;
  }
  
  #products-section header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .view-controls {
    width: 100%;
    justify-content: space-between;
  }
  
  .products-grid.list-view .card {
    grid-template-columns: 150px 1fr;
  }
  
  .products-grid.list-view .image-container {
    height: 150px;
  }
}

@media (max-width: 480px) {
  h1 {
    font-size: 2rem;
    margin: 1.5rem 0;
  }
  
  #filters,
  #products-section {
    padding: 1rem;
  }
  
  .products-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  .products-grid.list-view .card {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  .products-grid.list-view .details {
    padding: 1rem;
  }
  
  .product-actions {
    flex-direction: column;
  }
  
  .view-controls {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }
  
  #pagination {
    gap: 0.25rem;
  }
  
  .pagination-link,
  .pagination-current {
    padding: 0.4rem 0.6rem;
    min-width: 35px;
    font-size: 0.9rem;
  }
}

/* Focus styles for accessibility */
button:focus,
input:focus,
select:focus,
a:focus {
  outline: 2px solid var(--dark-blue);
  outline-offset: 2px;
}

/* Loading state */
.card.loading {
  opacity: 0.6;
  pointer-events: none;
}

/* Print styles */
@media print {
  #filters,
  .wishlist-btn,
  .add-to-cart-form,
  .view-controls {
    display: none !important;
  }
  
  main {
    grid-template-columns: 1fr;
  }
  
  .card {
    break-inside: avoid;
    border: 1px solid var(--primary-black);
  }
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
      <div class="products-header">
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
</div>

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