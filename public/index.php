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
                      <img src="./assets/img/logo.svg" alt=/>
                    </a>
                  </div>
                </div>

                <div class="quote-block col-xs-10 col-xs-offset-2 col-md-12 col-md-offset-0 col-lg-6 col-lg-offset-0">
                  <h2>“IF THERE IS A LARGE VOTER TURNOUT, WE WILL WIN.”</h2>
                </div>
              </div>
            </div>

            <div class="container left-container col-xs-12 col-lg-6">

              <ul class="welcome-steps">
                <li class="welcome-step row">
                  <div class="step-number col-xs-3">
                    <h4>1</h4>
                  </div>
                  <div class="step-content col-xs-12 col-md-9 col-md-offset-1 col-lg-10 col-lg-offset-2">
                    <h5>step 1</h5>
                    <h2>Log into Tweet For Bernie</h2>
                    <p>with your Twitter account. We never post on your profile without your permission. Connecting your account helps us find Bernie supporters that you're friends with.</p>
                  </div>
                </li>

                <li class="welcome-step row">
                  <div class="step-number col-xs-3">
                    <h4>2</h4>
                  </div>
                  <div class="step-content col-xs-12 col-md-9 col-md-offset-1 col-lg-10 col-lg-offset-2">
                    <h5>step 2</h5>
                    <h2>We'll Find Your Followers</h2>
                    <p>by location and voting and voter registration deadlines.</p>
                  </div>
                </li>

                <li class="welcome-step row">
                  <div class="step-number col-xs-3">
                    <h4>3</h4>
                  </div>
                  <div class="step-content col-xs-12 col-md-9 col-md-offset-1 col-lg-10 col-lg-offset-2">
                    <h5>step 3</h5>
                    <h2>Send Your Friends a Twitter Message</h2>
                    <p>voting information for their state.that we’ll provide that includes important locations and links.</p>
                  </div>
                </li>
              </ul>


            </div>

            <div class="container login-container right-container col-xs-12 col-lg-6">
              <h2>Find your friends on Twitter. Encourage them to support Bernie, and help them get to the polls on primary election day.</h2>

              <div class="login-buttons">
                <a href="<?php echo $url ?>" class="btn btn-tw" type="submit">Login with Twitter</a>
                <p class="disclaimer">We never post on your profile without your permission. Connecting your account helps us find Bernie supporters that you're friends with.</p>
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
