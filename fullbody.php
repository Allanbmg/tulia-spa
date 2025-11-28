<?php
// PROCESS FORM SUBMISSION
$success = false;
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Database connection
    $conn = new mysqli("localhost", "root", "", "spa_system");

    if ($conn->connect_error) {
        $error = "Database connection failed!";
    } else {
        // Collect POST data
        $service = $_POST['service'];
        $date = $_POST['date'];
        $time = $_POST['time'];
      
        // Insert into MySQL
      $stmt = $conn->prepare("INSERT INTO bookings (service, booking_date, booking_time) VALUES (?, ?, ?)");
      $stmt->bind_param("sss", $service, $date, $time);


        if ($stmt->execute()) {
            $success = true;
        } else {
            $error = "Failed to save your booking!";
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Full Body Massage – Tulia Wellness Spa</title>

  <style>
    body {
      margin: 0;
      font-family: "Segoe UI", sans-serif;
      background: url('https://i.pinimg.com/736x/6b/9d/ad/6b9dada1d97991edf80499e74953822f.jpg') no-repeat center center fixed;
      background-size: cover;
      color: #fff;
      line-height: 1.6;
    }

    header {
      text-align: center;
      padding: 20px;
      color: #fff;
      text-shadow: 1px 1px 5px rgba(0,0,0,0.7);
    }

    .container {
      width: 90%;
      max-width: 1000px;
      margin: 40px auto;
      background: rgba(255, 255, 255, 0.25);
      backdrop-filter: blur(15px);
      -webkit-backdrop-filter: blur(15px);
      border-radius: 15px;
      padding: 30px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.3);
      color: #fff;
    }

    h2 {
      color: #ffeb99;
      margin-top: 0;
      text-shadow: 1px 1px 5px rgba(0,0,0,0.5);
    }

    p, li {
      text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
    }

    .rate-table {
      width: 100%;
      border-collapse: collapse;
      margin: 15px 0;
      background: rgba(0,0,0,0.25);
      color: #fff;
    }

    .rate-table th, .rate-table td {
      border: 1px solid rgba(255,255,255,0.5);
      padding: 12px;
      text-align: left;
    }

    .rate-table th {
      background: rgba(255, 184, 71, 0.5);
    }

    .booking-section {
      margin-top: 30px;
      padding: 25px;
      background: rgba(0,0,0,0.5);
      backdrop-filter: blur(10px);
      border-left: 5px solid #ff8c00;
      border-radius: 12px;
      color: #fff;
    }

    input, select, button {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border: 1px solid rgba(255,255,255,0.5);
      border-radius: 6px;
      font-size: 16px;
      background: rgba(255,255,255,0.15);
      color: #fff;
    }

    button {
      background: #ff8c00;
      color: white;
      cursor: pointer;
      font-weight: bold;
      transition: 0.3s;
    }

    button:hover {
      background: #e27700;
    }

    .success-message {
      margin-top: 15px;
      padding: 15px;
      background: rgba(0,128,0,0.6);
      border-left: 5px solid #2b8a2b;
      border-radius: 5px;
      display: block;
      color: #fff;
    }

    .error-message {
      margin-top: 15px;
      padding: 15px;
      background: rgba(255,0,0,0.6);
      border-left: 5px solid #b30000;
      border-radius: 5px;
      display: block;
      color: #fff;
    }
  </style>
</head>

<body>

  <header>
    <h1>Full Body Massage</h1>
    <p>Tulia Wellness Spa – Relax. Refresh. Renew.</p>
  </header>

  <div class="container">

    <h2>About the Full Body Massage</h2>
    <p>
      Our full body massage is designed to release tension, soothe sore muscles, 
      and promote deep relaxation.
    </p>

    <h2>Our Rates (KES)</h2>
    <table class="rate-table">
      <tr>
        <th>Service</th>
        <th>Duration</th>
        <th>Price (KES)</th>
      </tr>
      <tr>
        <td>Swedish Full Body Massage</td>
        <td>60 minutes</td>
        <td>KES 3,000</td>
      </tr>
      <tr>
        <td>Deep Tissue Full Body Massage</td>
        <td>75 minutes</td>
        <td>KES 4,500</td>
      </tr>
      <tr>
        <td>Luxury Aromatherapy Massage</td>
        <td>90 minutes</td>
        <td>KES 6,000</td>
      </tr>
    </table>

    <div class="booking-section">
      <h2>Book This Service</h2>

      <?php if ($success): ?>
        <div class="success-message">Booking successful! Your appointment has been saved.</div>
      <?php elseif (!$success && $error): ?>
        <div class="error-message"><?= $error ?></div>
      <?php endif; ?>

      <form method="POST" action="">
        <label>Select Service</label>
        <select name="service" required>
          <option value="">Choose...</option>
          <option value="Swedish Full Body Massage">Swedish Full Body Massage – KES 3,000</option>
          <option value="Deep Tissue Full Body Massage">Deep Tissue Full Body Massage – KES 4,500</option>
          <option value="Luxury Aromatherapy Massage">Luxury Aromatherapy Massage – KES 6,000</option>
        </select>

        <label>Choose Booking Date</label>
        <input type="date" name="date" required />

        <label>Select Time</label>
        <input type="time" name="time" required />

        <button type="submit">Book Appointment</button>
      </form>

    </div>
  </div>

</body>
</html>
