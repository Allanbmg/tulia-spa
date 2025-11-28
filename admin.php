<?php
session_start();

$mysqli = new mysqli("localhost", "root", "", "spa_system");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Redirect if not logged in OR not a staff user
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Fetch bookings
$bookings = $mysqli->query("SELECT * FROM bookings ORDER BY booking_date DESC, booking_time DESC")->fetch_all(MYSQLI_ASSOC);

// Fetch staff for assignment dropdown
$staff = $mysqli->query("SELECT staff_id, full_name FROM staff")->fetch_all(MYSQLI_ASSOC);
$allStaff = $mysqli->query("SELECT staff_id, full_name, email, status FROM staff")->fetch_all(MYSQLI_ASSOC);


// Dummy placeholders for services and clients
$services = [];
$clients = [];

// Handle Approve request
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    $mysqli->query("UPDATE bookings SET status='approved' WHERE id=$id");
    header("Location: admin.php?tab=appointments");
    exit;
}

// Handle Assign Staff request
if (isset($_POST['assign_staff'])) {
    $bookingId = intval($_POST['id']);
    $staffId = intval($_POST['staff_id']);

    // Check that the staff exists to avoid foreign key errors
    $staffResult = $mysqli->query("SELECT full_name FROM staff WHERE staff_id=$staffId");
    if ($staffResult->num_rows > 0) {
        $staffName = $staffResult->fetch_assoc()['full_name'];

        // Update both staff_id and staff_name
        $mysqli->query("
            UPDATE bookings 
            SET staff_id=$staffId, staff_name='$staffName' 
            WHERE id=$bookingId
        ");
    }

    header("Location: admin.php?tab=appointments");
    exit;
}


// Handle block/unblock actions using status column
if (isset($_GET['block_staff'])) {
    $id = intval($_GET['block_staff']);
    $mysqli->query("UPDATE staff SET status='inactive' WHERE staff_id=$id");
    header("Location: admin.php?tab=staff");
    exit;
}

if (isset($_GET['unblock_staff'])) {
    $id = intval($_GET['unblock_staff']);
    $mysqli->query("UPDATE staff SET status='active' WHERE staff_id=$id");
    header("Location: admin.php?tab=staff");
    exit;
}

// Handle Add Staff
if (isset($_POST['add_staff'])) {
    $fullName = $mysqli->real_escape_string($_POST['full_name']);
    $email = $mysqli->real_escape_string($_POST['email']);
    $status = $_POST['status'];
    $role = $_POST['role'];
    $username = $mysqli->real_escape_string($_POST['username']);
    
    // Hash the password
    $passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Insert into staff
    $mysqli->query("
        INSERT INTO staff (full_name, email, username, password_hash, role, status) 
        VALUES ('$fullName', '$email', '$username', '$passwordHash', '$role', '$status')
    ");

    header("Location: admin.php?tab=staff");
    exit;
}

// Handle Add Service
if (isset($_POST['add_service'])) {
    $name = $mysqli->real_escape_string($_POST['service_name']);
    $desc = $mysqli->real_escape_string($_POST['description']);
    $price = floatval($_POST['price']);
    $date = date('Y-m-d H:i:s');
    $mysqli->query("INSERT INTO services (service_name, description, price, created_at) VALUES ('$name', '$desc', $price, '$date')");
    header("Location: admin.php?tab=services");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Tulia Wellness Spa — Admin Dashboard</title>
<style>
/* Exact original CSS here, untouched */
:root{
  --glass-bg: rgba(255,255,255,0.08);
  --glass-border: rgba(255,255,255,0.15);
  --accent1: rgba(10,160,200,0.9);
  --accent2: rgba(40,200,150,0.9);
  --text: #e9f5f7;
  --muted: rgba(233,245,247,0.7);
  --danger: rgba(255,90,90,0.95);
}
*{box-sizing:border-box}
body{margin:0;font-family:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial;min-height:100vh;color:var(--text);background:url('https://i.pinimg.com/1200x/92/97/ca/9297ca62b5afade3dd4436de6f23da6f.jpg') center/cover fixed no-repeat;padding:30px;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;}
.wrap{display:flex;gap:24px;align-items:flex-start}
.sidebar{width:260px;padding:22px;border-radius:16px;background:linear-gradient(180deg, rgba(255,255,255,0.06), rgba(255,255,255,0.03));border:1px solid var(--glass-border);backdrop-filter: blur(12px) saturate(120%);box-shadow:0 8px 30px rgba(8,12,20,0.45);height:calc(100vh - 60px);overflow:auto;}
.brand{font-size:18px;font-weight:700;color:var(--text);display:flex;align-items:center;gap:12px;margin-bottom:16px}
.logo{width:44px;height:44px;border-radius:10px;background:linear-gradient(135deg,var(--accent1),var(--accent2));display:flex;align-items:center;justify-content:center;font-weight:700;color:white}
.nav{margin-top:12px}
.nav button{display:flex;align-items:center;width:100%;gap:10px;padding:10px 12px;border-radius:10px;border:none;background:transparent;color:var(--muted);cursor:pointer;text-align:left;font-weight:600;margin-bottom:6px;}
.nav button.active{background:linear-gradient(90deg, rgba(10,160,200,0.14), rgba(40,200,150,0.12));color:var(--text);}
.sidebar hr{border:0;border-top:1px solid rgba(255,255,255,0.04);margin:18px 0}
.small{font-size:13px;color:var(--muted)}
.main{flex:1;min-height:calc(100vh - 60px);display:flex;flex-direction:column;gap:18px}
.topbar{display:flex;align-items:center;justify-content:space-between;gap:20px}
.searchbox{flex:1;display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:12px;background:var(--glass-bg);border:1px solid var(--glass-border);backdrop-filter: blur(8px)}
.searchbox input{flex:1;background:transparent;border:0;color:var(--text);outline:none;font-size:15px}
.stats{display:flex;gap:14px}
.stat{min-width:160px;padding:14px;border-radius:12px;background:linear-gradient(180deg, rgba(255,255,255,0.04), rgba(255,255,255,0.02));border:1px solid var(--glass-border);backdrop-filter:blur(6px)}
.stat h3{margin:0;font-size:20px;color:var(--text)}
.stat p{margin:6px 0 0;font-size:13px;color:var(--muted)}
.panels{display:grid;grid-template-columns:1fr 420px;gap:18px}
.panel{padding:16px;border-radius:14px;background:var(--glass-bg);border:1px solid var(--glass-border);backdrop-filter: blur(12px)}
.panel h4{margin:0 0 8px;color:var(--text)}
table{width:100%;border-collapse:collapse}
th,td{padding:12px 10px;text-align:left;font-size:14px;color:var(--muted)}
th{font-size:13px;color:rgba(233,245,247,0.9);opacity:0.9}
tr{border-bottom:1px dashed rgba(255,255,255,0.03)}
.pill{display:inline-block;padding:6px 10px;border-radius:999px;background:rgba(255,255,255,0.04);font-weight:700;color:var(--text);font-size:13px}
.btn{padding:8px 12px;border-radius:8px;border:none;cursor:pointer;font-weight:700}
.btn.primary{background:linear-gradient(90deg,var(--accent1),var(--accent2));color:white}
.btn.ghost{background:transparent;border:1px solid rgba(255,255,255,0.06);color:var(--muted)}
.btn.danger{background:var(--danger);color:white}
.btn.small{padding:6px 8px;font-size:13px}
.actions{display:flex;gap:8px}
.modal-backdrop{position:fixed;inset:0;background:rgba(3,6,8,0.5);display:none;align-items:center;justify-content:center}
.modal{width:720px;max-width:95%;background:linear-gradient(180deg, rgba(255,255,255,0.05), rgba(255,255,255,0.03));border:1px solid var(--glass-border);padding:18px;border-radius:14px;color:var(--text);backdrop-filter: blur(12px)}
.modal header{display:flex;justify-content:space-between;align-items:center}
@media (max-width:980px){.wrap{flex-direction:column}.sidebar{width:100%;height:auto}.panels{grid-template-columns:1fr}}
.muted{color:var(--muted)}
.nowrap{white-space:nowrap}
</style>
</head>
<body>
<div class="wrap">
<aside class="sidebar">
<div class="brand"><div class="logo">TW</div><div>
<div style="font-size:14px;">Tulia Wellness Spa</div>
<div class="small">Administrator</div>
</div></div>
<div class="nav">
<button class="active" onclick="showTab('dashboard', this)">Dashboard</button>
<button onclick="showTab('appointments', this)">Appointments</button>
<button onclick="showTab('staff', this)">Staff</button>
<button onclick="showTab('services', this)">Services</button>
<button onclick="showTab('clients', this)">Clients</button>
<button onclick="showTab('settings', this)">Settings</button>
</div>
<hr />
<div class="small">Quick actions</div>
<div style="display:flex;gap:8px;margin-top:10px">
<button class="btn primary" onclick="openModal('addStaffModal')">+ Add Staff</button>
<button class="btn ghost" onclick="openModal('addServiceModal')">+ Add Service</button>
</div>
<hr />
<div class="small">Account</div>
<div style="margin-top:10px;display:flex;gap:8px">
<button class="btn ghost" onclick="logout()">Log out</button>
</div>
</aside>

<main class="main">
<div class="topbar">
<div class="searchbox">
<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><circle cx="11" cy="11" r="6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></circle></svg>
<input id="search" placeholder="Search clients, appointments, staff..." oninput="renderActiveTab()" />
</div>
<div style="display:flex;align-items:center;gap:12px">
<div class="pill" id="todayDate"></div>
<div style="text-align:right">
<div style="font-size:13px">Admin</div>
<div class="small muted">tulia@spa.example</div>
</div>
</div>
</div>

<!-- Tabs -->
<div id="dashboard" class="tab">
<div class="stats">
<div class="stat"><h3 id="statBookings">0</h3><p>Bookings Today</p></div>
<div class="stat"><h3 id="statRevenue">KSH 0</h3><p>Today's Revenue</p></div>
<div class="stat"><h3 id="statStaff">0</h3><p>Active Staff</p></div>
</div>
<div class="panels" style="margin-top:16px">
<section class="panel">
<h4>Recent Appointments</h4>
<div id="recentAppointments"></div>
</section>
<aside class="panel">
<h4>Quick Overview</h4>
<div style="margin-top:8px">
<div class="small">Popular Services</div>
<div id="popularServices" style="margin-top:8px"></div>
</div>
<hr style="border:none;border-top:1px solid rgba(255,255,255,0.03);margin:12px 0">
<div class="small">System</div>
<div style="margin-top:8px;font-size:13px;color:var(--muted)">Backup: <span id="backupStatus">OK</span></div>
</aside>
</div>
</div>

<!-- Appointments Table -->
<div id="appointments" class="tab" style="display:none">
<div class="panel" style="margin-bottom:12px">
<h4>All Appointments</h4>
<table>
<thead>
<tr><th>Client</th><th>Service</th><th>Staff</th><th>Time</th><th>Status</th><th>Actions</th></tr>
</thead>
<tbody id="appointmentsTable">
<?php foreach ($bookings as $b): ?>
<tr>
<td><?= htmlspecialchars($b['client_name']) ?></td>
<td><?= htmlspecialchars($b['service']) ?></td>
<td><?= htmlspecialchars($b['staff_name'] ?: '—') ?></td>
<td><?= $b['booking_date'] . ' ' . $b['booking_time'] ?></td>
<td><?= $b['status'] ?></td>
<td class="actions">
<?php if ($b['status'] !== "approved"): ?>
<a href="admin.php?approve=<?= $b['id'] ?>" class="btn small primary">Approve</a>
<?php endif; ?>
<form method="POST" style="display:inline-flex;gap:6px">
<input type="hidden" name="id" value="<?= $b['id'] ?>">
<select name="staff_id" style="padding:6px;border-radius:6px;">
<?php foreach ($staff as $s): ?>
<option value="<?= $s['staff_id'] ?>"><?= htmlspecialchars($s['full_name']) ?></option>
<?php endforeach; ?>
</select>
<button name="assign_staff" class="btn small ghost" type="submit">Assign</button>
</form>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
</div>


<!-- Staff Table -->
<div id="staff" class="tab" style="display:none">
  <div class="panel" style="margin-bottom:12px; overflow-x:auto;">
    <h4>All Staff</h4>
    <table style="width:100%; table-layout:auto; min-width:600px;">
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $allStaff = $mysqli->query("SELECT * FROM staff")->fetch_all(MYSQLI_ASSOC);
        foreach ($allStaff as $s):
        ?>
        <tr>
          <td><?= htmlspecialchars($s['full_name']) ?></td>
          <td><?= htmlspecialchars($s['email']) ?></td>
          <td><?= htmlspecialchars(ucfirst($s['status'])) ?></td>
          <td class="actions">
            <?php if ($s['status'] === 'active'): ?>
              <a href="admin.php?block_staff=<?= $s['staff_id'] ?>" class="btn small danger">Block</a>
            <?php else: ?>
              <a href="admin.php?unblock_staff=<?= $s['staff_id'] ?>" class="btn small primary">Unblock</a>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Services Table -->
<div id="services" class="tab" style="display:none">
  <div class="panel" style="margin-bottom:12px; overflow-x:auto;">
    <h4>All Services</h4>
    <table style="width:100%; table-layout:auto; min-width:600px;">
      <thead>
        <tr>
          <th>Service Name</th>
          <th>Description</th>
          <th>Price (KSH)</th>
          <th>Created</th>
        </tr>
      </thead>
      <tbody>
        <?php
        // Fetch all services
        $allServices = $mysqli->query("SELECT * FROM services ORDER BY service_name ASC")->fetch_all(MYSQLI_ASSOC);
        foreach ($allServices as $svc):
        ?>
        <tr>
          <td><?= htmlspecialchars($svc['service_name']) ?></td>
          <td><?= htmlspecialchars($svc['description'] ?? '—') ?></td>
          <td><?= htmlspecialchars($svc['price']) ?></td>
          <td><?= htmlspecialchars($svc['created_at']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Clients Table -->
<div id="clients" class="tab" style="display:none">
  <div class="panel" style="margin-bottom:12px; overflow-x:auto;">
    <h4>All Clients</h4>
    <table style="width:100%; table-layout:auto; min-width:600px;">
      <thead>
        <tr>
          <th>Email</th>
          <th>Role</th>
        </tr>
      </thead>
      <tbody>
        <?php
        // Fetch users (clients)
        $allUsers = $mysqli->query("SELECT email, role FROM users ORDER BY email ASC")->fetch_all(MYSQLI_ASSOC);

        foreach ($allUsers as $u):
        ?>
        <tr>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td><?= htmlspecialchars(ucfirst($u['role'])) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Settings Tab -->
<div id="settings" class="tab" style="display:none">
  <div class="panel" style="margin-bottom:12px; padding-bottom:20px;">

    <h4>System Settings</h4>

    <div style="margin-top:12px;">

      <!-- App Info -->
      <div style="margin-bottom:20px;">
        <div class="small" style="font-size:14px; color:var(--muted);">Application Information</div>
        <div style="margin-top:10px; padding:14px; border-radius:12px;
          background:rgba(255,255,255,0.04); border:1px solid var(--glass-border);">
          <p style="margin:0; font-size:14px; color:var(--text);">
            <strong>System Name:</strong> Tulia Wellness Spa Booking System
          </p>
          <p style="margin:6px 0 0; font-size:14px; color:var(--muted);">Version: 1.0.0</p>
        </div>
      </div>

      <!-- Admin Profile -->
      <div style="margin-bottom:20px;">
        <div class="small" style="font-size:14px; color:var(--muted);">Admin Profile</div>
        <div style="margin-top:10px; padding:14px; border-radius:12px;
          background:rgba(255,255,255,0.04); border:1px solid var(--glass-border);">
          <p style="margin:0; font-size:14px; color:var(--text);">
            <strong>Admin Email:</strong> tulia@spa.example
          </p>
        </div>
      </div>

      <!-- System Options -->
      <div style="margin-bottom:20px;">
        <div class="small" style="font-size:14px; color:var(--muted);">System Options</div>
        <div style="margin-top:10px; padding:14px; border-radius:12px;
          background:rgba(255,255,255,0.04); border:1px solid var(--glass-border);">
          <button class="btn small ghost" style="margin-bottom:8px; width:180px;">Clear Logs</button><br>
          <button class="btn small ghost" style="width:180px;">Backup Database</button>
        </div>
      </div>

      <!-- Footer Info -->
      <div style="text-align:center; margin-top:20px;">
        <div class="small" style="font-size:12px; color:var(--muted);">
          © 2025 Tulia Wellness Spa — Admin Panel
        </div>
      </div>

    </div>

  </div>
</div>
</main>
</div>

<div id="modalBackdrop" class="modal-backdrop">
<div class="modal" role="dialog">
<header><div id="modalTitle">Modal</div><button class="btn ghost" onclick="closeModal()">Close</button></header>
<div id="modalContent" style="margin-top:12px"></div>
</div>
</div>

<script>
// JS arrays fixed
const DB = {
  staff: <?= json_encode($staff) ?>,
  services: <?= json_encode($services) ?>,
  clients: <?= json_encode($clients) ?>,
  appointments: <?= json_encode($bookings) ?>
};

document.getElementById('todayDate').textContent =
  new Date().toLocaleString(undefined,{weekday:'short',month:'short',day:'numeric'});

function showTab(id, btn){
  document.querySelectorAll('.nav button').forEach(b=>b.classList.remove('active'));
  if(btn) btn.classList.add('active');
  document.querySelectorAll('.tab').forEach(t=>t.style.display='none');
  document.getElementById(id).style.display='block';
}

function logout(){ window.location.href='login.php'; }
//function openModal(templateId,title){ document.getElementById('modalBackdrop').style.display='flex'; }
function closeModal(){ document.getElementById('modalBackdrop').style.display='none'; }

function openModal(templateId,title){
  const tpl = document.getElementById(templateId);
  document.getElementById('modalContent').innerHTML = tpl ? tpl.innerHTML : '';
  document.getElementById('modalTitle').textContent = title || 'Modal';
  document.getElementById('modalBackdrop').style.display='flex';
}

// Add Service
document.getElementById('addServiceForm')?.addEventListener('submit', function(e){
  e.preventDefault();
  const form = e.target;
  const data = new FormData(form);
  fetch('admin.php', {
    method:'POST',
    body: data
  }).then(res => res.text())
    .then(_=>{
      const msg = document.getElementById('serviceSuccessMsg');
      msg.textContent = 'Service added successfully!';
      msg.style.display = 'block';
      form.reset();
    });
});

// Add Staff
document.getElementById('addStaffForm')?.addEventListener('submit', function(e){
  e.preventDefault();
  const form = e.target;
  const data = new FormData(form);
  fetch('admin.php', {
    method:'POST',
    body: data
  }).then(res => res.text())
    .then(_=>{
      const msg = document.getElementById('staffSuccessMsg');
      msg.textContent = 'Staff added successfully!';
      msg.style.display = 'block';
      form.reset();
    });
});

function renderRecentAppointments(){
  const el = document.getElementById('recentAppointments'); el.innerHTML='';
  DB.appointments.slice(0,5).forEach(a=>{
    const div = document.createElement('div');
    div.textContent = `${a.client_name} — ${a.service} on ${a.booking_date} ${a.booking_time}`;
    el.appendChild(div);
  });
  if(DB.appointments.length===0) el.textContent='No recent appointments';
}

</script>

<!-- Add Staff Modal -->
<template id="addStaffModal">
  <h4>Add New Staff</h4>
  <form method="POST" id="addStaffForm">
    <label>Full Name</label><br>
    <input type="text" name="full_name" required style="width:100%;padding:8px;margin-bottom:8px;"><br>

    <label>Username</label><br>
    <input type="text" name="username" required style="width:100%;padding:8px;margin-bottom:8px;"><br>

    <label>Email</label><br>
    <input type="email" name="email" required style="width:100%;padding:8px;margin-bottom:8px;"><br>

    <label>Password</label><br>
    <input type="password" name="password" required style="width:100%;padding:8px;margin-bottom:8px;"><br>

    <label>Role</label><br>
    <select name="role" required style="width:100%;padding:8px;margin-bottom:12px;">
      <option value="staff">Staff</option>
    </select>

     <label>Status</label><br>
    <select name="status" required style="width:100%;padding:8px;margin-bottom:12px;">
      <option value="active">Active</option>
      <option value="inactive">Inactive</option>
    </select>

    <div id="staffSuccessMsg" style="color:limegreen;margin-bottom:8px;display:none;"></div>

    <button type="submit" name="add_staff" class="btn primary" style="width:100%;">Add Staff</button>
  </form>
</template>

<!-- Add Service Modal -->
<template id="addServiceModal">
  <h4>Add New Service</h4>
  <form method="POST" id="addServiceForm">
    <label>Service Name</label><br>
    <input type="text" name="service_name" required style="width:100%;padding:8px;margin-bottom:8px;"><br>
    
    <label>Description</label><br>
    <textarea name="description" style="width:100%;padding:8px;margin-bottom:8px;"></textarea><br>
    
    <label>Price (KSH)</label><br>
    <input type="number" name="price" required style="width:100%;padding:8px;margin-bottom:12px;"><br>

    <div id="serviceSuccessMsg" style="color:limegreen;margin-bottom:8px;display:none;"></div>

    <button type="submit" name="add_service" class="btn primary" style="width:100%;">Add Service</button>
  </form>
</template>

</body>
</html>

