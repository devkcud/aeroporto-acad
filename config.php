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

  function createDialog($triggerID, $msg, $href, $yesButton = 'Sim', $noButton = 'Não') {
$uuid = uniqid();

echo "<dialog id='__dialog-$uuid'>
    <p>$msg</p>

    <div class='separator'></div>

    <section class='actions'>
      <button class='btn remove' id='__dialog-$uuid-confirm'>$yesButton</button>
      <button class='btn' id='__dialog-$uuid-cancel'>$noButton</button>
    </section>
  </dialog>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var confirmationDialog = document.getElementById('__dialog-$uuid');
      var confirmDeleteButton = document.getElementById('__dialog-$uuid-confirm');
      var cancelDeleteButton = document.getElementById('__dialog-$uuid-cancel');

      function openDialog(e) {
        if (e.shiftKey) {
          return handleConfirmation();
        }

        confirmationDialog.showModal();
      }

      function closeDialog() {
        confirmationDialog.close();
      }

      function handleConfirmation() {
        window.location.href = '$href';
      }

      var defaultButton = document.getElementById('$triggerID');
      if (defaultButton) {
        defaultButton.addEventListener('click', openDialog);
      }

      if (confirmDeleteButton) {
        confirmDeleteButton.addEventListener('click', handleConfirmation);
      }

      if (cancelDeleteButton) {
        cancelDeleteButton.addEventListener('click', closeDialog);
      }
    });
  </script>";
  }
?>
