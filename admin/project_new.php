<?php

session_start();

include_once "../utils/regexps.php";

$errors = [];
$success = [];

/**
 * ******************** [1] Check if submitted form is valid
 */
if ($_SERVER["REQUEST_METHOD"] === "POST") { // Is method allowed ?
  if (isset($_POST['title']) && trim($_POST['title']) !== '') { // Required field value
    // OK
    $title = trim($_POST['title']);
    if (strlen($title) > 100) { // Format check
      // KO
      $errors['title'] = "La longueur du champ 'Intitulé' ne doit pas excéder 100 caractères.";
    }
  } else {
    // KO
    $errors['title'] = "La saisie du champ 'Intitulé' est obligatoire.";
  }

  if (isset($_POST['summary']) && trim($_POST['summary']) !== '') { // Required field value
    // OK
    $summary = trim($_POST['summary']);
    if (!strlen($_POST['summary']) > 500) { // Format check
      // KO
      $errors['summary'] = "La longueur du champ 'Résumé' ne doit pas excéder 500 caractères.";
    }
  } else {
    // KO
    $errors['summary'] = "La saisie du champ 'Résumé' est obligatoire.";
  }

  if (isset($_POST['description']) && trim($_POST['description']) !== '') { // Optionnal field value
    // OK
    // TODO: Check description value length (e.g.: max. length)
    $description = trim($_POST['description']); 
  } else {
    // Not KO: Description value is empty
    $description = '';
  }

  if (isset($_POST['completed_at']) && trim($_POST['completed_at']) !== '') { // Required field value
    // OK
    $completedAt = trim($_POST['completed_at']);
    if (!preg_match($validPatterns['dateHyphens'], $completedAt)) { // Format check
      // KO
      $errors['completed_at'] = "Le champ 'Réalisé le' doit respecter le format mm/dd/yyyy (ex. : 12/05/2024).";
    }
  } else {
    // KO
    $errors['completed_at'] = "La saisie du champ 'Réalisé le' est obligatoire.";
  }

  if (isset($_FILES['screenshot']) && $_FILES['screenshot']['name'] !== '') { // Required field value
    // OK
    if ($_FILES['screenshot']['error'] === UPLOAD_ERR_OK) {
      // OK
      $imgInfo = getimagesize($_FILES['screenshot']['tmp_name']);
      if ($imgInfo !== FALSE) { // This is an image
        //OK
        $screenshotFileType = $imgInfo[2];
        if (($screenshotFileType === IMAGETYPE_JPEG) || ($screenshotFileType === IMAGETYPE_PNG)) { // Image format allowed types (JPEG or PNG)
          // OK
          if (strlen(pathinfo(trim($_FILES['screenshot']['name']), PATHINFO_FILENAME)) <= 237) { // 255 - 13 (uniqid()) - 1 (underscore) - 1 (dot) - 3 (extension)
            // OK
            $screenshot = $_FILES['screenshot'];
          } else {
            // KO
            $errors['screenshot'] = "La longueur du nom du fichier '<i>Screenshot</i> associé' ne doit pas excéder 237 caractères.";
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
      // TODO: Add a more precise message and treat error precisely
      $errors['screenshot'] = "Une erreur inattendue s'est produite lors de l'<i>upload</i> du fichier '<i>Screenshot</i> associé'";
    }
  } else {
    // KO
    $errors['screenshot'] = "Le fichier '<i>Screenshot</i> associé' est obligatoire.";
  }
} else {
  header("Location: ../405.php");
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
 * ******************** [2-B] Submitted form is valid ➔ Data sanitization and escaping
 */

// $title = addslashes(htmlspecialchars($title, ENT_NOQUOTES | ENT_SUBSTITUTE));
// $summary = addslashes(htmlspecialchars($summary, ENT_NOQUOTES | ENT_SUBSTITUTE));
// $description = addslashes(htmlspecialchars($description, ENT_NOQUOTES | ENT_SUBSTITUTE));
// $completedAt = addslashes(htmlspecialchars($completedAt, ENT_NOQUOTES | ENT_SUBSTITUTE));

// Note: escaping (see above) is unecessary as we later use a prepared statement
$title = htmlspecialchars($title, ENT_NOQUOTES | ENT_SUBSTITUTE);
$summary = htmlspecialchars($summary, ENT_NOQUOTES | ENT_SUBSTITUTE);
$description = htmlspecialchars($description, ENT_NOQUOTES | ENT_SUBSTITUTE);
$completedAt = htmlspecialchars($completedAt, ENT_NOQUOTES | ENT_SUBSTITUTE);

/**
 * ******************** [3] Screenshot file management
 */

$screenshotExt = ($screenshotFileType === IMAGETYPE_JPEG) ? 'jpg' : 'png';
$screenshotFilename = pathinfo(trim($screenshot['name']), PATHINFO_FILENAME);
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

// FIXME: Secure db connexion parameters
$host = 'localhost';
$dbName = 'cyberfolio_demo';
$user = 'mentor'; // Your MySQL user username
$pass = 'superMentor'; // Your MySQL user password

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
    ':id_profile' => 1, // FIXME: Temporary hard-coded value
  ];

  $statement = $connexion->prepare($query);
  $statement->execute($queryValues);

  $connexion = null;

  $success['project_creation'] = "La création du nouveau projet a réussi.";
  $_SESSION['success'] = $success;
  header('Location: project_index.php');
  exit;
} catch (PDOException $e) {
  $connexion = null;

  // TODO: Remove uploaded screenshot file from 'uploads' folder
  $errors['pdo'] = "Une erreur s'est produite lors de l'enregistrement du projet en base de données : veuillez contacter l'administrateur du site.";
  $_SESSION['form_errors'] = $errors;
  header('Location: project_new_form.php');
  exit;
}
