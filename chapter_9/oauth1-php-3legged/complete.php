<?php
require_once "OAuth.php";       //oauth library
require_once "common.php";      //common functions and variables

//get request token params from cookie and parse values
$request_cookie = $_COOKIE["requestToken"];
parse_str($request_cookie);

//create required consumer variables
$test_consumer = new OAuthConsumer($key, $secret, NULL);
$req_token = new OAuthConsumer($token, $token_secret, NULL);
$sig_method = new OAuthSignatureMethod_HMAC_SHA1();

//exchange authenticated request token for access token
$params = array('oauth_verifier' => $_GET['oauth_verifier']);
$acc_req = OAuthRequest::from_consumer_and_token($test_consumer, $req_token, "GET", $oauth_access_token_endpoint, $params);
$acc_req->sign_request($sig_method, $test_consumer, $req_token);
$access_ret = run_curl($acc_req->to_url(), 'GET');

//if access token fetch succeeded, we should have oauth_token and oauth_token_secret
//parse and generate access consumer from values
$access_token = array();
parse_str($access_ret, $access_token);
$access_consumer = new OAuthConsumer($access_token['oauth_token'], $access_token['oauth_token_secret'], NULL);

//build update PUT request payload
$guid = $access_token['xoauth_yahoo_guid'];
$title = 'New update';//arbitrary title
$description = 'The time is now '.date("g:i a");//arbitrary desc
$link = 'http://en.wikipedia.org/wiki/Haiku#Examples';//arbitrary link
$source = 'APP.'.$appid;//note: 'APP.' syntax
$date = time();
$suid = 'uniquestring'.time();//arbitrary, unique string
$body = array(
	"updates" => array(
		array(
			"collectionID" => $guid,
			"collectionType" => "guid",
			"class" => "app",
			"source" => $source,
			"type" => 'appActivity',
			"suid" => $suid,
			"title" => $title,
			"description" => $description,
			"link" => $link,
			"pubDate" => (string)$date
		)
	)
);

//build update PUT request URL
$url = sprintf("http://%s/v1/user/%s/updates/%s/%s",
	'social.yahooapis.com', 
	$guid, 
	$source, 
	urlencode($suid)
);

//build and sign request
$request = OAuthRequest::from_consumer_and_token($test_consumer, 
	$access_consumer, 
	'PUT',
	$url, 
	array());
$request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(),
	$test_consumer, 
	$access_consumer
);

//define request headers
$headers = array("Accept: application/json");
$headers[] = $request->to_header();
$headers[] = "Content-type: application/json";

//json encode request payload and make PUT request
$content = json_encode($body);
$resp = run_curl($url, 'PUT', $headers, $content);

//if debug mode, dump signatures & headers 
if ($debug){
    $debug_out = array('Access token' => $access_token,
                       'PUT URL'      => $url,
                       'PUT headers'  => $headers,
                       'PUT content'  => $content,
                       'PUT response' => $resp);
    
    print_r($debug_out);
}
?>
