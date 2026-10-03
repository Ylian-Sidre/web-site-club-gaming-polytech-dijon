<?php

require_once dirname(__DIR__, 3) . '/config/php_config/newsClient.php';

$db = $client -> selectDatabase('PolyGaming');
$collection = $db -> selectCollection('News');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $date = $_POST['date'];
    $imagePath = "";

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $uploadsDir = 'assets/news/';

        if (!is_dir($uploadsDir)) mkdir($uploadsDir, 0777, true);

        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $targetPath = $uploadsDir . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $imagePath = $targetPath;
        }
    }

    $newNews = [
        'title' => $title,
        'description' => $description,
        'date' => new MongoDB\BSON\UTCDateTime(strtotime($date) * 1000), 
        'imageUrl' => $imagePath,
        'createdAt' => new MongoDB\BSON\UTCDateTime()
    ];

    try {
        $insertResult = $collection->insertOne($newNews);
        echo "Événement ajouté avec succès ! ID : " . $insertResult->getInsertedId();
    } catch (Exception $e) {
        echo "Erreur lors de l'insertion : " . $e->getMessage();
    }
}

?>