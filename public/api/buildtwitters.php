<?php
/**
 * Created by PhpStorm.
 * User: khaled
 * Date: 2/23/16
 * Time: 8:35 PM
 */
session_start();
require $_SERVER['DOCUMENT_ROOT'] . "/../vendor/autoload.php";

use Abraham\TwitterOAuth\TwitterOAuth;
$keys_ini = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . "/../keys.ini");

define("CONSUMER_KEY", $keys_ini['consumer_key']);
define("CONSUMER_SECRET", $keys_ini['consumer_secret']);
define("DB_NAME", $keys_ini['database_name']);
define("DB_PASS", $keys_ini['database_pass']);

$baseURL = "http://".$_SERVER['HTTP_HOST']."/index.php";


if(!isset($_SESSION['access_token'])) {
    $oauth_callback = $baseURL;
    $request_token = [];
    $request_token['oauth_token'] = $_SESSION['oauth_token'];
    $request_token['oauth_token_secret'] = $_SESSION['oauth_token_secret'];

    if (isset($_REQUEST['oauth_token']) && $request_token['oauth_token'] !== $_REQUEST['oauth_token']) {
        echo "Failure to Properly Authenticate!!! Fatal Error...";
        die();
    }

    $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $request_token['oauth_token'], $request_token['oauth_token_secret']);
    $access_token = $connection->oauth("oauth/access_token", ["oauth_verifier" => $_REQUEST['oauth_verifier']]);
    $_SESSION['access_token'] = $access_token;
}
$access_token = $_SESSION['access_token'];
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
if(!isset($_SESSION['captainId'])) {
    echo "ERROR : SESSION EXPIRED";
    exit();
}

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
        if(in_array($lookup, $us_state_abbrevs_names, true)) {
            $state = $lookup;
        } else if(isset($us_state_abbrevs_names[$lookup])) {
            $state = $us_state_abbrevs_names[$lookup];
        }
    } else {
        $lookup = strtoupper(trim($parts[0]));
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
function makeCall($callArray, $token, $response = null) {
    $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $token['oauth_token'], $token['oauth_token_secret']);
    $base = $connection->get($callArray[0], $callArray[1]);
    $responseObj = $base->users;
    foreach($responseObj as $obj) {
        $friendShip = $connection->get('friendships/show', array("source_id"=>$obj->id, "target_screen_name"=>"BernieSanders"));
        $friendShipSen = $connection->get('friendships/show', array("source_id"=>$obj->id, "target_screen_name"=>"SenSanders"));

        if($friendShip->relationship->source->following || $friendShipSen->relationship->source->following) {
            $friendly = 1;
        } else {
            $friendly = 0;
        }

        $response[] = array(
            "tw_name"=> $obj->name,
            "tw_screen_name"=>$obj->screen_name,
            "tw_user_id"=>$obj->id,
            "tw_location"=>$obj->location,
            "tw_follower_count"=>$obj->followers_count,
            "tw_friend_count"=>$obj->friends_count,
            "tw_last_active"=>date("Y-m-d H:i:s", strtotime($obj->status->created_at)),
            "tw_profile_image"=>$obj->profile_image_url,
            "state"=>locationToState($obj->location),
            "bernie_follower"=>$friendly
        );
    }
    if($base->next_cursor > 0) {
        $callArray = array($callArray[0], array("cursor" => $base->next_cursor));
        makeCall($callArray, $connection, $response);
    }
    return $response;
}

function makeCitizens($response, $type) {
    $db = new MysqliDb('localhost', DB_NAME, DB_PASS, 'tweetforbernie');
    foreach($response as $respondent) {
        $db->where('tw_user_id', $respondent['tw_user_id']);
        $citizen = $db->getOne ("citizens");
        if(is_null($citizen)) {
            $idCitizen = $db->insert("citizens", $respondent);
            if ($idCitizen) {
                $db->insert("citizens_to_captains", array("captain_id"=>$_SESSION['captainId'], "citizen_id"=>$idCitizen, "status"=>$type));
            } else {
                echo $db->getLastError();
                die();
            }
        } else {
            $db-where("id", $citizen['id']);
            $db->update("citizens", $respondent);

            $db->where('captain_id', $_SESSION['captainId']);
            $db->where('citizen_id', $citizen['id']);
            $db->getOne("citizens_to_captains");
            if($db->count == 0) {
                $db->insert("citizens_to_captains", array("captain_id"=>$_SESSION['captainId'], "citizen_id"=>$citizen['id'], "status"=>$type));
            }
        }
    }
}

$db = new MysqliDb('localhost', DB_NAME, DB_PASS, 'tweetforbernie');

$db->where("captain_id", $_SESSION['captainId']);
$db->get("citizens_to_captains");

if($db->count == 0 || $_GET['rebuild_citizen'] == 'true') {
    $followers = makeCall(array("followers/list", array('cursor'=>-1)), $access_token);
    makeCitizens($followers, 1);
    $friends = makeCall(array("friends/list", array('cursor'=>-1)), $access_token);
    makeCitizens($friends, 2);
}
echo "Done";

exit();
