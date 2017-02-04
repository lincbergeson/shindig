<?php
session_start();
session_regenerate_id();
unset($_SESSION["email"]);
session_destroy();
header('Location: index.php');
die();