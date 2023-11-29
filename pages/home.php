<?php
  require_once("config.php");
  $con = connectDB();

  $result = $con->query("SELECT * FROM Airport");
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Aeroporto ACAD</title>
  </head>
  <body>
    <form action="registar.php" method="POST">
      <input type="text" name="address" placeholder="Endereço">

      <button>Registar</button>
    </form>

    <?php
      if ($result->num_rows > 0) {
        echo "<ul>";

        while($row = $result->fetch_assoc()) {
          echo "<li>";
          echo "<div class='address'>";
          echo "<img src='img/map.svg' width='40px' height='40px' />";
          echo "<span>" . $row["Address"] . "</span>";
          echo "</div>";
          echo "</li>";
        }

        echo "</ul>";
      } else {
        echo "<p id='no-results'>0 Resultados</p>";
      }
    ?>
  </body>
</html>
