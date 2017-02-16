<?php 
require_once('db_init.php'); 
require_once('functions.php');
session_start();
session_regenerate_id();
?>

<html>
<head>
<title>shinding</title>
</head>
<body>
<?php 
# check if they're trying to log in
if (!isset($_SESSION["email"])) {
  $message = "";
  if (isset($_POST["email"]) && isset($_POST["password"])) {
    $message = login($_POST["email"], $_POST["password"]);
  }
  if ($message === "success") {
    $_SESSION["email"] = $_POST["email"];
  } else {
    unset($_SESSION["email"]);
    print_login_form($message);
    die();
  }
} ?>
<p>you are logged in as user <?php echo $_SESSION["email"]; ?></p>
<?php if (is_active_user($_SESSION["email"]) !== "success"): ?>
<p>you need to activate your account. please check your email.</p>
<?php else: ?>
<p>your account is active!</p>
<?php endif; ?>
<p><a href="logout.php">log out</a></p>
</body>
</html>
