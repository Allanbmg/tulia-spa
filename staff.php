<?php
session_start();
$mysqli = new mysqli("localhost", "root", "", "spa_system");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Redirect if not logged in OR not a staff user
if (!isset($_SESSION['staff_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit;
}

$staff_id = $_SESSION['staff_id'];

// Handle AJAX status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'], $_POST['status'])) {
    $booking_id = (int)$_POST['booking_id'];
    $status = trim($mysqli->real_escape_string($_POST['status']));

    $allowed = ['Started','Completed','Cancelled'];
    if(!in_array($status, $allowed)) exit('fail');

    $mysqli->query("UPDATE bookings SET status='$status' WHERE id=$booking_id AND staff_id=$staff_id");
    echo $mysqli->affected_rows > 0 ? "success" : "fail";
    exit;
}

// Fetch logged-in staff info
$staff = $mysqli->query("SELECT * FROM staff WHERE staff_id = $staff_id")->fetch_assoc();

// Fetch assigned bookings for this staff
$bookings = $mysqli->query("SELECT * FROM bookings WHERE staff_id = $staff_id ORDER BY booking_date, booking_time")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Tulia Wellness Spa - Staff Dashboard</title>
<style>
body {margin:0; font-family:"Segoe UI",sans-serif; background:url('https://i.pinimg.com/736x/5a/ba/11/5aba1140451cd4cee51f6459d4bcc354.jpg') no-repeat center/cover; color:#fff; backdrop-filter:blur(5px);}
header {background: rgba(0,150,200,0.3); backdrop-filter:blur(10px); color:white; padding:20px; text-align:center; font-size:28px; font-weight:bold; letter-spacing:1px; box-shadow:0 2px 10px rgba(0,0,0,0.3);}
.container {display:flex;}
.sidebar {width:250px; background: rgba(0,150,200,0.25); backdrop-filter:blur(12px); height:100vh; box-shadow:2px 0 10px rgba(0,0,0,0.2); padding-top:20px;}
.sidebar a, .sidebar button {display:block; padding:15px 20px; text-decoration:none; color:white; font-size:18px; margin-bottom:10px; background:none; border:none; cursor:pointer; text-align:left;}
.sidebar a:hover, .sidebar button:hover {background: rgba(0,200,150,0.5); border-radius:5px;}
.main-content {flex-grow:1; padding:30px;}
h2 {color:#a8ffea; margin-bottom:15px;}
.card {background: rgba(255,255,255,0.15); backdrop-filter:blur(15px); padding:20px; margin-bottom:20px; border-radius:15px; box-shadow:0 4px 15px rgba(0,0,0,0.3);}
.appointment {border-left:5px solid #00b894; padding-left:15px; margin-bottom:15px;}
.status-btn {padding:8px 15px; background:#00b894; border:none; color:white; border-radius:5px; cursor:pointer; margin-top:8px; margin-right:10px; transition:0.3s;}
.status-btn:hover {background:#0984e3;}
.cancelled-btn {background:#d63031;}
.cancelled-btn:hover {background:#ff7675;}
.logout {display:block; text-align:center; text-decoration:none; margin-top:30px; width:90%; margin-left:5%; padding:12px; background:#d63031; border:none; color:white; font-size:18px; border-radius:8px; cursor:pointer; transition:0.3s;}
.logout:hover {background:#ff7675;}
</style>
</head>
<body>
<header>Tulia Wellness Spa – Staff Dashboard</header>

<div class="container">
  <div class="sidebar">
    <a href="#dashboard">Dashboard</a>
    <a href="#assigned">Assigned Clients</a>
    <a href="#upcoming">Upcoming Bookings</a>
    <a href="#profile">My Profile</a>
    <form method="POST" action="logout.php"><button class="logout">Log Out</button></form>
  </div>

  <div class="main-content">
    <section id="dashboard" class="card">
      <h2>Dashboard Overview</h2>
      <p>Welcome back, <?= htmlspecialchars($staff['full_name']) ?>! Here is a quick look at your tasks for today.</p>
    </section>

    <section id="assigned" class="card">
      <h2>Assigned Clients</h2>
      <?php if (count($bookings) > 0): ?>
        <?php foreach ($bookings as $b): ?>
          <div class="appointment" data-booking-id="<?= $b['id'] ?>">
            <strong>Client:</strong> <?= htmlspecialchars($b['client_name']) ?><br>
            <strong>Service:</strong> <?= htmlspecialchars($b['service']) ?><br>
            <strong>Time:</strong> <?= htmlspecialchars($b['booking_date']) ?> <?= htmlspecialchars($b['booking_time']) ?><br>
            
            <button class="status-btn" data-status="Started">Mark as Started</button>
            <button class="status-btn" data-status="Completed">Mark as Completed</button>
            <button class="status-btn cancelled-btn" data-status="Cancelled">Cancelled</button>
            
            <?php if (!empty($b['status'])): ?>
              <p class="status-text">Status: <?= htmlspecialchars($b['status']) ?></p>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>No bookings assigned to you yet.</p>
      <?php endif; ?>
    </section>

    <section id="upcoming" class="card">
      <h2>Upcoming Bookings</h2>
      <p>View your schedule for the rest of the day.</p>
      <ul>
        <?php foreach ($bookings as $b): ?>
          <li><?= htmlspecialchars($b['booking_time']) ?>: <?= htmlspecialchars($b['service']) ?> (Client: <?= htmlspecialchars($b['client_name']) ?>) — Status: <?= htmlspecialchars($b['status'] ?? 'Pending') ?></li>
        <?php endforeach; ?>
      </ul>
    </section>

    <section id="profile" class="card">
      <h2>My Profile</h2>
      <p>Name: <?= htmlspecialchars($staff['full_name']) ?></p>
      <p>Role: <?= htmlspecialchars($staff['role']) ?></p>
      <p>Available Services: <?= htmlspecialchars($staff['role']) == 'Massage Therapist' ? 'Massage, Body Scrub, Aromatherapy' : 'N/A' ?></p>
    </section>
  </div>
</div>

<script>
// Update booking status via AJAX
document.querySelectorAll('.status-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const container = this.closest('.appointment');
        const bookingId = container.dataset.bookingId;
        const status = this.dataset.status;

        const formData = new URLSearchParams();
        formData.append('booking_id', bookingId);
        formData.append('status', status);

        fetch('', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: formData.toString()
        })
        .then(res => res.text())
        .then(data => {
            if (data === 'success') {
                let statusText = container.querySelector('.status-text');
                if (!statusText) {
                    statusText = document.createElement('p');
                    statusText.className = 'status-text';
                    container.appendChild(statusText);
                }
                statusText.textContent = "Status: " + status;
                statusText.style.color = status === 'Cancelled' ? '#ff7675' : '#a8ffea';
            } else {
                alert('Failed to update status.');
            }
        });
    });
});
</script>
</body>
</html>
