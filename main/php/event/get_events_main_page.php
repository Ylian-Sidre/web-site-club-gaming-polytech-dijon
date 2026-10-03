<?php
require_once dirname(__DIR__, 3) . '/config/php_config/eventManager.php';

use MongoDB\Driver\Query;
use MongoDB\Driver\Manager;

try {
    $manager = new Manager($uri);
    
    $options = ['sort' => ['date' => -1], 'limit' => 3];
    $query = new Query([], $options);
    $cursor = $manager->executeQuery("PolyGaming.Events", $query);

    $news = [];
    foreach ($cursor as $document) {
        $newsDate = new DateTime();
        if (is_numeric($document->date)) {
            $newsDate->setTimestamp((int)($document->date / 1000));
        } elseif ($document->date instanceof MongoDB\BSON\UTCDateTime) {
            $newsDate = $document->date->toDateTime();
        } else {
            try { $newsDate = new DateTime($document->date); } catch (Exception $e) { continue; }
        }

        $news[] = [
            'title'       => $document->title,
            'description' => $document->description,
            'imageUrl'    => $document->imageUrl,
            'date'        => $newsDate->format('d/m/Y')
        ];
    }
    echo json_encode($news);

} catch (MongoDB\Driver\Exception\Exception $e) {
    http_response_code(500);
    echo "Erreur de connexion : " . $e->getMessage();
}
?>