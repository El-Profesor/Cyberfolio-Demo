<?php

session_start();

include_once "admin_top.php";

?>
<div class=""> <!-- MAIN CONTENT : SART -->
  <h4 class="mb-3">Accueil du dashboard</h4>
  <?php

  if (!empty($_SESSION['success'])) {
    $successList = '';
    foreach ($_SESSION['success'] as $successMsg) {
      $successList .= '<li>' . $successMsg . '</li>';
    }
    unset($_SESSION['success']);

  ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <strong>Information :</strong>
      <ul><?= $successList ?></ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php

  }

  if (!empty($_SESSION['errors'])) {
    $errorsList = '';
    foreach ($_SESSION['errors'] as $errorMsg) {
      $errorsList .= '<li>' . $errorMsg . '</li>';
    }
    unset($_SESSION['errors']);

  ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <strong>Erreur :</strong>
      <ul><?= $errorsList ?></ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php

  }

  ?>
</div> <!-- MAIN CONTENT : END -->
<?php

include_once "admin_bottom.php";

?>