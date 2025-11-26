<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Contact Us - ToyLand</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../info.css">
    <link rel="stylesheet" href="../infomation.css">

  <!-- <style>
    
  </style> -->
</head>
<body>
   
           <head><?php include '../header4.php'; ?></head>
  

  <main>
    <section class="contact-info">
      <h2><i class="fas fa-headset"></i> Customer Service</h2>
      <div class="info-item">
        <i class="fas fa-envelope"></i>
        <div>
          <h3>Email</h3>
          <p>support@toyland.com</p>
        </div>
      </div>
      
      <div class="info-item">
        <i class="fas fa-phone"></i>
        <div>
          <h3>Phone</h3>
          <p>+1-234-567-8900</p>
        </div>
      </div>
      
      <div class="info-item">
        <i class="fas fa-clock"></i>
        <div>
          <h3>Working Hours</h3>
          <p>Monday-Friday: 9:00 AM - 6:00 PM</p>
          <p>Saturday: 10:00 AM - 4:00 PM</p>
        </div>
      </div>
    </section>

    <section>
      <h2><i class="fas fa-map-marker-alt"></i> Store Locations</h2>
      <ul>
        <li>
          <i class="fas fa-store"></i>
          <div>
            <h3>ToyLand Cityville</h3>
            <p>123 Toy Street, Cityville, ST 12345</p>
          </div>
        </li>
        <li>
          <i class="fas fa-store"></i>
          <div>
            <h3>ToyLand Playtown</h3>
            <p>456 Fun Avenue, Playtown, ST 67890</p>
          </div>
        </li>
      </ul>
      
      <div class="map-container">
        <i class="fas fa-map-marked-alt fa-3x"></i>
        <span style="margin-left: 10px;">Interactive Map Here</span>
      </div>
    </section>

    <section class="form-section">
      <h2><i class="fas fa-paper-plane"></i> Send Us a Message</h2>
      <form id="contact-form">
        <div class="form-group">
          <label for="name">Full Name</label>
          <input type="text" id="name" name="name" required placeholder="Your full name">
        </div>
        
        <div class="form-group">
          <label for="email">Email Address</label>
          <input type="email" id="email" name="email" required placeholder="Your email address">
        </div>
        
        <div class="form-group full-width">
          <label for="subject">Subject</label>
          <input type="text" id="subject" name="subject" placeholder="What is this regarding?">
        </div>
        
        <div class="form-group full-width">
          <label for="message">Message</label>
          <textarea id="message" name="message" required placeholder="How can we help you?"></textarea>
        </div>
        
        <div class="form-group full-width">
          <button type="submit"><i class="fas fa-paper-plane"></i> Send Message</button>
        </div>
      </form>
    </section>
  </main>

  <footer>
    <p>© 2023 ToyLand. Bringing Joy to Children Everywhere.</p>
  </footer>

  <div id="notification" class="notification"></div>

  <script src="contact.js"></script>

  <!-- <script>
    
  </script> -->
</body>
</html>