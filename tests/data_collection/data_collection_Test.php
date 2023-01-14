<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

require_once('app/Modules/data_collection/data_collection.php');
require_once('app/global.php');

class data_collection_test extends TestCase {
    use MockeryPHPUnitIntegration;
 
    /**
     * Test first establishes the constants of the test. Then the test makes the mocked
     * version of the data_collection class. The data fetching functions are public and 
     * can be mocked. All times which they should be called are handled in the function
     * each separately. 
     * 
     * Test is a success if output is same as contents of sample_result.json.
     */
    public function test_fetch_pilot_info() 
    {   
        $droneLocationXML = simplexml_load_file('tests/data_collection/sample_full_xml.xml');
        $result = json_decode(file_get_contents('tests/data_collection/sample_result.json'), true);
        $time = 1673179916;

        $get_content = \Mockery::mock('data_collection');
        $get_content->expects('processed_drone_data')->passthru();
        $get_content->expects('time_function')->andReturn($time)->zeroOrMoreTimes();
        $get_content->expects('file_put_contents_function')->andReturn(NULL)->zeroOrMoreTimes();
        $get_content->expects('file_get_contents_function')->with(PILOT_URL . 'SN-Example9')->andReturn(file_get_contents('tests/data_collection/sample_pilot_info.json'))->once();
        $get_content->expects('file_get_contents_function')->with(PILOT_URL . 'SN-Example8')->andReturn(false)->once();
        $get_content->expects('file_get_contents_function')->with(PILOT_DATA_TIMING_ADDRESS)->andReturn($time - 3)->once();
        $get_content->expects('file_get_contents_function')->with(PILOT_DATA_ADDRESS)->andReturn(file_get_contents('tests/data_collection/sample_drones.json'))->once();

        $data = $get_content->processed_drone_data($droneLocationXML, 100);

        $this->assertEquals($result, $data);
    }
}

