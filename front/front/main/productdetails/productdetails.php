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
  <link rel="stylesheet" href="../style1.css">
  <link rel="stylesheet" href="productdetails.css">
  <title>Product Detail - <?php echo htmlspecialchars($product['name']); ?></title>
</head>
<body>
  
  <?php include '../header1.php'; ?>
  <header>
    <h1>Product Detail</h1>
    
  </header>
  <main>
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