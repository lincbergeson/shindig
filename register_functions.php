<?php
require_once('db_init.php');

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
you have signed up for a shindig account! congrats!<br /><br />

you can log in with your email address and the password you set at registration, but you need to click the following link to activate your account. (if you don't activate it, you can't really do anything. we do this to prevent robots from creating accounts. robots are not invited to the shindig.)<br /><br />

so yeah, click here:<br />
<a href="http://shindig.x10.bz/verify.php?email=$email&hash=$activation_hash">http://shindig.x10.bz/verify.php?email=$email&hash=$activation_hash</a><br /><br />

should be good to go after that!<br /><br />

-- your friends at the shindig
MSG;
  $headers  = 'MIME-Version: 1.0' . "\r\n"
            . "Content-type: text/html; charset=iso-8859-1\r\n"
            . "From: shindig <noreply@shindig.x10.bz>\r\n";
  mail($to, $subject, $message, $headers);
}