<?php
  require_once("../config.php");
  $con = connectDB();

  $id = $_POST["id"];

  $stmt = $con->prepare("SELECT * FROM Airport WHERE AirportID = ?");
  $stmt->bind_param("s", $id);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Aeroporto ACAD</title>
  </head>
  <body>
    <button class="go-back" onclick="window.location.href = 'home.php';">Voltar</button>

    <?php
      echo "<div class='address'>";
      echo "<img src='../img/map.svg' width='40px' height='40px' />";
      echo "<h1>" . $row["Address"] . "</h1>";
      echo "</div>";
    ?>
  </body>
</html>
