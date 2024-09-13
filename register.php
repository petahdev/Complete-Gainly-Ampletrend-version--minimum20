<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connect.php'; // Make sure this file contains your database connection code

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['register'])) {
        // Registration
        $username = $_POST['username'];
        $email = $_POST['useremail'];
        $password = $_POST['password'];
        $confirmpassword = $_POST['confirmpassword'];
        $mobilenumber = $_POST['mobilenumber'];

        // Check if passwords match
        if ($password !== $confirmpassword) {
            echo "Passwords do not match.";
            exit();
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo "Email already registered.";
            exit();
        }

        $stmt->close();

        // Prepare SQL statement for insertion
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, mobilenumber) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $hashedPassword, $mobilenumber);

        if ($stmt->execute()) {
            // Successful registration, redirect to login page
            header("Location: index.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } elseif (isset($_POST['login'])) {
        // Login
        $email = $_POST['email']; // Email is used for login
        $password = $_POST['password'];

        // Debugging statements
        echo "Email: " . $email . "<br>";
        echo "Password: " . $password . "<br>";

        // Prepare SQL statement to fetch user
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($userId, $hashedPassword);
            $stmt->fetch();

            // Verify the password
            if (password_verify($password, $hashedPassword)) {
                // Password is correct, set session variables and redirect
                $_SESSION['user_id'] = $userId;
                $_SESSION['email'] = $email;
                header("Location: dashboard.php");
                exit();
            } else {
                echo "Invalid credentials.";
            }
        } else {
            echo "No user found.";
        }

        $stmt->close();
    }
}

$conn->close();
?>
