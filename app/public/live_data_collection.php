<?php declare(strict_types=1);

require_once(__DIR__ . '../../global.php');
require_once(DATA_COLLECTION_MODULE);

header('Content-Type: application/json');

$droneLocationXML = simplexml_load_file(DRONE_URL);

$drones = processed_drone_data($droneLocationXML, 100);

file_put_contents(PILOT_DATA_ADDRESS, json_encode($drones));

echo json_encode($drones);

