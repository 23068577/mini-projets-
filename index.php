<?php

// db.php : Fichier de connexion à la base de données



$host = 'localhost'; // Hôte de la base de données

$dbname = 'blog_collaboratif'; // Nom de la base

$username = 'root'; // Nom d'utilisateur MySQL

$password = ''; // Mot de passe MySQL



try {

    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {

    die("Erreur de connexion : " . $e->getMessage());

}

?>
<?php

require 'db.php';



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = htmlspecialchars($_POST['name']);

    $email = htmlspecialchars($_POST['email']);

    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $bio = htmlspecialchars($_POST['bio']);

    $skills = htmlspecialchars($_POST['skills']);



    // Upload de l'image (optionnel)

    $photo = null;

    if (!empty($_FILES['photo']['name'])) {

        $photo = 'uploads/' . basename($_FILES['photo']['name']);

        move_uploaded_file($_FILES['photo']['tmp_name'], $photo);

    }



    // Insertion dans la base

    $sql = "INSERT INTO utilisateurs (nom, email, mot_de_passe, bio, photo, compétences)

            VALUES (:name, :email, :password, :bio, :photo, :skills)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([

        ':name' => $name,

        ':email' => $email,

        ':password' => $password,

        ':bio' => $bio,

        ':photo' => $photo,

        ':skills' => $skills,

    ]);



    header('Location: login.html');

    exit();

}

?>

<?php

require 'db.php';

session_start();



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = htmlspecialchars($_POST['email']);

    $password = $_POST['password'];



    $sql = "SELECT * FROM utilisateurs WHERE email = :email";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([':email' => $email]);

    $user = $stmt->fetch();



    if ($user && password_verify($password, $user['mot_de_passe'])) {

        $_SESSION['user_id'] = $user['id'];

        $_SESSION['user_name'] = $user['nom'];

        header('Location: index.html');

        exit();

    } else {

        echo "Email ou mot de passe incorrect.";

    }

}

?>

<?php

require 'db.php';

session_start();



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {

    $title = htmlspecialchars($_POST['title']);

    $content = htmlspecialchars($_POST['content']);

    $category = intval($_POST['category']);

    $author_id = $_SESSION['user_id'];

    $date = date('Y-m-d H:i:s');



    $sql = "INSERT INTO articles (titre, contenu, date_publication, id_auteur, id_categorie)

            VALUES (:title, :content, :date, :author_id, :category)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([

        ':title' => $title,

        ':content' => $content,

        ':date' => $date,

        ':author_id' => $author_id,

        ':category' => $category,

    ]);



    header('Location: index.html');

    exit();

} else {

    echo "Veuillez vous connecter pour publier un article.";

}

?>

<?php

require 'db.php';



$sql = "SELECT articles.*, utilisateurs.nom AS auteur, categories.nom_catégorie AS categorie

        FROM articles

        JOIN utilisateurs ON articles.id_auteur = utilisateurs.id

        JOIN categories ON articles.id_categorie = categories.id

        ORDER BY date_publication DESC";

$stmt = $pdo->query($sql);

$articles = $stmt->fetchAll();

?>



<!DOCTYPE html>

<html lang="fr">

<head>

    <meta charset="UTF-8">

    <title>Accueil - Blog Collaboratif</title>

</head>

<body>

    <h1>Articles Récents</h1>

    <?php foreach ($articles as $article): ?>

        <article>

            <h2><?= htmlspecialchars($article['titre']) ?></h2>

            <p><strong>Auteur :</strong> <?= htmlspecialchars($article['auteur']) ?></p>

            <p><strong>Catégorie :</strong> <?= htmlspecialchars($article['categorie']) ?></p>

            <p><?= nl2br(htmlspecialchars(substr($article['contenu'], 0, 100))) ?>...</p>

            <a href="article.php?id=<?= $article['id'] ?>">Lire la suite</a>

        </article>

    <?php endforeach; ?>

</body>

</html>

<?php

require 'db.php';

session_start();



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {

    $content = htmlspecialchars($_POST['comment']);

    $article_id = intval($_POST['article_id']);

    $user_id = $_SESSION['user_id'];

    $date = date('Y-m-d H:i:s');



    $sql = "INSERT INTO commentaires (contenu, date_commentaire, id_utilisateur, id_article)

            VALUES (:content, :date, :user_id, :article_id)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([

        ':content' => $content,

        ':date' => $date,

        ':user_id' => $user_id,

        ':article_id' => $article_id,

    ]);



    header("Location: article.php?id=$article_id");

    exit();

}

?>

