<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>footer</title>
    <link rel="stylesheet" href="../main/style1.css">
    <style>
      /* Footer Styles - Black & White Theme */
footer {
    background-color: #000000;
    color: #ffffff;
    padding: 40px 0 20px;
    font-family: Arial, sans-serif;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.footer-content {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 30px;
    margin-bottom: 30px;
}

.footer-column {
    flex: 1;
    min-width: 200px;
}

.footer-column h3 {
    color: #ffffff;
    font-size: 18px;
    margin-bottom: 20px;
    font-weight: bold;
    border-bottom: 2px solid #ffffff;
    padding-bottom: 10px;
}

.footer-column p {
    color: #cccccc;
    line-height: 1.6;
    margin-bottom: 20px;
}

.social-links {
    display: flex;
    gap: 15px;
}

.social-links a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    background-color: #ffffff;
    color: #000000;
    border-radius: 50%;
    text-decoration: none;
    transition: all 0.3s ease;
}

.social-links a:hover {
    background-color: #333333;
    color: #ffffff;
    transform: translateY(-2px);
}

.footer-links {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-links li {
    margin-bottom: 12px;
}

.footer-links a {
    color: #cccccc;
    text-decoration: none;
    transition: color 0.3s ease;
    display: flex;
    align-items: center;
}

.footer-links a:hover {
    color: #ffffff;
    text-decoration: underline;
}

.footer-links i {
    margin-right: 10px;
    width: 16px;
    text-align: center;
    color: #ffffff;
}

.copyright {
    border-top: 1px solid #333333;
    padding-top: 20px;
    text-align: center;
}

.copyright p {
    color: #cccccc;
    margin: 0;
    font-size: 14px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .footer-content {
        flex-direction: column;
        gap: 30px;
    }
    
    .footer-column {
        min-width: 100%;
    }
    
    .social-links {
        justify-content: flex-start;
    }
}

@media (max-width: 480px) {
    footer {
        padding: 30px 0 15px;
    }
    
    .container {
        padding: 0 15px;
    }
    
    .footer-column h3 {
        font-size: 16px;
    }
    
    .footer-links li {
        margin-bottom: 8px;
    }
}

/* Font Awesome icon styles (if not already loaded) */
.fab, .fas {
    font-family: "Font Awesome 5 Brands", "Font Awesome 5 Free";
    font-weight: 400;
}

.fas {
    font-weight: 900;
}

.fa-facebook:before { content: "\f39e"; }
.fa-twitter:before { content: "\f099"; }
.fa-instagram:before { content: "\f16d"; }
.fa-pinterest:before { content: "\f0d2"; }
.fa-map-marker-alt:before { content: "\f3c5"; }
.fa-phone:before { content: "\f095"; }
.fa-envelope:before { content: "\f0e0"; }
.fa-clock:before { content: "\f017"; }
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