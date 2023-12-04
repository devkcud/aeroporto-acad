<?php
  require_once("../config.php");
  $con = connectDB();

  if (isset($_GET["remove"])) {
    $id = $_GET["remove"];

    $stmt = $con->prepare("DELETE FROM Plane WHERE PlaneID = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: planes.php");
  }

  if (isset($_GET["model"])) {
    $planetypeid = $_GET["planetypeid"];
    $model = $_GET["model"];
    $seat_capacity = $_GET["seat-capacity"] === "" ? null : (htmlspecialchars($_GET["seat-capacity"]) === "0" ? null : htmlspecialchars($_GET["seat-capacity"]));
    $cargo_capacity = $_GET["cargo-capacity"] === "" ? null : (htmlspecialchars($_GET["cargo-capacity"]) === "0" ? null : htmlspecialchars($_GET["cargo-capacity"]));
    $source_airport = $_GET["source-airport"] === "" ? null : $_GET["source-airport"];
    $destination_airport = $_GET["destination-airport"] === "" ? null : $_GET["destination-airport"];

    $stmt = $con->prepare("INSERT INTO Plane (PlaneTypeID, Model, SeatCapacity, CargoCapacity, SourceAirportID, DestinationAirportID) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $planetypeid, htmlspecialchars($model), $seat_capacity, $cargo_capacity, $source_airport, $destination_airport);
    $stmt->execute();
    $stmt->close();

    header("Location: planes.php");
  }

  if (isset($_GET["removeall"])) {
    $stmt = $con->prepare("DELETE FROM Plane");
    $stmt->execute();
    $stmt->close();

    header("Location: planes.php");
  }

  $result = $con->query("SELECT * FROM Plane");
  $planeTypes = $con->query("SELECT * FROM PlaneType");
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <?php require_once("../setuphead.php") ?>
    <link rel="stylesheet" href="../css/planes.css" />
  </head>
  <body>
    <?php require_once("../setupheader.php") ?>

    <form id="insert" method="GET">
      <div>
        <div>
          <label class="required" for="model">Modelo</label>
          <input type="text" name="model" placeholder="Modelo" required>
        </div>
        <div>
          <label for="seat-capacity">Assentos</label>
          <input type="number" min="0" name="seat-capacity" placeholder="Assentos">
        </div>
        <div>
          <label for="cargo-capacity">Carga</label>
          <input type="number" min="0" step="0.001" name="cargo-capacity" placeholder="Peso suportado (Toneladas)">
        </div>
      </div>
      <div>
        <div>
          <label for="source-airport">Aeroporto de partida</label>
          <select name="source-airport">
            <?php
              $airports = $con->query("SELECT * FROM Airport");

              if ($airports->num_rows === 0) {
                echo "<option value='' disabled hidden selected>Nenhum aeroporto cadastrado</option>";
              } else {
                echo "<option value='' disabled hidden selected>Selecione o aeroporto de partida</option>";

                while ($airport = $airports->fetch_assoc()) {
                  echo "<option value='" . $airport["AirportID"] . "'>" . $airport["Address"] . "</option>";
                }
              }
            ?>
          </select>
        </div>
        <div>
          <label for="destination-airport">Aeroporto de destino</label>
          <select name="destination-airport">
            <?php
              $airports = $con->query("SELECT * FROM Airport");

              if ($airports->num_rows === 0) {
                echo "<option value='' disabled hidden selected>Nenhum aeroporto cadastrado</option>";
              } else {
                echo "<option value='' disabled hidden selected>Selecione o aeroporto de destino</option>";

                while ($airport = $airports->fetch_assoc()) {
                  echo "<option value='" . $airport["AirportID"] . "'>" . $airport["Address"] . "</option>";
                }
              }
            ?>
          </select>
        </div>
      </div>
      <div>
        <div>
          <label class="required" for="planetypeid">Tipo de avião</label>
          <select name="planetypeid" required>
            <?php
              if ($planeTypes->num_rows === 0) {
                echo "<option value='' disabled hidden selected>Nenhum tipo de avião cadastrado</option>";
              } else {
                echo "<option value='' disabled hidden selected>Selecione o tipo de avião</option>";
                while ($planeType = $planeTypes->fetch_assoc()) {
                  echo "<option value='" . $planeType["PlaneTypeID"] . "'>" . $planeType["Category"] . " - " . $planeType["Purpose"] . "</option>";
                }
              }
            ?>
          </select>
        </div>
      </div>

      <button class="btn center">Registrar</button>
    </form>

    <div class="separator"></div>

    <div style="display: flex; justify-content: center; gap: 1rem">
      <button id="deleteall" class="btn remove">Deletar Tudo</button>

      <?php createDialog('deleteall', 'Tem certeza de que deseja excluir tudo? <b>Esta ação excluirá TODOS os aviões.</b>', 'planes.php?removeall=true', 'Sim, deletar TUDO', 'Cancelar') ?>
    </div>

    <div class="separator"></div>

    <main id="planes">
      <h1 class="title">Aviões</h1>

      <?php
        function getSupportType($seatCapacity, $cargoCapacity) {
          if ($seatCapacity && $cargoCapacity) {
              return "Híbrido";
          } else if ($seatCapacity) {
              return "Transporte";
          } else if ($cargoCapacity) {
              return "Carga";
          } else {
              return "Não especificado";
          }
        }

        if ($result->num_rows === 0) {
          echo "<p class='no-results'>Nenhum avião registrado.</p>";
        } else {
          echo "<div class='planes-list'>";

          while ($row = $result->fetch_assoc()) {
            echo "<div class='plane'>";

            echo "<div class='title'>";
            echo "<img src='../img/plane.svg' width='40px' height='40px' />";
            echo "<h1>" . $row["Model"] . "</h1>";

            $planeType = $con->query("SELECT Category, Purpose FROM PlaneType WHERE PlaneTypeID = " . $row["PlaneTypeID"])->fetch_assoc();
            echo "<span class='smol'>" . $planeType["Category"] . " (" . $planeType["Purpose"] . ")</span>";
            echo "</div>";

            echo "<div class='airports'>";

            if ($row["SourceAirportID"]) {
              $sourceAirport = $con->query("SELECT Address FROM Airport WHERE AirportID = " . $row["SourceAirportID"])->fetch_assoc();
              echo "<span class='important'>" . $sourceAirport["Address"] . "</span>";
            }

            if ($row["SourceAirportID"] && $row["DestinationAirportID"]) {
              echo "<div id='arrow'><img src='../img/plane.svg' id='ab-" . $row["PlaneID"] . "' onload='trajectory(\"" . $row["PlaneID"] . "\")' data-firsttime='true'></div>";
            }

            if ($row["DestinationAirportID"]) {
              $destinationAirport = $con->query("SELECT Address FROM Airport WHERE AirportID = " . $row["DestinationAirportID"])->fetch_assoc();
              echo "<span class='important'>" . $destinationAirport["Address"] . "</span>";
            }

            echo "</div>";

            echo "<div class='separator'></div>";

            if ($row["SeatCapacity"]) {
              echo "<p><b>Assentos:</b> " . $row["SeatCapacity"] . "</p>";
            }

            if ($row["CargoCapacity"]) {
              echo "<p><b>Peso suportado:</b> " . $row["CargoCapacity"] . " Toneladas</p>";
            }

            echo "<p><b>Categoria:</b> " . getSupportType($row["SeatCapacity"], $row["CargoCapacity"]) . "</p>";

            echo "<div class='separator'></div>";

            echo "<div class='center actions'>";
            echo "<button id='" . $row["PlaneID"] . "' class='btn remove'>Remover</button>";
            createDialog($row["PlaneID"], 'Tem certeza de que deseja excluir o avião <b>' . $row["Model"] . '</b>?', 'planes.php?remove=' . $row["PlaneID"], 'Sim, deletar');
            echo "</div>";

            echo "</div>";
          }

          echo "</div>";
        }
      ?>
    </main>

    <script>
      let lastNum = 0;

      function trajectory(planeID) {
        const arrow = document.getElementById("ab-" + planeID);
        let num = ((parseFloat(window.crypto.getRandomValues(new Uint32Array(1))[0].toString().substring(0, 3)) / 100) + 5).toFixed(3);

        while (lastNum === Math.floor(num)) {
          num = ((parseFloat(window.crypto.getRandomValues(new Uint32Array(1))[0].toString().substring(0, 3)) / 100) + 5).toFixed(3);
        }

        arrow.style.animationDuration = `${num}s`;

        lastNum = Math.floor(num);
        console.log(lastNum);
      }
    </script>
  </body>
</html>
