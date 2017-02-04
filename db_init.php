<?php
$dbh = new PDO('mysql:host=localhost;dbname=shindig', 'shindig', 'SeQMnpToMvvTY7Uf', array(PDO::ATTR_PERSISTENT => true));
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);