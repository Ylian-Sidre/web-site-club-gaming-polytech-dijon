<?php
require_once dirname(__DIR__, 3) . '/config/php_config/newsManager.php';

header('Content-Type: application/json');

try {
    $manager = new MongoDB\Driver\Manager($uri);
    
    $options = ['sort' => ['date' => -1]];
    $query = new MongoDB\Driver\Query([], $options);

    $cursor = $manager->executeQuery("PolyGaming.News", $query);

    $news = [];

    foreach ($cursor as $document) {
        $newsDate = new DateTime();
        
        if (is_numeric($document->date)) {
            $newsDate->setTimestamp((int)($document->date / 1000));
        } 
        elseif ($document->date instanceof MongoDB\BSON\UTCDateTime) {
            $newsDate = $document->date->toDateTime();
        }
        else {
            try {
                $newsDate = new DateTime($document->date);
            } catch (Exception $e) {
                continue; 
            }
        }
        
        $item = [
            'title'       => $document->title,
            'description' => $document->description,
            'imageUrl'    => $document->imageUrl,
            'date'        => $newsDate->format('Y-m-d')
        ];

        $news[] = $item;
    }

    echo json_encode(['news' => $news]);

} catch (MongoDB\Driver\Exception\Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>