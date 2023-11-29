<?php
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  ini_set('error_reporting', E_ALL);

  function connectDB() {
    $con = new mysqli("localhost", "root", "", "ACADSystem");

    if ($con->connect_error) {
      die("Connection failed: " . $con->connect_error);
    }

    return $con;
  }
?>
