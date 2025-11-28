<?php
// admin.php
$mysqli = new mysqli("localhost", "root", "", "spa_system");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Only fetch bookings for now
$bookings = $mysqli->query("SELECT * FROM bookings ORDER BY booking_date DESC, booking_time DESC")->fetch_all(MYSQLI_ASSOC);

// Use empty arrays for other tables so JS doesn't break
$staff = [];
$services = [];
$clients = [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Tulia Wellness Spa — Admin Dashboard</title>
<style>
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

<div id="appointments" class="tab" style="display:none">
<div class="panel" style="margin-bottom:12px">
<h4>All Appointments</h4>
<table>
<thead><tr><th>Client</th><th>Service</th><th>Staff</th><th>Time</th><th>Status</th></tr></thead>
<tbody id="appointmentsTable"></tbody>
</table>
</div>
</div>

<!-- Staff, Services, Clients tabs remain but empty -->
<div id="staff" class="tab" style="display:none"><div class="panel"><h4>Staff Members</h4><table><tbody id="staffTable"></tbody></table></div></div>
<div id="services" class="tab" style="display:none"><div class="panel"><h4>Services</h4><table><tbody id="servicesTable"></tbody></table></div></div>
<div id="clients" class="tab" style="display:none"><div class="panel"><h4>Clients</h4><table><tbody id="clientsTable"></tbody></table></div></div>
</main>
</div>

<!-- Modal -->
<div id="modalBackdrop" class="modal-backdrop">
<div class="modal" role="dialog">
<header><div id="modalTitle">Modal</div><button class="btn ghost" onclick="closeModal()">Close</button></header>
<div id="modalContent" style="margin-top:12px"></div>
</div>
</div>

<script>
// Data object
const DB = {
  staff: <?php echo json_encode($staff); ?>,
  services: <?php echo json_encode($services); ?>,
  clients: <?php echo json_encode($clients); ?>,
  appointments: <?php echo json_encode($bookings); ?>,
};

// Date display
document.getElementById('todayDate').textContent = new Date().toLocaleString(undefined,{weekday:'short',month:'short',day:'numeric'});

// Tab switching
function showTab(id, btn){
  document.querySelectorAll('.nav button').forEach(b=>b.classList.remove('active'));
  if(btn) btn.classList.add('active');
  document.querySelectorAll('.tab').forEach(t=>t.style.display='none');
  document.getElementById(id).style.display='block';
  renderActiveTab();
}

function logout(){ window.location.href='login.php'; }
function openModal(templateId,title){ alert('Modal functionality maintained'); }
function closeModal(){ document.getElementById('modalBackdrop').style.display='none'; }

// Rendering
function renderActiveTab(){
  const active = document.querySelector('.nav button.active').textContent.toLowerCase();
  if(active.includes('dashboard')) renderDashboard();
  else if(active.includes('appointment')) renderAppointmentsTable();
  else if(active.includes('staff')) renderStaff();
  else if(active.includes('service')) renderServices();
  else if(active.includes('client')) renderClients();
}

function renderDashboard(){
  const today = (new Date()).toISOString().slice(0,10);
  document.getElementById('statBookings').textContent = DB.appointments.filter(a=>a.booking_date===today).length;
  const revenue = DB.appointments.reduce((s,a)=>s + (parseInt(a.price)||0),0);
  document.getElementById('statRevenue').textContent = 'KSH ' + revenue;
  document.getElementById('statStaff').textContent = DB.staff.length;
  renderRecentAppointments();
  renderPopular();
}

function renderAppointmentsTable(){
  const tbody = document.getElementById('appointmentsTable'); tbody.innerHTML='';
  const q = document.getElementById('search').value.toLowerCase();
  DB.appointments.forEach(a=>{
    if(q && !(a.client_name.toLowerCase().includes(q) || a.service.toLowerCase().includes(q) || (a.staff_name && a.staff_name.toLowerCase().includes(q)))) return;
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${a.client_name}</td><td>${a.service}</td><td>${a.staff_name||'—'}</td><td>${a.booking_date} ${a.booking_time}</td><td>${a.status}</td>`;
    tbody.appendChild(tr);
  });
}

function renderRecentAppointments(){
  const el = document.getElementById('recentAppointments'); el.innerHTML='';
  DB.appointments.slice(0,5).forEach(a=>{
    const div = document.createElement('div');
    div.textContent = `${a.client_name} — ${a.service} on ${a.booking_date} ${a.booking_time}`;
    el.appendChild(div);
  });
  if(DB.appointments.length===0) el.textContent='No recent appointments';
}

function renderPopular(){
  const map={};
  DB.appointments.forEach(a=>map[a.service]=(map[a.service]||0)+1);
  const arr = Object.keys(map).map(k=>({name:k,count:map[k]})).sort((a,b)=>b.count-a.count).slice(0,3);
  const el = document.getElementById('popularServices'); el.innerHTML='';
  arr.forEach(x=>{ const d = document.createElement('div'); d.textContent=`${x.name} — ${x.count} bookings`; el.appendChild(d); });
  if(arr.length===0) el.textContent='No data yet';
}

// Empty renders for other tabs
function renderStaff(){ const tbody=document.getElementById('staffTable'); tbody.innerHTML='No staff data yet'; }
function renderServices(){ const tbody=document.getElementById('servicesTable'); tbody.innerHTML='No services data yet'; }
function renderClients(){ const tbody=document.getElementById('clientsTable'); tbody.innerHTML='No clients data yet'; }

// Initial render
renderActiveTab();
</script>
</body>
</html>
