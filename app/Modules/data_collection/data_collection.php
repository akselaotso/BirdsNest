<?php declare(strict_types=1);

require_once(__DIR__ . '/../../global.php');

/**
 * Data collection module responsible for collecting data and directly returning it in a 
 * displayable format. Has one public function, processed_drone_data.
 */
class data_collection
{
    /**
     * A wrapper for file_get_contents to enable testing
     * 
     * @param string $url - path to file
     * @return bool|string - the contents of the file
     */
    public function file_get_contents_function(string $url)
    {
        return file_get_contents($url);
    }

    /**
     * a wrapper for getting time, helps with testing.
     * @return int - the current time
     */
    public function time_function(): int 
    {
        return time();
    }

    /**
     * file_put_contents function for testing.
     */
    public function file_put_contents_function(string $url, string $content): void 
    {
        file_put_contents($url, $content);
    }
    
    /**
     * distance_from_nest takes a coordinate as y and x components and returns the 
     * distance from the birds nest.
     * 
     * @param float $y
     * @param float $x
     * @return float $distance in meters
     */
    private function distance_from_nest(float $xCoordinate, float $yCoordinate): float
    {
        $nestXCoordinate = 250000;
        $nestYCoordinate = 250000;

        $ySeparation = abs($yCoordinate - $nestYCoordinate);
        $xSeparation = abs($xCoordinate - $nestXCoordinate);
        $distance = sqrt(pow($ySeparation, 2) + pow($xSeparation, 2)) / 1000;

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
    private function remove_older_than(array $droneArray, int $minutes): array
    {
        foreach (array_keys($droneArray) as $drone) {
            if ($droneArray[$drone]['lastTime'] < $this->time_function() - $minutes * 60) {
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
    private function fetch_pilot_info(string $serialNumber)
    {
        $pilotDataFile = $this->file_get_contents_function(PILOT_URL . $serialNumber);

        if (is_bool($pilotDataFile)) {
            $pilotData = array(
                "firstName" => "Unknown",
                "lastName" => "",
                "phoneNumber" => "Unknown number",
                "email" => "Unknown@unknown"
            );
        } else {
            $pilotData = json_decode($pilotDataFile, true);
        }

        return array(
            "pilot" => $pilotData['firstName'] . " " . $pilotData['lastName'],
            "phone" => $pilotData['phoneNumber'],
            "email" => $pilotData['email'],
        );
    }

    /**
     * Used to check if drones.json was updated less than 2 seconds ago, if updated more
     * than 2 seconds ago. This way if multiple users are on the site it wont go overboard
     * with updating.
     * 
     * @return bool - if true the file is less than 2 seconds old
     */    
    private function checked_less_than_2_seconds_ago(): bool
    {
        $lastTime = $this->file_get_contents_function(PILOT_DATA_TIMING_ADDRESS);
        if ($lastTime > $this->time_function() - 2) {
            return true;
        }
        file_put_contents(PILOT_DATA_TIMING_ADDRESS, $this->time_function());
        return false;
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
    public function processed_drone_data(SimpleXMLElement $droneXML, int $requiredDistance): array
    {
        $illegalDrones = json_decode($this->file_get_contents_function(PILOT_DATA_ADDRESS), true);

        if ($this->checked_less_than_2_seconds_ago() == true) {
            return $this->remove_older_than($illegalDrones, 10);
        }

        if (is_null($illegalDrones)) {
            $illegalDrones = array();
        }

        /**
         * For each drone in $droneXML check if location is too close to nest. If it is, fetch 
         * information if necessary and add the drone to the list of illegal drones.
         */
        foreach ($droneXML->capture->children() as $drone) {
            $droneX = floatval($drone->positionX);
            $droneY = floatval($drone->positionY);
            $droneDistanceFromNest = $this->distance_from_nest($droneX, $droneY);

            if ($droneDistanceFromNest < $requiredDistance) {
                $droneNumber = strval($drone->serialNumber);

                if (array_key_exists($droneNumber, $illegalDrones)) {
                    if ($droneDistanceFromNest < $illegalDrones[$droneNumber]['distance']) {
                        $illegalDrones[$droneNumber]['distance'] = $droneDistanceFromNest;
                    }
                } else {
                    $illegalDrones[$droneNumber] = $this->fetch_pilot_info($droneNumber);
                    $illegalDrones[$droneNumber]['distance'] = $droneDistanceFromNest;
                }

                $illegalDrones[$droneNumber]['lastTime'] = $this->time_function();
            }
        }

        return $this->remove_older_than($illegalDrones, 10);
    }
}

