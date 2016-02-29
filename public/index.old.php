<?php
/**
 * Created by PhpStorm.
 * User: khaled
 * Date: 2/23/16
 * Time: 5:17 PM
 */
session_start();

require $_SERVER['DOCUMENT_ROOT'] . "/../vendor/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;
$keys_ini = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . "/../keys.ini");

$consumer_key = $keys_ini['consumer_key'];
$consumer_secret = $keys_ini['consumer_secret'];

$baseURL = "http://".$_SERVER['SERVER_NAME']."/index.php";


if(!isset($_SESSION['access_token']) && !isset($_GET['logincomplete'])) {
    $oauth_callback = $baseURL."?logincomplete=true";
    $connection = new TwitterOAuth($consumer_key, $consumer_secret);
    $request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => $oauth_callback));

    $_SESSION['oauth_token'] = $request_token['oauth_token'];
    $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

    $url = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));
    $_SESSION['step'] = "stepOne";
} elseif(!isset($_SESSION['access_token']) && isset($_GET['logincomplete'])) {
    $oauth_callback = $baseURL;
    $request_token = [];
    $request_token['oauth_token'] = $_SESSION['oauth_token'];
    $request_token['oauth_token_secret'] = $_SESSION['oauth_token_secret'];

    if (isset($_REQUEST['oauth_token']) && $request_token['oauth_token'] !== $_REQUEST['oauth_token']) {
        echo "Failure to Properly Authenticate!!! Fatal Error...";
        die();
    }
    $connection = new TwitterOAuth($consumer_key, $consumer_secret, $request_token['oauth_token'], $request_token['oauth_token_secret']);
    $access_token = $connection->oauth("oauth/access_token", ["oauth_verifier" => $_REQUEST['oauth_verifier']]);
    $_SESSION['access_token'] = $access_token;
    header('Location:'.$baseURL);
} else {
    $db = new MysqliDb('localhost', 'a', 'a', 'tweetforbernie');

    $access_token = $_SESSION['access_token'];
    $connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token['oauth_token'], $access_token['oauth_token_secret']);
    if(!isset($_SESSION['captainId'])) {
        $userIdentity = $connection->get('account/verify_credentials');
        $db->where('tw_user_id', $userIdentity->id);
        $captain = $db->getOne("captains");
        if (is_null($captain)) {
            echo "\nGenerating new Record\n";
            $data = Array(
                "tw_user_id" => $userIdentity->id,
                "tw_screen_name" => $userIdentity->screen_name,
                "tw_name" => $userIdentity->name,
                "tw_location" => $userIdentity->location,
                "tw_friend_count" => $userIdentity->friends_count,
                "tw_follower_count" => $userIdentity->followers_count
            );
            $idCaptain = $db->insert('captains', $data);

            if ($idCaptain) {
                $flashMsg = "Successfully created new Captain";
            } else {
                $flashMsg = $db->getLastError();
            }
        }
        $_SESSION['captainTwitterId'] = $userIdentity->id;
        $_SESSION['captainId'] = $captain['id'];
        $_SESSION['captainName'] = $userIdentity->name;
        $_SESSION['captainScreen'] = $userIdentity->screen_name;
        $_SESSION['captainImage'] = $userIdentity->profile_image_url;
    }
    $_SESSION['step'] = "stepTwo";
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Tweet For Bernie</title>
        <link href="./css/master.css" rel="stylesheet" type="text/css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
        <script src="./js/<?php echo $_SESSION['step'].".js"?>"></script>
    </head>
    <body>
    <div id="page-container">
        <div id="title-block">Tweet For Bernie</div>
            <div id="leftcolumn">
        <?php switch($_SESSION['step']) {
            case "stepOne": ?>
                <?php break; ?>
            <?php case "stepTwo": ?>
                <div id="captain-info">
                    <img src="<?php echo $_SESSION['captainImage'] ?>" id="captain-pic">
                    <span id="captain-name">Welcome <?php echo $_SESSION['captainName'] ?></span>
                    <span id="captain-screen">@<?php echo $_SESSION['captainScreen'] ?></span>
                </div>
                <div id="captain-messaging">
                    <button id="Sync">Sync My Twitter Friends and Followers</button>
                    <ul>
                        <li>Found Friends: </li>
                        <li>Found Followers: </li>
                    </ul>
                </div>
                <div id="load-by-state">
                    <select id="state-selector">

                        <option value="SC">South Carolina</option>
                    </select>
                </div>
                <?php break; ?>
        <?php } ?>
            </div>
            <div id="rightcolumn">
        <?php switch($_SESSION['step']) {
            case "stepOne": ?>
                <a href="<?php echo $url?>"><button id="twitter-login">Login with Twitter</button></a>
                <?php break; ?>
            <?php case "stepTwo": ?>

                <?php break; ?>
        <?php } ?>
            </div>
    </div>

    </body>
</html>


