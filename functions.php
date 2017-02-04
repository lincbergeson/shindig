<?php
require_once('db_init.php');
function login($email, $password) {
  global $dbh;
  $message = "please log in below";
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $message = "invalid email address. please re-enter!";
  } else {
    $passhash = hash("sha512", $password);
    try {
      $query = $dbh->prepare("select * from users where Email = ? and HashedPassword = ?;");
      $query->bindParam(1, $email);
      $query->bindParam(2, $passhash);
      $query->execute();
      if ($query->fetch()) {
        $message = "success";
      } else {
        $message = "user not found. please re-enter!";
      }
    } catch (Exception $e) {
      $message = "Failed: {$e->getMessage()}";
    }
  }
  return $message;
}

function print_login_form($message) { ?>
  <h1>welcome to the shindig</h1>
  <p><?php echo $message; ?></p>
  <form action="index.php" method="post">
    <p>Email address:</p>
    <input type="text" name="email" />
    <p>Password:</p>
    <input type="password" name="password" />
    <br /><br />
    <input type="submit" value="Log In" />
  </form>
  <p>Not a member? <a href="register.php">Sign up today!</a></p>
<?php } 

function register_user($email, $password) {
  global $dbh;
  $message = "";
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $message = "invalid email. please re-enter!";
  } else {
    $passhash = hash("sha512", $password);
    try {
      $dbh->beginTransaction();
      $query = $dbh->prepare("insert into users (Email, HashedPassword) values (?, ?)");
      $query->bindParam(1, $email);
      $query->bindParam(2, $passhash);
      $query->execute();
      $dbh->commit();
      
      $message = "success";
      
    } catch (Exception $e) {
      $dbh->rollBack();
      $message = "Failed: {$e->getMessage()}";
    }
  }
  return $message;
}

?>