<?php

session_start();

$errors = [];
$success = [];

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

      /**
       * ******************** [2] Remove the corresponding record from database
       */

      $host = 'localhost';
      $dbName = 'cyberfolio';
      $user = 'mentor';
      $pass = 'superMentor';

      try {
        $connexion = new PDO("mysql:host=$host;dbname=$dbName", $user, $pass);
        $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query  = 'SELECT COUNT(*) FROM `project` ';
        $query .= 'WHERE `id_project`=:id_project';

        $queryValues = [
          ':id_project' => $idProject,
        ];

        $statement = $connexion->prepare($query);
        $result = $statement->execute($queryValues);
        $resultCount = $statement->fetchColumn();

        if ($resultCount === 0) {
          $errors['project_details'] = "Le détail du projet n'est pas disponible : le projet n° $idProject n'existe pas ou a été supprimé.";
          $_SESSION['errors'] = $errors;
          header('Location: project_index.php');
          exit;
        } else {
          $query  = 'SELECT `id_project`, `title`, `summary`, `description`, `screenshot`, `completed_at` ';
          $query .= 'FROM `project` ';
          $query .= 'WHERE `id_project`=:id_project';

          $statement = $connexion->prepare($query);
          $result = $statement->execute($queryValues);
          $row = $statement->fetch(PDO::FETCH_ASSOC);

          $tableRows  = '';
          $tableRows .= '<tr><th class="align-middle">#</th><td>' . $row['id_project'] . '</td></tr>' . PHP_EOL;
          $tableRows .= '<tr><th class="align-middle">Intitulé</th><td>' . $row['title'] . '</td></tr>' . PHP_EOL;
          $tableRows .= '<tr><th class="align-middle">Résumé</th><td>' . $row['summary'] . '</td></tr>' . PHP_EOL;
          $tableRows .= '<tr><th class="align-middle">Description</th><td>' . $row['description'] . '</td></tr>' . PHP_EOL;
          $tableRows .= '<tr><th class="align-middle">Screenshot</th><td>' . $row['screenshot'] . '</td></tr>' . PHP_EOL;
          $tableRows .= '<tr><th class="align-middle">Réalisé le</th><td>' . $row['completed_at'] . '</td></tr>' . PHP_EOL;
          $tableRows .= '<tr><th class="align-middle">Actions</th><td>';
          $tableRows .= '<a href="project_update_form.php?id=' . $row['id_project'] . '" title="Modifier ce projet" class="btn btn-secondary btn-sm me-1"><i class="bi bi-pen"></i></a>';
              $tableRows .= '<a href="project_delete_form.php?id=' . $row['id_project'] . '" title="Supprimer ce projet" class="btn btn-danger btn-sm"><i class="bi bi-trash3"></i></a>';
          $tableRows .= '</td></tr>' . PHP_EOL;
        }
      } catch (PDOException $e) {
        echo $query;
        exit($e);
        $errors['pdo'] = "Une erreur s'est produite lors de la récupération du projet en base de données : veuillez contacter l'administrateur du site.";
        $_SESSION['errors'] = $errors;
        header('project_index.php');
        exit;
      }
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

include_once "admin_top.php";

?>
<div class=""> <!-- MAIN CONTENT : SART -->
  <h4 class="mb-3">Détail du projet n°<?= $idProject ?></h4>
  <div class="table-responsive small">
    <table class="table table-striped">
      <tbody>
        <?php

        echo $tableRows;

        ?>
      </tbody>
    </table>
    <a href="project_index.php" title="Retour à la liste des projets">Retour à la liste des projets</a>
  </div>
</div> <!-- MAIN CONTENT : END -->
<?php

include_once "admin_bottom.php";

?>