<?php
require_once( "..\src\GeometryCoordinates.php" );
require_once( "..\src\Base\BaseCoordinates.php" );
require_once( "..\src\HaversineCoordinates.php" );
require_once( "..\src\MercatorCoordinates.php" );
use SR\GeoDataTest\Base\BaseCoordinates;
use SR\GeoDataTest\HaversineCoordinates;
use SR\GeoDataTest\MercatorCoordinates;
use SR\GeoDataTest\GeometryCoordinates;
try {
    if (isset($_GET["radius"]))
        $radius = $_GET["radius"];
    else
        $radius = 2000;
    $lat = $_GET["lat"];
    $lon = $_GET["lon"];
    
    //  Необходимо определить корректные настройки для подключения к БД
    $dsn = 'mysql:host=localhost;dbname=db'; // Define 'data source name'
    $user = 'root';                       // Define username
    $pass = '';                         // Defeine password
    $pdo = new PDO($dsn, $user, $pass);       // Create the PDO object
    $geoPointsProvider = new HaversineCoordinates($pdo, 'points');


    $allPoints = $geoPointsProvider->getAllPoints();
    $time = microtime(true);
    $firstCirclePoints = $geoPointsProvider->getPoints($lat, $lon, $radius);
    $timeTaken = microtime(true) - $time;


    $pointsSet = [
        'points' => [
            'all' => $allPoints,
            'firstCircle' => $firstCirclePoints
        ],
        'time' => $timeTaken,
    ];

    header("Content-type: Application/json");
    header('Access-Control-Allow-Origin: *');
    // echo $timeTaken; die();
    echo json_encode($pointsSet);
    die();

} catch (\Exception $e) {
    dump($e);
}?>