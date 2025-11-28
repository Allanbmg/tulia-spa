<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tulia Wellness Spa – Login</title>
<style>
body { margin:0; padding:0; font-family:"Segoe UI",sans-serif; background:linear-gradient(rgba(0,0,0,0.35),rgba(0,0,0,0.35)), url('https://i.pinimg.com/1200x/48/0f/f8/480ff8795adb6e5e5fe563c064d731c8.jpg'); background-size:cover; display:flex; justify-content:center; align-items:center; height:100vh;}
.login-container { background: rgba(255,255,255,0.15); padding:40px; border-radius:18px; width:350px; box-shadow:0 8px 20px rgba(0,0,0,0.3); backdrop-filter:blur(10px); text-align:center;}
.login-container h2 { color:#fff; font-size:28px; margin-bottom:20px;}
.login-container input, .login-container select { width:100%; padding:12px; margin:10px 0; border:none; border-radius:10px; outline:none;}
.btn { width:100%; padding:12px; border:none; background:#5bbba8; color:white; border-radius:10px; cursor:pointer; font-size:16px; margin-top:15px;}
.btn:hover { background:#48a694;}
.signup-link { margin-top:15px; color:#fff; font-size:14px;}
.signup-link a { color:#5bbba8; text-decoration:none; font-weight:bold;}
.signup-link a:hover { text-decoration:underline;}
</style>
</head>
<body>
<div class="login-container">
<h2>Tulia Wellness Spa</h2>
<form id="loginForm">
<select name="role" id="roleSelect" required>
<option value="" disabled selected>Select Role</option>
<option value="admin">Admin</option>
<option value="staff">Staff</option>
<option value="client">Client</option>
</select>
<input type="email" name="email" placeholder="Email Address" required />
<input type="password" name="password" placeholder="Password" required />
<button class="btn" type="submit">Login</button>
</form>
<div class="signup-link">
Don't have an account? <a href="signup.html">Create One</a>
</div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', function(e){
  e.preventDefault();
  const role = document.getElementById('roleSelect').value;
  if(role === 'admin') window.location.href='admin.html';
  else if(role === 'staff') window.location.href='staff.html';
  else if(role === 'client') window.location.href='client.html';
  else alert('Please select a valid role.');
});
</script>
</body>
</html>
