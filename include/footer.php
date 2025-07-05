<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Full Width Footer</title>

  <!-- Font Awesome for Icons -->
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
    rel="stylesheet"
  />
  
  <!-- Bootstrap CDN -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
  />

  <style>
    /* Reset margin and padding */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: Arial, sans-serif;
    }

    footer {
      width: 100%;
      background-color: rgb(235, 73, 52);
      color: white;
      padding: 60px 0;
    }

    .footer-widget {
      margin-bottom: 30px;
    }

    .footer-widget .widget-title {
      font-size: 20px;
      font-weight: 600;
      margin-bottom: 20px;
      padding-bottom: 10px;
      border-bottom: 2px solid rgba(255, 255, 255, 0.3);
    }

    .footer-nav ul {
      list-style: none;
      padding-left: 0;
    }

    .footer-nav ul li {
      margin-bottom: 10px;
    }

    .footer-nav ul li a {
      color: white;
      text-decoration: none;
      transition: color 0.3s;
    }

    .footer-nav ul li a:hover {
      color: #ffc107;
    }

    .footer-widget ul li {
      margin-bottom: 10px;
      font-size: 15px;
    }

    .footer-widget ul li i {
      margin-right: 10px;
    }

    .footer-widget.p-4 {
      background-color: rgba(255, 255, 255, 0.05);
      border-radius: 10px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .footer-widget .btn {
      font-weight: 600;
      margin-top: 15px;
      background-color: #fff;
      color: rgb(235, 73, 52);
      border: none;
      transition: 0.3s ease;
    }

    .footer-widget .btn:hover {
      background-color: #ffc107;
      color: #fff;
    }

    @media (max-width: 767px) {
      .footer-widget {
        text-align: center;
      }

      .footer-widget ul li {
        justify-content: center;
      }

      .footer-widget .btn {
        display: inline-block;
        margin-top: 10px;
      }
    }
  </style>
</head>

<body>

  <!-- Footer Start -->
  <footer>
    <div class="container">
      <div class="row">

        <!-- Left CTA Section -->
        <div class="col-md-12 col-lg-4 mb-4">
          <div class="footer-widget p-4">
            <h5 class="text-white mb-3">
              <i class="fas fa-home me-2"></i>हामीसँग सम्पर्क गर्नुहोस्
            </h5>
            <p class="text-white fw-bold" style="font-size: 16px;">
              भरपर्दो र विश्वासिलो घर किनबेचको लागि सम्पर्क गर्नुहोस्।
            </p>
            <a href="contact.php" class="btn btn-light">
              अहिले सम्पर्क गर्नुहोस्
            </a>
          </div>
        </div>

        <!-- Right Section -->
        <div class="col-md-12 col-lg-8">
          <div class="row">

            <!-- Support Links -->
            <div class="col-md-4">
              <div class="footer-widget footer-nav">
                <h4 class="widget-title text-white">Support</h4>
                <ul>
                  <li><a href="#">Forum</a></li>
                  <li><a href="#">Terms and Conditions</a></li>
                  <li><a href="#">Frequently Asked Questions</a></li>
                  <li><a href="#">Contact</a></li>
                </ul>
              </div>
            </div>

            <!-- Quick Links -->
            <div class="col-md-4">
              <div class="footer-widget footer-nav">
                <h4 class="widget-title text-white">Quick Links</h4>
                <ul>
                  <li><a href="about.php">About Us</a></li>
                  <li><a href="#">Featured Property</a></li>
                  <li><a href="#">Submit Property</a></li>
                  <li><a href="agent.php">Our Agents</a></li>
                </ul>
              </div>
            </div>

            <!-- Contact Info -->
            <div class="col-md-4">
              <div class="footer-widget">
                <h4 class="widget-title text-white">Contact Us</h4>
                <ul style='list-style: none; margin: 0;padding:0;' >
                  <li><i class="fas fa-map-marker-alt"></i> Samakhusi, Kathmandu</li>
                  <li><i class="fas fa-phone-alt"></i> 9836264636</li>
                  <li><i class="fas fa-envelope"></i> sandesh@gmail.com</li>
                </ul>
              </div>
            </div>

          </div> <!-- End Inner Row -->
        </div>

      </div>
    </div>
  </footer>
  <!-- Footer End -->

</body>
</html>
