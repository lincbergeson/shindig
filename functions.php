<?php
require_once('db_init.php');

function is_active_user($email) {
  $message = "";
  global $dbh;
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $message = "invalid email address.";
  }
  else {
    try {
      $query = $dbh->prepare("select IsActive from users where Email = ?;");
      $query->bindParam(1, $email);
      $query->execute();
      $result = $query->fetch(PDO::FETCH_ASSOC);
      if ($result["IsActive"] == 1) {
        $message = "success";
      }
      else { 
        $message = "user is not active.";
      }
    } catch (Exception $e) {
      $message = "Failed: {$e->getMessage()}";
    }
  }
  return $message;
}
        

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

function user_exists($email) {
  global $dbh;
  $exists = false;
  $query = $dbh->prepare("select * from users where Email = ?;");
  $query->bindParam(1, $email);
  $query->execute();
  if ($query->fetch()) {
    $exists = true;
  }
  return $exists;
}

function register_user($email, $password) {
  global $dbh;
  $message = "";
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $message = "invalid email. please re-enter!";
  } 
  else if (user_exists($email)) {
    $message = "that user already exists in the database.";
  }
  else {
    $passhash = hash("sha512", $password);
    $activation_hash = md5(rand(0,1000));
    try {
      $dbh->beginTransaction();
      $query = $dbh->prepare("insert into users (Email, HashedPassword, ActivationHash, IsActive) values (?, ?, ?, 0)");
      $query->bindParam(1, $email);
      $query->bindParam(2, $passhash);
      $query->bindParam(3, $activation_hash);
      $query->execute();
      $dbh->commit();
      
      send_verification_email($email, $activation_hash);
      $message = "success";
      
    } catch (Exception $e) {
      $dbh->rollBack();
      $message = "Failed: {$e->getMessage()}";
    }
  }
  return $message;
}

function send_verification_email($email, $activation_hash) {
  $to = $email;
  $subject = 'verify your shindig account';
  $message = <<<MSG
you have signed up for a shindig account! congrats!

you can log in with your email address and the password you set at registration, but you need to click the following link to activate your account. (if you don't activate it, you can't really do anything. we do this to prevent robots from creating accounts. robots are not invited to the shindig.)

so yeah, click here:
<a href="http://shindig.x10.bz/verify.php?email=$email&hash=$activation_hash">http://shindig.x10.bz/verify.php?email=$email&hash=$activation_hash</a>

should be good to go after that!
MSG;
  $headers  = 'MIME-Version: 1.0' . "\r\n";
  $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
  $headers .= "From: noreply@shindig.x10.bz <noreply@shindig.x10.bz>\r\n";
  mail($to, $subject, $message, $headers);
}
?>
