<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

// Ensure only clients can access this page
if ($_SESSION['role'] != 'client') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tulia Wellness Spa - Client Dashboard</title>

  <style>
    body {
      margin: 0;
      padding: 0;
      background: url('https://i.pinimg.com/736x/c9/31/db/c931db7c71f40af381acd1b4782a1f4f.jpg') no-repeat center center / cover;
      font-family: "Segoe UI", sans-serif;
      color: #2f4f4f;
    }

    header {
      background: #3baea0;
      padding: 20px;
      text-align: center;
      color: white;
      font-size: 28px;
      letter-spacing: 1px;
    }

    nav {
      display: flex;
      justify-content: space-around;
      align-items: center;
      background: #def2f1;
      padding: 15px;
      font-size: 18px;
    }

    nav a {
      text-decoration: none;
      color: #2f4f4f;
      padding: 8px 15px;
      border-radius: 8px;
      transition: 0.3s;
    }

    nav a:hover {
      background: #3baea0;
      color: white;
    }

    .logout-btn {
      background: red;
      color: white !important;
      padding: 8px 18px;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
    }

    .logout-btn:hover {
      background: darkred;
    }

    .container {
      padding: 30px;
    }

    .section-title {
      font-size: 26px;
      margin-bottom: 15px;
      color: orange;
      font-family: cursive;
    }

    .services-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
    }

    .service-card {
      background: white;
      padding: 15px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      text-align: center;
      transition: 0.3s;
    }

    .service-card:hover {
      transform: scale(1.05);
    }

    .service-card img {
      width: 100%;
      height: 150px;
      object-fit: cover;
      border-radius: 10px;
    }

    .locations-list {
      margin-top: 20px;
      font-size: 20px;
      color: yellow;
    }

    footer {
      margin-top: 40px;
      background: #3baea0;
      text-align: center;
      padding: 15px;
      color: white;
    }
  </style>
</head>
<body>

<header>
  Karibu Nyumbani
</header>

<nav>
  <a href="#services">Services</a>
  <a href="#locations">Locations</a>
  <a href="#profile">My Profile</a>

  <!-- LOGOUT BUTTON -->
 <form method="POST" action="logout.php" style="margin:0;">
    <button type="submit" class="logout-btn">Logout</button>
</form>

</nav>

<div class="container">

  <!-- SERVICES -->
  <section id="services">
    <h2 class="section-title">Our Services</h2>

    <div class="services-grid">

      <a href="fullbody.php" class="service-link">
        <div class="service-card">
          <img src="https://i.pinimg.com/736x/4a/c4/16/4ac4165c57ae7a33415b83492bc68a33.jpg" />
          <h3>Full Body Massage</h3>
        </div>
      </a>

      <a href="facial.php" class="service-link">
        <div class="service-card">
          <img src="https://i.pinimg.com/736x/1a/eb/c8/1aebc869c07279823f66e6d35e8d5557.jpg" />
          <h3>Facial Treatment</h3>
        </div>
      </a>

      <a href="sauna.php" class="service-link">
        <div class="service-card">
          <img src="https://i.pinimg.com/1200x/48/0f/f8/480ff8795adb6e5e5fe563c064d731c8.jpg" />
          <h3>Sauna & Steam</h3>
        </div>
      </a>

      <a href="manicureandpedicure.php" class="service-link">
        <div class="service-card">
          <img src="https://i.pinimg.com/1200x/02/ea/e1/02eae1fc1f0e7c9f4bfa52ee8347a941.jpg" />
          <h3>Manicure & Pedicure</h3>
        </div>
      </a>

      <a href="bodyscrub.php" class="service-link">
        <div class="service-card">
          <img src="https://i.pinimg.com/1200x/8b/d7/9e/8bd79eac8e6b3839aa9df62bc2a2344f.jpg" />
          <h3>Body Scrub</h3>
        </div>
      </a>

      <a href="aromatherapy.php" class="service-link">
        <div class="service-card">
          <img src="https://i.pinimg.com/736x/ab/d2/31/abd231b09f4666df853ff4b0cabf01ec.jpg" />
          <h3>Aromatherapy</h3>
        </div>
      </a>

    </div>
  </section>

  <!-- LOCATIONS -->
  <section id="locations">
    <h2 class="section-title">Our Locations</h2>
    <div class="locations-list">
      <p>📍 Nairobi Branch</p>
      <p>📍 Mombasa Branch</p>
      <p>📍 Eldoret Branch</p>
    </div>
  </section>

  <!-- PROFILE -->
  <section id="profile" style="margin-top: 40px;">
    <h2 class="section-title">My Profile</h2>
    <a href="profile.php">
      <button style="padding: 12px 20px; background:#3baea0; color:white; border:none; border-radius:8px; font-size:18px; cursor:pointer;">
        View Profile
      </button>
    </a>
  </section>

</div>

<footer>
  © 2025 Tulia Wellness Spa — Relax. Refresh. Renew.
</footer>

</body>
</html>
