<?php
  require_once("../config.php");
  $con = connectDB();

  if (isset($_GET["remove"])) {
    $id = $_GET["remove"];

    $stmt = $con->prepare("DELETE FROM PlaneType WHERE PlaneTypeID = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: types.php");
  }

  if (isset($_GET["category"]) && isset($_GET["purpose"])) {
    $category = $_GET["category"];
    $purpose = $_GET["purpose"];

    $stmt = $con->prepare("INSERT INTO PlaneType (Category, Purpose) VALUES (?, ?)");
    $stmt->bind_param("ss", htmlspecialchars($category), htmlspecialchars($purpose));
    $stmt->execute();
    $stmt->close();

    header("Location: types.php");
  }

  $result = $con->query("SELECT * FROM PlaneType");
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <?php require_once("../setuphead.php") ?>
    <link rel="stylesheet" href="../css/types.css" />
  </head>
  <body>
    <?php require_once("../setupheader.php") ?>

    <form id="insert" method="GET">
      <input type="text" name="category" placeholder="Nome" required>
      <input type="text" name="purpose" placeholder="Tipo" required>

      <button class="btn">Registrar</button>
    </form>

    <div class="separator"></div>

    <main id="plane-types">
      <h1 class="title">Tipos de Aviões</h1>

      <?php
        if ($result->num_rows === 0) {
          echo "<p class='no-results'>Nenhum tipo de avião registrado.</p>";
          return;
        }

        echo "<div class='types-list'>";

        while($row = $result->fetch_assoc()) {
          echo "<div class='type'>";

          echo "<div class='title'>";
          echo "<img src='../img/plane.svg' width='40px' height='40px' />";
          echo "<h1>" . $row["Category"] . " <span class='smol'>" . $row["Purpose"] . "</span>" . "</h1>";
          echo "</div>";

          echo "<div class='actions'>";
          echo "<a href='types.php?remove=" . $row["PlaneTypeID"] . "' class='btn remove'>Deletar</a>";
          echo "</div>";

          echo "</div>";
        }

        echo "</div>";
      ?>
    </main>

  </body>
</html>
