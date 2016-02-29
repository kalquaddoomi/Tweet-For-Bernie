<?php
/**
 * Created by PhpStorm.
 * User: khaled
 * Date: 2/24/16
 * Time: 8:59 AM
 */
session_start();
date_default_timezone_set('EST');
require $_SERVER['DOCUMENT_ROOT'] . "/../vendor/autoload.php";
$keys_ini = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . "/../keys.ini");

define("DB_NAME", $keys_ini['database_name']);
define("DB_PASS", $keys_ini['database_pass']);

if(!isset($_SESSION['captainId'])) {
    echo "ERROR : SESSION EXPIRED";
    exit();
}
$db = new MysqliDb('localhost', DB_NAME, DB_PASS, 'tweetforbernie');

$db->join("citizens z", "p.citizen_id=z.id", "LEFT");
$db->where("p.captain_id", $_SESSION['captainId']);
$db->where("p.status", 1);
$db->where("z.bernie_follower", 1);
if(isset($_GET['stateChoice'])) {
    $db->where("z.state", $_GET['stateChoice']);
}

$mycitizens = $db->get("citizens_to_captains p");


$html = '';
if($db->count <= 0) {
    $html .= "<h4>Sorry, you don't have any followers who also follow Bernie Sanders in ".$_GET['stateChoice']."</h4>";
} else {
    foreach ($mycitizens as $mycitizen) {
        $db->where('citizen_id', $mycitizen['id']);
        $db->where('captain_id', $_SESSION['captainId']);
        $db->orderBy("sent_time");
        $lastMessage = $db->getOne("messages");
        if($lastMessage) {
            $lastSentMessage = date("m-d-y g:i a", strtotime($lastMessage['sent_time']));
        }

        $html .= "<li class='friend-block col-md-4' id='".$mycitizen['tw_user_id']."'>";
        $html .= "<div class='friend-avatar col-xs-4'>";
        $html .= "   <img src='" . $mycitizen['tw_profile_image'] . "' alt='' style='width:100%'/>";
        $html .= "</div>";
        $html .= "<div class='friend-info col-xs-8'>";
        $html .= "<p class='friend-name'>" . $mycitizen['tw_name'] . "</p>";
        $html .= "<p class='friend-screen'>@" . $mycitizen['tw_screen_name'] . "</p>";
        $html .= "<p class='friend-location'>" . $mycitizen['tw_location'] . "</p>";
        if($lastMessage) {
            $html .= "<p class='friend-contact'>Last Messaged: " . $lastSentMessage . " (EST)</p>";
        }
        $html .= "</div>";
        $html .= "</li>";
    }
}
echo $html;