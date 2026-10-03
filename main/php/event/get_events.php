<?php
header('Content-Type: application/json');

require_once dirname(__DIR__, 3) . '/config/php_config/eventManager.php';

try {
    $manager = new MongoDB\Driver\Manager($uri);
    
    $query = new MongoDB\Driver\Query([]);

    $cursor = $manager->executeQuery("PolyGaming.Events", $query);

    $ongoing = [];
    $past = [];
    $now = new DateTime(); 

    foreach ($cursor as $document) {
        $eventDate = new DateTime();
        
        if (is_numeric($document->date)) {
            $eventDate->setTimestamp((int)($document->date / 1000));
        } 
        elseif ($document->date instanceof MongoDB\BSON\UTCDateTime) {
            $eventDate = $document->date->toDateTime();
        }
        else {
            try {
                $eventDate = new DateTime($document->date);
            } catch (Exception $e) {
                continue; 
            }
        }
        
        $item = [
            'title'       => $document->title,
            'description' => $document->description,
            'imageUrl'    => $document->imageUrl,
            'date'        => $eventDate->format('Y-m-d')
        ];
        if ($eventDate >= $now) {
            $ongoing[] = $item;
        } else {
            $past[] = $item;
        }
    }

    echo json_encode(['ongoing' => $ongoing, 'past' => $past]);

} catch (MongoDB\Driver\Exception\Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>