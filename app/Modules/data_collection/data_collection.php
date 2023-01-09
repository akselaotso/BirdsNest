<?php declare(strict_types=1);

require_once(__DIR__ . '../../../global.php');

/**
 * distance_from_nest takes a coordinate as y and x components and returns the 
 * distance from the birds nest.
 * 
 * @param float $y
 * @param float $x
 * @return float $distance in meters
 */
function distance_from_nest(float $xCoordinate, float $yCoordinate): float {
    $nestXCoordinate = 250000;
    $nestYCoordinate = 250000;

    $ySeparation = abs($yCoordinate - $nestYCoordinate);
    $xSeparation = abs($xCoordinate - $nestXCoordinate);
    $distance = sqrt(pow($ySeparation, 2) + pow($xSeparation, 2)) / 1000 ;

    return $distance;
}

/**
 * remove_older_than removes items from an array with a timestamp older than the 
 * determined minutes.
 * 
 * @param array $array is the input array from which the items are removed
 * @param int $minutes is the age limit in minutes before something is removed.
 * @return array $droneArray returns an array from which the items have been removed.
 */
function remove_older_than(array $droneArray, int $minutes): array {
    foreach (array_keys($droneArray) as $drone) {
        if ($droneArray[$drone]['lastTime'] < time() - $minutes * 60) {
            unset($droneArray[$drone]);
        }
    }

    return $droneArray;
}

/**
 * fetch_pilot_data fetches the information of the pilot of a drone based on its serial number
 * and returns it as an array. 
 * 
 * @param string $serialNumber is the serial number of the drone used to fetch the pilot's information
 * @return array returns an array of the pilots information
 */
function fetch_pilot_info(string $serialNumber) {
    $pilotData = json_decode(file_get_contents(PILOT_URL . $serialNumber), true);

    return array(
        "pilot" => $pilotData['firstName'] . " " . $pilotData['lastName'],
        "phone" => $pilotData['phoneNumber'],
        "email" => $pilotData['email'],
    );
}


/**
 * fetch_drone_data fetches the currently visible drones and updates the drones.json 
 * document in the public folder appropriately. 
 * 
 * @param SimpleXMLElement $droneXml contains the fetched information of the drones and their locations
 * @param int $requiredDistance is the minimum allowed distance from the nest
 * @return array $illegalDrones containing info of pilot and drone location for violations within the
 * appropriate time interval
 */
function processed_drone_data(SimpleXMLElement $droneXML, int $requiredDistance): array {
    // Get the previous drone and pilot information from the server 
    $illegalDrones = json_decode(file_get_contents(PILOT_DATA_ADDRESS), true);

    // If JSON document is empty the above returns null hence redefining as empty array 
    if (is_null($illegalDrones)) {
        $illegalDrones = array();
    }

    /**
     * For each drone in $droneXML check if location is too close to nest. If it is, fetch 
     * information if necessary and add the drone to the list of illegal drones.
     */
    foreach ($droneXML -> capture -> children() as $drone) {
        $droneX = floatval($drone -> positionX);
        $droneY = floatval($drone -> positionY);
        $droneDistanceFromNest = distance_from_nest($droneX, $droneY);

        if ($droneDistanceFromNest < $requiredDistance) {
            $droneNumber = strval($drone->serialNumber); 

            /**
             * If the drone is already in the array, rewrite the time and check distance
             * and update if necessary. Else fetch information and add it.  
             */
            if (array_key_exists($droneNumber, $illegalDrones)) {
                if ($droneDistanceFromNest < $illegalDrones[$droneNumber]['distance']) {
                    $illegalDrones[$droneNumber]['distance'] = $droneDistanceFromNest;
                }
            } else {
                $illegalDrones[$droneNumber] = fetch_pilot_info($droneNumber);

                $illegalDrones[$droneNumber]['distance'] = $droneDistanceFromNest;
            }

            // Set timestamp for latest violation
            $illegalDrones[$droneNumber]['lastTime'] = time();
        }
    }

    return remove_older_than($illegalDrones, 10);
}

