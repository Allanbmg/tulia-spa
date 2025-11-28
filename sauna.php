<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sauna & Steam – Tulia Wellness Spa</title>

  <style>
    body {
      margin: 0;
      font-family: "Segoe UI", sans-serif;
      background: url('https://i.pinimg.com/736x/67/9f/74/679f74b7982e5ee3394bea918f2b8097.jpg') no-repeat center center fixed;
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

    ul {
      padding-left: 20px;
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

    .booking-section h2 {
      color: #fffacd;
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

    input::placeholder {
      color: #f0f0f0;
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
      display: none;
      color: #fff;
    }

    @media (max-width: 600px) {
      .container {
        padding: 20px;
      }
    }
  </style>
</head>

<body>

  <header>
    <h1>Sauna & Steam</h1>
    <p>Tulia Wellness Spa – Relax. Refresh. Renew.</p>
  </header>

  <div class="container">

    <h2>About Sauna & Steam</h2>
    <p>
      Our sauna and steam therapy helps detoxify the body, relax muscles, and improve circulation.
      This treatment promotes relaxation, reduces stress, and leaves your skin refreshed and glowing.
    </p>

    <h2>Benefits of Sauna & Steam</h2>
    <ul>
      <li>Detoxifies the body through sweating</li>
      <li>Relieves stress and tension</li>
      <li>Improves blood circulation and skin health</li>
      <li>Helps in relaxation and better sleep</li>
      <li>Boosts overall wellness and energy</li>
    </ul>

    <h2>Our Rates (KES)</h2>
    <table class="rate-table">
      <tr>
        <th>Service</th>
        <th>Duration</th>
        <th>Price (KES)</th>
      </tr>
      <tr>
        <td>Standard Sauna Session</td>
        <td>30 minutes</td>
        <td>KES 1,500</td>
      </tr>
      <tr>
        <td>Steam Therapy</td>
        <td>45 minutes</td>
        <td>KES 2,000</td>
      </tr>
      <tr>
        <td>Luxury Sauna & Steam Package</td>
        <td>60 minutes</td>
        <td>KES 3,500</td>
      </tr>
    </table>

    <div class="booking-section">
      <h2>Book This Service</h2>

      <form id="bookingForm">
        <label>Select Service</label>
        <select id="service" required>
          <option value="">Choose...</option>
          <option value="Standard Sauna Session">Standard Sauna Session – KES 1,500</option>
          <option value="Steam Therapy">Steam Therapy – KES 2,000</option>
          <option value="Luxury Sauna & Steam Package">Luxury Sauna & Steam Package – KES 3,500</option>
        </select>

        <label>Choose Booking Date</label>
        <input type="date" id="date" required />

        <label>Select Time</label>
        <input type="time" id="time" required />

        <button type="submit">Book Appointment</button>
      </form>

      <div class="success-message" id="successMessage">
        Booking successful! Your appointment request has been received.
      </div>
    </div>

  </div>

  <script>
    document.getElementById("bookingForm").addEventListener("submit", function (e) {
      e.preventDefault();
      document.getElementById("successMessage").style.display = "block";
    });
  </script>

</body>
</html>
