<?php

session_start();

include_once "admin_top.php";

?>
<div class=""> <!-- MAIN CONTENT : SART -->
  <h4 class="mb-3 d-flex justify-content-between"><span>Liste des projets</span><a href="project_new_form.php" title="Ajouter un nouveau projet" class="btn btn-primary"><i class="bi bi-plus-circle pe-2"></i>Nouveau projet</a></h4>
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
  <div class="table-responsive small">
    <table class="table table-striped">
      <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">Intitulé</th>
          <th scope="col">Résumé</th>
          <th scope="col">Description</th>
          <th scope="col">Screenshot</th>
          <th scope="col">Réalisé le</th>
          <th scope="col">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $host = 'localhost';
        $dbName = 'cyberfolio';
        $user = 'mentor';
        $pass = 'superMentor';
        $tableRows = '';

        try {
          $connexion = new PDO("mysql:host=$host;dbname=$dbName", $user, $pass);

          $query = 'SELECT COUNT(*) FROM `project`';

          $results = $connexion->query($query);
          $resultsCount = $results->fetchColumn();

          if ($resultsCount === 0) {
            $tableRows = '<tr><td colspan="7">Vous n\'avez aucun projet pour le moment</td></tr>' . PHP_EOL;
          } else {
            $query = 'SELECT `id_project`, `title`, `summary`, `description`, `screenshot`, `completed_at` FROM `project`';

            $results = $connexion->query($query, PDO::FETCH_ASSOC);

            foreach ($results as $row) {
              $tableRows .= '<tr><td class="align-middle text-truncate" style="max-width: 150px;">' . implode('</td><td class="align-middle text-truncate" style="max-width: 150px;">', array_map('stripslashes', $row)) . '</td>';
              $tableRows .= '<td>';
              $tableRows .= '<a href="project_show.php?id=' . $row['id_project'] . '" title="Voir le détail de ce projet" class="btn btn-primary btn-sm me-1"><i class="bi bi-file-richtext"></i></a>';
              $tableRows .= '<a href="project_update_form.php?id=' . $row['id_project'] . '" title="Modifier ce projet" class="btn btn-secondary btn-sm me-1"><i class="bi bi-pen"></i></a>';
              $tableRows .= '<a href="project_delete_form.php?id=' . $row['id_project'] . '" title="Supprimer ce projet" class="btn btn-danger btn-sm"><i class="bi bi-trash3"></i></a>';
              $tableRows .= '</td></tr>' . PHP_EOL;
            }
          }
        } catch (PDOException $e) {
          $error = "Une erreur s'est produite lors de l'interrogation de la base de données : veuillez contacter l'administrateur du site.";
          echo $error;
        } finally {
          $connexion = null;
        }

        echo $tableRows;
        ?>
      </tbody>
    </table>
  </div>
</div> <!-- MAIN CONTENT : END -->
<?php

include_once "admin_bottom.php";

?>