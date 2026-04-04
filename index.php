<?php
session_start();
require_once 'config.php';

$activeForm = isset($_SESSION['active_form']) ? $_SESSION['active_form'] : 'login';
$errors = [
    'login' => isset($_SESSION['login_error']) ? $_SESSION['login_error'] : '',
    'signup' => isset($_SESSION['SignUp_error']) ? $_SESSION['SignUp_error'] : ''
];

function showError($msg) {
    return $msg ? "<div class='error-message'>$msg</div>" : '';
}

unset($_SESSION['login_error'], $_SESSION['SignUp_error']);

//LOGIN 
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE email='$email'");
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($pass, $user['password'])) {
            $_SESSION['name'] = $user['name'];
            header("Location: Mainpage.html");
            exit();
        } else {
            $_SESSION['login_error'] = "Wrong password";
        }
    } else {
        $_SESSION['login_error'] = "Email not found";
    }
    $_SESSION['active_form'] = 'login';
}

//SIGNUP
if (isset($_POST['signup'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = $conn->query("SELECT * FROM users WHERE email='$email'");
    if ($check->num_rows > 0) {
        $_SESSION['SignUp_error'] = "Email already exists";
        $_SESSION['active_form'] = 'signup';
    } else {
        $conn->query("INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$pass')");
        $_SESSION['login_error'] = "Account created! Please login.";
        $_SESSION['active_form'] = 'login';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login & Sign Up</title>
<style>
*,
*:after{
    padding: 0;
    margin: 0;
    box-sizing: border-box;
}
body {
  margin: 0;
  height: 100vh;
  background: url("web3.jpg") center/cover no-repeat fixed;
  overflow: hidden;
  font-weight: bold;
}

/* LOGIN FORM */
.login-box{
    min-height: 520px;
    width: 400px;
    background-color: rgba(255,255,255,0.13);
    position: absolute;
    top: 50%;
    left: 25%;
    transform: translateY(-50%);
    border-radius: 10px;
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255,255,255,0.1);
    box-shadow: 0 0 40px rgba(8,7,16,0.6);
    padding: 50px 35px;
    animation: slideInLeft 1s ease forwards;
}

/*SIGNUP FORM*/
.register-box{
    min-height: 720px;
    width: 450px;
    background-color: rgba(255,255,255,0.13);
    position: absolute;
    top: 50%;
    left: 20%;
    transform: translateY(-50%);
    border-radius: 10px;
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255,255,255,0.1);
    box-shadow: 0 0 40px rgba(8,7,16,0.6);
    padding: 50px 35px;
    animation: slideInRight 1s ease forwards;
}

@keyframes slideInLeft {
    from { transform: translate(-100%, -50%); opacity: 0; }
    to { transform: translate(50%, -50%); opacity: 1; }
}
@keyframes slideInRight {
    from { transform: translate(100%, -50%); opacity: 0; }
    to { transform: translate(50%, -50%); opacity: 1; }
}

form *{
    font-family: 'Times New Roman', Times, serif;
    color: #ffffff;
    letter-spacing: 0.5px;
    outline: none;
    border: none;
}
form h3{
    font-size: 32px;
    text-align: center;
}
label{
    display: block;
    margin-top: 30px;
}
input{
    display: block;
    height: 50px;
    width: 100%;
    background-color: rgba(255,255,255,0.07);
    border-radius: 3px;
    padding: 0 10px;
    margin-top: 8px;
}
::placeholder{ color: white; }

button {
  margin-top: 50px;
  width: 100%;
  background-color: orange;
  color: white;
  padding: 15px 0;
  font-size: 18px;
  border-radius: 5px;
  cursor: pointer;
}
button:hover { background-color: darkorange; }

.social {
  margin-top: 30px;
  display: flex;
  gap: 10px;
}
.social div {
  width: 150px;
  height: 40px;
  background-size: contain;
  background-repeat: no-repeat;
  cursor: pointer;
}
.social .fb { background-image: url("facebook.png"); }
.social .go { background-image: url("google.png"); }
.social .in { background-image: url("instagram.png"); }

/* Hide signup initially */
.register-box { display: <?= $activeForm=='signup' ? 'block' : 'none'; ?>; }
.login-box { display: <?= $activeForm=='login' ? 'block' : 'none'; ?>; }
.error-message { padding: 12px; background: #f8d7da; border-radius: 6px; color: #a42834; margin-bottom: 20px; text-align: center; }
</style>
</head>

<body>

<!-- LOGIN PAGE -->
<div class="login-box">
<form action="" method="post">
    <h3>Login Here</h3>
    <?= showError($errors['login']); ?>
    <label>Username</label>
    <input type="text" name="email" placeholder="Email or Phone" required>
    <label>Password</label>
    <input type="password" name="password" id="loginPassword" placeholder="Password" required>
    <span onclick="togglePassword('loginPassword')">👁️</span>
    <button type="submit" name="login">Log In</button>

    <div class="social">
      <div class="go"></div>
      <div class="fb"></div>
      <div class="in"></div>
      <a href="#" onclick="showSignup(); return false;">Create Account</a>
    </div>
</form>
</div>

<!--SIGNUP PAGE-->
<div class="register-box">
<form action="" method="post">
    <h3>Create Account</h3>
    <?= showError($errors['signup']); ?>
    <label>Username</label>
    <input type="text" name="name" placeholder="Username" required>
    <label>Email Address</label>
    <input type="text" name="email" placeholder="Email" required>
    <label>Password</label>
    <input type="password" name="password" id="signupPassword" placeholder="Password" required>
    <span onclick="togglePassword('signupPassword')">👁️</span>
    <button type="submit" name="signup">Sign Up</button>

    <div class="social">
      <div class="go"></div>
      <div class="fb"></div>
      <div class="in"></div>
      <a href="#" onclick="showLogin(); return false;">Already have account</a>
    </div>
</form>
</div>

<script>
function togglePassword(id) {
  const field = document.getElementById(id);
  field.type = field.type === "password" ? "text" : "password";
}
function showSignup() {
    document.querySelector('.login-box').style.display = 'none';
    document.querySelector('.register-box').style.display = 'block';
}
function showLogin() {
    document.querySelector('.register-box').style.display = 'none';
    document.querySelector('.login-box').style.display = 'block';
}
</script>

</body>
</html>