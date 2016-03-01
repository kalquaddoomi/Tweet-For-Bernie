<?php
/**
 * Created by PhpStorm.
 * User: khaled
 * Date: 3/1/16
 * Time: 3:07 PM
 */
require "../vendor/autoload.php";
$keys_ini = parse_ini_file("../keys.ini");

define("CONSUMER_KEY", $keys_ini['consumer_key']);
define("CONSUMER_SECRET", $keys_ini['consumer_secret']);
define("DB_NAME", $keys_ini['database_name']);
define("DB_PASS", $keys_ini['database_pass']);

function locationToState($location) {
    $us_state_abbrevs_names = array(
        'ALABAMA'=>'AL',
        'ALASKA'=>'AK',
        'AMERICAN SAMOA'=>'AS',
        'ARIZONA'=>'AZ',
        'ARKANSAS'=>'AR',
        'CALIFORNIA'=>'CA',
        'COLORADO'=>'CO',
        'CONNECTICUT'=>'CT',
        'DELAWARE'=>'DE',
        'DISTRICT OF COLUMBIA'=>'DC',
        'FEDERATED STATES OF MICRONESIA'=>'FM',
        'FLORIDA'=>'FL',
        'GEORGIA'=>'GA',
        'GUAM GU'=>'GU',
        'HAWAII'=>'HI',
        'IDAHO'=>'ID',
        'ILLINOIS'=>'IL',
        'INDIANA'=>'IN',
        'IOWA'=>'IA',
        'KANSAS'=>'KS',
        'KENTUCKY'=>'KY',
        'LOUISIANA'=>'LA',
        'MAINE'=>'ME',
        'MARSHALL ISLANDS'=>'MH',
        'MARYLAND'=>'MD',
        'MASSACHUSETTS'=>'MA',
        'MICHIGAN'=>'MI',
        'MINNESOTA'=>'MN',
        'MISSISSIPPI'=>'MS',
        'MISSOURI'=>'MO',
        'MONTANA'=>'MT',
        'NEBRASKA'=>'NE',
        'NEVADA'=>'NV',
        'NEW HAMPSHIRE'=>'NH',
        'NEW JERSEY'=>'NJ',
        'NEW MEXICO'=>'NM',
        'NEW YORK'=>'NY',
        'NORTH CAROLINA'=>'NC',
        'NORTH DAKOTA'=>'ND',
        'NORTHERN MARIANA ISLANDS'=>'MP',
        'OHIO'=>'OH',
        'OKLAHOMA'=>'OK',
        'OREGON'=>'OR',
        'PALAU'=>'PW',
        'PENNSYLVANIA'=>'PA',
        'PUERTO RICO'=>'PR',
        'RHODE ISLAND'=>'RI',
        'SOUTH CAROLINA'=>'SC',
        'SOUTH DAKOTA'=>'SD',
        'TENNESSEE'=>'TN',
        'TEXAS'=>'TX',
        'UTAH'=>'UT',
        'VERMONT'=>'VT',
        'VIRGIN ISLANDS'=>'VI',
        'VIRGINIA'=>'VA',
        'WASHINGTON'=>'WA',
        'WEST VIRGINIA'=>'WV',
        'WISCONSIN'=>'WI',
        'WYOMING'=>'WY',
        'ARMED FORCES AFRICA \ CANADA \ EUROPE \ MIDDLE EAST'=>'AE',
        'ARMED FORCES AMERICA (EXCEPT CANADA)'=>'AA',
        'ARMED FORCES PACIFIC'=>'AP'
    );
    $parts = explode(",", $location);
    $state = "UNK";


    if(count($parts) > 1) {
        $lookup = strtoupper(trim($parts[1]));
        if (in_array($lookup, $us_state_abbrevs_names, true)) {
            $state = $lookup;
        } else if (isset($us_state_abbrevs_names[$lookup])) {
            $state = $us_state_abbrevs_names[$lookup];
        }
    }
    if($state == 'UNK') {
        $lookup = strtoupper(trim($parts[0]));
        if(isset($us_state_abbrevs_names[$lookup])) {
            $state = $us_state_abbrevs_names[$lookup];
        }
        switch($lookup) {
            case "NYC":
            case "NEW YORK":
            case "NEW YORK CITY":
                $state = "NY";
                break;
        }
    }

    return $state;
}

$db = new MysqliDb('localhost', 'a', 'a', 'tweetforbernie');
$db->where('state', "UNK");
$unknowns = $db->get("citizens");


foreach($unknowns as $unknown) {
    echo $unknown['tw_name'];
    echo " >> ".$unknown['tw_location']."\n";
    $newLoc = locationToState($unknown['tw_location']);
    echo "Location Check: ".locationToState($unknown['tw_location'])."\n";

    if($newLoc != 'UNK') {
        $db->where('id', $unknown['id']);
        $data['state'] = $newLoc;
        $db->update('citizens', $data);
        echo "Updated Citizen ".$unknown['id']." to ".$newLoc."\n";
    }
}
exit();