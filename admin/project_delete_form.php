<?php

session_start();

/**
 * ******************** [1] Check if submitted form is valid
 */
if ($_SERVER["REQUEST_METHOD"] === "GET") {
  if (isset($_GET['id']) && trim($_GET['id']) !== '') { // Required project 'id' value
    // OK
    $filterOptions = [
      'options' => [
        'min_range' => 1,
      ],
    ];
    if (filter_var($_GET['id'], FILTER_VALIDATE_INT, $filterOptions) !== FALSE) {
      // OK
      $idProject = $_GET['id'];

      include_once "admin_top.php";
?>
      <div class="col-md-7 col-lg-8 mx-auto"> <!-- MAIN CONTENT : SART -->
        <h4 class="mb-3">Formulaire de suppression d'un projet</h4>
        <form action="project_delete.php" method="post" class="needs-validation" novalidate="">
          <div class="row g-3">

            <div class="col-12">
              Vous êtes sur le point de supprimer un projet. Êtes-vous certain de vouloir supprimer le projet n°<?= $idProject ?> ?
              <input type="hidden" id="id-project" name="id" value="<?= $idProject ?>">
            </div>

            <hr class="my-4">

            <div class="col-12 d-flex justify-content-start gap-3">
              <a href="project_index.php" title="Retour à la liste des projets" class="btn btn-primary btn-lg"><i class="bi bi-escape pe-2"></i>Non</a>
              <button class="btn btn-danger btn-lg" type="submit"><i class="bi bi-trash3 pe-2"></i>Oui</button>
            </div>
        </form>
      </div> <!-- MAIN CONTENT : END -->
<?php
      include_once "admin_bottom.php";

    } else {
      // KO: Suspicious request (project 'id' is not an integer positive value)
      header("Location: ../404.php");
      exit;
    }
  } else {
    // KO: Suspicious request (project 'id' is an empty value)
    header("Location: ../404.php");
    exit;
  }
} else {
  // KO: Suspicious request (request method is not GET)
  header("Location: ../404.php");
  exit;
}

?>