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