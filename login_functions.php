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