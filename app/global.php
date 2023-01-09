<?php declare(strict_types=1);

/**
 * Most all relevant constants in the project can be easily altered from
 * the global.php file. The customisation is not really necessary for the 
 * scope of this project, but in a more complicated app could be helpful.
 */

// Project
define('FRONTPAGE_ADDRESS', __DIR__ . '/public/index.html');
define('PILOT_DATA_ADDRESS', __DIR__ . '/public/drones.json');
define('TEMPLATE_PATH', __DIR__ . '/Templates/default/');
define('DAEMON_SLEEP_TIME', 2);

// External
define('DRONE_URL', 'http://assignments.reaktor.com/birdnest/drones');
define('PILOT_URL', 'http://assignments.reaktor.com/birdnest/pilots/');

