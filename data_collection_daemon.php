<?php declare(strict_types=1);

require_once('global.php');
require_once('Templates/render.php');
require_once('Modules/data_collection/data_collection.php');

// If program invoked from command line start the daemon.
if (php_sapi_name() === 'cli'){

    // Remove execution time limit
    set_time_limit(0);

    // Update data and render page indefinetly with a break between each cycle.
    while (TRUE) {
        // Get info of drones and pilots if drone enter within given meters from the nest in the past 10min
        $drones = process_drone_data(simplexml_load_file(DRONE_URL), 100);

        // Update JSON drones.json in public folder
        file_put_contents(PILOT_DATA_ADDRESS, json_encode($drones));

        // Re-render fron page
        render_front_page();

        sleep(DAEMON_SLEEP_TIME);
    }    
} else {
    exit("Please use the CLI.");
}

