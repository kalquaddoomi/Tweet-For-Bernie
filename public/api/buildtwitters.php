<?php
/**
 * Created by PhpStorm.
 * User: khaled
 * Date: 2/23/16
 * Time: 8:35 PM
 */
session_start();
date_default_timezone_set('EST');
error_reporting(E_ERROR);

require $_SERVER['DOCUMENT_ROOT'] . "/../vendor/autoload.php";

use Abraham\TwitterOAuth\TwitterOAuth;
$keys_ini = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . "/../keys.ini");
$nextCursor = -1;
define("CONSUMER_KEY", $keys_ini['consumer_key']);
define("CONSUMER_SECRET", $keys_ini['consumer_secret']);
define("DB_NAME", $keys_ini['database_name']);
define("DB_PASS", $keys_ini['database_pass']);
define("ALLOWED_CALLS", 2);

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
if(!isset($_GET['reset_counter'])) {
    $_SESSION['captainLastCursor'] = -1;
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
function makeCall($callArray, $token, $totalCalls=0, $response = null) {
    $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $token['oauth_token'], $token['oauth_token_secret']);
    $base = $connection->get($callArray[0], $callArray[1]);
    if(isset($base->errors)) {
        if($base->errors[0]->code == 88) {
            echo "RATE-LIMIT";
        }
    }
    if($base->next_cursor > 0) {
        echo $base->next_cursor;
        $_SESSION['captainLastCursor'] = $nextCursor = $base->next_cursor;
    } else {
        $_SESSION['captainLastCursor'] = $nextCursor = -1;
        echo -1;
    }
    $responseObj = $base->users;
    foreach($responseObj as $obj) {
        $friendly = 1;
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
            $db->where("id", $citizen['id']);
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

if($_SESSION['captainLastCursor'] == -1) {
    $followers = makeCall(array("followers/list", array('cursor'=>$_SESSION['captainLastCursor'],'count'=>50, 'include_user_entities'=>'false')), $access_token);
} else {
    $followers = makeCall(array("followers/list", array('cursor' => $_SESSION['captainLastCursor'], 'count' => 50, 'include_user_entities' => 'false')), $access_token);
}
makeCitizens($followers, 1);

exit();
