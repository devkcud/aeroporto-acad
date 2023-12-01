<?php
  require_once("../config.php");
  $con = connectDB();

  if (isset($_GET["insert"])) {
    $address = $_GET["insert"];

    $stmt = $con->prepare("INSERT INTO Airport (Address) VALUES (?)");
    $stmt->bind_param("s", htmlspecialchars($address));
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

    <?php require_once("../setuphead.php") ?>
    <link rel="stylesheet" href="../css/home.css" />
  </head>
  <body>
    <form id="inserir-endereco" method="GET">
      <input type="text" name="insert" placeholder="Endereço" required>

      <button class="btn">Registrar</button>
    </form>

    <div class="separator"></div>

    <?php
      if ($result->num_rows > 0) {
        echo "<ul id='addresses'>";

        while($row = $result->fetch_assoc()) {
          echo "<li>";
          echo "<div class='address'>";
          echo "<img src='../img/map.svg' />";
          echo "<span>" . $row["Address"] . "</span>";
          echo "</div>";
          echo "<div class='actions'>";
          echo "<form action='view.php' method='POST'>";
          echo "<input type='hidden' name='id' value='" . $row["AirportID"] . "' />";
          echo "<button class='btn edit' type='submit'>Editar</button>";
          echo "</form>";
          echo "<form method='GET'>";
          echo "<input type='hidden' name='remove' value='" . $row["AirportID"] . "' />";
          echo "<button class='btn remove' type='submit'>Remover</button>";
          echo "</form>";
          echo "</div>";
          echo "</li>";
        }

        echo "</ul>";
      } else {
        echo "<p class='no-results'>0 Resultados</p>";
      }
    ?>
  </body>
</html>
