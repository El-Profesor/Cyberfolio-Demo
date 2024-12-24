<?php

session_start();

$errors = [];
$success = [];

/**
 * ******************** [1] Check if submitted form is valid
 */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  if (isset($_POST['id']) && trim($_POST['id']) !== '') { // Required project 'id' value
    // OK
    $filterOptions = [
      'options' => [
        'min_range' => 1,
      ],
    ];
    if (filter_var($_POST['id'], FILTER_VALIDATE_INT, $filterOptions) !== FALSE) {
      // OK
      $idProject = $_POST['id'];

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

        $query  = 'DELETE FROM `project` WHERE `id_project`=:id_project';

        $queryValues = [
          ':id_project' => $idProject,
        ];

        $statement = $connexion->prepare($query);
        $statement->execute($queryValues);
        $isDeleted = $statement->rowCount() === 1;

        $connexion = null;

        if ($isDeleted) {
          // TODO: Remove project screenshot image from 'uploads' folder
          $success['project_deletion'] = "La suppression du projet a réussi.";
          $_SESSION['success'] = $success;
        } else {
          $errors['project_deletion'] = "La suppression du projet n'a pas pu aboutir : le projet n° $idProject n'existe pas ou a déjà été supprimé.";
          $_SESSION['errors'] = $errors;
        }
        header('Location: project_index.php');
        exit;
      } catch (PDOException $e) {
        $errors['pdo'] = "Une erreur s'est produite lors de la suppression du projet de la base de données : veuillez contacter l'administrateur du site.";
        $_SESSION['errors'] = $errors;
        header('Location: project_index.php');
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
  // KO: Suspicious request (request method is not POST)
  header("Location: ../404.php");
  exit;
}