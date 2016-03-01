<?php
/**
 * Created by PhpStorm.
 * User: khaled
 * Date: 2/29/16
 * Time: 10:53 AM
 */
session_start();
require $_SERVER['DOCUMENT_ROOT'] . "/../vendor/autoload.php";
date_default_timezone_set('EST');
error_reporting(E_ERROR);

use Abraham\TwitterOAuth\TwitterOAuth;
$keys_ini = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . "/../keys.ini");

define("CONSUMER_KEY", $keys_ini['consumer_key']);
define("CONSUMER_SECRET", $keys_ini['consumer_secret']);

define("DB_NAME", $keys_ini['database_name']);
define("DB_PASS", $keys_ini['database_pass']);

if(!isset($_SESSION['captainId'])) {
    echo "ERROR : SESSION EXPIRED";
    exit();
}

if(!isset($_GET['citizenId'])) {
    echo "ERROR : NO CITIZEN ID";
    exit();
}

if(!isset($_GET['tw_message'])) {
    echo "ERROR : NO MESSAGE";
    exit();
}
$db = new MysqliDb('localhost', DB_NAME, DB_PASS, 'tweetforbernie');

$captainTwitterId = $_SESSION['captainTwitterId'];
$citizenTwitterId = $_GET['citizenId'];
$message = $_GET['tw_message'];

$db->where('tw_user_id', $citizenTwitterId);
$citizen = $db->getOne("citizens");

$db->where('citizen_id', $citizen['id']);
$db->where('captain_id', $_SESSION['captainId']);
$db->orderBy("sent_time");
$messages = $db->getOne("messages");
if($messages) {
    $nowTime = date("Y-m-d H:i:s");
    $date1 = new DateTime($nowTime);
    $date2 = new DateTime($messages['sent_time']);
    $interval = $date2->diff($date1);
}

if( !isset($messages['sent_time']) || ($interval->days > 0) || ($interval->h >= 12)) {
    $access_token = $_SESSION['access_token'];
    $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);

//$followers = $connection->get('followers/list', ["include_user_entities" => false]);
    $messageTo = $connection->post('direct_messages/new', array("user_id"=>$citizenTwitterId, "text"=>$message));

    $messageData = array(
        'captain_id'=>$_SESSION['captainId'],
        'citizen_id'=>$citizen['id'],
        'message'=>$message
    );
    $db->insert('messages', $messageData);

    echo "SUCCESS";
} else {
    echo "SUCCESS";
}

