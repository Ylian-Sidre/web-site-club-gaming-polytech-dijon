<?php

require_once dirname(__DIR__, 3) . '/config/php_config/newsClient.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    
    $db = $client -> selectDatabase('PolyGaming');    
    $collection = $db -> selectCollection('News');
    $cursor = $collection ->find([]);
    $news = iterator_to_array($cursor);

    echo json_encode($news);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "error" => "Erreur de base de données",
        "message" => $e->getMessage()
    ]);
}
?>