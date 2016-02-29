<?php
/**
 * Created by PhpStorm.
 * User: khaled
 * Date: 2/24/16
 * Time: 9:20 AM
 */
require "../vendor/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;
$keys_ini = parse_ini_file("../keys.ini");

define("CONSUMER_KEY", $keys_ini['consumer_key']);
define("CONSUMER_SECRET", $keys_ini['consumer_secret']);
$baseURL = "";

if($argc < 2) {
    echo "Must pass Access Token";
    exit();
}
$db = new MysqliDb('localhost', 'a', 'a', 'tweetforbernie');

$access_token = parse_str($argv[1]);
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);

$userIdentity = $connection->get('account/verify_credentials');
$db->where('tw_user_id', $userIdentity->id);
$captain = $db->getOne("captains");


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
            "tw_profile_image"=>$obj->profile_image_url
        );
    }
    if($base->next_cursor > 0) {
        $callArray = array($callArray[0], array("cursor" => $base->next_cursor));
        makeCall($callArray, $connection, $response);
    }
    return $response;
}

function makeCitizens($response, $captainId) {
    $db = new MysqliDb('localhost', 'a', 'a', 'tweetforbernie');
    foreach($response as $respondent) {
        $db->where('tw_user_id', $respondent['tw_user_id']);
        $citizen = $db->getOne ("citizens");
        if(is_null($citizen)) {
            $idCitizen = $db->insert("citizens", $respondent);
            if ($idCitizen) {
                $db->insert("citizens_to_captains", array("captain_id"=>$captainId, "citizen_id"=>$idCitizen));
            } else {
                echo $db->getLastError();
                die();
            }
        } else {
            $db->where('captain_id', $captainId);
            $db->where('citizen_id', $citizen['id']);
            $db->getOne("citizens_to_captains");
            if($db->count == 0) {
                $db->insert("citizens_to_captains", array("captain_id"=>$captainId, "citizen_id"=>$citizen['id']));
            }
        }
    }
}


$db->where("captain_id", $_SESSION['captainId']);
$db->get("citizens_to_captains");

if($db->count == 0 || $_GET['rebuild_citizen'] == 'true') {
    $followers = makeCall(array("followers/list", array('cursor'=>-1)), $access_token);
    makeCitizens($followers, $captain['id']);
    $friends = makeCall(array("friends/list", array('cursor'=>-1)), $access_token);
    makeCitizens($friends, $captain['id']);
}

exit();