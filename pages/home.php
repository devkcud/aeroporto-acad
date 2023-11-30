<?php
  require_once("../config.php");
  $con = connectDB();

  if (isset($_GET["insert"])) {
    $address = $_GET["insert"];

    $stmt = $con->prepare("INSERT INTO Airport (Address) VALUES (?)");
    $stmt->bind_param("s", $address);
    $stmt->execute();
    $stmt->close();

    header("Location: home.php");
  }

  if (isset($_GET["remove"])) {
    $id = $_GET["remove"];

    $stmt = $con->prepare("DELETE FROM Airport WHERE AirportID = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: home.php");
  }

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
    <form method="GET">
      <input type="text" name="insert" placeholder="Endereço">

      <button>Registar</button>
    </form>

    <?php
      if ($result->num_rows > 0) {
        echo "<ul>";

        while($row = $result->fetch_assoc()) {
          echo "<li>";
          echo "<div class='address'>";
          echo "<img src='../img/map.svg' width='40px' height='40px' />";
          echo "<span>" . $row["Address"] . "</span>";
          echo "</div>";
          echo "<div class='actions'>";
          echo "<form method='GET'>";
          echo "<input type='hidden' name='remove' value='" . $row["AirportID"] . "' />";
          echo "<button type='submit'>Remover</button>";
          echo "</form>";
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
