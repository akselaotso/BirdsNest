<?php declare(strict_types=1);

// Project
define('PROJECT_ROOT_DIRECTORY', __DIR__);
define('PILOT_DATA_ADDRESS', PROJECT_ROOT_DIRECTORY . '/data/drones.json');
define('PILOT_DATA_TIMING_ADDRESS', PROJECT_ROOT_DIRECTORY . '/data/drones_json_last_update.txt');
define('DATA_COLLECTION_MODULE', PROJECT_ROOT_DIRECTORY . '/Modules/data_collection/data_collection.php');
define('LIVE_DATA_UPDATE_PATH', '/live_data_collection.php'); // path from public root

// External
define('DRONE_URL', 'http://assignments.reaktor.com/birdnest/drones');
define('PILOT_URL', 'http://assignments.reaktor.com/birdnest/pilots/');

