<?php
require_once dirname(__DIR__, 3) . '/config/php_config/eventClient.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = $client -> selectDatabase('PolyGaming');    
    $collection = $db -> selectCollection('Events');
    $id = new MongoDB\BSON\ObjectId($_POST['id']);

    $updateData = [
        'title' => $_POST['title'],
        'date' => $_POST['date'],
        'description' => $_POST['description']
    ];

        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            
            $uploadsDir = '../../assets/events/';
            if (!is_dir($uploadsDir)) {
                mkdir($uploadsDir, 0777, true);
            }

            $fileName = time() . '_' . basename($_FILES['image']['name']);
            $targetPath = $uploadsDir . $fileName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                $updateData['imageUrl'] = 'assets/events/' . $fileName;
            }
        }

    $collection->updateOne(['_id' => $id], ['$set' => $updateData]);

    header("Location: ../../html/private/events_list.php?status=success");
    exit();
}