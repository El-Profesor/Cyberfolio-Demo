<?php

session_start();

include_once "../utils/regexp.php";

$errors = [];
$success = [];

/**
 * ******************** [1] Check if submitted form is valid
 */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  if (isset($_POST['title']) && trim($_POST['title']) !== '') { // Required field value
    // OK
    if (strlen($_POST['title']) <= 100) {
      // OK
      $title = $_POST['title'];
    } else {
      // KO
      $errors['title'] = "La longueur du champ 'Intitulé' ne doit pas excéder 100 caractères.";
    }
  } else {
    // KO
    $errors['title'] = "La saisie du champ 'Intitulé' est obligatoire.";
  }

  if (isset($_POST['summary']) && trim($_POST['summary']) !== '') { // Required field value
    // OK
    if (strlen($_POST['summary']) <= 500) {
      // OK
      $summary = $_POST['summary'];
    } else {
      // KO
      $errors['summary'] = "La longueur du champ 'Résumé' ne doit pas excéder 500 caractères.";
    }
  } else {
    // KO
    $errors['summary'] = "La saisie du champ 'Résumé' est obligatoire.";
  }

  if (isset($_POST['description']) && trim($_POST['description']) !== '') { // Optionnal field value
    // OK
    // TODO: Check description value length (max. length)
    $description = $_POST['description']; 
  } else {
    // Not KO: Description value is empty
    $description = '';
  }

  if (isset($_POST['completed_at']) && trim($_POST['completed_at']) !== '') { // Required field value
    // OK
    if (preg_match($validPatterns['dateHyphens'], $_POST['completed_at'])) {
      // OK
      $completedAt = $_POST['completed_at'];
    } else {
      // KO
      $errors['completed_at'] = "Le champ 'Réalisé le' doit respecter le format mm/dd/yyyy (ex. : 12/05/2024).";
    }
  } else {
    // KO
    $errors['completed_at'] = "La saisie du champ 'Réalisé le' est obligatoire.";
  }

  if (isset($_FILES['screenshot']) && !empty($_FILES['screenshot'])) { // Required field value
    // OK
    if ($_FILES['screenshot']['error'] === UPLOAD_ERR_OK) {
      // OK
      $imgInfo = getimagesize($_FILES['screenshot']['tmp_name']);
      if ($imgInfo !== FALSE) { // This is an image
        //OK
        if (($screenshotType = $imgInfo[2] === IMAGETYPE_JPEG) || ($screenshotType = $imgInfo[2] === IMAGETYPE_PNG)) { // Image format allowed types (JPEG or PNG)
          // OK
          if (strlen(trim($_FILES['screenshot']['name'])) <= 255) {
            // OK
            $screenshot = $_FILES['screenshot'];
          } else {
            // KO
            $errors['screenshot'] = "La longueur du nom du fichier '<i>Screenshot</i> associé' ne doit pas excéder 255 caractères.";
          }
        } else {
          $errors['screenshot'] = "Le fichier '<i>Screenshot</i> associé' doit être du type JPEG ou PNG.";
        }
      } else {
        // KO
        $errors['screenshot'] = "Le fichier '<i>Screenshot</i> associé' transmis doit être une image.";
      }
    } else {
      // KO
      // TODO: Add a more precise message and treat error
      $errors['screenshot'] = "Une erreur inattendue s'est produite lors de l'<i>upload</i> du fichier '<i>Screenshot</i> associé";
    }
  } else {
    // KO
    $errors['screenshot'] = "Le fichier '<i>Screenshot</i> associé' est obligatoire.";
  }
} else {
  header("Location: ../404.php");
  exit;
}

/**
 * ******************** [2-A] Submitted form is not valid (some errors occured)
 */

if (!empty($errors)) {
  $_SESSION['form_errors'] = $errors;
  header('Location: project_new_form.php');
  exit;
}

/**
 * ******************** [2-B] Submitted form is valid ➔ Data sanitization
 */

$title = htmlspecialchars(addslashes(trim($title)));
$summary = htmlspecialchars(addslashes(trim($summary)));
$description = htmlspecialchars(addslashes(trim($description)));
$completedAt = htmlspecialchars(addslashes(trim($completedAt)));

/**
 * ******************** [3] Screenshot file management
 */

// FIXME: Error on JPEG MIME type detection
$screenshotExt = ($screenshotType === IMAGETYPE_JPEG) ? 'jpg' : 'png';
$screenshotFilename = pathinfo($screenshot['name'], PATHINFO_FILENAME);
$finalFilename = uniqid() . '_' . $screenshotFilename . '.' . $screenshotExt;

$copySucceed = copy($screenshot['tmp_name'], "../public/uploads/" . $finalFilename);

if ($copySucceed !== TRUE) { // File copy into 'public/uploads/' folder failed
  // KO
  $errors['file_upload'] = "Une erreur s'est produite lors de l'<i>upload</i> du fichier '<i>Screenshot</i> associé' : veuillez contacter l'administrateur du site.";
  $_SESSION['form_errors'] = $errors;
  header('Location: project_new_form.php');
  exit;
}

/**
 * ******************** [4] Create the corresponding record in database
 */

$host = 'localhost';
$dbName = 'cyberfolio';
$user = 'mentor';
$pass = 'superMentor';

try {
  $connexion = new PDO("mysql:host=$host;dbname=$dbName", $user, $pass);
  $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $query  = 'INSERT INTO `project` (`title`, `summary`, `description`, `screenshot`, `completed_at`, `id_profile`) ';
  $query .= 'VALUES (:title, :summary, :description, :screenshot, :completed_at, :id_profile)';

  $queryValues = [
    ':title' => $title,
    ':summary' => $summary,
    ':description' => $description,
    ':screenshot' => $finalFilename,
    ':completed_at' => $completedAt,
    ':id_profile' => 1, // Temporary hard-coded value
  ];

  $statement = $connexion->prepare($query);
  $statement->execute($queryValues);

  $connexion = null;

  $success['project_creation'] = "La création du nouveau projet a réussi.";
  $_SESSION['success'] = $success;
  header('Location: project_index.php');
  exit;
} catch (PDOException $e) {
  $errors['pdo'] = "Une erreur s'est produite lors de l'enregistrement du projet en base de données : veuillez contacter l'administrateur du site.";
  $_SESSION['form_errors'] = $errors;
  header('Location: project_new_form.php');
  exit;
}
