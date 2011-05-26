<?php
require_once "OAuth.php";       //oauth library
require_once "common.php";      //common functions and variables

//initialize consumer
$consumer = new OAuthConsumer($key, $secret, NULL);

//prepare to get request token
$sig_method = new OAuthSignatureMethod_HMAC_SHA1();
$parsed = parse_url($request_token_endpoint);
$params = array('oauth_callback' => $base_url);

//sign request and get request token
$req_req = OAuthRequest::from_consumer_and_token($consumer, NULL, "GET", $request_token_endpoint, $params);
$req_req->sign_request($sig_method, $consumer, NULL);
$req_token = run_curl($req_req->to_url(), 'GET');

//if fetching request token was successful we should have oauth_token and oauth_token_secret
parse_str($req_token, $tokens);
$oauth_token = $tokens['oauth_token'];
$oauth_token_secret = $tokens['oauth_token_secret'];

//store key and token details in cookie to pass to complete stage
setcookie("requestToken", "key=$key&token=$oauth_token&token_secret=$oauth_token_secret");
			  
//build authentication url following sign-in and redirect user
$auth_url = $authorize_endpoint . "?oauth_token=$oauth_token";
header("Location: $auth_url");
?>