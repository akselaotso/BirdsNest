<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once('app/Modules/data_collection/data_collection.php');

class data_collection_test extends TestCase {
    public function test_distance_from_nest() {
        $result = 156.8216;
        $test = round(distance_from_nest(342000, 123000), 4);

        $this->assertEquals($result, $test);
    }

    /**
     * The sample data contains only drones that are older than 10 minutes. 
     * Adding a new drone and running function should result in only the new
     * drone remaining.
     */
    public function test_remove_older_than() {
        $sampleData = json_decode(file_get_contents('tests/sample_drones.json'), true);
        $addition["SN-Example5"] = array(
            "pilot" => "Example5",
            "phone" => "+1234567895",
            "email" => "Example5@example.com",
            "distance" => 50.3,
            "lastTime" => time()
        );

        $sampleData["SN-Example5"] = $addition["SN-Example5"];
        
        $this->assertEquals(remove_older_than($sampleData, 10), $addition);
    }


}

