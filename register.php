<?php 
require_once('db_init.php');
require_once('functions.php');
session_start();
session_regenerate_id();

$message = "";
if (isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["password_confirm"])) {
  $email = $_POST["email"];
  if ($_POST["password"] !== $_POST["password_confirm"]) {
    $message = "those passwords do not match. please re-enter!";
  } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $message = "invalid email. please re-enter!";
  } else {
    $message = register_user($email, $_POST["password"]);
    if ($message === "success") {
      # successful registration, redirect to main page
      $message = "user successfully created";
      $_SESSION["email"] = $email;
      header('Location: index.php');
      die();
    }
  }
}
?>

<html>
<head>
<title>Register</title>
</head>
<body>
<h1>register for the shindig</h1>
<?php if (!empty($message)): ?>
  <p><?php echo $message; ?></p>
<?php endif; ?>
<form action="register.php" method="post">
  <p>Enter your email address:</p>
  <input type="text" name="email" />
  <p>Enter your password:</p>
  <input type="password" name="password" />
  <p>Confirm your password:</p>
  <input type="password" name="password_confirm" />
  <br /><br />
  <input type="submit" value="Sign up" />
</form>