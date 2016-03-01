<?php
session_start();

require $_SERVER['DOCUMENT_ROOT'] . "/../vendor/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;
$keys_ini = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . "/../keys.ini");

$consumer_key = $keys_ini['consumer_key'];
$consumer_secret = $keys_ini['consumer_secret'];
$dbname = $keys_ini['database_name'];
$dbpass = $keys_ini['database_pass'];

$baseURL = "http://".$_SERVER['SERVER_NAME']."/dashboard.php";

if(!isset($_SESSION['access_token']) && !isset($_GET['logincomplete'])) {
    $oauth_callback = $baseURL."?logincomplete=true";
    $connection = new TwitterOAuth($consumer_key, $consumer_secret);
    $request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => $oauth_callback));

    $_SESSION['oauth_token'] = $request_token['oauth_token'];
    $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

    $url = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));
} else {
    header('Location:'.$baseURL);
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

    </head>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->


          <div class="wrapper row">
            <div class="top-header col-xs-12">
              <div class="block-content row">
                <div class="logo-block col-xs-10 col-xs-offset-2 col-md-12 col-md-offset-0 col-lg-6 col-lg-offset-0">
                  <div class="logo">
                    <a href="index.html">
                      <img src="./assets/img/TweetsForBernie.svg" alt=/>
                    </a>
                  </div>
                </div>

                <div class="quote-block col-xs-10 col-xs-offset-2 col-md-12 col-md-offset-0 col-lg-6 col-lg-offset-0">
                    <h2>“IF THERE IS A LARGE VOTER TURNOUT,
                     WE WILL WIN.”</h2>
                </div>
              </div>
            </div>

            <div class="container left-container col-xs-12 col-lg-6">
                <?php if(isset($_GET['loginerror']) && $_GET['loginerror'] == 'mismatch') { ?>
                    <div style="height:25px; width:100%; position:fixed; z-order:10000; top:0px; left:0px; background-color:red; color:whitesmoke; font-size:2rem; text-align:center;">
                        There was a problem with your login. This is often resolved by trying to login again. Please try again.
                    </div>
                <?php }
                ?>
              <ul class="welcome-steps">
                <li class="welcome-step row">
                  <div class="step-number col-xs-3">
                    <h4>1</h4>
                  </div>
                  <div class="step-content col-xs-12 col-md-9 col-md-offset-1 col-lg-10 col-lg-offset-2">
                    <h5>step 1</h5>
                    <h2>Log into Tweet For Bernie</h2>
                    <p>with your Twitter account. We never post on your profile without your permission. Connecting your account helps us find Bernie supporters that follow you.</p>
                  </div>
                </li>

                <li class="welcome-step row">
                  <div class="step-number col-xs-3">
                    <h4>2</h4>
                  </div>
                  <div class="step-content col-xs-12 col-md-9 col-md-offset-1 col-lg-10 col-lg-offset-2">
                    <h5>step 2</h5>
                    <h2>We'll Find Your Followers</h2>
                    <p>based on what state they live in and if they follow Bernie.</p>
                  </div>
                </li>

                <li class="welcome-step row">
                  <div class="step-number col-xs-3">
                    <h4>3</h4>
                  </div>
                  <div class="step-content col-xs-12 col-md-9 col-md-offset-1 col-lg-10 col-lg-offset-2">
                    <h5>step 3</h5>
                    <h2>Send Your Friends a Tweet</h2>
                    <p>that we'll help you write with important information relevant to their state.</p>
                  </div>
                </li>
              </ul>


            </div>

            <div class="container login-container right-container col-xs-12 col-lg-6">
              <h2>Find your followers who support Bernie on Twitter and make sure they vote! #TweetsForBernie</h2>

              <div class="login-buttons">
                <a href="<?php echo $url ?>" class="btn btn-tw" type="submit">Login with Twitter</a>
                <p class="disclaimer">We never post on your profile without your permission. Connecting your account helps us find Bernie supporters that you're friends with.</p>
              </div>
            </div>

              <div class="panel-footer col-xs-12">
                  <div class="row">
                      <div class="col-xs-10 col-xs-offset-1  col-lg-4 col-lg-offset-4 text-center">
                          <h4>Built by volunteers</h4>
                          <a href="https://coders.forsanders.com/" target="_blank">
                              <img src="./assets/img/white-billionaires-2x.png" class="not-the-b" alt="built buy volunteers, not the billionaires!">
                          </a>
                      </div>
                  </div>
              </div>


          </div>

        <script src="./assets/js/deps/dependencies.js"></script>
        <script src="./assets/js/main.js"></script>

        <script src="https://use.typekit.net/sry7oto.js"></script>
        <script>try{Typekit.load({ async: true });}catch(e){}</script>
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
