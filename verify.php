<?php
require_once('db_init.php');

if (isset($_GET['email']) && !empty($_GET['email']) AND isset($_GET['hash']) && !empty($_GET['hash'])) {
  $email = $_GET['email'];
  $hash = $_GET['hash'];
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $message = "invalid email address.";
  }
  else {
    try {
      $query = $dbh->prepare("select * from users where Email = ? and ActivationHash = ? and IsActive = '0';");
      $query->bindParam(1, $email);
      $query->bindParam(2, $hash);
      $query->execute();
      if ($result = $query->fetch()) {
        try {
          $dbh->beginTransaction();
          $query = $dbh->prepare("update users set IsActive = '1' where Email = ? and ActivationHash = ? and IsActive = '0';");
          $query->bindParam(1, $email);
          $query->bindParam(2, $hash);
          $query->execute();
          $dbh->commit();

          $message = "your account has been verified!";
        }
        catch (Exception $e) {
          $dbh->rollBack();
          $message = "Failed: {$e->getMessage()}";
        }
      }
      else {
        $message = "invalid email/hash combination.";
      }
    } catch (Exception $e) {
      $message = "Failed: {$e->getMessage()}";
    }
  }
}
else {
  $message = "no email or hash provided.";
}
?>

<p><?php echo $message; ?></p>