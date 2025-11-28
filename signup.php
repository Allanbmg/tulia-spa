<?php
// Show all errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'database.php';

// Handle form submission
if(isset($_POST['signup'])) {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password_raw = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if($password_raw !== $confirm_password){
        $error = "Passwords do not match!";
    } else {
        $password = password_hash($password_raw, PASSWORD_DEFAULT);
        $role = 'client';

        // Check if email already exists
        $check = "SELECT * FROM users WHERE email='$email'";
        $result = mysqli_query($conn, $check);

        if(mysqli_num_rows($result) > 0) {
            $error = "Email already exists!";
        } else {
            // Insert new client
            $sql = "INSERT INTO users (full_name, email, password, role) 
                    VALUES ('$full_name', '$email', '$password', '$role')";

            if(mysqli_query($conn, $sql)) {
                // Redirect to dashboard after successful signup
                header("Location: client.html");
                exit();
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
        }
    }
}

// Optional: You can store $error in session to show on signup.html if needed
?>
