<?php

require_once dirname(__DIR__, 3) . '/config/php_config/newsClient.php';

$db = $client -> selectDatabase('PolyGaming');    
$collection = $db -> selectCollection('News');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Erreur : ID de l'événement manquant.");
}

try {
$id = new MongoDB\BSON\ObjectId($_GET['id']);
$news = $collection->findOne(['_id' => $id]);

    if (!$news) {
        die("Événement introuvable dans la base de données.");
    }
} catch (Exception $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier la news</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="menuButton">
        <a class="redirection-button" href="../../html/private/news_list.php">Retour à la liste</a>
    </div>

    <h2>Modifier la news</h2>

    <form action="update_news.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $news['_id'] ?>">

        <label>Titre :</label><br>
        <input type="text" name="title" value="<?= htmlspecialchars($news['title']) ?>" required><br><br>

        <label>Date :</label><br>
        <input type="date" name="date" value="<?= $news['date'] ?>" required><br><br>

        <label>Description :</label><br>
        <textarea name="description" rows="5"><?= htmlspecialchars($news['description']) ?></textarea><br><br>

        <p>Image actuelle : <strong><?= $news['imageUrl'] ?? 'Aucune' ?></strong></p>
        <label>Remplacer l'image :</label><br>
        <input type="file" name="image"><br><br>

        <button type="submit" class="redirection-button">Enregistrer les modifications</button>
    </form>
</body>
</html>