<?php
// Include backend functions
require_once 'productdetails_backend.php';

// Get product ID from URL parameter
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get product details
$product = getProductDetails($product_id);
$images = getProductImages($product_id);
$reviews = getProductReviews($product_id);

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $customer_id = 1; // You'll need to get this from session
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];
    $reviewer_name = $_POST['reviewer_name'];
    
    if (addProductReview($product_id, $customer_id, $rating, $comment, $reviewer_name)) {
        $review_success = "Review submitted successfully! It will be visible after approval.";
        // Refresh reviews
        $reviews = getProductReviews($product_id);
    } else {
        $review_error = "Failed to submit review. Please try again.";
    }
}

// If product not found, show error
if (!$product) {
    die("Product not found!");
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <!-- <link rel="stylesheet" href="../style1.css">
  <link rel="stylesheet" href="productdetails.css"> -->
  <title>Product Detail - <?php echo htmlspecialchars($product['name']); ?></title>
  <style>
    /* Product Details Specific Styles */
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

/* Header */
header {
  background-color: var(--primary-black);
  color: var(--primary-white);
  padding: 1rem 2rem;
  text-align: center;
}

header h1 {
  margin: 0;
  font-size: 1.8rem;
  font-weight: 600;
}

/* Main Content */
main {
  max-width: 1200px;
  margin: 2rem auto;
  padding: 0 1rem;
}

/* Product Info Section */
#product-info {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 3rem;
  margin-bottom: 3rem;
  background: var(--primary-white);
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

/* Image Gallery */
#gallery {
  position: relative;
}

#main-img {
  width: 100%;
  height: 400px;
  object-fit: cover;
  border-radius: 8px;
  border: 2px solid var(--medium-gray);
  margin-bottom: 1rem;
}

#gallery-buttons {
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  display: flex;
  justify-content: space-between;
  padding: 0 1rem;
  transform: translateY(-50%);
}

#prev-img,
#next-img {
  background: rgba(255, 255, 255, 0.9);
  border: none;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  cursor: pointer;
  font-size: 1.2rem;
  color: var(--primary-black);
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

#prev-img:hover,
#next-img:hover {
  background: var(--primary-white);
  transform: scale(1.1);
  color: var(--dark-blue);
}

/* Thumbnails */
#thumbnails {
  display: flex;
  gap: 0.5rem;
  justify-content: center;
  flex-wrap: wrap;
}

.thumbnail {
  width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: 6px;
  cursor: pointer;
  border: 2px solid var(--medium-gray);
  transition: all 0.3s ease;
  opacity: 0.7;
}

.thumbnail:hover {
  opacity: 1;
  border-color: var(--dark-blue);
  transform: scale(1.05);
}

.thumbnail.active {
  opacity: 1;
  border-color: var(--dark-blue);
  transform: scale(1.1);
}

/* Product Details */
#details {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

#product-title {
  color: var(--primary-black);
  font-size: 2rem;
  font-weight: 700;
  margin: 0;
  line-height: 1.2;
}

#product-price {
  color: var(--dark-blue);
  font-size: 2rem;
  font-weight: 700;
  margin: 0;
}

#product-description {
  color: var(--dark-gray);
  font-size: 1.1rem;
  line-height: 1.6;
  margin: 0;
}

/* Variants Form */
#variants-form {
  display: flex;
  gap: 1.5rem;
  flex-wrap: wrap;
}

#variants-form label {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  font-weight: 600;
  color: var(--dark-gray);
}

#variant-size,
#variant-color {
  padding: 0.75rem;
  border: 2px solid var(--medium-gray);
  border-radius: 6px;
  font-size: 1rem;
  background: var(--primary-white);
  color: var(--primary-black);
  transition: border-color 0.3s ease;
  min-width: 120px;
}

#variant-size:focus,
#variant-color:focus {
  outline: none;
  border-color: var(--dark-blue);
}

/* Add to Cart Button */
#add-to-cart {
  background: var(--dark-blue);
  color: var(--primary-white);
  border: none;
  padding: 1rem 2rem;
  border-radius: 8px;
  font-size: 1.1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  width: 100%;
  max-width: 300px;
}

#add-to-cart:hover {
  background: #1565c0;
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(25, 118, 210, 0.3);
}

/* Reviews Section */
#reviews {
  background: var(--primary-white);
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

#reviews h2 {
  color: var(--primary-black);
  font-size: 1.5rem;
  font-weight: 600;
  margin-bottom: 1.5rem;
  border-bottom: 2px solid var(--light-blue);
  padding-bottom: 0.5rem;
}

/* Reviews List */
#reviews-list {
  list-style: none;
  padding: 0;
  margin: 0 0 2rem 0;
}

.review-item {
  border-bottom: 1px solid var(--medium-gray);
  padding: 1.5rem 0;
}

.review-item:last-child {
  border-bottom: none;
}

.review-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 0.5rem;
  flex-wrap: wrap;
}

.review-header strong {
  color: var(--primary-black);
  font-size: 1.1rem;
}

.review-rating {
  color: var(--star-color);
  font-size: 1rem;
}

.review-date {
  color: var(--text-gray);
  font-size: 0.9rem;
}

.review-item h4 {
  color: var(--primary-black);
  margin: 0.5rem 0;
  font-size: 1.1rem;
}

.review-item p {
  color: var(--dark-gray);
  margin: 0;
  line-height: 1.5;
}

/* Review Form */
#review-form {
  background: var(--light-gray);
  padding: 2rem;
  border-radius: 8px;
  border: 1px solid var(--medium-gray);
}

#review-form h2 {
  color: var(--primary-black);
  font-size: 1.3rem;
  margin-bottom: 1.5rem;
}

#review-form label {
  display: block;
  margin-bottom: 1rem;
  font-weight: 600;
  color: var(--dark-gray);
}

#review-form input[type="text"],
#review-form select,
#review-form textarea {
  width: 100%;
  padding: 0.75rem;
  border: 2px solid var(--medium-gray);
  border-radius: 6px;
  font-size: 1rem;
  background: var(--primary-white);
  color: var(--primary-black);
  transition: border-color 0.3s ease;
  margin-top: 0.25rem;
}

#review-form input[type="text"]:focus,
#review-form select:focus,
#review-form textarea:focus {
  outline: none;
  border-color: var(--dark-blue);
}

#review-form textarea {
  height: 120px;
  resize: vertical;
  font-family: inherit;
}

#review-form button[type="submit"] {
  background: var(--dark-blue);
  color: var(--primary-white);
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 6px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

#review-form button[type="submit"]:hover {
  background: #1565c0;
  transform: translateY(-2px);
}

/* Alert Messages */
.alert {
  padding: 1rem;
  border-radius: 6px;
  margin-bottom: 1rem;
  font-weight: 600;
}

.alert-success {
  background: #e8f5e8;
  color: #2e7d32;
  border: 1px solid #c8e6c9;
}

.alert-error {
  background: #ffebee;
  color: #c62828;
  border: 1px solid #ffcdd2;
}

/* Rating Stars */
.review-rating {
  unicode-bidi: bidi-override;
  color: var(--star-color);
}

.review-rating::before {
  content: "★★★★★";
  letter-spacing: 2px;
}

/* Responsive Design */
@media (max-width: 1024px) {
  #product-info {
    gap: 2rem;
  }
  
  #main-img {
    height: 350px;
  }
}

@media (max-width: 768px) {
  #product-info {
    grid-template-columns: 1fr;
    gap: 2rem;
    padding: 1.5rem;
  }
  
  #main-img {
    height: 300px;
  }
  
  #product-title {
    font-size: 1.6rem;
  }
  
  #product-price {
    font-size: 1.6rem;
  }
  
  #variants-form {
    flex-direction: column;
    gap: 1rem;
  }
  
  #variant-size,
  #variant-color {
    min-width: auto;
  }
  
  #reviews {
    padding: 1.5rem;
  }
  
  .review-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }
}

@media (max-width: 480px) {
  main {
    padding: 0 0.5rem;
  }
  
  #product-info {
    padding: 1rem;
  }
  
  #main-img {
    height: 250px;
  }
  
  #gallery-buttons {
    padding: 0 0.5rem;
  }
  
  #prev-img,
  #next-img {
    width: 35px;
    height: 35px;
    font-size: 1rem;
  }
  
  .thumbnail {
    width: 50px;
    height: 50px;
  }
  
  #details {
    gap: 1rem;
  }
  
  #product-title {
    font-size: 1.4rem;
  }
  
  #product-price {
    font-size: 1.4rem;
  }
  
  #review-form {
    padding: 1.5rem;
  }
}

/* Animation for image transitions */
#main-img {
  transition: opacity 0.3s ease;
}

.thumbnail {
  transition: all 0.3s ease;
}

/* Focus styles for accessibility */
button:focus,
input:focus,
select:focus,
textarea:focus {
  outline: 2px solid var(--dark-blue);
  outline-offset: 2px;
}

/* Loading state for buttons */
button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none !important;
}

/* Print Styles */
@media print {
  #gallery-buttons,
  #review-form,
  #add-to-cart {
    display: none !important;
  }
  
  #product-info,
  #reviews {
    box-shadow: none;
    border: 1px solid var(--primary-black);
  }
}
  </style>
</head>
<body>
  
  <?php include '../header1.php'; ?>
  
  <main>
    <h1>Product Detail</h1>
    <section id="product-info">
      <div id="gallery">
        <div id="gallery-buttons">
          <button id="prev-img">◀</button>
          <button id="next-img">▶</button>
        </div>
        
        <?php if (!empty($images)): ?>
          <img id="main-img" src="<?php echo htmlspecialchars($images[0]['image_url']); ?>" 
               alt="<?php echo htmlspecialchars($images[0]['alt_text'] ?: $product['name']); ?>" 
               width="300" height="300">
        <?php else: ?>
          <img id="main-img" src="<?php echo htmlspecialchars($product['image_url']); ?>" 
               alt="<?php echo htmlspecialchars($product['name']); ?>" 
               width="300" height="300">
        <?php endif; ?>
        
        <div id="thumbnails">
          <?php if (!empty($images)): ?>
            <?php foreach ($images as $index => $image): ?>
              <img src="<?php echo htmlspecialchars($image['image_url']); ?>" 
                   alt="<?php echo htmlspecialchars($image['alt_text'] ?: $product['name']); ?>" 
                   class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>" 
                   width="60" height="60"
                   data-index="<?php echo $index; ?>">
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>

      <div id="details">
        <h2 id="product-title"><?php echo htmlspecialchars($product['name']); ?></h2>
        <p id="product-price">$<?php echo number_format($product['price'], 2); ?></p>
        <p id="product-description"><?php echo htmlspecialchars($product['description']); ?></p>

        <form id="variants-form">
          <label>
            Size:
            <select id="variant-size">
              <option value="S">Small</option>
              <option value="M">Medium</option>
              <option value="L">Large</option>
            </select>
          </label>
          <label>
            Color:
            <select id="variant-color">
              <option value="red">Red</option>
              <option value="blue">Blue</option>
              <option value="green">Green</option>
            </select>
          </label>
        </form>

        <form method="POST" action="../cart/cart_backend.php">
          <input type="hidden" name="action" value="add">
          <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
          <input type="hidden" name="quantity" value="1">
          <button type="submit" id="add-to-cart">Add to Cart</button>
        </form>
      </div>
    </section>

    <section id="reviews">
      <h2>Customer Reviews</h2>
      
      <?php if (!empty($reviews)): ?>
        <ul id="reviews-list">
          <?php foreach ($reviews as $review): ?>
            <li class="review-item">
              <div class="review-header">
                <strong><?php echo htmlspecialchars($review['first_name'] . ' ' . $review['last_name']); ?></strong>
                <span class="review-rating"><?php echo getRatingStars($review['rating']); ?></span>
                <span class="review-date"><?php echo date('M j, Y', strtotime($review['created_at'])); ?></span>
              </div>
              <?php if (!empty($review['title'])): ?>
                <h4><?php echo htmlspecialchars($review['title']); ?></h4>
              <?php endif; ?>
              <p><?php echo htmlspecialchars($review['comment']); ?></p>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p>No reviews yet. Be the first to review this product!</p>
      <?php endif; ?>

      <form id="review-form" method="POST">
        <h2>Leave a review</h2>
        
        <?php if (isset($review_success)): ?>
          <div class="alert alert-success"><?php echo $review_success; ?></div>
        <?php endif; ?>
        
        <?php if (isset($review_error)): ?>
          <div class="alert alert-error"><?php echo $review_error; ?></div>
        <?php endif; ?>
        
        <label>
          Name: 
          <input type="text" id="reviewer-name" name="reviewer_name" required>
        </label>
        <label>
          Rating:
          <select id="rating" name="rating" required>
            <option value="5">★★★★★</option>
            <option value="4">★★★★☆</option>
            <option value="3">★★★☆☆</option>
            <option value="2">★★☆☆☆</option>
            <option value="1">★☆☆☆☆</option>
          </select>
        </label>
        <label>
          Comment:
          <textarea name="comment" required></textarea>
        </label>
        <button type="submit" name="submit_review">Submit Review</button>
      </form>
    </section>
  </main>

  <script>
    // Image gallery functionality
    document.addEventListener('DOMContentLoaded', function() {
      const mainImg = document.getElementById('main-img');
      const thumbnails = document.querySelectorAll('.thumbnail');
      const prevBtn = document.getElementById('prev-img');
      const nextBtn = document.getElementById('next-img');
      
      let currentImageIndex = 0;
      
      // Thumbnail click event
      thumbnails.forEach(thumb => {
        thumb.addEventListener('click', function() {
          const index = parseInt(this.getAttribute('data-index'));
          currentImageIndex = index;
          updateMainImage();
          updateActiveThumbnail();
        });
      });
      
      // Previous button
      prevBtn.addEventListener('click', function() {
        currentImageIndex = (currentImageIndex - 1 + thumbnails.length) % thumbnails.length;
        updateMainImage();
        updateActiveThumbnail();
      });
      
      // Next button
      nextBtn.addEventListener('click', function() {
        currentImageIndex = (currentImageIndex + 1) % thumbnails.length;
        updateMainImage();
        updateActiveThumbnail();
      });
      
      function updateMainImage() {
        if (thumbnails[currentImageIndex]) {
          mainImg.src = thumbnails[currentImageIndex].src;
          mainImg.alt = thumbnails[currentImageIndex].alt;
        }
      }
      
      function updateActiveThumbnail() {
        thumbnails.forEach(thumb => thumb.classList.remove('active'));
        if (thumbnails[currentImageIndex]) {
          thumbnails[currentImageIndex].classList.add('active');
        }
      }
    });
  </script>
</body>
</html>