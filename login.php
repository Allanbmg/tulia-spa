<?php
session_start();
include 'database.php';  // Your DB connection

$error = '';

if (isset($_POST['login'])) {

    $role = $_POST['role'];
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = trim($_POST['password']);

    // Determine which table and fields to use
    if ($role === 'client') {
        $table = 'users';
        $id_field = 'id';
        $password_field = 'password';
    } else if ($role === 'staff' || $role === 'admin') {
        $table = 'staff';
        $id_field = 'staff_id';
        $password_field = 'password_hash';
    } else {
        $error = "Invalid role selected!";
    }


    if (!$error) {
        // Add role filter for staff/admin
        if ($role === 'staff' || $role === 'admin') {
            $sql = "SELECT * FROM staff WHERE email='$email' AND role='$role' LIMIT 1";
        } else {
            $sql = "SELECT * FROM users WHERE email='$email' LIMIT 1";
        }

        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);

            if (password_verify($password, $user[$password_field])) {
                $_SESSION['role'] = $role;
                if ($role === 'client') {
                    $_SESSION['user_id'] = $user[$id_field];
                    header("Location: client.php");
                    exit();
                } else if ($role === 'staff') {
                    $_SESSION['staff_id'] = $user[$id_field];
                    header("Location: staff.php");
                    exit();
                } else if ($role === 'admin') {
                    $_SESSION['admin_id'] = $user[$id_field];
                    header("Location: admin.php");
                    exit();
                }
            } else {
                $error = "Email or password is incorrect!";
            }
        } else {
            $error = "Email or password is incorrect!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tulia Wellness Spa – Login</title>

<style>
body { margin:0; padding:0; font-family:"Segoe UI",sans-serif;
background:linear-gradient(rgba(0,0,0,0.35),rgba(0,0,0,0.35)),
url('https://i.pinimg.com/1200x/48/0f/f8/480ff8795adb6e5e5fe563c064d731c8.jpg');
background-size:cover; display:flex; justify-content:center; align-items:center; height:100vh;}

.login-container { background: rgba(255,255,255,0.15); padding:40px;
border-radius:18px; width:350px; box-shadow:0 8px 20px rgba(0,0,0,0.3);
backdrop-filter:blur(10px); text-align:center;}

.login-container h2 { color:#fff; font-size:28px; margin-bottom:20px;}
.login-container input, .login-container select {
width:100%; padding:12px; margin:10px 0; border:none; border-radius:10px; outline:none;}

.btn { width:100%; padding:12px; border:none; background:#5bbba8;
color:white; border-radius:10px; cursor:pointer; font-size:16px; margin-top:15px;}
.btn:hover { background:#48a694;}

.signup-link { margin-top:15px; color:#fff; font-size:14px;}
.signup-link a { color:#5bbba8; text-decoration:none; font-weight:bold;}
.signup-link a:hover { text-decoration:underline;}

.error { color:red; margin-bottom:10px; font-weight:bold; }
</style>
</head>
<body>

<div class="login-container">
<h2>Tulia Wellness Spa</h2>

<?php if ($error) { echo "<p class='error'>$error</p>"; } ?>

<form method="POST" action="">
<select name="role" required>
    <option value="" disabled selected>Select Role</option>
    <option value="admin">Admin</option>
    <option value="staff">Staff</option>
    <option value="client">Client</option>
</select>

<input type="email" name="email" placeholder="Email Address" required />
<input type="password" name="password" placeholder="Password" required />

<button class="btn" type="submit" name="login">Login</button>
</form>

<div class="signup-link">
Don't have an account? <a href="signup.html">Create One</a>
</div>
</div>

</body>
</html>
