<?php

session_start();
error_reporting(E_ALL);
date_default_timezone_set("UTC");
require $_SERVER['DOCUMENT_ROOT']."/../vendor/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;
$keys_ini = parse_ini_file($_SERVER['DOCUMENT_ROOT']."/../keys.ini");
$consumer_key = $keys_ini['consumer_key'];
$consumer_secret = $keys_ini['consumer_secret'];
define("CONSUMER_KEY", $keys_ini['consumer_key']);
define("CONSUMER_SECRET", $keys_ini['consumer_secret']);

if(!isset($_SESSION['access_token'])) {
$oauth_callback = "http://www.tweetforbernie.dev/app/index.php";

$request_token = [];
$request_token['oauth_token'] = $_SESSION['oauth_token'];
$request_token['oauth_token_secret'] = $_SESSION['oauth_token_secret'];

if (isset($_REQUEST['oauth_token']) && $request_token['oauth_token'] !== $_REQUEST['oauth_token']) {
echo "Failure to Properly Authenticate!!! Dying...";
die();
}
$connection = new TwitterOAuth($consumer_key, $consumer_secret, $request_token['oauth_token'], $request_token['oauth_token_secret']);
$access_token = $connection->oauth("oauth/access_token", ["oauth_verifier" => $_REQUEST['oauth_verifier']]);
$_SESSION['access_token'] = $access_token;
}

$access_token = $_SESSION['access_token'];
$connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token['oauth_token'], $access_token['oauth_token_secret']);
//$followers = $connection->get("account/verify_credentials");

$db = new MysqliDb('localhost', 'a', 'a', 'tweetforbernie');
//$followers = $connection->get('followers/list', ["include_user_entities" => false]);

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
        $response[] = array(
            "tw_name"=> $obj->name,
            "tw_screen_name"=>$obj->screen_name,
            "tw_user_id"=>$obj->id,
            "tw_location"=>$obj->location,
            "tw_follower_count"=>$obj->followers_count,
            "tw_friend_count"=>$obj->friends_count,
            "tw_last_active"=>date("Y-m-d H:i:s", strtotime($obj->status->created_at)),
            "tw_profile_image"=>$obj->profile_image_url,
            "state" => locationToState($obj->location)
        );
    }
    if($base->next_cursor > 0) {
        $callArray = array($callArray[0], array("cursor" => $base->next_cursor));
        makeCall($callArray, $connection, $response);
    }
    return $response;
}

$captainTwitterId = $_SESSION['captainTwitterId'];

$citizenTwitterId = "704344207603982336";

$friendsOfBernie = $connection->get('followers/list', array("count"=>1, "cursor"=>1527701858901948960));


/*
$message = htmlentities("Test Message to FL Voter. http://www.berniesanders.com/fl");

$db->where('tw_user_id', $citizenTwitterId);
$citizen = $db->getOne("citizens");

//$followers = $connection->get('followers/list', ["include_user_entities" => false]);
$messageTo = $connection->post('direct_messages/new', array("user_id"=>$citizenTwitterId, "text"=>$message));

$messageData = array(
    'captain_id'=>$_SESSION['captainId'],
    'citizen_id'=>$citizen['id'],
    'message'=>$message
);
$db->insert('messages', $messageData);


// 704344207603982336
*/
echo "<pre>";
print_r($friendsOfBernie);
echo "</pre>";



?>

