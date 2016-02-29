<?php
/**
 * Created by PhpStorm.
 * User: khaled
 * Date: 2/24/16
 * Time: 8:59 AM
 */
session_start();
require $_SERVER['DOCUMENT_ROOT'] . "/../vendor/autoload.php";

if(!isset($_SESSION['captainId'])) {
    echo "ERROR : SESSION EXPIRED";
    exit();
}
$db = new MysqliDb('localhost', 'a', 'a', 'tweetforbernie');
$db->join("citizens z", "p.citizen_id=z.id", "LEFT");
$db->where("p.captain_id", $_SESSION['captainId']);
$db->where("z.status", 1);
if(isset($_GET['stateChoice'])) {
    $db->where("z.state", $_GET['stateChoice']);
}

$mycitizens = $db->get("citizens_to_captains p");


$html = '';
if($db->count <= 0) {
    $html .= "<h4>Sorry, you don't have any followers in ".$_GET['stateChoice']."</h4>";
} else {
    foreach ($mycitizens as $mycitizen) {

        $html .= "<li class='friend-block col-md-4' id='".$mycitizen['tw_user_id']."'>";
        $html .= "<div class='friend-avatar col-xs-4'>";
        $html .= "   <img src='" . $mycitizen['tw_profile_image'] . "' alt='' style='width:100%'/>";
        $html .= "</div>";
        $html .= "<div class='friend-info col-xs-8'>";
        $html .= "<p class='friend-name'>" . $mycitizen['tw_name'] . "</p>";
        $html .= "<p class='friend-screen'>@" . $mycitizen['tw_screen_name'] . "</p>";
        $html .= "<p class='friend-location'>" . $mycitizen['tw_location'] . "</p>";
        $html .= "</div>";
        $html .= "</li>";
    }
}
echo $html;