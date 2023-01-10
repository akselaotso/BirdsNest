<?php declare(strict_types=1);

require_once(__DIR__ . '/../global.php');
require_once(DATA_COLLECTION_MODULE);

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Birds nest</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Lexend&display=swap');

        * {
            font-family: 'Lexend', sans-serif;
            color: #151A1E;
            max-width: 70ch;
        }
        
        body {
            background-color: #f0f0f0;
        }
        
        main {
            padding: 10px;
        }
        
        main>p {
            margin: 50px 0;
        }
        
        #pilotData div p {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>    
    <main>
        <h2>
            Law breaking pilots in order form least to most recent.
        </h2>

        <p>
            A pilot lands on the list if they enter within 100m of the nest. The list remembers the worst violation for 10 minutes after the last violation. Display's name, phone number, email and shortest recorded distance from the nest.
        </p>
        
        <p>
            App by Otso Aksela, code on github at <a target="_blank" href="https://github.com/akselaotso/BirdsNest">https://github.com/akselaotso/BirdsNest</a>.
        </p>

        <div id="pilotDataDiv">
            <?php 
                $droneLocationXML = simplexml_load_file(DRONE_URL);

                $pilots = processed_drone_data($droneLocationXML, 100);
                
                file_put_contents(PILOT_DATA_ADDRESS, json_encode($pilots));

                $dataHTML = "";

                foreach ($pilots as $pilot) {
                    $dataHTML .= 
                        '<div>
                        <h3>' . $pilot['pilot'] . '</h3>
                        <p>' . $pilot['phone'] . ', ' . $pilot['email'] . ', ' . number_format($pilot['distance'], 2, '.', '') . ' meters</p>
                        </div>';
                }

                echo($dataHTML);
            ?>
        </div>

    </main>

    <script>
        /**
         * After window has loaded start the function. The function
         * Fetches data from the drones.json document, which is also 
         * in the public folder, and displays that data within the div 
         * with the id "pilotData".
         */
        window.onload = function fetch_data() {
            const container = document.getElementById("pilotDataDiv");
            const pilotDataURL = "http://" + window.location.hostname + "/BirdsNest/app/public/live_data_collection.php";

            /**
             * Every 2 seconds execute the fetch action with promises.
             * The code loops through every drone, formatting the array.
             * The array is then joined to a string and inserted to HTML.
             */ 
            setInterval(() => { 
                fetch(pilotDataURL).then((response) => { 
                    return response.json();
                }).then((drones) => {
                    var dataContent = "";
                    Object.entries(drones).forEach((element) => {
                        dataContent = dataContent.concat(`<div> 
                            <h3>${element[1].pilot}</h3> 
                            <p>${element[1].phone}, ${element[1].email}, ${element[1].distance.toFixed(2)} meters</p>
                            </div>`);
                    });
                    container.innerHTML = dataContent; 
                }).catch((error) => { 
                    console.log(error); 
                });
            }, 2000);
        }
    </script>
</body>
</html> 

