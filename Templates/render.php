<?php declare(strict_types=1);

require_once(__DIR__ . '../../global.php');

/**
 * the function renders the front page and saves the result directly in the correct file, 
 * if there are changes to the file.
 */
function render_front_page(): void {
    /** 
     * @var string $startHTML contains all HTML before the data field
     * @var string $endHTML contains all HTML after the data field
     * @var string $dataHTML will contain all the HTML for the data field 
     * 
     * A more complicated division might be more convenient in a larger app. However
     * for the purposes of this project a simple template is quite sufficient.
    */
    $startHTML = file_get_contents(TEMPLATE_PATH . 'start.html');
    $endHTML = file_get_contents(TEMPLATE_PATH . 'end.html');
    $dataHTML = '';

    $pilotData = json_decode(file_get_contents(PILOT_DATA_ADDRESS), true);

    // Loops through the JSON turned array and adds the appropriate string to the dataHTML variable 
    foreach ($pilotData as $pilot) {
        $dataHTML .= 
            '<div>
            <h3>' . $pilot['pilot'] . '</h3>
            <p>' . $pilot['phone'] . ', ' . $pilot['email'] . ', ' . $pilot['distance'] . ' meters</p>
            </div>';
    }

    // Place correct URL
    $endHTML = str_replace("{PILOT_DATA_URL}", PILOT_DATA_URL, $endHTML);

    // The program overwrites the HTML file only if the two versions are not identical.
    $newHTML = $startHTML . $dataHTML . $endHTML;
    $oldHTML = file_get_contents(FRONTPAGE_ADDRESS);

    if (strcmp($oldHTML, $newHTML) != 0) {
        file_put_contents(FRONTPAGE_ADDRESS, $newHTML);
    }
}

