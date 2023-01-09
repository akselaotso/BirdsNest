<?php declare(strict_types=1);

require_once('global.php');
require_once('Templates/render.php');
require_once('Modules/data_collection/data_collection.php');

// Only allow cli starts for security
if (php_sapi_name() === 'cli'){

    set_time_limit(0);

    // Update data and render page indefinetly with a break between each cycle. Has to be killed from the cli
    while (TRUE) {
        $droneLocationXML = simplexml_load_file(DRONE_URL);

        if (!is_bool($droneLocationXML)) {
            $drones = processed_drone_data($droneLocationXML, 100);

            file_put_contents(PILOT_DATA_ADDRESS, json_encode($drones));

            render_front_page();
            echo("/");
        } else {
            echo(" # ");
        }

        sleep(DAEMON_SLEEP_TIME);
    }    
} else {
    exit("Please use the CLI.");
}

