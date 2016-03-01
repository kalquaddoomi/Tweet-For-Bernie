<?php
session_start();

require $_SERVER['DOCUMENT_ROOT'] . "/../vendor/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;
$keys_ini = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . "/../keys.ini");

$consumer_key = $keys_ini['consumer_key'];
$consumer_secret = $keys_ini['consumer_secret'];
$dbname = $keys_ini['database_name'];
$dbpass = $keys_ini['database_pass'];
$resync = "false";
$baseURL = "http://".$_SERVER['SERVER_NAME']."/index.php";

if(!isset($_SESSION['access_token']) && !isset($_GET['logincomplete'])) {
    $oauth_callback = $baseURL."?logincomplete=true";
    $connection = new TwitterOAuth($consumer_key, $consumer_secret);
    $request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => $oauth_callback));

    $_SESSION['oauth_token'] = $request_token['oauth_token'];
    $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

    $url = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));
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
    $db = new MysqliDb('localhost', $dbname, $dbpass, 'tweetforbernie');

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
            $resync = "true";
        }
        $db->where('tw_user_id', $userIdentity->id);
        $captain = $db->getOne("captains");
        if($captain['tw_follower_count'] != $userIdentity->followers_count) {
            $resync = true;
        }
        $_SESSION['captainTwitterId'] = $userIdentity->id;
        $_SESSION['captainId'] = $captain['id'];
        $_SESSION['captainName'] = $userIdentity->name;
        $_SESSION['captainScreen'] = $userIdentity->screen_name;
        $_SESSION['captainImage'] = $userIdentity->profile_image_url;
        $_SESSION['captainFollowers'] = $userIdentity->followers_count;

    }
}
?>
<!doctype html>
<html class="no-js" lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>Tweet For Bernie</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="apple-touch-icon" href="apple-touch-icon.png">
        <!-- Place favicon.ico in the root directory -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>

        <link rel="stylesheet" href="./assets/css/app.css">
        <script src="./assets/js/vendor/modernizr-2.8.3.min.js"></script>
        <link rel="stylesheet" href="./assets/css/normalize.css">
        <script src="./js/dashboard.js"></script>
    </head>
    <body>
        <!--[if lt IE 9]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->


        <div class="wrapper row">
            <div class="top-header app col-xs-12">
              <div class="block-content row">
                <div class="logo-block col-xs-10 col-xs-offset-2 col-md-12 col-md-offset-0 col-lg-6 col-lg-offset-0">
                  <div class="logo">
                    <a href="index.html">
                      <img src="./assets/img/logo.svg" alt=/>
                    </a>
                  </div>
                </div>

                  <div class="quote-block col-xs-10 col-xs-offset-2 col-md-12 col-md-offset-0 col-lg-6 col-lg-offset-0">
                      <h2>“IF THERE IS A LARGE VOTER TURNOUT,
                          WE WILL WIN.”</h2>
                  </div>
              </div>
            </div>

            <div class="container left-container col-xs-12 col-lg-4">

              <div class="user-block row">
                <div class="user-avatar col-xs-3">
                  <img src="<?php echo $_SESSION['captainImage'] ?>" alt="" style="width: 100%;"/>
                </div>
                <div class="user-component col-xs-8">
                  <h5>Welcome <span id="username"><?php echo $_SESSION['captainName'] ?></span></h5>
                    <h5>Total Current Followers: <?php echo $_SESSION['captainFollowers'] ?></h5>
                  <a href="/logout.php" class="sign-out">Sign Out</a>
                </div>

              </div>

            <div class="task row">
            <h4 class="task-title col-xs-10 col-xs-offset-1">
                Send a message to your friends in :
            </h4>
            <div class="task-state col-xs-10 col-xs-offset-1">
                  <select class="form-control" id="states-list">
                    <option value="Unset">Pick a state</option>

                  	<option value="AL">Alabama</option>
                  	<!-- <option value="AK">Alaska</option>
                  	<option value="AZ">Arizona</option> -->
                  	<option value="AR">Arkansas</option>
                      <!--
                  	<option value="CA">California</option> -->
                  	<option value="CO">Colorado</option>
                      <!--
                  	<option value="CT">Connecticut</option>
                  	<option value="DE">Delaware</option>
                  	<option value="DC">District Of Columbia</option> -->
                  	<option value="FL">Florida</option>
                  	<option value="GA">Georgia</option>
                      <!--
                  	<option value="HI">Hawaii</option>
                  	<option value="ID">Idaho</option>
                  	<option value="IL">Illinois</option>
                  	<option value="IN">Indiana</option>
                  	<option value="IA">Iowa</option>
                  	<option value="KS">Kansas</option>
                  	<option value="KY">Kentucky</option>
                  	<option value="LA">Louisiana</option>
                  	<option value="ME">Maine</option>
                  	<option value="MD">Maryland</option> -->

                  	<option value="MA">Massachusetts</option>
                  	<!-- <option value="MI">Michigan</option> -->
                  	<option value="MN">Minnesota</option>
                      <!--
                  	<option value="MS">Mississippi</option>
                  	<option value="MO">Missouri</option>
                  	<option value="MT">Montana</option>
                  	<option value="NE">Nebraska</option>
                  	<option value="NV">Nevada</option>
                  	<option value="NH">New Hampshire</option>
                  	<option value="NJ">New Jersey</option>
                  	<option value="NM">New Mexico</option>
                  	<option value="NY">New York</option>
                  	<option value="NC">North Carolina</option>
                  	<option value="ND">North Dakota</option>
                  	<option value="OH">Ohio</option> -->
                  	<option value="OK">Oklahoma</option>
                    <!--
                      <option value="OR">Oregon</option>
                  	<option value="PA">Pennsylvania</option>
                  	<option value="RI">Rhode Island</option>
                  	<option value="SC">South Carolina</option>
                  	<option value="SD">South Dakota</option> -->
                  	<option value="TN">Tennessee</option>
                  	<option value="TX">Texas</option>
                      <!--
                  	<option value="UT">Utah</option> -->
                  	<option value="VT">Vermont</option>
                  	<option value="VA">Virginia</option>
                      <!--
                  	<option value="WA">Washington</option>
                  	<option value="WV">West Virginia</option>
                  	<option value="WI">Wisconsin</option>
                  	<option value="WY">Wyoming</option>
                    <option value="UNK">Locations Unknown</option> -->
                  </select>
                </div>
                <p class="task-info col-xs-10 col-xs-offset-1">
                  Democratic <span id="task-type"></span> is in <span id="task-deadline"></span>
                </p>
              </div>
                <div class="col-xs-8 resync-control">
                    <div id="resync" data-resync="<?php echo $resync ?>"></div>
                    <button id="sync-citizens">Sync my Friends and Followers Now</button>
                </div>
            </div>

            <div class="container right-container col-xs-12 col-lg-8">
              <h3 id="followers-state"> Pick a State to find your Followers</h3>

              <ul class="friends-list row" id="friends-list-container">

              </ul>

              <div class="message-block row">
                  <h4>Message to my selected Followers:</h4>
                <textarea class="form-control" rows="3" id="friends-message">

                </textarea>
                <!-- <div class="checkbox left">
                  <label>
                    <input type="checkbox" value="">
                    send a reminder on voting day too
                  </label>
                </div> -->
                <div class="right btn">
                  <button class="btn btn-primary" id="send-messages">send</button>
                </div>
              </div>
            </div>



          </div>

        <script src="./assets/js/deps/dependencies.js"></script>
        <script src="./assets/js/main.js"></script>

        <script>
            (function(d) {
                var config = {
                        kitId: 'ptd7fjg',
                        scriptTimeout: 3000,
                        async: true
                    },
                    h=d.documentElement,t=setTimeout(function(){h.className=h.className.replace(/\bwf-loading\b/g,"")+" wf-inactive";},config.scriptTimeout),tk=d.createElement("script"),f=false,s=d.getElementsByTagName("script")[0],a;h.className+=" wf-loading";tk.src='https://use.typekit.net/'+config.kitId+'.js';tk.async=true;tk.onload=tk.onreadystatechange=function(){a=this.readyState;if(f||a&&a!="complete"&&a!="loaded")return;f=true;clearTimeout(t);try{Typekit.load(config)}catch(e){}};s.parentNode.insertBefore(tk,s)
            })(document);
        </script>
        <!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
        <script>
            (function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=
            function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;
            e=o.createElement(i);r=o.getElementsByTagName(i)[0];
            e.src='https://www.google-analytics.com/analytics.js';
            r.parentNode.insertBefore(e,r)}(window,document,'script','ga'));
            ga('create','UA-74460420-1','auto');ga('send','pageview');
        </script>
    </body>
</html>
