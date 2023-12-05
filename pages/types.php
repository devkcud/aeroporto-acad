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

  if (isset($_GET["backdefault"])) {
    $stmt = $con->prepare("DELETE FROM PlaneType");
    $stmt->execute();
    $stmt->close();

    $sql = "INSERT INTO PlaneType (Category, Purpose) VALUES
        ('Boeing', 'Comercial'),
        ('Airbus', 'Transporte'),
        ('Bombardier', 'Militar'),
        ('Embraer', 'Jato Regional'),
        ('Cessna', 'Privado'),
        ('Lockheed Martin', 'Militar'),
        ('Gulfstream', 'Jato Executivo');";

    $stmt = $con->prepare($sql);
    $stmt->execute();
    $stmt->close();

    header("Location: types.php");
  }

  if (isset($_GET["removeall"])) {
    $stmt = $con->prepare("DELETE FROM PlaneType");
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

    <form class="insert" method="GET">
      <input type="text" name="category" placeholder="Nome" required>
      <input type="text" name="purpose" placeholder="Tipo" required>

      <button class="btn">Registrar</button>
    </form>

    <div class="separator"></div>

    <div style="display: flex; justify-content: center; gap: 1rem">
      <button id="turndefault" class="btn remove">Redefinir Padrão</button>
      <button id="deleteall" class="btn remove">Deletar Tudo</button>

      <?php createDialog('turndefault', 'Tem certeza de que deseja retornar ao padrão? <b>Esta ação excluirá TODOS os tipos de avião.</b>', 'types.php?backdefault=true', 'Sim, restaurar padrão', 'Cancelar') ?>
      <?php createDialog('deleteall', 'Tem certeza de que deseja excluir tudo? <b>Esta ação excluirá TODOS os tipos de avião.</b>', 'types.php?removeall=true', 'Sim, deletar TUDO', 'Cancelar') ?>
    </div>

    <div class="separator"></div>

    <main id="plane-types">
      <h1 class="title">Tipos de Aviões</h1>

      <?php
        if ($result->num_rows === 0) {
          echo "<p class='no-results'>Nenhum tipo de avião registrado.</p>";
        } else {
          echo "<div class='types-list'>";

          while($row = $result->fetch_assoc()) {
            echo "<div class='type'>";

            echo "<div class='title'>";
            echo "<img src='../img/plane.svg' width='40px' height='40px' />";
            echo "<h1>" . $row["Category"] . " <span class='smol'>" . $row["Purpose"] . "</span>" . "</h1>";
            echo "</div>";

            echo "<div class='actions'>";
            echo "<button id='". $row["PlaneTypeID"] . "' class='btn remove'>Remover</button>";
            createDialog($row["PlaneTypeID"], 'Tem certeza de que deseja excluir <b>' . $row["Category"] . '</b>?', 'types.php?remove=' . $row["PlaneTypeID"], 'Sim, deletar');
            echo "</div>";

            echo "</div>";
          }

          echo "</div>";
        }
      ?>
    </main>
  </body>
</html>
