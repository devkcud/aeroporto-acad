<?php
  require_once("../config.php");
  $con = connectDB();

  $id = $_GET["id"];

  if (isset($_GET["endereco"])) {
    $endereco = $_GET["endereco"];

    $stmt = $con->prepare("UPDATE Airport SET Address = ? WHERE AirportID = ?");
    $stmt->bind_param("ss", $endereco, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: view.php?id=" . $id);
  }

  if (isset($_GET["removePlane"])) {
    $planeID = $_GET["removePlane"];

    $stmt = $con->prepare("UPDATE Plane SET SourceAirportID = null WHERE PlaneID = ? AND SourceAirportID = ?");
    $stmt->bind_param("ss", $planeID, $id);
    $stmt->execute();
    $stmt->close();

    $stmt = $con->prepare("UPDATE Plane SET DestinationAirportID = null WHERE PlaneID = ? AND DestinationAirportID = ?");
    $stmt->bind_param("ss", $planeID, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: view.php?id=" . $id);
  }

  if (isset($_GET["plane"]) && isset($_GET["planeRole"])) {
    $planeID = $_GET["plane"];
    $planeRole = $_GET["planeRole"];

    if ($planeRole === "source") {
      $stmt = $con->prepare("UPDATE Plane SET SourceAirportID = ? WHERE PlaneID = ?");
      $stmt->bind_param("ss", $id, $planeID);
      $stmt->execute();
      $stmt->close();
    } else {
      $stmt = $con->prepare("UPDATE Plane SET DestinationAirportID = ? WHERE PlaneID = ?");
      $stmt->bind_param("ss", $id, $planeID);
      $stmt->execute();
      $stmt->close();
    }

    header("Location: view.php?id=" . $id);
  }

  $stmt = $con->prepare("SELECT * FROM Airport WHERE AirportID = ?");
  $stmt->bind_param("s", $id);
  $stmt->execute();
  $result = $stmt->get_result();
  $airport = $result->fetch_assoc();
  $result->free();
  $stmt->close();
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <?php require_once("../setuphead.php") ?>
    <link rel="stylesheet" href="../css/view.css" />
  </head>
  <body>
    <?php require_once("../setupheader.php") ?>

    <?php
      echo "<div class='address'>";
      echo "<img src='../img/map.svg' width='24px' height='24px' />";
      echo "<h1>" . $airport["Address"] . "</h1>";
      echo "</div>";
    ?>

    <main>
      <h1 class="title">Configuração</h1>

      <section class="config">
        <form class="insert" method="GET">
          <input type="hidden" name="id" value=<?php echo "'" . $id . "'" ?>>

          <div>
            <div>
              <label class="required" for="endereco">Endereço</label>
              <input type="text" name="endereco" placeholder="Endereço" value=<?php echo "'" . $airport["Address"] . "'" ?> required>
            </div>
          </div>

          <button class="btn save center">Salvar</button>
        </form>
      </section>

      <div class="separator"></div>

      <h1 class="title">Aviões Associados</h1>

      <section class="planes">
        <?php
          $stmt = $con->prepare("SELECT * FROM Plane WHERE SourceAirportID = ? OR DestinationAirportID = ?");
          $stmt->bind_param("ss", $id, $id);
          $stmt->execute();
          $result = $stmt->get_result();

          if ($result->num_rows === 0) {
            echo "<p class='no-results'>Nenhum avião associado a este aeroporto.</p>";
          } else {
            while ($plane = $result->fetch_assoc()) {
              echo "<div class='plane'>";
              echo "<div class='title'>";
              echo "<img src='../img/plane.svg' width='40px' height='40px' />";
              echo "<h1>" . $plane["Model"] . "</h1>";
              echo "</div>";

              if ($plane["SourceAirportID"] == $id) {
                echo "<p><b>Definido como:</b> Partida</p>";
              }

              if ($plane["DestinationAirportID"] == $id) {
                echo "<p><b>Definido como:</b> Destino</p>";
              }

              echo "<button id='remove-" . $plane["PlaneID"] . "' class='btn remove'>Remover Associação</button>";
              createDialog("remove-" . $plane["PlaneID"], 'Tem certeza de que deseja excluir <b>' . $plane["Model"] . '</b>?', 'view.php?id=' . $id . '&removePlane=' . $plane["PlaneID"], 'Sim, deletar');

              echo "</div>";
            }
          }
        ?>
      </section>

      <div class="separator"></div>

      <section class="add-plane">
        <h1 class="title">Associar Avião</h1>

        <form class="insert" method="GET">
          <input type="hidden" name="id" value="<?php echo $id; ?>">

          <div>
            <div>
              <label for="plane">Avião:</label>
              <select name="plane" required>
                <?php
                  $planes = $con->query("SELECT * FROM Plane");

                  if ($planes ->num_rows === 0) {
                    echo "<option value='' disabled hidden selected>Nenhum avião cadastrado</option>";
                  } else {
                    echo "<option value='' disabled hidden selected>Selecione o avião de destino</option>";

                    while ($plane = $planes->fetch_assoc()) {
                      echo "<option value='" . $plane["PlaneID"] . "'>" . $plane["Model"] . "</option>";
                    }
                  }
                ?>
              </select>
            </div>
          </div>

          <div>
            <div>
              <label for="planeRole">Função:</label>
              <select name="planeRole" required>
                <option value="" disabled hidden selected>Selecione a função</option>
                <option value="source">Partida</option>
                <option value="destination">Destino</option>
              </select>
            </div>
          </div>

          <button type="submit" class="btn save">Associar Avião</button>
        </form>
      </section>
    </main>
  </body>
</html>
