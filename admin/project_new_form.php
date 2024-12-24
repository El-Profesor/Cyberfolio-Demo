<?php

session_start();

include_once "admin_top.php";

?>
<div class="col-md-7 col-lg-8 mx-auto"> <!-- MAIN CONTENT : SART -->
  <h4 class="mb-3">Formulaire de création d'un projet</h4>
  <?php
  if (!empty($_SESSION['form_errors'])) {
    $errorsList = '';
    foreach ($_SESSION['form_errors'] as $errorMsg) {
      $errorsList .= '<li>' . $errorMsg . '</li>';
    }
    unset($_SESSION['form_errors']);
  ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <strong>Le formulaire contient des erreurs :</strong>
      <ul><?= $errorsList ?></ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php
  }
  ?>
  <form action="project_new.php" method="post" enctype="multipart/form-data" class="needs-validation" novalidate="">
    <div class="row g-3">

      <div class="col-12">
        <label for="title" class="form-label">Intitulé</label>
        <input type="text" class="form-control" id="title" name="title" placeholder="Intitulé du projet" required="">
        <div class="invalid-feedback">
          Merci de renseigner l'intitulé du projet.
        </div>
      </div>

      <div class="col-12">
        <label for="summary" class="form-label">Résumé</label>
        <input type="text" class="form-control" id="summary" name="summary" placeholder="Courte description du projet" required="">
        <div class="invalid-feedback">
          Merci de fournir une courte description du projet (résumé).
        </div>
      </div>

      <div class="col-12">
        <label for="description" class="form-label">Description <span class="text-body-secondary">(optionnel)</span></label>
        <textarea type="text" class="form-control" id="description" name="description" placeholder="Description complète du projet" rows="4"></textarea>
        <div class="invalid-feedback">
          <!-- No feedback -->
        </div>
      </div>

      <div class="col-4">
        <label for="completed_at" class="form-label">Réalisé le</label>
        <input type="date" class="form-control" id="completed_at" name="completed_at" required="">
        <div class="invalid-feedback">
          Merci de renseigner la date de réalisation du projet.
        </div>
      </div>

      <div class="col-8">
        <label for="screenshot" class="form-label"><i>Screenshot</i> associé</label>
        <input type="file" class="form-control" id="screenshot" name="screenshot" required="">
        <div class="invalid-feedback">
          Merci de fournir une image illustrant le projet.
        </div>
      </div>
    </div>

    <hr class="my-4">

    <button class="w-100 btn btn-primary btn-lg" type="submit">Enregistrer le nouveau projet</button>
  </form>
</div> <!-- MAIN CONTENT : END -->
<?php

include_once "admin_bottom.php";

?>