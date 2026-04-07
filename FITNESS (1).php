<?php
session_start();
require_once 'config.php';

// Clear previous errors
$login_error = isset($_SESSION['login_error']) ? $_SESSION['login_error'] : '';
$register_error = isset($_SESSION['register_error']) ? $_SESSION['register_error'] : '';
$active_form = isset($_SESSION['active_form']) ? $_SESSION['active_form'] : 'login';
unset($_SESSION['login_error'], $_SESSION['register_error'], $_SESSION['active_form']);

// ===== SIGNUP =====
if (isset($_POST['signup'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $checkmail = $conn->query("SELECT email FROM users WHERE email = '$email'");
    if ($checkmail->num_rows > 0) {
        $_SESSION['register_error'] = 'Email is already registered!';
        $_SESSION['active_form'] = 'register';
    } else {
        $conn->query("INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')");
        $_SESSION['login_error'] = "Account created! Please login.";
        $_SESSION['active_form'] = 'login';
    }
    header("Location: FITNESS.php");
    exit();
}

// ===== LOGIN =====
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE email = '$email'");

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($pass, $user['password'])) {
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            header("Location: user_page.php");
            exit();
        } else {
            $_SESSION['login_error'] = 'Incorrect password';
        }
    } else {
        $_SESSION['login_error'] = 'Email not found';
    }
    $_SESSION['active_form'] = 'login';
    header("Location: FITNESS.php");
    exit();
}
?>