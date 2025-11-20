<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>footer</title>
    <link rel="stylesheet" href="../../front/main/style1.css">
    <style>
      footer {
      background-color: var(--dark);
      color: white;
      padding: 2%;
    }
    
    .footer-content {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 2rem;
    }
    
    .footer-column h3 {
      color: var(--white);
      margin-bottom: 1rem;
      font-size: 1.2rem;
    }
    
    .footer-links {
      list-style: none;
    }
    
    .footer-links li {
      margin-bottom: 0.5rem;
    }
    
    .footer-links a {
      color: var(--gray);
      text-decoration: none;
      transition: color 0.3s;
    }
    
    .footer-links a:hover {
      color: var(--white);
    }
    
    .social-links {
      display: flex;
      gap: 1rem;
      margin-top: 1rem;
    }
    
    .social-links a {
      color: var(--white);
      font-size: 1.5rem;
    }
    
    .copyright {
      text-align: center;
      margin-top: 2rem;
      padding-top: 1rem;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
      color: var(--gray);
    }
    

    </style>
</head>
<body>
    <footer>
    <div class="container">
      <div class="footer-content">
        <div class="footer-column">
          <h3>ToyBox</h3>
          <p>Your one-stop shop for all kinds of toys and games for children of all ages.</p>
          <div class="social-links">
            <a href="#"><i class="fab fa-facebook"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-pinterest"></i></a>
          </div>
        </div>
        
        <div class="footer-column">
          <h3>Quick Links</h3>
          <ul class="footer-links">
            <li><a href="../index/index.php">Home</a></li>
            <li><a href="../productdetails/productdetails.php">Products</a></li>
            <li><a href="../productlist/productlist.php">Categories</a></li>
            <li><a href="../../informationalpages/about/about.php">About Us</a></li>
            <li><a href="../../informationalpages/contact/contact.php">Contact</a></li>
          </ul>
        </div>
        
        <div class="footer-column">
          <h3>Customer Service</h3>
          <ul class="footer-links">
            <li><a href="../checkout/shipping/shipping.php">Shipping & Returns</a></li>
            <li><a href="../../informationalpages/faq.php">FAQ</a></li>
            <li><a href="../../informationalpages/termsprivacy.php">Terms & Conditions</a></li>
            <li><a href="../../informationalpages/termsprivacy.php">Privacy Policy</a></li>
          </ul>
        </div>
        
        <div class="footer-column">
          <h3>Contact Info</h3>
          <ul class="footer-links">
            <li><i class="fas fa-map-marker-alt"></i> 123 Toy Street, Fun City</li>
            <li><i class="fas fa-phone"></i> (555) 123-4567</li>
            <li><i class="fas fa-envelope"></i> info@toybox.com</li>
            <li><i class="fas fa-clock"></i> Mon-Fri: 9AM-5PM</li>
          </ul>
        </div>
      </div>
      
      <div class="copyright">
        <p>&copy; <span id="year"><?php echo date('Y'); ?></span> ToyBox • All rights reserved</p>
      </div>
    </div>
  </footer>

</body>
</html>