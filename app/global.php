<?php declare(strict_types=1);

/**
 * Most all relevant constants in the project can be easily altered from
 * the global.php file. The customisation is not really necessary for the 
 * scope of this project, but in a more complicated app could be helpful.
 */

// Project
define('PROJECT_ROOT_DIRECTORY', __DIR__);
define('PILOT_DATA_ADDRESS', PROJECT_ROOT_DIRECTORY . '/public/drones.json');
define('DATA_COLLECTION_MODULE', PROJECT_ROOT_DIRECTORY . '/Modules/data_collection/data_collection.php');
define('DAEMON_SLEEP_TIME', 2);

// External
define('DRONE_URL', 'http://assignments.reaktor.com/birdnest/drones');
define('PILOT_URL', 'http://assignments.reaktor.com/birdnest/pilots/');

