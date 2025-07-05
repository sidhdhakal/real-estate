<?php 
ini_set('session.cache_limiter','public');
session_cache_limiter(false);
session_start();
include("config.php");								
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <title>About Us - Real Estate PHP</title>
 
  <style>
    body {
      font-family: 'Comfortaa', cursive;
      margin: 0;
      padding: 0;
    }

    .hero {
      position: relative;
      height: 70vh;
      background: url('images/banner/cartoon.png') center center/cover no-repeat;
      color: #fff;
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: 60px 30px;
      text-shadow: 0 2px 4px rgba(0,0,0,0.6);
    }

    .hero h1 {
      font-size: 3rem;
      font-weight: bold;
      margin-bottom: 20px;
    }

    .hero p {
      max-width: 600px;
      font-size: 1.1rem;
      line-height: 1.8;
    }

    .about-section {
      background: #fff;
      padding: 60px 20px;
      text-align: center;
    }

    .about-section h2 {
      font-size: 2rem;
      font-weight: 700;
      color: rgb(235, 73, 52);
      margin-bottom: 30px;
    }

    .about-section p {
      max-width: 900px;
      margin: 0 auto;
      font-size: 1.05rem;
      color: #444;
      line-height: 1.7;
    }

    @media (max-width: 768px) {
      .hero h1 {
        font-size: 2.2rem;
      }

      .hero p {
        font-size: 1rem;
      }
    }
  </style>
</head>

<body>

<div id="page-wrapper">
  <!-- Header -->
  <?php include("include/header.php"); ?>

  <!-- Hero Section -->
  <div class="hero">
    <h1>Who we are?</h1>
    <p>
      We are Real Estate Helping Partner.
    </p>
  </div>

  <!-- About Section from DB -->
  <section class="about-section">
    <?php 
      $query = mysqli_query($con, "SELECT * FROM about");
      while($row = mysqli_fetch_array($query)) {
    ?>
      <h2><?php echo htmlspecialchars($row['1']); ?></h2>
      <p><?php echo $row['2']; ?></p>
    <?php } ?>
  </section>

  <!-- Footer -->
  <?php include("include/footer.php"); ?>
</div>


</body>
</html>
