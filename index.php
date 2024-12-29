<?php

/**
 * ******************** [1] Get all records from database
 */

// FIXME: Secure db connexion paramters
$host = 'localhost';
$dbName = 'cyberfolio_demo';
$user = 'mentor'; // Your MySQL user username
$pass = 'superMentor'; // Your MySQL user password
$projectCards = '';
$emptyProject = '<article class="empty-project"><div>Aucun projet à afficher</div></article>' . PHP_EOL;

try {
    $connexion = new PDO("mysql:host=$host;dbname=$dbName", $user, $pass);

    $query = 'SELECT COUNT(*) FROM `project`';

    $results = $connexion->query($query);
    $resultsCount = $results->fetchColumn();

    if ($resultsCount === 0) {
        $projectCards = $emptyProject;
    } else {
        $query = 'SELECT `id_project`, `title`, `summary`, `description`, `screenshot`, `completed_at` FROM `project`';

        $results = $connexion->query($query, PDO::FETCH_ASSOC);

        foreach ($results as $row) {
            $projectCards .= '<article>' . PHP_EOL;
            $projectCards .= '<h2>' . $row['title'] . '<a href="#"><i class="light-icon-external-link"></i></a></h2>' . PHP_EOL;
            $projectCards .= '<p class="truncate">' . $row['description'] . '</p>' . PHP_EOL;
            $projectCards .= '<img src="public/uploads/' . $row['screenshot'] . '" alt="Screenshot du projet \'' . $row['title'] . '\'">' . PHP_EOL;
            $projectCards .= '<ul>' . PHP_EOL;
            $projectCards .= '<li><img src="public/uploads/html_logo.svg" alt="Logo technologie HTML"></li>' . PHP_EOL;
            $projectCards .= '<li><img src="public/uploads/css_logo.svg" alt="Logo technologie CSS"></li>' . PHP_EOL;
            $projectCards .= '<li><img src="public/uploads/javascript_logo.svg" alt="Logo technologie JavaScript"></li>' . PHP_EOL;
            $projectCards .= '<li><img src="public/uploads/php_logo.svg" alt="Logo technologie PHP"></li>' . PHP_EOL;
            $projectCards .= '<li><img src="public/uploads/mysql_logo.svg" alt="Logo technologie MySQL"></li>' . PHP_EOL;
            $projectCards .= '</ul>' . PHP_EOL;
            $projectCards .= '</article>' . PHP_EOL;
        }
    }
} catch (PDOException $e) {
    $projectCards = $emptyProject;
} finally {
    $connexion = null;
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>John Doe - Développeur Web full-stack - Portfolio</title>
    <link href="https://unpkg.com/light-icons/dist/light-icon.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <header>
        <nav>
            <a href="." alt="Accueil"><img src="assets/img/guardia_cs_logo.svg"></a>
            <h1>John Doe - Développeur Web full-stack</h1>
            <ul>
                <li>Accueil</li>
                <li>Projets</li>
                <li>Compétences</li>
                <li>CV</li>
                <li>Contact</li>
            </ul>
        </nav>
    </header>
    <main>
        <section id="projects">
            <?php

            echo $projectCards;

            ?>
        </section>
    </main>
    <footer>
        <p>© 2024 - John Doe. Tous droits réservés. <a>Mentions légales</a> | <a>C.G.U.</a>
            | <a>C.G.V.</a></p>
    </footer>
</body>

</html>